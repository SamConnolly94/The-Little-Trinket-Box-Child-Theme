<?php
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

?>