{{--
  Slice Inner — ported from components/views/slice-inner.tsx.

  Per-post data is assembled by \App\v0_slice_data() (app/helpers.php) into
  a single inline @php(...) assignment rather than a multi-statement
  @php...@endphp block — see single-project.blade.php for why.
--}}
@extends('layouts.app')

@section('content')
  @while (have_posts()) @php(the_post())
    @php($s = \App\v0_slice_data(get_the_ID()))
    @php($others = new \WP_Query(['post_type' => 'slice', 'posts_per_page' => 3, 'post__not_in' => [get_the_ID()], 'orderby' => 'rand']))

    <div class="min-h-screen bg-background pb-28 pt-28">
      <div class="mx-auto max-w-5xl px-6">
        <a href="{{ get_post_type_archive_link('slice') }}" class="mb-8 inline-flex items-center gap-2 rounded-full bg-ink/5 px-4 py-2 text-sm font-semibold text-ink transition hover:bg-citrus-green hover:text-white">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
          {{ __('Back to Citrus Slices', 'sage') }}
        </a>

        <h1 class="text-balance font-display text-4xl font-bold tracking-tight text-ink md:text-6xl">{!! get_the_title() !!}</h1>
        <p class="mt-2 text-lg text-ink/60">
          {{ $s['client'] }}
          @if (! empty($s['clips']))
            &middot; {{ count($s['clips']) }} {{ __('clips', 'sage') }}
          @endif
        </p>
        @if ($s['category'])
          <a href="{{ $s['category']['link'] }}" class="mt-4 inline-block rounded-full bg-citrus-green/15 px-3 py-1 text-sm font-semibold text-citrus-green-dark transition hover:bg-citrus-green/25">{!! $s['category']['name'] !!}</a>
        @endif

        {{-- Video gallery — a Citrus Slices project is a series of clips --}}
        @if (! empty($s['clips']))
          <div class="mt-10">
            <h2 class="font-display text-2xl font-bold text-ink">{{ __('The Series', 'sage') }}</h2>
            <p class="mt-1 text-ink/50">{{ __('Tap any clip to play it.', 'sage') }}</p>

            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
              @foreach ($s['clips'] as $clip)
                @continue(empty($clip['video']['type']))
                <button
                  type="button"
                  data-video-trigger
                  data-video-type="{{ $clip['video']['type'] }}"
                  data-vimeo-id="{{ $clip['video']['id'] }}"
                  data-video-url="{{ $clip['video']['url'] }}"
                  data-video-title="{{ $clip['title'] }}"
                  class="group relative overflow-hidden rounded-3xl bg-ink text-left shadow-sm ring-1 ring-black/5"
                >
                  <div class="relative aspect-video overflow-hidden">
                    @if ($clip['thumb'])
                      <img
                        src="{{ $clip['thumb'] }}"
                        alt="{{ $clip['title'] }}"
                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110 group-hover:blur-sm"
                        loading="lazy"
                      >
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-ink/90 via-ink/20 to-transparent"></div>
                    <div class="absolute inset-0 z-10 bg-citrus-green/50 opacity-0 mix-blend-multiply transition-opacity duration-300 group-hover:opacity-100"></div>

                    <span class="absolute inset-0 z-20 flex items-center justify-center opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                      <span class="flex h-14 w-14 items-center justify-center rounded-full bg-white text-citrus-green-dark shadow-lg transition group-hover:scale-110">
                        <svg class="h-6 w-6 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                      </span>
                    </span>

                    <div class="pointer-events-none absolute inset-x-0 bottom-0 z-0 p-4">
                      <h3 class="font-display text-base font-bold leading-tight text-white">{{ $clip['title'] }}</h3>
                    </div>
                  </div>
                </button>
              @endforeach
            </div>
          </div>
        @elseif ($s['cover_video']['type'])
          {{-- No clips set — show one large player for the cover video instead of an empty "Series" section. --}}
          <div class="mt-10 overflow-hidden rounded-3xl bg-black shadow-xl">
            <div class="relative aspect-video">
              @if ($s['cover_video']['type'] === 'vimeo')
                <iframe
                  src="https://player.vimeo.com/video/{{ $s['cover_video']['id'] }}?title=0&byline=0&portrait=0"
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
                  @if ($s['cover_thumb']) poster="{{ $s['cover_thumb'] }}" @endif
                >
                  <source src="{{ $s['cover_video']['url'] }}">
                </video>
              @endif
            </div>
          </div>
        @endif

        {{-- Metadata matrix --}}
        <div class="mt-16 grid gap-10 md:grid-cols-[1.4fr_1fr]">
          <div>
            <h2 class="font-display text-2xl font-bold text-ink">{{ __('The Squeeze', 'sage') }}</h2>
            <p class="mt-3 text-pretty text-lg leading-relaxed text-ink/70">{!! $s['summary'] !!}</p>

            @if (! empty($s['distribution']))
              <h3 class="mt-8 font-display text-lg font-bold text-ink">{{ __('Distribution', 'sage') }}</h3>
              <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($s['distribution'] as $d)
                  <span class="rounded-full bg-citrus-green/12 px-4 py-1.5 text-sm font-semibold text-citrus-green-dark">{{ $d }}</span>
                @endforeach
              </div>
            @endif
          </div>

          <div class="rounded-3xl bg-citrus-green-tint p-7">
            <h3 class="font-display text-lg font-bold text-ink">{{ __('Credits', 'sage') }}</h3>
            <dl class="mt-4 space-y-4">
              @foreach ($s['credits'] as $c)
                <div>
                  <dt class="text-xs font-semibold uppercase tracking-wide text-ink/50">{{ $c['role'] ?? '' }}</dt>
                  <dd class="font-display font-bold text-ink">{{ $c['name'] ?? '' }}</dd>
                </div>
              @endforeach
            </dl>
          </div>
        </div>

        {{-- Behind the scenes gallery — hidden entirely when no images are uploaded --}}
        @if (! empty($s['gallery']))
          <div class="mt-16">
            <h2 class="font-display text-2xl font-bold text-ink">{{ __('Behind the Scenes', 'sage') }}</h2>
            <div class="mt-5 grid gap-4 sm:grid-cols-2 md:grid-cols-3">
              @foreach ($s['gallery'] as $i => $src)
                <div class="relative overflow-hidden rounded-2xl bg-ink/5 {{ $i === 0 ? 'sm:col-span-2 sm:row-span-2 aspect-video sm:aspect-square' : 'aspect-video' }}">
                  <img src="{{ $src }}" alt="{!! sprintf(__('Behind the scenes of %s', 'sage'), get_the_title()) !!}" class="h-full w-full object-cover" loading="lazy">
                </div>
              @endforeach
            </div>
          </div>
        @endif

        {{-- Other Slices suggestions --}}
        @if ($others->have_posts())
          <div class="mt-20 border-t border-ink/10 pt-12">
            <h2 class="font-display text-2xl font-bold text-ink">{{ __('Other Citrus Slices', 'sage') }}</h2>
            <div class="mt-6 grid gap-6 sm:grid-cols-3">
              @while ($others->have_posts()) @php($others->the_post())
                @php($other_video = \App\v0_video_source(get_post_meta(get_the_ID(), 'cover_vimeo_id', true), get_post_meta(get_the_ID(), 'cover_video_file', true)))
                @php($other_cover = \App\v0_video_thumbnail($other_video, get_the_ID(), 'large', get_post_meta(get_the_ID(), 'cover_thumbnail', true)))
                <a href="{{ get_permalink() }}" class="group overflow-hidden rounded-3xl bg-background text-left shadow-sm ring-1 ring-ink/10 transition hover:-translate-y-1 hover:shadow-lg">
                  <div class="relative aspect-video overflow-hidden">
                    @if ($other_cover)
                      <img src="{{ $other_cover }}" alt="{!! get_the_title() !!}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy">
                    @endif
                    <div class="absolute inset-0 bg-citrus-green/40 opacity-0 mix-blend-multiply transition-opacity duration-300 group-hover:opacity-100"></div>
                  </div>
                  <div class="flex items-center justify-between gap-2 p-4">
                    <div>
                      <h3 class="font-display font-bold leading-tight text-ink">{!! get_the_title() !!}</h3>
                      <p class="text-sm text-ink/60">{{ get_post_meta(get_the_ID(), 'client', true) }}</p>
                    </div>
                    <svg class="h-5 w-5 shrink-0 text-citrus-green-dark transition group-hover:translate-x-0.5 group-hover:-translate-y-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
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
