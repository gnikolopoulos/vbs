<?php

/**
 * Class that provides various helper functions
 *
 * @link       https://www.interactive-design.gr
 * @since      1.0.0
 * @package    Vbs
 * @subpackage Vbs/helper
 * @author     George Nikolopoulos <georgen@interactive-design.gr>
 */
class Vbs_Helper
{

  /**
   * Returns an array of key => value pairs of all pages
   *
   * @since    1.0.0
   * @return   array
   */
  public static function getPages()
  {
    $args = [
      'post_type' => 'page',
      'post_status' => 'publish',
      'posts_per_page' => -1,
      'orderby' => 'title',
      'order' => 'ASC',
    ];

    return array_column(get_posts( $args ), 'post_title', 'ID');
  }

  /**
   * Returns an array of key => value pairs of all active drivers
   *
   * @since    1.0.0
   * @return   array
   */
  public static function getDrivers()
  {
    $args = [
      'post_type' => 'driver',
      'post_status' => 'publish',
      'posts_per_page' => -1,
      'orderby' => 'title',
      'order' => 'ASC',
      'meta_query' => [
        [
          'key' => '_active',
          'value' => 'yes',
        ],
      ],
    ];

    return array_column(get_posts( $args ), 'post_title', 'ID');
  }

  /**
   * Returns an array of key => value pairs of all active vehicles
   *
   * @since    1.0.0
   * @return   array
   */
  public static function getVehicles()
  {
    $args = [
      'post_type' => 'vehicle',
      'post_status' => 'publish',
      'posts_per_page' => -1,
      'orderby' => 'title',
      'order' => 'ASC',
      'meta_query' => [
        [
          'key' => '_active',
          'value' => 'yes',
        ],
      ],
    ];

    return array_column(get_posts( $args ), 'post_title', 'ID');
  }

  /**
   * Returns an array of key => value pairs of all active addons
   *
   * @since    1.0.0
   *
   * @param    int    $vehicle_id    Vehicle id to get addons for
   * @return   array
   */
  public static function getAddons( int $vehicle_id )
  {
    $args = [
      'post_type' => 'addon',
      'post_status' => 'publish',
      'posts_per_page' => -1,
      'orderby' => 'title',
      'order' => 'ASC',
      'meta_query' => [
        [
          'value' => 'post:vehicle:' . $vehicle_id,
        ],
        [
          'key' => '_active',
          'value' => 'yes',
        ],
      ],
    ];

    return array_column(get_posts( $args ), 'post_title', 'ID');
  }

  /**
   * Returns an array of key => value pairs of all active locations
   *
   * @since    1.0.0
   * @return   array
   */
  public static function getLocations()
  {
    $args = [
      'post_type' => 'location',
      'post_status' => 'publish',
      'posts_per_page' => -1,
      'orderby' => 'title',
      'order' => 'ASC',
      'meta_query' => [
        [
          'key' => '_active',
          'value' => 'yes',
        ],
      ],
    ];

    return array_column(get_posts( $args ), 'post_title', 'ID');
  }

  /**
   * Returns an array of vehicles available using the current parameters.
   * In order for a vehicle to be available the following have to be TRUE:
   *   - Vehicle should be marked as active
   *   - Vehicle should not appear in a booking with the same pickup or return date
   *   - Vehicle's available seats must be equal or less than the number of passengers
   *
   * @since  1.0.0
   *
   * @param  array  $params Search parameters
   * @return array          Associative array of query results
   */
  public function getAvailableVehicles( array $params )
  {
    global $wpdb;
    $query = "SELECT ID,
        cars.meta_value AS available,
        IFNULL(bookings.bookings_count, 0) AS booked
    FROM $wpdb->posts vehicles
    INNER JOIN $wpdb->postmeta active ON active.post_id = vehicles.ID AND active.meta_key = '_active' AND active.meta_value = 'yes'
    INNER JOIN $wpdb->postmeta seats ON seats.post_id = vehicles.ID AND seats.meta_key = '_seats' AND seats.meta_value >= %d
    INNER JOIN $wpdb->postmeta cars ON cars.post_id = vehicles.ID AND cars.meta_key = '_available'
    LEFT JOIN (
        SELECT vehicle.meta_value AS selected_vehicle,
          COUNT(*) AS bookings_count
        FROM $wpdb->posts
        INNER JOIN $wpdb->postmeta vehicle ON vehicle.post_id = $wpdb->posts.ID AND vehicle.meta_key = '_vehicle'
        INNER JOIN $wpdb->postmeta pickup_date ON pickup_date.post_id = $wpdb->posts.ID AND pickup_date.meta_key = '_pickup_datetime'
        LEFT JOIN $wpdb->postmeta return_date ON return_date.post_id = $wpdb->posts.ID AND return_date.meta_key = '_return_datetime'
        WHERE $wpdb->posts.post_type = 'booking'
          AND $wpdb->posts.post_status = 'publish'
          AND (pickup_date.meta_value = %s OR pickup_date.meta_value = %s)
          OR (return_date.meta_value = %s OR return_date.meta_value = %s)
        GROUP BY $wpdb->posts.ID
    ) AS bookings ON bookings.selected_vehicle = vehicles.ID
    WHERE vehicles.post_type = 'vehicle'
      AND vehicles.post_status = 'publish'
    HAVING available - booked > 0;";
    $sql_params = [
      (int)$params['passengers'],
      $params['pickup_datetime'],
      $params['return_datetime'] != '' ? $params['return_datetime'] : $params['pickup_datetime'],
      $params['pickup_datetime'],
      $params['return_datetime'] != '' ? $params['return_datetime'] : $params['pickup_datetime'],
    ];

    return $wpdb->get_results( $wpdb->prepare( $query, $sql_params ), ARRAY_A );
  }

