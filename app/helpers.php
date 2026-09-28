<?php

/**
 * Small helpers shared across the v0 blueprint Blade templates.
 */

namespace App;

use Illuminate\Support\Facades\Vite;

/**
 * Resolve a build URL for a static brand asset shipped in resources/images.
 * Falls back to a CMB2-managed image ID/URL when one is passed instead.
 */
function v0_image(string $file): string
{
    return Vite::asset("resources/images/{$file}");
}

/**
 * Resolve an <img> src for a field that may hold a CMB2 `file` value
 * (a plain URL) or fall back to a bundled brand asset.
 */
function v0_image_or(?string $meta_url, string $fallback_file): string
{
    return $meta_url ?: v0_image($fallback_file);
}

/**
 * Run a CMB2 `wysiwyg` field's raw stored value through the same
 * typography + paragraph filters WordPress applies to normal post content
 * (`wptexturize` for smart quotes/dashes/ellipses, `wpautop` to guarantee
 * `<p>` tags exist even if the editor's Text/HTML tab was used to type
 * content without them). get_post_meta() returns the raw, unfiltered
 * value, so callers must pass it through this before echoing with `{!! !!}`.
 */
function v0_rich_text(string $html): string
{
    return wpautop(wptexturize($html));
}

/**
 * The page-template filename used for the homepage.
 */
const HOMEPAGE_TEMPLATE = 'template-homepage.blade.php';

/**
 * Resolve the ID of the Page using the "Homepage (Citrus)" template,
 * wherever the current request happens to be. All homepage copy lives in
 * CMB2 fields on that one Page (see app/Fields/v0-auto-fields.php), but
 * shared sections — namely the Contact section, which doubles as the
 * sitewide footer — need to read those fields even while rendering on a
 * project, slice, or legal page. Resolved once per request.
 */
function v0_homepage_id(): int
{
    static $id = null;

    if ($id !== null) {
        return $id;
    }

    if (is_page_template(HOMEPAGE_TEMPLATE)) {
        return $id = get_queried_object_id();
    }

    $pages = get_posts([
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_key' => '_wp_page_template',
        'meta_value' => HOMEPAGE_TEMPLATE,
        'fields' => 'ids',
        'no_found_rows' => true,
    ]);

    return $id = $pages[0] ?? 0;
}

/**
 * Read a CMB2 field from the Homepage page, with a fallback for fields
 * that haven't been filled in yet (or if no Homepage page exists at all).
 * Works whether the current request IS the homepage or not.
 */
function v0_home_meta(string $key, $default = '')
{
    $id = v0_homepage_id();
    $value = $id ? get_post_meta($id, $key, true) : '';

    return empty($value) ? $default : $value;
}

/**
 * Read a value from the Theme Settings options page (Appearance → Theme
 * Settings, registered in app/Fields/v0-auto-fields.php), falling back to
 * $default when the field is empty or has never been saved.
 */
function v0_setting(string $key, $default = '')
{
    $value = function_exists('cmb2_get_option') ? cmb2_get_option('v0_theme_settings', $key, '') : '';

    return empty($value) ? $default : $value;
}

/**
 * ID of the Homepage-template page currently being viewed, or 0 if this
 * request isn't rendering that template. Distinct from v0_homepage_id(),
 * which resolves the homepage from anywhere on the site.
 */
function v0_current_homepage_id(): int
{
    return is_page_template(HOMEPAGE_TEMPLATE) ? get_queried_object_id() : 0;
}

/**
 * Whether the preloader should render for the given Homepage page ID.
 * The CMB2 checkbox saves 'on' when checked and no meta row at all when
 * unchecked, so an empty value here means "never touched" — default it
 * to enabled, matching the field's own 'on' default.
 */
function v0_preloader_enabled(int $homepage_id): bool
{
    return $homepage_id && (get_post_meta($homepage_id, 'preloader_enable', true) ?: 'on') === 'on';
}

/**
 * Safely coerce a CMB2 group/repeater meta value to an array.
 *
 * get_post_meta( $id, $key, true ) returns '' (not []) when a field has
 * never been saved. PHP's (array) cast turns that '' into [''] — a
 * *non-empty* array holding one blank string — not []. Every `empty($x)`
 * check and `foreach` built on a raw `(array) get_post_meta(...)` cast is
 * silently wrong as a result: sections render with one blank entry instead
 * of hiding, and counts are off by one. Route every group/repeater field
 * through this instead of casting directly.
 */
function v0_meta_array($value): array
{
    return is_array($value) ? $value : [];
}

/**
 * The first assigned "Category" term for a `project` or `slice` post, or
 * null if none is set. Both post types use WordPress's native `category`
 * taxonomy (see app/post-types.php) instead of a plain CMB2 text field.
 */
