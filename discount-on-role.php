<?php

/**
 * Plugin Name: Discount On Role
 * Description: This plugin will Show Product Price Based On User Role
 * Author: Mousam Debadatta
 * Version: 1.0.0
 * Text Domain: discount-on-role
 */

// No direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define('DISCOUNT_ON_ROLE_FILE', __FILE__);
define('DISCOUNT_ON_ROLE_PATH', plugin_dir_path(__FILE__));
define('DISCOUNT_ON_ROLE_BASE', plugin_basename(__FILE__));


/**
 * Admin notice if WooCommerce not installed and activated.
 */
function discount_on_role_no_woocommerce() { 
	?>
	<div class="error">
		<p><?php _e( 'Discount On Role Plugin is activated but not effective. It requires WooCommerce in order to work.', 'discount-on-role' ); ?></p>
	</div>
	<?php	
}

/**
*  Main Class
*/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	require DISCOUNT_ON_ROLE_PATH . 'class-discount-on-role.php';
	new Discount_On_Role();

} else {

	add_action( 'admin_notices', 'discount_on_role_no_woocommerce' );

}
