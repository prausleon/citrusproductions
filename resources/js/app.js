/**
 * v0 blueprint interactivity — vanilla JS ports of the client-side behavior
 * from the original Next.js components (Preloader, FloatingNav, scroll-spy,
 * VideoLightbox, Competencies accordion). No framework required.
 */

// ---------------------------------------------------------------------
// Hero parallax (components/sections/hero.tsx + hooks/use-scroll.ts)
// Same rAF-throttled scroll subscription and transform formulas as the
// original useScrollY-driven component, applied directly to the three
// layers tagged in resources/views/sections/hero.blade.php.
// ---------------------------------------------------------------------
function initHeroParallax() {
  const bg = document.querySelector('[data-parallax-bg]')
  const decor = document.querySelector('[data-parallax-decor]')
  const headline = document.querySelector('[data-parallax-headline]')
  if (!bg && !decor && !headline) return

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return

  const apply = () => {
    const clamped = Math.min(window.scrollY, 900)

    if (bg) {
      bg.style.transform = `translateY(${clamped * 0.25}px) scale(${1 + clamped * 0.0004})`
    }
    if (decor) {
      decor.style.transform = `translateY(${clamped * -0.12}px)`
    }
    if (headline) {
      headline.style.transform = `translateY(${clamped * 0.3}px) scale(${1 + clamped * 0.0006})`
      headline.style.opacity = String(Math.max(0, 1 - clamped / 600))
    }
  }

  let ticking = false
  const onScroll = () => {
    if (ticking) return
    ticking = true
    requestAnimationFrame(() => {
      apply()
      ticking = false
    })
  }

  apply()
  window.addEventListener('scroll', onScroll, { passive: true })
}

// ---------------------------------------------------------------------
// Loading screen (components/preloader.tsx) — a real one.
// The bar fills as the page's actual assets finish loading, and the screen
// only fades out once every one of them is done. (The CSS failsafe on
// [data-preloader] dismisses it at 15s if this JS never runs at all.)
// ---------------------------------------------------------------------
const PRELOADER_MIN_MS = 600 // keep the bar up at least this long, so a fast/cached load doesn't strobe
const PRELOADER_MAX_MS = 12000 // never hang forever on one stuck asset
const VIDEO_READY_MAX_MS = 5000 // some browsers refuse to preload video; don't hold the page hostage to one

/** Resolves once a <video> has its first frame — or has errored / timed out. Never rejects, never blocks for long. */
function videoReady(video) {
  return new Promise((resolve) => {
    if (video.readyState >= 2) return resolve()

    let timer
    const finish = () => {
      clearTimeout(timer)
      resolve()
    }
    video.addEventListener('loadeddata', finish, { once: true })
    video.addEventListener('error', finish, { once: true })
    timer = setTimeout(finish, VIDEO_READY_MAX_MS)
  })
}

/**
 * Everything that has to finish before the page counts as loaded:
 *  - the window `load` event (stylesheets, scripts, in-document images and
 *    iframes), then web fonts — checked *after* load, since fonts are only
 *    requested once styles are applied;
 *  - every eager <img>. Lazy ones below the fold are deliberately left out:
 *    they don't load until scrolled to, so waiting on them would hang;
 *  - the hero intro video's first frame, when there is one.
 * Failed images/videos count as finished — a broken asset must not trap
 * visitors on the loading screen.
 */
function collectLoadTasks(introVideo) {
  const tasks = []

  tasks.push(
    (document.readyState === 'complete'
      ? Promise.resolve()
      : new Promise((resolve) => window.addEventListener('load', resolve, { once: true }))
    ).then(() => document.fonts?.ready)
  )

  document.querySelectorAll('img').forEach((img) => {
    if (img.loading === 'lazy') return
    if (img.complete) {
      tasks.push(Promise.resolve())
      return
    }
    tasks.push(
      new Promise((resolve) => {
        img.addEventListener('load', resolve, { once: true })
        img.addEventListener('error', resolve, { once: true })
      })
    )
  })

  if (introVideo) tasks.push(videoReady(introVideo))

  return tasks.map((task) => task.catch(() => {}))
}

/** Returns a promise that resolves once the loading screen has fully faded out (immediately if there isn't one). */
function initPreloader(introVideo) {
  const el = document.querySelector('[data-preloader]')
  if (!el) return Promise.resolve()

  const bar = el.querySelector('[data-preloader-bar]')
  const track = el.querySelector('[data-preloader-track]')
  const startedAt = performance.now()

  const setProgress = (fraction) => {
    if (bar) bar.style.transform = `scaleX(${fraction})`
    track?.setAttribute('aria-valuenow', String(Math.round(fraction * 100)))
  }

  const tasks = collectLoadTasks(introVideo)
  let finished = 0
  tasks.forEach((task) => task.then(() => setProgress(++finished / tasks.length)))

  return Promise.race([Promise.all(tasks), new Promise((resolve) => setTimeout(resolve, PRELOADER_MAX_MS))])
    .then(() => {
      setProgress(1)
      // Let the bar visibly reach the end (its own 300ms transition) and honor the minimum display time.
      const wait = Math.max(0, PRELOADER_MIN_MS - (performance.now() - startedAt)) + 300
      return new Promise((resolve) => setTimeout(resolve, wait))
    })
    .then(
      () =>
        new Promise((resolve) => {
          el.classList.remove('opacity-100')
          el.classList.add('pointer-events-none', 'opacity-0')
          setTimeout(() => {
            el.remove()
            resolve()
          }, 700)
        })
    )
}

