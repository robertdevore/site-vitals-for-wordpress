<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Plugin activation
 * 
 * @since  1.0.0
 * @return void
 */
function site_vitals_plugin_activate() {
    // Instantiate the main class.
    $sv         = new Site_Vitals_For_WordPress();
    $categories = $sv->get_categories();

    foreach ( $categories as $cat_slug => $cat_label ) {
        // Prepare the table data for this category.
        $table = new Site_Vitals_Table( $sv, $cat_slug );
        $table->prepare_items();
        $checks = $table->items;

        // Cache the checks for 12 hours (43200 seconds).
        set_transient( 'site_vitals_' . $cat_slug, $checks, 43200 );
    }
}
register_activation_hook( __FILE__, 'site_vitals_plugin_activate' );

/**
 * Handles the AJAX request to retrieve cached or freshly computed site vitals data for a given category.
 *
 * This function:
 * - Verifies permissions and a security nonce.
 * - Retrieves category-specific checks from a transient if available or computes them on the fly.
 * - Counts how many checks are "Good," "Needs Attention," or "Needs Improvement."
 * - Returns the counts as a JSON response.
 *
 * Hooked to the 'wp_ajax_site_vitals_get_category_data' action.
 *
 * @since  1.0.0
 * @return void This function outputs JSON data and terminates execution.
 */
function site_vitals_get_category_data() {
    // Security check.
    check_ajax_referer( 'site_vitals_nonce', 'nonce' );

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( [ 'message' => 'No permission.' ] );
    }

    $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
    if ( empty( $category ) ) {
        wp_send_json_error( [ 'message' => 'No category provided.' ] );
    }

    // Attempt to get cached data.
    $transient_key = 'site_vitals_' . $category;
    $checks = get_transient( $transient_key );

    if ( false === $checks ) {
        // Not cached, run table checks.
        $table = new Site_Vitals_Table( new Site_Vitals_For_WordPress(), $category );
        $table->prepare_items();
        $checks = $table->items;
        // Cache for 12 hours.
        set_transient( $transient_key, $checks, 43200 );
    }

    $good_count    = 0;
    $warning_count = 0;
    $danger_count  = 0;

    foreach ( $checks as $check ) {
        $status = isset( $check['result'] ) ? strtolower( $check['result'] ) : '';

        if ( false !== strpos( $status, 'good' ) || false !== strpos( $status, 'caching active' ) ) {
            // Good statuses.
            $good_count++;
        } elseif (
            false !== strpos( $status, 'needs attention' ) ||
            false !== strpos( $status, 'needs optimization' ) ||
            false !== strpos( $status, 'no caching detected' ) ||
            false !== strpos( $status, 'no seo plugins detected' ) ||
            false !== strpos( $status, 'no sitemap found' ) ||
            false !== strpos( $status, 'fair' )
        ) {
            // Warning statuses.
            $warning_count++;
        } elseif ( false !== strpos( $status, 'needs improvement' ) ) {
            // Danger statuses.
            $danger_count++;
        } else {
            // If something unexpected, treat as warning.
            $warning_count++;
        }
    }

    wp_send_json_success( [
        'good'    => $good_count,
        'warning' => $warning_count,
        'danger'  => $danger_count,
    ] );
}
add_action( 'wp_ajax_site_vitals_get_category_data', 'site_vitals_get_category_data' );

/**
 * Schedule the site vitals update event on plugin activation.
 *
 * Checks if the 'update_site_vitals_event' is already scheduled. If not, it schedules
 * the event to run hourly starting from the current time.
 *
 * @since  1.0.0
 * @return void
 */
function site_vitals_schedule_update() {
    if ( ! wp_next_scheduled( 'update_site_vitals_event' ) ) {
        wp_schedule_event( time(), 'hourly', 'update_site_vitals_event' );
    }
}
register_activation_hook( __FILE__, 'site_vitals_schedule_update' );

/**
 * Unschedule the site vitals update event on plugin deactivation.
 *
 * Retrieves the timestamp for the next scheduled 'update_site_vitals_event'. If found,
 * it unschedules the event to prevent it from running after the plugin is deactivated.
 *
 * @since  1.0.0
 * @return void
 */
function site_vitals_unschedule_update() {
    $timestamp = wp_next_scheduled( 'update_site_vitals_event' );
    if ( $timestamp ) {
        wp_unschedule_event( $timestamp, 'update_site_vitals_event' );
    }
}
register_deactivation_hook( __FILE__, 'site_vitals_unschedule_update' );

// Hook the update function.
add_action( 'update_site_vitals_event', 'update_site_vitals' );
