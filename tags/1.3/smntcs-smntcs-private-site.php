<?php
/**
 * Plugin Name: SMNTCS Private Site
 * Plugin URI: https://github.com/nielslange/smntcs-private-site
 * Description: Allow only logged in users to access the site
 * Author: Niels Lange <info@nielslange.de>
 * Author URI: https://nielslange.de
 * Text Domain: smntcs-private-site
 * Domain Path: /languages/
 * Version: 1.3
 * Requires at least: 3.4
 * Requires PHP: 5.6
 * Tested up to: 5.2
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage SMNTCS Private Site
 * @author     Niels Lange <info@nielslange.de>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */

// Avoid direct plugin access
if ( ! defined( 'ABSPATH' ) ) { die( '¯\_(ツ)_/¯' );
}

// Load text domain
add_action( 'plugins_loaded', 'smntcs_ps_plugins_loaded' );
function smntcs_ps_plugins_loaded() {
	load_plugin_textdomain( 'smntcs-private-site', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

// Add settings link on plugin page
add_filter( "plugin_action_links_" . plugin_basename( __FILE__ ), 'smntcs_ps_plugin_settings_link' );
function smntcs_ps_plugin_settings_link( $links ) {
	$admin_url    = admin_url( 'customize.php?autofocus[control]=smntcs_ps_enable' );
	$settings_url = sprintf( '<a href="%s">%s</a>', $admin_url, __( 'Settings', 'smntcs-private-site' ) );
	array_unshift( $links, $settings_url );

	return $links;
}

// Enhance customizer
add_action( 'customize_register', 'smntcs_ps_register_customize' );
function smntcs_ps_register_customize( $wp_customize ) {
	$wp_customize->add_section( 'smntcs_ps_section', array(
		'priority' => 500,
		'title'    => __( 'Private Site', 'smntcs-private-site' ),
	) );

	/* Enable private site ******************************************************/

	$wp_customize->add_setting( 'smntcs_ps_enable', array(
		'capability' => 'edit_theme_options',
		'default'    => false,
		'type'       => 'option',
	) );

	$wp_customize->add_control( 'smntcs_ps_enable', array(
		'label'   => __( 'Enable Private Site', 'smntcs-private-site' ),
		'section' => 'smntcs_ps_section',
		'type'    => 'checkbox',
	) );

	/* Enable login button ******************************************************/

	$wp_customize->add_setting( 'smntcs_ps_login_button', array(
		'capability' => 'edit_theme_options',
		'default'    => false,
		'type'       => 'option',
	) );

	$wp_customize->add_control( 'smntcs_ps_login_button', array(
		'label'   => __( 'Show login button', 'smntcs-private-site' ),
		'section' => 'smntcs_ps_section',
		'type'    => 'checkbox',
	) );

	/* Add custom message *******************************************************/

	$wp_customize->add_setting( 'smntcs_ps_message', array(
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'type'              => 'option',
	) );

	$wp_customize->add_control( 'smntcs_ps_message', array(
		'label'       => __( 'Message', 'smntcs-private-site' ),
		'section'     => 'smntcs_ps_section',
		'type'        => 'textarea',
		'input_attrs' => array(
			'placeholder' => __( 'This site is marked private by its owner.', 'smntcs-private-site' ),
		)
	) );

	/* Select message color ********************************************************/

	$wp_customize->add_setting( 'smntcs_ps_message_color', array(
		'default' => '#2e4453',
		'type'    => 'option',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'smntcs_ps_message_color', array(
		'label'    => __( 'Message Color', 'smntcs-private-site' ),
		'section'  => 'smntcs_ps_section',
		'settings' => 'smntcs_ps_message_color',
	) ) );

	/* Add custom background ****************************************************/

	$wp_customize->add_setting( 'smntcs_ps_background', array(
		'capability' => 'edit_theme_options',
		'default'    => null,
		'type'       => 'option',
	) );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'smntcs_ps_background', array(
		'label'    => __( 'Background Image', 'themename' ),
		'section'  => 'smntcs_ps_section',
		'settings' => 'smntcs_ps_background',
	) ) );
}

add_filter( 'page_template', 'smntcs_ps_page_template' );
function smntcs_ps_page_template( $page_template ) {
	if ( get_option( 'smntcs_ps_enable' ) && ! is_user_logged_in() ) {
		$page_template = dirname( __FILE__ ) . '/template/private-site.php';
    }

	return $page_template;
}

add_action( 'wp_head', 'smntcs_ps_custom_css' );
function smntcs_ps_custom_css() {
	if ( get_option( 'smntcs_ps_enable' ) && ! is_user_logged_in() ) {
		?>
        <style>
            body {
                background: url(<?php print(get_option('smntcs_ps_background')); ?>) no-repeat center center fixed;
                -webkit-background-size: cover;
                -moz-background-size: cover;
                -o-background-size: cover;
                background-size: cover;
                color: <?php print(get_option('smntcs_ps_message_color', '#2e4453')); ?> !important;
                display: flex;
                align-items: center;
                justify-content: center;
				text-align: center;
				height: 100%;
            }
        </style>
	    <?php
	}

}
