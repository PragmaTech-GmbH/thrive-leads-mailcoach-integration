<?php
/**
 * Unit tests for the Mailcoach Connection class
 *
 * @package Thrive_Leads_Mailcoach_Integration
 */

use Brain\Monkey\Functions;

/**
 * Class Test_Mailcoach_Connection
 */
class Test_Mailcoach_Connection extends WP_UnitTestCase {

    /**
     * @var Thrive_Dash_List_Connection_Mailcoach
     */
    protected $connection;

    /**
     * @var PHPUnit\Framework\MockObject\MockObject
     */
    protected $api_mock;

    /**
     * Set up the test case
     */
    public function setUp(): void {
        parent::setUp();
        // Brain\Monkey is already set up in the bootstrap file
        
        // Set up WordPress function mocks
        Functions\when('sanitize_text_field')->returnArg(1);
        Functions\when('__')->returnArg(1);
        Functions\when('esc_html__')->returnArg(1);
        Functions\when('esc_attr')->returnArg(1);
        
        require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/inc/auto-responder/lib/vendor/Mailcoach/Api.php';
        require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/inc/auto-responder/classes/Connection/Mailcoach.php';
        
        // Create a mock for the API class
        $this->api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                               ->disableOriginalConstructor()
                               ->getMock();
        
        // Create the connection object
        $this->connection = new Thrive_Dash_List_Connection_Mailcoach();
        
        // Set up reflection to inject the mock API
        $reflection = new ReflectionClass( 'Thrive_Dash_List_Connection_Mailcoach' );
        $method = $reflection->getMethod( 'get_api_instance' );
        $method->setAccessible( true );
        
        // Override the get_api_instance method to return our mock
        $connection_mock = $this->getMockBuilder( 'Thrive_Dash_List_Connection_Mailcoach' )
                                ->setMethods( ['get_api_instance'] )
                                ->getMock();
        $connection_mock->method( 'get_api_instance' )->willReturn( $this->api_mock );
        
        $this->connection = $connection_mock;
    }

    /**
     * Tear down the test case
     */
    public function tearDown(): void {
        // Brain\Monkey tearDown is handled in the bootstrap file
        parent::tearDown();
    }

    /**
     * Test get_title method
     */
    public function test_get_title() {
        $this->assertEquals( 'Mailcoach', $this->connection->get_title() );
    }

    /**
     * Test get_type method
     */
    public function test_get_type() {
        $this->assertEquals( 'autoresponder', $this->connection->get_type() );
    }

    /**
     * Test has_tags method
     */
    public function test_has_tags() {
        $this->assertTrue( $this->connection->has_tags() );
    }

    /**
     * Test has_custom_fields method
     */
    public function test_has_custom_fields() {
        $this->assertTrue( $this->connection->has_custom_fields() );
    }

    /**
     * Test has_optin method
     */
    public function test_has_optin() {
        $this->assertTrue( $this->connection->has_optin() );
    }

    /**
     * Test read_credentials method with valid credentials
     */
    public function test_read_credentials_valid() {
        // Mock the test_connection method to return true
        $mock_connection = $this->getMockBuilder( 'Thrive_Dash_List_Connection_Mailcoach' )
                               ->setMethods( ['test_connection', 'set_credentials', 'save'] )
                               ->getMock();
        
        $mock_connection->method( 'test_connection' )->willReturn( true );
        $mock_connection->method( 'set_credentials' )->willReturn( true );
        $mock_connection->method( 'save' )->willReturn( true );
        
        $result = $mock_connection->read_credentials( [
            'connection' => [
                'api_key' => 'valid_key',
                'api_url' => 'https://example.com',
            ],
        ] );
        
        $this->assertTrue( $result );
    }

    /**
     * Test read_credentials method with missing API key
     */
    public function test_read_credentials_missing_api_key() {
        $result = $this->connection->read_credentials( [
            'connection' => [
                'api_url' => 'https://example.com',
            ],
        ] );
        
        $this->assertEquals( 'API Key is required', $result );
    }

