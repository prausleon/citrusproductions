{{--
  Ported from components/sections/slices-section.tsx.
  Pulls up to 6 `slice` posts flagged "Feature on Homepage"; falls back to
  the latest 6 published slices if none are flagged yet.
--}}
@php
  $eyebrow = \App\v0_home_meta('slices_eyebrow', 'The Fruits of Our Labor');
  $heading = \App\v0_home_meta('slices_heading', 'Citrus Slices');
  $subheading = \App\v0_home_meta('slices_subheading', 'Citrus Slices is our bite-sized video series service, specialized for social media posting.');
  $cta_label = \App\v0_home_meta('slices_cta_label', 'Explore Citrus Slices');

  $slices_query = new \WP_Query([
    'post_type' => 'slice',
    'posts_per_page' => 6,
    'orderby' => 'menu_order date',
    'order' => 'ASC',
    'meta_key' => 'featured',
    'meta_value' => 'on',
  ]);

  if (! $slices_query->have_posts()) {
    $slices_query = new \WP_Query([
      'post_type' => 'slice',
      'posts_per_page' => 6,
      'orderby' => 'menu_order date',
      'order' => 'ASC',
    ]);
  }
@endphp

<section id="slices" class="relative overflow-hidden bg-citrus-green py-28 text-white md:py-36" style="scroll-margin-top: 80px">
  <x-citrus-wheel class="-right-8 top-16 opacity-20" :size="200" spin :float="false" clip="quarter" />
  <x-citrus-wheel class="-left-6 bottom-10 opacity-15" :size="160" spin :float="false" clip="quarter" />

  <div class="relative mx-auto max-w-6xl px-6">
    <div class="max-w-xl">
      <p class="mb-3 font-display text-sm font-bold uppercase tracking-[0.3em] text-white/80">{{ $eyebrow }}</p>
      <h2 class="font-display text-5xl font-bold tracking-tight md:text-6xl">{{ $heading }}</h2>
      <p class="mt-3 text-pretty text-lg text-white/85">{!! $subheading !!}</p>
    </div>

    @if ($slices_query->have_posts())
      <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @while ($slices_query->have_posts()) @php($slices_query->the_post())
          @include('partials.card-slice')
        @endwhile
      </div>
    @endif
    @php(wp_reset_postdata())

    <div class="mt-12 flex justify-center">
      <a
        href="{{ get_post_type_archive_link('slice') }}"
        class="rounded-full bg-white px-8 py-3.5 font-semibold text-citrus-green-dark shadow-lg transition hover:bg-citrus-cream"
      >
        {{ $cta_label }}
      </a>
    </div>
  </div>
</section>
