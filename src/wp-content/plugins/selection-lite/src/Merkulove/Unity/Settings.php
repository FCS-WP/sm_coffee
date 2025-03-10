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

namespace Merkulove\SelectionLite\Unity;

use Merkulove\SelectionLite\Caster;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/**
 * SINGLETON: Class used to implement plugin settings.
 *
 * @since 1.0
 *
 **/
final class Settings {

	/**
	 * Plugin settings.
     *
     * @since 1.0
     * @access public
	 * @var array
	 **/
	public $options = [];

	/**
	 * The one true Settings.
	 *
     * @since 1.0
     * @access private
	 * @var Settings
	 **/
	private static $instance;

	/**
	 * Sets up a new Settings instance.
	 *
     * @since 1.0
	 * @access private
     *
     * @return void
	 **/
	private function __construct() {

		/** Get plugin settings. */
	    $this->get_options();

        /** Add plugin settings page. */
        $this->add_settings_page();

    }

	/**
	 * Render Tabs Headers.
	 *
     * @since 1.0
	 * @access private
     *
     * @return void
	 **/
	private function render_tabs() {

	    /** Selected tab key. */
        $current = $this->get_current_tab();

		/** Tabs array. */
		$tabs = Plugin::get_tabs();

		/** Render Tabs. */
		?>
        <aside class="mdc-drawer">
            <div class="mdc-drawer__content">
                <nav class="mdc-list">

                    <?php $this->render_logo(); ?>
                    <hr class="mdc-plugin-menu">
                    <hr class="mdc-list-divider">

                    <h6 class="mdc-list-group__subheader"><?php echo esc_html__( 'Plugin settings', 'selection-lite' ) ?></h6>
					<?php

					foreach ( $tabs as $tab => $value ) {

                        /** Skip disabled tabs. */
					    if ( ! Tab::is_tab_enabled( $tab ) ) { continue; }

						/** Prepare CSS classes. */
						$classes = [];
						$classes[] = 'mdc-list-item';
                        $classes[] = "mdp-menu-tab-{$tab}";

						/** Mark Active Tab. */
						if ( $tab === $current ) {
							$classes[] = 'mdc-list-item--activated';
						}

						/** Prepare link. */
						$link = '?page=mdp_selection_lite_settings&tab=' . $tab;

						?>
                        <a class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" href="<?php echo esc_attr( $link ); ?>">
                            <i class='material-icons mdc-list-item__graphic' aria-hidden='true'><?php echo esc_html( $value['icon'] ); ?></i>
                            <span class='mdc-list-item__text'><?php echo esc_html( $value['label'] ); ?></span>
                        </a>
						<?php

					}

					/** Helpful links. */
					$this->support_link();

					/** Create Go Pro Tab. */
					$this->display_pro();

					?>
                </nav>
            </div>
        </aside>
		<?php
	}

	/**
	 * Displays useful links for an activated and non-activated plugin.
     *
     * @since 1.0
     * @access private
	 *
     * @return void
	 **/
	private function support_link() {

	    ?>
        <hr class="mdc-list-divider">
        <h6 class="mdc-list-group__subheader"><?php echo esc_html__( 'Helpful links', 'selection-lite' ) ?></h6>

        <a class="mdc-list-item" href="https://docs.merkulov.design/tag/selection" target="_blank">
            <i class="material-icons mdc-list-item__graphic" aria-hidden="true">collections_bookmark</i>
            <span class="mdc-list-item__text"><?php echo esc_html__( 'Documentation', 'selection-lite' ) ?></span>
        </a>

        <a class="mdc-list-item" href="https://wordpress.org/support/plugin/selection-lite/" target="_blank">
            <i class="material-icons mdc-list-item__graphic" aria-hidden="true">mail</i>
            <span class="mdc-list-item__text"><?php echo esc_html__( 'Get help', 'selection-lite' ) ?></span>
        </a>

        <a class="mdc-list-item" href="https://wordpress.org/support/plugin/selection-lite/reviews/#new-post" target="_blank">
            <i class="material-icons mdc-list-item__graphic" aria-hidden="true">thumb_up</i>
            <span class="mdc-list-item__text"><?php echo esc_html__( 'Rate this plugin', 'selection-lite' ) ?></span>
        </a>

        <a class="mdc-list-item" href="https://1.envato.market/cc-merkulove" target="_blank">
            <i class="material-icons mdc-list-item__graphic" aria-hidden="true">store</i>
            <span class="mdc-list-item__text"><?php echo esc_html__( 'More plugins', 'selection-lite' ) ?></span>
        </a>
		<?php

	}

