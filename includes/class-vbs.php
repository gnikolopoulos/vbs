<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.interactive-design.gr
 * @since      1.0.0
 *
 * @package    Vbs
 * @subpackage Vbs/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Vbs
 * @subpackage Vbs/includes
 * @author     George Nikolopoulos <georgen@interactive-design.gr>
 */
class Vbs
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Vbs_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if ( defined( 'VBS_VERSION' ) ) {
			$this->version = VBS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'vbs';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Vbs_Loader. Orchestrates the hooks of the plugin.
	 * - Vbs_i18n. Defines internationalization functionality.
	 * - Vbs_Admin. Defines all hooks for the admin area.
	 * - Vbs_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vbs-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vbs-i18n.php';

		/**
		 * The class responsible for providing helper function that are used throughout the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'helper/class-vbs-helper.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-vbs-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-vbs-public.php';

		/**
		 * The class responsible for making sure that other plugins required are installed and activated
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'lib/class-tgm-plugin-activation.php';

		$this->loader = new Vbs_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Vbs_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{
		$plugin_i18n = new Vbs_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$plugin_admin = new Vbs_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'tgmpa_register', $plugin_admin, 'register_required_plugins' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_admin_menu' );
		$this->loader->add_action( 'carbon_fields_register_fields', $plugin_admin, 'register_containers' );
		$this->loader->add_filter( 'carbon_fields_map_field_api_key', $plugin_admin, 'get_maps_api_key' );
		$this->loader->add_action( 'init', $plugin_admin, 'register_booking_post_type' );
		$this->loader->add_action( 'init', $plugin_admin, 'register_vehicle_post_type' );
		$this->loader->add_action( 'init', $plugin_admin, 'register_driver_post_type' );
		$this->loader->add_action( 'init', $plugin_admin, 'register_location_post_type' );
		$this->loader->add_action( 'init', $plugin_admin, 'register_surcharge_post_type' );
		$this->loader->add_action( 'init', $plugin_admin, 'register_addon_post_type' );

		$this->loader->add_filter( 'query_vars', $plugin_admin, 'register_query_vars' );

		$this->loader->add_filter( 'manage_booking_posts_columns', $plugin_admin, 'bookings_columns' );
		$this->loader->add_action( 'manage_booking_posts_custom_column', $plugin_admin, 'booking_columns_renderer', 10, 2 );

		$this->loader->add_action( 'admin_print_scripts-post.php', $plugin_admin, 'add_maps_js' );

		//$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		//$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$plugin_public = new Vbs_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'carbon_fields_register_fields', $plugin_public, 'register_blocks' );

		$this->loader->add_action( 'wp_ajax_initiate_search', $plugin_public, 'initiate_search' );
		$this->loader->add_action( 'wp_ajax_nopriv_initiate_search', $plugin_public, 'initiate_search' );

		$this->loader->add_action( 'wp_ajax_select_vehicle', $plugin_public, 'select_vehicle' );
		$this->loader->add_action( 'wp_ajax_nopriv_select_vehicle', $plugin_public, 'select_vehicle' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Vbs_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}

}
