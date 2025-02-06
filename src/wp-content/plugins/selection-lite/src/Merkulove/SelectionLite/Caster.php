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

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

use Elementor\Icons_Manager;
use Merkulove\SelectionLite\Unity\Plugin;
use Merkulove\SelectionLite\Unity\Settings;
use Merkulove\SelectionLite\Unity\UI;

/**
 * SINGLETON: Caster class contain main plugin logic.
 *
 * @since 1.0
 *
 **/
final class Caster {

	/**
	 * The one true Caster.
	 *
	 * @since 1.0
	 * @access private
	 * @var Caster
	 **/
	private static $instance;

	/**
	 * Array of duplicated activated plugins that used Selection
	 * @var array
	 */
	private static $duplicates = array();

	/**
	 * Set up the plugin.
	 *
	 * @return void
	 **@since 1.0
	 * @access public
	 *
	 */
	public function setup() {

		/** Define hooks that runs on both the front-end and the dashboard. */
		$this->both_hooks();

		/** Define public hooks. */
		$this->public_hooks();

		/** Define admin hooks. */
		$this->admin_hooks();

	}

	/**
	 * Define hooks that runs on both the front-end and the dashboard.
	 *
	 * @return void
	 **@since 1.0
	 * @access private
	 *
	 */
	private function both_hooks() {

		/** Add Sticky Effect */
		StickyEffect::get_instance();

		/** Add Elementor Widgets */
		Elementor::get_instance()->register_elementor_widgets();

		/** Run template kit styles import */
		TemplateKitStyles::get_instance();

	}

	/**
	 * Public hooks.
	 *
	 * @return void
	 **@since 1.0
	 * @access private
	 *
	 */
	private function public_hooks() {
		/** Public hooks and filters. */

		/** Work only on frontend area. */
		if ( is_admin() ) {
			return;
		}

		/** Add plugin custom css file styles. */
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_custom_css_file' ] );
	}

	/**
	 * Register all the hooks related to the admin area functionality.
	 *
	 * @return void
	 **@since 1.0
	 * @access private
	 *
	 */
	private function admin_hooks() {

		/** Work only in admin area. */
		if ( ! is_admin() ) {
			return;
		}

		/**  Add custom category. */
		add_action( 'elementor/elements/categories_registered', [ $this, 'add_elementor_widget_categories' ] );

		/** Add meta-box scripts and styles */
		add_action( 'current_screen', [ $this, 'enqueue_edit' ] );

		/** Show message on the plugins page */
		add_action( 'current_screen', [ $this, 'active_merkulove_plugins' ] );

		/** Render notices */
		add_action( 'admin_notices', [ $this, 'render_notice_duplicate' ] );
		add_action( 'admin_footer', [ $this, 'render_snackbar_duplicate' ] );

	}


	/**
	 * Create title icon.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access private
	 *
	 */
	private function create_widgeter_title_icon( $title_icon, $enabled ) {

		if ( empty( $title_icon ) || ! $enabled ) {
			return '';
		}

		$icon = $title_icon['library'] === 'svg' ?
			Icons_Manager::render_uploaded_svg_icon( $title_icon['value'] ) :
			'<i class="' . esc_attr( $title_icon['value'] ) . '"></i>';

		return sprintf(
			'<div class="mdp-widgeter-elementor-title-icon">%s</div>',
			$icon
		);
	}

	/**
	 * Updates custom CSS file/Creates if it is not exists.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access private
	 *
	 */
	public function update_custom_css_file() {

		if ( ! isset( $_GET['settings-updated'] ) ) {
			return;
		}

		if ( isset( $_POST['selection_lite_settings_updated_nonce'] ) ) {
			if ( ! wp_verify_nonce( $_POST['selection_lite_settings_updated_nonce'], 'selection-lite-settings-updated' ) ) {
				wp_die( 'Nonce verification failed.' );
			}
		}

		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		WP_Filesystem();

		$upload_dir = wp_upload_dir();
		$directory  = trailingslashit( $upload_dir['basedir'] ) . 'selection-lite';
		$file_path  = trailingslashit( $directory ) . $this->css_file_name() . '.css';

		if ( ! $wp_filesystem->is_dir( $directory ) ) {
			$wp_filesystem->mkdir( $directory );
		}

		try {
			$wp_filesystem->put_contents( $file_path, Settings::get_instance()->options['custom_css'], FS_CHMOD_FILE );
		} catch ( \Exception $e ) {
			if ( WP_DEBUG_LOG ) {
				error_log( $e->getMessage() );
			}
		}
	}

	public function css_file_name() {

		// Get domain from site url
		return wp_parse_url( get_site_url(), PHP_URL_HOST );

	}