	/**
	 * Display Go Pro button.
	 *
	 * @since 1.0
	 * @access public
	 **/
	private function display_pro() {

		$go_pro_tab = admin_url( 'admin.php?page=mdp_selection_lite_settings&tab=pro' );

		?>
        <hr class="mdc-list-divider">
        <h6 class="mdc-list-group__subheader"><?php esc_html_e( 'Upgrade License', 'selection-lite' ); ?></h6>

        <a class="mdc-list-item mdc-activation-status activated" href="<?php echo esc_url( $go_pro_tab ); ?>">
            <i class='material-icons mdc-list-item__graphic' aria-hidden='true'>label_important</i>
            <span class="mdc-list-item__text"><?php esc_html_e( 'Upgrade to Pro', 'selection-lite' ); ?></span>
        </a>
		<?php

	}

	/**
	 * Add plugin settings page.
	 *
     * @since 1.0
	 * @access public
     *
     * @return void
	 **/
	public function add_settings_page() {

		add_action( 'admin_menu', [ $this, 'add_admin_menu' ], 1000 );
		add_action( 'admin_init', [ $this, 'settings_init' ] );

	}

	/**
	 * Generate Settings Page.
	 *
     * @since 1.0
	 * @access public
     *
     * @return void
	 **/
	public function settings_init() {

        /** Add settings foreach tab. */
        foreach ( Plugin::get_tabs() as $tab_slug => $tab ) {

            /** Skip tabs without handlers. */
            if ( empty( $tab['class'] ) ) { continue; }

            /** Call add_settings from appropriate class for each tab. */
            call_user_func( [ $tab['class'], 'get_instance' ] )->add_settings( $tab_slug );

        }

	}

	/**
	 * Add admin menu for plugin settings.
	 *
     * @since 1.0
	 * @access public
     *
     * @return void
	 **/
	public function add_admin_menu() {

	    /** Submenu for Elementor plugins. */
        if ( 'elementor' === Plugin::get_type() ) {

            $this->add_submenu_elementor();

        /** Root level menu for WordPress plugins. */
        } else {

            $this->add_menu_wordpress();

        }

	}

    /**
     * Add admin menu for Elementor plugins.
     *
     * @since 1.0
     * @access private
     *
     * @return void
     **/
	private function add_submenu_elementor() {

        /** Check if Elementor installed and activated. */
        $parent = 'options-general.php';
        if ( did_action( 'elementor/loaded' ) ) {
            $parent = 'elementor';
            //$parent = 'edit-comments.php';

        }

        add_submenu_page(
            $parent,
            esc_html__( 'Selection Settings', 'selection-lite' ),
            esc_html__( 'Selection for Lite', 'selection-lite' ),
            'manage_options',
            'mdp_selection_lite_settings',
            [ $this, 'options_page' ]
        );

    }

