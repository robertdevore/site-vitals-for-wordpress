<?php

/**
  * The plugin bootstrap file
  *
  * @link              https://robertdevore.com
  * @since             1.0.0
  * @package           Site_Vitals_For_WordPress
  *
  * @wordpress-plugin
  *
  * Plugin Name: Site Vitals for WordPress®
  * Description: Monitor and improve the performance, security, and SEO health of your WordPress® site.
  * Plugin URI:  https://github.com/robertdevore/site-vitals-for-wordpress/
  * Version:     1.1.1
  * Author:      Robert DeVore
  * Author URI:  https://robertdevore.com/
  * License:     GPL-2.0+
  * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
  * Text Domain: site-vitals-wp
  * Domain Path: /languages
  * Update URI:  https://github.com/robertdevore/site-vitals-for-wordpress/
  */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

require 'includes/activation.php';
require 'includes/helper-functions.php';

require 'vendor/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/robertdevore/site-vitals-for-wordpress/',
	__FILE__,
	'site-vitals-for-wordpress'
);

// Set the branch that contains the stable release.
$myUpdateChecker->setBranch( 'main' );

// Define the plugin version.
define( 'SVWP_VERSION', '1.1.1' );

// Check if Composer's autoloader is already registered globally.
if ( ! class_exists( 'RobertDevore\WPComCheck\WPComPluginHandler' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use RobertDevore\WPComCheck\WPComPluginHandler;
new WPComPluginHandler( plugin_basename( __FILE__ ), 'https://robertdevore.com/why-this-plugin-doesnt-support-wordpress-com-hosting/' );

// Plugin basename.
$plugin_name = plugin_basename( __FILE__ );

/**
 * Add settings link on plugin page
 *
 * @param array $links an array of links related to the plugin.
 * 
 * @since  1.1.1
 * @return array updatead array of links related to the plugin.
 */
function svwp_settings_link( $links ) {
    $settings_link = '<a href="admin.php?page=site-vitals">' . esc_html__( 'Settings', 'site-vitals-wp' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( "plugin_action_links_$plugin_name", 'svwp_settings_link' );

class Site_Vitals_For_WordPress {

    private $categories;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->categories = [
            'performance' => esc_html__( 'Performance', 'site-vitals-wp' ),
            'security'    => esc_html__( 'Security', 'site-vitals-wp' ),
            'seo'         => esc_html__( 'SEO', 'site-vitals-wp' ),
            'ux'          => esc_html__( 'User Experience (UX)', 'site-vitals-wp' ),
            'content'     => esc_html__( 'Content Management', 'site-vitals-wp' ),
            'technical'   => esc_html__( 'Technical Config', 'site-vitals-wp' ),
            //'compliance'  => esc_html__( 'Accessibility', 'site-vitals-wp' ), // @TODO COMING SOON
        ];
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        require_once plugin_dir_path( __FILE__ ) . 'classes/Site_Vitals_Table.php';
    }

    /**
     * Retrieves the list of available categories.
     *
     * Each category corresponds to a set of checks that the plugin will run.
     * Categories are defined in the plugin's constructor and are used to organize
     * the checks into distinct groups (e.g., Performance, Security, SEO).
     *
     * @since  1.0.0
     * @return array Associative array of category slugs and their corresponding labels.
     */
    public function get_categories() {
        return $this->categories;
    }

    /**
     * Adds menu and submenu pages.
     * 
     * @since  1.0.0
     * @return void
     */
    public function add_admin_menu() {
        add_menu_page(
            esc_html__( 'Site Vitals', 'site-vitals-wp' ),
            esc_html__( 'Site Vitals', 'site-vitals-wp' ),
            'manage_options',
            'site-vitals',
            [ $this, 'settings_page' ],
            'dashicons-heart',
            222
        );

        foreach ( $this->categories as $slug => $label ) {
            add_submenu_page(
                'site-vitals',
                esc_html( $label ),
                esc_html( $label ),
                'manage_options',
                'site-vitals-' . $slug,
                function() use ( $slug ) {
                    $this->settings_page( $slug );
                }
            );
        }
    }

    /**
     * Displays the settings page.
     *
     * @param string $category Optional. The category to display. Default is 'performance'.
     * 
     * @since  1.0.0
     * @return void
     */
    public function settings_page( $category = null ) {
        if ( is_null( $category ) || '' === $category ) {
            echo '<div class="wrap">
            <h1><strong>' . esc_html__( 'Site Vitals', 'site-vitals-wp' ) . '</strong>
                <a id="site-vitals-support-btn" href="https://robertdevore.com/contact/" target="_blank" class="button button-alt" style="margin-left: 10px;">
                    <span class="dashicons dashicons-format-chat" style="vertical-align: middle;"></span> ' . esc_html__( 'Support', 'site-vitals-wp' ) . '
                </a>
                <a id="site-vitals-docs-btn" href="https://robertdevore.com/articles/site-vitals-for-wordpress/" target="_blank" class="button button-alt" style="margin-left: 5px;">
                    <span class="dashicons dashicons-media-document" style="vertical-align: middle;"></span> ' . esc_html__( 'Documentation', 'site-vitals-wp' ) . '
                </a>
            </h1><hr />';

            // Create a grid container for category summaries
            echo '<div class="site-vitals-grid" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:20px;">';

            foreach ( $this->categories as $slug => $label ) {
                $this->display_category_summary( $slug, $label );
            }

            echo '</div></div>';
        } else {
            $category_label = $this->categories[ $category ] ?? esc_html__( 'Site Vitals', 'site-vitals-wp' );
            echo '<div class="wrap">
                        <h1><strong>' . esc_html__( 'Site Vitals', 'site-vitals-wp' ) . '</strong>: <span class="title-cat">' . esc_html( $category_label ) . '</span>
                <a id="site-vitals-support-btn" href="https://robertdevore.com/contact/" target="_blank" class="button button-alt" style="margin-left: 10px;">
                    <span class="dashicons dashicons-format-chat" style="vertical-align: middle;"></span> ' . esc_html__( 'Support', 'site-vitals-wp' ) . '
                </a>
                <a id="site-vitals-docs-btn" href="https://robertdevore.com/articles/site-vitals-for-wordpress/" target="_blank" class="button button-alt" style="margin-left: 5px;">
                    <span class="dashicons dashicons-media-document" style="vertical-align: middle;"></span> ' . esc_html__( 'Documentation', 'site-vitals-wp' ) . '
                </a>
            </h1><hr />';

            if ( isset( $this->categories[ $category ] ) ) {
                $this->display_category_table( $category );
            } else {
                echo '<p>' . esc_html__( 'Invalid category.', 'site-vitals-wp' ) . '</p>';
            }

            echo '</div>';
        }
    }

    /**
     * Displays the category table.
     *
     * @param string $category The category slug.
     * 
     * @since  1.0.0
     * @return void
     */
    private function display_category_table( $category ) {
        $table = new Site_Vitals_Table( $this, $category );
        $table->prepare_items();
        $table->display();
    }

    /**
     * Enqueues admin scripts and styles.
     *
     * @param string $hook The current admin page.
     * 
     * @since  1.0.0
     * @return void
     */
    public function enqueue_scripts( $hook ) {
        if ( strpos( $hook, 'site-vitals' ) === false ) {
            return;
        }

        wp_enqueue_style( 'site-vitals-css', plugin_dir_url( __FILE__ ) . 'assets/css/admin.css', [], SVWP_VERSION );
        wp_enqueue_script( 'site-vitals-js', plugin_dir_url( __FILE__ ) . 'assets/js/admin.js', [ 'jquery' ], SVWP_VERSION, true );
        wp_localize_script( 'site-vitals-js', 'siteVitals', [
            'nonce' => wp_create_nonce('site_vitals_nonce')
        ] );
    }

    /**
     * Runs the page load speed check.
     *
     * @return array Associative array with result and recommendation.
     * 
     * @since  1.0.0
     * @return array
     */
    public function run_page_load_speed_check() {
        $url          = home_url();
        $num_requests = 5;
        $total_time   = 0;

        for ( $i = 0; $i < $num_requests; $i++ ) {
            $start_time  = microtime( true );
            $response    = wp_remote_get( esc_url( $url ) );
            $load_time   = microtime( true ) - $start_time;
            $total_time += $load_time;

            if ( is_wp_error( $response ) ) {
                return [
                    'result'         => esc_html__( 'Error', 'site-vitals-wp' ),
                    'recommendation' => esc_html__( 'Unable to retrieve homepage load time. Please check network and server status.', 'site-vitals-wp' )
                ];
            }
        }

        $average_load_time = $total_time / $num_requests;
        $threshold         = 1;
        $result            = ( $average_load_time > $threshold ) ? esc_html__( 'Needs Improvement', 'site-vitals-wp' ) : esc_html__( 'Good', 'site-vitals-wp' );
        $recommendation    = ( $average_load_time > $threshold )
            ? sprintf( __( 'Average homepage load time is %s seconds. Consider optimizing images, minifying assets, and enabling caching.', 'site-vitals-wp' ), round( $average_load_time, 2 ) )
            : esc_html__( 'Average homepage load time is optimal.', 'site-vitals-wp' );

        return [
            'result'         => $result . ' (' . round( $average_load_time, 2 ) . 's)',
            'recommendation' => $recommendation
        ];
    }

    /**
     * Runs the image optimization check.
     *
     * @since  1.0.0
     * @return array Associative array with result and recommendation.
     */
    public function run_image_optimization_check() {
        $uploads_dir  = wp_upload_dir()['basedir'];
        $large_images = [];
        $image_files  = array_merge(
            glob( $uploads_dir . '/*.jpg' ),
            glob( $uploads_dir . '/*.jpeg' ),
            glob( $uploads_dir . '/*.png' ),
            glob( $uploads_dir . '/*.gif' ),
            glob( $uploads_dir . '/*.webp' ),
            glob( $uploads_dir . '/*.heic' ),
        );

        foreach ( $image_files as $file ) {
            if ( filesize( $file ) > 500 * 1024 ) {
                $large_images[] = basename( $file );
            }
        }

        $result = empty( $large_images ) 
            ? esc_html__( 'Good', 'site-vitals-wp' ) 
            : esc_html__( 'Needs Optimization', 'site-vitals-wp' );

        $recommendation = empty( $large_images )
            ? esc_html__( 'All images are optimized.', 'site-vitals-wp' )
            : esc_html__( 'Consider compressing these images: ', 'site-vitals-wp' ) . implode( ', ', $large_images );

        return [
            'result'         => $result,
            'recommendation' => $recommendation
        ];
    }

    /**
     * Runs the code optimization check.
     *
     * @since  1.0.0
     * @return array Associative array with result and recommendation.
     */
    public function run_code_optimization_check() {
        $theme_dir       = get_template_directory();
        $css_files       = glob( $theme_dir . '/*.css' );
        $js_files        = glob( $theme_dir . '/*.js' );
        $excessive_files = count( $css_files ) > 10 || count( $js_files ) > 10;

        $result         = $excessive_files ? esc_html__( 'Fair', 'site-vitals-wp' ) : esc_html__( 'Good', 'site-vitals-wp' );
        $recommendation = $excessive_files
            ? esc_html__( 'Consider removing or consolidating unused CSS/JS files.', 'site-vitals-wp' )
            : esc_html__( 'Code optimization is at an acceptable level.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation
        ];
    }

    /**
     * Runs the third-party scripts check.
     *
     * @since  1.0.0
     * @return array Associative array with result and recommendation.
     */
    public function run_third_party_scripts_check() {
        global $wp_scripts;
        $slow_scripts = [];

        if ( ! empty( $wp_scripts->queue ) ) {
            foreach ( $wp_scripts->queue as $handle ) {
                $src = $wp_scripts->registered[ $handle ]->src;

                if ( strpos( $src, 'http' ) === 0 && ! strpos( $src, home_url() ) ) {
                    $start_time = microtime( true );
                    wp_remote_get( esc_url( $src ) );
                    $load_time = microtime( true ) - $start_time;

                    if ( $load_time > 0.5 ) {
                        $slow_scripts[] = basename( $src ) . ' (' . round( $load_time, 2 ) . 's)';
                    }
                }
            }
        }

        $result         = empty( $slow_scripts ) ? esc_html__( 'Good', 'site-vitals-wp' ) : esc_html__( 'Needs Attention', 'site-vitals-wp' );
        $recommendation = empty( $slow_scripts )
            ? esc_html__( 'No slow third-party scripts detected.', 'site-vitals-wp' )
            : esc_html__( 'Consider optimizing or deferring these scripts: ', 'site-vitals-wp' ) . implode( ', ', $slow_scripts );

        return [
            'result'         => $result,
            'recommendation' => $recommendation
        ];
    }

    /**
     * Runs the server response time check.
     *
     * @since  1.0.0
     * @return array Associative array with result and recommendation.
     */
    public function run_server_response_time_check() {
        $start_time    = microtime( true );

        wp_remote_get( esc_url( home_url() ) );

        $response_time = microtime( true ) - $start_time;
        $threshold     = 0.5;

        $result         = ( $response_time > $threshold ) ? esc_html__( 'Needs Improvement', 'site-vitals-wp' ) : esc_html__( 'Good', 'site-vitals-wp' );
        $recommendation = ( $response_time > $threshold )
            ? sprintf( __( 'Server response time is %s seconds. Consider server optimizations or upgrading your hosting plan.', 'site-vitals-wp' ), round( $response_time, 2 ) )
            : esc_html__( 'Server response time is optimal.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation
        ];
    }

    /**
     * Runs SSL certificate check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_ssl_certificate_check() {
        $ssl_enabled    = is_ssl();
        $result         = $ssl_enabled ? esc_html__( 'Good', 'site-vitals-wp' ) : esc_html__( 'Needs Attention', 'site-vitals-wp' );
        $recommendation = $ssl_enabled
            ? esc_html__( 'SSL is active on your site.', 'site-vitals-wp' )
            : esc_html__( 'Your site is not using SSL. Consider enabling SSL for security and SEO benefits.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs plugin update check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_plugin_update_check() {
        $outdated_plugins = [];
        $plugin_updates   = get_site_transient( 'update_plugins' );

        foreach ( get_plugins() as $plugin => $details ) {
            if ( isset( $plugin_updates->response[ $plugin ] ) ) {
                $outdated_plugins[] = sanitize_text_field( $details['Name'] );
            }
        }

        $plugin_count   = count( $outdated_plugins );
        $result         = ( 0 === $plugin_count ) ? esc_html__( 'Good', 'site-vitals-wp' ) : esc_html__( 'Needs Attention', 'site-vitals-wp' );
        $recommendation = ( 0 === $plugin_count )
            ? esc_html__( 'All plugins are up to date.', 'site-vitals-wp' )
            : wp_kses_post(
                sprintf(
                    /* translators: %1$d: number of outdated plugins, %2$s: list of outdated plugin names */
                    __( '<strong>%1$d outdated plugins</strong> detected: %2$s. Update them to the latest version for security.', 'site-vitals-wp' ),
                    absint( $plugin_count ),
                    esc_html( implode( ', ', $outdated_plugins ) )
                )
            );

        return [
            'result'        => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs theme update check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_theme_update_check() {
        $update_themes   = get_site_transient( 'update_themes' );
        $outdated_themes = [];

        if ( ! empty( $update_themes->response ) ) {
            foreach ( $update_themes->response as $slug => $theme_data ) {
                $theme             = wp_get_theme( $slug );
                $outdated_themes[] = sanitize_text_field( $theme->get( 'Name' ) );
            }
        }

        $theme_count    = count( $outdated_themes );
        $result         = ( 0 === $theme_count ) ? esc_html__( 'Good', 'site-vitals-wp' ) : esc_html__( 'Needs Attention', 'site-vitals-wp' );
        $recommendation = ( 0 === $theme_count )
            ? esc_html__( 'All themes are up to date.', 'site-vitals-wp' )
            : wp_kses_post(
                sprintf(
                    /* translators: %1$d: number of outdated themes, %2$s: list of outdated theme names */
                    __( '<strong>%1$d outdated themes</strong> detected: %2$s. Update them to the latest version for security.', 'site-vitals-wp' ),
                    absint( $theme_count ),
                    esc_html( implode( ', ', $outdated_themes ) )
                )
            );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs WordPress core update check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_core_update_check() {
        global $wp_version;
        $core_updates = get_site_transient( 'update_core' );
        $needs_update = false;

        if ( ! empty( $core_updates->updates ) ) {
            foreach ( $core_updates->updates as $update ) {
                if ( 'upgrade' === $update->response && version_compare( $wp_version, $update->current, '<' ) ) {
                    $needs_update = true;
                    break;
                }
            }
        }

        $result         = $needs_update ? esc_html__( 'Needs Attention', 'site-vitals-wp' ) : esc_html__( 'Good', 'site-vitals-wp' );
        $recommendation = $needs_update
            ? esc_html__( 'A WordPress core update is available. Please update to the latest version.', 'site-vitals-wp' )
            : esc_html__( 'WordPress core is up to date.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs login security check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_login_security_check() {
        $two_fa_enabled = get_option( 'wp_two_factor_enabled', false ); // Hypothetical plugin option for 2FA
        $result         = $two_fa_enabled ? esc_html__( 'Good', 'site-vitals-wp' ) : esc_html__( 'Needs Attention', 'site-vitals-wp' );
        $recommendation = $two_fa_enabled
            ? esc_html__( 'Two-factor authentication is enabled for logins.', 'site-vitals-wp' )
            : esc_html__( 'Consider enabling two-factor authentication for additional login security.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs security headers check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_security_headers_check() {
        $headers         = getallheaders();
        $missing_headers = [];

        // Check for common security headers.
        if ( ! isset( $headers['X-Content-Type-Options'] ) ) {
            $missing_headers[] = 'X-Content-Type-Options';
        }
        if ( ! isset( $headers['X-Frame-Options'] ) ) {
            $missing_headers[] = 'X-Frame-Options';
        }
        if ( ! isset( $headers['X-XSS-Protection'] ) ) {
            $missing_headers[] = 'X-XSS-Protection';
        }

        $result = empty( $missing_headers ) ? esc_html__( 'Good', 'site-vitals-wp' ) : esc_html__( 'Needs Attention', 'site-vitals-wp' );

        $recommendation = empty( $missing_headers )
            ? esc_html__( 'All recommended security headers are present.', 'site-vitals-wp' )
            : sprintf(
                /* translators: %s: list of missing security headers */
                __( 'Missing security headers: %s. Consider adding them to improve security.', 'site-vitals-wp' ),
                esc_html( implode( ', ', $missing_headers ) )
            );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs file permissions check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_file_permissions_check() {
        $file_paths = [
            ABSPATH . 'wp-config.php',   // File, should be 644 (or even 600/640 in some setups)
            ABSPATH . '.htaccess',       // File, should be 644
            WP_CONTENT_DIR . '/uploads', // Directory, should be 755
            ABSPATH . 'wp-admin',        // Directory, should be 755
            ABSPATH . 'wp-includes',     // Directory, should be 755
        ];
        $file_paths     = apply_filters( 'svwp_pemissions_check_file_paths', $file_paths );
        $insecure_files = [];

        foreach ( $file_paths as $file ) {
            if ( file_exists( $file ) ) {
                $perms = substr( sprintf( '%o', fileperms( $file ) ), -3 );
                if ( is_dir( $file ) ) {
                    // Directories are generally 755.
                    if ( $perms !== '755' ) {
                        $insecure_files[] = sanitize_text_field( basename( $file ) );
                    }
                } else {
                    // Files are generally 644 (or in some cases, more restrictive like 600).
                    if ( $perms !== '644' ) {
                        $insecure_files[] = sanitize_text_field( basename( $file ) );
                    }
                }
            }
        }

        $result = empty( $insecure_files ) 
            ? esc_html__( 'Good', 'site-vitals-wp' ) 
            : esc_html__( 'Needs Attention', 'site-vitals-wp' );

        $recommendation = empty( $insecure_files )
            ? esc_html__( 'File permissions are secure.', 'site-vitals-wp' )
            : sprintf(
                /* translators: %s: list of insecure files */
                __( 'Insecure file permissions detected for: %s. Set permissions to the recommended values (644 for files, 755 for directories) for enhanced security.', 'site-vitals-wp' ),
                esc_html( implode( ', ', $insecure_files ) )
            );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs SEO meta tags check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_seo_meta_tags_check() {
        $url      = home_url();
        $response = wp_remote_get( $url );

        if ( is_wp_error( $response ) ) {
            return [
                'result'         => esc_html__( 'Error', 'site-vitals-wp' ),
                'recommendation' => esc_html__( 'Unable to fetch homepage for SEO check. Please verify the server status.', 'site-vitals-wp' ),
            ];
        }

        $body                 = wp_remote_retrieve_body( $response );
        $has_title            = preg_match( '/<title>(.*?)<\/title>/', $body );
        $has_meta_description = preg_match( '/<meta name="description" content="(.*?)"/', $body );

        $result = ( $has_title && $has_meta_description ) ? esc_html__( 'Good', 'site-vitals-wp' ) : esc_html__( 'Needs Attention', 'site-vitals-wp' );

        $recommendation = ( 'Good' === $result )
            ? esc_html__( 'Title and description meta tags are set correctly.', 'site-vitals-wp' )
            : esc_html__( 'Ensure both title and description meta tags are set for optimal SEO.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs SEO plugin detection.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_seo_plugin_detection() {
        $seo_plugins = [
            'wordpress-seo/wp-seo.php',
            'all-in-one-seo-pack/all_in_one_seo_pack.php',
            'rank-math/rank-math.php',
            'seo-by-rank-math/seo-by-rank-math.php',
            'the-seo-framework/the-seo-framework.php',
            'squirrly-seo/squirrly.php',
            'wp-seopress/seopress.php',
            'premium-seo-pack/premium-seo-pack.php',
            'smartcrawl-seo/wpmu_dev_smartcrawl.php',
            'platinum-seo-pack/platinum_seo_pack.php',
            'schema-and-structured-data-for-wp/structured-data-for-wp.php',
            'seo-ultimate/seo-ultimate.php',
            'wp-meta-seo/wp-meta-seo.php',
            'wpsso/wpsso.php',
            'all-in-one-schemaorg-rich-snippets/index.php',
            'google-analytics-for-wordpress/googleanalytics.php',
            'w3-total-cache/w3-total-cache.php',
            'simple-sitemap/simple-sitemap.php',
            'xml-sitemap-generator/xml-sitemap-generator.php',
            'google-sitemap-generator/sitemap.php',
            'broken-link-checker/broken-link-checker.php',
            'seo-integration-for-wp/seo-integration.php',
            'seo-image-optimizer/seo-image-optimizer.php',
            'seo-friendly-images/seo-friendly-images.php',
            'local-seo/local-seo.php',
            'seo-for-wordpress/seo-for-wordpress.php'
        ];

        $active_seo_plugins = array_filter( $seo_plugins, 'is_plugin_active' );

        $result         = empty( $active_seo_plugins ) ? esc_html__( 'No SEO Plugins Detected', 'site-vitals-wp' ) : esc_html__( 'SEO Plugin Active', 'site-vitals-wp' );
        $recommendation = empty( $active_seo_plugins )
            ? esc_html__( 'Consider installing an SEO plugin to enhance your site’s search engine visibility.', 'site-vitals-wp' )
            : esc_html__( 'SEO plugin detected. Your site has additional SEO features enabled.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs image alt text check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_image_alt_text_check() {
        global $wpdb;

        $images_without_alt = $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT COUNT(*)
                FROM {$wpdb->prefix}posts AS p
                LEFT JOIN {$wpdb->prefix}postmeta AS pm
                    ON p.ID = pm.post_id AND pm.meta_key = %s
                WHERE p.post_type = %s
                    AND p.post_mime_type LIKE %s
                    AND (pm.meta_value IS NULL OR pm.meta_value = '')
                ",
                '_wp_attachment_image_alt',
                'attachment',
                'image/%'
            )
        );

        $result = ( 0 === intval( $images_without_alt ) ) ? esc_html__( 'Good', 'site-vitals-wp' ) : esc_html__( 'Needs Attention', 'site-vitals-wp' );

        $recommendation = ( 0 === intval( $images_without_alt ) )
            ? esc_html__( 'All images have alt text, which is good for SEO.', 'site-vitals-wp' )
            : wp_kses_post(
                sprintf(
                    /* translators: %s: number of images missing alt text */
                    __( '<strong>%s images</strong> are missing alt text. Add descriptive alt text to improve SEO.', 'site-vitals-wp' ),
                    intval( $images_without_alt )
                )
            );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs sitemap check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_sitemap_check() {
        $common_sitemap_urls = [
            home_url( '/sitemap.xml' ),
            home_url( '/sitemap_index.xml' ),
            home_url( '/sitemap/sitemap-index.xml' ),
            home_url( '/wp-sitemap.xml' ),
        ];

        // Filter the sitemap URL's.
        $common_sitemap_urls = apply_filters( 'sv_common_sitemap_urls', $common_sitemap_urls );

        $sitemap_found = false;

        foreach ( $common_sitemap_urls as $sitemap_url ) {
            $response = wp_remote_head( $sitemap_url );
            if ( ! is_wp_error( $response ) && 200 === intval( wp_remote_retrieve_response_code( $response ) ) ) {
                $sitemap_found = true;
                break;
            }
        }

        $result = $sitemap_found ? esc_html__( 'Good', 'site-vitals-wp' ) : esc_html__( 'No Sitemap Found', 'site-vitals-wp' );

        $recommendation = $sitemap_found
            ? esc_html__( 'Sitemap detected, which helps search engines index your site.', 'site-vitals-wp' )
            : esc_html__( 'No sitemap found. Consider adding a sitemap to improve search engine indexing.', 'site-vitals-wp' );

        return [
            'result'        => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs mobile responsiveness check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_mobile_responsiveness_check() {
        $theme_supports_responsive = current_theme_supports( 'responsive-embeds' );
        $result                    = $theme_supports_responsive ? esc_html__( 'Good', 'site-vitals-wp' ) : esc_html__( 'Needs Improvement', 'site-vitals-wp' );
        $recommendation            = $theme_supports_responsive
            ? esc_html__( 'Your theme supports mobile responsiveness, enhancing user experience on mobile devices.', 'site-vitals-wp' )
            : esc_html__( 'Consider using a mobile-responsive theme to improve user experience on mobile devices.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }
 
    /**
     * Runs navigation clarity check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_navigation_clarity_check() {
        global $wpdb;

        // Try to retrieve the cached orphaned pages count.
        $orphaned_pages = get_transient( 'site_vitals_orphaned_pages_count' );

        if ( false === $orphaned_pages ) {
            // The transient is missing or expired, so run the query.
            $orphaned_pages = $wpdb->get_var( $wpdb->prepare( "
                SELECT COUNT(*) 
                FROM {$wpdb->posts} AS p
                WHERE p.post_type = %s
                AND p.post_status = %s
                AND NOT EXISTS (
                    SELECT 1
                    FROM {$wpdb->posts} AS p2
                    WHERE p2.post_content LIKE CONCAT('%%', p.post_name, '%%')
                        AND p2.ID <> p.ID
                )
            ", 'page', 'publish' ) );

            // Cache the result for one hour.
            set_transient( 'site_vitals_orphaned_pages_count', $orphaned_pages, HOUR_IN_SECONDS );
        }

        $result = ( $orphaned_pages > 0 )
            ? esc_html__( 'Needs Attention', 'site-vitals-wp' )
            : esc_html__( 'Good', 'site-vitals-wp' );

        $recommendation = ( $orphaned_pages > 0 )
            ? wp_kses_post(
                sprintf(
                    /* translators: %s: number of orphaned pages */
                    __( 'Detected <strong>%s pages</strong> might be orphaned with no incoming links. Consider improving navigation or linking these pages.', 'site-vitals-wp' ),
                    absint( $orphaned_pages )
                )
            )
            : esc_html__( 'Navigation clarity is good, with no orphaned pages detected.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs accessibility check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_accessibility_check() {
        global $wpdb;

        $missing_alts = $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT COUNT(*)
                FROM {$wpdb->posts} AS p
                LEFT JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id AND pm.meta_key = %s
                WHERE p.post_type = %s
                    AND p.post_mime_type LIKE %s
                    AND (pm.meta_value IS NULL OR pm.meta_value = '')
                ",
                '_wp_attachment_image_alt',
                'attachment',
                'image/%'
            )
        );

        $result         = ( 0 === intval( $missing_alts ) ) ? esc_html__( 'Good', 'site-vitals-wp' ) : esc_html__( 'Needs Improvement', 'site-vitals-wp' );
        $recommendation = ( 0 === intval( $missing_alts ) )
            ? esc_html__( 'All images have alt tags, which helps meet accessibility standards.', 'site-vitals-wp' )
            : wp_kses_post(
                sprintf(
                    /* translators: %s: number of images missing alt tags */
                    __( '<strong>%s images</strong> are missing alt tags. Add descriptive alt tags to improve accessibility.', 'site-vitals-wp' ),
                    absint( $missing_alts )
                )
            );

        return [
            'result'        => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs 404 error check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_404_error_check() {
        $pages_to_check = [
            home_url( '/nonexistent-page' ),
            home_url( '/broken-link' ),
        ];

        // Filter the pages.
        $pages_to_check = apply_filters( 'sv_404_pages_to_check', $pages_to_check );

        $error_count = 0;
        foreach ( $pages_to_check as $url ) {
            $response = wp_remote_head( $url );
            if ( ! is_wp_error( $response ) && 404 === intval( wp_remote_retrieve_response_code( $response ) ) ) {
                $error_count++;
            }
        }

        $result         = ( $error_count > 0 ) ? esc_html__( 'Needs Attention', 'site-vitals-wp' ) : esc_html__( 'Good', 'site-vitals-wp' );
        $recommendation = ( $error_count > 0 )
            ? wp_kses_post(
                sprintf(
                    /* translators: %s: number of 404 error pages */
                    __( 'Detected <strong>%s pages</strong> returning 404 errors. Ensure no broken links or missing pages.', 'site-vitals-wp' ),
                    absint( $error_count )
                )
            )
            : esc_html__( 'No 404 error pages detected, navigation is clear.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs page load time check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
    */
    public function run_page_load_time_check() {
        $url        = home_url();
        $start_time = microtime( true );
        $response   = wp_remote_get( $url );
        $load_time  = microtime( true ) - $start_time;

        $threshold      = 2; // Set load time threshold in seconds
        $result         = ( $load_time > $threshold ) ? esc_html__( 'Needs Improvement', 'site-vitals-wp' ) : esc_html__( 'Good', 'site-vitals-wp' );
        $recommendation = ( $load_time > $threshold )
            ? wp_kses_post(
                sprintf(
                    /* translators: %s: homepage load time in seconds */
                    __( 'Homepage load time is <strong>%s seconds</strong>. Consider optimizing images or enabling caching.', 'site-vitals-wp' ),
                    esc_html( round( $load_time, 2 ) )
                )
            )
            : esc_html__( 'Homepage load time is optimal.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs font readability check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_font_readability_check() {
        $default_font_size   = '16px';
        $default_line_height = '1.5';

        // Check theme's customizer settings (if theme supports it).
        $font_size_set   = get_theme_mod( 'font_size_base', $default_font_size );
        $line_height_set = get_theme_mod( 'line_height_base', $default_line_height );

        $result         = ( ( $font_size_set === $default_font_size ) && ( $line_height_set === $default_line_height ) ) ? esc_html__( 'Good', 'site-vitals-wp' ) : esc_html__( 'Needs Attention', 'site-vitals-wp' );
        $recommendation = ( 'Good' === $result )
            ? esc_html__( 'Font size and line spacing meet readability standards.', 'site-vitals-wp' )
            : esc_html__( 'Consider adjusting font size and line spacing to improve readability.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs content freshness check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_content_freshness_check() {
        global $wpdb;

        // Try to get the stale posts count from the transient.
        $stale_posts = get_transient( 'site_vitals_stale_posts_count' );

        if ( false === $stale_posts ) {
            // Transient not set or expired; run the query.
            $stale_posts = $wpdb->get_var(
                "SELECT COUNT(*) FROM {$wpdb->posts}
                WHERE post_type = 'post'
                AND post_status = 'publish'
                AND post_modified < (NOW() - INTERVAL 1 YEAR)"
            );

            // Cache the result for 1 hour (3600 seconds).
            set_transient( 'site_vitals_stale_posts_count', $stale_posts, 3600 );
        }

        $result         = ( $stale_posts > 0 ) ? esc_html__( 'Needs Attention', 'site-vitals-wp' ) : esc_html__( 'Good', 'site-vitals-wp' );
        $recommendation = ( $stale_posts > 0 )
            ? wp_kses_post(
                sprintf(
                    /* translators: %s: number of stale posts */
                    __( '<strong>%s posts</strong> have not been updated in over a year. Consider updating or reviewing for relevance.', 'site-vitals-wp' ),
                    absint( $stale_posts )
                )
            )
            : esc_html__( 'All posts have been updated recently.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }
 
    /**
     * Updates a cached array of site vitals.
     *
     * This function runs heavy queries and stores the results in a transient.
     *
     * @return void
     */
    function update_site_vitals() {
        global $wpdb;

        $vitals = [];

        // Count posts with short content.
        $vitals['short_posts'] = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->posts}
                WHERE post_type = 'post'
                AND post_status = 'publish'
                AND LENGTH(post_content) < %d",
                300
            )
        );

        // Total published posts.
        $vitals['total_posts'] = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts}
            WHERE post_type = 'post' AND post_status = 'publish'"
        );

        // Posts with media.
        $vitals['posts_with_media'] = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(DISTINCT post_id)
                FROM {$wpdb->postmeta}
                WHERE meta_key = %s",
                '_thumbnail_id'
            )
        );

        // Duplicate titles.
        $vitals['duplicate_titles'] = $wpdb->get_var(
            "SELECT COUNT(*)
            FROM (
                SELECT post_title
                FROM {$wpdb->posts}
                WHERE post_type = 'post' AND post_status = 'publish'
                GROUP BY post_title
                HAVING COUNT(post_title) > 1
            ) AS duplicates"
        );

        // High revision count (more than 20 revisions per post).
        $vitals['high_revision_posts'] = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*)
                FROM (
                    SELECT post_parent
                    FROM {$wpdb->posts}
                    WHERE post_type = 'revision'
                    GROUP BY post_parent
                    HAVING COUNT(ID) > %d
                ) AS excessive_revisions",
                20
            )
        );

        // Posts missing taxonomy terms (e.g. categories/tags).
        $vitals['untagged_posts'] = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*)
                FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
                WHERE p.post_type = %s
                AND p.post_status = %s
                AND tr.term_taxonomy_id IS NULL",
                'post',
                'publish'
            )
        );

        // Save the data for one hour.
        set_transient( 'site_vitals_data', $vitals, HOUR_IN_SECONDS );
    }

    /**
     * Runs broken links check.
     *
     * @since 1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_broken_links_check() {
        // Try to get a cached value.
        $broken_links = get_transient( 'site_vitals_broken_links_count' );

        if ( false === $broken_links ) {
            global $wpdb;
            $post_links = $wpdb->get_results(
                "SELECT ID, post_content FROM {$wpdb->posts}
                WHERE post_type = 'post' AND post_status = 'publish'",
                OBJECT
            );

            $broken_links = 0;
            foreach ( $post_links as $post ) {
                preg_match_all( '/href=["\']?([^"\'>]+)["\']?/', $post->post_content, $matches );
                if ( ! empty( $matches[1] ) ) {
                    foreach ( $matches[1] as $link ) {
                        if ( filter_var( $link, FILTER_VALIDATE_URL ) ) {
                            $response = wp_remote_head( $link );
                            if ( is_wp_error( $response ) || 404 === intval( wp_remote_retrieve_response_code( $response ) ) ) {
                                $broken_links++;
                            }
                        }
                    }
                }
            }

            // Cache the result.
            set_transient( 'site_vitals_broken_links_count', $broken_links, HOUR_IN_SECONDS );
        }

        $result = ( $broken_links > 0 ) 
            ? esc_html__( 'Needs Attention', 'site-vitals-wp' ) 
            : esc_html__( 'Good', 'site-vitals-wp' );

        $recommendation = ( $broken_links > 0 )
            ? wp_kses_post(
                sprintf(
                    /* translators: %s: number of broken links */
                    __( '<strong>%s broken links</strong> found. Consider updating or removing these links.', 'site-vitals-wp' ),
                    absint( $broken_links )
                )
            )
            : esc_html__( 'No broken links detected.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs content length check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_content_length_check() {
        $vitals      = get_transient( 'site_vitals_data' );
        $short_posts = isset( $vitals['short_posts'] ) ? $vitals['short_posts'] : 0;

        $result = ( $short_posts > 0 ) 
            ? esc_html__( 'Needs Attention', 'site-vitals-wp' ) 
            : esc_html__( 'Good', 'site-vitals-wp' );

        $recommendation = ( $short_posts > 0 )
            ? wp_kses_post(
                sprintf(
                    /* translators: %s: number of short posts */
                    __( '<strong>%s posts</strong> have short content. Consider adding more detail or value to these posts.', 'site-vitals-wp' ),
                    absint( $short_posts )
                )
            )
            : esc_html__( 'All posts meet the recommended content length.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs media usage check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_media_usage_check() {
        $vitals           = get_transient( 'site_vitals_data' );
        $total_posts      = isset( $vitals['total_posts'] ) ? $vitals['total_posts'] : 0;
        $posts_with_media = isset( $vitals['posts_with_media'] ) ? $vitals['posts_with_media'] : 0;

        $missing_media_count = $total_posts - $posts_with_media;

        $result = ( $missing_media_count > 0 ) 
            ? esc_html__( 'Needs Attention', 'site-vitals-wp' ) 
            : esc_html__( 'Good', 'site-vitals-wp' );

        $recommendation = ( $missing_media_count > 0 )
            ? wp_kses_post(
                sprintf(
                    /* translators: %s: number of posts missing featured images */
                    __( '<strong>%s posts</strong> are missing featured images. Consider adding media to enrich content.', 'site-vitals-wp' ),
                    absint( $missing_media_count )
                )
            )
            : esc_html__( 'All posts include featured images or media.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs duplicate content check.
     *
     * @since 1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_duplicate_content_check() {
        $vitals           = get_transient( 'site_vitals_data' );
        $duplicate_titles = isset( $vitals['duplicate_titles'] ) ? $vitals['duplicate_titles'] : 0;

        $result = ( $duplicate_titles > 0 ) 
            ? esc_html__( 'Needs Attention', 'site-vitals-wp' ) 
            : esc_html__( 'Good', 'site-vitals-wp' );

        $recommendation = ( $duplicate_titles > 0 )
            ? wp_kses_post(
                sprintf(
                    /* translators: %s: number of duplicate titles */
                    __( '<strong>%s posts</strong> have duplicate titles. Consider making each title unique for better SEO.', 'site-vitals-wp' ),
                    absint( $duplicate_titles )
                )
            )
            : esc_html__( 'No duplicate titles detected.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs revision count check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_revision_count_check() {
        $vitals              = get_transient( 'site_vitals_data' );
        $high_revision_posts = isset( $vitals['high_revision_posts'] ) ? $vitals['high_revision_posts'] : 0;

        $result = ( $high_revision_posts > 0 ) 
            ? esc_html__( 'Needs Attention', 'site-vitals-wp' ) 
            : esc_html__( 'Good', 'site-vitals-wp' );

        $recommendation = ( $high_revision_posts > 0 )
            ? wp_kses_post(
                sprintf(
                    /* translators: %s: number of posts with excessive revisions */
                    __( '<strong>%s posts</strong> have excessive revisions. Consider cleaning up to optimize the database.', 'site-vitals-wp' ),
                    absint( $high_revision_posts )
                )
            )
            : esc_html__( 'Revision count is within an acceptable range.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Runs taxonomy usage check.
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_taxonomy_usage_check() {
        $vitals         = get_transient( 'site_vitals_data' );
        $untagged_posts = isset( $vitals['untagged_posts'] ) ? $vitals['untagged_posts'] : 0;

        $result = ( $untagged_posts > 0 ) 
            ? esc_html__( 'Needs Attention', 'site-vitals-wp' ) 
            : esc_html__( 'Good', 'site-vitals-wp' );

        $recommendation = ( $untagged_posts > 0 )
            ? wp_kses_post(
                sprintf(
                    /* translators: %s: number of posts missing categories or tags */
                    __( '<strong>%s posts</strong> are missing categories or tags. Consider categorizing/tagging them for better organization.', 'site-vitals-wp' ),
                    absint( $untagged_posts )
                )
            )
            : esc_html__( 'All posts are categorized or tagged.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Caching Status Check
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_caching_status_check() {
        $caching_plugins = [
            'wp-super-cache/wp-cache.php',
            'w3-total-cache/w3-total-cache.php',
            'wp-fastest-cache/wpFastestCache.php',
            'autoptimize/autoptimize.php',
            'litespeed-cache/litespeed-cache.php',
            'hummingbird-performance/wp-hummingbird.php',
            'cache-enabler/cache-enabler.php',
            'comet-cache/comet-cache.php',
            'hyper-cache/plugin.php',
            'simple-cache/simple-cache.php',
            'breeze/breeze.php',
            'nginx-helper/nginx-helper.php',
            'flexible-cache/flexible-cache.php',
            'powered-cache/powered-cache.php',
            'cachify/cachify.php',
            'swift-performance-lite/performance.php',
            'wp-optimize/wp-optimize.php',
            'redis-cache/redis-cache.php',
            'wp-rocket/wp-rocket.php',
            'wp-cloudflare-page-cache/wp-cloudflare-super-page-cache.php',
            'cloudflare/cloudflare.php',
            'fvm/fast-velocity-minify.php',
            'sg-cachepress/sg-cachepress.php',
            'quick-cache/quick-cache.php',
            'total-cache/cache.php',
            'advanced-cache/advanced-cache.php',
            'wp-cachecom/wp-cachecom.php',
            'faster-cache/faster-cache.php',
            'cache-optimizer/cache-optimizer.php',
            'gator-cache/gator-cache.php',
            'wp-performance/wp-performance.php',
            'leverage-browser-caching/leverage-browser-caching.php',
            'jch-optimize/jch-optimize.php',
            'page-speed-ninja/page-speed-ninja.php',
            'rapid-cache/rapid-cache.php',
            'wp-cloudflare-cache/wp-cloudflare-cache.php',
            'optimize-performance/optimize-performance.php',
            'phastpress/phastpress.php',
            'asset-cleanup/asset-cleanup.php',
            'minification-for-wp/minification-for-wp.php',
            'flying-pages/flying-pages.php',
            'litespeed-cache-helper/litespeed-cache-helper.php',
            'ezcache/ezcache.php',
            'super-static-cache/super-static-cache.php',
            'varnish-http-purge/varnish-http-purge.php',
            'clearfy/cache.php',
            'optimal-cache/optimal-cache.php',
            'rocket-nginx/rocket-nginx.php',
            'vendi-cache/vendi-cache.php',
            'simple-page-cache/simple-page-cache.php',
            'magic-cache/magic-cache.php',
        ];

        $active_cache_plugins = array_filter( $caching_plugins, function( $plugin ) {
            return is_plugin_active( $plugin );
        } );

        $result         = empty( $active_cache_plugins ) ? esc_html__( 'No Caching Detected', 'site-vitals-wp' ) : esc_html__( 'Caching Active', 'site-vitals-wp' );
        $recommendation = empty( $active_cache_plugins )
            ? esc_html__( 'Consider installing a caching plugin to improve performance.', 'site-vitals-wp' )
            : esc_html__( 'Caching is active, which helps improve performance.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * PHP Version Check
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_php_version_check() {
        $current_version     = phpversion();
        $recommended_version = '8.0';
        $result              = version_compare( $current_version, $recommended_version, '>=' ) ? esc_html__( 'Good', 'site-vitals-wp' ) : esc_html__( 'Needs Attention', 'site-vitals-wp' );
        $recommendation      = ( 'Good' === $result )
            ? esc_html__( 'PHP version is up to date.', 'site-vitals-wp' )
            : sprintf(
                /* translators: %1$s: current PHP version, %2$s: recommended PHP version */
                __( 'Current PHP version is %1$s. Consider upgrading to PHP %2$s or higher.', 'site-vitals-wp' ),
                esc_html( $current_version ),
                esc_html( $recommended_version )
            );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Database Optimization Check
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_database_optimization_check() {
        global $wpdb;

        $transient_count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE %s",
                '\_transient\_%'
            )
        );

        $revision_count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s",
                'revision'
            )
        );

        $needs_optimization = $transient_count > 100 || $revision_count > 100;

        $result         = $needs_optimization ? esc_html__( 'Needs Optimization', 'site-vitals-wp' ) : esc_html__( 'Good', 'site-vitals-wp' );
        $recommendation = $needs_optimization
            ? sprintf(
                /* translators: %1$d: number of transients, %2$d: number of revisions */
                __( 'Database optimization recommended: %1$d transients and %2$d post revisions detected.', 'site-vitals-wp' ),
                absint( $transient_count ),
                absint( $revision_count )
            )
            : esc_html__( 'Database is optimized.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Max Upload Size Check
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_max_upload_size_check() {
        $max_upload_size  = wp_max_upload_size();
        $recommended_size = 10 * 1024 * 1024; // 10 MB
        $result           = ( $max_upload_size >= $recommended_size ) ? esc_html__( 'Good', 'site-vitals-wp' ) : esc_html__( 'Needs Attention', 'site-vitals-wp' );
        $recommendation   = ( 'Good' === $result )
            ? esc_html__( 'Max upload size is sufficient.', 'site-vitals-wp' )
            : sprintf(
                /* translators: %s: formatted max upload size, %s: recommended size */
                __( 'Current max upload size is %1$s. Consider increasing it for larger media files.', 'site-vitals-wp' ),
                esc_html( size_format( $max_upload_size ) ),
                esc_html( size_format( $recommended_size ) )
            );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Memory Limit Check
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_memory_limit_check() {
        $memory_limit      = wp_convert_hr_to_bytes( WP_MEMORY_LIMIT );
        $recommended_limit = 128 * 1024 * 1024; // 128 MB
        $result            = ( $memory_limit >= $recommended_limit ) ? esc_html__( 'Good', 'site-vitals-wp' ) : esc_html__( 'Needs Attention', 'site-vitals-wp' );
        $recommendation    = ( 'Good' === $result )
            ? esc_html__( 'Memory limit is sufficient.', 'site-vitals-wp' )
            : sprintf(
                /* translators: %s: formatted memory limit, %s: recommended memory limit */
                __( 'Current memory limit is %1$s. Consider increasing it to at least %2$s.', 'site-vitals-wp' ),
                esc_html( size_format( $memory_limit ) ),
                esc_html( size_format( $recommended_limit ) )
            );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Gzip Compression Check
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_gzip_compression_check() {
        $compression_enabled = isset( $_SERVER['HTTP_ACCEPT_ENCODING'] ) && strpos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' ) !== false;
        $result              = $compression_enabled ? esc_html__( 'Good', 'site-vitals-wp' ) : esc_html__( 'Needs Attention', 'site-vitals-wp' );
        $recommendation      = $compression_enabled
            ? esc_html__( 'Gzip compression is enabled, which helps reduce page load time.', 'site-vitals-wp' )
            : esc_html__( 'Gzip compression is not enabled. Consider enabling it for faster load times.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Alt Text for Images Check
     *
     * @return array An array containing the result and recommendation.
     */
    public function run_alt_text_check() {
        $images_without_alt = get_posts( [
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'posts_per_page' => -1,
            'meta_query'     => [
                [
                    'key'     => '_wp_attachment_image_alt',
                    'compare' => 'NOT EXISTS',
                ],
            ],
            'fields'          => 'ids',
        ] );

        $count          = count( $images_without_alt );
        $result         = ( 0 === $count ) ? esc_html__( 'Good', 'site-vitals-wp' ) : esc_html__( 'Needs Attention', 'site-vitals-wp' );
        $recommendation = ( 0 === $count )
            ? esc_html__( 'All images have alt text.', 'site-vitals-wp' )
            : sprintf(
                /* translators: %d: number of images missing alt text */
                __( '<strong>%d images</strong> are missing alt text. Add descriptive alt text for accessibility.', 'site-vitals-wp' ),
                absint( $count )
            );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Color Contrast Check (basic placeholder)
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_color_contrast_check() {
        // Placeholder - requires custom scanning of site elements or a third-party service
        $result         = esc_html__( 'Unchecked', 'site-vitals-wp' );
        $recommendation = esc_html__( 'Use a contrast checker to ensure text is readable against its background color.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Keyboard Navigation Check
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_keyboard_navigation_check() {
        $result         = esc_html__( 'Unchecked', 'site-vitals-wp' );
        $recommendation = esc_html__( 'Ensure that all interactive elements are accessible via keyboard navigation.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * ARIA Roles and Landmarks Check
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_aria_roles_check() {
        $result         = esc_html__( 'Unchecked', 'site-vitals-wp' );
        $recommendation = esc_html__( 'Use appropriate ARIA roles and landmarks on major content areas for accessibility.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Form Labels Check
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_form_labels_check() {
        global $wpdb;

        $count = $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT COUNT(*)
                FROM {$wpdb->posts}
                WHERE post_content LIKE %s
                    AND post_content NOT LIKE %s
                ",
                '%<input%',
                '%<label%'
            )
        );

        $result         = ( 0 === $count ) ? esc_html__( 'Good', 'site-vitals-wp' ) : esc_html__( 'Needs Attention', 'site-vitals-wp' );
        $recommendation = ( 0 === $count )
            ? esc_html__( 'All form fields have labels.', 'site-vitals-wp' )
            : sprintf(
                /* translators: %d: number of form fields missing labels */
                __( '<strong>%d form fields</strong> are missing labels. Add labels to improve accessibility.', 'site-vitals-wp' ),
                absint( $count )
            );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Heading Structure Check
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_heading_structure_check() {
        // Placeholder - requires parsing content for heading levels
        $result         = esc_html__( 'Unchecked', 'site-vitals-wp' );
        $recommendation = esc_html__( 'Ensure headings follow a logical structure (e.g., H1 > H2 > H3) for accessibility.', 'site-vitals-wp' );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Link Descriptions Check
     *
     * @since  1.0.0
     * @return array An array containing the result and recommendation.
     */
    public function run_link_descriptions_check() {
        global $wpdb;

        $ambiguous_links = $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT COUNT(*)
                FROM {$wpdb->posts}
                WHERE post_content REGEXP %s
                ",
                '>(Click here|Read more|Learn more)<'
            )
        );

        $result         = ( 0 === $ambiguous_links ) ? esc_html__( 'Good', 'site-vitals-wp' ) : esc_html__( 'Needs Attention', 'site-vitals-wp' );
        $recommendation = ( 0 === $ambiguous_links )
            ? esc_html__( 'All links are descriptive.', 'site-vitals-wp' )
            : sprintf(
                /* translators: %d: number of ambiguous links */
                __( '<strong>%d links</strong> have ambiguous text. Use descriptive link text for accessibility.', 'site-vitals-wp' ),
                absint( $ambiguous_links )
            );

        return [
            'result'         => $result,
            'recommendation' => $recommendation,
        ];
    }

    /**
     * Displays category summary with checks.
     *
     * @param string $category The category identifier.
     * @param string $label    The label to display for the category.
     *
     * @since  1.0.0
     * @return void
     */
    public function display_category_summary( $category, $label ) {
        // Build the URL for the settings page.
        $settings_url = admin_url( 'admin.php?page=site-vitals-' . $category );

        // Build the output string.
        $output  = '<a href="' . esc_url( $settings_url ) . '" style="text-decoration: none; color: inherit;">';
        $output .= '<div class="site-vital-summary-box" data-category="' . esc_attr( $category ) . '">';
        $output .= '<h2>' . esc_html( $label ) . '</h2>';
        $output .= '<div class="site-vital-status-counts">';
        $output .= '<div class="sv-loading">' . esc_html__( 'Loading', 'site-vitals-wp' ) . '...</div>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</a>';

        // Echo the entire output once.
        echo $output;
    }


    /**
     * Gets the CSS class based on the status.
     *
     * @param string $status The status string.
     *
     * @since  1.0.0
     * @return string The corresponding CSS class.
     */
    private function get_status_class( $status ) {
        switch ( $status ) {
            case __( 'Good', 'site-vitals-wp' ):
                return 'status-good';
            case __( 'Needs Attention', 'site-vitals-wp' ):
                return 'status-warning';
            case __( 'Needs Improvement', 'site-vitals-wp' ):
                return 'status-danger';
            case __( 'No Caching Detected', 'site-vitals-wp' ):
                return 'status-danger';
            case __( 'Caching Active', 'site-vitals-wp' ):
                return 'status-good';
            case __( 'Needs Optimization', 'site-vitals-wp' ):
                return 'status-warning';
            default:
                return 'status-default';
        }
    }
}

new Site_Vitals_For_WordPress();
