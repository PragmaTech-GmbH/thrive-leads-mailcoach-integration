<?php
/**
 * Integration tests for subscriber management
 *
 * @package Thrive_Leads_Mailcoach_Integration
 */

/**
 * Class Test_Subscriber_Management
 */
class Test_Subscriber_Management extends WP_UnitTestCase {

    /**
     * @var Thrive_Dash_List_Connection_Mailcoach
     */
    protected $connection;

    /**
     * Set up the test case
     */
    public function setUp(): void {
        parent::setUp();
        
        // Include necessary files
        if (!class_exists('Thrive_Dash_Api_Mailcoach')) {
            require_once dirname(dirname(dirname(__FILE__))) . '/inc/auto-responder/lib/vendor/Mailcoach/Api.php';
        }
        
        if (!class_exists('Thrive_Dash_List_Connection_Mailcoach')) {
            require_once dirname(dirname(dirname(__FILE__))) . '/inc/auto-responder/classes/Connection/Mailcoach.php';
        }
        
        // Create a mock of the connection class that overrides the API client
        $this->connection = $this->getMockBuilder( 'Thrive_Dash_List_Connection_Mailcoach' )
                                 ->setMethods( ['get_api_instance'] )
                                 ->getMock();
    }

    /**
     * Test subscriber addition with basic information
     */
    public function test_add_subscriber_basic() {
        // Create a mock API instance
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                         ->disableOriginalConstructor()
                         ->setMethods( ['addSubscriber'] )
                         ->getMock();
        
        // Configure the mock to expect specific arguments and return a predefined response
        $api_mock->expects( $this->once() )
                 ->method( 'addSubscriber' )
                 ->with(
                     $this->equalTo( 'list123' ),
                     $this->callback( function( $subscriber_data ) {
                         return isset( $subscriber_data['email'] ) && 
                                $subscriber_data['email'] === 'test@example.com' &&
                                isset( $subscriber_data['first_name'] ) && 
                                $subscriber_data['first_name'] === 'Test User';
                     } )
                 )
                 ->willReturn( [
                     'uuid'  => 'sub123',
                     'email' => 'test@example.com',
                 ] );
        
        // Configure the connection mock to return our API mock
        $this->connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        // Test the add_subscriber method
        $result = $this->connection->add_subscriber( 'list123', [
            'email' => 'test@example.com',
            'name'  => 'Test User',
        ] );
        
        $this->assertTrue( $result );
    }

    /**
     * Test subscriber addition with custom fields
     */
    public function test_add_subscriber_with_custom_fields() {
        // Create a mock API instance
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                         ->disableOriginalConstructor()
                         ->setMethods( ['addSubscriber'] )
                         ->getMock();
        
        // Configure the mock to expect specific arguments and return a predefined response
        $api_mock->expects( $this->once() )
                 ->method( 'addSubscriber' )
                 ->with(
                     $this->equalTo( 'list123' ),
                     $this->callback( function( $subscriber_data ) {
                         return isset( $subscriber_data['email'] ) && 
                                $subscriber_data['email'] === 'test@example.com' &&
                                isset( $subscriber_data['extra_attributes'] ) &&
                                isset( $subscriber_data['extra_attributes']['phone'] ) &&
                                $subscriber_data['extra_attributes']['phone'] === '123-456-7890' &&
                                isset( $subscriber_data['extra_attributes']['company'] ) &&
                                $subscriber_data['extra_attributes']['company'] === 'Test Company';
                     } )
                 )
                 ->willReturn( [
                     'uuid'  => 'sub123',
                     'email' => 'test@example.com',
                 ] );
        
        // Configure the connection mock to return our API mock
        $this->connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        // Test the add_subscriber method with custom fields
        $result = $this->connection->add_subscriber( 'list123', [
            'email' => 'test@example.com',
            'name'  => 'Test User',
            'phone' => '123-456-7890',
            'extra' => [
                'company' => 'Test Company',
            ],
        ] );
        
        $this->assertTrue( $result );
    }

    /**
     * Test subscriber addition with double opt-in
     */
    public function test_add_subscriber_with_double_optin() {
        // Create a mock API instance
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                         ->disableOriginalConstructor()
                         ->setMethods( ['addSubscriber'] )
                         ->getMock();
        
        // Configure the mock to expect specific arguments and return a predefined response
        $api_mock->expects( $this->once() )
                 ->method( 'addSubscriber' )
                 ->with(
                     $this->equalTo( 'list123' ),
                     $this->callback( function( $subscriber_data ) {
                         return isset( $subscriber_data['email'] ) && 
                                $subscriber_data['email'] === 'test@example.com' &&
                                isset( $subscriber_data['requires_confirmation'] ) && 
                                $subscriber_data['requires_confirmation'] === true;
                     } )
                 )
                 ->willReturn( [
                     'uuid'  => 'sub123',
                     'email' => 'test@example.com',
                 ] );
        
        // Configure the connection mock to return our API mock
        $this->connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        // Test the add_subscriber method with double opt-in
        $result = $this->connection->add_subscriber( 'list123', [
            'email'           => 'test@example.com',
            'name'            => 'Test User',
            'mailcoach_optin' => 'double',
        ] );
        
        $this->assertTrue( $result );
    }