function v0_primary_category_term(int $post_id): ?\WP_Term
{
    $terms = get_the_terms($post_id, 'category');

    if (empty($terms) || is_wp_error($terms)) {
        return null;
    }

    return $terms[0];
}

/**
 * Convenience wrapper around v0_primary_category_term() for call sites
 * that only need the category name, not a clickable link to it.
 */
function v0_primary_category(int $post_id): string
{
    return v0_primary_category_term($post_id)->name ?? '';
}

/**
 * URL for "every `project`/`slice` post in this category" — the post
 * type's own archive (Works Archive / Slices Hub), filtered by the shared
 * `category` taxonomy via WordPress's native `category_name` query var.
 * That query var combines with whatever post_type the archive URL already
 * resolves to, so this reuses archive-project.blade.php /
 * archive-slice.blade.php exactly as they are — no new template needed.
 */
function v0_category_archive_link(\WP_Term $term, string $post_type): string
{
    $archive_url = get_post_type_archive_link($post_type);

    return $archive_url ? add_query_arg('category_name', $term->slug, $archive_url) : '';
}

/**
 * A post's primary category as ['name' => ..., 'link' => ...] for
 * rendering a clickable badge, or null if it has no category. `$post_type`
 * decides which archive (Works Archive / Slices Hub) the link points at.
 * Used for the compact badge on archive/listing cards, which only ever
 * shows one category — see v0_category_views() for the full list, used on
 * the single project/slice pages.
 */
function v0_category_view(int $post_id, string $post_type): ?array
{
    $term = v0_primary_category_term($post_id);

    if (! $term) {
        return null;
    }

    return [
        'name' => $term->name,
        'link' => v0_category_archive_link($term, $post_type),
    ];
}

/**
 * Every "Category" term assigned to a `project` or `slice` post, each as
 * ['name' => ..., 'link' => ...]. Unlike v0_category_view() (single,
 * card-listing badge), this is for the single project/slice page, where a
 * post can carry more than one category and all of them should show.
 */
function v0_category_views(int $post_id, string $post_type): array
{
    $terms = get_the_terms($post_id, 'category');

    if (empty($terms) || is_wp_error($terms)) {
        return [];
    }

    return array_map(fn (\WP_Term $term) => [
        'name' => $term->name,
        'link' => v0_category_archive_link($term, $post_type),
    ], $terms);
}

/**
 * Extract a bare numeric Vimeo ID whether the field holds just the ID (as
 * instructed) or a full Vimeo URL was pasted in instead — an easy mistake,
 * since copying the URL out of the browser's address bar is the natural
 * way to find a video. Concatenating a full URL into vumbnail.com/player
 * embed URLs silently breaks them, so every Vimeo ID is normalized through
 * here before use.
 */
function v0_normalize_vimeo_id(string $value): string
{
    $value = trim($value);

    if ($value !== '' && preg_match('/(\d{5,})/', $value, $matches)) {
        return $matches[1];
    }

    return '';
}

/**
 * Resolve which video source to use for an item that supports both a
 * Vimeo ID and a self-hosted upload (project's main video, a slice's cover,
 * or an individual clip) — the uploaded file always wins when both are
 * set, since uploading one is the more deliberate, explicit choice.
 *
 * Returns ['type' => 'vimeo'|'upload'|'', 'id' => string, 'url' => string].
 */
function v0_video_source(string $vimeo_id, string $video_url): array
{
    if ($video_url) {
        return ['type' => 'upload', 'id' => '', 'url' => $video_url];
    }

    $vimeo_id = v0_normalize_vimeo_id($vimeo_id);

    if ($vimeo_id) {
        return ['type' => 'vimeo', 'id' => $vimeo_id, 'url' => ''];
    }

    return ['type' => '', 'id' => '', 'url' => ''];
}

/**
 * Thumbnail URL for a video resolved via v0_video_source(). Priority:
 *   1. `$override` — an explicit "Thumbnail" field, when set. Works for
 *      either video type, so it can also replace the automatic Vimeo
 *      thumbnail, not just provide one for a self-hosted video.
 *   2. Vimeo's public thumbnail service, for a Vimeo video.
 *   3. The given post's Featured Image — self-hosted video has no
 *      equivalent auto-thumbnail service, so this is how a self-hosted
 *      project/slice/clip gets a thumbnail without one being set explicitly.
 */
function v0_video_thumbnail(array $video, int $post_id, string $size = 'large', string $override = ''): string
{
    if ($override) {
        return $override;
    }

    if ($video['type'] === 'vimeo') {
        return "https://vumbnail.com/{$video['id']}.jpg";
    }

    return get_the_post_thumbnail_url($post_id, $size) ?: '';
}

