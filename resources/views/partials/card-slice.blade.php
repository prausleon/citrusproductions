{{--
  Reusable slice card — ported from the inline card markup in
  components/sections/slices-section.tsx / components/views/slices-hub.tsx.
  Expects the WP loop positioned on a `slice` post.
--}}
@php
  $video = \App\v0_video_source(get_post_meta(get_the_ID(), 'cover_vimeo_id', true), get_post_meta(get_the_ID(), 'cover_video_file', true));
  $client = get_post_meta(get_the_ID(), 'client', true);
  $category = \App\v0_category_view(get_the_ID(), 'slice');
  $clips = \App\v0_meta_array(get_post_meta(get_the_ID(), 'clips', true));
  $thumb = \App\v0_video_thumbnail($video, get_the_ID(), 'large', get_post_meta(get_the_ID(), 'cover_thumbnail', true));
@endphp

<div class="group relative overflow-hidden rounded-3xl bg-citrus-green-dark text-left shadow-sm ring-1 ring-white/15">
  <div class="relative aspect-video overflow-hidden">
    @if ($thumb)
      <img
        src="{{ $thumb }}"
        alt="{!! get_the_title() !!} — {{ $client }}"
        class="h-full w-full object-cover transition-all duration-500 group-hover:scale-110 group-hover:blur-sm"
        loading="lazy"
      >
    @endif
    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>
    <div class="absolute inset-0 bg-citrus-green/60 opacity-0 mix-blend-multiply transition-opacity duration-300 group-hover:opacity-100"></div>

    <div class="absolute inset-0 flex items-center justify-center gap-3 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
      @if ($video['type'])
        <button
          type="button"
          data-video-trigger
          data-video-type="{{ $video['type'] }}"
          data-vimeo-id="{{ $video['id'] }}"
          data-video-url="{{ $video['url'] }}"
          data-video-title="{!! get_the_title() !!}"
          class="flex h-14 w-14 items-center justify-center rounded-full bg-white text-citrus-green-dark shadow-lg transition hover:scale-110"
          aria-label="{!! sprintf(__('Play %s', 'sage'), get_the_title()) !!}"
        >
          <svg class="h-6 w-6 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
        </button>
      @endif
      <a
        href="{{ get_permalink() }}"
        class="flex h-14 w-14 items-center justify-center rounded-full bg-citrus-orange text-white shadow-lg transition hover:scale-110"
        aria-label="{!! sprintf(__('Open %s series', 'sage'), get_the_title()) !!}"
      >
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
      </a>
    </div>
  </div>

  {{-- The category badge below is its own link, so it can't sit inside
       the "open series" <a> (nested anchors are invalid HTML). --}}
  <div class="w-full p-5 text-left">
    <span class="mb-1 inline-flex items-center gap-2 text-xs font-semibold text-white/70">
      @if ($category)
        <a href="{{ $category['link'] }}" class="rounded-full bg-white/15 px-2.5 py-0.5 backdrop-blur transition hover:bg-white/30">{!! $category['name'] !!}</a>
      @endif
      @if (! empty($clips))
        <span>{{ count($clips) }} {{ __('clips', 'sage') }}</span>
      @endif
    </span>
    <a href="{{ get_permalink() }}" class="block" aria-label="{!! sprintf(__('Open %s series', 'sage'), get_the_title()) !!}">
      <h3 class="mt-1 font-display text-lg font-bold leading-tight text-white">{!! get_the_title() !!}</h3>
      <p class="text-sm text-white/70">{{ $client }}</p>
    </a>
  </div>
</div>
