{{--
  Sensible fallback shown when no Contact Form 7 ID is set on the "Contact
  Form 7 ID" field (Settings → Citrus Site Content). Purely presentational —
  wire up a real form ID as soon as one exists. Field layout mirrors the
  original v0 form (name/company/mobile/email/message) so the design never
  regresses while a form is being set up.
--}}
@php
  $fields = [
    ['name' => 'name', 'label' => __('Name', 'sage'), 'type' => 'text', 'autocomplete' => 'name', 'required' => true],
    ['name' => 'company', 'label' => __('Company', 'sage'), 'type' => 'text', 'autocomplete' => 'organization', 'required' => false],
    ['name' => 'mobile', 'label' => __('Mobile Number', 'sage'), 'type' => 'tel', 'autocomplete' => 'tel', 'required' => false],
    ['name' => 'email', 'label' => __('Email Address', 'sage'), 'type' => 'email', 'autocomplete' => 'email', 'required' => true],
  ];
@endphp

<div class="rounded-2xl border border-dashed border-citrus-orange/40 bg-citrus-cream/40 p-1">
  <p class="px-4 pt-3 text-xs font-semibold uppercase tracking-wide text-citrus-orange-dark">
    {{ __('Preview only — set a Contact Form 7 ID in Site Content to go live.', 'sage') }}
  </p>
  <form class="flex flex-col gap-4 p-4" onsubmit="return false">
    <div class="grid gap-4 sm:grid-cols-2">
      @foreach ($fields as $f)
        <div>
          <label class="mb-1.5 block text-sm font-semibold text-ink/70">
            {{ $f['label'] }}
            @if ($f['required'])<span class="text-citrus-orange"> *</span>@endif
          </label>
          <input
            type="{{ $f['type'] }}"
            autocomplete="{{ $f['autocomplete'] }}"
            @if ($f['required']) required @endif
            disabled
            class="w-full rounded-xl border border-ink/15 bg-citrus-cream/40 px-4 py-3 text-ink outline-none transition focus:border-citrus-orange focus:ring-2 focus:ring-citrus-orange/30"
          >
        </div>
      @endforeach
    </div>
    <div>
      <label class="mb-1.5 block text-sm font-semibold text-ink/70">{{ __('Message', 'sage') }}<span class="text-citrus-orange"> *</span></label>
      <textarea rows="4" required disabled class="w-full resize-y rounded-xl border border-ink/15 bg-citrus-cream/40 px-4 py-3 text-ink outline-none transition focus:border-citrus-orange focus:ring-2 focus:ring-citrus-orange/30"></textarea>
    </div>
    <button type="submit" disabled class="mt-1 cursor-not-allowed self-start rounded-full bg-citrus-orange/60 px-8 py-3.5 font-semibold text-white">
      {{ __('Send Message', 'sage') }}
    </button>
  </form>
</div>
