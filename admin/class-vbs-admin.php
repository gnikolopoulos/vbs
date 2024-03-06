<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.interactive-design.gr
 * @since      1.0.0
 * @package    Vbs
 * @subpackage Vbs/admin
 * @author     George Nikolopoulos <georgen@interactive-design.gr>
 */
class Vbs_Admin
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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version )
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/vbs-admin.css', array(), $this->version, 'screen' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/vbs-admin.js', array( 'jquery' ), $this->version, false );
	}

  public function add_maps_js()
  {
    global $post_type;
    if ( 'booking' == $post_type ) {
      wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key='.carbon_get_theme_option('google_api_key').'&v=weekly', ['google-maps-booking'], null, ['strategy' => 'async', 'in_footer' => true]);
      wp_enqueue_script( 'google-maps-booking', plugin_dir_url( __FILE__ ) . 'js/vbs-admin-booking.js', [], null, ['strategy' => 'defer', 'in_footer' => true] );
    }
  }

	/**
   * Register the required plugins for this theme.
	 *
   * The variables passed to the `tgmpa()` function should be:
   * - an array of plugin arrays;
   * - optionally a configuration array.
   *
   * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
   *
   * @since    1.0.0
   */
  public function register_required_plugins()
  {
    /*
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = [
      [
        'name'               => 'CarbonFields',
        'slug'               => 'carbon-fields',
        'source'             => plugin_dir_path( dirname( __FILE__ ) ) . '/lib/plugins/carbon-fields.zip',
        'required'           => true,
        'version'            => '3.6.3',
        'force_activation'   => false,
        'force_deactivation' => true,
      ],
    ];

    $config = [
      'id'           => 'vbs',
      'default_path' => '',
      'menu'         => 'tgmpa-install-plugins',
      'parent_slug'  => 'plugins.php',
      'capability'   => 'manage_options',
      'has_notices'  => true,
      'dismissable'  => true,
      'dismiss_msg'  => '',
      'is_automatic' => false,
      'message'      => '',
    ];

    tgmpa( $plugins, $config );
  }

  /**
   * Add the main plugin menu
   * All other items will be submenus of this one
   *
   * @link    https://developer.wordpress.org/reference/functions/add_menu_page/
   * @since   1.0.0
   */
  public function register_admin_menu()
  {
  	add_menu_page(
			__( 'Vehicle Booking', 'vbs' ),
			__( 'Vehicle Booking', 'vbs' ),
			'manage_options',
			'vbs.php',
			[$this, 'dashboard_renderer'],
			'dashicons-car',
			5
		);
  }

  /**
   * Renderer for the main plugin menu
   * This will inclide a template for the display of the dashboard
   *
   * @since    1.0.0
   */
  public function dashboard_renderer()
  {
		echo 'Dashboard to appear here...';
	}

  /**
   * Register the plugin options page and metaboxes
   *
   * @since    1.0.0
   */
  public function register_containers()
  {
    Container::make( 'theme_options', __( 'Settings', 'vbs' ) )
    	->set_page_parent( 'vbs.php' )
      ->add_tab( __( 'General', 'vbs' ), [
        Field::make( 'text', 'google_api_key', __( 'Google Maps API key', 'vbs' ) )
          ->set_help_text( __('https://developers.google.com/maps/documentation/places/web-service/get-api-key') ),
        Field::make( 'select', 'currency', __( 'Default currency', 'vbs' ) )
          ->set_options([
            '€' => 'Euro',
            '$' => 'United States Dollar',
          ])
          ->set_default_value('€'),
      ])
      ->add_tab( __( 'Page Configuration', 'vbs' ), [
        Field::make( 'select', 'vehicles_page', __( 'Page that contains the vehicles list block', 'vbs' ) )
          ->set_options(['Vbs_Helper', 'getPages']),
        Field::make( 'select', 'addons_page', __( 'Page that contains the addons list block', 'vbs' ) )
          ->set_options(['Vbs_Helper', 'getPages']),
        Field::make( 'select', 'customer_page', __( 'Page that contains the customer information block', 'vbs' ) )
          ->set_options(['Vbs_Helper', 'getPages']),
        Field::make( 'select', 'confirmation_page', __( 'Page that contains the booking confirmation block', 'vbs' ) )
          ->set_options(['Vbs_Helper', 'getPages']),
      ])
      ->add_tab( __( 'Vehicle Features', 'vbs' ), [
      	Field::make( 'complex', 'features', __( 'Vehicle Features', 'vbs' ) )
    			->set_layout('tabbed-vertical')
          ->set_collapsed(true)
    			->add_fields([
    				Field::make( 'text', 'name', __( 'Name', 'vbs' ) )
    					->set_required(true),
    				Field::make( 'image', 'icon', __( 'Icon', 'vbs' ) ),
    			])
    			->set_header_template('
            <% if (name) { %>
            	<%- name %>
            <% } %>
          '),
      ])
      ->add_tab( __( 'Vehicle Categories', 'vbs' ), [
      	Field::make( 'complex', 'categories', __( 'Vehicle Categories', 'vbs' ) )
    			->set_layout('tabbed-vertical')
          ->set_collapsed(true)
    			->add_fields([
    				Field::make( 'text', 'name', __( 'Name', 'vbs' ) )
    					->set_required(true),
    			])
    			->set_header_template('
            <% if (name) { %>
            	<%- name %>
            <% } %>
          '),
      ]);

    // Drivers
    Container::make( 'post_meta', __( 'Driver Information', 'vbs' ) )
    	->where( 'post_type', '=', 'driver' )
    	->set_context( 'normal' )
    	->add_fields([
        Field::make( 'text', 'email', __( 'Email', 'vbs' ) ),
        Field::make( 'text', 'phone', __( 'Phone Number', 'vbs' ) )
        	->set_required(true),
        Field::make( 'text', 'license', __( 'License Number', 'vbs' ) ),
        Field::make( 'date', 'dob', __( 'Date of birth', 'vbs' ) )
        	->set_storage_format( 'Y-m-d' ),
        Field::make( 'checkbox', 'active', __( 'Active', 'vbs' ) )
        	->set_option_value('yes'),
    	]);

    Container::make( 'post_meta', __( 'Driver Images', 'vbs' ) )
    	->where( 'post_type', '=', 'driver' )
    	->set_context( 'side' )
    	->add_fields([
        Field::make( 'image', 'photo', __( 'Photo', 'vbs' ) ),
    	]);

    // Vehicles
    Container::make( 'post_meta', __( 'Vehicle Information', 'vbs' ) )
    	->where( 'post_type', '=', 'vehicle' )
    	->set_context( 'normal' )
    	->add_fields([
    		Field::make( 'select', 'category', __( 'Vehicle Category', 'vbs' ) )
    			->set_width(50)
    			->set_options(array_column(carbon_get_theme_option('categories'), 'name')),
    		Field::make( 'select', 'fuel_type', __( 'Fuel type', 'vbs' ) )
    			->set_width(50)
        	->set_options([
        		'diesel' => __( 'Diesel', 'vbs' ),
        		'petrol' => __( 'Petrol', 'vbs' ),
        		'gas'	   => __( 'Gas', 'vbs' ),
        	]),
        Field::make( 'select', 'doors', __( 'Doors', 'vbs' ) )
        	->set_width(25)
        	->set_options([
        		'3' => '3',
        		'5' => '5',
        	])
        	->set_default_value('3'),
        Field::make( 'text', 'seats', __( 'Seats', 'vbs' ) )
        	->set_width(25)
        	->set_attribute('type', 'number')
        	->set_attribute('min', 2)
        	->set_attribute('max', 9)
        	->set_default_value(3),
        Field::make( 'text', 'luggage', __( 'Luggage capacity', 'vbs' ) )
        	->set_width(25)
        	->set_attribute('type', 'number')
        	->set_attribute('min', 1)
        	->set_attribute('max', 10)
        	->set_default_value(1),
        Field::make( 'text', 'available', __( 'Available Vehicles', 'vbs' ) )
        	->set_width(25)
        	->set_attribute('type', 'number')
        	->set_attribute('min', 1)
        	->set_attribute('max', 10)
        	->set_default_value(1),
        Field::make( 'select', 'cost_type', __( 'Cost type', 'vbs' ) )
        	->set_width(20)
        	->set_options([
        		'flat' => __( 'Flat', 'vbs' ),
        		'incr' => __( 'Incremental', 'vbs' ),
        	])
        	->set_default_value('flat'),
        Field::make( 'text', 'flat_cost', __( 'Flat cost', 'vbs' ) )
        	->set_required(true)
        	->set_width(80)
        	->set_conditional_logic([
        		[
            	'field' => 'cost_type',
            	'value' => 'flat',
        		]
    			]),
    		Field::make( 'complex', 'incr_cost', __( 'Incremental cost', 'vbs' ) )
    			->set_width(80)
    			->set_layout('tabbed-vertical')
          ->set_collapsed(true)
          ->set_min(1)
        	->set_conditional_logic([
        		[
            	'field' => 'cost_type',
            	'value' => 'incr',
        		]
    			])
    			->add_fields([
    				Field::make( 'text', 'distance_from', __( 'From distance', 'vbs' ) )
    					->set_required(true)
    					->set_width(33)
    					->set_attribute('type', 'number')
		        	->set_attribute('min', 0),
		        Field::make( 'text', 'distance_to', __( 'To distance', 'vbs' ) )
		        	->set_required(true)
    					->set_width(33)
    					->set_attribute('type', 'number')
		        	->set_attribute('min', 1),
		        Field::make( 'text', 'cost', __( 'Cost', 'vbs' ) )
		        	->set_required(true)
    					->set_width(33),
    			])
    			->set_header_template('
            <% if (distance_from) { %>
            	<%- distance_from %> - <%- distance_to %>
            <% } %>
          '),
        Field::make( 'checkbox', 'active', __( 'Active', 'vbs' ) )
        	->set_option_value('yes'),
    	]);

    Container::make( 'post_meta', __( 'Features', 'vbs' ) )
    	->where( 'post_type', '=', 'vehicle' )
    	->set_context( 'side' )
    	->set_priority( 'low' )
    	->add_fields([
    		Field::make( 'multiselect', 'features', __( 'Features', 'vbs' ) )
        	->set_options(array_column(carbon_get_theme_option('features'), 'name')),
    	]);

    // Addons
    Container::make( 'post_meta', __( 'Addon Information', 'vbs' ) )
    	->where( 'post_type', '=', 'addon' )
    	->set_context( 'normal' )
    	->add_fields([
    		Field::make( 'text', 'cost', __( 'Cost per booking', 'vbs' ) )
    			->set_required(true),
        Field::make( 'association', 'vehicles', __( 'Available on vehicles', 'vbs' ) )
        	->set_types([
			      [
			        'type'      => 'post',
			        'post_type' => 'vehicle',
			      ]
				   ]),
        Field::make( 'checkbox', 'active', __( 'Active', 'vbs' ) )
        	->set_option_value('yes'),
    	]);

    // Bookings
    Container::make( 'post_meta', __( 'Booking Details', 'vbs' ) )
    	->where( 'post_type', '=', 'booking' )
    	->set_context( 'normal' )
    	->add_tab( __( 'Route Information', 'vbs' ), [
        Field::make( 'date_time', 'pickup_datetime', __( 'Pickup Date/Time', 'vbs' ) )
          ->set_picker_options([
            'altInput' => true,
            'enableTime' => true,
            'minDate' => date("Y-m-d H:i:s"),
            'dateFormat' => 'Y-m-d H:i:s',
            'minuteIncrement' => 15,
          ]),
        Field::make( 'text', 'pickup_address', __( 'Pickup Address', 'vbs' ) )
        	->set_width(50),
        Field::make( 'text', 'pickup_address_coordinates', __( 'Pickup Address Coordinates', 'vbs' ) )
          ->set_width(50)
          ->set_attribute( 'readOnly', true ),
        Field::make( 'text', 'dropoff_address', __( 'Dropoff Address', 'vbs' ) )
        	->set_width(50),
        Field::make( 'text', 'dropoff_address_coordinates', __( 'Dropoff Address Coordinates', 'vbs' ) )
          ->set_width(50)
          ->set_attribute( 'readOnly', true ),
        Field::make( 'checkbox', 'return', __( 'Return trip', 'vbs' ) )
        	->set_option_value('yes'),
        Field::make( 'date_time', 'return_datetime', __( 'Return Date/Time', 'vbs' ) )
        	->set_picker_options([
            'altInput' => true,
            'enableTime' => true,
            'minDate' => date("Y-m-d H:i:s"),
            'dateFormat' => 'Y-m-d H:i:s',
            'minuteIncrement' => 15,
          ])
        	->set_conditional_logic([
        		[
            	'field' => 'return',
            	'value' => true,
        		]
    			]),
        Field::make( 'text', 'distance', __( 'Total Distance', 'vbs' ) )
        	->set_width(50)
        	->set_attribute('readOnly', true),
        Field::make( 'text', 'disctance_cost', __( 'Distance Cost', 'vbs' ) )
        	->set_width(50)
        	->set_attribute('readOnly', true),
        Field::make( 'text', 'addons_cost', __( 'Addons Cost', 'vbs' ) )
        	->set_width(33)
        	->set_attribute('readOnly', true),
        Field::make( 'text', 'surcharge_cost', __( 'Surcharge Cost', 'vbs' ) )
        	->set_width(33)
        	->set_attribute('readOnly', true),
        Field::make( 'text', 'total_cost', __( 'Total Cost', 'vbs' ) )
        	->set_width(33)
        	->set_attribute('readOnly', true),
    	])
    	->add_tab( __( 'Route Map', 'vbs' ), [
    		Field::make( 'html', 'map', __( 'Route Map', 'vbs' ) )
    			->set_html('<div id="map" style="width: 100%; height: 500px;"></div>'),
    	])
    	->add_tab( __( 'Vehicle Information', 'vbs' ), [
    		Field::make( 'select', 'vehicle', __( 'Selected Vehicle', 'vbs' ) )
    			->set_width(50)
    			->add_options(['Vbs_Helper', 'getVehicles']),
    		Field::make( 'select', 'driver', __( 'Assigned Driver', 'vbs' ) )
    			->set_width(50)
        	->add_options([
        		'' => '...',
        	])
    			->add_options(['Vbs_Helper', 'getDrivers']),
    		Field::make( 'text', 'persons', __( 'Persons', 'vbs' ) )
    			->set_width(50)
        	->set_attribute('type', 'number')
        	->set_attribute('min', 1)
        	->set_attribute('max', 10)
        	->set_default_value(1),
        Field::make( 'text', 'luggage', __( 'Luggage', 'vbs' ) )
        	->set_width(50)
        	->set_attribute('type', 'number')
        	->set_attribute('min', 1)
        	->set_attribute('max', 10)
        	->set_default_value(1),
    	])
    	->add_tab( __( 'Addons Information', 'vbs' ), [
    		Field::make( 'association', 'addons', __( 'Selected Addons', 'vbs' ) )
    			->set_types([
			      [
			        'type'      => 'post',
			        'post_type' => 'addon',
			      ]
				  ]),
    	])
    	->add_tab( __( 'Customer Information', 'vbs' ), [
    		Field::make( 'text', 'first_name', __( 'First Name', 'vbs' ) )
        	->set_width(50),
        Field::make( 'text', 'last_name', __( 'Last Name', 'vbs' ) )
        	->set_width(50),
        Field::make( 'text', 'email', __( 'Email', 'vbs' ) )
        	->set_width(50),
        Field::make( 'text', 'phone', __( 'Phone Number', 'vbs' ) )
        	->set_width(50),
    		Field::make( 'textarea', 'notes', __( 'Customer Notes', 'vbs' ) ),
    	])
    	->add_tab( __( 'Misc Information', 'vbs' ), [
        Field::make( 'select', 'status', __( 'Booking status', 'vbs' ) )
          ->set_width(50)
          ->add_options([
            'pending' => __( 'Pending', 'vbs' ),
            'confirmed' => __( 'Confirmed', 'vbs' ),
            'complete' => __( 'Complete', 'vbs' ),
            'canceled' => __( 'Canceled', 'vbs' ),
          ])
          ->set_default_value('pending'),
    		Field::make( 'text', 'payment_method', __( 'Payment Method', 'vbs' ) )
        	->set_attribute('readOnly', true),
    	]);

	  // Surcharges
    Container::make( 'post_meta', __( 'Surcharge Conditions', 'vbs' ) )
    	->where( 'post_type', '=', 'surcharge' )
    	->set_context( 'normal' )
    	->add_fields([
    		Field::make( 'radio', 'type', __( 'Type', 'vbs' ) )
        	->set_options([
        		'date' => __( 'Date', 'vbs' ),
        		'location' => __( 'Location', 'vbs' ),
        	]),
        Field::make( 'date', 'date_from', __( 'From date', 'vbs' ) )
        	->set_required(true)
        	->set_width(50)
        	->set_input_format('F j, Y', 'F j, Y')
        	->set_conditional_logic([
        		[
            	'field' => 'type',
            	'value' => 'date',
        		]
    			]),
    		Field::make( 'date', 'date_to', __( 'To date', 'vbs' ) )
    			->set_required(true)
    			->set_width(50)
        	->set_input_format('F j, Y', 'F j, Y')
        	->set_conditional_logic([
        		[
            	'field' => 'type',
            	'value' => 'date',
        		]
    			]),
    		Field::make( 'association', 'location', __( 'Locations', 'vbs' ) )
    			->set_required(true)
    			->set_types([
			      [
			        'type'      => 'post',
			        'post_type' => 'location',
			      ]
			    ])
			    ->set_min(1)
        	->set_conditional_logic([
        		[
            	'field' => 'type',
            	'value' => 'location',
        		]
    			]),
        Field::make( 'radio', 'cost_type', __( 'Cost Type', 'vbs' ) )
          ->set_options([
            'flat' => __( 'Flat Amount', 'vbs' ),
            'percentage' => __( 'Percentage', 'vbs' ),
          ])
          ->set_width(20),
        Field::make( 'text', 'cost', __( 'Cost', 'vbs' ) )
          ->set_required(true)
          ->set_width(50),
        Field::make( 'checkbox', 'active', __( 'Active', 'vbs' ) )
          ->set_option_value('yes')
          ->set_width(30),
    	]);

    // Locations
    Container::make( 'post_meta', __( 'Location Information', 'vbs' ) )
    	->where( 'post_type', '=', 'location' )
    	->set_context( 'normal' )
    	->add_fields([
    		Field::make( 'map', 'address', __( 'Address', 'vbs' ) ),
    		Field::make( 'checkbox', 'active', __( 'Active', 'vbs' ) )
        	->set_option_value('yes'),
    	]);
  }

  public function get_maps_api_key()
  {
  	return carbon_get_theme_option('google_api_key');
  }

  /**
   * Create the Bookings custom post type
   *
   * @link    https://developer.wordpress.org/reference/functions/register_post_type/
   * @since   1.0.0
   */
  public function register_booking_post_type()
  {
  	$labels = [
			'name'               =>   __( 'Bookings', 'vbs' ),
			'singular_name'      =>   __( 'Booking', 'vbs' ),
			'menu_name'          =>   __( 'Bookings', 'vbs' ),
			'name_admin_bar'     =>   __( 'Booking', 'vbs' ),
			'add_new_item'			 =>   __( 'Add Booking', 'vbs' ),
			'edit_item'          =>   __( 'Edit Booking', 'vbs' ),
			'new_item'           =>   __( 'New Booking', 'vbs' ),
			'all_items'          =>   __( 'Bookings', 'vbs' ),
			'view_item'          =>   __( 'View Booking', 'vbs' ),
			'search_items'       =>   __( 'Search Bookings', 'vbs' ),
			'not_found'          =>   __( 'No Bookings found', 'vbs' ),
			'not_found_in_trash' =>   __( 'No Bookings found in Trash', 'vbs' ),
		];

		$args = [
			'hierarchical'       =>  false,
			'labels'             =>  $labels,
			'public'             =>  false,
			'publicly_queryable' =>  false,
			'description'        => __( 'Bookings', 'vbs' ),
			'show_ui'            =>  true,
			'show_in_menu'       =>  'vbs.php',
			'show_in_nav_menus'  =>  false,
			'query_var'          =>  true,
			'rewrite'            =>  false,
			'query_var'          =>  false,
			'capability_type'    =>  'page', //['booking', 'bookings'],
			'has_archive'        =>  false,
			'menu_position'      =>  22,
			'show_in_rest'       =>  false,
			'supports'           =>  ['title'],
		];

		register_post_type('booking', $args);
  }

  /**
   * Create the Vehicles custom post type
   *
   * @link    https://developer.wordpress.org/reference/functions/register_post_type/
   * @since   1.0.0
   */
  public function register_vehicle_post_type()
  {
  	$labels = [
			'name'               =>   __( 'Vehicles', 'vbs' ),
			'singular_name'      =>   __( 'Vehicle', 'vbs' ),
			'menu_name'          =>   __( 'Vehicles', 'vbs' ),
			'name_admin_bar'     =>   __( 'Vehicle', 'vbs' ),
			'add_new_item'			 =>   __( 'Add Vehicle', 'vbs' ),
			'edit_item'          =>   __( 'Edit Vehicle', 'vbs' ),
			'new_item'           =>   __( 'New Vehicle', 'vbs' ),
			'all_items'          =>   __( 'Vehicles', 'vbs' ),
			'view_item'          =>   __( 'View Vehicle', 'vbs' ),
			'search_items'       =>   __( 'Search Vehicles', 'vbs' ),
			'not_found'          =>   __( 'No Vehicles found', 'vbs' ),
			'not_found_in_trash' =>   __( 'No Vehicles found in Trash', 'vbs' ),
		];

		$args = [
			'hierarchical'       =>  false,
			'labels'             =>  $labels,
			'public'             =>  false,
			'publicly_queryable' =>  false,
			'description'        => __( 'Vehicles', 'vbs' ),
			'show_ui'            =>  true,
			'show_in_menu'       =>  'vbs.php',
			'show_in_nav_menus'  =>  false,
			'query_var'          =>  true,
			'rewrite'            =>  false,
			'query_var'          =>  false,
			'capability_type'    =>  'page', //['vehicle', 'vehicles'],
			'has_archive'        =>  false,
			'menu_position'      =>  22,
			'show_in_rest'       =>  false,
			'supports'           =>  ['title', 'editor', 'thumbnail'],
		];

		register_post_type('vehicle', $args);
  }

  /**
   * Create the Drivers custom post type
   *
   * @link    https://developer.wordpress.org/reference/functions/register_post_type/
   * @since   1.0.0
   */
  public function register_driver_post_type()
  {
  	$labels = [
			'name'               =>   __( 'Drivers', 'vbs' ),
			'singular_name'      =>   __( 'Driver', 'vbs' ),
			'menu_name'          =>   __( 'Drivers', 'vbs' ),
			'name_admin_bar'     =>   __( 'Driver', 'vbs' ),
			'add_new_item'			 =>   __( 'Add Driver', 'vbs' ),
			'edit_item'          =>   __( 'Edit Driver', 'vbs' ),
			'new_item'           =>   __( 'New Driver', 'vbs' ),
			'all_items'          =>   __( 'Drivers', 'vbs' ),
			'view_item'          =>   __( 'View Driver', 'vbs' ),
			'search_items'       =>   __( 'Search Drivers', 'vbs' ),
			'not_found'          =>   __( 'No Drivers found', 'vbs' ),
			'not_found_in_trash' =>   __( 'No Drivers found in Trash', 'vbs' ),
		];

		$args = [
			'hierarchical'       =>  false,
			'labels'             =>  $labels,
			'public'             =>  false,
			'publicly_queryable' =>  false,
			'description'        => __( 'Drivers', 'vbs' ),
			'show_ui'            =>  true,
			'show_in_menu'       =>  'vbs.php',
			'show_in_nav_menus'  =>  false,
			'query_var'          =>  true,
			'rewrite'            =>  false,
			'query_var'          =>  false,
			'capability_type'    =>  'page', //['driver', 'drivers'],
			'has_archive'        =>  false,
			'menu_position'      =>  22,
			'show_in_rest'       =>  false,
			'supports'           =>  ['title'],
		];

		register_post_type('driver', $args);
  }

  /**
   * Create the Locations custom post type
   *
   * @link    https://developer.wordpress.org/reference/functions/register_post_type/
   * @since   1.0.0
   */
  public function register_location_post_type()
  {
  	$labels = [
			'name'               =>   __( 'Locations', 'vbs' ),
			'singular_name'      =>   __( 'Location', 'vbs' ),
			'menu_name'          =>   __( 'Locations', 'vbs' ),
			'name_admin_bar'     =>   __( 'Location', 'vbs' ),
			'add_new_item'			 =>   __( 'Add Location', 'vbs' ),
			'edit_item'          =>   __( 'Edit Location', 'vbs' ),
			'new_item'           =>   __( 'New Location', 'vbs' ),
			'all_items'          =>   __( 'Locations', 'vbs' ),
			'view_item'          =>   __( 'View Location', 'vbs' ),
			'search_items'       =>   __( 'Search Locations', 'vbs' ),
			'not_found'          =>   __( 'No Locations found', 'vbs' ),
			'not_found_in_trash' =>   __( 'No Locations found in Trash', 'vbs' ),
		];

		$args = [
			'hierarchical'       =>  false,
			'labels'             =>  $labels,
			'public'             =>  false,
			'publicly_queryable' =>  false,
			'description'        => __( 'Locations', 'vbs' ),
			'show_ui'            =>  true,
			'show_in_menu'       =>  'vbs.php',
			'show_in_nav_menus'  =>  false,
			'query_var'          =>  true,
			'rewrite'            =>  false,
			'query_var'          =>  false,
			'capability_type'    =>  'page', //['location', 'locations'],
			'has_archive'        =>  false,
			'menu_position'      =>  22,
			'show_in_rest'       =>  false,
			'supports'           =>  ['title'],
		];

		register_post_type('location', $args);
  }

  /**
   * Create the Surcharges custom post type
   *
   * @link    https://developer.wordpress.org/reference/functions/register_post_type/
   * @since   1.0.0
   */
  public function register_surcharge_post_type()
  {
  	$labels = [
			'name'               =>   __( 'Surcharges', 'vbs' ),
			'singular_name'      =>   __( 'Surcharge', 'vbs' ),
			'menu_name'          =>   __( 'Surcharges', 'vbs' ),
			'name_admin_bar'     =>   __( 'Surcharge', 'vbs' ),
			'add_new_item'			 =>   __( 'Add Surcharge', 'vbs' ),
			'edit_item'          =>   __( 'Edit Surcharge', 'vbs' ),
			'new_item'           =>   __( 'New Surcharge', 'vbs' ),
			'all_items'          =>   __( 'Surcharges', 'vbs' ),
			'view_item'          =>   __( 'View Surcharge', 'vbs' ),
			'search_items'       =>   __( 'Search Surcharges', 'vbs' ),
			'not_found'          =>   __( 'No Surcharges found', 'vbs' ),
			'not_found_in_trash' =>   __( 'No Surcharges found in Trash', 'vbs' ),
		];

		$args = [
			'hierarchical'       =>  false,
			'labels'             =>  $labels,
			'public'             =>  false,
			'publicly_queryable' =>  false,
			'description'        => __( 'Surcharges', 'vbs' ),
			'show_ui'            =>  true,
			'show_in_menu'       =>  'vbs.php',
			'show_in_nav_menus'  =>  false,
			'query_var'          =>  true,
			'rewrite'            =>  false,
			'query_var'          =>  false,
			'capability_type'    =>  'page', //['surcharge', 'surcharges'],
			'has_archive'        =>  false,
			'menu_position'      =>  22,
			'show_in_rest'       =>  false,
			'supports'           =>  ['title'],
		];

		register_post_type('surcharge', $args);
  }

  /**
   * Create the Addons custom post type
   *
   * @link    https://developer.wordpress.org/reference/functions/register_post_type/
   * @since   1.0.0
   */
  public function register_addon_post_type()
  {
  	$labels = [
			'name'               =>   __( 'Addons', 'vbs' ),
			'singular_name'      =>   __( 'Addon', 'vbs' ),
			'menu_name'          =>   __( 'Addons', 'vbs' ),
			'name_admin_bar'     =>   __( 'Addon', 'vbs' ),
			'add_new_item'			 =>   __( 'Add Addon', 'vbs' ),
			'edit_item'          =>   __( 'Edit Addon', 'vbs' ),
			'new_item'           =>   __( 'New Addon', 'vbs' ),
			'all_items'          =>   __( 'Addons', 'vbs' ),
			'view_item'          =>   __( 'View Addon', 'vbs' ),
			'search_items'       =>   __( 'Search Addons', 'vbs' ),
			'not_found'          =>   __( 'No Addons found', 'vbs' ),
			'not_found_in_trash' =>   __( 'No Addons found in Trash', 'vbs' ),
		];

		$args = [
			'hierarchical'       =>  false,
			'labels'             =>  $labels,
			'public'             =>  false,
			'publicly_queryable' =>  false,
			'description'        => __( 'Addons', 'vbs' ),
			'show_ui'            =>  true,
			'show_in_menu'       =>  'vbs.php',
			'show_in_nav_menus'  =>  false,
			'query_var'          =>  true,
			'rewrite'            =>  false,
			'query_var'          =>  false,
			'capability_type'    =>  'page', //['surcharge', 'surcharges'],
			'has_archive'        =>  false,
			'menu_position'      =>  22,
			'show_in_rest'       =>  false,
			'supports'           =>  ['title', 'editor', 'thumbnail'],
		];

		register_post_type('addon', $args);
  }

  /**
   * Modify columns for the bookings list custom post type
   *
   * @link    https://developer.wordpress.org/reference/hooks/manage_post_type_posts_columns/
   * @since   1.0.0
   *
   * @param  array  $columns Existing columns array
   * @return array           Updated columns array
   */
  public function bookings_columns( array $columns )
  {
  	unset($columns['date']);

  	return array_merge($columns, [
  		'dates' => __( 'Pickup/Return Date', 'vbs' ),
  		'vehicle' => __( 'Vehicle', 'vbs' ),
  		'driver' => __( 'Driver', 'vbs' ),
  		'customer' => __( 'Customer', 'vbs' ),
  		'cost' => __( 'Cost', 'vbs' ),
  		'date' => __( 'Date', 'vbs' ),
  	]);
  }

  /**
   * Renderer for the content of each custom column
   *
   * @link    https://developer.wordpress.org/reference/hooks/manage_post-post_type_posts_custom_column/
   * @since   1.0.0
   *
   * @param  string $column  Column key
   * @param  int    $post_id Current post id
   * @return void
   */
  public function booking_columns_renderer( string $column, int $post_id )
  {
  	switch ( $column ) {
  		case 'dates':
  			$pickup = date('F j, Y H:i:s', strtotime(carbon_get_post_meta($post_id, 'pickup_datetime')));
  			$dropoff = carbon_get_post_meta($post_id, 'return_datetime') ? date('F j, Y H:i:s', strtotime(carbon_get_post_meta($post_id, 'return_datetime'))) : '';
  			echo sprintf('%s</br>%s', $pickup, $dropoff);
  			break;
  		case 'vehicle':
  			echo get_the_title(carbon_get_post_meta($post_id, 'vehicle'));
  			break;
  		case 'driver':
  			echo carbon_get_post_meta($post_id, 'driver') ? get_the_title(carbon_get_post_meta($post_id, 'driver')) : __( 'Unassinged', 'vbs' );
  			break;
  		case 'customer':
  			echo sprintf('%s %s', carbon_get_post_meta($post_id, 'first_name'), carbon_get_post_meta($post_id, 'last_name'));
  			break;
  		case 'cost':
  			echo sprintf('%s %.2f', carbon_get_theme_option('currency'), carbon_get_post_meta($post_id, 'total_cost'));
  			break;
  		default:
  			break;
  	}
  }

  /**
   * Add custom query parameters to be used by blocks to load data
   *
   * @link    https://developer.wordpress.org/reference/hooks/query_vars/
   * @since   1.0.0
   * @param   array  $query_vars Original array of query vars
   * @return  array              Updated array with custom vars
   */
  public function register_query_vars( array $query_vars )
  {
    $query_vars[] = 'search';

    return $query_vars;
  }

}