	/**
	 * Enqueue custom CSS file
	 *
	 * @return void
	 * @since 1.0.0
	 * @access private
	 *
	 */
	public function enqueue_custom_css_file() {

		$custom_css = Settings::get_instance()->options['custom_css'];

		if ( $this->is_custom_css_not_empty() ) {
			wp_enqueue_style(
				'mdp-selection-lite-custom',
				wp_get_upload_dir()['baseurl'] . '/selection-lite/' . $this->css_file_name() . '.css',
				[],
				Plugin::get_version()
			);
		} else if ( ! empty( $custom_css ) ) {
			wp_add_inline_style( 'elementor-frontend', $custom_css );
		}


	}

	/**
	 * Check if custom CSS not empty
	 *
	 * @return bool
	 * @since 1.0.0
	 * @access private
	 *
	 */
	private function is_custom_css_not_empty() {
		$file_path = wp_get_upload_dir()['basedir'] . '/selection-lite/' . $this->css_file_name() . '.css';

		return file_exists( $file_path ) && filesize( $file_path ) > 0;
	}

	public function widgeter_get_widget_default_args( $type, $title_tag, $icon_enabled, $icon, $icon_position, $align_class = '', $before_widget = '' ) {
		$disable_default_icon = $type === 'rss-feed' && $icon_enabled ? 'mdp-widgeter-disable-default-icon' : '';

		$default_args = [
			'before_widget' => '<div 
                                class="mdp-widgeter-elementor-box  
                                mdp-widgeter-' . $type . '-elementor-box ' . $align_class . ' ' . $disable_default_icon . ' "
                                >',
			'after_widget'  => '</div>',
			'before_title'  => sprintf(
				'<div class="mdp-widgeter-elementor-title-wrapper"> 
                                   <%s class="
                                        mdp-widgeter-title 
                                        mdp-widgeter-%s-title 
                                        mdp-widgeter-elementor-icon-position-%s">%s',
				$title_tag,
				$type,
				esc_attr( $icon_position ),
				$this->create_widgeter_title_icon( $icon, $icon_enabled )
			),
			'after_title'   => sprintf( '</%s></div>', $title_tag )
		];

		/** Set custom before widget */
		if ( ! empty( $before_widget ) ) {
			$default_args['before_widget'] = $before_widget;
		}

		return $default_args;
	}

	/**
	 * Find and store activated merkulove plugins for Elementor
	 */
	public function active_merkulove_plugins() {

		$screen = get_current_screen();
		if ( null === $screen ) {
			return;
		}

		// Run only on the Plugins page and Selection settings
		if ( ! in_array( $screen->id, [ 'plugins', 'toplevel_page_mdp_selection_lite_settings' ] ) ) {
			return;
		}

		// Get active plugins
		$active_plugin = get_option( 'active_plugins' );
		if ( ! is_array( $active_plugin ) ) {
			return;
		}

		// Get selection plugins
		$selection_plugins = get_option( 'mdp_selection_lite_widgets_settings' );
		if ( ! is_array( $selection_plugins ) ) {
			return;
		}

		foreach ( $active_plugin as $plugin_path ) {

			$plugin_slug = explode( '/', $plugin_path )[0];

			// Find plugins contains -elementor in the file name
			if ( strpos( $plugin_slug, '-elementor' ) ) {

				$selection_slug = str_replace( '-elementor', '', $plugin_slug );

				if ( isset( $selection_plugins[ $selection_slug ] ) && $selection_plugins[ $selection_slug ] === 'on' ) {

					array_push( self::$duplicates, ucfirst( $selection_slug ) );

				}

			}

		}

	}

	/**
	 * Render message about duplicated elementor widgets as single plugin by merkulove
	 */
	public function render_notice_duplicate() {

		if ( empty( self::$duplicates ) ) {
			return;
		}

		$duplicates = preg_filter( '/$/', ' for Elementor', self::$duplicates );
		?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <strong>Selection</strong><?php esc_html_e( ' already uses the functionality of these plugins: ', 'selection-lite' ); ?>
                <strong><?php echo esc_html( implode( ', ', $duplicates ) ); ?></strong>.
				<?php esc_html_e( ' You can safely deactivate these plugins.', 'selection-lite' ); ?>
            </p>
        </div>
		<?php

	}

	/**
	 * Render snackbar about duplicated elementor widgets as single plugin by merkulove
	 */
	public function render_snackbar_duplicate() {

		if ( empty( self::$duplicates ) ) {
			return;
		}

		$duplicates = preg_filter( '/$/', ' for Elementor', self::$duplicates );
		UI::get_instance()->render_snackbar(
			esc_html__( 'Selection already uses the functionality of these plugins: ', 'selection-lite' ) . implode( ', ', $duplicates ),
			'warning',
			- 1,
			true,
			[
				[
					'caption' => esc_html__( 'Plugins', 'selection-lite' ),
					'link'    => admin_url( 'plugins.php' )
				]
			]
		);

	}

