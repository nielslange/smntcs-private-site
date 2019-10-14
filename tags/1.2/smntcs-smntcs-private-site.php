<?php
/**
 * Plugin Name: SMNTCS Private Site
 * Plugin URI: https://github.com/nielslange/smntcs-private-site
 * Description: Allow only logged in users to access the site
 * Author: Niels Lange
 * Author URI: https://nielslange.com
 * Text Domain: smntcs-private-site
 * Domain Path: /languages/
 * Version: 1.2
 * Requires at least: 3.4
 * Tested up to: 5.0
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

/* Copyright 2014-2016 Niels Lange (email : info@nielslange.de)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
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
	$admin_url    = admin_url( 'widgets.php' );
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
		'default'           => __( 'This site is marked private by its owner.', 'smntcs-private-site' ),
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
            }

            #message {
                margin-bottom: 25vh;
                text-align: center;
                width: 200px;
            }
        </style>
	    <?php
	}
}