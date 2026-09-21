{{-- Ported from components/sections/about.tsx. --}}
@php
  $eyebrow = \App\v0_home_meta('about_eyebrow', 'Deep Roots, Wide Branches');
  $heading = \App\v0_home_meta('about_heading', 'About Us');
  $intro = \App\v0_home_meta('about_intro', "<p>When life gives you citruses&hellip; make videos! Citrus Video Productions has been telling visual stories for over two decades.</p>");
  $body = \App\v0_home_meta('about_body', '');
@endphp

<section id="about" class="relative overflow-hidden bg-citrus-green-tint py-28 text-ink md:py-36" style="scroll-margin-top: 80px">
  <x-citrus-wheel class="-right-20 top-8 opacity-30" :size="150" spin :float="false" />
  <x-citrus-wheel class="-left-20 bottom-0 opacity-25" :size="220" spin :float="false" />

  <div class="relative mx-auto max-w-6xl px-6">
    <p class="mb-3 font-display text-sm font-bold uppercase tracking-[0.3em] text-citrus-green-dark">
      {{ $eyebrow }}
    </p>
    <h2 class="max-w-3xl text-balance font-display text-5xl font-bold leading-none tracking-tight md:text-7xl">
      {{ $heading }}
    </h2>

    <div class="mt-10 grid gap-8 md:grid-cols-2 md:gap-14">
      <div class="space-y-4 text-pretty text-xl font-semibold leading-relaxed md:text-2xl">
        {!! \App\v0_rich_text($intro) !!}
      </div>
      <div class="space-y-5 text-pretty text-base leading-relaxed text-ink/80 md:text-lg">
        {!! \App\v0_rich_text($body) !!}
      </div>
    </div>
  </div>
</section>
