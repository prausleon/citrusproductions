{{--
  Ported from components/sections/competencies.tsx. The open/close accordion
  state is handled client-side in resources/js/app.js (initAccordion); the
  first panel starts open, matching the original component's default state.
--}}
@php
  $eyebrow = \App\v0_home_meta('competencies_eyebrow', 'The Secret Recipe');
  $heading = \App\v0_home_meta('competencies_heading', 'Our Competencies');

  $default_phases = [
    ['phase' => 'Pre-Production', 'tasks' => "Concept Development\nCreative Direction\nScriptwriting\nStoryboarding\nTalent coordination"],
    ['phase' => 'Production', 'tasks' => "Shoot coordination\nDirection and Cinematography\nProduction Design\nLive Sound Recording"],
    ['phase' => 'Post-Production', 'tasks' => "Offline editing\nOnline Editing\nVO Recording\nScoring and Original Composition\nSound Design\nVFX and GFX\nAnimation\nMotion Graphics"],
  ];
  $phases = \App\v0_home_meta('competencies_phases', $default_phases);
@endphp

<section id="competencies" class="relative bg-background py-28 md:py-36" style="scroll-margin-top: 80px">
  <div class="mx-auto max-w-5xl px-6">
    <p class="mb-3 font-display text-sm font-bold uppercase tracking-[0.3em] text-citrus-orange">{{ $eyebrow }}</p>
    <h2 class="font-display text-5xl font-bold tracking-tight text-ink md:text-6xl">{{ $heading }}</h2>

    <div class="mt-12 space-y-4">
      @foreach ($phases as $i => $phase)
        @php($tasks = array_values(array_filter(array_map('trim', explode("\n", $phase['tasks'] ?? '')))))
        @php($open = $i === 0)
        <div
          data-accordion-item
          class="overflow-hidden rounded-3xl border transition-colors {{ $open ? 'border-citrus-orange/40 bg-citrus-cream' : 'border-ink/10 bg-background' }}"
        >
          <button
            type="button"
            data-accordion-trigger
            class="flex w-full items-center justify-between gap-4 px-6 py-6 text-left md:px-8"
            aria-expanded="{{ $open ? 'true' : 'false' }}"
          >
            <span class="flex items-center gap-4">
              <span class="font-display text-sm font-bold text-citrus-orange">0{{ $i + 1 }}</span>
              <span class="font-display text-2xl font-bold tracking-tight text-ink md:text-3xl">{{ $phase['phase'] ?? '' }}</span>
            </span>
            <span
              data-accordion-icon
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full transition {{ $open ? 'rotate-45 bg-citrus-orange text-white' : 'bg-ink/5 text-ink' }}"
            >
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </span>
          </button>
          <div data-accordion-panel class="grid transition-all duration-500 ease-out" style="grid-template-rows: {{ $open ? '1fr' : '0fr' }}">
            <div class="overflow-hidden">
              {{--
                columns-* (not grid) so tasks read top-to-bottom down the
                first column, then continue top-to-bottom in the second —
                a grid with grid-cols-2 instead fills left-to-right,
                row by row, which reads out of order for a task list.
              --}}
              <ul class="columns-1 gap-x-8 px-6 pb-8 sm:columns-2 md:px-8">
                @foreach ($tasks as $task)
                  <li class="mb-3 flex items-center gap-3 break-inside-avoid text-ink/80">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-citrus-green/15 text-citrus-green-dark">
                      <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                    <span class="font-medium">{!! $task !!}</span>
                  </li>
                @endforeach
              </ul>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