// ---------------------------------------------------------------------
// Hero intro — the video plays inside the hero's frosted box, then the
// headline copy replaces it (see sections/hero.blade.php + the
// [data-intro] rules in resources/css/app.css). It plays on every visit: the
// inline script in the hero sets data-intro="pending" before first paint
// (skipped only for prefers-reduced-motion, which never gets here).
// ---------------------------------------------------------------------
function initHeroIntro(hero, video, loaded) {
  const finish = () => {
    if (hero.dataset.intro === 'done') return
    hero.dataset.intro = 'done'
    video.pause()
  }

  const start = () => {
    if (hero.dataset.intro !== 'pending') return

    hero.dataset.intro = 'playing'
    video.addEventListener('ended', finish, { once: true })
    video.addEventListener('error', finish, { once: true })
    setTimeout(finish, ((video.duration || 5) + 3) * 1000) // never leave the headline hidden if playback stalls
    video.play().catch(finish) // e.g. autoplay blocked — skip straight to the headline
  }

  // Wait for the loading screen to be gone (so the video is seen from its
  // first frame) *and* for the video itself to be ready to play.
  Promise.all([loaded, videoReady(video)]).then(start)
}

// ---------------------------------------------------------------------
// Floating nav — mobile menu, smooth-scroll anchors, scroll-spy
// (components/floating-nav.tsx: onNavigate used scrollIntoView({behavior:'smooth'}))
// ---------------------------------------------------------------------

/** Respect the user's motion preference — smooth scroll only when they allow it. */
function prefersSmoothScroll() {
  return ! window.matchMedia('(prefers-reduced-motion: reduce)').matches
}

function scrollToId(id) {
  const el = document.getElementById(id)
  if (!el) return false
  el.scrollIntoView({ behavior: prefersSmoothScroll() ? 'smooth' : 'auto', block: 'start' })
  return true
}

function initNav() {
  const toggle = document.querySelector('[data-nav-toggle]')
  const menu = document.querySelector('[data-nav-menu]')
  const scrim = document.querySelector('[data-nav-scrim]')
  const iconOpen = document.querySelector('[data-nav-icon-open]')
  const iconClose = document.querySelector('[data-nav-icon-close]')
  const links = document.querySelectorAll('[data-nav-anchor]')
  const homeLink = document.querySelector('[data-nav-home]')

  const setOpen = (open) => {
    menu?.classList.toggle('hidden', !open)
    menu?.classList.toggle('flex', open)
    scrim?.classList.toggle('hidden', !open)
    iconOpen?.classList.toggle('hidden', open)
    iconClose?.classList.toggle('hidden', !open)
    toggle?.setAttribute('aria-expanded', String(open))
  }

  toggle?.addEventListener('click', () => setOpen(menu?.classList.contains('hidden')))
  scrim?.addEventListener('click', () => setOpen(false))

  // Section anchors: if the target section exists on this page, smooth-scroll
  // to it in place and update the URL hash without a full navigation. If it
  // doesn't exist (we're on a project/slice/legal page), let the link
  // navigate home+#id as normal — initAnchorScrollOnLoad() picks it up there.
  links.forEach((link) => {
    link.addEventListener('click', (e) => {
      setOpen(false)
      const id = link.dataset.navAnchor
      if (scrollToId(id)) {
        e.preventDefault()
        history.pushState(null, '', `#${id}`)
      }
    })
  })

  // Logo/"Home": if already on the homepage, scroll to top smoothly instead
  // of reloading the page.
  homeLink?.addEventListener('click', (e) => {
    if (!document.querySelector('section#hero')) return
    e.preventDefault()
    window.scrollTo({ top: 0, behavior: prefersSmoothScroll() ? 'smooth' : 'auto' })
    history.pushState(null, '', homeLink.getAttribute('href'))
  })

  // Scroll-spy only makes sense on the front page, where every section id exists.
  const sections = Array.from(document.querySelectorAll('section[id]')).filter((s) =>
    document.querySelector(`[data-nav-anchor="${s.id}"]`)
  )
  if (!sections.length) return

  const setActive = (id) => {
    links.forEach((link) => {
      const active = link.dataset.navAnchor === id
      link.classList.toggle('bg-citrus-orange', active)
      link.classList.toggle('text-white', active)
      link.classList.toggle('shadow', active)
      link.classList.toggle('text-ink/70', !active)
      link.setAttribute('aria-current', active ? 'page' : 'false')
    })
  }

  const spy = new IntersectionObserver(
    (entries) => {
      const visible = entries.filter((e) => e.isIntersecting).sort((a, b) => b.intersectionRatio - a.intersectionRatio)
      if (visible[0]) setActive(visible[0].target.id)
    },
    { rootMargin: '-45% 0px -45% 0px', threshold: [0, 0.25, 0.5, 0.75, 1] }
  )
  sections.forEach((s) => spy.observe(s))
}

