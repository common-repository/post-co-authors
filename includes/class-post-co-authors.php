<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://acquaintsoft.com/
 * @since      1.0.0
 *
 * @package    Post_Co_Authors
 * @subpackage Post_Co_Authors/includes
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
 * @package    Post_Co_Authors
 * @subpackage Post_Co_Authors/includes
 * @author     Post Co-Authors <post-co-author@gmail.com>
 */
class Post_Co_Authors {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Post_Co_Authors_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	public function __construct() {

		$this->plugin_name = 'post-co-authors';
		$this->version = '1.0.0';

		$this->pcauthor_load_dependencies();
		$this->pcauthor_set_locale();
		$this->pcauthor_define_admin_hooks();
		$this->pcauthor_define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Post_Co_Authors_Loader. Orchestrates the hooks of the plugin.
	 * - Post_Co_Authors_i18n. Defines internationalization functionality.
	 * - Post_Co_Authors_Admin. Defines all hooks for the admin area.
	 * - Post_Co_Authors_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pcauthor_load_dependencies() {

		/**
		 * Include the class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-post-co-authors-loader.php';

		/**
		 * Include the class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-post-co-authors-i18n.php';

		/**
		 * Include the class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-post-co-authors-admin.php';

		/**
		 * Include the class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-post-co-authors-public.php';

		$this->loader = new Post_Co_Authors_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Post_Co_Authors_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pcauthor_set_locale() {

		$plugin_i18n = new Post_Co_Authors_i18n();

		$this->loader->pcauthor_add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pcauthor_define_admin_hooks() {

		$plugin_admin = new Post_Co_Authors_Admin( $this->pcauthor_get_plugin_name(), $this->pcauthor_get_version() );

		$this->loader->pcauthor_add_action( 'admin_enqueue_scripts', $plugin_admin, 'pcauthor_enqueue_styles' );
		$this->loader->pcauthor_add_action( 'admin_enqueue_scripts', $plugin_admin, 'pcauthor_enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pcauthor_define_public_hooks() {

		$plugin_public = new Post_Co_Authors_Public( $this->pcauthor_get_plugin_name(), $this->pcauthor_get_version() );

		$this->loader->pcauthor_add_action( 'wp_enqueue_scripts', $plugin_public, 'pcauthor_enqueue_styles' );
		$this->loader->pcauthor_add_action( 'wp_enqueue_scripts', $plugin_public, 'pcauthor_enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function pcauthor_run() {
		$this->loader->pcauthor_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function pcauthor_get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Post_Co_Authors_Loader    Orchestrates the hooks of the plugin.
	 */
	public function pcauthor_get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function pcauthor_get_version() {
		return $this->version;
	}

}
