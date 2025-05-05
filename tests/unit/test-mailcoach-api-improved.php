<?php
/**
 * Improved unit tests for the Mailcoach API class with better HTTP mocking
 *
 * @package Thrive_Leads_Mailcoach_Integration
 */

use Brain\Monkey\Functions;
require_once dirname( dirname( __FILE__ ) ) . '/mocks/MailcoachMockResponses.php';

/**
 * Class Test_Mailcoach_Api_Improved
 */
class Test_Mailcoach_Api_Improved extends WP_UnitTestCase {

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
     * Test getting email lists successfully
     */
    public function test_get_lists_success() {
        // Mock the wp_remote_request function to return a successful response
        Functions\when( 'wp_remote_request' )->justReturn(
            MailcoachMockResponses::get_mock_response('email-lists')
        );
        
        Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 200 );
        
        // Get the mock response body and ensure it's decoded
        $mock_response = MailcoachMockResponses::get_mock_response('email-lists');
        $body_content = is_string($mock_response['body']) ? $mock_response['body'] : json_encode($mock_response['body']);
        
        Functions\when( 'wp_remote_retrieve_body' )->justReturn($body_content);
        
        Functions\when( 'is_wp_error' )->justReturn( false );

        $api = new Thrive_Dash_Api_Mailcoach( 'test_key', 'https://example.com' );
        $lists = $api->getLists();
        
