{{--
  Global "Behind the Scenes" image gallery lightbox. Opens on click from any
  grid marked up as [data-gallery] wrapping a set of [data-gallery-item]
  thumbnails (see single-project.blade.php / single-slice.blade.php).
  resources/js/app.js reads the images out of whichever gallery was clicked,
  so this one lightbox serves every gallery on the page.
--}}
<div
  data-gallery-lightbox
  class="fixed inset-0 z-[90] hidden items-center justify-center bg-ink/90 p-4 backdrop-blur-sm"
  role="dialog"
  aria-modal="true"
  aria-label="{{ __('Image gallery', 'sage') }}"
>
  <button
    type="button"
    data-gallery-close
    class="absolute right-5 top-5 flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-citrus-orange"
    aria-label="{{ __('Close gallery', 'sage') }}"
  >
    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
  </button>

  <button
    type="button"
    data-gallery-prev
    class="absolute left-3 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-citrus-orange sm:left-6"
    aria-label="{{ __('Previous image', 'sage') }}"
  >
    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
  </button>
  <button
    type="button"
    data-gallery-next
    class="absolute right-3 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-citrus-orange sm:right-6"
    aria-label="{{ __('Next image', 'sage') }}"
  >
    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  </button>

  <div class="w-full max-w-5xl" data-gallery-panel>
    <div class="relative aspect-video overflow-hidden rounded-2xl bg-black shadow-2xl">
      <img data-gallery-image src="" alt="" class="absolute inset-0 h-full w-full object-contain">
    </div>
    <p data-gallery-counter class="mt-4 text-center text-sm font-semibold text-white/70"></p>
  </div>
</div>