/**
 * Land smoothly on a section when arriving with a URL hash already set —
 * e.g. clicking a nav link on a project page navigates to `/#work`, and by
 * the time our JS runs the browser has already jumped there instantly.
 * Reset to the top first, then animate down to the real target.
 */
function initAnchorScrollOnLoad() {
  const id = window.location.hash?.slice(1)
  if (!id || !document.getElementById(id)) return

  window.scrollTo(0, 0)
  requestAnimationFrame(() => scrollToId(id))
}

// ---------------------------------------------------------------------
// Video lightbox (components/video-lightbox.tsx)
// Plays either a Vimeo embed or a self-hosted upload, depending on the
// triggering element's data-video-type ("vimeo" or "upload").
// ---------------------------------------------------------------------
function vimeoEmbed(id) {
  return `https://player.vimeo.com/video/${id}?title=0&byline=0&portrait=0`
}

function initVideoLightbox() {
  const box = document.querySelector('[data-video-lightbox]')
  if (!box) return

  const frame = box.querySelector('[data-video-frame]')
  const video = box.querySelector('[data-video-el]')
  const title = box.querySelector('[data-video-title]')
  const panel = box.querySelector('[data-video-panel]')

  const reset = () => {
    frame.src = ''
    frame.classList.add('hidden')
    video.pause()
    video.removeAttribute('src')
    video.load()
    video.classList.add('hidden')
  }

  const open = (trigger) => {
    const label = trigger.dataset.videoTitle || ''
    const type = trigger.dataset.videoType || (trigger.dataset.vimeoId ? 'vimeo' : '')

    reset()

    if (type === 'upload' && trigger.dataset.videoUrl) {
      video.src = trigger.dataset.videoUrl
      video.classList.remove('hidden')
      video.play().catch(() => {})
    } else if (trigger.dataset.vimeoId) {
      frame.src = `${vimeoEmbed(trigger.dataset.vimeoId)}&autoplay=1`
      frame.title = label
      frame.classList.remove('hidden')
    } else {
      return
    }

    title.textContent = label
    box.classList.remove('hidden')
    box.classList.add('flex')
    document.body.style.overflow = 'hidden'
  }

  const close = () => {
    box.classList.add('hidden')
    box.classList.remove('flex')
    reset()
    document.body.style.overflow = ''
  }

  document.addEventListener('click', (e) => {
    const trigger = e.target.closest('[data-video-trigger]')
    if (trigger) {
      e.preventDefault()
      open(trigger)
      return
    }
    if (e.target.closest('[data-video-close]') || (e.target === box)) {
      close()
    }
  })

  panel?.addEventListener('click', (e) => e.stopPropagation())

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !box.classList.contains('hidden')) close()
  })
}

// ---------------------------------------------------------------------
// Competencies accordion (components/sections/competencies.tsx)
// ---------------------------------------------------------------------
function initAccordion() {
  document.querySelectorAll('[data-accordion-item]').forEach((item) => {
    const trigger = item.querySelector('[data-accordion-trigger]')
    const panel = item.querySelector('[data-accordion-panel]')
    const icon = item.querySelector('[data-accordion-icon]')

    trigger?.addEventListener('click', () => {
      const isOpen = trigger.getAttribute('aria-expanded') === 'true'
      trigger.setAttribute('aria-expanded', String(!isOpen))
      panel.style.gridTemplateRows = isOpen ? '0fr' : '1fr'
      icon?.classList.toggle('rotate-45', !isOpen)
      icon?.classList.toggle('bg-citrus-orange', !isOpen)
      icon?.classList.toggle('text-white', !isOpen)
      icon?.classList.toggle('bg-ink/5', isOpen)
      icon?.classList.toggle('text-ink', isOpen)
      item.classList.toggle('border-citrus-orange/40', !isOpen)
      item.classList.toggle('bg-citrus-cream', !isOpen)
      item.classList.toggle('border-ink/10', isOpen)
      item.classList.toggle('bg-background', isOpen)
    })
  })
}

document.addEventListener('DOMContentLoaded', () => {
  initHeroParallax()

  // The hero intro — the loading screen tracks
  // its video too, so it's ready to play the moment the screen lifts.
  const hero = document.querySelector('[data-intro="pending"]')
  const introVideo = hero?.querySelector('[data-hero-video]') ?? null
  if (hero && !introVideo) hero.dataset.intro = 'done' // no video in the markup — just show the headline
  if (introVideo) {
    introVideo.preload = 'auto' // ships as "none" so reduced-motion visitors (who skip the intro) never download it
    introVideo.load()
  }

  const loaded = initPreloader(introVideo)
  if (hero && introVideo) initHeroIntro(hero, introVideo, loaded)

  initNav()
  initAnchorScrollOnLoad()
  initVideoLightbox()
  initAccordion()
})
