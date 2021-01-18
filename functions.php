<?php

define("MAX_NUM_CUSTOM_TEXT_BOXES", 5);

// enqueue the child theme stylesheet
function the_little_trinket_box_child_theme_enqueue_scripts() {
    wp_register_style( 'childstyle', get_stylesheet_directory_uri() . '/style.css' );
    wp_enqueue_style( 'childstyle' );
}
add_action( 'wp_enqueue_scripts', 'the_little_trinket_box_child_theme_enqueue_scripts', 11);

/**
 * Define the option to add a customisable text field under the 
 */
function the_little_trinket_box_adv_product_options() {
    $customTextBoxCount = defined('MAX_NUM_CUSTOM_TEXT_BOXES') ? constant('MAX_NUM_CUSTOM_TEXT_BOXES') : 0;

    for ($i = 1; $i <= $customTextBoxCount; $i++) {
        echo '<div class="options_group">';
        woocommerce_wp_checkbox( array(
            'id'      => 'custom_text_enabled_' . strval($i),
            'value'   => get_post_meta( get_the_ID(), 'custom_text_enabled_' . strval($i), true ),
            'label'   => 'Custom text box ' . strval($i) . ' enabled?',
            'desc_tip' => true,
            'description' => 'This checkbox should be enabled when there is a requirement to show custom text area ' . strval($i) . '.',
        ));

        woocommerce_wp_checkbox( array(
            'id'      => 'custom_text_is_optional_' . strval($i),
            'value'   => get_post_meta( get_the_ID(), 'custom_text_is_optional_' . strval($i), true ),
            'label'   => 'Can be empty?',
            'desc_tip' => true,
            'description' => 'Custom text boxes can be left blank if this is checked',
        ));

        // Text Field
        woocommerce_wp_text_input( 
            array( 
                'id'          => 'custom_text_field_label_' . strval($i), 
                'value'       => get_post_meta( get_the_ID(), 'custom_text_field_label_' . strval($i), true ),
                'label'       => __( 'Label to show next to text field', 'woocommerce' ), 
                'placeholder' => 'Label to show',
                'desc_tip'    => 'true',
                'description' => __( 'Enter the value that should be shown next to custom text field number ' . strval($i), 'woocommerce' ) 
            ));
        
        // Max length
        $max_len_default_value = get_post_meta( get_the_ID(), 'custom_text_field_max_length_' . strval($i), true );
        if (!isset($max_len_default_value) || $max_len_default_value == '') 
        {
            // Default to 200 characters
            $max_len_default_value = 200;
        }

        woocommerce_wp_text_input( 
            array( 
                'id'                => 'custom_text_field_max_length_' . strval($i), 
                'value'             => $max_len_default_value,
                'label'             => "Max length", 
                'placeholder'       => 'Enter the max num of characters', 
                'desc_tip'          => true,
                'description'       => "Define the maximum number of characters that can be input into this text box",
                'type'              => 'number', 
                'custom_attributes' => array(
                        'step' 	=> 'any',
                        'min'	=> '0'
                    ) 
            )
        );

        // Allow symbols?
        woocommerce_wp_checkbox( array(
            'id'      => 'custom_text_field_allow_symbols_' . strval($i),
            'value'   => get_post_meta( get_the_ID(), 'custom_text_field_allow_symbols_' . strval($i), true ),
            'label'   => 'Allow symbols?',
            'desc_tip' => true,
            'description' => 'Symbols should be accepted within the customisable text ' . strval($i),
        ));
        echo '</div>';
    }    
}
add_action( 'woocommerce_product_options_advanced', 'the_little_trinket_box_adv_product_options');
 
 
function the_little_trinket_box_save_fields( $id, $post ) {
    $customTextBoxCount = defined('MAX_NUM_CUSTOM_TEXT_BOXES') ? constant('MAX_NUM_CUSTOM_TEXT_BOXES') : 0;
    for ($i = 1; $i <= $customTextBoxCount; $i++) {
        update_post_meta( $id, 'custom_text_enabled_' . strval($i), $_POST['custom_text_enabled_' . strval($i)] );
        update_post_meta( $id, 'custom_text_is_optional_' . strval($i), $_POST['custom_text_is_optional_' . strval($i)] );
        update_post_meta( $id, 'custom_text_field_label_' . strval($i), $_POST['custom_text_field_label_' . strval($i)] );
        update_post_meta( $id, 'custom_text_field_max_length_' . strval($i), $_POST['custom_text_field_max_length_' . strval($i)] );
        update_post_meta( $id, 'custom_text_field_allow_symbols_' . strval($i), $_POST['custom_text_field_allow_symbols_' . strval($i)] );
    }
}
add_action( 'woocommerce_process_product_meta', 'the_little_trinket_box_save_fields', 10, 2 );

