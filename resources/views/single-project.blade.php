{{--
  Project Inner — ported from components/views/project-inner.tsx.

  Per-post data is assembled by \App\v0_project_data() (app/helpers.php)
  into a single inline @php(...) assignment rather than a multi-statement
  @php...@endphp block — this project's installed Blade compiler mis-
  compiles a block that appears in the same file AFTER an earlier inline
  @php(...) call (here, @php(the_post()) on the @while line), so every
  per-post computation in this template stays a single-statement inline
  directive.
--}}
@extends('layouts.app')

@section('content')
  @while (have_posts()) @php(the_post())
    @php($p = \App\v0_project_data(get_the_ID()))
    @php($others = new \WP_Query(['post_type' => 'project', 'posts_per_page' => 3, 'post__not_in' => [get_the_ID()], 'orderby' => 'rand']))

    <div class="min-h-screen bg-background pb-28 pt-28">
      <div class="mx-auto max-w-5xl px-6">
        <a href="{{ get_post_type_archive_link('project') }}" class="mb-8 inline-flex items-center gap-2 rounded-full bg-ink/5 px-4 py-2 text-sm font-semibold text-ink transition hover:bg-citrus-orange hover:text-white">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
          {{ __('Back to work', 'sage') }}
        </a>

        <h1 class="text-balance font-display text-4xl font-bold tracking-tight text-ink md:text-6xl">{!! get_the_title() !!}</h1>
        <p class="mt-2 text-lg text-ink/60">{{ $p['client'] }} &middot; {{ $p['year'] }}</p>
        @if ($p['category'])
          <a href="{{ $p['category']['link'] }}" class="mt-4 inline-block rounded-full bg-citrus-orange/10 px-3 py-1 text-sm font-semibold text-citrus-orange transition hover:bg-citrus-orange/20">{!! $p['category']['name'] !!}</a>
        @endif

        {{-- 16:9 video stage — Vimeo embed or self-hosted <video> --}}
        @if ($p['video']['type'])
          <div class="mt-8 overflow-hidden rounded-3xl bg-black shadow-xl">
            <div class="relative aspect-video">
              @if ($p['video']['type'] === 'vimeo')
                <iframe
                  src="https://player.vimeo.com/video/{{ $p['video']['id'] }}?title=0&byline=0&portrait=0"
                  title="{!! get_the_title() !!}"
                  class="absolute inset-0 h-full w-full"
                  allow="autoplay; fullscreen; picture-in-picture"
                  allowfullscreen
                ></iframe>
              @else
                <video
                  class="absolute inset-0 h-full w-full"
                  controls
                  playsinline
                  @if ($p['thumb']) poster="{{ $p['thumb'] }}" @endif
                >
                  <source src="{{ $p['video']['url'] }}">
                </video>
              @endif
            </div>
          </div>
        @endif

        {{-- Metadata matrix --}}
        <div class="mt-12 grid gap-10 md:grid-cols-[1.4fr_1fr]">
          <div>
            <h2 class="font-display text-2xl font-bold text-ink">{{ __('The Squeeze', 'sage') }}</h2>
            <p class="mt-3 text-pretty text-lg leading-relaxed text-ink/70">{!! $p['summary'] !!}</p>

            @if (! empty($p['distribution']))
              <h3 class="mt-8 font-display text-lg font-bold text-ink">{{ __('Distribution', 'sage') }}</h3>
              <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($p['distribution'] as $d)
                  <span class="rounded-full bg-citrus-green/12 px-4 py-1.5 text-sm font-semibold text-citrus-green-dark">{{ $d }}</span>
                @endforeach
              </div>
            @endif
          </div>

          @if (! empty($p['credits']))
            <div class="rounded-3xl bg-citrus-cream p-7">
              <h3 class="font-display text-lg font-bold text-ink">{{ __('Credits', 'sage') }}</h3>
              <dl class="mt-4 space-y-4">
                @foreach ($p['credits'] as $c)
                  <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-ink/50">{{ $c['role'] ?? '' }}</dt>
                    <dd class="font-display font-bold text-ink">{{ $c['name'] ?? '' }}</dd>
                  </div>
                @endforeach
              </dl>
            </div>
          @endif
        </div>

        {{-- Behind the scenes gallery — hidden entirely when no images are uploaded --}}
        @if (! empty($p['gallery']))
          <div class="mt-16">
            <h2 class="font-display text-2xl font-bold text-ink">{{ __('Behind the Scenes', 'sage') }}</h2>
            <div class="mt-5 grid gap-4 sm:grid-cols-2 md:grid-cols-3">
              @foreach ($p['gallery'] as $i => $src)
                <div class="relative overflow-hidden rounded-2xl bg-ink/5 {{ $i === 0 ? 'sm:col-span-2 sm:row-span-2 aspect-video sm:aspect-square' : 'aspect-video' }}">
                  <img src="{{ $src }}" alt="{!! sprintf(__('Behind the scenes of %s', 'sage'), get_the_title()) !!}" class="h-full w-full object-cover" loading="lazy">
                </div>
              @endforeach
            </div>
          </div>
        @endif

        @if ($p['video']['type'] === 'vimeo')
          <div class="mt-14 flex justify-center">
            <a
              href="https://vimeo.com/{{ $p['video']['id'] }}"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center gap-2 rounded-full bg-ink px-8 py-4 font-semibold text-white transition hover:bg-citrus-orange"
            >
              {{ __('Watch on Vimeo', 'sage') }}
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
            </a>
          </div>
        @endif

        {{-- Other Work suggestions --}}
        @if ($others->have_posts())
          <div class="mt-20 border-t border-ink/10 pt-12">
            <h2 class="font-display text-2xl font-bold text-ink">{{ __('Other Work', 'sage') }}</h2>
            <div class="mt-6 grid gap-6 sm:grid-cols-3">
              @while ($others->have_posts()) @php($others->the_post())
                @php($other_video = \App\v0_video_source(get_post_meta(get_the_ID(), 'vimeo_id', true), get_post_meta(get_the_ID(), 'video_file', true)))
                @php($other_thumb = \App\v0_video_thumbnail($other_video, get_the_ID(), 'large', get_post_meta(get_the_ID(), 'thumbnail', true)))
                <a href="{{ get_permalink() }}" class="group overflow-hidden rounded-3xl bg-background text-left shadow-sm ring-1 ring-ink/10 transition hover:-translate-y-1 hover:shadow-lg">
                  <div class="relative aspect-video overflow-hidden">
                    @if ($other_thumb)
                      <img src="{{ $other_thumb }}" alt="{!! get_the_title() !!}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy">
                    @endif
                    <div class="absolute inset-0 bg-citrus-orange/40 opacity-0 mix-blend-multiply transition-opacity duration-300 group-hover:opacity-100"></div>
                  </div>
                  <div class="flex items-center justify-between gap-2 p-4">
                    <div>
                      <h3 class="font-display font-bold leading-tight text-ink">{!! get_the_title() !!}</h3>
                      <p class="text-sm text-ink/60">{{ get_post_meta(get_the_ID(), 'client', true) }}</p>
                    </div>
                    <svg class="h-5 w-5 shrink-0 text-citrus-orange transition group-hover:translate-x-0.5 group-hover:-translate-y-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                  </div>
                </a>
              @endwhile
              @php(wp_reset_postdata())
            </div>
          </div>
        @endif
      </div>
    </div>
  @endwhile

  @include('sections.contact')
@endsection
