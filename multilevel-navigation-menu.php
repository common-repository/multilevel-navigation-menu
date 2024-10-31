<?php
/**
 * Plugin Name: Multilevel Navigation Menu
 * Plugin URI: https://wordpress.org/plugins/multilevel-navigation-menu
 * Description: Multilevel Navigation Menu plugin ability to add a full-screen navigation menu to our website.
 * Author: Laxman Prajapati
 * Author URI: https://laxmanprajapati.wordpress.com/
 * Version: 1.0.8
 * Text Domain: multilevel-navigation-menu
 *
 * Multilevel Navigation Menu is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Multilevel Navigation Menu is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Multilevel Navigation Menu. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package MultilevelNavigationMenu
 * @author Laxman Prajapati
 * @version 1.0.8
 */

class Multilevel_Navigation_Menu_WP {
	public function __construct() {
		register_activation_hook( __FILE__, array( __CLASS__, 'mnmwp_active_function' ) );
		add_action( 'activated_plugin', array( __CLASS__, 'mnmwp_activation_redirect' ) );
		
		// Admin JS
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'mnmwp_assets' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'mnmwp_frontend_assets' ) );

		// Setting Page
		add_action( 'admin_menu', array( __CLASS__, 'mnmwp_add_menu' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( __CLASS__, 'mnmwp_add_link' ) );
		add_action( 'admin_init', array( __CLASS__, 'mnmwp_setting_display' ) );

		// Register Menu Location
		add_action( 'init', array( __CLASS__, 'mnmwp_register_menu' ) );

		// Multilevel Navigation Menu Shortcode
		add_shortcode( 'multilevel_navigation_menu',  array($this, 'mnmwp_menu_section_sc') );

		// Select you menu in location Message
		add_action( 'admin_notices', array( __CLASS__, 'mnmwp_menu_selection_admin_notice' ) );
	}


	/**
	 * Select your menu in location Message.
	 *
	 */
	public static function mnmwp_menu_selection_admin_notice() {
	    if ( ! has_nav_menu( 'mnmwp_register_main_menu' ) ) :
		    echo '<div class="notice notice-success notice-error is-dismissible">
		        <p>Can you please select your menu on the "MNM Header Menu" Location. <a href="'.admin_url( 'nav-menus.php?action=locations' ).'">Click here</a></p>
		    </div>';
	    endif;
	}

	/**
	 * Activation default option.
	 *
	 * @return void
	 */
	public static function mnmwp_active_function() {
		add_option( 'mnmwp-switch', 1 );
	}

	/**
	 * After Activate redirection.
	 *
	 * @return void
	 */
	public static function mnmwp_activation_redirect( $plugin ) {
		if ( plugin_basename( __FILE__ ) === $plugin ) {
			exit( esc_url( wp_safe_redirect( admin_url( 'themes.php?page=multilevel-navigation-menu' ) ) ) );
		}
	}

	/**
	 * Admin Custom Script and CSS assets.
	 *
	 * @return void
	 */
	public static function mnmwp_assets() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'mnmwp-backend-css', plugin_dir_url( __FILE__ ) . 'assets/css/mnmwp-backend.css', array(), '1.0.1', false );
		wp_enqueue_script( 'mnmwp-backend-js', plugin_dir_url( __FILE__ ) . 'assets/js/mnmwp-backend.js', array('wp-color-picker'), '1.0.1', false );
	}

	/**
	 * Frontend Custom Script and CSS assets.
	 *
	 * @return void
	 */
	public static function mnmwp_frontend_assets() {
		wp_enqueue_style( 'mnmwp-frontend-css', plugin_dir_url( __FILE__ ) . 'assets/css/mnmwp-front.css', array(), '1.0.1', false );
		wp_enqueue_script( 'mnmwp-frontend-js', plugin_dir_url( __FILE__ ) . 'assets/js/mnmwp-front.js', array('jquery'), '1.0.1', false );
	}

	/**
	 * Add settings page under the Appearance menu
	 *
	 * @return void
	 */
	public static function mnmwp_add_menu() {
		add_theme_page(
			__( 'Multilevel Navigation Menu', 'multilevel-navigation-menu' ),
			__( 'Multilevel Navigation Menu', 'multilevel-navigation-menu' ),
			'manage_options',
			'multilevel-navigation-menu',
			array( __CLASS__, 'mnmwp_menu_page' ),
			60
		);
	}

	/**
	 * Register New Menu Location
	 *
	 */
	public static function mnmwp_register_menu() {
		add_theme_support( 'nav-menus' );
		register_nav_menu( 'mnmwp_register_main_menu', __( 'MNM Header Menu', 'multilevel-navigation-menu' ) );
	}

	/**
	 * Add setting page link
	 *
	 * @return array
	 */
	public static function mnmwp_add_link( $links ) {
		return array_merge(
			array(
				'settings' => sprintf(
					'<a href="%1$s">%2$s</a>',
					esc_url( admin_url( 'themes.php?page=multilevel-navigation-menu' ) ),
					__( 'Settings', 'multilevel-navigation-menu' )
				),
			),
			$links
		);
	}

	/**
	 * Plugin Pages
	 */
	public static function mnmwp_menu_page() {
		printf( '<div class="wrap">' );
		printf( '<div class="mnmsection">' );
		settings_errors();
		printf( '<form method="post" class="mnmwp-option-page" action="options.php">' );
		settings_fields( 'mnmwp_setting_section' );
		printf( '<div class="mnmwp-head-section"><div class="mnmwp-logo-area"><img src="'.plugin_dir_url( __FILE__ ).'assets/images/mnmwp_logo.png" class="mnmwp_logo" height="50" width="50" title="Multilevel Navigation Menu" /></div>' );
		printf( '<div class="mnmwp-title-area"><h3>Multilevel Navigation Menu</h3></div></div>' );
		do_settings_sections( 'multilevel-navigation-menu' );
		submit_button( __( 'Save', 'multilevel-navigation-menu' ) );
		printf( '</form></div></div>' );
	}

	/**
	 * Display settins with page
	 *
	 * @return void
	 */
	public static function mnmwp_setting_display() {
		/**-- Setting Page Section Title --**/
		add_settings_section( 'mnmwp_setting_section', esc_html__( '', 'multilevel-navigation-menu' ), array( __CLASS__, 'mnmwp_content_callback' ), 'multilevel-navigation-menu' );

		add_settings_field( 'mnmwp-switch', esc_html__( 'Multilevel Navigation Menu', 'multilevel-navigation-menu' ), array( __CLASS__, 'mnmwp_setting_element' ), 'multilevel-navigation-menu', 'mnmwp_setting_section' );
		$mnmwp_switch_args = array(
			'type'              => 'string',
			'sanitize_callback' => array( __CLASS__, 'mnmwp_sanitize_checkbox' ),
			'default'           => 0,
		);
		register_setting( 'mnmwp_setting_section', 'mnmwp-switch', $mnmwp_switch_args );


		$mnmwp_back_color_args = array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '#333333',
		);


		$mnmwp_font_default_color_args = array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '#dddddd',
		);
		
		$mnmwp_font_hover_color_args = array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '#ffffff',
		);

		$mnmwp_font_active_color_args = array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '#ffffff',
		);

		$mnmwp_menu_icon_color = array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '#333333',
		);
		
		$mnmwp_outer_width_args = array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '100%',
		);

		$mnmwp_inner_width_args = array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '100px',
		);

		$mnmwp_mobile_menu_breakpoint = array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '767px',
		);

		

		/** First Level Menu Color Field **/
		add_settings_field( 'mnmwp-first-back-color', esc_html__( 'First Level Menu Background Color', 'multilevel-navigation-menu' ), array( __CLASS__, 'mnmwp_first_background_color' ), 'multilevel-navigation-menu', 'mnmwp_setting_section' );
		register_setting( 'mnmwp_setting_section', 'mnmwp-first-back-color', $mnmwp_back_color_args );
		register_setting( 'mnmwp_setting_section', 'mnmwp-first-back-color-hover', $mnmwp_back_color_args );
		register_setting( 'mnmwp_setting_section', 'mnmwp-first-back-color-active', $mnmwp_back_color_args );

		add_settings_field( 'mnmwp-first-font-color', esc_html__( 'First Level Menu Font Color', 'multilevel-navigation-menu' ), array( __CLASS__, 'mnmwp_first_font_color' ), 'multilevel-navigation-menu', 'mnmwp_setting_section' );
		register_setting( 'mnmwp_setting_section', 'mnmwp-first-font-color', $mnmwp_font_default_color_args );
		register_setting( 'mnmwp_setting_section', 'mnmwp-first-font-color-hover', $mnmwp_font_hover_color_args );
		register_setting( 'mnmwp_setting_section', 'mnmwp-first-font-color-active', $mnmwp_font_active_color_args );

		/** Second Level Menu Color Field **/
		add_settings_field( 'mnmwp-second-back-color', esc_html__( 'Second Level Menu Background Color', 'multilevel-navigation-menu' ), array( __CLASS__, 'mnmwp_second_background_color' ), 'multilevel-navigation-menu', 'mnmwp_setting_section' );
		register_setting( 'mnmwp_setting_section', 'mnmwp-second-back-color', $mnmwp_back_color_args );
		register_setting( 'mnmwp_setting_section', 'mnmwp-second-back-color-hover', $mnmwp_back_color_args );
		register_setting( 'mnmwp_setting_section', 'mnmwp-second-back-color-active', $mnmwp_back_color_args );

		add_settings_field( 'mnmwp-second-font-color', esc_html__( 'Second Level Menu Font Color', 'multilevel-navigation-menu' ), array( __CLASS__, 'mnmwp_second_font_color' ), 'multilevel-navigation-menu', 'mnmwp_setting_section' );
		register_setting( 'mnmwp_setting_section', 'mnmwp-second-font-color', $mnmwp_font_default_color_args );
		register_setting( 'mnmwp_setting_section', 'mnmwp-second-font-color-hover', $mnmwp_font_hover_color_args );
		register_setting( 'mnmwp_setting_section', 'mnmwp-second-font-color-active', $mnmwp_font_active_color_args );

		/** Rest All Level Menu Color Field **/
		add_settings_field( 'mnmwp-rest-back-color', esc_html__( 'Rest All Level Menu Background Color', 'multilevel-navigation-menu' ), array( __CLASS__, 'mnmwp_rest_background_color' ), 'multilevel-navigation-menu', 'mnmwp_setting_section' );
		register_setting( 'mnmwp_setting_section', 'mnmwp-rest-back-color', $mnmwp_back_color_args );
		register_setting( 'mnmwp_setting_section', 'mnmwp-rest-back-color-hover', $mnmwp_back_color_args );
		register_setting( 'mnmwp_setting_section', 'mnmwp-rest-back-color-active', $mnmwp_back_color_args );

		add_settings_field( 'mnmwp-rest-font-color', esc_html__( 'Rest All Level Menu Font Color', 'multilevel-navigation-menu' ), array( __CLASS__, 'mnmwp_rest_font_color' ), 'multilevel-navigation-menu', 'mnmwp_setting_section' );
		register_setting( 'mnmwp_setting_section', 'mnmwp-rest-font-color', $mnmwp_font_default_color_args );
		register_setting( 'mnmwp_setting_section', 'mnmwp-rest-font-color-hover', $mnmwp_font_hover_color_args );
		register_setting( 'mnmwp_setting_section', 'mnmwp-rest-font-color-active', $mnmwp_font_active_color_args );

		/** MNM Mobile Menu Icon  **/
		add_settings_field( 'mnmwp-menu-icon-color', esc_html__( 'MNM Mobile Menu Icon Color', 'multilevel-navigation-menu' ), array( __CLASS__, 'mnmwp_menu_icon_color' ), 'multilevel-navigation-menu', 'mnmwp_setting_section' );
		register_setting( 'mnmwp_setting_section', 'mnmwp-menu-icon-color', $mnmwp_menu_icon_color );

		/** Navigation Menu Outer Width **/
		add_settings_field( 'mnmwp-menu-outer-width', esc_html__( 'Navigation Menu Outer Width', 'multilevel-navigation-menu' ), array( __CLASS__, 'mnmwp_menu_outer_width' ), 'multilevel-navigation-menu', 'mnmwp_setting_section' );
		register_setting( 'mnmwp_setting_section', 'mnmwp-menu-outer-width', $mnmwp_outer_width_args );

		/** Navigation Menu Inner Width **/
		add_settings_field( 'mnmwp-menu-inner-width', esc_html__( 'Navigation Menu Inner Container Left Right Space (Padding)', 'multilevel-navigation-menu' ), array( __CLASS__, 'mnmwp_menu_inner_width' ), 'multilevel-navigation-menu', 'mnmwp_setting_section' );
		register_setting( 'mnmwp_setting_section', 'mnmwp-menu-inner-width', $mnmwp_inner_width_args );

		/** Mobile Menu Breakpoint **/
		add_settings_field( 'mnmwp-mobile-menu-breakpoint', esc_html__( 'Mobile Menu Breakpoint', 'multilevel-navigation-menu' ), array( __CLASS__, 'mnmwp_mobile_menu_breakpoint' ), 'multilevel-navigation-menu', 'mnmwp_setting_section' );
		register_setting( 'mnmwp_setting_section', 'mnmwp-mobile-menu-breakpoint', $mnmwp_mobile_menu_breakpoint );

		/** MNM Menu Shortcode **/
		add_settings_field( 'mnmwp-menu-shortcode', esc_html__( 'MNM Menu Shortcode', 'multilevel-navigation-menu' ), array( __CLASS__, 'mnmwp_menu_shortcode' ), 'multilevel-navigation-menu', 'mnmwp_setting_section' );
		register_setting( 'mnmwp_setting_section', 'mnmwp-menu-shortcode', $mnmwp_menu_shortcode );
		
	}

	/**
	 * Setting page description.
	 *
	 * @return void
	 */
	public static function mnmwp_content_callback() {
		esc_html_e( '', 'multilevel-navigation-menu' );
	}

	/**
	 * Add Color field.
	 *
	 * @return void
	 */

	/** First Level Menu Color Field Function **/
	public static function mnmwp_first_background_color() {
		printf( '<input type="text" name="mnmwp-first-back-color" id="mnmwp-first-back-color" class="mnmwp-menu-color" value="%1$s" /><p class="description" id="tagline-description">Default</p>', esc_html( get_option( 'mnmwp-first-back-color' ) ) );
		printf( '<input type="text" name="mnmwp-first-back-color-hover" id="mnmwp-first-back-color-hover" class="mnmwp-menu-color" value="%1$s" /><p class="description" id="tagline-description">On Hover</p>', esc_html( get_option( 'mnmwp-first-back-color-hover' ) ) );
		printf( '<input type="text" name="mnmwp-first-back-color-active" id="mnmwp-first-back-color-active" class="mnmwp-menu-color" value="%1$s" /><p class="description" id="tagline-description">On Active</p>', esc_html( get_option( 'mnmwp-first-back-color-active' ) ) );
	}
	public static function mnmwp_first_font_color() {
		printf( '<input type="text" name="mnmwp-first-font-color" id="mnmwp-first-font-color" class="mnmwp-menu-color" value="%1$s" /><p class="description" id="tagline-description">Default</p>', esc_html( get_option( 'mnmwp-first-font-color' ) ) );
		printf( '<input type="text" name="mnmwp-first-font-color-hover" id="mnmwp-first-font-color-hover" class="mnmwp-menu-color" value="%1$s" /><p class="description" id="tagline-description">On Hover</p>', esc_html( get_option( 'mnmwp-first-font-color-hover' ) ) );
		printf( '<input type="text" name="mnmwp-first-font-color-active" id="mnmwp-first-font-color-active" class="mnmwp-menu-color" value="%1$s" /><p class="description" id="tagline-description">On Active</p>', esc_html( get_option( 'mnmwp-first-font-color-active' ) ) );
	}

	/** Second Level Menu Color Field Function **/
	public static function mnmwp_second_background_color() {
		printf( '<input type="text" name="mnmwp-second-back-color" id="mnmwp-second-back-color" class="mnmwp-menu-color" value="%1$s" /><p class="description" id="tagline-description">Default</p>', esc_html( get_option( 'mnmwp-second-back-color' ) ) );
		printf( '<input type="text" name="mnmwp-second-back-color-hover" id="mnmwp-second-back-color-hover" class="mnmwp-menu-color" value="%1$s" /><p class="description" id="tagline-description">On Hover</p>', esc_html( get_option( 'mnmwp-second-back-color-hover' ) ) );
		printf( '<input type="text" name="mnmwp-second-back-color-active" id="mnmwp-second-back-color-active" class="mnmwp-menu-color" value="%1$s" /><p class="description" id="tagline-description">On Active</p>', esc_html( get_option( 'mnmwp-second-back-color-active' ) ) );
	}
	public static function mnmwp_second_font_color() {
		printf( '<input type="text" name="mnmwp-second-font-color" id="mnmwp-second-font-color" class="mnmwp-menu-color" value="%1$s" /><p class="description" id="tagline-description">Default</p>', esc_html( get_option( 'mnmwp-second-font-color' ) ) );
		printf( '<input type="text" name="mnmwp-second-font-color-hover" id="mnmwp-second-font-color-hover" class="mnmwp-menu-color" value="%1$s" /><p class="description" id="tagline-description">On Hover</p>', esc_html( get_option( 'mnmwp-second-font-color-hover' ) ) );
		printf( '<input type="text" name="mnmwp-second-font-color-active" id="mnmwp-second-font-color-active" class="mnmwp-menu-color" value="%1$s" /><p class="description" id="tagline-description">On Active</p>', esc_html( get_option( 'mnmwp-second-font-color-active' ) ) );
	}

	/** Rest All Level Menu Color Field Function **/
	public static function mnmwp_rest_background_color() {
		printf( '<input type="text" name="mnmwp-rest-back-color" id="mnmwp-rest-back-color" class="mnmwp-menu-color" value="%1$s" /><p class="description" id="tagline-description">Default</p>', esc_html( get_option( 'mnmwp-rest-back-color' ) ) );
		printf( '<input type="text" name="mnmwp-rest-back-color-hover" id="mnmwp-rest-back-color-hover" class="mnmwp-menu-color" value="%1$s" /><p class="description" id="tagline-description">On Hover</p>', esc_html( get_option( 'mnmwp-rest-back-color-hover' ) ) );
		printf( '<input type="text" name="mnmwp-rest-back-color-active" id="mnmwp-rest-back-color-active" class="mnmwp-menu-color" value="%1$s" /><p class="description" id="tagline-description">On Active</p>', esc_html( get_option( 'mnmwp-rest-back-color-active' ) ) );
	}
	public static function mnmwp_rest_font_color() {
		printf( '<input type="text" name="mnmwp-rest-font-color" id="mnmwp-rest-font-color" class="mnmwp-menu-color" value="%1$s" /><p class="description" id="tagline-description">Default</p>', esc_html( get_option( 'mnmwp-rest-font-color' ) ) );
		printf( '<input type="text" name="mnmwp-rest-font-color-hover" id="mnmwp-rest-font-color-hover" class="mnmwp-menu-color" value="%1$s" /><p class="description" id="tagline-description">On Hover</p>', esc_html( get_option( 'mnmwp-rest-font-color-hover' ) ) );
		printf( '<input type="text" name="mnmwp-rest-font-color-active" id="mnmwp-rest-font-color-active" class="mnmwp-menu-color" value="%1$s" /><p class="description" id="tagline-description">On Active</p>', esc_html( get_option( 'mnmwp-rest-font-color-active' ) ) );
	}

	/** MNM Menu Icon Field **/
	public static function mnmwp_menu_icon_color() {
		printf( '<input type="text" name="mnmwp-menu-icon-color" id="mnmwp-menu-icon-color" class="mnmwp-menu-color" value="%1$s" /><p class="description" id="tagline-description">Mobile Menu Icon Color (Default set is #333333)</p>', esc_html( get_option( 'mnmwp-menu-icon-color' ) ) );
	}

	/** Navigation Menu Outer Width Function **/
	public static function mnmwp_menu_outer_width() {
		printf( '<input type="text" name="mnmwp-menu-outer-width" id="mnmwp-menu-outer-width" class="mnmwp-menu-width" value="%1$s" /><span class="description" id="mnmwp-description">
		Insert Width with "px" or "%%". (Default set is 100%%)</span>', esc_html( get_option( 'mnmwp-menu-outer-width' ) ) );
	}

	/** Navigation Menu Inner Width Function **/
	public static function mnmwp_menu_inner_width() {
		printf( '<input type="text" name="mnmwp-menu-inner-width" id="mnmwp-menu-inner-width" class="mnmwp-menu-width" value="%1$s" /><span class="description" id="mnmwp-description">
		Insert Width with "px" or "%%". (Default set is 100px)</span>', esc_html( get_option( 'mnmwp-menu-inner-width' ) ) );
	}

	/** Mobile Menu Breakpoint Function **/
	public static function mnmwp_mobile_menu_breakpoint() {
		printf( '<input type="text" name="mnmwp-mobile-menu-breakpoint" id="mnmwp-mobile-menu-breakpoint" class="mnmwp-mobile-menu-breakpoint" value="%1$s" /><span class="description" id="mnmwp-description">
		Insert Breakpoint with "px". (Default set is 767px)</span>', esc_html( get_option( 'mnmwp-mobile-menu-breakpoint' ) ) );
	}

	/** MNM Menu Shortcode Field **/
	public static function mnmwp_menu_shortcode() {
		printf( '<input type="text" name="mnmwp-menu-shortcode" id="mnmwp-menu-shortcode" class="mnmwp-menu-shortcode" value="&lt;?php echo do_shortcode(&apos;[multilevel_navigation_menu]&apos;); ?&gt;" readonly /><span class="description" id="mnmwp-description">
		This is your shortcode.</span>', esc_html( get_option( 'mnmwp-menu-shortcode' ) ) );
	}

	

	/**
	 * Callback for enable.
	 *
	 * @return void
	 */
	public static function mnmwp_setting_element() {
		$mnmwp_enable = get_option( 'mnmwp-switch' );
		printf( '<label class="switch"><input type="checkbox" name="mnmwp-switch" id="mnmwp-switch" value="1" %1$s /><span class="slider round"></span></label><p class="description" id="tagline-description">Enable Or Disable?</p>', ( ( '0' !== $mnmwp_enable ) ? ( esc_attr( 'checked' ) ) : '' ) );
	}

	/**
	 * Checkbox value.
	 *
	 * @return integer
	 */
	public static function mnmwp_sanitize_checkbox( $input ) {
		return ( '1' !== $input ) ? 0 : 1;
	}

	/**
	 * Shortcode of Multilevel Navigation Menu plugin.
	 *
	 */
	public static function mnmwp_menu_section_sc($atts){
		$mnmwp_menu_switch = get_option( 'mnmwp-switch' );

		$mnmwp_menu_default_back_color = '#333333';
		$mnmwp_menu_default_font_color = '#dddddd';

		$mnmwp_menu_default_back_color_hover = '#333333';
		$mnmwp_menu_default_font_color_hover = '#ffffff';

		$mnmwp_menu_default_back_color_active = '#333333';
		$mnmwp_menu_default_font_color_active = '#ffffff';

		$mnmwp_menu_default_icon_color = '#333333';

		$mnmwp_menu_default_outer_width = '100%';
		$mnmwp_menu_default_inner_width = '100px';

		$mnmwp_mobile_menu_default_breakpoint = '767px';

		

		/** First Level Menu Color **/
		/*-- Background Color (Default)--*/
		$mnmwp_first_back_color = get_option( 'mnmwp-first-back-color' );
		if(empty($mnmwp_first_back_color)) $mnmwp_first_back_color = $mnmwp_menu_default_back_color;
		/*-- Background Color (On Hover)--*/
		$mnmwp_first_back_color_hover = get_option( 'mnmwp-first-back-color-hover' );
		if(empty($mnmwp_first_back_color_hover)) $mnmwp_first_back_color_hover = $mnmwp_menu_default_back_color_hover;
		/*-- Background Color (On Active)--*/
		$mnmwp_first_back_color_active = get_option( 'mnmwp-first-back-color-active' );
		if(empty($mnmwp_first_back_color_active)) $mnmwp_first_back_color_active = $mnmwp_menu_default_back_color_active;

		/*-- Font Color (Default)--*/
		$mnmwp_first_font_color = get_option( 'mnmwp-first-font-color' );
		if(empty($mnmwp_first_font_color)) $mnmwp_first_font_color = $mnmwp_menu_default_font_color;
		/*-- Font Color (On Hover)--*/
		$mnmwp_first_font_color_hover = get_option( 'mnmwp-first-font-color-hover' );
		if(empty($mnmwp_first_font_color_hover)) $mnmwp_first_font_color_hover = $mnmwp_menu_default_font_color_hover;
		/*-- Font Color (On Active)--*/
		$mnmwp_first_font_color_active = get_option( 'mnmwp-first-font-color-active' );
		if(empty($mnmwp_first_font_color_active)) $mnmwp_first_font_color_active = $mnmwp_menu_default_font_color_active;

		/** Second Level Menu Color Get **/
		/*-- Background Color (Default)--*/
		$mnmwp_second_back_color = get_option( 'mnmwp-second-back-color' );
		if(empty($mnmwp_second_back_color)) $mnmwp_second_back_color = $mnmwp_menu_default_back_color;
		/*-- Background Color (On Hover)--*/
		$mnmwp_second_back_color_hover = get_option( 'mnmwp-second-back-color-hover' );
		if(empty($mnmwp_second_back_color_hover)) $mnmwp_second_back_color_hover = $mnmwp_menu_default_back_color_hover;
		/*-- Background Color (On Active)--*/
		$mnmwp_second_back_color_active = get_option( 'mnmwp-second-back-color-active' );
		if(empty($mnmwp_second_back_color_active)) $mnmwp_second_back_color_active = $mnmwp_menu_default_back_color_active;

		/*-- Font Color (Default)--*/
		$mnmwp_second_font_color = get_option( 'mnmwp-second-font-color' );
		if(empty($mnmwp_second_font_color)) $mnmwp_second_font_color = $mnmwp_menu_default_font_color;
		/*-- Font Color (On Hover)--*/
		$mnmwp_second_font_color_hover = get_option( 'mnmwp-second-font-color-hover' );
		if(empty($mnmwp_second_font_color_hover)) $mnmwp_second_font_color_hover = $mnmwp_menu_default_font_color_hover;
		/*-- Font Color (On Active)--*/
		$mnmwp_second_font_color_active = get_option( 'mnmwp-second-font-color-active' );
		if(empty($mnmwp_second_font_color_active)) $mnmwp_second_font_color_active = $mnmwp_menu_default_font_color_active;

		/** Rest All Level Menu Color Get **/
		/*-- Background Color (Default)--*/
		$mnmwp_rest_back_color = get_option( 'mnmwp-rest-back-color' );
		if(empty($mnmwp_rest_back_color)) $mnmwp_rest_back_color = $mnmwp_menu_default_back_color;
		/*-- Background Color (On Hover)--*/
		$mnmwp_rest_back_color_hover = get_option( 'mnmwp-rest-back-color-hover' );
		if(empty($mnmwp_rest_back_color_hover)) $mnmwp_rest_back_color_hover = $mnmwp_menu_default_back_color_hover;
		/*-- Background Color (On Active)--*/
		$mnmwp_rest_back_color_active = get_option( 'mnmwp-rest-back-color-active' );
		if(empty($mnmwp_rest_back_color_active)) $mnmwp_rest_back_color_active = $mnmwp_menu_default_back_color_active;

		/*-- Font Color (Default)--*/
		$mnmwp_rest_font_color = get_option( 'mnmwp-rest-font-color' );
		if(empty($mnmwp_rest_font_color)) $mnmwp_rest_font_color = $mnmwp_menu_default_font_color;
		/*-- Font Color (On Hover)--*/
		$mnmwp_rest_font_color_hover = get_option( 'mnmwp-rest-font-color-hover' );
		if(empty($mnmwp_rest_font_color_hover)) $mnmwp_rest_font_color_hover = $mnmwp_menu_default_font_color_hover;
		/*-- Font Color (On Active)--*/
		$mnmwp_rest_font_color_active = get_option( 'mnmwp-rest-font-color-active' );
		if(empty($mnmwp_rest_font_color_active)) $mnmwp_rest_font_color_active = $mnmwp_menu_default_font_color_active;

		/** MNM Menu Icon Color **/
		$mnmwp_menu_icon_color = get_option( 'mnmwp-menu-icon-color' );
		if(empty($mnmwp_menu_icon_color)) $mnmwp_menu_icon_color = $mnmwp_menu_default_icon_color;

		/** Navigation Menu Outer Width Get **/
		$mnmwp_menu_outer_width = get_option( 'mnmwp-menu-outer-width' );
		if(empty($mnmwp_menu_outer_width)) $mnmwp_menu_outer_width = $mnmwp_menu_default_back_color;
		
		/** Navigation Menu Inner Width Get **/
		$mnmwp_menu_inner_width = get_option( 'mnmwp-menu-inner-width' );
		if(empty($mnmwp_menu_inner_width)) $mnmwp_menu_inner_width = $mnmwp_menu_default_font_color;

		/** Mobile Menu Breakpoint **/
		$mnmwp_mobile_menu_breakpoint = get_option( 'mnmwp-mobile-menu-breakpoint' );
		if(empty($mnmwp_mobile_menu_breakpoint)) $mnmwp_mobile_menu_breakpoint = $mnmwp_mobile_menu_default_breakpoint;

		
		if ( '0' !== $mnmwp_menu_switch ) {
		    ob_start();
		    if ( has_nav_menu( 'mnmwp_register_main_menu' ) ) :
				wp_nav_menu(
					array(
						'theme_location'  => 'mnmwp_register_main_menu',
						'menu_class'      => 'mnmwp-menu',
						'container_class' => 'mnmwp-menu-nav',
						'container_id'    => 'mnmwp-main-menu',
						'link_before'     => '',
						'link_after'      => '',
						'items_wrap'      => '<ul id="%1$s" class="%2$s" tabindex="0">%3$s</ul>',
					)
				);

				?>
				<script type="text/javascript">
					(function($) {
						function mnmwp_window_resize() {
					        if ($(window).width() < <?php echo chop($mnmwp_mobile_menu_breakpoint,"px"); ?>) {
					        	$('html').addClass('is_mobile');
					        }
					        else {
					        	$('html').removeClass('is_mobile');
					        }
					    }
					    $(document).ready( function() {
					        $(window).resize(mnmwp_window_resize);
					        mnmwp_window_resize();
					    });
					})(jQuery);   
				</script>

				<style>
					/*--- For Desktop ---*/
					/*-- First Level --*/
					#mnmwp-main-menu {background: <?php echo $mnmwp_first_back_color; ?>; width: <?php echo $mnmwp_menu_outer_width; ?>; padding: 0 <?php echo $mnmwp_menu_inner_width; ?>;}
					#mnmwp-main-menu > ul > li{background: <?php echo $mnmwp_first_back_color; ?>;}
					#mnmwp-main-menu > ul > li:hover{background: <?php echo $mnmwp_first_back_color_hover; ?>;}
					#mnmwp-main-menu ul li a {color: <?php echo $mnmwp_first_font_color; ?>;}
					#mnmwp-main-menu ul li a:hover {color: <?php echo $mnmwp_first_font_color_hover; ?>;}
					#mnmwp-main-menu > ul > li.current-menu-item, #mnmwp-main-menu > ul > li.current-menu-ancestor {background: <?php echo $mnmwp_first_back_color_active; ?>;}
					#mnmwp-main-menu > ul > li.current-menu-item > a, #mnmwp-main-menu > ul > li.current-menu-ancestor > a{color: <?php echo $mnmwp_first_font_color_active; ?>;}

					/*-- Icon Color --*/
					#mnmwp-main-menu ul li.has-sub > a:after, #mnmwp-main-menu ul li.has-sub > a:before {background: <?php echo $mnmwp_first_font_color; ?>;}
					#mnmwp-main-menu ul li.has-sub:hover > a:after, #mnmwp-main-menu ul li.has-sub:hover > a:before {background: <?php echo $mnmwp_first_font_color_hover; ?>;}
					#mnmwp-main-menu ul li.current-menu-item.has-sub > a:after, 
					#mnmwp-main-menu ul li.current-menu-item.has-sub > a:before, 
					#mnmwp-main-menu ul li.current-menu-item.has-sub:hover > a:after, 
					#mnmwp-main-menu ul li.current-menu-item.has-sub:hover > a:before, 
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub > a:after, 
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub > a:before
					{background: <?php echo $mnmwp_first_font_color_active; ?>;}

					/*-- Second Level --*/
					#mnmwp-main-menu ul ul li a{background: <?php echo $mnmwp_second_back_color; ?>;color: <?php echo $mnmwp_second_font_color; ?>;}
					#mnmwp-main-menu ul ul > li:hover > a {background: <?php echo $mnmwp_second_back_color_hover; ?>;color: <?php echo $mnmwp_second_font_color_hover; ?>;}
					
					#mnmwp-main-menu ul ul li.current-menu-item > a, #mnmwp-main-menu ul li.current-menu-ancestor ul li.current-menu-ancestor > a{background: <?php echo $mnmwp_second_back_color_active; ?>;color: <?php echo $mnmwp_second_font_color_active; ?>;}

					/*-- Icon Color --*/
					#mnmwp-main-menu ul ul li.has-sub > a:after, #mnmwp-main-menu ul ul li.has-sub > a:before {background: <?php echo $mnmwp_second_font_color; ?>;}
					#mnmwp-main-menu ul ul li.has-sub:hover > a:after, 
					#mnmwp-main-menu ul ul li.has-sub:hover > a:before {background: <?php echo $mnmwp_second_font_color_hover; ?>;}
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub > a:after, 
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub > a:before, 
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub:hover > a:after, 
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub:hover > a:before
					{background: <?php echo $mnmwp_second_font_color_active; ?>;}

					
					/*-- Rest All Level --*/
					#mnmwp-main-menu ul ul ul li a{background: <?php echo $mnmwp_rest_back_color; ?>;color: <?php echo $mnmwp_rest_font_color; ?>;}
					#mnmwp-main-menu ul ul ul li:hover > a {background: <?php echo $mnmwp_rest_back_color_hover; ?>;color: <?php echo $mnmwp_rest_font_color_hover; ?>;}
					#mnmwp-main-menu ul ul ul li.current-menu-item > a, #mnmwp-main-menu ul li.current-menu-ancestor ul li.current-menu-ancestor ul li.current-menu-ancestor.has-sub > a,
					#mnmwp-main-menu ul ul ul li.current-menu-item > a, #mnmwp-main-menu ul li.current-menu-ancestor ul li.current-menu-ancestor ul li.current-menu-item.has-sub > a
					{background: <?php echo $mnmwp_rest_back_color_active; ?>;color: <?php echo $mnmwp_rest_font_color_active; ?>;}

					#mnmwp-main-menu ul ul ul li.current-menu-item > a, #mnmwp-main-menu ul li.current-menu-ancestor ul li.current-menu-ancestor ul li.current-menu-ancestor ul li.current-menu-ancestor.has-sub > a,
					#mnmwp-main-menu ul ul ul li.current-menu-item > a, #mnmwp-main-menu ul li.current-menu-ancestor ul li.current-menu-ancestor ul li.current-menu-ancestor ul li.current-menu-item.has-sub > a
					{background: <?php echo $mnmwp_rest_back_color_active; ?>;color: <?php echo $mnmwp_rest_font_color_active; ?>;}
					#mnmwp-main-menu ul ul ul li.current-menu-item > a, #mnmwp-main-menu ul li.current-menu-ancestor ul li.current-menu-ancestor ul li.current-menu-ancestor ul li.current-menu-ancestor ul li.current-menu-item.has-sub > a
					{background: <?php echo $mnmwp_rest_back_color_active; ?>;color: <?php echo $mnmwp_rest_font_color_active; ?>;}

					/*-- Icon Color --*/
					#mnmwp-main-menu ul ul ul li.has-sub > a:after, #mnmwp-main-menu ul ul ul li.has-sub > a:before {background: <?php echo $mnmwp_rest_font_color; ?>;}
					#mnmwp-main-menu ul ul ul li.has-sub:hover > a:after, #mnmwp-main-menu ul ul ul li.has-sub:hover > a:before {background: <?php echo $mnmwp_rest_font_color_hover; ?>;}
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub a:after, 
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub a:before, 
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub:hover a:after, 
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub:hover a:before
					{background: <?php echo $mnmwp_rest_font_color_active; ?>;}
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.has-sub > a:after, 
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.has-sub > a:before 
					{background: <?php echo $mnmwp_rest_font_color; ?>;}
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub > a:after, 
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub > a:before 
					{background: <?php echo $mnmwp_rest_font_color_active; ?>;}
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub:hover > a:after, 
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub:hover > a:before 
					{background: <?php echo $mnmwp_rest_font_color_active; ?>;}
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.has-sub:hover > a:after, 
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.has-sub:hover > a:before
					{background: <?php echo $mnmwp_rest_font_color_hover; ?>;}
					#mnmwp-main-menu > ul > li.current-menu-ancestor.has-sub ul > li.current-menu-ancestor.has-sub ul > li.current-menu-item.has-sub > a:after, 
					#mnmwp-main-menu > ul > li.current-menu-ancestor.has-sub ul > li.current-menu-ancestor.has-sub ul > li.current-menu-item.has-sub > a:before 
					{background: <?php echo $mnmwp_rest_font_color_active; ?>;}
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-item.has-sub > a:after, 
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-item.has-sub > a:before 
					{background: <?php echo $mnmwp_rest_font_color_active; ?>;}
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-item.has-sub:hover > a:after, 
					#mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-item.has-sub:hover > a:before
					{background: <?php echo $mnmwp_rest_font_color_active; ?>;}
					
					/*--- For Mobile ---*/
					/*-- First Level --*/
					.is_mobile #mnmwp-main-menu #mnm-menu-button .btn.menu-btn span, 
					.is_mobile #mnmwp-main-menu ul li .mnm-submenu-button:before, 
					.is_mobile #mnmwp-main-menu ul li .mnm-submenu-button:after{background: <?php echo $mnmwp_first_font_color; ?>;}
					.is_mobile #mnmwp-main-menu > ul > li:hover {background: none;}
					.is_mobile #mnmwp-main-menu ul li:hover > a{background: <?php echo $mnmwp_first_back_color_hover; ?>;color: <?php echo $mnmwp_first_font_color_hover; ?>;}

					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor:hover > a, 
					.is_mobile #mnmwp-main-menu ul li.current-menu-item:hover > a {background: <?php echo $mnmwp_first_back_color_active; ?>;color: <?php echo $mnmwp_first_font_color_active; ?>;}
					
					/*-- Second Level --*/
					.is_mobile #mnmwp-main-menu ul ul li:hover > a{background: <?php echo $mnmwp_second_back_color_hover; ?>;color: <?php echo $mnmwp_second_font_color_hover; ?>;}
					.is_mobile #mnmwp-main-menu ul ul li.current-menu-ancestor:hover > a, 
					.is_mobile #mnmwp-main-menu ul ul li.current-menu-item:hover > a{background: <?php echo $mnmwp_second_back_color_active; ?>;color: <?php echo $mnmwp_second_font_color_active; ?>;}
					
					/*-- Rest All Level --*/
					.is_mobile #mnmwp-main-menu ul ul ul li:hover > a{background: <?php echo $mnmwp_rest_back_color_hover; ?>;color: <?php echo $mnmwp_rest_font_color_hover; ?>;}
					.is_mobile #mnmwp-main-menu ul ul ul li.current-menu-ancestor:hover > a,
					.is_mobile #mnmwp-main-menu ul ul ul li.current-menu-item:hover > a
					{background: <?php echo $mnmwp_rest_back_color_active; ?>;color: <?php echo $mnmwp_rest_font_color_active; ?>;}
					

					/*-- Icon CSS --*/
					.is_mobile #mnmwp-main-menu ul > li.has-sub > .mnm-submenu-button.mnm-submenu-opened:before, 
					.is_mobile #mnmwp-main-menu ul > li.has-sub > .mnm-submenu-button.mnm-submenu-opened:after,
					.is_mobile #mnmwp-main-menu ul > li.has-sub > .mnm-submenu-opened.mnm-submenu-opened:before,
					.is_mobile #mnmwp-main-menu ul > li.has-sub:hover > .mnm-submenu-opened:after{background:<?php echo $mnmwp_first_font_color_hover; ?>;}
					.is_mobile #mnmwp-main-menu ul ul li .mnm-submenu-button:before, 
					.is_mobile #mnmwp-main-menu ul ul li .mnm-submenu-button:after{background: <?php echo $mnmwp_second_font_color; ?>;}
					.is_mobile #mnmwp-main-menu ul ul > li.has-sub > .mnm-submenu-opened.mnm-submenu-opened:before,
					.is_mobile #mnmwp-main-menu ul ul > li.has-sub > .mnm-submenu-opened.mnm-submenu-opened:after {background: <?php echo $mnmwp_second_font_color_hover; ?>;}
					.is_mobile #mnmwp-main-menu ul ul ul li .mnm-submenu-button:before, 
					.is_mobile #mnmwp-main-menu ul ul ul li .mnm-submenu-button:after{background: <?php echo $mnmwp_rest_font_color; ?>;}
					.is_mobile #mnmwp-main-menu ul ul ul > li.has-sub > .mnm-submenu-opened.mnm-submenu-opened:before,
					.is_mobile #mnmwp-main-menu ul ul ul > li.has-sub > .mnm-submenu-opened.mnm-submenu-opened:after {background: <?php echo $mnmwp_rest_font_color_hover; ?>;}


					/*- First Level Icon -*/
					.is_mobile #mnmwp-main-menu ul li.current-menu-item.has-sub > .mnm-submenu-button:after, 
					.is_mobile #mnmwp-main-menu ul li.current-menu-item.has-sub > .mnm-submenu-button:before, 
					.is_mobile #mnmwp-main-menu ul li.current-menu-item.has-sub > .mnm-submenu-button.mnm-submenu-opened:after, 
					.is_mobile #mnmwp-main-menu ul li.current-menu-item.has-sub > .mnm-submenu-button.mnm-submenu-opened:before, 
					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub > .mnm-submenu-button:after, 
					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub > .mnm-submenu-button:before
					{background: <?php echo $mnmwp_first_font_color_active; ?>;}
					/*- Second Level Icon -*/
					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub > .mnm-submenu-button:after, 
					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub > .mnm-submenu-button:before, 
					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub > .mnm-submenu-button.mnm-submenu-opened:after, 
					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub > .mnm-submenu-button.mnm-submenu-opened:before
					{background: <?php echo $mnmwp_second_font_color_active; ?>;}
					/*- Rest Level Icon -*/

					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub .mnm-submenu-button:after, 
					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub .mnm-submenu-button:before
					{background: <?php echo $mnmwp_rest_font_color_active; ?>;}
					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub > .mnm-submenu-button.mnm-submenu-opened:after, 
					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub > .mnm-submenu-button.mnm-submenu-opened:before
					{background: <?php echo $mnmwp_rest_font_color_active; ?>;}


					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.has-sub > .mnm-submenu-button:after, 
					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.has-sub > .mnm-submenu-button:before 
					{background: <?php echo $mnmwp_rest_font_color; ?>;}
					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub > .mnm-submenu-button:after, 
					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub > .mnm-submenu-button:before 
					{background: <?php echo $mnmwp_rest_font_color_active; ?>;}

					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub > .mnm-submenu-button.mnm-submenu-opened:after, 
					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub > .mnm-submenu-button.mnm-submenu-opened:before 
					{background: <?php echo $mnmwp_rest_font_color_active; ?>;}
					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.has-sub > .mnm-submenu-button.mnm-submenu-opened:after, 
					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.has-sub > .mnm-submenu-button.mnm-submenu-opened:before
					{background: <?php echo $mnmwp_rest_font_color_hover; ?>;}

					.is_mobile #mnmwp-main-menu > ul > li.current-menu-ancestor.has-sub ul > li.current-menu-ancestor.has-sub ul > li.current-menu-item.has-sub > .mnm-submenu-button:after, 
					.is_mobile #mnmwp-main-menu > ul > li.current-menu-ancestor.has-sub ul > li.current-menu-ancestor.has-sub ul > li.current-menu-item.has-sub > .mnm-submenu-button:before 
					{background: <?php echo $mnmwp_rest_font_color_active; ?>;}
					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-item.has-sub > .mnm-submenu-button:after, 
					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-item.has-sub > .mnm-submenu-button:before 
					{background: <?php echo $mnmwp_rest_font_color_active; ?>;}

					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-item.has-sub > .mnm-submenu-button.mnm-submenu-opened:after, 
					.is_mobile #mnmwp-main-menu ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-ancestor.has-sub ul li.current-menu-item.has-sub > .mnm-submenu-button.mnm-submenu-opened:before
					{background: <?php echo $mnmwp_rest_font_color_active; ?>;}

					.is_mobile #mnmwp-main-menu #mnm-menu-button button.btn.menu-btn span{background: <?php echo $mnmwp_menu_icon_color; ?>;}

				</style>
				<?php
			else:
				echo '<style>.mnmwp_no_menu_selected {width: 100%;padding: 15px '.$mnmwp_menu_inner_width.';display: flex;}</style>'; 
				printf( '<span class="mnmwp_no_menu_selected">Can you please select your menu on the "MNM Header Menu" Location.</span>' );
			endif;
		}
	}
}

new Multilevel_Navigation_Menu_WP();