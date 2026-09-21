<?php

/**
 * One-time demo content seeder.
 *
 * Populates the `project` / `slice` CPTs and the two legal pages with the
 * same copy that shipped in the v0.app design, using the exact CMB2 meta
 * shape registered in app/Fields/v0-auto-fields.php. This means the theme
 * looks and works exactly like the design the moment it's activated, and
 * every value is immediately editable from wp-admin.
 *
 * Runs once on theme activation and is guarded by an option flag, so it
 * never overwrites content an editor has since changed.
 */

namespace App;

add_action('after_switch_theme', function () {
    if (get_option('v0_seeded')) {
        return;
    }

    seed_projects();
    seed_slices();
    seed_legal_pages();

    update_option('v0_seeded', true);
});

/**
 * One-time migration: `project`/`slice` category was originally a plain
 * CMB2 text field (postmeta key `category`) before it was switched to
 * WordPress's native `category` taxonomy. Any post seeded or edited under
 * the old field still has its category sitting in that now-unused meta
 * key with no term assigned — carry it over once, then clean up the meta.
 * Runs after app/post-types.php registers `project`/`slice` on `init`.
 */
add_action('init', function () {
    if (get_option('v0_category_meta_migrated')) {
        return;
    }

    migrate_category_meta_to_taxonomy();

    update_option('v0_category_meta_migrated', true);
}, 20);

function migrate_category_meta_to_taxonomy(): void
{
    if (! post_type_exists('project') && ! post_type_exists('slice')) {
        return;
    }

    $post_ids = get_posts([
        'post_type' => ['project', 'slice'],
        'post_status' => 'any',
        'posts_per_page' => -1,
        'meta_key' => 'category',
        'fields' => 'ids',
        'no_found_rows' => true,
    ]);

    foreach ($post_ids as $post_id) {
        $name = get_post_meta($post_id, 'category', true);

        if ($name) {
            wp_set_post_categories($post_id, [wp_create_category($name)], true);
        }

        delete_post_meta($post_id, 'category');
    }
}

/**
 * One-time migration: the intro video used to live in the "Preloader"
 * group (`preloader_video`) and played on the loading screen. It now plays
 * inside the hero box, so its field moved to the Hero group as
 * `hero_video`. Carry any already-uploaded video (and its attachment ID)
 * over so it isn't silently orphaned, then drop the old keys.
 */
add_action('init', function () {
    if (get_option('v0_preloader_video_migrated')) {
        return;
    }

    migrate_preloader_video_to_hero();

    update_option('v0_preloader_video_migrated', true);
}, 20);

/**
 * One-time: the Works Archive "full reel" URL used to live on the Homepage
 * page as `contact_archive_url`. It now belongs to Theme Settings
 * (`works_archive_reel_url`), so carry over a customised value and drop the
 * old key. Never overwrites a value already saved on Theme Settings.
 */
add_action('init', function () {
    if (get_option('v0_reel_url_migrated')) {
        return;
    }

    migrate_reel_url_to_theme_settings();

    update_option('v0_reel_url_migrated', true);
}, 20);

function migrate_reel_url_to_theme_settings(): void
{
    $page_ids = get_posts([
        'post_type' => 'page',
        'post_status' => 'any',
        'posts_per_page' => -1,
        'meta_key' => 'contact_archive_url',
        'fields' => 'ids',
        'no_found_rows' => true,
    ]);

    $settings = get_option('v0_theme_settings', []);
    $settings = is_array($settings) ? $settings : [];

    foreach ($page_ids as $page_id) {
        $url = get_post_meta($page_id, 'contact_archive_url', true);

        if ($url && empty($settings['works_archive_reel_url'])) {
            $settings['works_archive_reel_url'] = $url;
            update_option('v0_theme_settings', $settings);
        }

        delete_post_meta($page_id, 'contact_archive_url');
    }
}

