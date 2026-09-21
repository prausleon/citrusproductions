{{--
  Template Name: Homepage (Citrus)
--}}
{{--
  Home page — ported from app/page.tsx (the `view === 'home'` branch).

  Unlike a plain front-page.blade.php (which WordPress would force onto the
  site's front page regardless of template choice), this is a normal,
  selectable Page template: create a Page in wp-admin, set this template
  under Page Attributes, fill in the "Homepage Content" meta box, then set
  that Page as your static front page under Settings → Reading.

  All section copy is read from that Page's CMB2 fields (see
  app/Fields/v0-auto-fields.php → "Homepage Content"); the Work/Slices
  grids still pull live from the `project` / `slice` CPTs.
--}}
@extends('layouts.app')

@section('content')
  @while (have_posts()) @php(the_post())
    <div>
      @include('sections.hero')
      @include('sections.about')
      @include('sections.work')
      @include('sections.slices')
      @include('sections.clients')
      @include('sections.competencies')
      @include('sections.contact')
    </div>
  @endwhile
@endsection