/**
 * Display input on single product page
 */
function the_little_trinket_box_custom_option(){
    global $product;
    $id = $product->get_id();

    $customTextBoxCount = defined('MAX_NUM_CUSTOM_TEXT_BOXES') ? constant('MAX_NUM_CUSTOM_TEXT_BOXES') : 0;

    $values = [];
    for ($i = 1; $i <= $customTextBoxCount; $i++) {
        $customOption = '_custom_option_' . strval($i);
        $valueToAdd = isset( $_POST[$customOption] ) ? sanitize_text_field( $_POST[$customOption] ) : '';
        array_push($values, $valueToAdd);
        $customTextEnabled = get_post_meta($id, 'custom_text_enabled_' . strval($i), true) == "yes" ? true : false;
        if ($customTextEnabled) {
            $customLabel = get_post_meta($id, 'custom_text_field_label_' . strval($i), true);
        }
    }

    $customTextboxExists = false;
    $tableHtml = '<table class="variations" cellspacing="0" style="margin-bottom: 10px;"><tbody>';
    for ($i = 1; $i <= $customTextBoxCount; $i++) {
        $customTextEnabled = get_post_meta($id, 'custom_text_enabled_' . strval($i), true) == "yes" ? true : false;
        if ($customTextEnabled) {
            $customTextboxExists = true;
            $customLabel = get_post_meta($id, 'custom_text_field_label_' . strval($i), true);
            $optional = get_post_meta($id, 'custom_text_is_optional_' . strval($i), true) == "yes" ? true : false; 
            $allowSymbols = get_post_meta($id, 'custom_text_field_allow_symbols_' . strval($i), true) == "yes" ? true : false; 

            $maxlen = get_post_meta($id, 'custom_text_field_max_length_' . strval($i), true);
            $tableHtml .= '<tr><td><label style="margin-right: 10px;" for="_custom_option_' . strval($i) . '">' . $customLabel . '</label></td><td class="value"><input class="input-text text input optgroup textarea" name="_custom_option_' . strval($i).'" value="' . esc_attr( $values[$i] ) . '" maxlength="' . $maxlen .'"';
            if (!$optional)
            {
                $tableHtml .= 'required ';
            }
            if (!$allowSymbols)
            {
                $tableHtml .= 'pattern=[A-Za-z0-9]* title="No special characters allowed"';
            }
            $tableHtml .= '/></td></tr>';
        }
    }
    $tableHtml .= '</tbody></table>';
    printf($tableHtml);

}
add_action( 'woocommerce_before_add_to_cart_button', 'the_little_trinket_box_custom_option', 9);

/**
 * Validate the product that has been submitted
 */
