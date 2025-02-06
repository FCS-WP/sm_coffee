<?php /** @noinspection PhpUndefinedClassInspection */

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



use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Exception;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Merkulove\SelectionLite\Unity\Plugin as UnityPlugin;

/** @noinspection PhpUnused */

/**
 * Widgeter - Custom Elementor Widget.
 **/
class posts_elementor extends Widget_Base {

	/**
	 * Use this to sort widgets.
	 * A smaller value means earlier initialization of the widget.
	 * Can take negative values.
	 * Default widgets and widgets from 3rd party developers have 0 $mdp_order
	 **/
	public $mdp_order = 1;

	/**
	 * Widget base constructor.
	 * Initializing the widget base class.
	 *
	 * @access public
	 *
	 * @param array $data Widget data. Default is an empty array.
	 * @param array|null $args Optional. Widget default arguments. Default is null.
	 *
	 * @return void
	 **@throws Exception If arguments are missing when initializing a full widget instance.
	 */
	public function __construct( $data = [], $args = null ) {

		parent::__construct( $data, $args );

		wp_register_style(
			'mdp-selection-lite-admin',
			UnityPlugin::get_url() . 'css/elementor-admin' . UnityPlugin::get_suffix() . '.css',
			[],
			UnityPlugin::get_version()
		);

	}

	/**
	 * Return a widget name.
	 *
	 * @return string
	 **/
	public function get_name() {

		return 'mdp-widgeter-posts-elementor';

	}

	/**
	 * Return the widget title that will be displayed as the widget label.
	 *
	 * @return string
	 **/
	public function get_title() {

		return esc_html__( 'Recent posts', 'selection-lite' );

	}

	/**
	 * Set the widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {

		return 'mdp-posts-elementor-widget-icon';

	}

	/**
	 * Set the category of the widget.
	 *
	 * @return array with category names
	 **/
	public function get_categories() {

		return [ 'selection-category' ];

	}

	/**
	 * Get widget keywords. Retrieve the list of keywords the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget keywords.
	 **/
	public function get_keywords() {

		return [ 'Merkulove', 'Widgeter' ];

	}

	/**
	 * Get style dependencies.
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @access public
	 *
	 * @return array Widget styles dependencies.
	 **/
	public function get_style_depends() {

		return [ 'mdp-widgeter', 'mdp-widgeter-elementor-admin' ];

	}

	/**
	 * Get script dependencies.
	 * Retrieve the list of script dependencies the element requires.
	 *
	 * @access public
	 *
	 * @return array Element scripts dependencies.
	 **/
	public function get_script_depends() {

		return [ 'mdp-widgeter' ];

	}

	/**
	 * Add the widget controls.
	 *
	 * @access protected
	 * @return void with category names
	 **/
	protected function register_controls() {

		/** Content Tab. */
		$this->tab_content();

		/** Style Tab. */
		$this->tab_style();

	}

	/**
	 * Add widget controls on Content tab.
	 *
	 * @return void
	 **@since 1.0.0
	 * @access private
	 *
	 */
	private function tab_content() {

		/** Content -> General Content Section. */
		$this->section_content_general();

	}

	/**
	 * Add widget controls on Style tab.
	 *
	 * @return void
	 **@since 1.0.0
	 * @access private
	 *
	 */
	private function tab_style() {

		/** Style -> Section Style Title. */
		$this->section_style_title();

		/** Style -> Section Style List. */
		$this->section_style_list();

		/** Style -> Section Style List Item. */
		$this->section_style_list_item();

	}

