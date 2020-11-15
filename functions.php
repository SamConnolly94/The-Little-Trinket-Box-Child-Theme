<?php
// enqueue the child theme stylesheet
function the_little_trinket_box_child_theme_enqueue_scripts() {
    wp_register_style( 'childstyle', get_stylesheet_directory_uri() . '/style.css' );
    wp_enqueue_style( 'childstyle' );
}
add_action( 'wp_enqueue_scripts', 'the_little_trinket_box_child_theme_enqueue_scripts', 11);

/**
 * Define the option to add a customisable text field under the 
 */
function the_little_trinket_box_adv_product_options(){
 
	echo '<div class="options_group">';
 
	woocommerce_wp_checkbox( array(
		'id'      => 'customisable_text',
		'value'   => get_post_meta( get_the_ID(), 'customisable_text', true ),
		'label'   => 'Enable customisable text',
		'desc_tip' => true,
		'description' => 'This product should show the user a text box to customise a product.',
	) );
 
	echo '</div>';
 
}
add_action( 'woocommerce_product_options_advanced', 'the_little_trinket_box_adv_product_options');
 
 
function the_little_trinket_box_save_fields( $id, $post ){
    update_post_meta( $id, 'customisable_text', $_POST['customisable_text'] );
}
add_action( 'woocommerce_process_product_meta', 'the_little_trinket_box_save_fields', 10, 2 );

/**
 * Display input on single product page
 */
function the_little_trinket_box_custom_option(){
    global $product;
    $id = $product->get_id();
    $customisable_product = get_post_meta( $id, 'customisable_text', true);
    $value = isset( $_POST['_custom_option' ] ) ? sanitize_text_field( $_POST['_custom_option'] ) : '';

    if ($customisable_product)
    {
        printf( '<p><label>%s<input name="_custom_option" value="%s" /></label></p>', __( 'Text to customise with', 'the-little-trinket-box-plugin-textdomain' ), esc_attr( $value ) );
    }
}
add_action( 'woocommerce_before_add_to_cart_button', 'the_little_trinket_box_custom_option', 9);

/**
 * Add custom data to the cart item
 * 
 * @param array $cart_item
 * @param int $product_id
 * @return array
 */
function the_little_trinket_box_add_cart_item_data( $cart_item, $product_id ){

    if( isset( $_POST['_custom_option'] ) ) {
        $cart_item['custom_option'] = sanitize_text_field( $_POST[ '_custom_option' ] );
    }

    return $cart_item;

}
add_filter( 'woocommerce_add_cart_item_data', 'the_little_trinket_box_add_cart_item_data', 10, 2 );

/**
 * Load cart data from session
 * 
 * @param array $cart_item
 * @param array $other_data
 * @return array
 */
function the_little_trinket_box_get_cart_item_from_session( $cart_item, $values ) {

    if ( isset( $values['custom_option'] ) ){
        $cart_item['custom_option'] = $values['custom_option'];
    }

    return $cart_item;

}
add_filter( 'woocommerce_get_cart_item_from_session', 'the_little_trinket_box_get_cart_item_from_session', 20, 2 );

/**
 * Add meta to order item
 * 
 * @param  WC_Order_Item  $order_item
 * @param  string         $cart_item_key
 * @param  array          $values The cart item values array.
 * @since 3.0.0
 */
//
function the_little_trinket_box_add_order_item_meta( $order_item, $cart_item_key, $values ) {

    if ( ! empty( $values['custom_option'] ) ) {
        $customised_text = sanitize_text_field( $values[ 'mnm_container' ] );
        $order_item->add_meta_data( 'custom_option', $customised_text , true );
        $order_item->update_meta_data( 'pa_custom-text', $customised_text );
    }

}
add_action( 'woocommerce_checkout_create_order_line_item', 'the_little_trinket_box_add_order_item_meta', 10, 3 );

/**
 * Display entered value in cart
 * 
 * @param array $other_data
 * @param array $cart_item
 * @return array
 */
function the_little_trinket_box_get_item_data( $other_data, $cart_item ) {

    if ( isset( $cart_item['custom_option'] ) ){

        $other_data[] = array(
            'key' => __( 'Your custom text', 'the-little-trinket-box-plugin-textdomain' ),
            'display' => sanitize_text_field( $cart_item['custom_option'] )
        );

    }

    return $other_data;

}
add_filter( 'woocommerce_get_item_data', 'the_little_trinket_box_get_item_data', 10, 2 );

/**
 * Restore custom field to product meta when product retrieved for order item.
 * Meta fields will be automatically displayed if not prefixed with _
 * 
 * @param WC_Product $product 
 * @param WC_Order_Item_Product $order_item
 * @return array
 */
function the_little_trinket_box_order_item_product( $product, $order_item ){

    if( $order_item->get_meta( 'custom_option' ) ){
        $product->add_meta_data( 'custom_option', $order_item->get_meta( 'custom_option' ), true );
    }

    return $product;

}
add_filter( 'woocommerce_order_item_product', 'the_little_trinket_box_order_item_product', 10, 2 );

/**
 * Customize the display of the meta key in order tables.
 * 
 * @param string $display_key
 * @param obj[] $meta
 * @param WC_Order_Item_Product $order_item
 * @return string
 */
function the_little_trinket_box_order_item_display_meta_key( $display_key, $meta, $order_item ){
    if( $meta->key == 'custom_option' ){
        $display_key =  __( 'Your custom text', 'the-little-trinket-box-plugin-textdomain' );
    }

    return $display_key;

}
add_filter( 'woocommerce_order_item_display_meta_key', 'the_little_trinket_box_order_item_display_meta_key', 10, 3 );

if(!function_exists('the_little_trinket_box_add_values_to_order_item_meta'))
{
  function the_little_trinket_box_add_values_to_order_item_meta($item_id, $values)
  {
        global $woocommerce, $wpdb;
        $user_custom_values = $values['custom_option'];
        if(!empty($user_custom_values))
        {
            wc_add_order_item_meta($item_id,'Custom Text',$user_custom_values);  
        }
  }
}
add_action('woocommerce_add_order_item_meta','the_little_trinket_box_add_values_to_order_item_meta',1,2);