<?php

/**
 * Mailcoach API implementation
 */
class Thrive_Dash_Api_Mailcoach {
    /**
     * @var string API key
     */
    protected $api_key;

    /**
     * @var string API endpoint
     */
    protected $api_url;

    /**
     * @var array API request headers
     */
    protected $headers = array();

    /**
     * Constructor
     *
     * @param string $api_key
     * @param string $api_url
     */
    public function __construct($api_key, $api_url) {
        $this->api_key = $api_key;
        $this->api_url = rtrim($api_url, '/');

        $this->headers = array(
            'Authorization' => 'Bearer ' . $this->api_key,
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json'
        );
    }

    /**
     * Get all available email lists
     *
     * @return array
     * @throws Thrive_Dash_Api_Mailcoach_Exception
     */
    public function getLists() {
        return $this->_call('email-lists');
    }

    /**
     * Get list details including custom fields
     *
     * @param string $list_id
     * @return array
     * @throws Thrive_Dash_Api_Mailcoach_Exception
     */
    public function getList($list_id) {
        return $this->_call('email-lists/' . $list_id);
    }

    /**
     * Get subscribers for a list
     *
     * @param string $list_id
     * @return array
     * @throws Thrive_Dash_Api_Mailcoach_Exception
     */
    public function getSubscribers($list_id) {
        return $this->_call('email-lists/' . $list_id . '/subscribers');
    }

    /**
     * Add subscriber to a list
     *
     * @param string $list_id
     * @param array $arguments
     * @return array
     * @throws Thrive_Dash_Api_Mailcoach_Exception
     */
    public function addSubscriber($list_id, $arguments) {
        return $this->_call('email-lists/' . $list_id . '/subscribers', $arguments, 'POST');
    }

    /**
     * Update subscriber
     *
     * @param string $subscriber_id
     * @param array $arguments
     * @return array
     * @throws Thrive_Dash_Api_Mailcoach_Exception
     */
    public function updateSubscriber($subscriber_id, $arguments) {
        return $this->_call('subscribers/' . $subscriber_id, $arguments, 'PATCH');
    }

    /**
     * Delete subscriber
     *
     * @param string $subscriber_id
     * @return array
     * @throws Thrive_Dash_Api_Mailcoach_Exception
     */
    public function deleteSubscriber($subscriber_id) {
        return $this->_call('subscribers/' . $subscriber_id, array(), 'DELETE');
    }

    /**
     * Unsubscribe subscriber
     *
     * @param string $subscriber_id
     * @return array
     * @throws Thrive_Dash_Api_Mailcoach_Exception
     */
    public function unsubscribeSubscriber($subscriber_id) {
        return $this->_call('subscribers/' . $subscriber_id . '/unsubscribe', array(), 'POST');
    }

    /**
     * Confirm subscriber
     *
     * @param string $subscriber_id
     * @return array
     * @throws Thrive_Dash_Api_Mailcoach_Exception
     */
    public function confirmSubscriber($subscriber_id) {
        return $this->_call('subscribers/' . $subscriber_id . '/confirm', array(), 'POST');
    }

    /**
     * Get custom fields for a list
     *
     * @param string $list_id
     * @return array
     * @throws Thrive_Dash_Api_Mailcoach_Exception
     */
    public function getCustomFields($list_id) {
        return $this->_call('email-lists/' . $list_id . '/subscriber-custom-fields');
    }

    /**
     * Add tags to subscriber
     *
     * @param string $subscriber_id
     * @param array $tags
     * @return array
     * @throws Thrive_Dash_Api_Mailcoach_Exception
     */
    public function addTagsToSubscriber($subscriber_id, $tags) {
        return $this->_call('subscribers/' . $subscriber_id . '/tags', array('tags' => $tags), 'POST');
    }

    /**
     * Remove tags from subscriber
     *
     * @param string $subscriber_id
     * @param array $tags
     * @return array
     * @throws Thrive_Dash_Api_Mailcoach_Exception
     */
    public function removeTagsFromSubscriber($subscriber_id, $tags) {
        return $this->_call('subscribers/' . $subscriber_id . '/tags', array('tags' => $tags), 'DELETE');
    }

    /**
     * Make an API call
     *
     * @param string $endpoint
     * @param array $data
     * @param string $method
     * @return array
     * @throws Thrive_Dash_Api_Mailcoach_Exception
     */
    protected function _call($endpoint, $data = array(), $method = 'GET') {
        $url = $this->api_url . '/api/' . $endpoint;

        $args = array(
            'headers' => $this->headers,
            'method'  => $method,
        );

        if ($method !== 'GET' && !empty($data)) {
            $args['body'] = json_encode($data);
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            $error_message = method_exists($response, 'get_error_message') ? 
                $response->get_error_message() : 
                'Network connection failed';
            throw new Thrive_Dash_Api_Mailcoach_Exception($error_message);
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body        = wp_remote_retrieve_body($response);
        $data        = json_decode($body, true);

        if ($status_code >= 400) {
            $message = isset($data['message']) ? $data['message'] : 'Unknown error occurred';
            throw new Thrive_Dash_Api_Mailcoach_Exception($message, $status_code);
        }

        return $data;
    }
}

/**
 * Mailcoach API Exception
 */
class Thrive_Dash_Api_Mailcoach_Exception extends Exception {}