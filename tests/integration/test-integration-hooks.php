<?php
/**
 * Integration tests for WordPress hooks
 *
 * @package Thrive_Leads_Mailcoach_Integration
 */

// The main plugin class is loaded from mocks in the bootstrap

use Brain\Monkey\Functions;

/**
 * Class Test_Integration_Hooks
 */
class Test_Integration_Hooks extends WP_UnitTestCase {

    /**
     * Set up test
     */
    public function setUp(): void {
        parent::setUp();
        
        // Mock global $wp_filter
        global $wp_filter;
        $wp_filter = [];
        $wp_filter['thrive_dashboard_loaded'] = new stdClass();
        $wp_filter['thrive_dashboard_loaded']->callbacks = [
            10 => [
                'plugin_callback' => [
                    'function' => [new Thrive_Leads_Mailcoach_Integration(), 'init'],
                    'accepted_args' => 1
                ]
            ]
        ];
    }

    /**
     * Test plugin initialization
     */
    public function test_plugin_init_hooks() {
        global $wp_filter;
        
        // Check if our hook is registered
        $this->assertArrayHasKey( 'thrive_dashboard_loaded', $wp_filter );
        
        // Find our callback in the registered hooks
        $found = false;
        foreach ( $wp_filter['thrive_dashboard_loaded']->callbacks as $priority => $callbacks ) {
            foreach ( $callbacks as $callback ) {
                if ( is_array( $callback['function'] ) && is_object( $callback['function'][0] ) ) {
                    if ( get_class( $callback['function'][0] ) === 'Thrive_Leads_Mailcoach_Integration' ) {
                        $found = true;
                        break 2;
                    }
                }
            }
        }
        
        $this->assertTrue( $found, 'The plugin init hook was not found' );
    }

    /**
     * Test connection registration filter
     */
    public function test_connection_registration() {
        // Create a mock of the plugin instance
        $plugin = new Thrive_Leads_Mailcoach_Integration();
        
        // Test the filter
        $connections = [];
        $connections = $plugin->register_connection( $connections );
        
        $this->assertArrayHasKey( 'mailcoach', $connections );
        $this->assertEquals( 'Thrive_Dash_List_Connection_Mailcoach', $connections['mailcoach'] );
    }

    /**
     * Test autoload paths filter
     */
    public function test_autoload_paths() {
        // Create a mock of the plugin instance
        $plugin = new Thrive_Leads_Mailcoach_Integration();
        
        // Test the filter
        $paths = [];
        $paths = $plugin->add_autoload_paths( $paths );
        
        $this->assertContains( dirname( dirname( dirname( __FILE__ ) ) ) . '/inc/auto-responder/', $paths );
    }
}