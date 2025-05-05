<?php
/**
 * Improved unit tests for the Mailcoach Connection class
 *
 * @package Thrive_Leads_Mailcoach_Integration
 */

use Brain\Monkey\Functions;
require_once dirname( dirname( __FILE__ ) ) . '/mocks/MailcoachMockResponses.php';

/**
 * Class Test_Mailcoach_Connection_Improved
 */
class Test_Mailcoach_Connection_Improved extends WP_UnitTestCase {

    /**
     * @var Thrive_Dash_List_Connection_Mailcoach
     */
    protected $connection;

    /**
     * Set up the test case
     */
    public function setUp(): void {
        parent::setUp();
        // Brain\Monkey is already set up in the bootstrap file
        
        // Setup global mocks for WordPress functions
        Functions\when('sanitize_text_field')->returnArg(1);
        Functions\when('__')->returnArg(1);
        Functions\when('esc_html__')->returnArg(1);
        Functions\when('esc_attr')->returnArg(1);
        
        require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/inc/auto-responder/lib/vendor/Mailcoach/Api.php';
        require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/inc/auto-responder/classes/Connection/Mailcoach.php';
        
        // Create the connection object
        $this->connection = new Thrive_Dash_List_Connection_Mailcoach();
    }

    /**
     * Tear down the test case
     */
    public function tearDown(): void {
        // Brain\Monkey tearDown is handled in the bootstrap file
        parent::tearDown();
    }

    /**
     * Test read_credentials with valid data
     */
    public function test_read_credentials_valid() {
        // Mock methods that interact with WordPress
        Functions\when( 'sanitize_text_field' )->returnArg( 1 );
        
        // Create a partial mock that overrides the test_connection method
        $connection = $this->getMockBuilder( 'Thrive_Dash_List_Connection_Mailcoach' )
                           ->setMethods( ['test_connection', 'set_credentials', 'save'] )
                           ->getMock();
        
        $connection->method( 'test_connection' )->willReturn( true );
        $connection->method( 'set_credentials' )->willReturn( true );
        $connection->method( 'save' )->willReturn( true );
        
        $result = $connection->read_credentials( [
            'connection' => [
                'api_key' => 'valid_api_key',
                'api_url' => 'https://example.com',
            ],
        ] );
        
        $this->assertTrue( $result );
    }
    
    /**
     * Test read_credentials with invalid data (missing API key)
     */
    public function test_read_credentials_missing_api_key() {
        // Mock methods that interact with WordPress
        Functions\when( 'sanitize_text_field' )->returnArg( 1 );
        Functions\when( '__' )->returnArg( 1 );
        
        $result = $this->connection->read_credentials( [
            'connection' => [
                'api_url' => 'https://example.com',
            ],
        ] );
        
        $this->assertEquals( 'API Key is required', $result );
    }
    
    /**
     * Test read_credentials with invalid data (missing API URL)
     */
    public function test_read_credentials_missing_api_url() {
        // Mock methods that interact with WordPress
        Functions\when( 'sanitize_text_field' )->returnArg( 1 );
        Functions\when( '__' )->returnArg( 1 );
        
        $result = $this->connection->read_credentials( [
            'connection' => [
                'api_key' => 'valid_api_key',
            ],
        ] );
        
        $this->assertEquals( 'API URL is required', $result );
    }
    
    /**
     * Test test_connection method with successful connection
     */
    public function test_test_connection_success() {
        // Mock the get_api_instance method to return a mock API
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                          ->disableOriginalConstructor()
                          ->setMethods( ['getLists'] )
                          ->getMock();
        
        $api_mock->method( 'getLists' )->willReturn( [
            'data' => [
                [
                    'uuid' => '12345678-1234-1234-1234-123456789012',
                    'name' => 'Test List',
                ],
            ],
        ] );
        
        $connection = $this->getMockBuilder( 'Thrive_Dash_List_Connection_Mailcoach' )
                           ->setMethods( ['get_api_instance'] )
                           ->getMock();
        
        $connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        $result = $connection->test_connection();
        
        $this->assertTrue( $result );
    }
    
    /**
     * Test test_connection method with failed authentication
     */
    public function test_test_connection_auth_failure() {
        // Mock the get_api_instance method to return a mock API
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                          ->disableOriginalConstructor()
                          ->setMethods( ['getLists'] )
                          ->getMock();
        
        $api_mock->method( 'getLists' )->will( $this->throwException( new Exception( 'Unauthenticated.' ) ) );
        
        $connection = $this->getMockBuilder( 'Thrive_Dash_List_Connection_Mailcoach' )
                           ->setMethods( ['get_api_instance'] )
                           ->getMock();
        
        $connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        $result = $connection->test_connection();
        
        $this->assertEquals( 'Unauthenticated.', $result );
    }
    