	/**
	 * Add custom category.
	 *
	 * @param $elements_manager
	 *
	 * @return void
	 * @since 1.0
	 */
	public function add_elementor_widget_categories( $elements_manager ) {

		$elements_manager->add_category(
			'selection-category',
			[
				'title' => esc_html__( 'Selection Lite', 'selection-lite' ),
				'icon'  => 'fa fa-plug',
			]
		);

	}

	/**
	 * Scripts and Styles for the Template edit page
	 *
	 * @return void
	 **@since   1.0
	 */
	public function enqueue_edit() {

		$screen = get_current_screen();

		// Run only on Elementor Templates
		if ( null === $screen ) {
			return;
		}
		if ( 'elementor_library' !== $screen->id ) {
			return;
		}

		// Add class .mdc-disable to body. So we can use UI without overrides WP CSS, only for this page
		add_action( 'admin_body_class', [ $this, 'add_admin_class' ] );

		// Enqueue styles
		wp_enqueue_style( 'merkulov-ui', Plugin::get_url() . 'src/Merkulove/Unity/assets/css/merkulov-ui' . Plugin::get_suffix() . '.css', [], Plugin::get_version() );
		wp_enqueue_style( 'mdp-selection-edit', Plugin::get_url() . 'css/admin-edit' . Plugin::get_suffix() . '.css', [], Plugin::get_version() );

		// Enqueue scripts
		wp_enqueue_script( 'merkulov-ui', Plugin::get_url() . 'src/Merkulove/Unity/assets/js/merkulov-ui' . Plugin::get_suffix() . '.js', [], Plugin::get_version(), true );
		wp_enqueue_script( 'mdp-selection-edit', Plugin::get_url() . 'js/assignments' . Plugin::get_suffix() . '.js', [ 'jquery' ], Plugin::get_version(), true );

		/** Add code editor for Custom PHP. */
		wp_enqueue_code_editor( array( 'type' => 'application/x-httpd-php' ) );

	}

