{{--
  Works Archive — ported from components/views/works-archive.tsx.
  Every published `project`, all landscape thumbnails, no pagination — the
  query itself is set to return every post (see app/post-types.php's
  `pre_get_posts` hook), so this always renders exactly one page.
--}}
@extends('layouts.app')

@php
  $eyebrow = \App\v0_setting('works_archive_eyebrow', 'The Full Basket');
  $heading = \App\v0_setting('works_archive_heading') ? esc_html(\App\v0_setting('works_archive_heading')) : (post_type_archive_title('', false) ?: __('Works Archive', 'sage'));
  $intro = \App\v0_setting('works_archive_intro', "Every slice of life we've put in motion. Hover a piece to play the film or dive into the full case study.");
  $reel_label = \App\v0_setting('works_archive_reel_label', 'Visit our full Vimeo reel');
  $reel_url = \App\v0_setting('works_archive_reel_url', 'https://vimeo.com/citrusreel');
@endphp

@section('content')
  <div class="min-h-screen bg-citrus-orange pb-28 pt-28 text-white">
    <div class="mx-auto max-w-6xl px-6">
      <a href="{{ home_url('/') }}" class="mb-8 inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white hover:text-citrus-orange-dark">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        {{ __('Back home', 'sage') }}
      </a>

      <p class="mb-3 font-display text-sm font-bold uppercase tracking-[0.3em] text-white/80">{{ $eyebrow }}</p>
      <h1 class="font-display text-5xl font-bold tracking-tight md:text-7xl">{!! $heading !!}</h1>
      <p class="mt-4 max-w-2xl text-pretty text-lg text-white/85">
        {!! $intro !!}
      </p>

      @if (is_category())
        <p class="mt-4 text-lg font-semibold text-white/90">
          {{ __('Filtered by:', 'sage') }} {!! single_cat_title('', false) !!}
          &mdash; <a href="{{ get_post_type_archive_link('project') }}" class="underline underline-offset-2 hover:text-white">{{ __('View all work', 'sage') }}</a>
        </p>
      @endif

      @if (have_posts())
        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          @while (have_posts()) @php(the_post())
            @include('partials.card-project')
          @endwhile
        </div>
      @else
        <x-alert type="warning">{{ __('No projects have been published yet.', 'sage') }}</x-alert>
      @endif

      <div class="mt-16 flex justify-center">
        <a
          href="{{ $reel_url }}"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center gap-2 rounded-full bg-white px-8 py-4 font-semibold text-citrus-orange-dark shadow-lg transition hover:bg-citrus-cream"
        >
          {{ $reel_label }}
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
        </a>
      </div>
    </div>
  </div>

  @include('sections.contact')
@endsection
