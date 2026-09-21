<?php

/**
 * Custom post types for the "Citrus Video Productions" v0 blueprint.
 *
 * Registers the two content collections the design depends on:
 * - `project` → "Our Work" grid / Works Archive / project case-study page
 * - `slice`   → "Citrus Slices" grid / Slices Hub / slice series page
 *
 * Both use WordPress's native `category` taxonomy (the same one `post` uses)
 * for the "category" badge shown in the v0 design — see \App\v0_primary_category()
 * in app/helpers.php for how it's read back on the frontend.
 *
 * Field data for both post types is registered in app/Fields/v0-auto-fields.php.
 */

namespace App;

add_action('init', function () {
    register_post_type('project', [
        'label' => __('Projects', 'sage'),
        'labels' => [
            'name' => __('Projects', 'sage'),
            'singular_name' => __('Project', 'sage'),
            'add_new_item' => __('Add New Project', 'sage'),
            'edit_item' => __('Edit Project', 'sage'),
            'all_items' => __('Our Work', 'sage'),
        ],
        'public' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-video-alt3',
        'menu_position' => 5,
        'supports' => ['title', 'thumbnail', 'page-attributes', 'revisions'],
        'taxonomies' => ['category'],
        'has_archive' => 'work',
        'rewrite' => ['slug' => 'work', 'with_front' => false],
        'show_in_nav_menus' => true,
    ]);

    register_post_type('slice', [
        'label' => __('Slices', 'sage'),
        'labels' => [
            'name' => __('Slices', 'sage'),
            'singular_name' => __('Slice', 'sage'),
            'add_new_item' => __('Add New Slice', 'sage'),
            'edit_item' => __('Edit Slice', 'sage'),
            'all_items' => __('Citrus Slices', 'sage'),
        ],
        'public' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-images-alt2',
        'menu_position' => 6,
        'supports' => ['title', 'thumbnail', 'page-attributes', 'revisions'],
        'taxonomies' => ['category'],
        'has_archive' => 'citrus-slices',
        'rewrite' => ['slug' => 'citrus-slices', 'with_front' => false],
        'show_in_nav_menus' => true,
    ]);
});

/**
 * The Works Archive (/work/) prints every project on one page — no
 * pagination. Scoped to the main query on that archive only (via
 * is_post_type_archive()), so a category-filtered view of it (still the
 * same archive, just with an added taxonomy filter) is unpaginated too,
 * while the Slices Hub is untouched.
 */
add_action('pre_get_posts', function (\WP_Query $query) {
    if (! is_admin() && $query->is_main_query() && is_post_type_archive('project')) {
        $query->set('posts_per_page', -1);
    }
});
