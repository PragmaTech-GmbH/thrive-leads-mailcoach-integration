<?php
/**
 * Unit tests for the Mailcoach API class
 *
 * @package Thrive_Leads_Mailcoach_Integration
 */

use Brain\Monkey\Functions;

/**
 * Class Test_Mailcoach_Api
 */
class Test_Mailcoach_Api extends WP_UnitTestCase {

    /**
     * Set up the test case
     */
    public function setUp(): void {
        parent::setUp();
        // Brain\Monkey is already set up in the bootstrap file
        
        require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/inc/auto-responder/lib/vendor/Mailcoach/Api.php';
    }

    /**
     * Tear down the test case
     */
    public function tearDown(): void {
        // Brain\Monkey tearDown is handled in the bootstrap file
        parent::tearDown();
    }

    /**
     * Test API constructor
     */
    public function test_constructor() {
        $api = new Thrive_Dash_Api_Mailcoach( 'test_key', 'https://example.com' );
        
        $this->assertInstanceOf( 'Thrive_Dash_Api_Mailcoach', $api );
    }

    /**
     * Test get lists method
     */
    public function test_get_lists() {
        // Mock the wp_remote_request function
        Functions\when( 'wp_remote_request' )->justReturn( [
            'response' => [ 'code' => 200 ],
            'body'     => json_encode( [
                'data' => [
                    [
                        'uuid' => '12345',
                        'name' => 'Test List',
                    ],
                ],
            ] ),
        ] );
        
        Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 200 );
        Functions\when( 'wp_remote_retrieve_body' )->justReturn( json_encode( [
            'data' => [
                [
                    'uuid' => '12345',
                    'name' => 'Test List',
                ],
            ],
        ] ) );
        
        Functions\when( 'is_wp_error' )->justReturn( false );

        $api = new Thrive_Dash_Api_Mailcoach( 'test_key', 'https://example.com' );
        $lists = $api->getLists();
        
        $this->assertIsArray( $lists );
        $this->assertArrayHasKey( 'data', $lists );
        $this->assertEquals( '12345', $lists['data'][0]['uuid'] );
        $this->assertEquals( 'Test List', $lists['data'][0]['name'] );
    }

    /**
     * Test add subscriber method
     */
    public function test_add_subscriber() {
        // Mock wp_remote_request to simulate a successful API request
        Functions\when( 'wp_remote_request' )->justReturn( [
            'response' => [ 'code' => 200 ],
            'body'     => json_encode( [
                'uuid'  => 'sub123',
                'email' => 'test@example.com',
            ] ),
        ] );
        
        Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 200 );
        Functions\when( 'wp_remote_retrieve_body' )->justReturn( json_encode( [
            'uuid'  => 'sub123',
            'email' => 'test@example.com',
        ] ) );
        
        Functions\when( 'is_wp_error' )->justReturn( false );

        $api = new Thrive_Dash_Api_Mailcoach( 'test_key', 'https://example.com' );
        
        $subscriber_data = [
            'email'      => 'test@example.com',
            'first_name' => 'Test',
            'last_name'  => 'User',
        ];
        
        $result = $api->addSubscriber( 'list123', $subscriber_data );
        
        $this->assertIsArray( $result );
        $this->assertEquals( 'sub123', $result['uuid'] );
        $this->assertEquals( 'test@example.com', $result['email'] );
    }

    /**
     * Test API error handling
     */
    public function test_api_error_handling() {
        // Mock wp_remote_request to simulate an API error
        Functions\when( 'wp_remote_request' )->justReturn( [
            'response' => [ 'code' => 400 ],
            'body'     => json_encode( [
                'message' => 'Invalid API key',
            ] ),
        ] );
        
        Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 400 );
        Functions\when( 'wp_remote_retrieve_body' )->justReturn( json_encode( [
            'message' => 'Invalid API key',
        ] ) );
        
        Functions\when( 'is_wp_error' )->justReturn( false );

        $api = new Thrive_Dash_Api_Mailcoach( 'invalid_key', 'https://example.com' );
        
        $this->expectException( 'Thrive_Dash_Api_Mailcoach_Exception' );
        $this->expectExceptionMessage( 'Invalid API key' );
        
        $api->getLists();
    }

    /**
     * Test WP error handling
     */
    public function test_wp_error_handling() {
        // Create a simple WP_Error instance
        $wp_error = new WP_Error('http_request_failed', 'Connection failed');

        // Mock wp_remote_request to simulate a WordPress error
        Functions\when( 'wp_remote_request' )->justReturn( $wp_error );
        Functions\when( 'is_wp_error' )->justReturn( true );

        $api = new Thrive_Dash_Api_Mailcoach( 'test_key', 'https://example.com' );
        
        $this->expectException( 'Thrive_Dash_Api_Mailcoach_Exception' );
        $this->expectExceptionMessage( 'Connection failed' );
        
        $api->getLists();
    }

    /**
     * Test get custom fields
     */
    public function test_get_custom_fields() {
        // Mock the wp_remote_request function
        Functions\when( 'wp_remote_request' )->justReturn( [
            'response' => [ 'code' => 200 ],
            'body'     => json_encode( [
                'data' => [
                    [
                        'name' => 'phone',
                        'type' => 'text',
                    ],
                    [
                        'name' => 'company',
                        'type' => 'text',
                    ],
                ],
            ] ),
        ] );
        
        Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 200 );
        Functions\when( 'wp_remote_retrieve_body' )->justReturn( json_encode( [
            'data' => [
                [
                    'name' => 'phone',
                    'type' => 'text',
                ],
                [
                    'name' => 'company',
                    'type' => 'text',
                ],
            ],
        ] ) );
        
        Functions\when( 'is_wp_error' )->justReturn( false );

        $api = new Thrive_Dash_Api_Mailcoach( 'test_key', 'https://example.com' );
        $fields = $api->getCustomFields( 'list123' );
        
        $this->assertIsArray( $fields );
        $this->assertArrayHasKey( 'data', $fields );
        $this->assertEquals( 'phone', $fields['data'][0]['name'] );
        $this->assertEquals( 'company', $fields['data'][1]['name'] );
    }
}