{{--
  Decorative floating citrus element used across parallax layers.
  Ported from components/citrus-decor.tsx (CitrusWheel).

  Sized responsively: `size` is the desktop (sm: and up) size in px, scaled
  down by `mobileScale` below the sm breakpoint so these elements don't
  overwhelm small screens. Implemented with CSS custom properties so every
  usage across the site benefits without touching each call site.

  Props:
    size        int    px, default 160 — used from the sm breakpoint up
    mobileScale float  default 0.6 — fraction of `size` used below sm
    spin        bool   slow rotation, default false
    float       bool   gentle bob animation, default true
    opacity     float  default 1
    src         string image URL, default the orange wheel asset
    clip        string 'circle' | 'quarter', default 'circle'
--}}
@props([
  'size' => 160,
  'mobileScale' => 0.6,
  'spin' => false,
  'float' => true,
  'opacity' => 1,
  'src' => null,
  'clip' => 'circle',
])

@php
  $src = $src ?: \App\v0_image('orange-wheel.png');
  $isQuarter = $clip === 'quarter';
  $mobileSize = max(1, (int) round($size * $mobileScale));
@endphp

<div
  {{ $attributes->merge(['class' => 'pointer-events-none absolute select-none '.($float ? 'animate-citrus-float' : '')]) }}
  style="opacity: {{ $opacity }}; --wheel-size: {{ $mobileSize }}px; --wheel-size-lg: {{ $size }}px"
  aria-hidden="true"
>
  <div
    class="relative overflow-hidden drop-shadow-xl w-[var(--wheel-size)] h-[var(--wheel-size)] sm:w-[var(--wheel-size-lg)] sm:h-[var(--wheel-size-lg)] {{ $spin ? 'animate-citrus-spin' : '' }}"
    style="{{ $isQuarter ? 'clip-path: circle(100% at 100% 100%);' : 'border-radius: 9999px;' }}"
  >
    @if ($isQuarter)
      <img
        src="{{ $src }}"
        alt=""
        class="absolute left-0 top-0 max-w-none w-[calc(var(--wheel-size)*2)] h-[calc(var(--wheel-size)*2)] sm:w-[calc(var(--wheel-size-lg)*2)] sm:h-[calc(var(--wheel-size-lg)*2)]"
      >
    @else
      <img src="{{ $src }}" alt="" class="h-full w-full object-cover">
    @endif
  </div>
</div>
