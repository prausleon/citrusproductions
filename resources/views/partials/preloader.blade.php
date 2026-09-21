{{--
  Loading screen — just the loading line. The video that used to play here
  now plays inside the hero box instead (see sections/hero.blade.php).

  The line is a real progress bar, not a canned animation: initPreloader()
  in resources/js/app.js fills it as the page's actual assets finish (window
  load, web fonts, every above-the-fold image, the hero intro video's first
  frame) and only fades this screen out once all of them are done. It's
  shown on every load of the Homepage template until then; toggle it off
  entirely with the "Enable Preloader" field.

  If JS never runs, a CSS animation in resources/css/app.css dismisses this
  screen after 15s so the page can never be left blocked.
--}}
<div
  data-preloader
  class="fixed inset-0 z-[100] flex items-center justify-center bg-background opacity-100 transition-opacity duration-700"
>
  <div
    data-preloader-track
    class="h-1 w-40 overflow-hidden rounded-full bg-citrus-orange/15"
    role="progressbar"
    aria-label="{{ __('Loading', 'sage') }}"
    aria-valuemin="0"
    aria-valuemax="100"
    aria-valuenow="0"
  >
    <div
      data-preloader-bar
      class="h-full w-full origin-left rounded-full bg-citrus-orange transition-transform duration-300 ease-out"
      style="transform: scaleX(0)"
    ></div>
  </div>
</div>