    /**
     * Test test_connection method with network failure
     */
    public function test_test_connection_network_failure() {
        // Mock the get_api_instance method to return a mock API
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                          ->disableOriginalConstructor()
                          ->setMethods( ['getLists'] )
                          ->getMock();
        
        $api_mock->method( 'getLists' )->will( $this->throwException( new Exception( 'Network connection failed.' ) ) );
        
        $connection = $this->getMockBuilder( 'Thrive_Dash_List_Connection_Mailcoach' )
                           ->setMethods( ['get_api_instance'] )
                           ->getMock();
        
        $connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        $result = $connection->test_connection();
        
        $this->assertEquals( 'Network connection failed.', $result );
    }
    
    /**
     * Test add_subscriber with basic subscriber data
     */
    public function test_add_subscriber_basic() {
        // Mock the get_api_instance method to return a mock API
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                          ->disableOriginalConstructor()
                          ->setMethods( ['addSubscriber'] )
                          ->getMock();
        
        $api_mock->method( 'addSubscriber' )->willReturn( [
            'uuid' => 'sub-12345',
            'email' => 'test@example.com',
            'first_name' => 'Test',
            'last_name' => 'User',
        ] );
        
        $connection = $this->getMockBuilder( 'Thrive_Dash_List_Connection_Mailcoach' )
                           ->setMethods( ['get_api_instance'] )
                           ->getMock();
        
        $connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        $result = $connection->add_subscriber( '12345678-1234-1234-1234-123456789012', [
            'email' => 'test@example.com',
            'name' => 'Test',
            'last_name' => 'User',
        ] );
        
        $this->assertTrue( $result );
    }
    
    /**
     * Test adding a subscriber with custom fields
     */
    public function test_add_subscriber_with_custom_fields() {
        // Mock the get_api_instance method to return a mock API
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                          ->disableOriginalConstructor()
                          ->setMethods( ['addSubscriber'] )
                          ->getMock();
        
        // Set expectations for addSubscriber call
        $api_mock->expects( $this->once() )
                 ->method( 'addSubscriber' )
                 ->with(
                     $this->equalTo( '12345678-1234-1234-1234-123456789012' ),
                     $this->callback( function( $args ) {
                         return isset( $args['email'] ) && 
                                $args['email'] === 'test@example.com' &&
                                isset( $args['extra_attributes'] ) &&
                                isset( $args['extra_attributes']['company'] ) && 
                                $args['extra_attributes']['company'] === 'Test Company';
                     } )
                 )
                 ->willReturn( [
                     'uuid' => 'sub-12345',
                     'email' => 'test@example.com',
                     'first_name' => 'Test',
                     'last_name' => 'User',
                     'extra_attributes' => [
                         'company' => 'Test Company',
                     ],
                 ] );
        
        $connection = $this->getMockBuilder( 'Thrive_Dash_List_Connection_Mailcoach' )
                           ->setMethods( ['get_api_instance'] )
                           ->getMock();
        
        $connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        $result = $connection->add_subscriber( '12345678-1234-1234-1234-123456789012', [
            'email' => 'test@example.com',
            'name' => 'Test',
            'last_name' => 'User',
            'extra' => [
                'company' => 'Test Company',
            ],
        ] );
        
        $this->assertTrue( $result );
    }
    
    /**
     * Test adding a subscriber with double opt-in
     */
    public function test_add_subscriber_with_double_optin() {
        // Mock the get_api_instance method to return a mock API
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                          ->disableOriginalConstructor()
                          ->setMethods( ['addSubscriber'] )
                          ->getMock();
        
        // Set expectations for addSubscriber call
        $api_mock->expects( $this->once() )
                 ->method( 'addSubscriber' )
                 ->with(
                     $this->equalTo( '12345678-1234-1234-1234-123456789012' ),
                     $this->callback( function( $args ) {
                         return isset( $args['email'] ) && 
                                $args['email'] === 'test@example.com' &&
                                isset( $args['requires_confirmation'] ) && 
                                $args['requires_confirmation'] === true;
                     } )
                 )
                 ->willReturn( [
                     'uuid' => 'sub-12345',
                     'email' => 'test@example.com',
                     'first_name' => 'Test',
                     'last_name' => 'User',
                     'requires_confirmation' => true,
                 ] );
        
        $connection = $this->getMockBuilder( 'Thrive_Dash_List_Connection_Mailcoach' )
                           ->setMethods( ['get_api_instance'] )
                           ->getMock();
        
        $connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        $result = $connection->add_subscriber( '12345678-1234-1234-1234-123456789012', [
            'email' => 'test@example.com',
            'name' => 'Test',
            'last_name' => 'User',
            'mailcoach_optin' => 'double',
        ] );
        
        $this->assertTrue( $result );
    }
    
