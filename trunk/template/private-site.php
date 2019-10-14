<?php 
  
wp_head(); 
  
print('<div id="message">');

if ( get_option( 'smntcs_ps_message' ) ) :
  printf('<p>%s</p>', get_option( 'smntcs_ps_message' ));
endif;

if ( get_option( 'smntcs_ps_login_button' ) ) :
  printf('<a href="%s"><input type="submit" class="button button-primary button-large" value="%s"></a>', wp_login_url(), __( 'Visit login form', 'smntcs-private-site' ));
endif;

print('</div>');
  
wp_footer();