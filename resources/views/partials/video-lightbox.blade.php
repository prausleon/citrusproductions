{{--
  Global video lightbox — ported from components/video-lightbox.tsx, extended
  to also play self-hosted uploads (not just Vimeo). Hidden by default;
  resources/js/app.js wires up any [data-video-trigger] button (project
  cards, slice cards, clip cards, "play" buttons) to open it, showing
  whichever of the iframe/<video> matches the trigger's data-video-type.
--}}
<div
  data-video-lightbox
  class="fixed inset-0 z-[90] hidden items-center justify-center bg-ink/90 p-4 backdrop-blur-sm"
  role="dialog"
  aria-modal="true"
  aria-label="{{ __('Video player', 'sage') }}"
>
  <button
    type="button"
    data-video-close
    class="absolute right-5 top-5 flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-citrus-orange"
    aria-label="{{ __('Close video', 'sage') }}"
  >
    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
  </button>
  <div class="w-full max-w-5xl" data-video-panel>
    <div class="relative aspect-video overflow-hidden rounded-2xl bg-black shadow-2xl">
      <iframe
        data-video-frame
        src=""
        title=""
        class="absolute inset-0 hidden h-full w-full"
        allow="autoplay; fullscreen; picture-in-picture"
        allowfullscreen
      ></iframe>
      <video
        data-video-el
        class="absolute inset-0 hidden h-full w-full"
        controls
        playsinline
      ></video>
    </div>
    <p data-video-title class="mt-4 text-center font-display text-lg font-bold text-white"></p>
  </div>
</div>