function migrate_preloader_video_to_hero(): void
{
    $page_ids = get_posts([
        'post_type' => 'page',
        'post_status' => 'any',
        'posts_per_page' => -1,
        'meta_key' => 'preloader_video',
        'fields' => 'ids',
        'no_found_rows' => true,
    ]);

    foreach ($page_ids as $page_id) {
        $url = get_post_meta($page_id, 'preloader_video', true);

        // Never clobber a hero video that was already set on the new field.
        if ($url && ! get_post_meta($page_id, 'hero_video', true)) {
            update_post_meta($page_id, 'hero_video', $url);

            if ($attachment_id = get_post_meta($page_id, 'preloader_video_id', true)) {
                update_post_meta($page_id, 'hero_video_id', $attachment_id);
            }
        }

        delete_post_meta($page_id, 'preloader_video');
        delete_post_meta($page_id, 'preloader_video_id');
    }
}

function seed_projects(): void
{
    if (! post_type_exists('project') || wp_count_posts('project')->publish > 0) {
        update_option('v0_seeded', true);

        return;
    }

    $projects = [
        [
            'title' => 'Breast Cancer Myths and Facts',
            'client' => 'Public Health Advocacy',
            'category' => 'Advocacy Film',
            'vimeo_id' => '1181799051',
            'year' => '2025',
            'summary' => 'A clear, compassionate explainer that separates fear from fact — squeezing dense medical information into a warm, watchable story built for wide public reach.',
            'credits' => [
                ['role' => 'Direction', 'name' => 'Citrus Video Productions'],
                ['role' => 'Scriptwriting', 'name' => 'Citrus Writers Pool'],
                ['role' => 'Post-Production', 'name' => 'Citrus Post'],
            ],
            'distribution' => ['Social', 'Broadcast', 'Community Screenings'],
        ],
        [
            'title' => 'CBE: I Can Serve',
            'client' => 'CBE',
            'category' => 'Brand Story',
            'vimeo_id' => '1129067700',
            'year' => '2024',
            'summary' => "A people-first brand film celebrating a culture of service — real voices, honest moments, and a rhythm that keeps you leaning in.",
            'credits' => [
                ['role' => 'Direction', 'name' => 'Citrus Video Productions'],
                ['role' => 'Cinematography', 'name' => 'Citrus Crew'],
                ['role' => 'Sound Design', 'name' => 'Citrus Post'],
            ],
            'distribution' => ['Corporate', 'Social'],
        ],
        [
            'title' => 'FedLand: Built to Rise',
            'client' => 'Federal Land',
            'category' => 'Corporate Campaign',
            'vimeo_id' => '1084270990',
            'year' => '2024',
            'summary' => 'An aspirational corporate campaign built around ambition and altitude — polished visuals and an original score that carry the brand skyward.',
            'credits' => [
                ['role' => 'Direction', 'name' => 'Citrus Video Productions'],
                ['role' => 'Original Score', 'name' => 'Citrus Music'],
                ['role' => 'VFX & GFX', 'name' => 'Citrus Post'],
            ],
            'distribution' => ['Corporate', 'Events', 'Digital'],
        ],
        [
            'title' => 'BPI ASM 2024',
            'client' => 'BPI',
            'category' => 'Event Film',
            'vimeo_id' => '1084269488',
            'year' => '2024',
            'summary' => 'The annual stockholders meeting opener — a high-energy event film that sets the tone in the room and reads just as sharp on screen.',
            'credits' => [
                ['role' => 'Direction', 'name' => 'Citrus Video Productions'],
                ['role' => 'Motion Graphics', 'name' => 'Citrus Post'],
                ['role' => 'Editing', 'name' => 'Citrus Editors'],
            ],
            'distribution' => ['Events', 'Corporate'],
        ],
        [
            'title' => 'Water for Growth and Development',
            'client' => 'ADBI',
            'category' => 'Documentary',
            'vimeo_id' => '1084267662',
            'year' => '2024',
            'summary' => 'A documentary look at water as the engine of growth — field footage, expert voices, and clean information design working together.',
            'credits' => [
                ['role' => 'Direction', 'name' => 'Citrus Video Productions'],
                ['role' => 'Field Production', 'name' => 'Citrus Crew'],
                ['role' => 'Animation', 'name' => 'Citrus Post'],
            ],
            'distribution' => ['Institutional', 'Digital'],
        ],
        [
            'title' => 'FNG Riverpark',
            'client' => 'Filinvest',
            'category' => 'Real Estate',
            'vimeo_id' => '1003620765',
            'year' => '2023',
            'summary' => 'A lifestyle showcase for a riverside community — cinematic drone work and grounded human moments that make the place feel like home.',
            'credits' => [
                ['role' => 'Direction', 'name' => 'Citrus Video Productions'],
                ['role' => 'Aerial', 'name' => 'Citrus Crew'],
                ['role' => 'Color', 'name' => 'Citrus Post'],
            ],
            'distribution' => ['Social', 'Sales Gallery'],
        ],
        [
            'title' => 'Filinvest: Making Dreams Real',
            'client' => 'Filinvest',
            'category' => 'Brand Film',
            'vimeo_id' => '821929587',
            'year' => '2023',
            'summary' => 'A flagship brand film about turning aspiration into address — emotive storytelling stitched across families, homes, and milestones.',
            'credits' => [
                ['role' => 'Direction', 'name' => 'Citrus Video Productions'],
                ['role' => 'Scriptwriting', 'name' => 'Citrus Writers Pool'],
                ['role' => 'Scoring', 'name' => 'Citrus Music'],
            ],
            'distribution' => ['Broadcast', 'Social', 'Corporate'],
        ],
    ];

    foreach ($projects as $i => $p) {
        $post_id = wp_insert_post([
            'post_type' => 'project',
            'post_title' => $p['title'],
            'post_status' => 'publish',
            'menu_order' => $i,
        ]);

        if (is_wp_error($post_id) || ! $post_id) {
            continue;
        }

        update_post_meta($post_id, 'client', $p['client']);
        wp_set_post_categories($post_id, [wp_create_category($p['category'])]);
        update_post_meta($post_id, 'vimeo_id', $p['vimeo_id']);
        update_post_meta($post_id, 'year', $p['year']);
        update_post_meta($post_id, 'summary', $p['summary']);
        update_post_meta($post_id, 'featured', $i < 6 ? 'on' : '');
        update_post_meta($post_id, 'credits', $p['credits']);
        update_post_meta($post_id, 'distribution', array_map(fn ($d) => ['value' => $d], $p['distribution']));
    }
}