    /**
     * Test read_credentials method with missing API URL
     */
    public function test_read_credentials_missing_api_url() {
        $result = $this->connection->read_credentials( [
            'connection' => [
                'api_key' => 'valid_key',
            ],
        ] );
        
        $this->assertEquals( 'API URL is required', $result );
    }

    /**
     * Test test_connection method with successful connection
     */
    public function test_test_connection_success() {
        // Set up the API mock to return a successful result
        $this->api_mock->method( 'getLists' )->willReturn( [
            'data' => [
                [
                    'uuid' => 'list123',
                    'name' => 'Test List',
                ],
            ],
        ] );
        
        $result = $this->connection->test_connection();
        
        $this->assertTrue( $result );
    }

    /**
     * Test test_connection method with failed connection
     */
    public function test_test_connection_failure() {
        // Set up the API mock to throw an exception
        $this->api_mock->method( 'getLists' )->will( $this->throwException( new Exception( 'API Error' ) ) );
        
        $result = $this->connection->test_connection();
        
        $this->assertEquals( 'API Error', $result );
    }

    /**
     * Test add_subscriber method with success
     */
    public function test_add_subscriber_success() {
        // Set up the API mock to return a successful result
        $this->api_mock->method( 'addSubscriber' )->willReturn( [
            'uuid'  => 'sub123',
            'email' => 'test@example.com',
        ] );
        
        $result = $this->connection->add_subscriber( 'list123', [
            'email' => 'test@example.com',
            'name'  => 'Test User',
        ] );
        
        $this->assertTrue( $result );
    }

    /**
     * Test add_subscriber method with failure
     */
    public function test_add_subscriber_failure() {
        // Set up the API mock to throw an exception
        $this->api_mock->method( 'addSubscriber' )->will( $this->throwException( new Exception( 'Subscription failed' ) ) );
        
        $result = $this->connection->add_subscriber( 'list123', [
            'email' => 'test@example.com',
            'name'  => 'Test User',
        ] );
        
        $this->assertEquals( 'Subscription failed', $result );
    }

    /**
     * Test add_subscriber method with custom fields and tags
     */
    public function test_add_subscriber_with_custom_fields_and_tags() {
        // Mock the addSubscriber method to inspect arguments
        $this->api_mock->expects( $this->once() )
            ->method( 'addSubscriber' )
            ->with(
                $this->equalTo( 'list123' ),
                $this->callback( function( $subscriber_data ) {
                    return isset( $subscriber_data['email'] ) && 
                        $subscriber_data['email'] === 'test@example.com' &&
                        isset( $subscriber_data['extra_attributes'] ) &&
                        isset( $subscriber_data['extra_attributes']['company'] ) &&
                        $subscriber_data['extra_attributes']['company'] === 'Test Company';
                } )
            )
            ->willReturn( [
                'uuid'  => 'sub123',
                'email' => 'test@example.com',
            ] );
        
        // Mock the addTagsToSubscriber method
        $this->api_mock->expects( $this->never() )
            ->method( 'addTagsToSubscriber' );
        
        $result = $this->connection->add_subscriber( 'list123', [
            'email' => 'test@example.com',
            'name'  => 'Test User',
            'extra' => [
                'company' => 'Test Company',
            ],
        ] );
        
        $this->assertTrue( $result );
    }

    /**
     * Test get_api_custom_fields method
     */
    public function test_get_api_custom_fields() {
        // Set up the API mock to return custom fields
        $this->api_mock->method( 'getCustomFields' )->willReturn( [
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
        ] );
        
        $custom_fields = $this->connection->get_api_custom_fields( 'list123' );
        
        $this->assertIsArray( $custom_fields );
        $this->assertCount( 2, $custom_fields );
        $this->assertEquals( 'phone', $custom_fields[0]['name'] );
        $this->assertEquals( 'company', $custom_fields[1]['name'] );
    }
}