/**
 * Assemble all display data for a `project` post. Centralizing this here —
 * instead of a multi-statement @php block in the Blade template — keeps
 * single-project.blade.php to one inline @php(...) per post, which
 * sidesteps a bug in this project's installed Blade compiler where an
 * inline @php(...) followed later in the same file by a @php...@endphp
 * block gets mis-compiled (everything between them is swallowed as if it
 * were the block's raw PHP body).
 *
 * `gallery` is left empty when no images are uploaded — the "Behind the
 * Scenes" section hides itself entirely in that case rather than showing
 * placeholder images. `video` is resolved via v0_video_source() so
 * templates don't need to know whether it's a Vimeo ID or an upload; `thumb`
 * (via v0_video_thumbnail()) is the poster for a self-hosted <video> stage.
 */
function v0_project_data(int $post_id): array
{
    $video = v0_video_source(
        get_post_meta($post_id, 'vimeo_id', true),
        get_post_meta($post_id, 'video_file', true)
    );

    return [
        'video' => $video,
        'thumb' => v0_video_thumbnail($video, $post_id, 'large', get_post_meta($post_id, 'thumbnail', true)),
        'client' => get_post_meta($post_id, 'client', true),
        'year' => get_post_meta($post_id, 'year', true),
        'categories' => v0_category_views($post_id, 'project'),
        'summary' => get_post_meta($post_id, 'summary', true),
        'distribution' => array_column(v0_meta_array(get_post_meta($post_id, 'distribution', true)), 'value'),
        'credits' => v0_meta_array(get_post_meta($post_id, 'credits', true)),
        'gallery' => array_values(v0_meta_array(get_post_meta($post_id, 'gallery', true))),
    ];
}

/**
 * Assemble all display data for a `slice` post. `credits` falls back to
 * the v0 design's default Citrus Slices credits when a series doesn't set
 * its own; `distribution` has no such fallback — the section hides itself
 * entirely when empty (see single-slice.blade.php).
 * See v0_project_data() for why this lives in a helper rather than an
 * inline @php block, and for why `gallery` is left empty rather than
 * falling back to placeholder images.
 *
 * Each entry in `clips` gets a resolved `video` (see v0_video_source()) and
 * `thumb` (see v0_video_thumbnail()) — a clip's own "Thumbnail" field wins
 * if set (overriding even a Vimeo clip's automatic thumbnail), otherwise
 * it's the Vimeo thumbnail for a Vimeo clip or the series' Featured Image
 * for a self-hosted one.
 *
 * `cover_video`/`cover_thumb` are resolved the same way as a project's
 * `video`/`thumb` — used when a slice has no clips at all, so the page can
 * show a single large player for the cover video instead of an empty
 * "Series" section.
 */
function v0_slice_data(int $post_id): array
{
    $distribution = array_column(v0_meta_array(get_post_meta($post_id, 'distribution', true)), 'value');
    $credits = v0_meta_array(get_post_meta($post_id, 'credits', true));

    $clips = array_map(function ($clip) use ($post_id) {
        $video = v0_video_source($clip['vimeo_id'] ?? '', $clip['video_file'] ?? '');

        return [
            'title' => $clip['title'] ?? '',
            'video' => $video,
            'thumb' => v0_video_thumbnail($video, $post_id, 'large', $clip['poster'] ?? ''),
        ];
    }, v0_meta_array(get_post_meta($post_id, 'clips', true)));

    $cover_video = v0_video_source(
        get_post_meta($post_id, 'cover_vimeo_id', true),
        get_post_meta($post_id, 'cover_video_file', true)
    );

    return [
        'client' => get_post_meta($post_id, 'client', true),
        'categories' => v0_category_views($post_id, 'slice'),
        'summary' => get_post_meta($post_id, 'summary', true),
        'clips' => $clips,
        'cover_video' => $cover_video,
        'cover_thumb' => v0_video_thumbnail($cover_video, $post_id, 'large', get_post_meta($post_id, 'cover_thumbnail', true)),
        'distribution' => $distribution,
        'credits' => ! empty($credits) ? $credits : [
            ['role' => 'Concept & Direction', 'name' => 'Citrus Video Productions'],
            ['role' => 'Editing', 'name' => 'Citrus Post'],
            ['role' => 'Motion Graphics', 'name' => 'Citrus GFX'],
        ],
        'gallery' => array_values(v0_meta_array(get_post_meta($post_id, 'gallery', true))),
    ];
}

/**
 * Assemble display data for a Page using the "Legal Page (Citrus)"
 * template. See v0_project_data() for why this lives in a helper rather
 * than an inline @php block.
 */
function v0_legal_data(int $post_id): array
{
    return [
        'subtitle' => get_post_meta($post_id, 'legal_subtitle', true),
        'sections' => v0_meta_array(get_post_meta($post_id, 'legal_sections', true)),
    ];
}
