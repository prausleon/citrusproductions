<?php

/**
 * v0 Blueprint → CMB2 Auto-Generated Fields
 * ==========================================
 *
 * Self-contained field registration for every dynamic text block, image,
 * gallery, repeater, and toggle identified in the v0.app "Citrus Video
 * Productions" design (`_blueprint_source/v0-nextjs-source`).
 *
 * Everything below is registered on the `cmb2_admin_init` hook, so this
 * file is safe to `require` unconditionally — it simply does nothing if
 * the CMB2 plugin is not active.
 *
 * Sections:
 *   1. Homepage Content meta box  → resources/views/template-homepage.blade.php
 *   2. `project` CPT meta box     → single-project.blade.php / archive-project.blade.php
 *   3. `slice` CPT meta box       → single-slice.blade.php / archive-slice.blade.php
 *   4. Legal page meta box        → template-legal.blade.php (Privacy Policy / Terms)
 *   5. Theme Settings options page → archive-project.blade.php / archive-slice.blade.php
 *      (Appearance → Theme Settings; read with \App\v0_setting())
 *
 * Read the values back with:
 *   - \App\v0_home_meta( '<field_id>', $fallback )   — homepage fields, from anywhere
 *   - get_post_meta( $post_id, '<field_id>', true )  — CPT / legal page fields
 */

namespace App\Fields;

