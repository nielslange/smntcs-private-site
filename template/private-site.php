<?php
/**
 * Template to render the login screen.
 *
 * @package WordPress
 * @subpackage SMNTCS Private Site
 */

wp_head();

print( '<div id="message">' );

if ( get_option( 'smntcs_ps_message' ) ) :
	printf(
		'<p>%s</p>',
		esc_html( get_option( 'smntcs_ps_message' ) )
	);
endif;

if ( get_option( 'smntcs_ps_login_button' ) ) :
	printf(
		'<a href="%s"><input type="submit" class="button button-primary button-large" value="%s"></a>',
		esc_url( wp_login_url() ),
		esc_html__( 'Visit login form', 'smntcs-private-site' )
	);
endif;

print( '</div>' );

wp_footer();