    /**
     * Test adding a subscriber with phone field
     */
    public function test_add_subscriber_with_phone() {
        // Mock the get_api_instance method to return a mock API
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                          ->disableOriginalConstructor()
                          ->setMethods( ['addSubscriber'] )
                          ->getMock();
        
        // Set expectations for addSubscriber call
        $api_mock->expects( $this->once() )
                 ->method( 'addSubscriber' )
                 ->with(
                     $this->equalTo( '12345678-1234-1234-1234-123456789012' ),
                     $this->callback( function( $args ) {
                         return isset( $args['email'] ) && 
                                $args['email'] === 'test@example.com' &&
                                isset( $args['extra_attributes'] ) &&
                                isset( $args['extra_attributes']['phone'] ) && 
                                $args['extra_attributes']['phone'] === '555-1234';
                     } )
                 )
                 ->willReturn( [
                     'uuid' => 'sub-12345',
                     'email' => 'test@example.com',
                     'first_name' => 'Test',
                     'last_name' => 'User',
                     'extra_attributes' => [
                         'phone' => '555-1234',
                     ],
                 ] );
        
        $connection = $this->getMockBuilder( 'Thrive_Dash_List_Connection_Mailcoach' )
                           ->setMethods( ['get_api_instance'] )
                           ->getMock();
        
        $connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        $result = $connection->add_subscriber( '12345678-1234-1234-1234-123456789012', [
            'email' => 'test@example.com',
            'name' => 'Test',
            'last_name' => 'User',
            'phone' => '555-1234',
        ] );
        
        $this->assertTrue( $result );
    }
    
    /**
     * Test adding a subscriber with tags
     */
    public function test_add_subscriber_with_tags() {
        // Mock the get_api_instance method to return a mock API
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                          ->disableOriginalConstructor()
                          ->setMethods( ['addSubscriber', 'addTagsToSubscriber'] )
                          ->getMock();
        
        // Set expectations for addSubscriber call
        $api_mock->method( 'addSubscriber' )
                 ->willReturn( [
                     'uuid' => 'sub-12345',
                     'email' => 'test@example.com',
                     'first_name' => 'Test',
                     'last_name' => 'User',
                 ] );
        
        // Set expectations for addTagsToSubscriber call
        $api_mock->expects( $this->once() )
                 ->method( 'addTagsToSubscriber' )
                 ->with(
                     $this->equalTo( 'sub-12345' ),
                     $this->callback( function( $tags ) {
                         return is_array( $tags ) &&
                                count( $tags ) === 2 &&
                                in_array( 'newsletter', $tags ) &&
                                in_array( 'lead', $tags );
                     } )
                 )
                 ->willReturn( true );
        
        $connection = $this->getMockBuilder( 'Thrive_Dash_List_Connection_Mailcoach' )
                           ->setMethods( ['get_api_instance'] )
                           ->getMock();
        
        $connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        $result = $connection->add_subscriber( '12345678-1234-1234-1234-123456789012', [
            'email' => 'test@example.com',
            'name' => 'Test',
            'last_name' => 'User',
            'mailcoach_tags' => 'newsletter, lead',
        ] );
        
        $this->assertTrue( $result );
    }
    
    /**
     * Test subscriber addition failure
     */
    public function test_add_subscriber_failure() {
        // Mock the get_api_instance method to return a mock API
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                          ->disableOriginalConstructor()
                          ->setMethods( ['addSubscriber'] )
                          ->getMock();
        
        // Set the addSubscriber method to throw an exception
        $api_mock->method( 'addSubscriber' )
                 ->will( $this->throwException( new Exception( 'Failed to add subscriber' ) ) );
        
        $connection = $this->getMockBuilder( 'Thrive_Dash_List_Connection_Mailcoach' )
                           ->setMethods( ['get_api_instance'] )
                           ->getMock();
        
        $connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        $result = $connection->add_subscriber( '12345678-1234-1234-1234-123456789012', [
            'email' => 'test@example.com',
            'name' => 'Test',
            'last_name' => 'User',
        ] );
        
        $this->assertEquals( 'Failed to add subscriber', $result );
    }
    
