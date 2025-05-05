<?php
/**
 * Mock Thrive Leads Plugin class for testing
 *
 * @package Thrive_Leads_Mailcoach_Integration
 */

if ( ! class_exists( 'Thrive_Leads_Plugin' ) ) {

    /**
     * Class Thrive_Leads_Plugin
     */
    class Thrive_Leads_Plugin {

        /**
         * Constructor
         */
        public function __construct() {
            // Mock constructor
        }

        /**
         * Register connection
         *
         * @param string $key Connection key
         * @param string $class Connection class
         */
        public function register_connection( $key, $class ) {
            return true;
        }
    }
}