add_action('cmb2_admin_init', function () {

    /*
    |--------------------------------------------------------------------------
    | 1. Homepage Content — page-template meta box
    |--------------------------------------------------------------------------
    | Every "global" text block from the v0 design (Hero, About, Work +
    | Slices intros, Clients, Competencies, Contact, Footer, Preloader)
    | lives here, scoped to whichever Page uses the "Homepage (Citrus)"
    | template (resources/views/template-homepage.blade.php) — create a
    | Page, set its template in Page Attributes, and this box appears.
    | Grouped with `title` fields as visual dividers so a single,
    | self-contained box covers the whole page.
    */
    $home = new_cmb2_box([
        'id' => 'v0_homepage_fields',
        'title' => __('Homepage Content', 'sage'),
        'object_types' => ['page'],
        'show_on' => [
            'key' => 'page-template',
            'value' => [\App\HOMEPAGE_TEMPLATE],
        ],
        'context' => 'normal',
        'priority' => 'high',
    ]);

    // — Hero ------------------------------------------------------------
    $home->add_field(['name' => __('Hero Section', 'sage'), 'type' => 'title', 'id' => 'title_hero']);
    $home->add_field([
        'name' => __('Eyebrow', 'sage'),
        'id' => 'hero_eyebrow',
        'type' => 'text',
        'default' => 'Citrus Video Productions',
    ]);
    $home->add_field([
        'name' => __('Headline — prefix', 'sage'),
        'id' => 'hero_heading_prefix',
        'type' => 'text',
        'default' => 'Slices of',
    ]);
    $home->add_field([
        'name' => __('Headline — emphasis', 'sage'),
        'desc' => __('Rendered in italic orange.', 'sage'),
        'id' => 'hero_heading_emphasis',
        'type' => 'text',
        'default' => 'life in motion',
    ]);
    $home->add_field([
        'name' => __('Headline — line 2', 'sage'),
        'desc' => __('Basic HTML is allowed here, e.g. an inline &lt;br class="br-desktop"&gt; forces a line break only on larger screens — it collapses to nothing on phones so the text just wraps naturally there instead.', 'sage'),
        'id' => 'hero_heading_line2',
        'type' => 'text',
        'sanitization_cb' => 'wp_kses_post',
        'default' => 'from fresh and juicy minds',
    ]);
    $home->add_field([
        'name' => __('Background Image', 'sage'),
        'id' => 'hero_image',
        'type' => 'file',
        'options' => ['url' => false],
    ]);
    $home->add_field([
        'name' => __('Intro Video (mp4)', 'sage'),
        'desc' => __("Plays inside the hero box every time the homepage loads (skipped for visitors who prefer reduced motion), and the headline fades in over it once it finishes. Works best with a plain white background — the box turns solid white while it plays. Leave blank to use the bundled Citrus intro.", 'sage'),
        'id' => 'hero_video',
        'type' => 'file',
        'options' => ['url' => false],
        'text' => ['add_upload_file_text' => __('Add Video', 'sage')],
        'query_args' => ['type' => 'video'],
    ]);
    $home->add_field([
        'name' => __('Scroll Prompt Label', 'sage'),
        'id' => 'hero_scroll_label',
        'type' => 'text',
        'default' => 'Scroll to take a bite',
    ]);

    // — About -------------------------------------------------------------
    $home->add_field(['name' => __('About Section', 'sage'), 'type' => 'title', 'id' => 'title_about']);
    $home->add_field(['name' => __('Eyebrow', 'sage'), 'id' => 'about_eyebrow', 'type' => 'text', 'default' => 'Deep Roots, Wide Branches']);
    $home->add_field(['name' => __('Heading', 'sage'), 'id' => 'about_heading', 'type' => 'text', 'default' => 'About Us']);
    $home->add_field([
        'name' => __('Lead Statement', 'sage'),
        'id' => 'about_intro',
        'type' => 'wysiwyg',
        'options' => ['textarea_rows' => 4, 'media_buttons' => false],
        'default' => "<p>When life gives you citruses&hellip; make videos! Citrus Video Productions has been telling visual stories for over two decades.</p>",
    ]);
    $home->add_field([
        'name' => __('Body Copy', 'sage'),
        'id' => 'about_body',
        'type' => 'wysiwyg',
        'options' => ['textarea_rows' => 6, 'media_buttons' => false],
        'default' => "<p>Built on a core of strong writing, fluid collaboration, and zingy ideas, we've produced videos that are as effective as they are expressive. Drawing from our pool of freelancers, we assemble a crew tailored for your video needs.</p><p>Citrus gathers seasoned veterans and zesty creatives to serve up familiar stories and fresh takes. (And that's not pulp fiction!) We take a bite out of all kinds of stories, from short slices of life to buzzy long-form campaigns. Now that's juice that's worth the squeeze!</p>",
    ]);

    // — Work section intro --------------------------------------------------
    $home->add_field(['name' => __('Work Section', 'sage'), 'type' => 'title', 'id' => 'title_work']);
    $home->add_field(['name' => __('Eyebrow', 'sage'), 'id' => 'work_eyebrow', 'type' => 'text', 'default' => 'The Fruits of Our Labor']);
    $home->add_field(['name' => __('Heading', 'sage'), 'id' => 'work_heading', 'type' => 'text', 'default' => 'Our Work']);
    $home->add_field([
        'name' => __('Subheading', 'sage'),
        'id' => 'work_subheading',
        'type' => 'textarea_small',
        'default' => 'From conceptualization to distribution, we take your ideas from seed, to script, to the screen.',
    ]);
    $home->add_field(['name' => __('"See all" Button Label', 'sage'), 'id' => 'work_cta_label', 'type' => 'text', 'default' => 'View all work']);

    // — Citrus Slices section intro -----------------------------------------
    $home->add_field(['name' => __('Citrus Slices Section', 'sage'), 'type' => 'title', 'id' => 'title_slices']);
    $home->add_field(['name' => __('Eyebrow', 'sage'), 'id' => 'slices_eyebrow', 'type' => 'text', 'default' => 'The Fruits of Our Labor']);
    $home->add_field(['name' => __('Heading', 'sage'), 'id' => 'slices_heading', 'type' => 'text', 'default' => 'Citrus Slices']);
    $home->add_field([
        'name' => __('Subheading', 'sage'),
        'id' => 'slices_subheading',
        'type' => 'textarea_small',
        'default' => 'Citrus Slices is our bite-sized video series service, specialized for social media posting.',
    ]);
    $home->add_field(['name' => __('"See all" Button Label', 'sage'), 'id' => 'slices_cta_label', 'type' => 'text', 'default' => 'Explore Citrus Slices']);

    // — Clients ---------------------------------------------------------
    $home->add_field(['name' => __('Clients Section', 'sage'), 'type' => 'title', 'id' => 'title_clients']);
    $home->add_field(['name' => __('Eyebrow', 'sage'), 'id' => 'clients_eyebrow', 'type' => 'text', 'default' => 'The Harvest']);
    $home->add_field(['name' => __('Heading', 'sage'), 'id' => 'clients_heading', 'type' => 'text', 'default' => 'Our Clients']);
    $home->add_field([
        'name' => __('Subheading', 'sage'),
        'id' => 'clients_subheading',
        'type' => 'text',
        'default' => 'The brands that trust us to help their messages bear fruit.',
    ]);
    $clients_group = $home->add_field([
        'id' => 'clients_list',
        'type' => 'group',
        'options' => [
            'group_title' => __('Client {#}', 'sage'),
            'add_button' => __('Add Client', 'sage'),
            'remove_button' => __('Remove Client', 'sage'),
            'sortable' => true,
        ],
    ]);
    $home->add_group_field($clients_group, [
        'name' => __('Logo', 'sage'),
        'desc' => __('Transparent PNG or SVG recommended. Shown at a fixed height in the scrolling logo wall.', 'sage'),
        'id' => 'logo',
        'type' => 'file',
        'options' => ['url' => false],
        'text' => ['add_upload_file_text' => __('Add Logo', 'sage')],
    ]);
    $home->add_group_field($clients_group, [
        'name' => __('Client Name', 'sage'),
        'desc' => __("Used as the logo's alt text, and shown as text if no logo is uploaded yet.", 'sage'),
        'id' => 'name',
        'type' => 'text',
    ]);

    // — Competencies ------------------------------------------------------
    $home->add_field(['name' => __('Competencies Section', 'sage'), 'type' => 'title', 'id' => 'title_competencies']);
    $home->add_field(['name' => __('Eyebrow', 'sage'), 'id' => 'competencies_eyebrow', 'type' => 'text', 'default' => 'The Secret Recipe']);
    $home->add_field(['name' => __('Heading', 'sage'), 'id' => 'competencies_heading', 'type' => 'text', 'default' => 'Our Competencies']);
    $competencies_group = $home->add_field([
        'id' => 'competencies_phases',
        'type' => 'group',
        'options' => [
            'group_title' => __('Phase {#}', 'sage'),
            'add_button' => __('Add Phase', 'sage'),
            'remove_button' => __('Remove Phase', 'sage'),
            'sortable' => true,
        ],
    ]);
    $home->add_group_field($competencies_group, [
        'name' => __('Phase Name', 'sage'),
        'id' => 'phase',
        'type' => 'text',
    ]);
    $home->add_group_field($competencies_group, [
        'name' => __('Tasks', 'sage'),
        'desc' => __('One task per line.', 'sage'),
        'id' => 'tasks',
        'type' => 'textarea_small',
    ]);

    // — Contact -----------------------------------------------------------
    $home->add_field(['name' => __('Contact Section', 'sage'), 'type' => 'title', 'id' => 'title_contact']);
    $home->add_field(['name' => __('Eyebrow', 'sage'), 'id' => 'contact_eyebrow', 'type' => 'text', 'default' => 'Ready to do some picking?']);
    $home->add_field(['name' => __('Heading', 'sage'), 'id' => 'contact_heading', 'type' => 'text', 'default' => 'Contact Us']);
    $home->add_field(['name' => __('Email', 'sage'), 'id' => 'contact_email', 'type' => 'text', 'default' => 'info@citrusproductions.net']);
    $home->add_field([
        'name' => __('Studio Address', 'sage'),
        'id' => 'contact_address',
        'type' => 'textarea_small',
        'default' => 'Unit 1015, Medical Plaza Ortigas, San Miguel Avenue, Ortigas Center, Pasig City, Philippines',
    ]);
    $socials_group = $home->add_field([
        'id' => 'contact_socials',
        'type' => 'group',
        'options' => [
            'group_title' => __('Social Link {#}', 'sage'),
            'add_button' => __('Add Social Link', 'sage'),
            'remove_button' => __('Remove', 'sage'),
            'sortable' => true,
        ],
    ]);
    $home->add_group_field($socials_group, [
        'name' => __('Platform', 'sage'),
        'id' => 'icon',
        'type' => 'select',
        'options' => [
            'vimeo' => 'Vimeo',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
        ],
    ]);
    $home->add_group_field($socials_group, ['name' => __('Label', 'sage'), 'id' => 'label', 'type' => 'text']);
    $home->add_group_field($socials_group, ['name' => __('URL', 'sage'), 'id' => 'url', 'type' => 'text_url']);

    // — Contact Form 7 integration -----------------------------------------
    $home->add_field(['name' => __('Contact Form (Contact Form 7)', 'sage'), 'type' => 'title', 'id' => 'title_contact_form']);
    $home->add_field([
        'name' => __('Contact Form 7 ID', 'sage'),
        'desc' => __('Paste a Contact Form 7 shortcode ID or title (see Contact → Contact Forms). Leave blank to show a styled placeholder form instead.', 'sage'),
        'id' => 'contact_form_id',
        'type' => 'text',
    ]);

    // — Footer --------------------------------------------------------------
    $home->add_field(['name' => __('Footer', 'sage'), 'type' => 'title', 'id' => 'title_footer']);
    $home->add_field(['name' => __('Footer Logo', 'sage'), 'id' => 'footer_logo', 'type' => 'file', 'options' => ['url' => false]]);
    $home->add_field(['name' => __('Footer Studio Name', 'sage'), 'id' => 'footer_tagline', 'type' => 'text', 'default' => 'Citrus Video Productions']);

    // — Preloader -------------------------------------------------------
    $home->add_field(['name' => __('Preloader', 'sage'), 'type' => 'title', 'id' => 'title_preloader']);
    $home->add_field([
        'name' => __('Enable Preloader', 'sage'),
        'desc' => __('Covers the homepage with a loading bar until the page has fully loaded. (The intro video that used to play here now plays in the hero — see "Intro Video" under Hero Section.)', 'sage'),
        'id' => 'preloader_enable',
        'type' => 'checkbox',
        'default' => 'on',
    ]);

    /*
    |--------------------------------------------------------------------------
    | 2. `project` CPT — "Our Work" case-study fields
    |--------------------------------------------------------------------------
    */
    $project = new_cmb2_box([
        'id' => 'v0_project_fields',
        'title' => __('Project Details', 'sage'),
        'object_types' => ['project'],
        'context' => 'normal',
        'priority' => 'high',
        'show_names' => true,
    ]);

    // Section dividers ('title' fields) below split this box into tabs on
    // the edit screen — see resources/js/admin-cmb2-tabs.js. A box needs 2+
    // of them before the script bothers tabbing it.
    $project->add_field(['name' => __('Details', 'sage'), 'type' => 'title', 'id' => 'title_project_details']);
    $project->add_field(['name' => __('Client', 'sage'), 'id' => 'client', 'type' => 'text']);
    $project->add_field(['name' => __('Year', 'sage'), 'id' => 'year', 'type' => 'text']);
    $project->add_field([
        'name' => __('Summary ("The Squeeze")', 'sage'),
        'id' => 'summary',
        'type' => 'textarea_small',
    ]);
    $project->add_field([
        'name' => __('Feature on Homepage', 'sage'),
        'desc' => __('Show this project in the "Our Work" grid on the homepage (max 6 shown).', 'sage'),
        'id' => 'featured',
        'type' => 'checkbox',
    ]);

    $project->add_field(['name' => __('Video', 'sage'), 'type' => 'title', 'id' => 'title_project_video']);
    $project->add_field([
        'name' => __('Vimeo Video ID', 'sage'),
        'desc' => __('Numeric ID from the Vimeo URL — powers the thumbnail and player. Pasting the full Vimeo URL instead also works; it gets cleaned up to just the ID automatically.', 'sage'),
        'id' => 'vimeo_id',
        'type' => 'text',
        'sanitization_cb' => '\App\v0_normalize_vimeo_id',
    ]);
    $project->add_field([
        'name' => __('Self-Hosted Video (optional)', 'sage'),
        'desc' => __("Upload a video file instead of using Vimeo — for videos that can't be hosted there. Takes priority over the Vimeo Video ID above when set.", 'sage'),
        'id' => 'video_file',
        'type' => 'file',
        'options' => ['url' => false],
        'text' => ['add_upload_file_text' => __('Add Video', 'sage')],
        'query_args' => ['type' => 'video'],
    ]);
    $project->add_field([
        'name' => __('Thumbnail (optional)', 'sage'),
        'desc' => __('Mostly for a self-hosted video (which has no automatic thumbnail), but also replaces the automatic Vimeo thumbnail if set. Left blank, it falls back to the Vimeo thumbnail for a Vimeo video, or the Featured Image for a self-hosted one.', 'sage'),
        'id' => 'thumbnail',
        'type' => 'file',
        'options' => ['url' => false],
    ]);

    $project->add_field(['name' => __('Distribution & Credits', 'sage'), 'type' => 'title', 'id' => 'title_project_distribution']);
    $distribution = $project->add_field([
        'name' => __('Distribution', 'sage'),
        'id' => 'distribution',
        'type' => 'group',
        'options' => [
            'group_title' => __('Channel {#}', 'sage'),
            'add_button' => __('Add Channel', 'sage'),
            'remove_button' => __('Remove', 'sage'),
            'sortable' => true,
        ],
    ]);
    $project->add_group_field($distribution, ['name' => __('Channel', 'sage'), 'id' => 'value', 'type' => 'text']);

    $credits = $project->add_field([
        'name' => __('Credits', 'sage'),
        'id' => 'credits',
        'type' => 'group',
        'options' => [
            'group_title' => __('Credit {#}', 'sage'),
            'add_button' => __('Add Credit', 'sage'),
            'remove_button' => __('Remove', 'sage'),
            'sortable' => true,
        ],
    ]);
    $project->add_group_field($credits, ['name' => __('Role', 'sage'), 'id' => 'role', 'type' => 'text']);
    $project->add_group_field($credits, ['name' => __('Name', 'sage'), 'id' => 'name', 'type' => 'text']);

    $project->add_field(['name' => __('Gallery', 'sage'), 'type' => 'title', 'id' => 'title_project_gallery']);
    $project->add_field([
        'name' => __('Behind the Scenes Gallery', 'sage'),
        'id' => 'gallery',
        'type' => 'file_list',
        'text' => ['add_upload_files_text' => __('Add Images', 'sage')],
    ]);

    /*
    |--------------------------------------------------------------------------
    | 3. `slice` CPT — "Citrus Slices" series fields
    |--------------------------------------------------------------------------
    */
    $slice = new_cmb2_box([
        'id' => 'v0_slice_fields',
        'title' => __('Slice Series Details', 'sage'),
        'object_types' => ['slice'],
        'context' => 'normal',
        'priority' => 'high',
        'show_names' => true,
    ]);

    // Section dividers ('title' fields) below split this box into tabs on
    // the edit screen — see resources/js/admin-cmb2-tabs.js. A box needs 2+
    // of them before the script bothers tabbing it.
    $slice->add_field(['name' => __('Details', 'sage'), 'type' => 'title', 'id' => 'title_slice_details']);
    $slice->add_field(['name' => __('Client', 'sage'), 'id' => 'client', 'type' => 'text']);
    $slice->add_field([
        'name' => __('Summary ("The Squeeze")', 'sage'),
        'id' => 'summary',
        'type' => 'textarea_small',
    ]);
    $slice->add_field([
        'name' => __('Feature on Homepage', 'sage'),
        'desc' => __('Show this slice series in the "Citrus Slices" grid on the homepage (max 6 shown).', 'sage'),
        'id' => 'featured',
        'type' => 'checkbox',
    ]);

    $slice->add_field(['name' => __('Cover Video', 'sage'), 'type' => 'title', 'id' => 'title_slice_cover_video']);
    $slice->add_field([
        'name' => __('Cover Vimeo Video ID', 'sage'),
        'desc' => __('Used for the grid thumbnail + quick-play cover. Pasting the full Vimeo URL instead also works; it gets cleaned up to just the ID automatically.', 'sage'),
        'id' => 'cover_vimeo_id',
        'type' => 'text',
        'sanitization_cb' => '\App\v0_normalize_vimeo_id',
    ]);
    $slice->add_field([
        'name' => __('Cover Self-Hosted Video (optional)', 'sage'),
        'desc' => __('Upload a video file instead of using Vimeo for the cover — takes priority over the Cover Vimeo Video ID above when set.', 'sage'),
        'id' => 'cover_video_file',
        'type' => 'file',
        'options' => ['url' => false],
        'text' => ['add_upload_file_text' => __('Add Video', 'sage')],
        'query_args' => ['type' => 'video'],
    ]);
    $slice->add_field([
        'name' => __('Cover Thumbnail (optional)', 'sage'),
        'desc' => __('Mostly for a self-hosted cover video (which has no automatic thumbnail), but also replaces the automatic Vimeo thumbnail if set. Left blank, it falls back to the Vimeo thumbnail for a Vimeo cover, or the Featured Image for a self-hosted one.', 'sage'),
        'id' => 'cover_thumbnail',
        'type' => 'file',
        'options' => ['url' => false],
    ]);

    $slice->add_field(['name' => __('Clips', 'sage'), 'type' => 'title', 'id' => 'title_slice_clips']);
    $clips = $slice->add_field([
        'name' => __('Clips', 'sage'),
        'desc' => __('Each Citrus Slices series is a gallery of short clips.', 'sage'),
        'id' => 'clips',
        'type' => 'group',
        'options' => [
            'group_title' => __('Clip {#}', 'sage'),
            'add_button' => __('Add Clip', 'sage'),
            'remove_button' => __('Remove Clip', 'sage'),
            'sortable' => true,
        ],
    ]);
    $slice->add_group_field($clips, ['name' => __('Clip Title', 'sage'), 'id' => 'title', 'type' => 'text']);
    $slice->add_group_field($clips, [
        'name' => __('Vimeo Video ID', 'sage'),
        'desc' => __('Pasting the full Vimeo URL instead also works; it gets cleaned up to just the ID automatically.', 'sage'),
        'id' => 'vimeo_id',
        'type' => 'text',
        'sanitization_cb' => '\App\v0_normalize_vimeo_id',
    ]);
    $slice->add_group_field($clips, [
        'name' => __('Self-Hosted Video (optional)', 'sage'),
        'desc' => __('Takes priority over the Vimeo ID above when set.', 'sage'),
        'id' => 'video_file',
        'type' => 'file',
        'options' => ['url' => false],
        'query_args' => ['type' => 'video'],
    ]);
    $slice->add_group_field($clips, [
        'name' => __('Thumbnail (optional)', 'sage'),
        'desc' => __('Mostly for a self-hosted clip (which has no automatic thumbnail), but also replaces the automatic Vimeo thumbnail if set. Left blank, it falls back to the Vimeo thumbnail for a Vimeo clip, or the series Featured Image for a self-hosted one.', 'sage'),
        'id' => 'poster',
        'type' => 'file',
        'options' => ['url' => false],
    ]);

    $slice->add_field(['name' => __('Distribution & Credits', 'sage'), 'type' => 'title', 'id' => 'title_slice_distribution']);
    $slice_distribution = $slice->add_field([
        'name' => __('Distribution', 'sage'),
        'desc' => __('The Distribution section on the slice page is hidden entirely when this is left empty.', 'sage'),
        'id' => 'distribution',
        'type' => 'group',
        'options' => [
            'group_title' => __('Channel {#}', 'sage'),
            'add_button' => __('Add Channel', 'sage'),
            'remove_button' => __('Remove', 'sage'),
            'sortable' => true,
        ],
    ]);
    $slice->add_group_field($slice_distribution, ['name' => __('Channel', 'sage'), 'id' => 'value', 'type' => 'text']);

    $slice_credits = $slice->add_field([
        'name' => __('Credits', 'sage'),
        'desc' => __('Leave empty to use the default Citrus Slices credits.', 'sage'),
        'id' => 'credits',
        'type' => 'group',
        'options' => [
            'group_title' => __('Credit {#}', 'sage'),
            'add_button' => __('Add Credit', 'sage'),
            'remove_button' => __('Remove', 'sage'),
            'sortable' => true,
        ],
    ]);
    $slice->add_group_field($slice_credits, ['name' => __('Role', 'sage'), 'id' => 'role', 'type' => 'text']);
    $slice->add_group_field($slice_credits, ['name' => __('Name', 'sage'), 'id' => 'name', 'type' => 'text']);

    $slice->add_field(['name' => __('Gallery', 'sage'), 'type' => 'title', 'id' => 'title_slice_gallery']);
    $slice->add_field([
        'name' => __('Behind the Scenes Gallery', 'sage'),
        'id' => 'gallery',
        'type' => 'file_list',
        'text' => ['add_upload_files_text' => __('Add Images', 'sage')],
    ]);

    /*
    |--------------------------------------------------------------------------
    | 4. Legal page template — Privacy Policy / Terms & Conditions
    |--------------------------------------------------------------------------
    | Shown only on Pages using the "Legal Page (Citrus)" template
    | (resources/views/template-legal.blade.php).
    */
    $legal = new_cmb2_box([
        'id' => 'v0_legal_fields',
        'title' => __('Legal Page Content', 'sage'),
        'object_types' => ['page'],
        'show_on' => [
            'key' => 'page-template',
            'value' => ['template-legal.blade.php'],
        ],
        'context' => 'normal',
        'priority' => 'high',
    ]);

    $legal->add_field([
        'name' => __('Subtitle', 'sage'),
        'id' => 'legal_subtitle',
        'type' => 'textarea_small',
    ]);

    $legal_sections = $legal->add_field([
        'name' => __('Sections', 'sage'),
        'id' => 'legal_sections',
        'type' => 'group',
        'options' => [
            'group_title' => __('Section {#}', 'sage'),
            'add_button' => __('Add Section', 'sage'),
            'remove_button' => __('Remove Section', 'sage'),
            'sortable' => true,
        ],
    ]);
    $legal->add_group_field($legal_sections, ['name' => __('Title', 'sage'), 'id' => 'title', 'type' => 'text']);
    $legal->add_group_field($legal_sections, ['name' => __('Body', 'sage'), 'id' => 'body', 'type' => 'textarea']);


    /*
    |--------------------------------------------------------------------------
    | 5. Theme Settings — options page (Appearance → Theme Settings)
    |--------------------------------------------------------------------------
    | Site-wide copy that doesn't belong to any one Page or post: the
    | headings/intros on the Works Archive (/work/) and Slices Hub
    | (/citrus-slices/) listing pages. Stored as one option (`v0_theme_settings`);
    | read back with \App\v0_setting('<field_id>', $fallback). Every default
    | below matches the copy that was hardcoded before, so nothing changes
    | on the frontend until someone edits a field.
    */
    $settings = new_cmb2_box([
        'id' => 'v0_theme_settings',
        'title' => __('Theme Settings', 'sage'),
        'object_types' => ['options-page'],
        'option_key' => 'v0_theme_settings',
        'parent_slug' => 'themes.php',
        'menu_title' => __('Theme Settings', 'sage'),
        'capability' => 'manage_options',
        'save_button' => __('Save Settings', 'sage'),
    ]);

    // — Works Archive (/work/) ----------------------------------------------
    $settings->add_field([
        'name' => __('Works Archive', 'sage'),
        'desc' => __('The listing page of every project (/work/), including its category-filtered views.', 'sage'),
        'type' => 'title',
        'id' => 'title_works_archive',
    ]);
    $settings->add_field([
        'name' => __('Eyebrow', 'sage'),
        'desc' => __('The small uppercase line above the heading.', 'sage'),
        'id' => 'works_archive_eyebrow',
        'type' => 'text',
        'default' => 'The Full Basket',
    ]);
    $settings->add_field([
        'name' => __('Heading', 'sage'),
        'desc' => __('Leave blank to use the post type label ("Projects").', 'sage'),
        'id' => 'works_archive_heading',
        'type' => 'text',
    ]);
    $settings->add_field([
        'name' => __('Intro', 'sage'),
        'id' => 'works_archive_intro',
        'type' => 'textarea_small',
        'default' => "Every slice of life we've put in motion. Hover a piece to play the film or dive into the full case study.",
    ]);
    $settings->add_field([
        'name' => __('Full Reel Button Label', 'sage'),
        'id' => 'works_archive_reel_label',
        'type' => 'text',
        'default' => 'Visit our full Vimeo reel',
    ]);
    $settings->add_field([
        'name' => __('Full Reel Button URL', 'sage'),
        'desc' => __('Where the button at the bottom of the Works Archive points.', 'sage'),
        'id' => 'works_archive_reel_url',
        'type' => 'text_url',
        'default' => 'https://vimeo.com/citrusreel',
    ]);

    // — Slices Hub (/citrus-slices/) ------------------------------------------
    $settings->add_field([
        'name' => __('Slices Hub', 'sage'),
        'desc' => __('The listing page of every Citrus Slices series (/citrus-slices/), including its category-filtered views.', 'sage'),
        'type' => 'title',
        'id' => 'title_slices_archive',
    ]);
    $settings->add_field([
        'name' => __('Eyebrow', 'sage'),
        'desc' => __('The small uppercase line above the heading.', 'sage'),
        'id' => 'slices_archive_eyebrow',
        'type' => 'text',
        'default' => 'Bite-Sized & Buzzy',
    ]);
    $settings->add_field([
        'name' => __('Heading', 'sage'),
        'desc' => __('Leave blank to use the post type label ("Slices").', 'sage'),
        'id' => 'slices_archive_heading',
        'type' => 'text',
    ]);
    $settings->add_field([
        'name' => __('Intro', 'sage'),
        'id' => 'slices_archive_intro',
        'type' => 'textarea_small',
        'default' => 'Our short-form, social-first series service. Each series is a set of vertical clips built to stop the scroll and keep your feed fresh, juicy, and always in season.',
    ]);

});
