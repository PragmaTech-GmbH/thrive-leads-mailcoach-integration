<?php
/**
 * Mock for Thrive_Leads_Mailcoach_Integration class
 *
 * @package Thrive_Leads_Mailcoach_Integration
 */

if ( ! class_exists( 'Thrive_Leads_Mailcoach_Integration' ) ) {

    /**
     * Class Thrive_Leads_Mailcoach_Integration
     */
    class Thrive_Leads_Mailcoach_Integration {

        /**
         * Constructor
         */
        public function __construct() {
            // Mock constructor
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
            $paths[] = dirname(dirname(dirname(__FILE__))) . '/inc/auto-responder/';
            
            return $paths;
        }
    }
}