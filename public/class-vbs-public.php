<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @link       https://www.interactive-design.gr
 * @since      1.0.0
 * @package    Vbs
 * @subpackage Vbs/public
 * @author     George Nikolopoulos <georgen@interactive-design.gr>
 */
class Vbs_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version )
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		global $post;

		if (has_shortcode($post->post_content, 'vbs_booking_form')) {
			wp_enqueue_style( 'datetimepicker', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', [], '4.6.13', 'screen' );
			wp_enqueue_style( 'vbs-form-block', plugin_dir_url( __FILE__ ) . 'css/vbs-form-block.css', [], $this->version, 'screen' );
		}

		if (is_page(carbon_get_theme_option( 'vehicles_page' ))) {
			wp_enqueue_style( 'vbs-vehicle-list', plugin_dir_url( __FILE__ ) . 'css/vbs-vehicle-list.css', [], $this->version, 'screen' );
		}

		if (is_page(carbon_get_theme_option( 'addons_page' ))) {
			wp_enqueue_style( 'vbs-addon-list', plugin_dir_url( __FILE__ ) . 'css/vbs-addon-list.css', [], $this->version, 'screen' );
		}

		if (is_page(carbon_get_theme_option( 'customer_page' ))) {
			wp_enqueue_style( 'vbs-form-block', plugin_dir_url( __FILE__ ) . 'css/vbs-form-block.css', [], $this->version, 'screen' );
		}

		if (is_page(carbon_get_theme_option( 'summary_page' ))) {
			wp_enqueue_style( 'vbs-booking-summary-block', plugin_dir_url( __FILE__ ) . 'css/vbs-booking-summary-block.css', [], $this->version, 'screen' );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		global $post;

		if (has_shortcode($post->post_content, 'vbs_booking_form')) {
			wp_enqueue_script( 'datetimepicker', 'https://cdn.jsdelivr.net/npm/flatpickr', ['jquery'], '4.6.13', true );
			wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key='.carbon_get_theme_option('google_api_key').'&libraries=places', [], '', true);
			wp_enqueue_script( 'google-maps-init', plugin_dir_url( __FILE__ ) . 'js/google-maps-init.js', ['google-maps'], '', true);
			wp_register_script( 'vbs-form-block', plugin_dir_url( __FILE__ ) . 'js/vbs-form-block.js', [], $this->version, true );
			wp_localize_script( 'vbs-form-block', 'wp_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
			wp_enqueue_script( 'vbs-form-block' );
		}

		if (is_page(carbon_get_theme_option( 'vehicles_page' ))) {
			wp_register_script( 'vbs-vehicles-list-block', plugin_dir_url( __FILE__ ) . 'js/vbs-vehicles-list-block.js', [], $this->version, true );
			wp_localize_script( 'vbs-vehicles-list-block', 'wp_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
			wp_enqueue_script( 'vbs-vehicles-list-block' );
		}

		if (is_page(carbon_get_theme_option( 'addons_page' ))) {
			wp_register_script( 'vbs-addon-list-block', plugin_dir_url( __FILE__ ) . 'js/vbs-addon-list-block.js', [], $this->version, true );
			wp_localize_script( 'vbs-addon-list-block', 'wp_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
			wp_enqueue_script( 'vbs-addon-list-block' );
		}

		if (is_page(carbon_get_theme_option( 'customer_page' ))) {
			wp_register_script( 'vbs-customer-information-block', plugin_dir_url( __FILE__ ) . 'js/vbs-customer-information-block.js', [], $this->version, true );
			wp_localize_script( 'vbs-customer-information-block', 'wp_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
			wp_enqueue_script( 'vbs-customer-information-block' );
		}

		if (is_page(carbon_get_theme_option( 'summary_page' ))) {
      wp_enqueue_script( 'stripe-js', 'https://js.stripe.com/clover/stripe.js', [], '', true );

			wp_register_script( 'vbs-booking-summary-block', plugin_dir_url( __FILE__ ) . 'js/vbs-booking-summary-block.js', [], $this->version, true );
			wp_localize_script( 'vbs-booking-summary-block', 'wp_data', [
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'stripe_pk' => carbon_get_theme_option('stripe_pkey'),
        'stripe_sk' => carbon_get_theme_option('stripe_skey'),
      ]);
			wp_enqueue_script( 'vbs-booking-summary-block' );
		}
	}

  /**
   * Render the booking form shortcode
   *
   * @since    1.0.0
   *
   * @param    array    $atts    Shortcode attributes, if any
   *
   * @return   void
   */
  public function booking_form( $atts )
  {
  	ob_start();
  	require_once plugin_dir_path( dirname( __FILE__ ) ) . '/templates/shortcodes/booking_form.php';
  	return ob_get_clean();
  }

  /**
   * Render the vehicle list shortcode
   *
   * @since    1.0.0
   *
   * @param    array    $atts    Shortcode attributes, if any
   *
   * @return   void
   */
  public function vehicles_list( $atts )
  {
  	$helper = new Vbs_Helper( $this->plugin_name, $this->version );
	  $transient_data = get_transient( get_query_var( 'search' ) );
	  $distance = 0;
	  if ( $transient_data ) {
	    $distance = $helper->getDistance( $helper->formatDataForDistance( $transient_data ) );
	    if ( $transient_data['return_datetime'] != '' ) {
	      $distance = $distance * 2;
	    }
	  }

	  ob_start();
  	require_once plugin_dir_path( dirname( __FILE__ ) ) . '/templates/shortcodes/vehicles_list.php';
  	return ob_get_clean();
  }

  /**
   * Render the addons list shortcode
   *
   * @since    1.0.0
   *
   * @param    array    $atts    Shortcode attributes, if any
   *
   * @return   void
   */
  public function addons_list( $atts )
  {
  	$helper = new Vbs_Helper( $this->plugin_name, $this->version );
		$transient_data = get_transient( get_query_var( 'search' ) );

	  ob_start();
  	require_once plugin_dir_path( dirname( __FILE__ ) ) . '/templates/shortcodes/addons_list.php';
  	return ob_get_clean();
  }

  /**
   * Render the customer information form shortcode
   *
   * @since    1.0.0
   *
   * @param    array    $atts    Shortcode attributes, if any
   *
   * @return   void
   */
  public function customer_information( $atts )
  {
  	ob_start();
  	require_once plugin_dir_path( dirname( __FILE__ ) ) . '/templates/shortcodes/customer_information.php';
  	return ob_get_clean();
  }

  /**
   * Render the booking summary shortcode
   *
   * @since    1.0.0
   *
   * @param    array    $atts    Shortcode attributes, if any
   *
   * @return   void
   */
  public function booking_summary( $atts )
  {
    $helper = new Vbs_Helper( $this->plugin_name, $this->version );
    $transient_data = get_transient( get_query_var( 'search' ) );

    ob_start();
    require_once plugin_dir_path( dirname( __FILE__ ) ) . '/templates/shortcodes/booking_summary.php';
    return ob_get_clean();
  }

  /**
   * Render the booking confirmation shortcode
   *
   * @since    1.0.0
   *
   * @return   void
   */
  public function booking_confirmation()
  {
  	$transient_data = get_transient( get_query_var( 'search' ) );
    delete_transient(get_query_var( 'search' ));

  	ob_start();
  	require_once plugin_dir_path( dirname( __FILE__ ) ) . '/templates/shortcodes/booking_confirmation.php';
  	return ob_get_clean();
  }

  /**
   * Function that creates the initial search params for the booking
   *
   * Uses the uuid generated to create a trnasient that holds the booking data until the booking is finalized
   *
   * @since    1.0.0
   *
   * @return   void
   */
  public function initiate_search()
  {
  	if ( !wp_verify_nonce( $_REQUEST['nonce'], 'initiate_search_nonce')) {
      exit("No naughty business please");
   	}

   	date_default_timezone_set( wp_timezone_string() );
   	$transient_data = [
   		'id' => substr($_POST['uuid'], 0, 8),
   		'pickup_type' => $_POST['pickup_type'],
   		'pickup' => $_POST['pickup_type'] == 'location' ? array_intersect_key(carbon_get_post_meta( $_POST['pickup_location'], 'address' ), array_flip(['lat', 'lng', 'address'])) : [
   			'address' => $_POST['pickup'],
   			'lat' => $_POST['pickup_lat'],
   			'lng' => $_POST['pickup_lng'],
   		],
   		'pickup_datetime' => date('Y-m-d H:i:s', strtotime($_POST['pickup_datetime'])),
   		'dropoff_type' => $_POST['dropoff_type'],
   		'dropoff' => $_POST['dropoff_type'] == 'location' ? array_intersect_key(carbon_get_post_meta( $_POST['dropoff_location'], 'address' ), array_flip(['lat', 'lng', 'address'])) : [
   			'address' => $_POST['dropoff'],
   			'lat' => $_POST['dropoff_lat'],
   			'lng' => $_POST['dropoff_lng'],
   		],
   		'return_datetime' => $_POST['return_datetime'] ? date('Y-m-d H:i:s', strtotime($_POST['return_datetime'])) : '',
   		'passengers' => (int)$_POST['passengers'],
   	];

   	// Get available cars based on the parameters passed
   	$helper = new Vbs_Helper();
   	$transient_data['available_cars'] = array_column($helper->getAvailableVehicles($transient_data), 'ID');

   	// Set the transient
   	set_transient( $_POST['uuid'], $transient_data, 2 * HOUR_IN_SECONDS );

   	$return_data = [
   		'result' => true,
   		'redirect' => get_page_link( carbon_get_theme_option( 'vehicles_page' ) ) . '?search=' . $_POST['uuid']
   	];
    echo json_encode($return_data);
    die();
  }

  /**
   * Function that updates the tansient with the selected vehicle, distance, cost and available addons to shoose from
   *
   * @since    1.0.0
   *
   * @return   void
   */
  public function select_vehicle()
  {
  	if ( !wp_verify_nonce( $_REQUEST['nonce'], 'vehicle_list_nonce')) {
      exit("No naughty business please");
   	}

   	$transient_data = get_transient( $_POST['search'] );
   	if ( !$transient_data ) {
   		echo json_encode(['result' => false,]);
    	die();
   	}

   	$helper = new Vbs_Helper();
   	$transient_data = array_merge( $transient_data, [
   		'distance' => (int)$_POST['distance'],
   		'vehicle' => (int)$_POST['vehicle'],
   		'available_addons' => $helper->getAddons( (int)$_POST['vehicle'] ),
   		'vehicle_cost' => $helper->calculatePrice( (int)$_POST['vehicle'], (int)$_POST['distance'] ),
   	] );

   	// Update the transient
   	set_transient( $_POST['search'], $transient_data, 2 * HOUR_IN_SECONDS );

   	$return_data = [
   		'result' => true,
   	];

   	if ( count( $transient_data['available_addons'] ) > 0 ) {
   		$return_data['redirect'] = get_page_link( carbon_get_theme_option( 'addons_page' ) ) . '?search=' . $_POST['search'];
   	} else {
   		$return_data['redirect'] = get_page_link( carbon_get_theme_option( 'customer_page' ) ) . '?search=' . $_POST['search'];
   	}

   	echo json_encode($return_data);
    die();
  }

  /**
   * Function that updates the tansient with the selected addon
   *
   * @since    1.0.0
   *
   * @return   void
   */
  public function select_addon()
  {
  	if ( !wp_verify_nonce( $_REQUEST['nonce'], 'addon_list_nonce')) {
      exit("No naughty business please");
   	}

   	$transient_data = get_transient( $_POST['search'] );
   	if ( !$transient_data ) {
   		echo json_encode(['result' => false,]);
    	die();
   	}

   	$transient_data = array_merge( $transient_data, [
   		'addon' => (int)$_POST['addon'],
   		'addon_cost' => (int)$_POST['addon'] !== 0 ? (float)carbon_get_post_meta( (int)$_POST['addon'], 'cost' ) : 0,
   	] );

   	// Update the transient
   	set_transient( $_POST['search'], $transient_data, 2 * HOUR_IN_SECONDS );

   	$return_data = [
   		'result' => true,
   		'redirect' => get_page_link( carbon_get_theme_option( 'customer_page' ) ) . '?search=' . $_POST['search'],
   	];

   	echo json_encode($return_data);
    die();
  }

  /**
   * Function that updates the tansient with customer information
   *
   * @since    1.0.0
   *
   * @return   void
   */
  public function customer_data()
  {
  	if ( !wp_verify_nonce( $_REQUEST['nonce'], 'customer_info_nonce')) {
      exit("No naughty business please");
   	}

   	$transient_data = get_transient( $_POST['search'] );
   	if ( !$transient_data ) {
   		echo json_encode([
   			'result' => false,
   			'reason' => 'transient not found',
   		]);
    	die();
   	}

   	$transient_data = array_merge( $transient_data, [
   		'customer' => [
   			'first_name' => $_POST['first_name'],
   			'last_name' => $_POST['last_name'],
   			'email' => $_POST['email'],
   			'phone' => $_POST['phone'],
   			'mobile' => $_POST['mobile'],
   			'notes' => $_POST['notes'],
   		],
   	] );

   	// Update the transient
   	set_transient( $_POST['search'], $transient_data, 2 * HOUR_IN_SECONDS );

   	$return_data = [
   		'result' => true,
   		'redirect' => get_page_link( carbon_get_theme_option( 'summary_page' ) ) . '?search=' . $_POST['search'],
   	];

   	echo json_encode($return_data);
    die();
  }

  /**
   * Function that updates the tansient with payment information
   *
   * @since    1.0.0
   *
   * @return   void
   */
  public function summary()
  {
  	if ( !wp_verify_nonce( $_REQUEST['nonce'], 'booking_summary_nonce')) {
      exit("No naughty business please");
   	}

   	$transient_data = get_transient( $_POST['search'] );
   	if ( !$transient_data ) {
   		echo json_encode([
   			'result' => false,
   			'reason' => 'transient not found',
   		]);
    	die();
   	}

   	$transient_data = array_merge( $transient_data, [
   		'total_cost' => (float)$transient_data['vehicle_cost'] + (float)$transient_data['addon_cost'],
   		'payment_method' => $_POST['payment_method'],
   	] );

   	// Update the transient
   	set_transient( $_POST['search'], $transient_data, 2 * HOUR_IN_SECONDS );

   	$post_id = wp_insert_post([
   		'post_date' => date('Y-m-d H:i:s'),
   		'post_title' => '#' . $transient_data['id'],
   		'post_status' => 'publish',
   		'post_type' => 'booking',
   		'meta_input' => [
   			'_pickup_datetime' => date('Y-m-d H:i:s', strtotime($transient_data['pickup_datetime'])),
   			'_pickup_address' => $transient_data['pickup']['address'],
   			'_pickup_address_coordinates' => $transient_data['pickup']['lat'] . ', ' . $transient_data['pickup']['lng'],
   			'_dropoff_address' => $transient_data['dropoff']['address'],
   			'_dropoff_address_coordinates' => $transient_data['dropoff']['lat'] . ', ' . $transient_data['dropoff']['lng'],
   			'_return' => $transient_data['return_datetime'] != '' ? 'yes' : 'no',
   			'_return_datetime' => $transient_data['return_datetime'] ? date('Y-m-d H:i:s', strtotime($transient_data['return_datetime'])) : '',
   			'_distance' => $transient_data['distance'],
   			'_disctance_cost' => $transient_data['vehicle_cost'],
   			'_addons_cost' => $transient_data['addon_cost'],
   			'_surcharge_cost' => '',
   			'_total_cost' => $transient_data['total_cost'],
   			'_vehicle' => $transient_data['vehicle'],
   			'_persons' => $transient_data['passengers'],
   			'_luggage' => '',
   			'_addons|||0|value' => 'post:addon:' . $transient_data['addon'],
				'_addons|||0|type' => 'post',
				'_addons|||0|subtype' => 'addon',
				'_addons|||0|id' => $transient_data['addon'],
   			'_first_name' => $transient_data['customer']['first_name'],
   			'_last_name' => $transient_data['customer']['last_name'],
   			'_email' => $transient_data['customer']['email'],
   			'_phone' => $transient_data['customer']['phone'],
   			'_mobile' => $transient_data['customer']['mobile'],
   			'_notes' => $transient_data['customer']['notes'],
   			'_status' => 'pending',
   			'_payment_method' => $transient_data['payment_method'],
   		],
   	]);

   	if ( $post_id == 0 ) {
   		echo json_encode([
   			'result' => false,
   			'reason' => 'could not create post',
   		]);
    	die();
   	}

   	$return_data = [
   		'result' => true,
   		'redirect' => get_page_link( carbon_get_theme_option( 'confirmation_page' ) ) . '?search=' . $_POST['search'],
   	];

   	echo json_encode($return_data);
    die();
  }

  /**
   * Function that creates a Stripe Payment Intent
   *
   * @since    1.0.0
   *
   * @return   void
   */
  public function payment_intent()
  {
    $transient_data = get_transient( $_POST['search'] );
    if ( !$transient_data ) {
      echo json_encode([
        'result' => false,
        'reason' => 'transient not found',
      ]);
      die();
    }

    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'lib/stripe/init.php';

    $stripe = new \Stripe\StripeClient(carbon_get_theme_option('stripe_skey'));

    $customer = $stripe->customers->create([
      'name' => $transient_data['customer']['first_name'] . ' ' . $transient_data['customer']['last_name'],
      'email' => $transient_data['customer']['email'],
      'phone' => $transient_data['customer']['phone'] ?: $transient_data['customer']['mobile'],
    ]);

    $paymentIntent = $stripe->paymentIntents->create([
      'amount' => ((float)$transient_data['vehicle_cost'] + (float)$transient_data['addon_cost']) * 100,
      'currency' => carbon_get_theme_option('currency'),
      'customer' => $customer->id,
      'payment_method_types' => [
        'card',
        'link',
        'klarna',
        'paypal',
        'revolut_pay',
      ],
    ]);

    if (!$paymentIntent) {
      echo json_encode([
        'result' => false,
        'reason' => 'could not create payment intent',
      ]);
      die();
    }

    $return_data = [
      'result' => true,
      'key' => $paymentIntent->client_secret,
    ];

    echo json_encode($return_data);
    die();
  }

}
