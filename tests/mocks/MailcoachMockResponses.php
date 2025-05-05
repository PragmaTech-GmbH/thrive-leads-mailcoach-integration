<?php
/**
 * Mock responses for Mailcoach API
 *
 * @package Thrive_Leads_Mailcoach_Integration
 */

/**
 * Class MailcoachMockResponses
 * 
 * Provides realistic mock responses for Mailcoach API endpoints
 */
class MailcoachMockResponses {

    /**
     * Get mock response for a specific API endpoint
     * 
     * @param string $endpoint API endpoint
     * @param array $args Request arguments
     * @param string $method HTTP method
     * @param int $status_code Response status code
     * 
     * @return array Mock response
     */
    public static function get_mock_response($endpoint, $args = [], $method = 'GET', $status_code = 200) {
        $response_body = self::get_response_for_endpoint($endpoint, $args, $method);
        
        return [
            'response' => ['code' => $status_code],
            'body' => json_encode($response_body),
        ];
    }
    
    /**
     * Get response data for a specific endpoint
     * 
     * @param string $endpoint API endpoint
     * @param array $args Request arguments
     * @param string $method HTTP method
     * 
     * @return array Response data
     */
    private static function get_response_for_endpoint($endpoint, $args, $method) {
        // Email lists endpoint
        if (strpos($endpoint, 'email-lists') === 0 && $method === 'GET' && !strpos($endpoint, '/')) {
            return self::get_lists_response();
        }
        
        // Get list details
        if (preg_match('/^email-lists\/([a-z0-9-]+)$/', $endpoint) && $method === 'GET') {
            return self::get_list_details_response($endpoint);
        }
        
        // Custom fields endpoint
        if (preg_match('/^email-lists\/([a-z0-9-]+)\/subscriber-custom-fields$/', $endpoint) && $method === 'GET') {
            return self::get_custom_fields_response();
        }
        
        // Add subscriber endpoint
        if (preg_match('/^email-lists\/([a-z0-9-]+)\/subscribers$/', $endpoint) && $method === 'POST') {
            return self::get_add_subscriber_response($args);
        }
        
        // Get subscribers endpoint
        if (preg_match('/^email-lists\/([a-z0-9-]+)\/subscribers$/', $endpoint) && $method === 'GET') {
            return self::get_subscribers_response();
        }
        
        // Update subscriber endpoint
        if (preg_match('/^subscribers\/([a-z0-9-]+)$/', $endpoint) && $method === 'PATCH') {
            return self::get_update_subscriber_response($args);
        }
        
        // Delete subscriber endpoint
        if (preg_match('/^subscribers\/([a-z0-9-]+)$/', $endpoint) && $method === 'DELETE') {
            return ['success' => true];
        }
        
        // Confirm subscriber endpoint
        if (preg_match('/^subscribers\/([a-z0-9-]+)\/confirm$/', $endpoint) && $method === 'POST') {
            return self::get_confirm_subscriber_response();
        }
        
        // Unsubscribe subscriber endpoint
        if (preg_match('/^subscribers\/([a-z0-9-]+)\/unsubscribe$/', $endpoint) && $method === 'POST') {
            return self::get_unsubscribe_subscriber_response();
        }
        
        // Add tags to subscriber endpoint
        if (preg_match('/^subscribers\/([a-z0-9-]+)\/tags$/', $endpoint) && $method === 'POST') {
            return ['success' => true];
        }
        
        // Default fallback response
        return ['error' => 'Endpoint not mocked: ' . $endpoint];
    }
    
    /**
     * Get mock response for lists endpoint
     * 
     * @return array
     */
    private static function get_lists_response() {
        return [
            'data' => [
                [
                    'uuid' => '12345678-1234-1234-1234-123456789012',
                    'name' => 'Main Newsletter',
                    'created_at' => '2023-01-01T12:00:00.000000Z',
                    'updated_at' => '2023-01-01T12:00:00.000000Z',
                    'allow_form_subscriptions' => true,
                    'campaigns_count' => 12,
                    'active_subscribers_count' => 1500,
                    'default_from_email' => 'newsletter@example.com',
                    'default_from_name' => 'Example Newsletter',
                ],
                [
                    'uuid' => '87654321-4321-4321-4321-210987654321',
                    'name' => 'Product Updates',
                    'created_at' => '2023-02-15T09:30:00.000000Z',
                    'updated_at' => '2023-02-15T09:30:00.000000Z',
                    'allow_form_subscriptions' => true,
                    'campaigns_count' => 5,
                    'active_subscribers_count' => 780,
                    'default_from_email' => 'updates@example.com',
                    'default_from_name' => 'Example Product Team',
                ],
            ],
            'links' => [
                'first' => 'https://example.com/api/email-lists?page=1',
                'last' => 'https://example.com/api/email-lists?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'path' => 'https://example.com/api/email-lists',
                'per_page' => 15,
                'to' => 2,
                'total' => 2,
            ],
        ];
    }
    