    /**
     * Add admin menu for WordPress plugins.
     *
     * @since 1.0
     * @access private
     *
     * @return void
     **/
	private function add_menu_wordpress() {

        add_menu_page(
            esc_html__( 'Selection Settings', 'selection-lite' ),
            esc_html__( 'Selection Lite', 'selection-lite' ),
            'manage_options',
            'mdp_selection_lite_settings',
            [ $this, 'options_page' ],
            'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJMYXllcl8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHg9IjBweCIgeT0iMHB4IgoJIHZpZXdCb3g9IjAgMCA0OCA0OCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDggNDg7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPHBhdGggZmlsbD0iI0EwQTVBQiIgZD0iTTI4LDBoLThDOSwwLDAsOSwwLDIwdjhjMCwxMSw5LDIwLDIwLDIwaDhjMTEsMCwyMC05LDIwLTIwdi04QzQ4LDksMzksMCwyOCwweiBNMTEsMTkuM0MxMSwxMy4xLDE3LjIsOCwyNC44LDgKCWMzLjEsMCw2LjIsMC45LDguNiwyLjZjMC4yLDAuMSwwLjQsMC40LDAuNCwwLjdzLTAuMSwwLjYtMC4zLDAuOEwzMiwxMy42Yy0wLjQsMC4zLTAuOSwwLjMtMS4zLDAuMWMtMS43LTEtMy44LTEuNi02LTEuNgoJYy01LjMsMC05LjYsMy4zLTkuNiw3LjNzNC4zLDcuMyw5LjYsNy4zYzAuMywwLDAuNiwwLjEsMC44LDAuM3MwLjMsMC40LDAuMywwLjd2MmMwLDAuNS0wLjQsMS0xLDFDMTcuMiwzMC43LDExLDI1LjYsMTEsMTkuM3oKCSBNMjMuMiw0MGMtMy4xLDAtNi4yLTAuOS04LjYtMi41Yy0wLjMtMC4yLTAuNC0wLjQtMC40LTAuN3MwLjEtMC42LDAuMy0wLjhsMS41LTEuNWMwLjMtMC4zLDAuOS0wLjQsMS4zLTAuMWMxLjcsMS4xLDMuOCwxLjYsNiwxLjYKCWM1LjMsMCw5LjYtMy4zLDkuNi03LjNzLTQuMy03LjMtOS42LTcuM2MtMC4zLDAtMC42LTAuMS0wLjgtMC4zYy0wLjItMC4yLTAuMy0wLjQtMC4zLTAuN3YtMmMwLTAuNiwwLjUtMSwxLTEKCWM3LjYtMC4xLDEzLjgsNSwxMy44LDExLjNDMzcsMzQuOSwzMC44LDQwLDIzLjIsNDB6Ii8+Cjwvc3ZnPgo=',
            $this->get_admin_menu_position()
        );

    }

    /**
     * Calculate admin menu position based on plugin slug value.
     *
     * @since 1.0
     * @access public
     *
     * @return string
     **/
	private function get_admin_menu_position() {

        $hash = md5( Plugin::get_slug() );

        $int = (int) filter_var( $hash, FILTER_SANITIZE_NUMBER_INT );
        $int =  (int) ( $int / 1000000000000 );

        return '58.' . $int;

    }

	/**
	 * Plugin Settings Page.
	 *
     * @since 1.0
	 * @access public
     *
     * @return void
	 **/
	public function options_page() {

		/** User rights check. */
		if ( ! current_user_can( 'manage_options' ) ) { return; } ?>

        <!--suppress HtmlUnknownTarget -->
        <form action='options.php' method='post'>
            <div class="wrap">
				<?php

				/** Render "Settings saved!" message. */
				$this->render_nags();

                /** Update custom CSS file */
                Caster::get_instance()->update_custom_css_file();

				/** Render Tabs Headers. */
				?><section class="mdp-aside"><?php $this->render_tabs(); ?></section><?php

				/** Render Tabs Body. */
				?><section class="mdp-tab-content mdp-tab-name-<?php echo esc_attr( $this->get_current_tab() ) ?>"><?php

                /** Call settings from appropriate class for current tab. */
                foreach ( Plugin::get_tabs() as $tab_slug => $tab ) {

                    /** Work only on current tab. */
                    if ( ! $this->is_tab( $tab_slug ) ) { continue; }

                    /** Skip tabs without handlers. */
                    if ( empty( $tab['class'] ) ) { continue; }

                    call_user_func( [ $tab['class'], 'get_instance' ] )->do_settings( $tab_slug );

                }

                ?>
                </section>
            </div>
        </form>

		<?php
	}

    /**
     * Return current selected tab or first tab.
     *
     * @since  1.0
     * @access private
     *
     * @return string
     **/
	private function get_current_tab() {

        $tab = key( Plugin::get_tabs() ); // First tab is default tab

        if ( isset ( $_GET['tab'] ) ) {

	        if ( isset( $_POST['selection_lite_settings_updated_nonce'] ) ) {
		        if ( ! wp_verify_nonce( $_POST['selection_lite_settings_updated_nonce'], 'selection-lite-settings-updated' ) ) {
			        wp_die( 'Nonce verification failed.' );
		        }
	        }

            $tab = sanitize_key( $_GET['tab'] );

        }

        return $tab;
    }

    /**
     * Check if passed tab is current tab and tab is enabled.
     *
     * @param string $tab - Tab slug to check.
     *
     * @since  1.0
     * @access private
     *
     * @return bool
     **/
	private function is_tab( $tab ) {

        $current_tab = $this->get_current_tab();

        return ( $tab === $current_tab ) && Tab::is_tab_enabled( $current_tab );

    }

