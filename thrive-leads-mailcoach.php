<?php
/**
 * Plugin Name: Thrive Leads - Mailcoach Integration
 * Plugin URI: https://yourdomain.com/plugins/thrive-leads-mailcoach
 * Description: Adds Mailcoach integration to Thrive Leads
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourdomain.com
 * License: GPL-2.0+
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class Thrive_Leads_Mailcoach_Integration {
    
    /**
     * Initialize the plugin
     */
    public function __construct() {
        // Wait for Thrive Dashboard to be loaded
        add_action('thrive_dashboard_loaded', array($this, 'init'));
    }
    
    /**
     * Initialize integration after Thrive Dashboard is loaded
     */
    public function init() {
        // Check if Thrive Leads is active
        if (!class_exists('Thrive_Leads_Plugin')) {
            return;
        }
        
        // Register our integration with the List Manager
        add_filter('tvd_api_available_connections', array($this, 'register_connection'));
        
        // Add our files directory to autoload paths
        add_filter('tve_dash_autoload_paths', array($this, 'add_autoload_paths'));
        
        // Register our API class
        $this->register_api_class();
    }
    
    /**
     * Register Mailcoach with the available connections
     * 
     * @param array $connections
     * @return array
     */
    public function register_connection($connections) {
        // Add Mailcoach to the available connections list
        $connections['mailcoach'] = 'Thrive_Dash_List_Connection_Mailcoach';
        
        return $connections;
    }
    
    /**
     * Add our directories to the autoload paths
     * 
     * @param array $paths
     * @return array
     */
    public function add_autoload_paths($paths) {
        // Add our directories to the autoload paths
        $paths[] = dirname(__FILE__) . '/inc/auto-responder/';
        
        return $paths;
    }
    
    /**
     * Register the API class and copy necessary files
     */
    public function register_api_class() {
        // Define file paths
        $api_class_file = dirname(__FILE__) . '/inc/auto-responder/lib/vendor/Mailcoach/Api.php';
        $connection_file = dirname(__FILE__) . '/inc/auto-responder/classes/Connection/Mailcoach.php';
        $setup_view_file = dirname(__FILE__) . '/inc/auto-responder/views/setup/mailcoach.php';
        $logo_file = dirname(__FILE__) . '/inc/auto-responder/views/images/mailcoach.png';
        
        // Create necessary directories
        $this->create_directories();
        
        // Include our API class
        if (file_exists($api_class_file)) {
            include_once $api_class_file;
        }
        
        // Include our connection class
        if (file_exists($connection_file)) {
            include_once $connection_file;
        }
        
        // Copy the setup view to the Thrive Dashboard location
        $this->maybe_copy_file(
            $setup_view_file,
            WP_PLUGIN_DIR . '/thrive-dashboard/inc/auto-responder/views/setup/mailcoach.php'
        );
        
        // Copy the logo to the Thrive Dashboard location
        $this->maybe_copy_file(
            $logo_file,
            WP_PLUGIN_DIR . '/thrive-dashboard/inc/auto-responder/views/images/mailcoach.png'
        );
    }
    
    /**
     * Create necessary directories
     */
    protected function create_directories() {
        // Create the directories for our classes
        $this->maybe_create_dir(dirname(__FILE__) . '/inc/auto-responder/lib/vendor/Mailcoach');
        $this->maybe_create_dir(dirname(__FILE__) . '/inc/auto-responder/classes/Connection');
        $this->maybe_create_dir(dirname(__FILE__) . '/inc/auto-responder/views/setup');
        $this->maybe_create_dir(dirname(__FILE__) . '/inc/auto-responder/views/images');
    }
    
    /**
     * Create directory if it doesn't exist
     * 
     * @param string $dir
     */
    protected function maybe_create_dir($dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    /**
     * Copy file if it doesn't exist or is outdated
     * 
     * @param string $source
     * @param string $destination
     */
    protected function maybe_copy_file($source, $destination) {
        // Create destination directory if it doesn't exist
        $dest_dir = dirname($destination);
        if (!file_exists($dest_dir)) {
            mkdir($dest_dir, 0755, true);
        }
        
        // Copy the file if it doesn't exist or if our version is newer
        if (!file_exists($destination) || filemtime($source) > filemtime($destination)) {
            copy($source, $destination);
        }
    }
}

// Initialize the plugin
new Thrive_Leads_Mailcoach_Integration();
