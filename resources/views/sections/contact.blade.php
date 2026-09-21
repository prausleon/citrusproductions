{{--
  Ported from components/sections/contact.tsx.
  The static React <form> (name/company/mobile/email/message) is replaced
  with a Contact Form 7 shortcode — see the "Contact Form 7 ID" field in
  the Homepage Content meta box (Homepage page → Page Attributes → template
  "Homepage (Citrus)"). The CF7 form ID is stored there, with a styled
  static fallback rendered when no form has been configured yet. See
  CF7-INTEGRATION.md for the recommended CF7 form-tag template.

  This section also carries the site footer bar (logo, legal links,
  copyright), matching the original design where <Contact> renders at the
  bottom of every view. Since this partial is reused on every archive/
  single/legal template (not just the homepage), every field below is read
  via v0_home_meta(), which finds the "Homepage (Citrus)" page's content
  from anywhere in the site — see app/helpers.php.
--}}
@php
  $eyebrow = \App\v0_home_meta('contact_eyebrow', 'Ready to do some picking?');
  $heading = \App\v0_home_meta('contact_heading', 'Contact Us');
  $email = \App\v0_home_meta('contact_email', 'info@citrusproductions.net');
  $address = \App\v0_home_meta('contact_address', 'Unit 1015, Medical Plaza Ortigas, San Miguel Avenue, Ortigas Center, Pasig City, Philippines');
  $cf7_id = \App\v0_home_meta('contact_form_id', '');

  $default_socials = [
    ['icon' => 'vimeo', 'label' => 'Vimeo', 'url' => 'https://vimeo.com/citrusreel'],
    ['icon' => 'facebook', 'label' => 'Facebook', 'url' => 'https://facebook.com/CitrusVideoProductions'],
    ['icon' => 'instagram', 'label' => 'Instagram', 'url' => 'https://instagram.com/citrusvideos'],
    ['icon' => 'youtube', 'label' => 'YouTube', 'url' => 'https://youtube.com/@citrusvideoprod'],
  ];
  $socials = \App\v0_home_meta('contact_socials', $default_socials);

  $footer_logo = \App\v0_image_or(\App\v0_home_meta('footer_logo'), 'citrus-logo.png');
  $footer_tagline = \App\v0_home_meta('footer_tagline', 'Citrus Video Productions');

  $privacy_url = get_permalink(get_page_by_path('privacy-policy'));
  $terms_url = get_permalink(get_page_by_path('terms-and-conditions'));

  $social_icons = [
    'vimeo' => '<path d="M23.977 6.416c-.105 2.338-1.739 5.543-4.894 9.609-3.268 4.247-6.026 6.37-8.29 6.37-1.409 0-2.578-1.294-3.553-3.881L5.322 11.4C4.603 8.816 3.834 7.522 3.01 7.522c-.179 0-.806.378-1.881 1.132L0 7.197c1.185-1.044 2.351-2.084 3.501-3.128C5.08 2.701 6.266 1.984 7.055 1.91c1.867-.18 3.016 1.1 3.447 3.838.465 2.953.789 4.789.971 5.507.539 2.45 1.131 3.674 1.776 3.674.502 0 1.256-.796 2.265-2.385 1.004-1.589 1.54-2.797 1.612-3.628.144-1.371-.395-2.061-1.614-2.061-.574 0-1.167.121-1.777.391 1.186-3.868 3.434-5.757 6.762-5.637 2.465.06 3.628 1.664 3.493 4.797l-.013.021z"/>',
    'facebook' => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>',
    'instagram' => '<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>',
    'youtube' => '<path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>',
  ];
@endphp

<section id="contact" class="relative overflow-hidden bg-citrus-orange py-28 text-white md:py-36" style="scroll-margin-top: 80px">
  <x-citrus-wheel class="-right-16 -top-10 opacity-20" :size="300" :mobile-scale="0.45" spin :float="false" />
  <x-citrus-wheel class="-left-20 bottom-0 opacity-15" :size="240" :mobile-scale="0.45" spin :float="false" />

  <div class="relative mx-auto max-w-6xl px-6">
    <p class="mb-3 font-display text-sm font-bold uppercase tracking-[0.3em] text-white/80">{{ $eyebrow }}</p>
    <h2 class="max-w-3xl text-balance font-display text-5xl font-bold leading-none tracking-tight md:text-7xl">{{ $heading }}</h2>

    <div class="mt-12 grid gap-10 lg:grid-cols-[1fr_1.1fr]">
      {{-- Left: contact details --}}
      <div class="flex h-full flex-col gap-4">
        <a href="mailto:{{ $email }}" class="group rounded-3xl bg-white/10 p-7 backdrop-blur transition hover:bg-white/20">
          <svg class="mb-4 h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z" opacity="0"/><path d="M22 6l-10 7L2 6"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>
          <p class="text-sm font-semibold uppercase tracking-wide text-white/70">{{ __('Email', 'sage') }}</p>
          <p class="mt-1 break-all font-display text-lg font-bold">{{ $email }}</p>
        </a>
        <div class="flex flex-1 flex-col rounded-3xl bg-white/10 p-7 backdrop-blur">
          <svg class="mb-4 h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <p class="text-sm font-semibold uppercase tracking-wide text-white/70">{{ __('Studio', 'sage') }}</p>
          <p class="mt-1 text-pretty font-medium leading-relaxed">{!! $address !!}</p>
        </div>
        <div class="rounded-3xl bg-white/10 p-7 backdrop-blur">
          <p class="text-sm font-semibold uppercase tracking-wide text-white/70">{{ __('Connect with us', 'sage') }}</p>
          <div class="mt-4 flex flex-wrap gap-3">
            @foreach ($socials as $s)
              @continue(empty($s['url']))
              <a
                href="{{ $s['url'] }}"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="{{ $s['label'] ?? $s['icon'] }}"
                class="flex h-12 w-12 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white hover:text-citrus-orange"
              >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">{!! $social_icons[$s['icon']] ?? '' !!}</svg>
              </a>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Right: Contact Form 7 enquiry form --}}
      <div class="rounded-3xl bg-white p-7 text-ink shadow-xl md:p-9">
        @if ($cf7_id)
          {!! do_shortcode('[contact-form-7 id="'.esc_attr($cf7_id).'"]') !!}
        @else
          @include('partials.contact-form-fallback')
        @endif
      </div>
    </div>

    <footer class="mt-16 flex flex-col items-start justify-between gap-6 border-t border-white/20 pt-8 sm:flex-row sm:items-center">
      <div class="flex items-center gap-3">
        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-white">
          <img src="{{ $footer_logo }}" alt="" class="h-8 w-8 object-contain">
        </span>
        <span class="font-display text-lg font-bold">{{ $footer_tagline }}</span>
      </div>
      <div class="flex flex-wrap items-center gap-6 text-sm text-white/80">
        @if ($privacy_url)
          <a href="{{ $privacy_url }}" class="font-medium underline-offset-4 hover:underline">{{ __('Privacy Policy', 'sage') }}</a>
        @endif
        @if ($terms_url)
          <a href="{{ $terms_url }}" class="font-medium underline-offset-4 hover:underline">{{ __('Terms and Conditions', 'sage') }}</a>
        @endif
        <span>&copy; {{ date('Y') }} {{ $footer_tagline }}</span>
      </div>
    </footer>
  </div>
</section>
