<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://ri7nz.github.io/
 * @since      1.0.0
 *
 * @package    Tpid
 * @subpackage Tpid/includes
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
 * @package    Tpid
 * @subpackage Tpid/includes
 * @author     ri7nz.github.io <24h-support@ri7nz.github.io>
 */
class Tpid {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Tpid_Loader    $loader    Maintains and registers all hooks for the plugin.
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
     * @since  1.0.0
     * @access protected
     * @var    array $modules Daftar module yang akan di load oleh loader
     * @author <ri7nz@ri7nz.github.io>
     * @create 9 April 2018
     */
    protected $modules; 

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
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'tpid';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

    /**
     * @since 1.0.0
     * @method setModule  untuk mengatur nilai dari $this->modules
     * @access public 
     * @author <ri7nz@ri7nz.github.io>
     * @create 9 April 2018
     */
    public function setModules(){
        $modules = [
            //'MyCustom' => new MyCostumClass();
        ];
        
        /**
         * Mengubah dari Array Associative menjadi Object
         */
        $this->modules = (object) $modules; 
    }

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Tpid_Loader. Orchestrates the hooks of the plugin.
	 * - Tpid_i18n. Defines internationalization functionality.
	 * - Tpid_Admin. Defines all hooks for the admin area.
	 * - Tpid_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

        /**
         * @var array $dependencies berisikan filename dari module atau library
         * @author <ri7nz@ri7nz.github.io>
         * @create 9 April 2018
         */
        $dependencies = [
		    /**
		     * The class responsible for orchestrating the actions and filters of the
		     * core plugin.
		     */
		    'includes/class-tpid-loader.php',

		    /**
		     * The class responsible for defining internationalization functionality
		     * of the plugin.
		     */
		     'includes/class-tpid-i18n.php',

		    /**
		     * The class responsible for defining all actions that occur in the admin area.
		     */
		    'admin/class-tpid-admin.php',

		    /**
		     * The class responsible for defining all actions that occur in the public-facing
		     * side of the site.
		     */
            'public/class-tpid-public.php',

            /**
             * Menambahkan Class/Library Baru 
             * Contoh :
             */
             //'custom/My-Class.php',

        ];
        /**
         * Memuat list library yang telah diatur pada variable $dependencies
         */ 
        foreach ($dependencies as $lib){
            require_once plugin_dir_path( dirname( __FILE__ ) ) . $lib;
        }
        /**
         * Setelah memuat libarary didalam function ini selanjutnya 
         * ditampung pada property/attribute $this->loader 
         */
		$this->loader = new Tpid_Loader();

        /**
         * Memanggil function setModule 
         */
        $this->setModules();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Tpid_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Tpid_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Tpid_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

        /**
         * Contoh: Memanggil Modules yang diatur pada $this->setModules
         *
         * $MyCustomClass = $this->modules->MyCostumClass 
         *
         * //Contoh Untuk Menambah Action pada wordpress dari module yang telah dibuat
         * $this->loader->add_action( 'init', $MyCustomClass, 'MyCustomClassFunctionActionForWp');
         *
         * //Contoh untuk menambahkan filter pada wordpress dari module yang telah dibuat 
         * $this->loader->add_filter( 'filter_costum', $MyCustomClass, 'MyCustomClassFunctionFilter', 10, 2);
         *
         * cara diatas dapat digunakan pada method define_public_hooks() 
         */

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Tpid_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Tpid_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