  /**
   * Formats transient data in a new array to be passed on to the dinstance calculation function
   *
   * @since    1.0.0
   *
   * @param    array    $data   Transient data
   * @return   array            Formatted array
   */
  public function formatDataForDistance( array $data )
  {
    return [
      'pickup' => [
        $data['pickup']['lat'],
        $data['pickup']['lng'],
      ],
      'dropoff' => [
        $data['dropoff']['lat'],
        $data['dropoff']['lng'],
      ],
    ];
  }

  /**
   * Use Google Distance Matrix API to get the distance between the pickup and dropoff locations
   * Distance is always returned in meters
   *
   * @link    https://developer.wordpress.org/reference/functions/wp_remote_get/
   * @since   1.0.0
   *
   * @param  array  $params Address parameters
   * @return array          Distance Matrix API response
   */
  public function getDistance( array $params )
  {
    $request_params = [
      'destinations' => implode(',', $params['dropoff']),
      'origins' => implode(',', $params['pickup']),
      'key' => carbon_get_theme_option('google_api_key'),
    ];

    $response = wp_remote_get( 'https://maps.googleapis.com/maps/api/distancematrix/json?' . http_build_query( $request_params ) );

    if ( wp_remote_retrieve_response_code($response) == 200 ) {
      $response_data = json_decode( wp_remote_retrieve_body( $response ) );
      return $response_data->rows[0]->elements[0]->distance->value;
    } else {
      return json_decode( wp_remote_retrieve_body( $response ) );
    }
  }

  /**
   * Calculate to total price of the ride based on the vehicle's pricinf policy (flat or incremental)
   *
   * @since    1.0.0
   *
   * @param    int    $vehicle  Vehicle id
   * @param    int    $distance Total distance in METERS
   * @return   float            Total price
   */
  public function calculatePrice( int $vehicle, int $distance )
  {
    $vehicle_cost_type = carbon_get_post_meta( $vehicle, 'cost_type' );
    if ( $vehicle_cost_type == 'flat' ) {
      return round(($distance/1000) * carbon_get_post_meta( $vehicle, 'flat_cost' ), 2);
    }

    $vehile_incremental_cost = carbon_get_post_meta( $vehicle, 'incr_cost' );
    $matching_entry = array_values(array_filter($vehile_incremental_cost, function($item) use ($distance) {
      if ( $distance/1000 >= (int)$item['distance_from'] && $distance/1000 <= (int)$item['distance_to'] ) {
        return $item;
      }
    }));

    return round(($distance/1000) * $matching_entry[0]['cost'], 2);
  }

  /**
   * Display the given price based on settings
   *
   * @since    1.0.0
   * @param    float    $price    Input price to format
   * @return   string             Formatted price
   */
  public function formatPrice( float $price )
  {
    $symbol = '$';
    switch (carbon_get_theme_option( 'currency' )) {
      case 'eur':
        $symbol = '€';
        break;
      case 'usd':
        $symbol = '$';
        break;
      default:
        break;
    }
    return sprintf('%s%s', $symbol, number_format($price, 2, ',', ''));
  }

  /**
   * Returns an array of the enabled payment methods
   *
   * @since    1.0.0
   * @return   array              Methods array
   */
  public function getPaymentMethods()
  {
    $methods = [];

    if (carbon_get_theme_option('cash_enabled') == 'yes') {
      $methods[] = [
        'id' => 'cash',
        'title' => carbon_get_theme_option('cash_title'),
        'description' => carbon_get_theme_option('cash_description'),
        'icon' => VBS_BASE_PATH . 'public/img/icons/cash.svg',
      ];
    }

    if (carbon_get_theme_option('stripe_enabled') == 'yes') {
      $methods[] = [
        'id' => 'stripe',
        'title' => carbon_get_theme_option('stripe_title'),
        'description' => carbon_get_theme_option('stripe_description'),
        'icon' => VBS_BASE_PATH . 'public/img/icons/credit-card.svg',
      ];
    }

    if (carbon_get_theme_option('paypal_enabled') == 'yes') {
      $methods[] = [
        'id' => 'paypal',
        'title' => carbon_get_theme_option('paypal_title'),
        'description' => carbon_get_theme_option('paypal_description'),
        'icon' => VBS_BASE_PATH . 'public/img/icons/paypal.svg',
      ];
    }

    return $methods;
  }

}