function seed_slices(): void
{
    if (! post_type_exists('slice') || wp_count_posts('slice')->publish > 0) {
        return;
    }

    $slices = [
        [
            'title' => 'Connection Series',
            'client' => 'AyalaLand',
            'category' => 'Social Series',
            'summary' => 'A recurring social series built to spark everyday connection — bite-sized stories designed to stop the scroll and start a conversation.',
            'cover_vimeo_id' => '1003620765',
            'clips' => [
                ['title' => 'Episode 01 — Neighbors', 'vimeo_id' => '1003620765'],
                ['title' => 'Episode 02 — First Hello', 'vimeo_id' => '1129067700'],
                ['title' => 'Episode 03 — Home Again', 'vimeo_id' => '1084270990'],
                ['title' => 'Behind the Series', 'vimeo_id' => '1084269488'],
            ],
        ],
        [
            'title' => 'Kids Ask',
            'client' => 'AyalaLand',
            'category' => 'Explainer Series',
            'summary' => 'Big questions, tiny philosophers. A playful explainer series where kids ask the questions grown-ups forgot to.',
            'cover_vimeo_id' => '1129067700',
            'clips' => [
                ['title' => 'Why is the sky?', 'vimeo_id' => '1129067700'],
                ['title' => 'What is a home?', 'vimeo_id' => '1003620765'],
                ['title' => 'Where does water go?', 'vimeo_id' => '1084267662'],
            ],
        ],
        [
            'title' => 'SMDC at 20',
            'client' => 'SMDC',
            'category' => 'Milestone Reels',
            'summary' => 'A milestone anniversary campaign chopped into punchy vertical reels — two decades of stories, one scroll at a time.',
            'cover_vimeo_id' => '1084269488',
            'clips' => [
                ['title' => 'Then & Now', 'vimeo_id' => '1084269488'],
                ['title' => 'Voices at 20', 'vimeo_id' => '1084270990'],
                ['title' => 'The Next Chapter', 'vimeo_id' => '821929587'],
                ['title' => 'Highlight Reel', 'vimeo_id' => '1181799051'],
            ],
        ],
        [
            'title' => 'Water, Explained',
            'client' => 'ADBI',
            'category' => 'Explainer Series',
            'summary' => 'Complex water policy, distilled into clean, snackable shorts — information design that goes down easy.',
            'cover_vimeo_id' => '1084267662',
            'clips' => [
                ['title' => 'Why Water Matters', 'vimeo_id' => '1084267662'],
                ['title' => 'The Growth Link', 'vimeo_id' => '1084270990'],
                ['title' => 'What You Can Do', 'vimeo_id' => '1003620765'],
            ],
        ],
        [
            'title' => 'Built to Rise Teasers',
            'client' => 'Federal Land',
            'category' => 'Campaign Teasers',
            'summary' => 'Short, high-energy teaser cutdowns from the Built to Rise campaign — engineered for feeds and pre-rolls.',
            'cover_vimeo_id' => '1084270990',
            'clips' => [
                ['title' => 'Teaser — Skyline', 'vimeo_id' => '1084270990'],
                ['title' => 'Teaser — Ambition', 'vimeo_id' => '1084269488'],
                ['title' => 'Teaser — Arrival', 'vimeo_id' => '821929587'],
            ],
        ],
        [
            'title' => 'Myth vs Fact',
            'client' => 'Public Health',
            'category' => 'Advocacy Series',
            'summary' => 'A rapid-fire advocacy series busting health myths one card at a time — clear, kind, and endlessly shareable.',
            'cover_vimeo_id' => '1181799051',
            'clips' => [
                ['title' => 'Myth 01', 'vimeo_id' => '1181799051'],
                ['title' => 'Myth 02', 'vimeo_id' => '1129067700'],
                ['title' => 'Myth 03', 'vimeo_id' => '1084267662'],
                ['title' => 'The Facts', 'vimeo_id' => '1003620765'],
            ],
        ],
    ];

    foreach ($slices as $i => $s) {
        $post_id = wp_insert_post([
            'post_type' => 'slice',
            'post_title' => $s['title'],
            'post_status' => 'publish',
            'menu_order' => $i,
        ]);

        if (is_wp_error($post_id) || ! $post_id) {
            continue;
        }

        update_post_meta($post_id, 'client', $s['client']);
        wp_set_post_categories($post_id, [wp_create_category($s['category'])]);
        update_post_meta($post_id, 'cover_vimeo_id', $s['cover_vimeo_id']);
        update_post_meta($post_id, 'summary', $s['summary']);
        update_post_meta($post_id, 'featured', 'on');
        update_post_meta($post_id, 'clips', $s['clips']);
    }
}