	/**
	 * Add widget controls: Content -> General Content Section.
	 *
	 * @return void
	 **@since 1.0.0
	 * @access private
	 *
	 */
	private function section_content_general() {

		$this->start_controls_section( 'section_content_general', [
			'label' => esc_html__( 'General', 'selection-lite' ),
			'tab'   => Controls_Manager::TAB_CONTENT
		] );

		$this->add_control(
			'posts_title',
			[
				'label'       => esc_html__( 'Title', 'selection-lite' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Type your title here', 'selection-lite' ),
			]
		);

		$this->add_control(
			'posts_title_tag',
			[
				'label'   => esc_html__( 'Title HTML Tag', 'selection-lite' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				],
				'default' => 'h5'
			]
		);

		$this->add_control(
			'enable_posts_title_icon',
			[
				'label'        => esc_html__( 'Enable title icon', 'selection-lite' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'selection-lite' ),
				'label_off'    => esc_html__( 'No', 'selection-lite' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'posts_title_icon',
			[
				'label'     => esc_html__( 'Title icon', 'selection-lite' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-star',
					'library' => 'solid',
				],
				'condition' => [
					'enable_posts_title_icon' => 'yes'
				]
			]
		);

		$this->add_control(
			'posts_icon_position',
			[
				'label'     => esc_html__( 'Icon position', 'selection-lite' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'before',
				'options'   => [
					'before'       => esc_html__( 'Left', 'selection-lite' ),
					'after'        => esc_html__( 'Right', 'selection-lite' ),
					'above-left'   => esc_html__( 'Top left', 'selection-lite' ),
					'above-center' => esc_html__( 'Top center', 'selection-lite' ),
					'above-right'  => esc_html__( 'Top right', 'selection-lite' ),
					'under-left'   => esc_html__( 'Bottom left', 'selection-lite' ),
					'under-center' => esc_html__( 'Bottom center', 'selection-lite' ),
					'under-right'  => esc_html__( 'Bottom right', 'selection-lite' )
				],
				'condition' => [
					'enable_posts_title_icon' => 'yes'
				]
			]
		);

		$this->add_control(
			'posts_list_layout',
			[
				'label'     => esc_html__( 'List layout', 'selection-lite' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'column',
				'options'   => [
					'column' => esc_html__( 'Vertical', 'selection-lite' ),
					'row'    => esc_html__( 'Horizontal', 'selection-lite' ),
				],
				'selectors' => [
					'{{WRAPPER}} .mdp-widgeter-elementor-box ul' => 'flex-direction: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'posts_list_alignment',
			[
				'label'   => esc_html__( 'List alignment', 'selection-lite' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'   => [
						'title' => esc_html__( 'Left', 'selection-lite' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'selection-lite' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'selection-lite' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'toggle'  => true,
			]
		);

		$this->add_control(
			'posts_title_alignment',
			[
				'label'     => esc_html__( 'Title alignment', 'selection-lite' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'selection-lite' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'selection-lite' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'selection-lite' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mdp-widgeter-elementor-title-wrapper' => 'text-align: {{VALUE}};'
				],
				'default'   => 'left',
				'toggle'    => true,
			]
		);

		$this->add_control(
			'posts_items_display',
			[
				'label'      => esc_html__( 'Posts to display', 'selection-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 20,
						'step' => 1,
					]
				]
			]
		);

		$this->add_control(
			'display_post_date',
			[
				'label'        => esc_html__( 'Display post date', 'selection-lite' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Function for generating margin padding controls.
	 *
	 * @param $section_id
	 * @param $html_class
	 * @param array $default_padding
	 * @param array $default_margin
	 *
	 * @return void
	 * @since 1.0.0
	 * @access private
	 */
	private function generate_margin_padding_controls( $section_id, $html_class, $default_padding = [], $default_margin = [] ) {
		$this->add_responsive_control(
			$section_id . '_margin',
			[
				'label'      => esc_html__( 'Margin', 'selection-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],
				'default'    => $default_margin,
				'selectors'  => [
					"{{WRAPPER}} .$html_class" => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			$section_id . '_padding',
			[
				'label'      => esc_html__( 'Padding', 'selection-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],
				'separator'  => 'after',
				'default'    => $default_padding,
				'selectors'  => [
					"{{WRAPPER}} .$html_class" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);
	}

	/**
	 * Function for generating typography and tabs controls.
	 *
	 * @param $section_id
	 * @param $opts
	 *
	 * @return void
	 * @since 1.0.0
	 * @access private
	 */
	private function generate_typography_tabs_controls( $section_id, $opts = [] ) {
		$style_opts = [
			'html_class'                           => array_key_exists( 'html_class', $opts ) ?
				$opts['html_class'] : '',
			'active_class'                         => array_key_exists( 'active_class', $opts ) ?
				$opts['active_class'] : '',
			'include_color'                        => array_key_exists( 'include_color', $opts ) ?
				$opts['include_color'] : true,
			'include_border'                       => array_key_exists( 'include_border', $opts ) ?
				$opts['include_border'] : true,
			'include_bg'                           => array_key_exists( 'include_bg', $opts ) ?
				$opts['include_color'] : true,
			'include_typography'                   => array_key_exists( 'include_typography', $opts ) ?
				$opts['include_typography'] : true,
			'include_transition'                   => array_key_exists( 'include_transition', $opts ) ?
				$opts['include_transition'] : true,
			'additional_color'                     => array_key_exists( 'additional_color', $opts ) ?
				$opts['additional_color'] : false,
			'additional_background'                => array_key_exists( 'additional_background', $opts ) ?
				$opts['additional_background'] : false,
			'typography_name'                      => array_key_exists( 'typography_name', $opts ) ?
				$opts['typography_name'] : 'Typography',
			'additional_background_name'           => array_key_exists( 'additional_background_name', $opts ) ?
				$opts['additional_background_name'] : '',
			'additional_background_class'          => array_key_exists( 'additional_background_class', $opts ) ?
				$opts['additional_background_class'] : '',
			'additional_border_radius_class'       => array_key_exists( 'additional_border_radius_class', $opts ) ?
				$opts['additional_border_radius_class'] : '',
			'additional_border_radius_hover_class' => array_key_exists( 'additional_border_radius_hover_class', $opts ) ?
				$opts['additional_border_radius_hover_class'] : '',
			'include_active_tab'                   => array_key_exists( 'include_active_tab', $opts ) ?
				$opts['include_active_tab'] : false,
			'active_tab_name'                      => array_key_exists( 'active_tab_name', $opts ) ?
				$opts['active_tab_name'] : 'FOCUS',
			'color_prefix'                         => array_key_exists( 'color_prefix', $opts ) ?
				$opts['color_prefix'] : '',
			'color_class'                          => array_key_exists( 'color_class', $opts ) ?
				$opts['color_class'] : '',
			'color_hover_class'                    => array_key_exists( 'color_hover_class', $opts ) ?
				$opts['color_hover_class'] : '',
			'color_active_class'                   => array_key_exists( 'color_active_class', $opts ) ?
				$opts['color_active_class'] : '',
			'color_hover_selector'                 => array_key_exists( 'color_hover_selector', $opts ) ?
				$opts['color_hover_selector'] : '',
			'additional_color_name'                => array_key_exists( 'additional_color_name', $opts ) ?
				$opts['additional_color_name'] : '',
			'additional_color_class'               => array_key_exists( 'additional_color_class', $opts ) ?
				$opts['additional_color_class'] : '',
			'additional_color_hover_class'         => array_key_exists( 'additional_color_hover_class', $opts ) ?
				$opts['additional_color_hover_class'] : '',
			'additional_color_active_class'        => array_key_exists( 'additional_color_active_class', $opts ) ?
				$opts['additional_color_active_class'] : '',
			'additional_transition_selector'       => array_key_exists( 'additional_transition_selector', $opts ) ?
				$opts['additional_transition_selector'] : '',
			'typography_class'                     => array_key_exists( 'typography_class', $opts ) ?
				$opts['typography_class'] : '',
		];


		if ( $style_opts['include_typography'] ) {
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => $section_id . '_typography',
					'label'    => esc_html( $style_opts['typography_name'] ),

					'selector' => "{{WRAPPER}} ." . $style_opts['typography_class'],
				]
			);
		}

		$this->start_controls_tabs( $section_id . '_style_tabs' );

		$this->start_controls_tab(
			$section_id . '_normal_style_tab',
			[ 'label' => esc_html__( 'NORMAL', 'selection-lite' ) ]
		);

		if ( $style_opts['include_color'] ) {
			$this->add_control(
				$section_id . '_normal_text_color',
				[
					'label'     => esc_html( $style_opts['color_prefix'] . 'Color' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						"{{WRAPPER}} ." . $style_opts['color_class'] => 'color: {{VALUE}} !important;',
					],
				]
			);

		}

		if ( $style_opts['additional_color'] ) {
			$this->add_control(
				$section_id . '_' . $style_opts['additional_color_name'] . '_normal_text_color',
				[
					'label'     => esc_html( $style_opts['additional_color_name'] ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						"{{WRAPPER}} ." . $style_opts['additional_color_class'] => 'color: {{VALUE}} !important;',

					],
				]
			);
		}

		if ( $style_opts['include_bg'] ) {

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => $section_id . '_normal_background',
					'label'    => esc_html__( 'Background type', 'selection-lite' ),
					'types'    => [ 'classic', 'gradient', 'video' ],
					'selector' => "{{WRAPPER}} ." . $style_opts['html_class'],
				]
			);

		}

		if ( $style_opts['additional_background'] ) {

			$this->add_control(
				$section_id . '_' . $style_opts['additional_background_name'] . '_normal_text_color',
				[
					'label'     => esc_html( $style_opts['additional_background_name'] ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						"{{WRAPPER}} ." . $style_opts['additional_background_class'] => 'background: {{VALUE}} !important;',

					],
				]
			);

		}

		$this->add_control(
			$section_id . '_separate_normal',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		if ( $style_opts['include_border'] ) {

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'     => $section_id . '_border_normal',
					'label'    => esc_html__( 'Border Type', 'selection-lite' ),
					'selector' => "{{WRAPPER}} ." . $style_opts['html_class'],
				]
			);

		}

		$this->add_responsive_control(
			$section_id . '_border_radius_normal',
			[
				'label'      => esc_html__( 'Border radius', 'selection-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					"{{WRAPPER}} ." . $style_opts['html_class']   => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					$style_opts['additional_border_radius_class'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => $section_id . '_box_shadow_normal',
				'label'    => esc_html__( 'Box Shadow', 'selection-lite' ),
				'selector' => "{{WRAPPER}} ." . $style_opts['html_class'],
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab(
			$section_id . '_hover_style_tab',
			[ 'label' => esc_html__( 'HOVER', 'selection-lite' ) ]
		);

		if ( $style_opts['include_color'] ) {
			$this->add_control(
				$section_id . '_hover_color',
				[
					'label'     => esc_html( $style_opts['color_prefix'] . 'Color' ),
					'type'      => Controls_Manager::COLOR,

					'selectors' => [
						"{{WRAPPER}} ." . $style_opts['color_hover_class'] => 'color: {{VALUE}} !important;',
					],
				]
			);
		}

		if ( $style_opts['additional_color'] ) {
			$this->add_control(
				$section_id . '_' . $style_opts['additional_color_name'] . '_hover_text_color',
				[
					'label'     => esc_html( $style_opts['additional_color_name'] ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						"{{WRAPPER}} ." . $style_opts['additional_color_hover_class'] => 'color: {{VALUE}} !important;',
					],
				]
			);
		}

		if ( $style_opts['include_bg'] ) {
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => $section_id . '_background_hover',
					'label'    => esc_html__( 'Background type', 'selection-lite' ),
					'types'    => [ 'classic', 'gradient', 'video' ],
					'selector' => "{{WRAPPER}} ." . $style_opts['html_class'] . ":hover",
				]
			);
		}

		if ( $style_opts['additional_background'] ) {

			$this->add_control(
				$section_id . '_' . $style_opts['additional_background_name'] . '_hover_text_color',
				[
					'label'     => esc_html( $style_opts['additional_background_name'] ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						"{{WRAPPER}} ." . $style_opts['additional_background_class'] . ':hover' => 'background: {{VALUE}} !important;',

					],
				]
			);

		}


		if ( $style_opts['include_transition'] ) {
			$this->add_control(
				$section_id . '_hover_transition',
				[
					'label'      => esc_html__( 'Hover transition duration', 'selection-lite' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 's' ],
					'range'      => [
						's' => [
							'min'  => 0.1,
							'max'  => 5,
							'step' => 0.1,
						],
					],
					'default'    => [
						'unit' => 's',
						'size' => 0,
					],
					'selectors'  => [
						'{{WRAPPER}} .' . $style_opts['html_class']   => 'transition: color {{SIZE}}{{UNIT}}, background {{SIZE}}{{UNIT}}, box-shadow {{SIZE}}{{UNIT}}, border-radius {{SIZE}}{{UNIT}}, border {{SIZE}}{{UNIT}}, filter {{SIZE}}{{UNIT}}, stroke {{SIZE}}{{UNIT}};',
						$style_opts['additional_transition_selector'] => 'transition: color {{SIZE}}{{UNIT}}, background {{SIZE}}{{UNIT}}, box-shadow {{SIZE}}{{UNIT}}, border-radius {{SIZE}}{{UNIT}}, border {{SIZE}}{{UNIT}}, filter {{SIZE}}{{UNIT}}, stroke {{SIZE}}{{UNIT}};;'
					],
				]
			);
		}

		$this->add_control(
			$section_id . '_separate_hover',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		if ( $style_opts['include_border'] ) {

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'     => $section_id . '_border_hover',
					'label'    => esc_html__( 'Border Type', 'selection-lite' ),
					'selector' => "{{WRAPPER}} ." . $style_opts['html_class'] . ":hover",
				]
			);

		}

		$this->add_responsive_control(
			$section_id . '_border_radius_hover',
			[
				'label'      => esc_html__( 'Border radius', 'selection-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					"{{WRAPPER}} ." . $style_opts['html_class'] . ":hover" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					$style_opts['additional_border_radius_hover_class']    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => $section_id . '_box_shadow_hover',
				'label'    => esc_html__( 'Box Shadow', 'selection-lite' ),
				'selector' => "{{WRAPPER}} ." . $style_opts['html_class'] . ":hover",
			]
		);

		$this->end_controls_tab();

		if ( $style_opts['include_active_tab'] ) {

			$this->start_controls_tab(
				$section_id . '_focus_style_tab',
				[ 'label' => esc_html( $style_opts['active_tab_name'] ) ]
			);

			if ( $style_opts['include_color'] ) {
				$this->add_control(
					$section_id . '_active_color',
					[
						'label'     => esc_html( $style_opts['color_prefix'] . 'Color' ),
						'type'      => Controls_Manager::COLOR,

						'selectors' => [
							"{{WRAPPER}} ." . $style_opts['color_active_class'] => 'color: {{VALUE}} !important;',
						],
					]
				);
			}

			if ( $style_opts['additional_color'] ) {
				$this->add_control(
					$section_id . '_' . $style_opts['additional_color_name'] . '_active_text_color',
					[
						'label'     => esc_html( $style_opts['additional_color_name'] ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							"{{WRAPPER}} ." . $style_opts['additional_color_active_class'] => 'color: {{VALUE}} !important;',
						],
					]
				);
			}

			if ( $style_opts['include_bg'] ) {
				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => $section_id . '_background_active',
						'label'    => esc_html__( 'Background type', 'selection-lite' ),
						'types'    => [ 'classic', 'gradient', 'video' ],
						'selector' => "{{WRAPPER}} ." . $style_opts['active_class'],
					]
				);
			}

			$this->add_control(
				$section_id . '_separate_active',
				[
					'type' => Controls_Manager::DIVIDER,
				]
			);

			if ( $style_opts['include_border'] ) {

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'     => $section_id . '_border_active',
						'label'    => esc_html__( 'Border Type', 'selection-lite' ),
						'selector' => "{{WRAPPER}} ." . $style_opts['active_class'],
					]
				);

			}

			$this->add_responsive_control(
				$section_id . '_border_radius_active',
				[
					'label'      => esc_html__( 'Border radius', 'selection-lite' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors'  => [
						"{{WRAPPER}} ." . $style_opts['active_class'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => $section_id . '_box_shadow_active',
					'label'    => esc_html__( 'Box Shadow', 'selection-lite' ),
					'selector' => "{{WRAPPER}} ." . $style_opts['active_class'],
				]
			);

			$this->end_controls_tab();

		}


		$this->end_controls_tabs();
	}

	/**
	 * Add widget controls: Style -> Section Style Title.
	 *
	 * @return void
	 **@since 1.0.0
	 * @access private
	 *
	 */
	private function section_style_title() {

		$this->start_controls_section( 'section_style_title', [
			'label' => esc_html__( 'Title', 'selection-lite' ),
			'tab'   => Controls_Manager::TAB_STYLE
		] );

		$this->generate_margin_padding_controls(
			'section_style_title',
			'mdp-widgeter-posts-title'
		);

		$this->add_control(
			'title_width',
			[
				'label'      => esc_html__( 'Width', 'selection-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 1000,
						'step' => 1,
					],
					'%'  => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .mdp-widgeter-posts-title' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'posts_title_icon_spacing',
			[
				'label'      => esc_html__( 'Icon spacing', 'selection-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],
				'condition'  => [
					'enable_posts_title_icon' => 'yes'
				],
				'separator'  => 'before',
				'selectors'  => [
					"{{WRAPPER}} .mdp-widgeter-elementor-title-icon" => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'posts_title_icon_size',
			[
				'label'      => esc_html__( 'Icon size', 'selection-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					]
				],
				'condition'  => [
					'enable_posts_title_icon' => 'yes'
				],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} .mdp-widgeter-elementor-title-icon'     => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .mdp-widgeter-elementor-title-icon i'   => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .mdp-widgeter-elementor-title-icon svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->generate_typography_tabs_controls( 'section_style_title', [
			'html_class'                     => 'mdp-widgeter-posts-title',
			'typography_class'               => 'mdp-widgeter-posts-title',
			'additional_color'               => true,
			'additional_color_name'          => 'Icon color',
			'additional_color_class'         => 'mdp-widgeter-elementor-title-icon',
			'additional_color_hover_class'   => 'mdp-widgeter-posts-title:hover .mdp-widgeter-elementor-title-icon',
			'additional_transition_selector' => '{{WRAPPER}} .mdp-widgeter-elementor-title-icon',
			'color_class'                    => 'mdp-widgeter-posts-title',
			'color_hover_class'              => 'mdp-widgeter-posts-title:hover',
		] );

		$this->end_controls_section();

	}


	/**
	 * Add widget controls: Style -> Section Style List.
	 *
	 * @return void
	 **@since 1.0.0
	 * @access private
	 *
	 */
	private function section_style_list() {

		$this->start_controls_section( 'section_style_list', [
			'label' => esc_html__( 'List', 'selection-lite' ),
			'tab'   => Controls_Manager::TAB_STYLE
		] );

		$this->generate_margin_padding_controls(
			'section_style_list',
			'mdp-widgeter-posts-elementor-box ul'
		);

		$this->add_control(
			'list_width',
			[
				'label'      => esc_html__( 'Width', 'selection-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 1000,
						'step' => 1,
					],
					'%'  => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .mdp-widgeter-elementor-box ul' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'posts_list_style_type',
			[
				'label'     => esc_html__( 'List style type', 'selection-lite' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'disc',
				'options'   => [
					'disc'                 => esc_html__( 'Disc', 'selection-lite' ),
					'armenian'             => esc_html__( 'Armenian', 'selection-lite' ),
					'cjk-ideographic'      => esc_html__( 'Cjk ideographic', 'selection-lite' ),
					'decimal'              => esc_html__( 'Decimal', 'selection-lite' ),
					'decimal-leading-zero' => esc_html__( 'Decimal leading zero', 'selection-lite' ),
					'georgian'             => esc_html__( 'Georgian', 'selection-lite' ),
					'hebrew'               => esc_html__( 'Hebrew', 'selection-lite' ),
					'hiragana'             => esc_html__( 'Hiragana', 'selection-lite' ),
					'hiragana-iroha'       => esc_html__( 'Hiragana iroha', 'selection-lite' ),
					'katakana'             => esc_html__( 'Katakana', 'selection-lite' ),
					'katakana-iroha'       => esc_html__( 'Katakana iroha', 'selection-lite' ),
					'lower-alpha'          => esc_html__( 'Lower alpha', 'selection-lite' ),
					'lower-greek'          => esc_html__( 'Lower greek', 'selection-lite' ),
					'lower-latin'          => esc_html__( 'Lower latin', 'selection-lite' ),
					'lower-roman'          => esc_html__( 'Lower roman', 'selection-lite' ),
					'square'               => esc_html__( 'Square', 'selection-lite' ),
					'upper-alpha'          => esc_html__( 'Upper alpha', 'selection-lite' ),
					'upper-greek'          => esc_html__( 'Upper greek', 'selection-lite' ),
					'upper-latin'          => esc_html__( 'Upper latin', 'selection-lite' ),
					'upper-roman'          => esc_html__( 'Upper roman', 'selection-lite' ),
					'none'                 => esc_html__( 'None', 'selection-lite' ),
				],
				'selectors' => [
					'{{WRAPPER}} .mdp-widgeter-posts-elementor-box ul' => 'list-style-type: {{VALUE}};',
				],
			]
		);

		$this->generate_typography_tabs_controls( 'section_style_list', [
			'html_class'         => 'mdp-widgeter-posts-elementor-box ul',
			'include_typography' => false,
			'include_color'      => false,
		] );

		$this->end_controls_section();

	}

	/**
	 * Add widget controls: Style -> Section Style List Item.
	 *
	 * @return void
	 **@since 1.0.0
	 * @access private
	 *
	 */
	private function section_style_list_item() {

		$this->start_controls_section( 'section_style_list_item', [
			'label' => esc_html__( 'List item', 'selection-lite' ),
			'tab'   => Controls_Manager::TAB_STYLE
		] );

		$this->generate_margin_padding_controls(
			'section_style_list_item',
			'mdp-widgeter-posts-elementor-box li'
		);

		$this->generate_typography_tabs_controls( 'section_style_list_item', [
			'html_class'        => 'mdp-widgeter-posts-elementor-box li',
			'typography_class'  => 'mdp-widgeter-posts-elementor-box a',
			'color_class'       => 'mdp-widgeter-posts-elementor-box li a, {{WRAPPER}} .mdp-widgeter-posts-elementor-box li',
			'color_hover_class' => 'mdp-widgeter-posts-elementor-box li:hover a, {{WRAPPER}} .mdp-widgeter-posts-elementor-box li:hover',
		] );

		$this->end_controls_section();

	}

	/**
	 * Render Frontend Output. Generate the final HTML on the frontend.
	 *
	 * @access protected
	 *
	 * @return void
	 **/
	protected function render() {

		$settings        = $this->get_settings_for_display();

		$valid_tags = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ];
		$tag               = in_array( $settings['posts_title_tag'], $valid_tags ) ? $settings['posts_title_tag'] : 'h5';

		$default_widget_args = Caster::get_instance()->widgeter_get_widget_default_args(
			'posts',
			esc_attr( $tag ),
			$settings['enable_posts_title_icon'] === 'yes',
			esc_attr($settings['posts_title_icon']),
			esc_attr($settings['posts_icon_position']),
			'mdp-widgeter-elementor-list-align-' . esc_attr( $settings['posts_list_alignment'] )
		);

		$title = str_replace( [ '<', '>', '&', '"', '”', '\'', '‘' ], '', $settings['posts_title'] );
		$number = $settings['posts_items_display']['size'] ?? '0';

		the_widget( 'WP_Widget_Recent_Posts', [
			'title'     => esc_attr($title),
			'number'    => esc_attr($number),
			'show_date' => $settings['display_post_date'] === 'yes'
		], $default_widget_args );

	}

	/**
	 * Return link for documentation
	 * Used to add stuff after widget
	 *
	 * @access public
	 *
	 * @return string
	 **/
	public function get_custom_help_url() {

		return 'https://docs.merkulov.design/tag/widgeter';

	}

}
