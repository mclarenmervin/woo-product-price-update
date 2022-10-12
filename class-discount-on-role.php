<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Discount_On_Role {

    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'discount_on_role_enqueue_admin_script' ) );
        add_action( 'show_user_profile', array( $this, 'extra_user_profile_fields' ), 10, 1 );
        add_action( 'edit_user_profile', array( $this, 'extra_user_profile_fields'), 10, 1 );
        add_action( 'personal_options_update', array( $this, 'save_extra_user_profile_fields' ) );
        add_action( 'edit_user_profile_update', array( $this, 'save_extra_user_profile_fields' ) );

        // Generating dynamically the product "regular price"
        add_filter( 'woocommerce_product_get_regular_price', array( $this, 'custom_dynamic_regular_price' ), 10, 2 );
        add_filter( 'woocommerce_product_variation_get_regular_price', array( $this, 'custom_dynamic_regular_price' ), 10, 2 );

        // Generating dynamically the product "sale price"
        add_filter( 'woocommerce_product_get_sale_price', array( $this, 'custom_dynamic_sale_price' ), 10, 2 );
        add_filter( 'woocommerce_product_variation_get_sale_price', array( $this, 'custom_dynamic_sale_price' ), 10, 2 );

        // Displayed formatted regular price + sale price
        add_filter( 'woocommerce_get_price_html', array( $this, 'custom_dynamic_sale_price_html' ), 20, 2 );
        $this->discount_update_custom_roles();
    }

    public function discount_on_role_enqueue_admin_script() {
        wp_enqueue_script( 'discount_on_role_script', plugin_dir_url( __FILE__ ) . 'discount_on_role.js', array(), '1.0' );
        wp_enqueue_style( 'discount_on_role_style', plugin_dir_url( __FILE__ ) . 'discount_on_role.css', array(), '1.0' );
    }

    public function discount_update_custom_roles() {
        add_role('userdiscount', 'User Discount', array(
            'read' => true, // True allows that capability
            'edit_posts' => true,
            'delete_posts' => false, // Use false to explicitly deny
        ));
    }

    public function extra_user_profile_fields( $user ) { ?>

        <table class="form-table">
        <tr id="discount-on-role-row" style="display:none;">
            <th><label for="user_discount_on_role"><?php _e("Discount Percentage"); ?></label></th>
            <td>
                <input type="number" name="user_discount_on_role" id="user_discount_on_role" value="<?php echo esc_attr( get_the_author_meta( 'user_discount_on_role', $user->ID ) ); ?>" class="regular-text" /><br />
            </td>
        </tr>
    
        </table>
    <?php }
    
    public function save_extra_user_profile_fields( $user_id ) {
        if ( !current_user_can( 'edit_user', $user_id ) ) { 
            return false; 
        }
        update_user_meta( $user_id, 'user_discount_on_role', $_POST['user_discount_on_role'] );
    }
    
    public function custom_dynamic_regular_price( $regular_price, $product ) {
        if( empty($regular_price) || $regular_price == 0 )
            return $product->get_price();
        else
            return $regular_price;
    }
    
    public function custom_dynamic_sale_price( $sale_price, $product ) {
        $user_info = wp_get_current_user();
        $rate = (int)$user_info->user_discount_on_role;
        if ( in_array( 'userdiscount', (array) $user_info->roles ) ) {
            $discount_rate = 1 - ($rate/100);
        } else {
            $discount_rate = 1;
        }
        
        if( empty($sale_price) )
            return $product->get_regular_price() * $discount_rate;
        else
            return $sale_price;
    }
    
    public function custom_dynamic_sale_price_html( $price_html, $product ) {
        if( $product->is_type('variable') ) return $price_html;
    
        $price_html = wc_format_sale_price( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ), wc_get_price_to_display(  $product, array( 'price' => $product->get_sale_price() ) ) ) . $product->get_price_suffix();
    
        return $price_html;
    }
    
}
