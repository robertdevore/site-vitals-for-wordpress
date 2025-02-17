<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Site_Vitals_Table extends WP_List_Table {
    private $plugin_instance;
    private $category;

    /**
     * Class constructor.
     *
     * @since 1.0.0
     *
     * @param object $plugin_instance An instance of the main plugin class.
     * @param string $category        The category slug for the checks (e.g., 'performance', 'security').
     */
    public function __construct( $plugin_instance, $category ) {
        $this->plugin_instance = $plugin_instance;

        // Validate category.
        if ( ! array_key_exists( $category, $this->plugin_instance->get_categories() ) ) {
            $category = 'performance';
        }

        $this->category = $category;

        parent::__construct( [
            'singular' => 'site_vital',
            'plural'   => 'site_vitals',
            'ajax'     => false,
        ] );
    }

    /**
     * Defines the columns for the table.
     *
     * @since  1.0.0
     * @return array Associative array of columns with column keys as indexes and column titles as values.
     */
    public function get_columns() {
        return [
            'check' => 'Check'
        ];
    }

    /**
     * Prepares the table items for display.
     *
     * Fetches the columns, sets hidden and sortable columns (if any),
     * then sets the items property by retrieving category checks.
     *
     * @since  1.0.0
     * @return void
     */
    public function prepare_items() {
        $columns  = $this->get_columns();
        $hidden   = [];
        $sortable = [];

        $this->_column_headers = [$columns, $hidden, $sortable];
        $this->items = $this->get_category_checks();
    }

    /**
     * Retrieves the checks for the current category.
     *
     * Based on the category, it runs the appropriate checks defined in the plugin instance
     * and returns an array of associative arrays, each containing 'check', 'result', and 'recommendation'.
     *
     * @since  1.0.0
     * @return array[] An array of checks, where each element is an associative array with keys:
     *                 'check' (string) - The name of the check,
     *                 'result' (string) - The result of the check (e.g., "Good", "Needs Attention"),
     *                 'recommendation' (string) - The recommendation provided by the check.
     */
    private function get_category_checks() {
        $checks = [];

        switch ( $this->category ) {
            case 'performance':
                $checks[] = [
                    'check'          => __( 'Page Load Speed', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_page_load_speed_check()['result'],
                    'recommendation' => $this->plugin_instance->run_page_load_speed_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Image Optimization', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_image_optimization_check()['result'],
                    'recommendation' => $this->plugin_instance->run_image_optimization_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Code Optimization', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_code_optimization_check()['result'],
                    'recommendation' => $this->plugin_instance->run_code_optimization_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Third-Party Scripts', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_third_party_scripts_check()['result'],
                    'recommendation' => $this->plugin_instance->run_third_party_scripts_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Database Optimization', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_database_optimization_check()['result'],
                    'recommendation' => $this->plugin_instance->run_database_optimization_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Caching Status', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_caching_status_check()['result'],
                    'recommendation' => $this->plugin_instance->run_caching_status_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Server Response Time', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_server_response_time_check()['result'],
                    'recommendation' => $this->plugin_instance->run_server_response_time_check()['recommendation']
                ];
                break;

            case 'security':
                $checks[] = [
                    'check'          => __( 'SSL Certificate', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_ssl_certificate_check()['result'],
                    'recommendation' => $this->plugin_instance->run_ssl_certificate_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Plugin Update Status', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_plugin_update_check()['result'],
                    'recommendation' => $this->plugin_instance->run_plugin_update_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Theme Update Status', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_theme_update_check()['result'],
                    'recommendation' => $this->plugin_instance->run_theme_update_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'WordPress Core Update Status', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_core_update_check()['result'],
                    'recommendation' => $this->plugin_instance->run_core_update_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Login Security (2FA)', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_login_security_check()['result'],
                    'recommendation' => $this->plugin_instance->run_login_security_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Security Headers', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_security_headers_check()['result'],
                    'recommendation' => $this->plugin_instance->run_security_headers_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'File Permissions', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_file_permissions_check()['result'],
                    'recommendation' => $this->plugin_instance->run_file_permissions_check()['recommendation']
                ];
                break;

            case 'seo':
                $checks[] = [
                    'check'          => __( 'SEO Meta Tags', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_seo_meta_tags_check()['result'],
                    'recommendation' => $this->plugin_instance->run_seo_meta_tags_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'SEO Plugin Detection', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_seo_plugin_detection()['result'],
                    'recommendation' => $this->plugin_instance->run_seo_plugin_detection()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Image Alt Text', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_image_alt_text_check()['result'],
                    'recommendation' => $this->plugin_instance->run_image_alt_text_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Sitemap Presence', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_sitemap_check()['result'],
                    'recommendation' => $this->plugin_instance->run_sitemap_check()['recommendation']
                ];
                break;
                
            case 'ux':
                $checks[] = [
                    'check'          => __( 'Mobile Responsiveness', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_mobile_responsiveness_check()['result'],
                    'recommendation' => $this->plugin_instance->run_mobile_responsiveness_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Navigation Clarity', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_navigation_clarity_check()['result'],
                    'recommendation' => $this->plugin_instance->run_navigation_clarity_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( '404 Error Pages', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_404_error_check()['result'],
                    'recommendation' => $this->plugin_instance->run_404_error_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Page Load Time on Key Pages', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_page_load_time_check()['result'],
                    'recommendation' => $this->plugin_instance->run_page_load_time_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Font Readability', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_font_readability_check()['result'],
                    'recommendation' => $this->plugin_instance->run_font_readability_check()['recommendation']
                ];
                break;
                
            case 'content':
                $checks[] = [
                    'check'          => __( 'Content Freshness', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_content_freshness_check()['result'],
                    'recommendation' => $this->plugin_instance->run_content_freshness_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Broken Links', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_broken_links_check()['result'],
                    'recommendation' => $this->plugin_instance->run_broken_links_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Content Length', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_content_length_check()['result'],
                    'recommendation' => $this->plugin_instance->run_content_length_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Media Usage', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_media_usage_check()['result'],
                    'recommendation' => $this->plugin_instance->run_media_usage_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Duplicate Content', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_duplicate_content_check()['result'],
                    'recommendation' => $this->plugin_instance->run_duplicate_content_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Revision Count', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_revision_count_check()['result'],
                    'recommendation' => $this->plugin_instance->run_revision_count_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Taxonomy Usage', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_taxonomy_usage_check()['result'],
                    'recommendation' => $this->plugin_instance->run_taxonomy_usage_check()['recommendation']
                ];
                break;

            case 'technical':
                $checks[] = [
                    'check'          => __( 'Caching Status', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_caching_status_check()['result'],
                    'recommendation' => $this->plugin_instance->run_caching_status_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'PHP Version', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_php_version_check()['result'],
                    'recommendation' => $this->plugin_instance->run_php_version_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Database Optimization', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_database_optimization_check()['result'],
                    'recommendation' => $this->plugin_instance->run_database_optimization_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Max Upload Size', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_max_upload_size_check()['result'],
                    'recommendation' => $this->plugin_instance->run_max_upload_size_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Memory Limit', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_memory_limit_check()['result'],
                    'recommendation' => $this->plugin_instance->run_memory_limit_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Server Response Time', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_server_response_time_check()['result'],
                    'recommendation' => $this->plugin_instance->run_server_response_time_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Gzip Compression', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_gzip_compression_check()['result'],
                    'recommendation' => $this->plugin_instance->run_gzip_compression_check()['recommendation']
                ];
                break;

            case 'compliance':
                $checks[] = [
                    'check'          => __( 'Alt Text for Images', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_alt_text_check()['result'],
                    'recommendation' => $this->plugin_instance->run_alt_text_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Color Contrast', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_color_contrast_check()['result'],
                    'recommendation' => $this->plugin_instance->run_color_contrast_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Keyboard Navigation', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_keyboard_navigation_check()['result'],
                    'recommendation' => $this->plugin_instance->run_keyboard_navigation_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'ARIA Roles and Landmarks', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_aria_roles_check()['result'],
                    'recommendation' => $this->plugin_instance->run_aria_roles_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Form Labels', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_form_labels_check()['result'],
                    'recommendation' => $this->plugin_instance->run_form_labels_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Heading Structure', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_heading_structure_check()['result'],
                    'recommendation' => $this->plugin_instance->run_heading_structure_check()['recommendation']
                ];
                $checks[] = [
                    'check'          => __( 'Link Descriptions', 'site-vitals-wp' ),
                    'result'         => $this->plugin_instance->run_link_descriptions_check()['result'],
                    'recommendation' => $this->plugin_instance->run_link_descriptions_check()['recommendation']
                ];
                break;

            default:
                break;
        }

        return $checks;
    }

    /**
     * Handles default column rendering.
     *
     * Applies status classes based on the check result and returns a styled box with the check name,
     * result, and recommendation. Different statuses (e.g., "needs attention", "needs improvement")
     * get different CSS classes.
     *
     * @param array  $item        An associative array representing a single check, containing keys:
     *                            'check', 'result', 'recommendation'.
     * @param string $column_name The name of the current column.
     *
     * @since  1.0.0
     * @return string The HTML output for the column.
     */
    public function column_default( $item, $column_name ) {
        $status_lower = strtolower( $item['result'] );
        $status_class = 'site-vital-status-good';

        if ( strpos( $status_lower, 'no caching detected' ) !== false ) {
            $status_class = 'site-vital-status-needs-attention';
        } elseif ( strpos( $status_lower, 'needs attention' ) !== false ) {
            $status_class = 'site-vital-status-needs-attention';
        } elseif ( strpos( $status_lower, 'needs improvement' ) !== false || strpos( $status_lower, 'needs optimization' ) !== false ) {
            $status_class = 'site-vital-status-needs-improvement';
        }

        if ( $column_name === 'check' ) {
            return wp_kses_post( '<div class="site-vital-box ' . $status_class . '">
                <h3>' . esc_html( $item['check'] ) . '</h3>
                <div class="site-vital-result"><strong>' . esc_html__( 'Result', 'site-vitals-wp' ) . ':</strong> ' . esc_html( $item['result'] ) . '</div>
                <div class="site-vital-recommendation"><strong>' . esc_html__( 'Recommendation', 'site-vitals-wp' ) . ':</strong> ' . wp_kses_post( $item['recommendation'] ) . '</div>
            </div>' );
        }
        return esc_html( $item[$column_name] );
    }

    /**
     * Displays a message when no items are found.
     *
     * This message is shown when there are no checks available for the given category.
     *
     * @since  1.0.0
     * @return void
     */
    public function no_items() {
        esc_html_e( 'No site vitals data available for this category.', 'site-vitals-wp' );
    }

}