        $this->assertIsArray( $lists );
        $this->assertArrayHasKey( 'data', $lists );
        $this->assertCount( 2, $lists['data'] );
        $this->assertEquals( 'Main Newsletter', $lists['data'][0]['name'] );
        $this->assertEquals( 'Product Updates', $lists['data'][1]['name'] );
    }

    /**
     * Test failure due to invalid API key
     */
    public function test_invalid_api_key() {
        // Mock the wp_remote_request function to return an authentication error
        Functions\when( 'wp_remote_request' )->justReturn(
            MailcoachMockResponses::get_mock_error('invalid_api_key')
        );
        
        Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 401 );
        
        // Get the mock response body and ensure it's a string
        $mock_response = MailcoachMockResponses::get_mock_error('invalid_api_key');
        $body_content = is_string($mock_response['body']) ? $mock_response['body'] : json_encode($mock_response['body']);
        
        Functions\when( 'wp_remote_retrieve_body' )->justReturn($body_content);
        
        Functions\when( 'is_wp_error' )->justReturn( false );

        $api = new Thrive_Dash_Api_Mailcoach( 'invalid_key', 'https://example.com' );
        
        $this->expectException( 'Thrive_Dash_Api_Mailcoach_Exception' );
        $this->expectExceptionMessage( 'Unauthenticated.' );
        
        $api->getLists();
    }

    /**
     * Test adding a subscriber with basic information
     */
    public function test_add_subscriber_basic() {
        $mock_args = [
            'email' => 'test@example.com',
            'first_name' => 'Test',
            'last_name' => 'User',
        ];
        
        // Mock the wp_remote_request function to return a successful response
        Functions\when( 'wp_remote_request' )->justReturn(
            MailcoachMockResponses::get_mock_response('email-lists/12345678-1234-1234-1234-123456789012/subscribers', 
                $mock_args, 
                'POST'
            )
        );
        
        Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 200 );
        
        // Get the mock response body and ensure it's a string
        $mock_response = MailcoachMockResponses::get_mock_response('email-lists/12345678-1234-1234-1234-123456789012/subscribers', 
            $mock_args, 
            'POST'
        );
        $body_content = is_string($mock_response['body']) ? $mock_response['body'] : json_encode($mock_response['body']);
        
        Functions\when( 'wp_remote_retrieve_body' )->justReturn($body_content);
        
        Functions\when( 'is_wp_error' )->justReturn( false );

        $api = new Thrive_Dash_Api_Mailcoach( 'test_key', 'https://example.com' );
        
        $subscriber_data = [
            'email' => 'test@example.com',
            'first_name' => 'Test',
            'last_name' => 'User',
        ];
        
        $result = $api->addSubscriber( '12345678-1234-1234-1234-123456789012', $subscriber_data );
        
        $this->assertIsArray( $result );
        $this->assertEquals( 'test@example.com', $result['email'] );
        $this->assertEquals( 'Test', $result['first_name'] );
        $this->assertEquals( 'User', $result['last_name'] );
    }

    /**
     * Test adding a subscriber with custom fields
     */
    public function test_add_subscriber_with_custom_fields() {
        $mock_args = [
            'email' => 'test@example.com',
            'first_name' => 'Test',
            'last_name' => 'User',
            'extra_attributes' => [
                'company' => 'Test Company',
                'phone' => '555-1234',
            ],
        ];
        
        // Mock the wp_remote_request function to return a successful response
        Functions\when( 'wp_remote_request' )->justReturn(
            MailcoachMockResponses::get_mock_response('email-lists/12345678-1234-1234-1234-123456789012/subscribers', 
                $mock_args, 
                'POST'
            )
        );
        
        Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 200 );
        
        // Get the mock response body and ensure it's a string
        $mock_response = MailcoachMockResponses::get_mock_response('email-lists/12345678-1234-1234-1234-123456789012/subscribers', 
            $mock_args, 
            'POST'
        );
        $body_content = is_string($mock_response['body']) ? $mock_response['body'] : json_encode($mock_response['body']);
        
        Functions\when( 'wp_remote_retrieve_body' )->justReturn($body_content);
        
        Functions\when( 'is_wp_error' )->justReturn( false );

        $api = new Thrive_Dash_Api_Mailcoach( 'test_key', 'https://example.com' );
        
        $subscriber_data = [
            'email' => 'test@example.com',
            'first_name' => 'Test',
            'last_name' => 'User',
            'extra_attributes' => [
                'company' => 'Test Company',
                'phone' => '555-1234',
            ],
        ];
        
        $result = $api->addSubscriber( '12345678-1234-1234-1234-123456789012', $subscriber_data );
        
        $this->assertIsArray( $result );
        $this->assertEquals( 'test@example.com', $result['email'] );
        $this->assertArrayHasKey( 'extra_attributes', $result );
        $this->assertEquals( 'Test Company', $result['extra_attributes']['company'] );
        $this->assertEquals( '555-1234', $result['extra_attributes']['phone'] );
    }

    /**
     * Test subscriber already exists error
     */
    public function test_subscriber_already_exists() {
        // Mock the wp_remote_request function to return an error response
        Functions\when( 'wp_remote_request' )->justReturn(
            MailcoachMockResponses::get_mock_error('subscriber_exists')
        );
        
        Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 422 );
        
        // Get the mock response body and ensure it's a string
        $mock_response = MailcoachMockResponses::get_mock_error('subscriber_exists');
        $body_content = is_string($mock_response['body']) ? $mock_response['body'] : json_encode($mock_response['body']);
        
        Functions\when( 'wp_remote_retrieve_body' )->justReturn($body_content);
        
        Functions\when( 'is_wp_error' )->justReturn( false );

        $api = new Thrive_Dash_Api_Mailcoach( 'test_key', 'https://example.com' );
        
        $subscriber_data = [
            'email' => 'existing@example.com',
            'first_name' => 'Existing',
            'last_name' => 'User',
        ];
        
        $this->expectException( 'Thrive_Dash_Api_Mailcoach_Exception' );
        $this->expectExceptionMessage( 'The email has already been taken.' );
        
        $api->addSubscriber( '12345678-1234-1234-1234-123456789012', $subscriber_data );
    }

    /**
     * Test rate limit error
     */
    public function test_rate_limit_error() {
        // Mock the wp_remote_request function to return a rate limit error
        Functions\when( 'wp_remote_request' )->justReturn(
            MailcoachMockResponses::get_mock_error('rate_limit')
        );
        
        Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 429 );
        
        // Get the mock response body and ensure it's a string
        $mock_response = MailcoachMockResponses::get_mock_error('rate_limit');
        $body_content = is_string($mock_response['body']) ? $mock_response['body'] : json_encode($mock_response['body']);
        
        Functions\when( 'wp_remote_retrieve_body' )->justReturn($body_content);
        
        Functions\when( 'is_wp_error' )->justReturn( false );

        $api = new Thrive_Dash_Api_Mailcoach( 'test_key', 'https://example.com' );
        
        $this->expectException( 'Thrive_Dash_Api_Mailcoach_Exception' );
        $this->expectExceptionMessage( 'Too Many Attempts.' );
        
        $api->getLists();
    }

    /**
     * Test server error handling
     */
    public function test_server_error() {
        // Mock the wp_remote_request function to return a server error
        Functions\when( 'wp_remote_request' )->justReturn(
            MailcoachMockResponses::get_mock_error('server_error')
        );
        
        Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 500 );
        
        // Get the mock response body and ensure it's a string
        $mock_response = MailcoachMockResponses::get_mock_error('server_error');
        $body_content = is_string($mock_response['body']) ? $mock_response['body'] : json_encode($mock_response['body']);
        
        Functions\when( 'wp_remote_retrieve_body' )->justReturn($body_content);
        
        Functions\when( 'is_wp_error' )->justReturn( false );

        $api = new Thrive_Dash_Api_Mailcoach( 'test_key', 'https://example.com' );
        
        $this->expectException( 'Thrive_Dash_Api_Mailcoach_Exception' );
        $this->expectExceptionMessage( 'Server Error' );
        
        $api->getLists();
    }

    /**
     * Test list not found error
     */
    public function test_list_not_found_error() {
        // Mock the wp_remote_request function to return a not found error
        Functions\when( 'wp_remote_request' )->justReturn(
            MailcoachMockResponses::get_mock_error('list_not_found')
        );
        
        Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 404 );
        
        // Get the mock response body and ensure it's a string
        $mock_response = MailcoachMockResponses::get_mock_error('list_not_found');
        $body_content = is_string($mock_response['body']) ? $mock_response['body'] : json_encode($mock_response['body']);
        
        Functions\when( 'wp_remote_retrieve_body' )->justReturn($body_content);
        
        Functions\when( 'is_wp_error' )->justReturn( false );

        $api = new Thrive_Dash_Api_Mailcoach( 'test_key', 'https://example.com' );
        
        $this->expectException( 'Thrive_Dash_Api_Mailcoach_Exception' );
        $this->expectExceptionMessage( 'Email list not found.' );
        
        $api->getList( 'nonexistent-list' );
    }

    /**
     * Test getting custom fields
     */
    public function test_get_custom_fields() {
        // Mock the wp_remote_request function to return a successful response
        Functions\when( 'wp_remote_request' )->justReturn(
            MailcoachMockResponses::get_mock_response('email-lists/12345678-1234-1234-1234-123456789012/subscriber-custom-fields')
        );
        
        Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 200 );
        
        // Get the mock response body and ensure it's a string
        $mock_response = MailcoachMockResponses::get_mock_response('email-lists/12345678-1234-1234-1234-123456789012/subscriber-custom-fields');
        $body_content = is_string($mock_response['body']) ? $mock_response['body'] : json_encode($mock_response['body']);
        
        Functions\when( 'wp_remote_retrieve_body' )->justReturn($body_content);
        
        Functions\when( 'is_wp_error' )->justReturn( false );

        $api = new Thrive_Dash_Api_Mailcoach( 'test_key', 'https://example.com' );
        $fields = $api->getCustomFields( '12345678-1234-1234-1234-123456789012' );
        
        $this->assertIsArray( $fields );
        $this->assertArrayHasKey( 'data', $fields );
        $this->assertCount( 4, $fields['data'] );
        $this->assertEquals( 'phone', $fields['data'][0]['name'] );
        $this->assertEquals( 'company', $fields['data'][1]['name'] );
        $this->assertEquals( 'birthday', $fields['data'][2]['name'] );
        $this->assertEquals( 'interests', $fields['data'][3]['name'] );
    }

    /**
     * Test network failure handling
     */
    public function test_network_failure() {
        // Create a simple WP_Error instance
        $wp_error = new WP_Error('http_request_failed', 'Network connection failed');

        // Mock the wp_remote_request function to return a WP_Error
        Functions\when( 'wp_remote_request' )->justReturn( $wp_error );
        Functions\when( 'is_wp_error' )->justReturn( true );
        
        // Mock the get_error_message to return our expected message
        Functions\when( 'wp_error_get_error_message' )->justReturn( 'Network connection failed' );

        $api = new Thrive_Dash_Api_Mailcoach( 'test_key', 'https://example.com' );
        
        $this->expectException( 'Thrive_Dash_Api_Mailcoach_Exception' );
        $this->expectExceptionMessage( 'Network connection failed' );
        
        $api->getLists();
    }
}