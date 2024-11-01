<?php
/*
Plugin Name: SiteWatcher
Description: SiteWatcher will automatically scan your website after each plugin, theme and WordPress update. Receive immediate notifications whenever SiteWatcher detects significant changes or concerns on your website.
Version:     1.3.2
Tested up to: 6.4
Author:      nusoft
Author URI:  https://profiles.wordpress.org/nusoft/
Text Domain: sitewatcher
Domain Path: /languages
License:     GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) or die;

define( 'SITEWATCHER_OPTSGROUP_NAME', 'sitewatcher_optsgroup' );
define( 'SITEWATCHER_OPTIONS_NAME', 'sitewatcher_options' );
define( 'SITEWATCHER_LOGO_URL', plugins_url( 'images/logo.png', __FILE__ ) );
define( 'SITEWATCHER_VER', '1.3.2' );

if ( ! class_exists( 'SiteWatcher' ) ) {
    class SiteWatcher {
        public static function get_instance() {
            if ( self::$instance == null ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private $inc_dir = null;

        private static $instance = null;

		private $options = null;

        private function __clone() { }

        public function __wakeup() { }

        private function __construct() {
			// Props
			$this->options = null;

			// Activation hooks
			register_activation_hook( __FILE__, array( $this, 'activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );

            // Actions
			add_action( 'init', array( $this, 'load_textdomain' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
			add_action( 'rest_api_init', array( $this, 'add_rest_endpoint' ) );

			// Filters
			add_filter( 'auto_core_update_send_email', array( $this, 'auto_type_update_send_email' ) );
			add_filter( 'auto_theme_update_send_email', array( $this, 'auto_type_update_send_email' ) );
			add_filter( 'auto_plugin_update_send_email', array( $this, 'auto_type_update_send_email' ) );
        }

		public function activation() {
			update_option( SITEWATCHER_OPTIONS_NAME, array( 'enable_pi' => 1 ) );
		}

		public function deactivation() {
			delete_option( SITEWATCHER_OPTIONS_NAME );
		}

		public function load_textdomain() {
			load_plugin_textdomain( 'sitewatcher', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		public function register_settings() {
			register_setting( SITEWATCHER_OPTSGROUP_NAME, SITEWATCHER_OPTIONS_NAME );
		}

		public function add_menu_item() {
			add_menu_page(
				__( 'SiteWatcher', 'sitewatcher' ),
				__( 'SiteWatcher', 'sitewatcher' ),
				'manage_options',
				'sitewatcher',
				array( $this, 'render_options_page' ),
				'dashicons-visibility'
			);
		}

		public function render_options_page() {
			require( __DIR__ . '/options.php' );
		}

		public function add_rest_endpoint() {
			register_rest_route( 'sitewatcher/v1', '/info', array(
				'methods' => 'GET',
				'callback' => array( $this, 'output_json_info' ),
			) );
		}

		public function output_json_info( WP_REST_Request $request ) {
			if ( $this->get_option( 'enable_pi' ) != '1' ) {
				return new WP_REST_Response( new StdClass );
			}

			return new WP_REST_Response( array(
				'wordpress' => $this->get_wordpress_info(),
				'themes' => $this->get_themes_info(),
				'plugins' => $this->get_plugins_info()
			), 200 );
		}

		public function auto_type_update_send_email( $enabled ) {
			$disable_en = $this->get_option( 'disable_en' );
			if ( $disable_en ) {
				return false;
			}
			return $enabled;
		}

		private function get_plugin_data( $plugin ) {
			$file = trailingslashit( WP_PLUGIN_DIR ) . $plugin;
			if ( ! file_exists( $file ) ) return array();

			return get_plugin_data( $file );
		}

		private function get_theme_data( $theme ) {
			$_theme = wp_get_theme( $theme );

			return array( 'Name' => $_theme->get( 'Name' ), 'Version' => $_theme->get( 'Version' ) );
		}

		private function get_wordpress_info() {
			return array(
				'blogname' => get_bloginfo( 'name' ),
				'siteurl' => home_url(),
				'version' => get_bloginfo( 'version' )
			);
		}

		private function get_themes_info() {
			$current_theme = wp_get_theme();
			$output = array();
			$themes = wp_get_themes();
			foreach( $themes as $theme ) {
				$output[$theme->get( 'Name' )] = array(
					'version' => $theme->get( 'Version' ),
					'status' => $theme->get( 'Name' ) == $current_theme->get( 'Name' ) ? 'active' : 'inactive',
				);
			}
			return $output;
		}

		private function get_plugins_info() {
			$output = array();
			$plugins = get_plugins();
			foreach( $plugins as $index => $plugin ) {
				$output[$plugin['Name']] = array(
					'version' => $plugin['Version'],
					'status' => is_plugin_active( $index ) ? 'active' : 'inactive',
				);
			}
			return $output;
		}

		private function get_option( $option_name, $default = '' ) {
			if ( is_null( $this->options ) ) $this->options = ( array ) get_option( SITEWATCHER_OPTIONS_NAME, array() );
			if ( isset( $this->options[$option_name] ) ) return $this->options[$option_name];
			return $default;
		}
    }

	SiteWatcher::get_instance();
}