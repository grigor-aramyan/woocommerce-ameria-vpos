<?php

/**
 * Plugin Name: Woo Ameria vPOS Payment Gateway
 */

add_action( 'plugins_loaded', 'woo_payment_gateway' );

function woo_add_gateway_class( $methods ) {

	$methods[] = 'Woo_Ameria_vPOS_Payment_Gateway';
	return $methods;
}
add_filter( 'woocommerce_payment_gateways', 'woo_add_gateway_class' );

function woo_payment_gateway() {
	class Woo_Ameria_vPOS_Payment_Gateway extends WC_Payment_Gateway {
	    public function __construct() {
		    	$this->id                 	= 'woo_ameria_vpos_payment';
					//$this->order_button_text  = __( 'Proceed to Test PG', 'woocommerce' );
		    	$this->method_title       	= __( 'Woo Ameria vPOS Payment', 'woocommerce' );
		    	$this->method_description 	= __( 'WooCommerce Ameria vPOS Payment Gateway', 'woocommerce' );
		    	$this->title              	= __( 'Woo Ameria vPOS Payment', 'woocommerce' );

    			$this->has_fields = false;
    			$this->supports = array(
    				'products'
    			);

          $this->init_form_fields();
        	$this->init_settings();
					$this->enabled = $this->get_option('enabled');
					$this->username = $this->get_option('username');
					$this->password = $this->get_option('password');
					$this->clientId = $this->get_option('clientId');
					$this->backUri = $this->get_option('backUri');
					//$this->enabled 		= true;

		    	add_action( 'check_wootestpayment', array( $this, 'check_response' ) );
		    	// Save settings
    			if ( is_admin() ) {
    				// Versions over 2.0
    				// Save our administration options. Since we are not going to be doing anything special
    				// we have not defined 'process_admin_options' in this class so the method in the parent
    				// class will be used instead
    				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
    			}
      }

      public function init_form_fields() {
      	$this->form_fields = array(
      		'enabled' => array(
      			'title' => __( 'Enable', 'woocommerce' ),
      			'type' => 'checkbox',
      			'label' => __( 'Enable Woo Ameria vPOS Payment', 'woocommerce' ),
      			'default' => 'yes'
      		),
					'username' => array(
		        'title' => __( 'Username', 'woocommerce' ),
		        'type' => 'text',
		        //'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
		        'default' => __( 'vPOS username', 'woocommerce' )
		        //'desc_tip'      => true,
		    	),
			    'password' => array(
						'title' => __( 'Password', 'woocommerce' ),
						'type' => 'text',
						'default' => __( 'vPOS password', 'woocommerce' )
			    ),
					'clientId' => array(
						'title' => __( 'Client ID', 'woocommerce' ),
						'type' => 'text',
						'default' => __( 'vPOS client ID', 'woocommerce' )
			    ),
					'backUri' => array(
						'title' => __( 'Back URI', 'woocommerce' ),
						'type' => 'text',
						'default' => __( 'Redirect URI from vPOS', 'woocommerce' )
			    )
      	);
      }

      public function process_payment( $order_id ) {
        global $woocommerce;
	      $order = new WC_Order( $order_id );

				$url = 'http://vpos-testing.epizy.com/testing_get_payment_id.php';

				$desc = "order num {$order_id} for price of {$order->get_total()}!";
				$data = array('username' => $this->username, 'password' => $this->password, 'orderId' => 2229067, 'description' => $desc, 'amount' => 10, 'backUri' => $this->backUri, 'clientId' => $this->clientId);

				// use key 'http' even if you send the request to https://...
				$options = array(
				    'http' => array(
				        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				        'method'  => 'POST',
				        'content' => http_build_query($data)
				    )
				);
				$context  = stream_context_create($options);
				$result = file_get_contents($url, false, $context);

				if (($result === FALSE) || ($result == '')) {
					return array(
					 'result'    => 'failure',
					 'redirect'  => ''
				 );
				}

				return array(
	        'result'    => 'success',
	        'redirect'  => $result
    		);

      }

      public function check_response() {
        global $woocommerce;

				$order = new WC_Order( $GLOBALS['returned_order_id'] );
				$order->update_status('on-hold', __('Awaiting product delivery', 'woocommerce'));

				$woocommerce->cart->empty_cart();
      }
	}
}


add_action( 'init', 'check_for_woopaypal' );
function check_for_woopaypal() {

	if( isset($_GET['orderid']) && isset($_GET['respcode']) && isset($_GET['paymentid']) ) {
    // Start the gateways
    if ($_GET['respcode'] == '00') {
			WC()->payment_gateways();
			$GLOBALS['returned_order_id'] = 56;
	    do_action( 'check_wootestpayment' );
		}
  }

}

?>