    /**
     * Test subscriber addition with tags
     */
    public function test_add_subscriber_with_tags() {
        // Create a mock API instance
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                         ->disableOriginalConstructor()
                         ->setMethods( ['addSubscriber', 'addTagsToSubscriber'] )
                         ->getMock();
        
        // Configure the addSubscriber mock to expect specific arguments and return a predefined response
        $api_mock->expects( $this->once() )
                 ->method( 'addSubscriber' )
                 ->with(
                     $this->equalTo( 'list123' ),
                     $this->callback( function( $subscriber_data ) {
                         return isset( $subscriber_data['email'] ) && 
                                $subscriber_data['email'] === 'test@example.com';
                     } )
                 )
                 ->willReturn( [
                     'uuid'  => 'sub123',
                     'email' => 'test@example.com',
                 ] );
        
        // Configure the addTagsToSubscriber mock
        $api_mock->expects( $this->once() )
                 ->method( 'addTagsToSubscriber' )
                 ->with(
                     $this->equalTo( 'sub123' ),
                     $this->callback( function( $tags ) {
                         return is_array( $tags ) && 
                                in_array( 'newsletter', $tags ) && 
                                in_array( 'lead', $tags );
                     } )
                 )
                 ->willReturn( true );
        
        // Configure the connection mock to return our API mock
        $this->connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        // Test the add_subscriber method with tags
        $result = $this->connection->add_subscriber( 'list123', [
            'email'          => 'test@example.com',
            'name'           => 'Test User',
            'mailcoach_tags' => 'newsletter, lead',
        ] );
        
        $this->assertTrue( $result );
    }

    /**
     * Test error handling
     */
    public function test_add_subscriber_error_handling() {
        // Create a mock API instance that throws an exception
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                         ->disableOriginalConstructor()
                         ->setMethods( ['addSubscriber'] )
                         ->getMock();
        
        // Configure the mock to throw an exception
        $api_mock->method( 'addSubscriber' )
                 ->will( $this->throwException( new Exception( 'Subscriber already exists' ) ) );
        
        // Configure the connection mock to return our API mock
        $this->connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        // Test the add_subscriber method with an error condition
        $result = $this->connection->add_subscriber( 'list123', [
            'email' => 'test@example.com',
            'name'  => 'Test User',
        ] );
        
        // Check that the result is the error message
        $this->assertEquals( 'Subscriber already exists', $result );
    }

    /**
     * Test getting custom fields from the API
     */
    public function test_get_api_custom_fields() {
        // Create a mock API instance
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                         ->disableOriginalConstructor()
                         ->setMethods( ['getCustomFields'] )
                         ->getMock();
        
        // Configure the mock to return predefined custom fields
        $api_mock->expects( $this->once() )
                 ->method( 'getCustomFields' )
                 ->with( $this->equalTo( 'list123' ) )
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
                     ],
                 ] );
        
        // Configure the connection mock to return our API mock
        $this->connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        // Test the get_api_custom_fields method
        $custom_fields = $this->connection->get_api_custom_fields( 'list123' );
        
        // Check the result
        $this->assertIsArray( $custom_fields );
        $this->assertCount( 2, $custom_fields );
        $this->assertEquals( 'phone', $custom_fields[0]['name'] );
        $this->assertEquals( 'company', $custom_fields[1]['name'] );
    }

    /**
     * Test error handling in get_api_custom_fields
     */
    public function test_get_api_custom_fields_error_handling() {
        // Create a mock API instance that throws an exception
        $api_mock = $this->getMockBuilder( 'Thrive_Dash_Api_Mailcoach' )
                         ->disableOriginalConstructor()
                         ->setMethods( ['getCustomFields'] )
                         ->getMock();
        
        // Configure the mock to throw an exception
        $api_mock->method( 'getCustomFields' )
                 ->will( $this->throwException( new Exception( 'API Error' ) ) );
        
        // Configure the connection mock to return our API mock
        $this->connection->method( 'get_api_instance' )->willReturn( $api_mock );
        
        // Test the get_api_custom_fields method with an error condition
        $custom_fields = $this->connection->get_api_custom_fields( 'list123' );
        
        // Check that the result is an empty array when an error occurs
        $this->assertIsArray( $custom_fields );
        $this->assertEmpty( $custom_fields );
    }
}