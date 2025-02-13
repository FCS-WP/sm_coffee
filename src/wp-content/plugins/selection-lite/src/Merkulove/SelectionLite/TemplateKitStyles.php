<?php
/**
 * Selection Lite
 * Carefully selected Elementor addons bundle, for building the most awesome websites
 *
 * @encoding        UTF-8
 * @version         1.15
 * @copyright       (C) 2018-2024 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         GPLv3
 * @contributors    merkulove, vladcherviakov, phoenixmkua, podolianochka, viktorialev01
 * @support         help@merkulov.design
 **/

namespace Merkulove\SelectionLite;

use Exception;
use WP_REST_Response;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

final class TemplateKitStyles {

	/**
	 * @since 1.0
	 * @access private
	 * @var TemplateKitStyles
	 **/
	private static $instance;


	/**
	 * @since 1.0
	 * @access private
	 **/
	private function __construct() {

		// If Envato Template Kit export plugin installed on multisite
		if ( is_multisite() && is_plugin_active( 'template-kit-export/template-kit-export.php' ) ) {
			add_action( 'rest_api_init', [ $this, 'register_template_kit_id_endpoint' ] );
		}

		// Add action to get Template Kit styles
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Is Envato Template Kit Import plugin installed?
		if ( ! is_plugin_active( 'template-kit-import/template-kit-import.php' ) ) {
			return;
		}

		// Handle created Template Kit post
		add_action( 'wp_after_insert_post', [ $this, 'after_insert_post' ], 10, 3 );

	}

	/**
	 * Register the site_id endpoint
	 */
	public function register_template_kit_id_endpoint() {
		register_rest_route( 'template-kit/v1', '/id', array(
			'methods'  => 'GET',
			'callback' => function () {
				return new WP_REST_Response( get_current_blog_id(), 200 );
			},
		) );
	}

	/**
	 * Handle created Template Kit post
	 *
	 * @param $id
	 * @param $post
	 *
	 * @return void
	 */
	public function after_insert_post( $id, $post ) {

		// If envato_tk_import post type
		if ( 'envato_tk_import' !== $post->post_type ) {
			return;
		}

		// Template Kit domain
		$tk_domain = 'templatekit.co';

		// Get Template Kit slug from the Post title
		$tk = strtolower( $post->post_title );

		// Get Template Kit ID
		$tk_id = $this->remote_response( 'https://' . $tk . '.' . $tk_domain . '/wp-json/template-kit/v1/id' );
		if ( ! $tk_id ) {
			return;
		}

		// Get Template Kit styles
		$tk_css = $this->remote_response( 'https://' . $tk_domain . '/wp-content/uploads/sites/' . $tk_id . '/selection-lite/' . $tk . '.' . $tk_domain . '.css' );
		if ( ! $tk_css ) {
			return;
		}

		// Get saved Custom CSS settings
		$custom_css_settings = get_option( 'mdp_selection_lite_custom_css_settings', [] );

		// Get Template Kit styles
		$css = $custom_css_settings['custom_css'] ?? '';
		$css .= '
/* Template Kit - ' . $tk . ' */
';
		$css .= $tk_css;

		// Save Template Kit styles
		$this->save_kit_styles( $css );

		// Update file
		$this->update_css_style( $css );

	}

	/**
	 * Get remote response
	 *
	 * @param $url
	 *
	 * @return string|null
	 */
	private function remote_response( $url ) {

		$request = wp_remote_get(
			$url,
			[
				'timeout'   => 10,
				'blocking'  => true,
				'sslverify' => false,
			]
		);

		// Skip if errors
		if ( is_wp_error( $request ) ) {
			return null;
		}

		// Skip if response code is not 200
		if ( 200 !== wp_remote_retrieve_response_code( $request ) ) {
			return null;
		}

		return wp_remote_retrieve_body( $request );

	}

	/**
	 * Save Template Kit styles to the Custom CSS
	 *
	 * @param $css
	 *
	 * @return void
	 */
	private function save_kit_styles( $css ) {

		$css_options = get_option( 'mdp_selection_lite_custom_css_settings', [] );

		$css_options['custom_css'] = $css;

		update_option( 'mdp_selection_lite_custom_css_settings', $css_options );

	}

	/**
	 * Update CSS file
	 *
	 * @param $css
	 *
	 * @return void
	 */
	private function update_css_style( $css ) {

		global $wp_filesystem;

		// Create the directory if it doesn't exist
		$upload_dir = trailingslashit( wp_upload_dir()['basedir'] ) . 'selection-lite';
		if ( ! $wp_filesystem->is_dir( $upload_dir ) ) {
			$wp_filesystem->mkdir( $upload_dir );
		}

		// Define the file path
		$file_path = $upload_dir . '/' . wp_parse_url( get_site_url(), PHP_URL_HOST ) . '.css';

		// Write the CSS to the file
		if ( ! $wp_filesystem->put_contents( $file_path, $css, FS_CHMOD_FILE ) ) {
			if ( WP_DEBUG_LOG ) {
				error_log( 'Failed to write CSS to ' . $file_path );
			}
		}

	}

	/**
	 * @static
	 * @return TemplateKitStyles
	 **@since 1.0
	 * @access public
	 *
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