	/**
	 * Render nags on after settings save.
     *
     * @since 1.0
     * @access private
     *
     * @return void
	 **/
	private function render_nags() {

        /** Exit if this is not settings update. */
		if ( ! isset( $_GET['settings-updated'] ) ) {

			if ( isset( $_POST['selection_lite_settings_updated_nonce'] ) ) {
				if ( ! wp_verify_nonce( $_POST['selection_lite_settings_updated_nonce'], 'selection-lite-settings-updated' ) ) {
					wp_die( 'Nonce verification failed.' );
				}
			}

            return;
        }

        /** Render "Settings Saved" message. */
        $this->render_nag_saved();

	}

    /**
     * Render "Settings Saved" message.
     *
     * @since 1.0
     * @access private
     *
     * @return void
     **/
    private function render_nag_saved() {

        /** Exit if settings saving was not successful. */
        if ( 'true' !== $_GET['settings-updated'] ) {

	        if ( isset( $_POST['selection_lite_settings_updated_nonce'] ) ) {
		        if ( ! wp_verify_nonce( $_POST['selection_lite_settings_updated_nonce'], 'selection-lite-settings-updated' ) ) {
			        wp_die( 'Nonce verification failed.' );
		        }
	        }

            return;

        }

        /** Render "Settings Saved" message. */
        UI::get_instance()->render_snackbar( esc_html__( 'Settings saved!', 'selection-lite' ) );

    }

	/**
	 * Render logo and Save changes button in plugin settings.
	 *
     * @since 1.0
	 * @access private
     *
	 * @return void
	 **/
	private function render_logo() {

		?>
        <div class="mdc-drawer__header mdc-plugin-fixed">
            <a class="mdc-list-item mdp-plugin-title" href="#">
                <i class="mdc-list-item__graphic" aria-hidden="true">
                    <img src="<?php echo esc_attr( Plugin::get_url() . 'images/logo-color.svg' ); ?>" alt="<?php esc_html_e( 'SelectionLite', 'selection-lite' ) ?>">
                </i>
                <span class="mdc-list-item__text">
                    <?php if ( 'wordpress' === Plugin::get_type() ) : ?>
                        <?php esc_html_e( 'Selection Lite', 'selection-lite' ) ?>
                    <?php else: ?>
                        <?php esc_html_e( 'Selection', 'selection-lite' ) ?>
                    <?php endif; ?>
                </span>
                <span class="mdc-list-item__text">
                    <sup>
                        <?php esc_html_e( 'v.', 'selection-lite' ); ?>
                        <?php echo esc_attr( Plugin::get_version() ); ?>
                    </sup>
                </span>
            </a>
            <button type="submit" name="submit" id="submit" class="mdc-button mdc-button--dense mdc-button--raised">
                <span class="mdc-button__label"><?php esc_html_e( 'Save changes', 'selection-lite' ) ?></span>
            </button>
        </div>
		<?php

	}

    /**
     * Return settings array with default values.
     *
     * @since 1.0
     * @access public
     *
     * @return array
     **/
	private function get_default_settings() {

        /** Get all plugin tabs with settings fields. */
        $tabs = Plugin::get_tabs();

        $default = [];

        /** Collect settings from each tab. */
        foreach ( $tabs as $tab_slug => $tab ) {

            /** If current tab haven't fields. */
            if ( empty( $tab['fields'] ) ) { continue; }

            /** Collect default values from each field. */
            foreach ( $tab['fields'] as $field_slug => $field ) {

                $default[$field_slug] = $field['default'];

            }

        }

        return $default;

    }

	/**
	 * Get plugin settings with default values.
	 *
     * @since 1.0
	 * @access public
     *
	 * @return void
	 **/
	public function get_options() {

        /** Default values. */
        $defaults = $this->get_default_settings();

        $results = [];

        /** Get all plugin tabs with settings fields. */
        $tabs = Plugin::get_tabs();

        /** Collect settings from each tab. */
        foreach ( $tabs as $tab_slug => $tab ) {

	        $opt_name = "mdp_selection_lite_{$tab_slug}_settings";
            $options = get_option( $opt_name );
            $results = wp_parse_args( $options, $defaults );
            $defaults = $results;

        }

		$this->options = $results;

	}

	/**
	 * Main Settings Instance.
	 * Insures that only one instance of Settings exists in memory at any one time.
	 *
	 * @static
     * @since 1.0
     * @access public
     *
	 * @return Settings
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
