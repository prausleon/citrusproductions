{{-- Slices Hub — ported from components/views/slices-hub.tsx. --}}
@extends('layouts.app')

@php
  $eyebrow = \App\v0_setting('slices_archive_eyebrow', 'Bite-Sized & Buzzy');
  $heading = \App\v0_setting('slices_archive_heading') ? esc_html(\App\v0_setting('slices_archive_heading')) : (post_type_archive_title('', false) ?: __('Citrus Slices', 'sage'));
  $intro = \App\v0_setting('slices_archive_intro', 'Our short-form, social-first series service. Each series is a set of vertical clips built to stop the scroll and keep your feed fresh, juicy, and always in season.');
@endphp

@section('content')
  <div class="min-h-screen bg-citrus-green pb-28 pt-28 text-white">
    <div class="mx-auto max-w-6xl px-6">
      <a href="{{ home_url('/') }}" class="mb-8 inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white hover:text-citrus-green-dark">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        {{ __('Back home', 'sage') }}
      </a>

      <p class="mb-3 font-display text-sm font-bold uppercase tracking-[0.3em] text-white/80">{{ $eyebrow }}</p>
      <h1 class="font-display text-5xl font-bold tracking-tight md:text-7xl">{!! $heading !!}</h1>
      <p class="mt-4 max-w-2xl text-pretty text-lg text-white/85">
        {!! $intro !!}
      </p>

      @if (is_category())
        <p class="mt-4 text-sm font-semibold text-white/90">
          {{ __('Filtered by:', 'sage') }} {!! single_cat_title('', false) !!}
          &mdash; <a href="{{ get_post_type_archive_link('slice') }}" class="underline underline-offset-2 hover:text-white">{{ __('View all Citrus Slices', 'sage') }}</a>
        </p>
      @endif

      @if (have_posts())
        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          @while (have_posts()) @php(the_post())
            @include('partials.card-slice')
          @endwhile
        </div>
      @else
        <x-alert type="warning">{{ __('No slice series have been published yet.', 'sage') }}</x-alert>
      @endif

      {!! get_the_posts_navigation() !!}
    </div>
  </div>

  @include('sections.contact')
@endsection