function seed_legal_pages(): void
{
    $contact_email = 'info@citrusproductions.net';
    $contact_address = 'Unit 1015, Medical Plaza Ortigas, San Miguel Avenue, Ortigas Center, Pasig City, Philippines';

    $pages = [
        'privacy-policy' => [
            'title' => 'Privacy Policy',
            'subtitle' => 'How Citrus Video Productions handles the information you share with us.',
            'sections' => [
                ['title' => 'Information We Collect', 'body' => 'When you reach out through our contact form or channels, we collect your name, email address, phone number, company name, and any project details you choose to share. We only gather what we need to respond to your inquiry and scope your video project.'],
                ['title' => 'How We Use Your Information', 'body' => 'Your information is used solely to communicate with you, prepare proposals, deliver production services, and keep you updated on your project. We do not sell, rent, or share your personal information with third parties.'],
                ['title' => 'Your Rights (Philippine Data Privacy Act)', 'body' => 'In compliance with the Philippine Data Privacy Act of 2012 (RA 10173), you have full control over your data. You may contact us at any time to ask to view, update, correct, or completely delete your personal information from our records.'],
                ['title' => 'Project Footage & Media', 'body' => 'Footage, stills, and assets produced for a client remain governed by the terms of each individual production agreement. We showcase completed work in our portfolio only where usage rights permit.'],
                ['title' => 'Cookies & Analytics', 'body' => 'Our website may use basic analytics to understand how visitors interact with our pages. This helps us improve the user experience. You can disable cookies in your browser settings at any time.'],
                ['title' => 'Data Retention', 'body' => 'We retain project-related information for as long as necessary to fulfill our services and meet legal or contractual obligations, after which it is securely archived or deleted.'],
                ['title' => 'Contact Us', 'body' => "Questions about this policy? Email us at {$contact_email} or visit our studio at {$contact_address}."],
            ],
        ],
        'terms-and-conditions' => [
            'title' => 'Terms and Conditions',
            'subtitle' => 'By accessing and using this website, you agree to comply with and be bound by the following terms and conditions.',
            'sections' => [
                ['title' => 'Intellectual Property Rights', 'body' => 'All video reels, raw footage, production stills, photography, graphics, text, and website design displayed on this site are the sole intellectual property of Citrus Video Productions or our licensed partners. You may view and share links to our portfolio for booking inquiries, but you may not download, copy, modify, distribute, or use our work for any commercial purpose without our explicit, written consent.'],
                ['title' => 'Website Usage and Contact Form', 'body' => 'Our contact form is provided strictly for legitimate business inquiries, project scoping, and booking requests. By using our contact form, you agree to provide accurate, current, and truthful information (Name, Email, Phone Number, and Company Name), and not to use the form to transmit spam, advertising, malicious software, or fraudulent messages.'],
                ['title' => 'Client Projects and Agreements', 'body' => 'The portfolio items showcased on this website represent completed professional works. The specific ownership, distribution rights, revision policies, and payment terms for actual film or video projects are governed by separate, individual production contracts signed directly between Citrus Video Productions and each client. The terms on this website do not override individual production agreements.'],
                ['title' => 'Limitation of Liability', 'body' => 'While we strive to keep this website updated and functional, Citrus Video Productions is not liable for any temporary site downtime, server errors, or accidental typos in our service descriptions or pricing models.'],
                ['title' => 'Governing Law', 'body' => 'These Terms and Conditions are governed by and construed in accordance with the laws of the Republic of the Philippines. Any legal disputes arising from the use of this website shall be settled exclusively in the competent courts of Pasig City, Philippines.'],
                ['title' => 'Changes to These Terms', 'body' => 'We reserve the right to modify these terms at any time to reflect updates to our studio policies or changes in digital laws. Your continued use of the website after updates are posted constitutes acceptance of those changes.'],
                ['title' => 'Contact Information', 'body' => "For any legal inquiries regarding our terms, please reach out to us via email at {$contact_email} or visit our studio at {$contact_address}."],
            ],
        ],
    ];

    foreach ($pages as $slug => $page) {
        if (get_page_by_path($slug)) {
            continue;
        }

        $post_id = wp_insert_post([
            'post_type' => 'page',
            'post_title' => $page['title'],
            'post_name' => $slug,
            'post_status' => 'publish',
        ]);

        if (is_wp_error($post_id) || ! $post_id) {
            continue;
        }

        update_post_meta($post_id, '_wp_page_template', 'template-legal.blade.php');
        update_post_meta($post_id, 'legal_subtitle', $page['subtitle']);
        update_post_meta($post_id, 'legal_sections', $page['sections']);
    }
}
