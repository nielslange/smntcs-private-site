<?php
/**
 * Plugin Name:           SMNTCS Private Site
 * Plugin URI:            https://github.com/nielslange/smntcs-private-site
 * Description:           Allow only logged in users to access the site.
 * Author:                Niels Lange
 * Author URI:            https://nielslange.de
 * Text Domain:           smntcs-private-site
 * Version:               1.8
 * Requires at least:     3.4
 * Requires PHP:          5.6
 * License:               GPL-2.0-or-later
 * License URI:           https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package SMNTCS_Private_Site
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add settings link on plugin page
 *
 * @param array $links The original array with customizer links.
 * @return array $links The updated array with customizer links.
 */
function smntcs_ps_plugin_settings_link( $links ) {
	$admin_url    = admin_url( 'customize.php?autofocus[control]=smntcs_ps_enable' );
	$settings_url = sprintf( '<a href="%s">%s</a>', $admin_url, __( 'Settings', 'smntcs-private-site' ) );
	array_unshift( $links, $settings_url );

	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'smntcs_ps_plugin_settings_link' );

/**
 * Enhance customizer
 *
 * @param WP_Customize_Manager $wp_customize The instance of the WP_Customize_Manager class.
 */
function smntcs_ps_register_customize( $wp_customize ) {
	$wp_customize->add_section(
		'smntcs_ps_section',
		array(
			'priority' => 500,
			'title'    => __( 'Private Site', 'smntcs-private-site' ),
		)
	);

	$wp_customize->add_setting(
		'smntcs_ps_enable',
		array(
			'capability' => 'edit_theme_options',
			'default'    => false,
			'type'       => 'option',
		)
	);

	$wp_customize->add_control(
		'smntcs_ps_enable',
		array(
			'label'   => __( 'Enable Private Site', 'smntcs-private-site' ),
			'section' => 'smntcs_ps_section',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'smntcs_ps_login_button',
		array(
			'capability' => 'edit_theme_options',
			'default'    => false,
			'type'       => 'option',
		)
	);

	$wp_customize->add_control(
		'smntcs_ps_login_button',
		array(
			'label'   => __( 'Show login button', 'smntcs-private-site' ),
			'section' => 'smntcs_ps_section',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'smntcs_ps_message',
		array(
			'capability'        => 'edit_theme_options',
			'default'           => __( 'This site is marked private by its owner.', 'smntcs-private-site' ),
			'sanitize_callback' => 'sanitize_text_field',
			'type'              => 'option',
		)
	);

	$wp_customize->add_control(
		'smntcs_ps_message',
		array(
			'label'       => __( 'Message', 'smntcs-private-site' ),
			'section'     => 'smntcs_ps_section',
			'type'        => 'textarea',
			'input_attrs' => array(
				'placeholder' => __( 'This site is marked private by its owner.', 'smntcs-private-site' ),
			),
		)
	);

	$wp_customize->add_setting(
		'smntcs_ps_message_color',
		array(
			'default' => '#2e4453',
			'type'    => 'option',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'smntcs_ps_message_color',
			array(
				'label'    => __( 'Message Color', 'smntcs-private-site' ),
				'section'  => 'smntcs_ps_section',
				'settings' => 'smntcs_ps_message_color',
			)
		)
	);

	$wp_customize->add_setting(
		'smntcs_ps_background',
		array(
			'capability' => 'edit_theme_options',
			'default'    => null,
			'type'       => 'option',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'smntcs_ps_background',
			array(
				'label'    => __( 'Background Image', 'smntcs-private-site' ),
				'section'  => 'smntcs_ps_section',
				'settings' => 'smntcs_ps_background',
			)
		)
	);
}
add_action( 'customize_register', 'smntcs_ps_register_customize' );

/**
 * Load custom template
 *
 * @param array $page_template The original string with page template.
 * @return array $page_template The updated string with page template.
 */
function smntcs_ps_page_template( $page_template ) {
	if ( esc_attr( get_option( 'smntcs_ps_enable' ) ) && ! is_user_logged_in() ) {
		$page_template = dirname( __FILE__ ) . '/template/private-site.php';
	}

	return $page_template;
}
add_filter( '404_template', 'smntcs_ps_page_template' );
add_filter( 'archive_template', 'smntcs_ps_page_template' );
add_filter( 'attachment_template', 'smntcs_ps_page_template' );
add_filter( 'author_template', 'smntcs_ps_page_template' );
add_filter( 'category_template', 'smntcs_ps_page_template' );
add_filter( 'date_template', 'smntcs_ps_page_template' );
add_filter( 'frontpage_template', 'smntcs_ps_page_template' );
add_filter( 'embed_template', 'smntcs_ps_page_template' );
add_filter( 'frontpage_template', 'smntcs_ps_page_template' );
add_filter( 'home_template', 'smntcs_ps_page_template' );
add_filter( 'index_template', 'smntcs_ps_page_template' );
add_filter( 'page_template', 'smntcs_ps_page_template' );
add_filter( 'paged_template', 'smntcs_ps_page_template' );
add_filter( 'privacypolicy_template', 'smntcs_ps_page_template' );
add_filter( 'search_template', 'smntcs_ps_page_template' );
add_filter( 'single_template', 'smntcs_ps_page_template' );
add_filter( 'singular_template', 'smntcs_ps_page_template' );
add_filter( 'tag_template', 'smntcs_ps_page_template' );
add_filter( 'taxonomy_template', 'smntcs_ps_page_template' );

/**
 * Load custom CSS.
 *
 * @return void
 */
function smntcs_ps_custom_css() {
	if ( esc_attr( get_option( 'smntcs_ps_enable' ) ) && ! is_user_logged_in() ) {
		?>
		<style>
			body {
				background: url(<?php print( esc_html( get_option( 'smntcs_ps_background' ) ) ); ?>) no-repeat center center fixed;
				-webkit-background-size: cover;
				-moz-background-size: cover;
				-o-background-size: cover;
				background-size: cover;
				color: <?php print( esc_html( get_option( 'smntcs_ps_message_color', '#2e4453' ) ) ); ?> !important;
				display: flex;
				align-items: center;
				justify-content: center;
			}

			#message {
				text-align: center;
				width: 200px;
			}

			p {
				font-weight: 600;
				margin-bottom: 1em;
			}
		</style>
		<?php
	}
}
add_action( 'wp_head', 'smntcs_ps_custom_css' );
