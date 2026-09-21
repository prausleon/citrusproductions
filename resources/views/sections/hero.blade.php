{{--
  Ported from components/sections/hero.tsx.
  The scroll-driven parallax transforms were inline React state (useScrollY);
  here the three layers are tagged with data-parallax-* and driven by
  initHeroParallax() in resources/js/app.js, using the same rAF-throttled
  scroll + transform formulas as the original. Content still renders fully
  with no motion if JS is disabled.

  Intro video: on every visit, the frosted box first plays
  the intro video (the one that used to be in the preloader), then the
  headline copy fades in over it. The video and the copy share one grid cell
  so the box never changes height between the two. State lives on the
  <section> as data-intro="pending" | "playing" | "done" — set below by an
  inline script (which runs before first paint, so there's no flash), then
  advanced by initHeroIntro() in resources/js/app.js; the styling for each
  state is in resources/css/app.css. No data-intro at all
  (prefers-reduced-motion, no JS) means the video never renders and the
  copy simply shows.
--}}
@php
  $eyebrow = \App\v0_home_meta('hero_eyebrow', 'Citrus Video Productions');
  $prefix = \App\v0_home_meta('hero_heading_prefix', 'Slices of');
  $emphasis = \App\v0_home_meta('hero_heading_emphasis', 'life in motion');
  $line2 = \App\v0_home_meta('hero_heading_line2', 'from fresh and juicy minds');
  $scroll_label = \App\v0_home_meta('hero_scroll_label', 'Scroll to take a bite');
  $bg = \App\v0_image_or(\App\v0_home_meta('hero_image'), 'orange-macro-hero.png');
  $hero_video = \App\v0_image_or(\App\v0_home_meta('hero_video'), 'citrus-preloader.mp4');
@endphp

<section id="hero" class="relative flex min-h-[100svh] items-center justify-center overflow-hidden bg-background">
  {{-- Decides, before first paint, whether this visitor gets the intro: everyone does, except with reduced motion. --}}
  <script>
    (function () {
      try {
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
          document.currentScript.parentElement.setAttribute('data-intro', 'pending');
        }
      } catch (e) {}
    })();
  </script>

  {{-- Layer 1: macro orange slice backdrop, slow parallax --}}
  <div class="absolute inset-0" data-parallax-bg>
    <img src="{{ $bg }}" alt="{{ __('Macro close-up of a fresh, juicy orange slice', 'sage') }}" class="h-full w-full object-cover">
    <div class="absolute inset-0 bg-gradient-to-b from-background/10 via-transparent to-background"></div>
  </div>

  {{-- Layer 2: floating vector citrus wheels, kept to the corners --}}
  <div class="absolute inset-0" data-parallax-decor>
    <x-citrus-wheel class="left-[3%] top-[10%]" :size="205" spin :opacity="0.85" />
    <x-citrus-wheel class="right-[4%] top-[12%]" :size="165" spin :opacity="0.8" />
    <x-citrus-wheel class="bottom-[8%] right-[8%]" :size="135" :opacity="0.8" />
  </div>

  {{-- Layer 3: headline that scales up and pulls apart on scroll --}}
  <div class="relative z-10 px-6 text-center" data-parallax-headline>
    <div data-hero-box class="mx-auto max-w-4xl rounded-[2.5rem] bg-background/45 px-6 py-10 backdrop-blur-md ring-1 ring-white/40 sm:px-12 sm:py-12">
      <div class="grid items-center">
        {{-- Intro video. Decorative + muted (the clip has an audio track, but autoplay policies need it muted anyway). preload="none" so visitors who skip the intro (reduced motion) don't download it; the JS switches it to "auto" when the intro runs. --}}
        <div data-hero-video-wrap class="col-start-1 row-start-1" aria-hidden="true">
          <video
            data-hero-video
            class="mx-auto block h-auto w-full max-w-[620px]"
            src="{{ $hero_video }}"
            muted
            playsinline
            preload="none"
            tabindex="-1"
            disablepictureinpicture
          ></video>
        </div>

        <div data-hero-copy class="col-start-1 row-start-1">
          <p class="mb-4 inline-block rounded-full bg-citrus-orange px-4 py-1.5 font-display text-xs font-bold uppercase tracking-[0.3em] text-white shadow-sm sm:text-sm">
            {{ $eyebrow }}
          </p>
          <h1 class="mx-auto max-w-4xl text-balance font-display text-4xl font-bold leading-[1.05] tracking-tight text-ink sm:text-6xl md:text-7xl">
            {{ $prefix }} <span class="italic text-citrus-orange-dark">{{ $emphasis }}</span>
            <br>
            {!! wptexturize($line2) !!}
          </h1>
        </div>
      </div>
    </div>
    <div class="mt-10 flex justify-center">
      <span class="inline-flex items-center gap-2.5 rounded-full bg-citrus-orange px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-citrus-orange/30 ring-1 ring-white/20">
        <span class="h-2 w-2 animate-pulse rounded-full bg-citrus-green"></span>
        {{ $scroll_label }}
        <svg class="h-4 w-4 animate-bounce text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
      </span>
    </div>
  </div>
</section>
