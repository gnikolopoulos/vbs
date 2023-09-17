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
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/vbs-public.css', [], $this->version, 'screen' );

		if (has_block('carbon-fields/vbs-booking-form')) {
			wp_enqueue_style( 'datetimepicker', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', [], '4.6.13', 'screen' );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/vbs-public.js', ['jquery'], $this->version, false );

		if (has_block('carbon-fields/vbs-booking-form')) {
			wp_enqueue_script( 'datetimepicker', 'https://cdn.jsdelivr.net/npm/flatpickr', ['jquery'], '4.6.13', true );
			wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key='.carbon_get_theme_option('google_api_key').'&libraries=places', [], '', true);
			wp_enqueue_script( 'google-maps-init', plugin_dir_url( __FILE__ ) . 'js/google-maps-init.js', ['google-maps'], '', true);
			wp_register_script( 'vbs-form-block', plugin_dir_url( __FILE__ ) . 'js/vbs-form-block.js', [], $this->version, true );
			wp_localize_script( 'vbs-form-block', 'wp_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
			wp_enqueue_script( 'vbs-form-block' );
		}

		if (has_block('carbon-fields/vbs-vehicles-list')) {
			wp_register_script( 'vbs-vehicles-list-block', plugin_dir_url( __FILE__ ) . 'js/vbs-vehicles-list-block.js', [], $this->version, true );
			wp_localize_script( 'vbs-vehicles-list-block', 'wp_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
			wp_enqueue_script( 'vbs-vehicles-list-block' );
		}
	}

	/**
   * Register the plugin blocks for Guttenberg Editor
   * Also uses wp_register_style to register custom styles to be used
   *
   * @since    1.0.0
   */
  public function register_blocks()
  {
  	wp_register_style( 'vbs-form-block', plugin_dir_url( __FILE__ ) . 'css/vbs-form-block.css', [], $this->version, 'screen' );
  	wp_register_style( 'vbs-vehicle-list', plugin_dir_url( __FILE__ ) . 'css/vbs-vehicle-list.css', [], $this->version, 'screen' );
  	wp_register_style( 'vbs-addon-list', plugin_dir_url( __FILE__ ) . 'css/vbs-addon-list.css', [], $this->version, 'screen' );

  	Block::make( __( 'VBS Booking form', 'vbs' ) )
  		->set_description( __( 'Shows the booking form', 'vbs' ) )
  		->set_category( 'vbs', __( 'Vehicle Booking System', 'vbs' ), 'car' )
  		->set_mode('both')
  		->set_icon( 'feedback' )
  		->set_style( 'vbs-form-block' )
  		->set_inner_blocks( false )
  		->set_render_callback(function () {
  			require_once plugin_dir_path( dirname( __FILE__ ) ) . '/templates/blocks/booking_form.php';
  		});

  	Block::make( __( 'VBS Vehicles List', 'vbs' ) )
  		->set_description( __( 'Shows the available vehicles after a search', 'vbs' ) )
  		->set_category( 'vbs', __( 'Vehicle Booking System', 'vbs' ), 'car' )
  		->set_mode('both')
  		->set_icon( 'list-view' )
  		->set_style( 'vbs-vehicle-list' )
  		->set_inner_blocks( false )
  		->set_render_callback(function () {
  			$helper = new Vbs_Helper( $this->plugin_name, $this->version );
			  $transient_data = get_transient( get_query_var( 'search' ) );
			  $distance = 0;
			  if ( $transient_data ) {
			    $distance = $helper->getDistance( $helper->formatDataForDistance( $transient_data ) );
			    if ( $transient_data['return_datetime'] != '' ) {
			      $distance = $distance * 2;
			    }
			  }

  			require_once plugin_dir_path( dirname( __FILE__ ) ) . '/templates/blocks/vehicles_list.php';
  		});

  	Block::make( __( 'VBS Addons List', 'vbs' ) )
  		->set_description( __( 'Shows the available addons for the selected vehicle', 'vbs' ) )
  		->set_category( 'vbs', __( 'Vehicle Booking System', 'vbs' ), 'car' )
  		->set_mode('both')
  		->set_icon( 'list-view' )
  		->set_style( 'vbs-addon-list' )
  		->set_inner_blocks( false )
  		->set_render_callback(function () {
  			$helper = new Vbs_Helper( $this->plugin_name, $this->version );
			  $transient_data = get_transient( get_query_var( 'search' ) );

  			require_once plugin_dir_path( dirname( __FILE__ ) ) . '/templates/blocks/addons_list.php';
  		});
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
  	if ( !wp_verify_nonce( $_REQUEST['nonce'], "initiate_search_nonce")) {
      exit("No naughty business please");
   	}

   	date_default_timezone_set( wp_timezone_string() );
   	$transient_data = [
   		'id' => $_POST['nonce'],
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
   * Function that updated the tansient with the selected vehicle, distance, cost and available addons to shoose from
   *
   * @since    1.0.0
   *
   * @return   void
   */
  public function select_vehicle()
  {
  	if ( !wp_verify_nonce( $_REQUEST['nonce'], "vehicle_list_nonce")) {
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
   		'addons' => $helper->getAddons( (int)$_POST['vehicle'] ),
   		'cost' => $helper->calculatePrice( (int)$_POST['vehicle'], (int)$_POST['distance'] ),
   	] );

   	// Update the transient
   	set_transient( $_POST['search'], $transient_data, 2 * HOUR_IN_SECONDS );

   	$return_data = [
   		'result' => true,
   	];

   	if ( count( $transient_data ) > 0 ) {
   		$return_data['redirect'] = get_page_link( carbon_get_theme_option( 'addons_page' ) ) . '?search=' . $_POST['search'];
   	} else {
   		$return_data['redirect'] = get_page_link( carbon_get_theme_option( 'customer_page' ) ) . '?search=' . $_POST['search'];
   	}

   	echo json_encode($return_data);
    die();
  }

}