    /**
     * Get mock response for list details endpoint
     * 
     * @param string $endpoint
     * 
     * @return array
     */
    private static function get_list_details_response($endpoint) {
        preg_match('/^email-lists\/([a-z0-9-]+)$/', $endpoint, $matches);
        $list_id = $matches[1];
        
        return [
            'uuid' => $list_id,
            'name' => $list_id === '12345678-1234-1234-1234-123456789012' ? 'Main Newsletter' : 'Product Updates',
            'created_at' => '2023-01-01T12:00:00.000000Z',
            'updated_at' => '2023-01-01T12:00:00.000000Z',
            'allow_form_subscriptions' => true,
            'campaigns_count' => 12,
            'active_subscribers_count' => 1500,
            'default_from_email' => 'newsletter@example.com',
            'default_from_name' => 'Example Newsletter',
        ];
    }
    
    /**
     * Get mock response for custom fields endpoint
     * 
     * @return array
     */
    private static function get_custom_fields_response() {
        return [
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
                [
                    'name' => 'interests',
                    'type' => 'array',
                ],
            ],
            'links' => [
                'first' => 'https://example.com/api/email-lists/12345678-1234-1234-1234-123456789012/subscriber-custom-fields?page=1',
                'last' => 'https://example.com/api/email-lists/12345678-1234-1234-1234-123456789012/subscriber-custom-fields?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'path' => 'https://example.com/api/email-lists/12345678-1234-1234-1234-123456789012/subscriber-custom-fields',
                'per_page' => 15,
                'to' => 4,
                'total' => 4,
            ],
        ];
    }
    
    /**
     * Get mock response for add subscriber endpoint
     * 
     * @param array $args
     * 
     * @return array
     */
    private static function get_add_subscriber_response($args) {
        $email = isset($args['email']) ? $args['email'] : 'test@example.com';
        $first_name = isset($args['first_name']) ? $args['first_name'] : '';
        $last_name = isset($args['last_name']) ? $args['last_name'] : '';
        
        return [
            'uuid' => 'sub-' . md5($email),
            'email' => $email,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email_list_uuid' => '12345678-1234-1234-1234-123456789012',
            'subscribed_at' => date('Y-m-d\TH:i:s.u\Z'),
            'unsubscribed_at' => null,
            'created_at' => date('Y-m-d\TH:i:s.u\Z'),
            'updated_at' => date('Y-m-d\TH:i:s.u\Z'),
            'extra_attributes' => isset($args['extra_attributes']) ? $args['extra_attributes'] : [],
            'tags' => [],
        ];
    }
    
    /**
     * Get mock response for subscribers endpoint
     * 
     * @return array
     */
    private static function get_subscribers_response() {
        return [
            'data' => [
                [
                    'uuid' => 'sub-12345',
                    'email' => 'subscriber1@example.com',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'email_list_uuid' => '12345678-1234-1234-1234-123456789012',
                    'subscribed_at' => '2023-01-05T14:32:00.000000Z',
                    'unsubscribed_at' => null,
                    'created_at' => '2023-01-05T14:32:00.000000Z',
                    'updated_at' => '2023-01-05T14:32:00.000000Z',
                    'extra_attributes' => [
                        'company' => 'Example Corp',
                        'phone' => '555-1234',
                    ],
                    'tags' => ['newsletter', 'product-updates'],
                ],
                [
                    'uuid' => 'sub-67890',
                    'email' => 'subscriber2@example.com',
                    'first_name' => 'Jane',
                    'last_name' => 'Smith',
                    'email_list_uuid' => '12345678-1234-1234-1234-123456789012',
                    'subscribed_at' => '2023-02-10T09:15:00.000000Z',
                    'unsubscribed_at' => null,
                    'created_at' => '2023-02-10T09:15:00.000000Z',
                    'updated_at' => '2023-02-10T09:15:00.000000Z',
                    'extra_attributes' => [
                        'company' => 'Another Inc',
                        'birthday' => '1990-05-15',
                    ],
                    'tags' => ['newsletter'],
                ],
            ],
            'links' => [
                'first' => 'https://example.com/api/email-lists/12345678-1234-1234-1234-123456789012/subscribers?page=1',
                'last' => 'https://example.com/api/email-lists/12345678-1234-1234-1234-123456789012/subscribers?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'path' => 'https://example.com/api/email-lists/12345678-1234-1234-1234-123456789012/subscribers',
                'per_page' => 15,
                'to' => 2,
                'total' => 2,
            ],
        ];
    }
    
    /**
     * Get mock response for update subscriber endpoint
     * 
     * @param array $args
     * 
     * @return array
     */
    private static function get_update_subscriber_response($args) {
        $email = isset($args['email']) ? $args['email'] : 'updated@example.com';
        $first_name = isset($args['first_name']) ? $args['first_name'] : 'Updated';
        $last_name = isset($args['last_name']) ? $args['last_name'] : 'User';
        
        return [
            'uuid' => 'sub-12345',
            'email' => $email,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email_list_uuid' => '12345678-1234-1234-1234-123456789012',
            'subscribed_at' => '2023-01-05T14:32:00.000000Z',
            'unsubscribed_at' => null,
            'created_at' => '2023-01-05T14:32:00.000000Z',
            'updated_at' => date('Y-m-d\TH:i:s.u\Z'),
            'extra_attributes' => isset($args['extra_attributes']) ? $args['extra_attributes'] : [],
            'tags' => isset($args['tags']) ? $args['tags'] : [],
        ];
    }
    
    /**
     * Get mock response for confirm subscriber endpoint
     * 
     * @return array
     */
    private static function get_confirm_subscriber_response() {
        return [
            'uuid' => 'sub-12345',
            'email' => 'confirmed@example.com',
            'first_name' => 'Confirmed',
            'last_name' => 'User',
            'email_list_uuid' => '12345678-1234-1234-1234-123456789012',
            'subscribed_at' => date('Y-m-d\TH:i:s.u\Z'),
            'unsubscribed_at' => null,
            'created_at' => '2023-01-05T14:32:00.000000Z',
            'updated_at' => date('Y-m-d\TH:i:s.u\Z'),
            'extra_attributes' => [],
            'tags' => [],
        ];
    }
    
    /**
     * Get mock response for unsubscribe subscriber endpoint
     * 
     * @return array
     */
    private static function get_unsubscribe_subscriber_response() {
        return [
            'uuid' => 'sub-12345',
            'email' => 'unsubscribed@example.com',
            'first_name' => 'Unsubscribed',
            'last_name' => 'User',
            'email_list_uuid' => '12345678-1234-1234-1234-123456789012',
            'subscribed_at' => '2023-01-05T14:32:00.000000Z',
            'unsubscribed_at' => date('Y-m-d\TH:i:s.u\Z'),
            'created_at' => '2023-01-05T14:32:00.000000Z',
            'updated_at' => date('Y-m-d\TH:i:s.u\Z'),
            'extra_attributes' => [],
            'tags' => [],
        ];
    }
    
    /**
     * Generate mock error responses
     * 
     * @param string $error_type Type of error to generate
     * 
     * @return array Error response
     */
    public static function get_mock_error($error_type) {
        switch ($error_type) {
            case 'invalid_api_key':
                return [
                    'response' => ['code' => 401],
                    'body' => json_encode([
                        'message' => 'Unauthenticated.',
                    ]),
                ];
            
            case 'subscriber_exists':
                return [
                    'response' => ['code' => 422],
                    'body' => json_encode([
                        'message' => 'The email has already been taken.',
                        'errors' => [
                            'email' => [
                                'The email has already been taken.',
                            ],
                        ],
                    ]),
                ];
            
            case 'invalid_email':
                return [
                    'response' => ['code' => 422],
                    'body' => json_encode([
                        'message' => 'The email must be a valid email address.',
                        'errors' => [
                            'email' => [
                                'The email must be a valid email address.',
                            ],
                        ],
                    ]),
                ];
                
            case 'rate_limit':
                return [
                    'response' => ['code' => 429],
                    'body' => json_encode([
                        'message' => 'Too Many Attempts.',
                    ]),
                ];
                
            case 'server_error':
                return [
                    'response' => ['code' => 500],
                    'body' => json_encode([
                        'message' => 'Server Error',
                    ]),
                ];
                
            case 'list_not_found':
                return [
                    'response' => ['code' => 404],
                    'body' => json_encode([
                        'message' => 'Email list not found.',
                    ]),
                ];
                
            case 'subscriber_not_found':
                return [
                    'response' => ['code' => 404],
                    'body' => json_encode([
                        'message' => 'Subscriber not found.',
                    ]),
                ];
                
            default:
                return [
                    'response' => ['code' => 400],
                    'body' => json_encode([
                        'message' => 'Bad Request',
                    ]),
                ];
        }
    }
}