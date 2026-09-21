import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin'
import { wordpressPlugin, wordpressThemeJson } from '@roots/vite-plugin';

// Set APP_URL if it doesn't exist for Laravel Vite plugin
if (! process.env.APP_URL) {
  process.env.APP_URL = 'http://example.test';
}

export default defineConfig({
  base: '/wp-content/themes/citrus-sage11/public/build/',
  plugins: [
    tailwindcss(),
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/css/editor.css',
        'resources/js/editor.js',
      ],
      refresh: true,
      assets: ['resources/images/**', 'resources/fonts/**'],
    }),

    wordpressPlugin(),

    // Generate the theme.json file in the public/build/assets directory
    // based on the Tailwind config and the theme.json file from base theme folder
    wordpressThemeJson({
      disableTailwindColors: false,
      disableTailwindFonts: false,
      disableTailwindFontSizes: false,
      disableTailwindBorderRadius: false,
    }),
  ],
  server: {
    // Force IPv4 loopback. Without an explicit host, Vite binds to the
    // hostname "localhost", which Node resolves to the IPv6 loopback
    // (::1) on many systems — the dev-server URL then gets written into
    // public/hot as `http://[::1]:5173/...`, a different origin than
    // http://citrusproductions.local as far as the browser is concerned,
    // so every asset request is blocked by CORS and silently fails.
    host: '127.0.0.1',
    cors: true,
    proxy: {
      // Set this to your local WordPress development domain
      'http://localhost': 'http://citrusproductions.local',
    },
  },
  resolve: {
    alias: {
      '@scripts': '/resources/js',
      '@styles': '/resources/css',
      '@fonts': '/resources/fonts',
      '@images': '/resources/images',
    },
  },
})
