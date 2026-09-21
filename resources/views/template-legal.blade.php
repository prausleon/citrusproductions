{{--
  Template Name: Legal Page (Citrus)
--}}
{{--
  Ported from components/views/legal-page.tsx (shared by Privacy Policy and
  Terms & Conditions in the v0 design). Assign this template to any Page in
  Page Attributes, then fill in the "Legal Page Content" meta box.

  Per-post data comes from \App\v0_legal_data() (app/helpers.php) via a
  single inline @php(...) assignment rather than a @php...@endphp block —
  see single-project.blade.php for why.
--}}
@extends('layouts.app')

@section('content')
  @while (have_posts()) @php(the_post())
    @php($legal = \App\v0_legal_data(get_the_ID()))

    <div class="min-h-screen bg-background pb-28 pt-28">
      <div class="mx-auto max-w-3xl px-6">
        <a href="{{ home_url('/') }}" class="mb-8 inline-flex items-center gap-2 rounded-full bg-ink/5 px-4 py-2 text-sm font-semibold text-ink transition hover:bg-citrus-orange hover:text-white">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
          {{ __('Back home', 'sage') }}
        </a>

        <h1 class="font-display text-4xl font-bold tracking-tight text-ink md:text-6xl">{!! get_the_title() !!}</h1>
        @if ($legal['subtitle'])
          <p class="mt-4 text-pretty text-lg text-ink/60">{!! $legal['subtitle'] !!}</p>
        @endif

        <div class="mt-12 space-y-10">
          @foreach ($legal['sections'] as $section)
            <section>
              <h2 class="font-display text-2xl font-bold text-ink">{{ $section['title'] ?? '' }}</h2>
              <p class="mt-3 text-pretty text-lg leading-relaxed text-ink/70">{{ $section['body'] ?? '' }}</p>
            </section>
          @endforeach
        </div>
      </div>
    </div>
  @endwhile

  @include('sections.contact')
@endsection