function the_little_trinket_box_filter_add_to_cart_validation( $passed, $product_id, $quantity ) {
    $customTextBoxCount = defined('MAX_NUM_CUSTOM_TEXT_BOXES') ? constant('MAX_NUM_CUSTOM_TEXT_BOXES') : 0;
    for ($i = 1; $i <= $customTextBoxCount; $i++) {
        $enabledKey = 'custom_text_enabled_' . strval($i);
        $customTextEnabled = get_post_meta($product_id, $enabledKey, true) == "yes" ? true : false;
        if ($customTextEnabled)
        {
            $product = wc_get_product( $product_id );
            $optionalKey = 'custom_text_is_optional_' . strval($i);
            $optional = get_post_meta($product_id, $optionalKey, true) == "yes" ? true : false; 
            $labelKey = 'custom_text_field_label_' . strval($i);
            $label = get_post_meta($product_id, $labelKey, true);
            $customOption = '_custom_option_' . strval($i);
            if (!$optional && empty($_POST[ $customOption ]))
            {
                $errMsg = 'Please enter an option in the customisable field labelled \'' . $label . '\'.';
                wc_add_notice($errMsg, 'error' );
                $passed = false;
            }
            $allowSymbolsKey = 'custom_text_field_allow_symbols_' . strval($i);
            $allowSymbols = get_post_meta($product_id, $allowSymbolsKey, true) == "yes" ? true : false; 
            if (!$allowSymbols && !empty($_POST[ $customOption ] && _s_has_special_chars($_POST[ $customOption ])))
            {
                $errMsg = 'Value for \'' . $label . '\' cannot contain special characters.';
                wc_add_notice($errMsg, 'error' );
                $passed = false;
            }            
            $maxCharsKey = 'custom_text_field_max_length_' . strval($i);
            $maxChars = intval(get_post_meta($product_id, $maxCharsKey, true)); 
            if (strlen($_POST[ $customOption ]) > $maxChars)
            {
                $errMsg = 'Value for \'' . $label . '\' is longer than the max length of \'' . strval($maxChars) . '\'.';
                wc_add_notice($errMsg, 'error' );
                $passed = false;
            }
        }
    }
    return $passed;
}
add_filter( 'woocommerce_add_to_cart_validation', 'the_little_trinket_box_filter_add_to_cart_validation', 10, 3 );

function _s_has_special_chars( $string ) {
    return preg_match('/[^a-zA-Z\d]/', $string);
}

/**
 * Add custom data to the cart item
 * 
 * @param array $cart_item
 * @param int $product_id
 * @return array
 */