	/**
	 * Generates data for breadcrumbs.
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_data_breadcrumbs() {
		$breadcrumbs = [];

		// check if woocommerce plugin is active
		$woocommerce_active = false;
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			$woocommerce_active = true;
		}

		$page_num = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

		if ( is_front_page() ) {
			if ( $page_num > 1 ) {
				$breadcrumbs['homepage'] = [
					'name'        => get_bloginfo( 'name' ),
					'is_homepage' => true,
					'is_parent'   => false,
					'is_child'    => false,
					'link'        => site_url(),
					'is_active'   => true,
					'element'     => '',
				];

			}
		} else {
			if ( $woocommerce_active ) {
				if ( ! is_woocommerce() ) {
					$breadcrumbs['homepage'] = [
						'name'        => get_bloginfo( 'name' ),
						'is_homepage' => true,
						'is_parent'   => false,
						'is_child'    => false,
						'link'        => site_url(),
						'is_active'   => false,
						'element'     => ''
					];
				}
			} else {
				$breadcrumbs['homepage'] = [
					'name'        => get_bloginfo( 'name' ),
					'is_homepage' => true,
					'is_parent'   => false,
					'is_child'    => false,
					'link'        => site_url(),
					'is_active'   => false,
					'element'     => ''
				];
			}
			if ( get_post_type() === 'post' ) {
				$post_categories = get_the_category();
				if ( ! empty( $post_categories[0]->cat_ID ) ) {
					$elements = array_filter( explode( ',', get_category_parents( $post_categories[0]->cat_ID, true, ',' ) ) );
					for ( $i = 0; $i < count( $elements ); $i ++ ) {
						$breadcrumbs[ 'element_' . $i ] = [
							'name'        => '',
							'is_homepage' => false,
							'is_parent'   => false,
							'is_child'    => false,
							'link'        => '',
							'is_active'   => false,
							'element'     => $elements[ $i ]
						];
					}
				}
				$breadcrumbs['post'] = [
					'name'        => get_the_title(),
					'is_homepage' => false,
					'is_parent'   => false,
					'is_child'    => false,
					'link'        => '',
					'is_active'   => true,
					'element'     => ''
				];

			} elseif ( get_post_type() === 'page' ) {

				// if page has parent pages
				global $post;
				if ( $post->post_parent ) {
					$parent_id = $post->post_parent;
					while ( $parent_id ) {
						$page                       = get_post( $parent_id );
						$breadcrumbs['parent_page'] = [
							'name'        => get_the_title( $page->ID ),
							'is_homepage' => false,
							'is_child'    => false,
							'link'        => get_permalink( $page->ID ),
							'is_parent'   => true,
							'is_active'   => false,
							'element'     => ''
						];
						$parent_id                  = $page->post_parent;
					}
				}

				$breadcrumbs['page'] = [
					'name'        => get_the_title(),
					'is_homepage' => false,
					'is_parent'   => false,
					'is_child'    => false,
					'is_active'   => true,
					'link'        => '',
					'element'     => ''
				];

				// if page has child pages
				$children = get_children( [ 'post_type' => 'page', 'post_parent' => get_the_ID() ] );
				if ( $children ) {
					foreach ( $children as $child ) {
						$breadcrumbs['child_page'] = [
							'name'        => $child->post_title,
							'is_homepage' => false,
							'is_parent'   => false,
							'link'        => $child->guid,
							'is_active'   => false,
							'element'     => '',
							'is_child'    => true
						];
					}
				}

			} elseif ( is_category() ) {
				$breadcrumbs['category'] = [
					'name'        => single_cat_title( '', 0 ),
					'is_homepage' => false,
					'is_parent'   => false,
					'is_child'    => false,
					'is_active'   => true,
					'link'        => '',
					'element'     => ''
				];
			} elseif ( is_tag() ) {
				$breadcrumbs['tag'] = [
					'name'        => single_tag_title( '', 0 ),
					'is_homepage' => false,
					'is_parent'   => false,
					'is_child'    => false,
					'is_active'   => true,
					'link'        => '',
					'element'     => ''
				];
			} elseif ( is_day() ) {
				$breadcrumbs['year_archive']  = [
					'name'        => get_the_time( 'Y' ),
					'is_homepage' => false,
					'is_parent'   => false,
					'is_child'    => false,
					'link'        => get_year_link( get_the_time( 'Y' ) ),
					'is_active'   => false,
					'element'     => ''
				];
				$breadcrumbs['month_archive'] = [
					'name'        => get_the_time( 'F' ),
					'is_homepage' => false,
					'is_parent'   => false,
					'is_child'    => false,
					'link'        => get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ),
					'is_active'   => false,
					'element'     => ''
				];
				$breadcrumbs['day_archive']   = [
					'name'        => get_the_time( 'd' ),
					'is_homepage' => false,
					'is_parent'   => false,
					'is_child'    => false,
					'is_active'   => true,
					'link'        => '',
					'element'     => ''
				];
			} elseif ( is_month() ) {
				$breadcrumbs['year_archive']  = [
					'name'        => get_the_time( 'Y' ),
					'is_homepage' => false,
					'is_parent'   => false,
					'is_child'    => false,
					'link'        => get_year_link( get_the_time( 'Y' ) ),
					'is_active'   => false,
					'element'     => ''
				];
				$breadcrumbs['month_archive'] = [
					'name'        => get_the_time( 'F' ),
					'is_homepage' => false,
					'is_parent'   => false,
					'is_child'    => false,
					'is_active'   => true,
					'link'        => '',
					'element'     => ''
				];
			} elseif ( get_post_type() === 'elementor_library' ) {
				$breadcrumbs['elementor_library'] = [
					'name'        => get_the_title(),
					'is_homepage' => false,
					'is_parent'   => false,
					'is_child'    => false,
					'link'        => '',
					'is_active'   => true,
					'element'     => ''
				];
			} elseif ( is_year() ) {
				$breadcrumbs['year_archive'] = [
					'name'        => get_the_time( 'Y' ),
					'is_homepage' => false,
					'is_parent'   => false,
					'is_child'    => false,
					'is_active'   => true,
					'link'        => '',
					'element'     => ''
				];
			} elseif ( is_author() ) {
				global $author;
				$userdata                      = get_userdata( $author );
				$breadcrumbs['author_archive'] = [
					'name'        => $userdata->display_name,
					'is_homepage' => false,
					'is_parent'   => false,
					'is_child'    => false,
					'is_active'   => true,
					'link'        => '',
					'element'     => ''
				];
			} elseif ( is_404() ) {
				$breadcrumbs['Error 404'] = [
					'name'        => 'Error 404',
					'is_homepage' => false,
					'is_parent'   => false,
					'is_child'    => false,
					'is_active'   => true,
					'link'        => '',
					'element'     => ''
				];
			} elseif ( $woocommerce_active ) {
				if ( is_woocommerce() ) {
					$breadcrumbs['woocommerce'] = [
						'is_homepage' => false,
						'is_parent'   => false,
						'is_child'    => false,
						'is_active'   => false,
						'link'        => '',
						'element'     => ''
					];
				}
			}
		}

		return $breadcrumbs;
	}

	/**
	 * Add class to body in admin area.
	 *
	 * @param string $classes - Space-separated list of CSS classes.
	 *
	 * @return string
	 * @since 1.0
	 */
	public function add_admin_class( $classes ) {

		return $classes . ' mdc-disable ';

	}

	/**
	 * Main Caster Instance.
	 * Insures that only one instance of Caster exists in memory at any one time.
	 *
	 * @static
	 * @return Caster
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