    /**
     * Test get_api_custom_fields
     */
    public function test_get_api_custom_fields() {
        // Mock the get_api_instance method to return a mock API
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                          ->disableOriginalConstructor()
                          ->setMethods( ['getCustomFields'] )
                          ->getMock();
        
        // Set the getCustomFields method to return test data
        $api_mock->method( 'getCustomFields' )
                 ->willReturn( [
                     'data' => [
                         [
                             'name' => 'phone',
                             'type' => 'text',
                         ],
                         [
                             'name' => 'company',
                             'type' => 'text',
                         ],
                         [
                             'name' => 'birthday',
                             'type' => 'date',
                         ],
                     ],
                 ] );
        
        $connection = $this->getMockBuilder( 'Thrive_Dash_List_Connection_Mailcoach' )
                           ->setMethods( ['get_api_instance'] )
                           ->getMock();
        
        $connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        $custom_fields = $connection->get_api_custom_fields( '12345678-1234-1234-1234-123456789012' );
        
        $this->assertIsArray( $custom_fields );
        $this->assertCount( 3, $custom_fields );
        $this->assertEquals( 'phone', $custom_fields[0]['name'] );
        $this->assertEquals( 'company', $custom_fields[1]['name'] );
        $this->assertEquals( 'birthday', $custom_fields[2]['name'] );
    }
    
    /**
     * Test get_api_custom_fields with API error
     */
    public function test_get_api_custom_fields_error() {
        // Mock the get_api_instance method to return a mock API
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                          ->disableOriginalConstructor()
                          ->setMethods( ['getCustomFields'] )
                          ->getMock();
        
        // Set the getCustomFields method to throw an exception
        $api_mock->method( 'getCustomFields' )
                 ->will( $this->throwException( new Exception( 'API Error' ) ) );
        
        $connection = $this->getMockBuilder( 'Thrive_Dash_List_Connection_Mailcoach' )
                           ->setMethods( ['get_api_instance'] )
                           ->getMock();
        
        $connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        $custom_fields = $connection->get_api_custom_fields( '12345678-1234-1234-1234-123456789012' );
        
        $this->assertIsArray( $custom_fields );
        $this->assertEmpty( $custom_fields );
    }
    
    /**
     * Test _get_lists method (protected, accessed via reflection)
     */
    public function test_get_lists() {
        // Mock the get_api_instance method to return a mock API
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                          ->disableOriginalConstructor()
                          ->setMethods( ['getLists'] )
                          ->getMock();
        
        // Set the getLists method to return test data
        $api_mock->method( 'getLists' )
                 ->willReturn( [
                     'data' => [
                         [
                             'uuid' => '12345678-1234-1234-1234-123456789012',
                             'name' => 'Main Newsletter',
                         ],
                         [
                             'uuid' => '87654321-4321-4321-4321-210987654321',
                             'name' => 'Product Updates',
                         ],
                     ],
                 ] );
        
        $connection = $this->getMockBuilder( 'Thrive_Dash_List_Connection_Mailcoach' )
                           ->setMethods( ['get_api_instance'] )
                           ->getMock();
        
        $connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        // Use reflection to access protected method
        $reflection = new ReflectionClass( get_class( $connection ) );
        $method = $reflection->getMethod( '_get_lists' );
        $method->setAccessible( true );
        
        $lists = $method->invoke( $connection );
        
        $this->assertIsArray( $lists );
        $this->assertCount( 2, $lists );
        $this->assertEquals( '12345678-1234-1234-1234-123456789012', $lists[0]['id'] );
        $this->assertEquals( 'Main Newsletter', $lists[0]['name'] );
        $this->assertEquals( '87654321-4321-4321-4321-210987654321', $lists[1]['id'] );
        $this->assertEquals( 'Product Updates', $lists[1]['name'] );
    }
    
    /**
     * Test _get_lists with API error
     */
    public function test_get_lists_error() {
        // Mock the get_api_instance method to return a mock API
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                          ->disableOriginalConstructor()
                          ->setMethods( ['getLists'] )
                          ->getMock();
        
        // Set the getLists method to throw an exception
        $api_mock->method( 'getLists' )
                 ->will( $this->throwException( new Exception( 'API Error' ) ) );
        
        $connection = $this->getMockBuilder( 'Thrive_Dash_List_Connection_Mailcoach' )
                           ->setMethods( ['get_api_instance'] )
                           ->getMock();
        
        $connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        // Use reflection to access protected method
        $reflection = new ReflectionClass( get_class( $connection ) );
        $method = $reflection->getMethod( '_get_lists' );
        $method->setAccessible( true );
        
        $lists = $method->invoke( $connection );
        
        $this->assertIsArray( $lists );
        $this->assertEmpty( $lists );
    }
}