function the_little_trinket_box_add_cart_item_data( $cart_item, $product_id ){
    $customTextBoxCount = defined('MAX_NUM_CUSTOM_TEXT_BOXES') ? constant('MAX_NUM_CUSTOM_TEXT_BOXES') : 0;
    for ($i = 1; $i <= $customTextBoxCount; $i++) {
        if( isset( $_POST['_custom_option_' . strval($i)] ) ) {
            $optional = get_post_meta($product_id, 'custom_text_is_optional_' . strval($i), true);   
            if (!$optional || ($optional && isset($_POST[ '_custom_option_' . strval($i) ])))
            {
                $customLabel = get_post_meta($product_id, 'custom_text_field_label_' . strval($i), true);
                $customOption = sanitize_text_field( $_POST[ '_custom_option_' . strval($i) ] );
                $cart_item['custom_option_' . strval($i)] = [$customLabel, $customOption];
            }
        }            
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
    $customTextBoxCount = defined('MAX_NUM_CUSTOM_TEXT_BOXES') ? constant('MAX_NUM_CUSTOM_TEXT_BOXES') : 0;
    for ($i = 1; $i <= $customTextBoxCount; $i++) {
        if( isset( $values['custom_option_' . strval($i)]) ) {
            $customLabelVal = $values['custom_option_' . strval($i)][0];
            $customOptionVal = $values['custom_option_' . strval($i)][1];
            $cart_item['custom_option_' . strval($i)] = [$customLabelVal, $customOptionVal];
        }
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
    $customTextBoxCount = defined('MAX_NUM_CUSTOM_TEXT_BOXES') ? constant('MAX_NUM_CUSTOM_TEXT_BOXES') : 0;
    for ($i = 1; $i <= $customTextBoxCount; $i++) {
        if ( ! empty( $values['custom_option_' . strval($i)] ) ) {
            $customised_label = $values['custom_option_' . strval($i)][0];
            $customised_text = sanitize_text_field( $values['mnm_container']);
            $order_item->add_meta_data( 'custom_option_' . strval($i), [$customised_label, $customised_text], true );
            $order_item->update_meta_data( 'pa_custom-text-' . strval($i), $customised_text );
        }
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
    $customTextBoxCount = defined('MAX_NUM_CUSTOM_TEXT_BOXES') ? constant('MAX_NUM_CUSTOM_TEXT_BOXES') : 0;
    for ($i = 1; $i <= $customTextBoxCount; $i++) {
        // Called
        if ( isset( $cart_item['custom_option_' . strval($i)] ) ){
            $label = $cart_item['custom_option_' . strval($i)][0];
            $other_data[] = array(
                'key' => __(  $label, 'the-little-trinket-box-plugin-textdomain' ),
                'display' => sanitize_text_field( $cart_item['custom_option_' . strval($i)][1] )
            );
        }
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

    $customTextBoxCount = defined('MAX_NUM_CUSTOM_TEXT_BOXES') ? constant('MAX_NUM_CUSTOM_TEXT_BOXES') : 0;
    for ($i = 1; $i <= $customTextBoxCount; $i++) {
        if( !empty($order_item->get_meta( 'custom_option_' . strval($i)) ) ){
            $label = $order_item->get_meta( 'custom_option_' . strval($i) )[0];
            $customText = $order_item->get_meta( 'custom_option_' . strval($i) )[1];
            $product->add_meta_data( 'custom_option_' . strval($i),[$label, $customText], true );
        }
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
    $customTextBoxCount = defined('MAX_NUM_CUSTOM_TEXT_BOXES') ? constant('MAX_NUM_CUSTOM_TEXT_BOXES') : 0;
    for ($i = 1; $i <= $customTextBoxCount; $i++) {
        if( $meta->key == 'custom_option_' . strval($i) ){
            $label = $meta['custom_option_' . strval($i)][0];
            $display_key =  __( $label, 'the-little-trinket-box-plugin-textdomain' );
        }
    }
    return $display_key;

}
add_filter( 'woocommerce_order_item_display_meta_key', 'the_little_trinket_box_order_item_display_meta_key', 10, 3 );

if(!function_exists('the_little_trinket_box_add_values_to_order_item_meta'))
{
  function the_little_trinket_box_add_values_to_order_item_meta($item_id, $values)
  {
    global $woocommerce, $wpdb;
    
    $customTextBoxCount = defined('MAX_NUM_CUSTOM_TEXT_BOXES') ? constant('MAX_NUM_CUSTOM_TEXT_BOXES') : 0;
    for ($i = 1; $i <= $customTextBoxCount; $i++) {
        $user_custom_label = $values['custom_option_' . strval($i)][0];
        $user_custom_values = $values['custom_option_' . strval($i)][1];
        if(!empty($user_custom_values))
        {
            wc_add_order_item_meta($item_id, $user_custom_label, $user_custom_values);  
        }
    }
  }
}
add_action('woocommerce_add_order_item_meta','the_little_trinket_box_add_values_to_order_item_meta',1,2);

///////////////////////////////////////////////
// COOKIES
///////////////////////////////////////////////
/*
 * Only load the Facebook pixel tracking scripts if the Cookie Notice has been accepted.
 * If Cookie Notice is not active, or the customer has not accepted, the scripts will not load.
 */
function check_cn_has_been_accepted( $is_enabled ) {
    // The GDPR consent plugin we have should take care of this one for us. 
    return false;
}
add_filter( 'facebook_for_woocommerce_integration_pixel_enabled', 'check_cn_has_been_accepted', 10, 1 );

function the_little_trinket_box_customize_register( $wp_customize ) {
    
	// $selective_refresh = isset( $wp_customize->selective_refresh ) ? 'postMessage' : 'refresh';

	// /* Banners section */

	// $wp_customize->add_section(
	// 	'shop_isle_child_banners_section',
	// 	array(
	// 		'title'    => __( 'PURPLE AKI Banners section', 'shop-isle-child' ),
	// 		'priority' => apply_filters( 'shop_isle_section_priority', 15, 'shop_isle_child_banners_section' ),
	// 	)
	// );

	// /* Hide banner */
	// $wp_customize->add_setting(
	// 	'shop_isle_child_banners_hide',
	// 	array(
	// 		'transport'         => $selective_refresh,
	// 		'sanitize_callback' => 'shop_isle_sanitize_text',
	// 	)
	// );

	// $wp_customize->add_control(
	// 	'shop_isle_child_banners_hide',
	// 	array(
	// 		'type'     => 'checkbox',
	// 		'label'    => __( 'Hide banners section?', 'shop-isle' ),
	// 		'section'  => 'shop_isle_child_banners_section',
	// 		'priority' => 1,
	// 	)
	// );

	// $wp_customize->add_setting(
	// 	'shop_isle_child_banners_title',
	// 	array(
	// 		'transport'         => $selective_refresh,
	// 		'sanitize_callback' => 'shop_isle_sanitize_text',
	// 	)
	// );

	// $wp_customize->add_control(
	// 	'shop_isle_child_banners_title',
	// 	array(
	// 		'label'    => __( 'Section title', 'shop-isle' ),
	// 		'section'  => 'shop_isle_child_banners_section',
	// 		'priority' => 2,
	// 	)
	// );

	// /* Banner */
	// $wp_customize->add_setting(
	// 	'shop_isle_child_banners',
	// 	array(
	// 		'transport'         => 'postMessage',
	// 		'sanitize_callback' => 'shop_isle_sanitize_repeater',
	// 		'default'           => json_encode(
	// 			array(
	// 				array(
	// 					'image_url' => get_template_directory_uri() . '/assets/images/banner1.jpg',
	// 					'link'      => '#',
	// 				),
	// 				array(
	// 					'image_url' => get_template_directory_uri() . '/assets/images/banner2.jpg',
	// 					'link'      => '#',
	// 				),
	// 				array(
	// 					'image_url' => get_template_directory_uri() . '/assets/images/banner3.jpg',
	// 					'link'      => '#',
	// 				),
	// 			)
	// 		),
	// 	)
	// );
	// $wp_customize->add_control(
	// 	new Shop_Isle_Repeater_Controler(
	// 		$wp_customize,
	// 		'shop_isle_child_banners',
	// 		array(
	// 			'label'                         => __( 'Add new banner', 'shop-isle' ),
	// 			'section'                       => 'shop_isle_banners_section',
	// 			'active_callback'               => 'is_front_page',
	// 			'priority'                      => 3,
	// 			'shop_isle_image_control'       => true,
	// 			'shop_isle_link_control'        => true,
	// 			'shop_isle_text_control'        => false,
	// 			'shop_isle_subtext_control'     => false,
	// 			'shop_isle_label_control'       => false,
	// 			'shop_isle_icon_control'        => false,
	// 			'shop_isle_description_control' => false,
	// 			'shop_isle_box_label'           => __( 'Banner', 'shop-isle' ),
	// 			'shop_isle_box_add_label'       => __( 'Add new banner', 'shop-isle' ),
	// 		)
	// 	)
	// );

	// $wp_customize->get_section( 'shop_isle_child_banners_section' )->panel = 'shop_isle_front_page_sections';

 }
 add_action( 'customize_register', 'the_little_trinket_box_customize_register' );

function my_custom_scripts() {
    wp_enqueue_script( 'navbar-custom', get_stylesheet_directory_uri() . '/js/navbar.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'homepage-resizer', get_stylesheet_directory_uri() . '/js/homepage-resizer.js', array( 'jquery' ), '', true );
}
add_action( 'wp_enqueue_scripts', 'my_custom_scripts' );