# Contact Form 7 Integration

The Contact section (`resources/views/sections/contact.blade.php`) replaces
the v0 design's static React `<form>` with a real Contact Form 7 form,
resolved dynamically:

```php
$cf7_id = \App\v0_home_meta('contact_form_id', '');

if ($cf7_id) {
    echo do_shortcode('[contact-form-7 id="'.esc_attr($cf7_id).'"]');
} else {
    // styled placeholder — resources/views/partials/contact-form-fallback.blade.php
}
```

`v0_home_meta()` (see `app/helpers.php`) reads from whichever Page is using
the **Homepage (Citrus)** template — see the main README/setup notes for
creating that page. It resolves correctly from anywhere on the site, since
the Contact section doubles as the sitewide footer.

## Set it up (one-time)

1. In wp-admin, go to **Contact → Add New** and create a form named e.g.
   "Site Enquiry Form".
2. Replace its form body with the template below (matches the v0 design's
   fields: Name, Company, Mobile, Email, Message).
3. Copy the form's **ID** (shown in the Contact Forms list, or the shortcode
   CF7 gives you after saving).
4. Open your **Homepage** page in wp-admin (the one using the "Homepage
   (Citrus)" template) and find the **Contact Form (Contact Form 7)** group
   inside the **Homepage Content** meta box, then paste the ID into
   **Contact Form 7 ID**. Save.

The homepage Contact section (and the identical footer-contact block on
every archive/single/legal page) will immediately render the live form.
Leave the field blank and a disabled, styled preview form renders instead —
useful while a site is still being set up.

## Recommended CF7 form template

CF7 renders its own markup, so Tailwind utility classes can't be added to
the shortcode output directly. Instead, `resources/css/app.css` ships a
`.wpcf7-form` rule set that targets CF7's plain output tag-for-tag
(`input[type=text/email/tel]`, `textarea`, `input[type=submit]`, the
`.wpcf7-response-output` message, and a `.cf7-grid` helper for the
2-column layout) — rounded/ringed inputs, the pill submit button, and
on-brand success/error colors, no extra classes required. Paste this into
the CF7 form editor:

```
<div class="cf7-grid">
  <div>
    [text* your-name autocomplete:name placeholder "Name"]
  </div>
  <div>
    [text your-company autocomplete:organization placeholder "Company"]
  </div>
  <div>
    [tel your-mobile autocomplete:tel placeholder "Mobile Number"]
  </div>
  <div>
    [email* your-email autocomplete:email placeholder "Email Address"]
  </div>
</div>

[textarea* your-message placeholder "Message"]

[submit "Send Message"]
```

And under the form's **Mail** tab, a sensible starting point:

- **To:** the studio inbox (defaults to the CMB2 "Email" field's value —
  copy it in manually, since CF7 doesn't read theme options)
- **Subject:** `New enquiry from [your-name]`
- **Message body:** include `[your-name]`, `[your-company]`, `[your-mobile]`,
  `[your-email]`, `[your-message]`

## Notes

- `contact.blade.php` already provides the two-column layout (contact
  details + form card) and the white card container — the form template
  above only needs to fill that card, it shouldn't add its own outer
  background/padding.
- The **Flamingo** plugin (already installed) archives every CF7 submission
  in wp-admin even without an SMTP/mail setup, which is handy in local dev.
- If you use the CF7 Tag Generator instead of pasting the template above,
  no special classes are needed — the `.wpcf7-form input[type=...]` /
  `textarea` rules in `app.css` style plain CF7 markup automatically. Only
  wrap fields in `<div class="cf7-grid">…</div>` if you want the 2-column
  layout; a single-column form works fine without it.
