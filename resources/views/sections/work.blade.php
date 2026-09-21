{{--
  Ported from components/sections/work.tsx.
  Pulls up to 6 `project` posts flagged "Feature on Homepage"; falls back to
  the latest 6 published projects if none are flagged yet.
--}}
@php
  $eyebrow = \App\v0_home_meta('work_eyebrow', 'The Fruits of Our Labor');
  $heading = \App\v0_home_meta('work_heading', 'Our Work');
  $subheading = \App\v0_home_meta('work_subheading', 'From conceptualization to distribution, we take your ideas from seed, to script, to the screen.');
  $cta_label = \App\v0_home_meta('work_cta_label', 'View all work');

  $work_query = new \WP_Query([
    'post_type' => 'project',
    'posts_per_page' => 6,
    'orderby' => 'menu_order date',
    'order' => 'ASC',
    'meta_key' => 'featured',
    'meta_value' => 'on',
  ]);

  if (! $work_query->have_posts()) {
    $work_query = new \WP_Query([
      'post_type' => 'project',
      'posts_per_page' => 6,
      'orderby' => 'menu_order date',
      'order' => 'ASC',
    ]);
  }
@endphp

<section id="work" class="relative overflow-hidden bg-citrus-orange py-28 text-white md:py-36" style="scroll-margin-top: 80px">
  <x-citrus-wheel class="-right-14 top-16 opacity-20" :size="220" spin :float="false" />
  <x-citrus-wheel class="-left-10 bottom-10 opacity-15" :size="180" spin :float="false" />

  <div class="relative mx-auto max-w-6xl px-6">
    <div class="max-w-xl">
      <p class="mb-3 font-display text-sm font-bold uppercase tracking-[0.3em] text-white/80">{{ $eyebrow }}</p>
      <h2 class="font-display text-5xl font-bold tracking-tight md:text-6xl">{{ $heading }}</h2>
      <p class="mt-3 text-pretty text-lg text-white/85">{!! $subheading !!}</p>
    </div>

    @if ($work_query->have_posts())
      <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @while ($work_query->have_posts()) @php($work_query->the_post())
          @include('partials.card-project', ['aspect' => 'aspect-video'])
        @endwhile
      </div>
    @endif
    @php(wp_reset_postdata())

    <div class="mt-12 flex justify-center">
      <a
        href="{{ get_post_type_archive_link('project') }}"
        class="rounded-full bg-white px-8 py-3.5 font-semibold text-citrus-orange-dark shadow-lg transition hover:bg-citrus-cream"
      >
        {{ $cta_label }}
      </a>
    </div>
  </div>
</section>
