{{--
  Ported from components/sections/clients.tsx.
  Clients now upload a logo (CMB2 `file` field) instead of typing a plain
  name — the name field stays as the logo's alt text, and doubles as a
  text-pill fallback for any client without a logo uploaded yet.
--}}
@php
  $eyebrow = \App\v0_home_meta('clients_eyebrow', 'The Harvest');
  $heading = \App\v0_home_meta('clients_heading', 'Our Clients');
  $subheading = \App\v0_home_meta('clients_subheading', 'The brands that trust us to help their messages bear fruit.');

  $default_clients = ['AyalaLand', 'SMDC', 'Signature Series', 'Filinvest', 'Federal Land', 'Tokyo Tokyo', 'BPI'];
  $clients_group = \App\v0_home_meta('clients_list', []);
  $clients = ! empty($clients_group)
    ? array_values(array_filter($clients_group, fn ($c) => ! empty($c['name']) || ! empty($c['logo'])))
    : array_map(fn ($name) => ['name' => $name, 'logo' => ''], $default_clients);

  // Durations are how long one full loop takes — higher = slower.
  $rows = [
    ['direction' => 'left', 'duration' => 48],
    ['direction' => 'right', 'duration' => 40],
    ['direction' => 'left', 'duration' => 56],
  ];
@endphp

<section id="clients" class="relative overflow-hidden bg-citrus-cream py-24 md:py-32" style="scroll-margin-top: 80px">
  <x-citrus-wheel class="-right-16 -top-12 opacity-25" :size="220" spin :float="false" :src="\App\v0_image('orange-basket.png')" />
  <x-citrus-wheel class="-left-16 bottom-0 opacity-20" :size="170" spin :float="false" :src="\App\v0_image('orange-basket.png')" />

  <div class="relative mx-auto max-w-3xl px-6 text-center">
    <p class="mb-3 font-display text-sm font-bold uppercase tracking-[0.3em] text-citrus-orange">{{ $eyebrow }}</p>
    <h2 class="font-display text-5xl font-bold tracking-tight text-ink md:text-6xl">{{ $heading }}</h2>
    <p class="mt-3 text-lg text-ink/60">{{ $subheading }}</p>
  </div>

  <div class="mt-14 flex flex-col gap-4">
    @foreach ($rows as $row)
      @php($list = array_merge($clients, $clients))
      <div class="flex overflow-hidden">
        <div
          class="flex shrink-0 items-center gap-4 pr-4 {{ $row['direction'] === 'left' ? 'animate-marquee-left' : 'animate-marquee-right' }}"
          style="animation-duration: {{ $row['duration'] }}s"
        >
          @foreach ($list as $client)
            <div class="group flex h-20 shrink-0 items-center justify-center rounded-2xl border border-ink/10 bg-background px-8 transition-colors duration-300 hover:border-citrus-orange/40 sm:h-24">
              @if (! empty($client['logo']))
                <img
                  src="{{ $client['logo'] }}"
                  alt="{{ $client['name'] ?: __('Client logo', 'sage') }}"
                  class="max-h-10 w-auto max-w-[10rem] object-contain opacity-60 grayscale transition duration-300 group-hover:opacity-100 group-hover:grayscale-0 sm:max-h-12 sm:max-w-[12rem]"
                  loading="lazy"
                >
              @else
                <span class="font-display text-xl font-bold tracking-tight text-ink/25 transition-colors duration-300 group-hover:text-citrus-orange sm:text-2xl">
                  {{ $client['name'] }}
                </span>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>
</section>
