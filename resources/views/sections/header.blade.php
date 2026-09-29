{{--
  Floating site nav — ported from components/floating-nav.tsx (FloatingNav).
  Fixed pill header with logo, desktop inline nav, and a mobile dropdown.
  Section anchors only resolve on the front page; elsewhere they link back
  home first (`data-nav-anchor` is wired up in resources/js/app.js).
--}}
@php
  $logo = \App\v0_image_or(\App\v0_home_meta('header_logo'), 'citrus-logo.png');
  $nav_items = [
    'about' => __('About', 'sage'),
    'work' => __('Work', 'sage'),
    'slices' => __('Citrus Slices', 'sage'),
    'clients' => __('Clients', 'sage'),
    'competencies' => __('Competencies', 'sage'),
    'contact' => __('Contact Us', 'sage'),
  ];
@endphp

<header class="fixed inset-x-0 top-0 z-50 flex items-center justify-between gap-4 px-4 py-4 sm:px-6 md:px-10">
  <a
    href="{{ home_url('/') }}"
    data-nav-home
    class="group flex shrink-0 items-center rounded-full bg-background/70 p-2 shadow-sm ring-1 ring-black/5 backdrop-blur-md transition hover:bg-background"
    aria-label="{{ get_bloginfo('name') }} — {{ __('home', 'sage') }}"
  >
    <img
      src="{{ $logo }}"
      alt="{{ get_bloginfo('name') }}"
      class="h-14 w-14 shrink-0 object-contain sm:h-20 sm:w-20 md:h-20 md:w-20"
    >
  </a>

  {{-- Desktop / tablet inline nav --}}
  <nav class="hidden items-center gap-0.5 rounded-full bg-background/70 p-1.5 shadow-sm ring-1 ring-black/5 backdrop-blur-md md:flex" aria-label="{{ __('Primary', 'sage') }}">
    @foreach ($nav_items as $id => $label)
      <a
        href="{{ home_url('/#'.$id) }}"
        data-nav-anchor="{{ $id }}"
        class="relative rounded-full px-3 py-2 text-xs font-semibold tracking-tight text-ink/70 transition-colors hover:bg-citrus-orange/10 hover:text-citrus-orange lg:px-4 lg:text-sm"
      >
        {{ $label }}
      </a>
    @endforeach
  </nav>

  {{-- Mobile hamburger toggle --}}
  <button
    type="button"
    data-nav-toggle
    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-background/70 text-ink shadow-sm ring-1 ring-black/5 backdrop-blur-md transition hover:bg-background md:hidden"
    aria-label="{{ __('Open menu', 'sage') }}"
    aria-expanded="false"
    aria-controls="mobile-menu"
  >
    <svg data-nav-icon-open class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
    <svg data-nav-icon-close class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
  </button>

  {{-- Mobile dropdown menu --}}
  <button data-nav-scrim class="fixed inset-0 -z-10 hidden cursor-default md:hidden" aria-hidden="true" tabindex="-1"></button>
  <nav
    id="mobile-menu"
    data-nav-menu
    class="absolute right-4 top-20 hidden w-56 flex-col gap-1 rounded-3xl bg-background/95 p-2 shadow-xl ring-1 ring-black/5 backdrop-blur-md md:hidden"
  >
    @foreach ($nav_items as $id => $label)
      <a
        href="{{ home_url('/#'.$id) }}"
        data-nav-anchor="{{ $id }}"
        class="w-full rounded-2xl px-4 py-3 text-left text-sm font-semibold tracking-tight text-ink/80 transition-colors hover:bg-citrus-orange/10 hover:text-citrus-orange"
      >
        {{ $label }}
      </a>
    @endforeach
  </nav>
</header>
