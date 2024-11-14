<?php
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Site_Vitals_Table extends WP_List_Table {
    private $plugin_instance;
    private $category;

    public function __construct($plugin_instance, $category) {
        $this->plugin_instance = $plugin_instance;
        $this->category = $category;

        parent::__construct([
            'singular' => 'site_vital',
            'plural' => 'site_vitals',
            'ajax' => false
        ]);
    }

    public function get_columns() {
        return [
            'check' => 'Check'
        ];
    }
    

    public function prepare_items() {
        $columns  = $this->get_columns();
        $hidden   = [];
        $sortable = [];

        $this->_column_headers = [$columns, $hidden, $sortable];
        $this->items = $this->get_category_checks();
    }

    private function get_category_checks() {
        $checks = [];

        switch ($this->category) {
            case 'performance':
                $checks[] = [
                    'check' => 'Page Load Speed',
                    'result' => $this->plugin_instance->run_page_load_speed_check()['result'],
                    'recommendation' => $this->plugin_instance->run_page_load_speed_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Image Optimization',
                    'result' => $this->plugin_instance->run_image_optimization_check()['result'],
                    'recommendation' => $this->plugin_instance->run_image_optimization_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Code Optimization',
                    'result' => $this->plugin_instance->run_code_optimization_check()['result'],
                    'recommendation' => $this->plugin_instance->run_code_optimization_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Third-Party Scripts',
                    'result' => $this->plugin_instance->run_third_party_scripts_check()['result'],
                    'recommendation' => $this->plugin_instance->run_third_party_scripts_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Database Optimization',
                    'result' => $this->plugin_instance->run_database_optimization_check()['result'],
                    'recommendation' => $this->plugin_instance->run_database_optimization_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Caching Status',
                    'result' => $this->plugin_instance->run_caching_status_check()['result'],
                    'recommendation' => $this->plugin_instance->run_caching_status_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Server Response Time',
                    'result' => $this->plugin_instance->run_server_response_time_check()['result'],
                    'recommendation' => $this->plugin_instance->run_server_response_time_check()['recommendation']
                ];
                break;

            case 'security':
                $checks[] = [
                    'check' => 'SSL Certificate',
                    'result' => $this->plugin_instance->run_ssl_certificate_check()['result'],
                    'recommendation' => $this->plugin_instance->run_ssl_certificate_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Plugin Update Status',
                    'result' => $this->plugin_instance->run_plugin_update_check()['result'],
                    'recommendation' => $this->plugin_instance->run_plugin_update_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Theme Update Status',
                    'result' => $this->plugin_instance->run_theme_update_check()['result'],
                    'recommendation' => $this->plugin_instance->run_theme_update_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'WordPress Core Update Status',
                    'result' => $this->plugin_instance->run_core_update_check()['result'],
                    'recommendation' => $this->plugin_instance->run_core_update_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Login Security (2FA)',
                    'result' => $this->plugin_instance->run_login_security_check()['result'],
                    'recommendation' => $this->plugin_instance->run_login_security_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Security Headers',
                    'result' => $this->plugin_instance->run_security_headers_check()['result'],
                    'recommendation' => $this->plugin_instance->run_security_headers_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'File Permissions',
                    'result' => $this->plugin_instance->run_file_permissions_check()['result'],
                    'recommendation' => $this->plugin_instance->run_file_permissions_check()['recommendation']
                ];
                break;

            case 'seo':
                $checks[] = [
                    'check' => 'SEO Meta Tags',
                    'result' => $this->plugin_instance->run_seo_meta_tags_check()['result'],
                    'recommendation' => $this->plugin_instance->run_seo_meta_tags_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'SEO Plugin Detection',
                    'result' => $this->plugin_instance->run_seo_plugin_detection()['result'],
                    'recommendation' => $this->plugin_instance->run_seo_plugin_detection()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Image Alt Text',
                    'result' => $this->plugin_instance->run_image_alt_text_check()['result'],
                    'recommendation' => $this->plugin_instance->run_image_alt_text_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Sitemap Presence',
                    'result' => $this->plugin_instance->run_sitemap_check()['result'],
                    'recommendation' => $this->plugin_instance->run_sitemap_check()['recommendation']
                ];
                break;
                
            case 'ux':
                $checks[] = [
                    'check' => 'Mobile Responsiveness',
                    'result' => $this->plugin_instance->run_mobile_responsiveness_check()['result'],
                    'recommendation' => $this->plugin_instance->run_mobile_responsiveness_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Navigation Clarity',
                    'result' => $this->plugin_instance->run_navigation_clarity_check()['result'],
                    'recommendation' => $this->plugin_instance->run_navigation_clarity_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Accessibility Compliance',
                    'result' => $this->plugin_instance->run_accessibility_check()['result'],
                    'recommendation' => $this->plugin_instance->run_accessibility_check()['recommendation']
                ];
                $checks[] = [
                    'check' => '404 Error Pages',
                    'result' => $this->plugin_instance->run_404_error_check()['result'],
                    'recommendation' => $this->plugin_instance->run_404_error_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Page Load Time on Key Pages',
                    'result' => $this->plugin_instance->run_page_load_time_check()['result'],
                    'recommendation' => $this->plugin_instance->run_page_load_time_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Font Readability',
                    'result' => $this->plugin_instance->run_font_readability_check()['result'],
                    'recommendation' => $this->plugin_instance->run_font_readability_check()['recommendation']
                ];
                break;
                
            case 'content':
                $checks[] = [
                    'check' => 'Content Freshness',
                    'result' => $this->plugin_instance->run_content_freshness_check()['result'],
                    'recommendation' => $this->plugin_instance->run_content_freshness_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Broken Links',
                    'result' => $this->plugin_instance->run_broken_links_check()['result'],
                    'recommendation' => $this->plugin_instance->run_broken_links_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Content Length',
                    'result' => $this->plugin_instance->run_content_length_check()['result'],
                    'recommendation' => $this->plugin_instance->run_content_length_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Media Usage',
                    'result' => $this->plugin_instance->run_media_usage_check()['result'],
                    'recommendation' => $this->plugin_instance->run_media_usage_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Duplicate Content',
                    'result' => $this->plugin_instance->run_duplicate_content_check()['result'],
                    'recommendation' => $this->plugin_instance->run_duplicate_content_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Revision Count',
                    'result' => $this->plugin_instance->run_revision_count_check()['result'],
                    'recommendation' => $this->plugin_instance->run_revision_count_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Taxonomy Usage',
                    'result' => $this->plugin_instance->run_taxonomy_usage_check()['result'],
                    'recommendation' => $this->plugin_instance->run_taxonomy_usage_check()['recommendation']
                ];
                break;

            case 'technical':
                $checks[] = [
                    'check' => 'Caching Status',
                    'result' => $this->plugin_instance->run_caching_status_check()['result'],
                    'recommendation' => $this->plugin_instance->run_caching_status_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'PHP Version',
                    'result' => $this->plugin_instance->run_php_version_check()['result'],
                    'recommendation' => $this->plugin_instance->run_php_version_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Database Optimization',
                    'result' => $this->plugin_instance->run_database_optimization_check()['result'],
                    'recommendation' => $this->plugin_instance->run_database_optimization_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Max Upload Size',
                    'result' => $this->plugin_instance->run_max_upload_size_check()['result'],
                    'recommendation' => $this->plugin_instance->run_max_upload_size_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Memory Limit',
                    'result' => $this->plugin_instance->run_memory_limit_check()['result'],
                    'recommendation' => $this->plugin_instance->run_memory_limit_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Server Response Time',
                    'result' => $this->plugin_instance->run_server_response_time_check()['result'],
                    'recommendation' => $this->plugin_instance->run_server_response_time_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Gzip Compression',
                    'result' => $this->plugin_instance->run_gzip_compression_check()['result'],
                    'recommendation' => $this->plugin_instance->run_gzip_compression_check()['recommendation']
                ];
                break;

            case 'compliance':
                $checks[] = [
                    'check' => 'Alt Text for Images',
                    'result' => $this->plugin_instance->run_alt_text_check()['result'],
                    'recommendation' => $this->plugin_instance->run_alt_text_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Color Contrast',
                    'result' => $this->plugin_instance->run_color_contrast_check()['result'],
                    'recommendation' => $this->plugin_instance->run_color_contrast_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Keyboard Navigation',
                    'result' => $this->plugin_instance->run_keyboard_navigation_check()['result'],
                    'recommendation' => $this->plugin_instance->run_keyboard_navigation_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'ARIA Roles and Landmarks',
                    'result' => $this->plugin_instance->run_aria_roles_check()['result'],
                    'recommendation' => $this->plugin_instance->run_aria_roles_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Form Labels',
                    'result' => $this->plugin_instance->run_form_labels_check()['result'],
                    'recommendation' => $this->plugin_instance->run_form_labels_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Heading Structure',
                    'result' => $this->plugin_instance->run_heading_structure_check()['result'],
                    'recommendation' => $this->plugin_instance->run_heading_structure_check()['recommendation']
                ];
                $checks[] = [
                    'check' => 'Link Descriptions',
                    'result' => $this->plugin_instance->run_link_descriptions_check()['result'],
                    'recommendation' => $this->plugin_instance->run_link_descriptions_check()['recommendation']
                ];
                break;
                
            default:
                break;
        }

        return $checks;
    }

    public function column_default($item, $column_name) {
        // Determine the status class based on result
        $status_class = 'site-vital-status-good';
        if (strpos(strtolower($item['result']), 'needs attention') !== false) {
            $status_class = 'site-vital-status-needs-attention';
        } elseif (strpos(strtolower($item['result']), 'needs improvement') !== false || strpos(strtolower($item['result']), 'needs optimization') !== false) {
            $status_class = 'site-vital-status-needs-improvement';
        }
    
        // Return the boxed layout for each check
        if ($column_name === 'check') {
            return wp_kses_post('<div class="site-vital-box ' . $status_class . '">
                <h3>' . esc_html($item['check']) . '</h3>
                <div class="site-vital-result"><strong>Result:</strong> ' . esc_html($item['result']) . '</div>
                <div class="site-vital-recommendation"><strong>Recommendation:</strong> ' . wp_kses_post($item['recommendation']) . '</div>
            </div>');
        }
        return esc_html($item[$column_name]);
    }

    public function no_items() {
        _e('No site vitals data available for this category.', 'site-vitals-for-wordpress');
    }

}
