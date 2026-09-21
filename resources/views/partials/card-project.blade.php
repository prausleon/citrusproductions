{{--
  Reusable project card — ported from the WorkCard function in
  components/sections/work.tsx and reused (with a different $aspect) on the
  Works Archive masonry grid.

  Expects the WP loop to be positioned on a `project` post; call inside
  @while (have_posts()) the_post() ... @endwhile or a WP_Query loop.
--}}
@php
  $aspect = $aspect ?? 'aspect-video';
  $video = \App\v0_video_source(get_post_meta(get_the_ID(), 'vimeo_id', true), get_post_meta(get_the_ID(), 'video_file', true));
  $client = get_post_meta(get_the_ID(), 'client', true);
  $category = \App\v0_category_view(get_the_ID(), 'project');
  $thumb = \App\v0_video_thumbnail($video, get_the_ID(), 'large', get_post_meta(get_the_ID(), 'thumbnail', true));
@endphp

<div class="group relative overflow-hidden rounded-3xl bg-ink shadow-sm ring-1 ring-black/5">
  <div class="relative {{ $aspect }} overflow-hidden">
    @if ($thumb)
      <img
        src="{{ $thumb }}"
        alt="{!! get_the_title() !!} — {{ $client }}"
        class="h-full w-full object-cover transition-all duration-500 group-hover:scale-110 group-hover:blur-sm"
        loading="lazy"
      >
    @endif
    <div class="absolute inset-0 bg-gradient-to-t from-ink/90 via-ink/20 to-transparent"></div>

    {{--
      z-30: above the z-20 button overlay below, so the category badge —
      the only clickable thing in here — isn't blocked by that overlay's
      full-card (if invisible) hit area. See card-slice.blade.php too.
    --}}
    <div class="pointer-events-none absolute inset-x-0 bottom-0 z-30 p-5">
      @if ($category)
        <a href="{{ $category['link'] }}" class="pointer-events-auto mb-1 inline-block rounded-full bg-white/15 px-2.5 py-0.5 text-xs font-semibold text-white backdrop-blur transition hover:bg-white/30">
          {!! $category['name'] !!}
        </a>
      @endif
      <h3 class="font-display text-lg font-bold leading-tight text-white">{!! get_the_title() !!}</h3>
      <p class="text-sm text-white/70">{{ $client }}</p>
    </div>

    <div class="pointer-events-none absolute inset-0 z-10 bg-citrus-orange/50 opacity-0 mix-blend-multiply transition-opacity duration-300 group-hover:opacity-100"></div>
    <div class="absolute inset-0 z-20 flex items-center justify-center gap-3 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
      @if ($video['type'])
        <button
          type="button"
          data-video-trigger
          data-video-type="{{ $video['type'] }}"
          data-vimeo-id="{{ $video['id'] }}"
          data-video-url="{{ $video['url'] }}"
          data-video-title="{!! get_the_title() !!}"
          class="flex h-14 w-14 items-center justify-center rounded-full bg-white text-citrus-orange-dark shadow-lg transition hover:scale-110"
          aria-label="{!! sprintf(__('Play %s', 'sage'), get_the_title()) !!}"
        >
          <svg class="h-6 w-6 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
        </button>
      @endif
      <a
        href="{{ get_permalink() }}"
        class="flex h-14 w-14 items-center justify-center rounded-full bg-citrus-green text-white shadow-lg transition hover:scale-110"
        aria-label="{!! sprintf(__('Open %s case study', 'sage'), get_the_title()) !!}"
      >
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
      </a>
    </div>
  </div>
</div>
