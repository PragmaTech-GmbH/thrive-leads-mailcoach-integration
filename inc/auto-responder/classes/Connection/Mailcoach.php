<?php

/**
 * Thrive Leads Mailcoach Connection
 */
class Thrive_Dash_List_Connection_Mailcoach extends Thrive_Dash_List_Connection_Abstract {
    /**
     * @return string
     */
    public function get_title() {
        return 'Mailcoach';
    }

    /**
     * @return string
     */
    public function get_type() {
        return 'autoresponder';
    }

    /**
     * @return bool
     */
    public function has_tags() {
        return true;
    }

    /**
     * @return bool
     */
    public function has_custom_fields() {
        return true;
    }

    /**
     * @return bool
     */
    public function has_optin() {
        return true;
    }

    /**
     * Output the setup form html
     */
    public function output_setup_form() {
        $this->output_controls_html('mailcoach');
    }

    /**
     * Read credentials entered by user in the setup form
     *
     * @param array $submitted_data
     *
     * @return bool|string true for success or error message for failure
     */
    public function read_credentials($submitted_data) {
        // Extract API key from submitted data
        $api_key = isset($submitted_data['connection']['api_key']) 
            ? sanitize_text_field($submitted_data['connection']['api_key']) 
            : '';
        
        // Extract API URL from submitted data
        $api_url = isset($submitted_data['connection']['api_url']) 
            ? sanitize_text_field($submitted_data['connection']['api_url']) 
            : '';

        if (empty($api_key)) {
            return __('API Key is required', 'thrive-dash');
        }

        if (empty($api_url)) {
            return __('API URL is required', 'thrive-dash');
        }

        $this->set_credentials(array(
            'api_key' => $api_key,
            'api_url' => $api_url,
        ));

        $result = $this->test_connection();

        if ($result !== true) {
            return $result;
        }

        $this->save();

        return true;
    }

    /**
     * Test the connection to Mailcoach
     *
     * @return bool|string true on success, error message on failure
     */
    public function test_connection() {
        try {
            $this->get_api_instance()->getLists();

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Add a subscriber to a list
     *
     * @param mixed $list_identifier
     * @param array $arguments
     *
     * @return bool|string true on success, error message on failure
     */
    public function add_subscriber($list_identifier, $arguments) {
        try {
            $api = $this->get_api_instance();
            
            $subscriber_data = array(
                'email'      => $arguments['email'],
                'first_name' => isset($arguments['name']) ? $arguments['name'] : '',
                'last_name'  => isset($arguments['last_name']) ? $arguments['last_name'] : '',
            );

            // Add additional fields if they exist
            if (!empty($arguments['phone'])) {
                $subscriber_data['extra_attributes']['phone'] = $arguments['phone'];
            }

            // Process custom fields
            $custom_fields = $this->_process_custom_fields($arguments);
            if (!empty($custom_fields)) {
                $subscriber_data['extra_attributes'] = isset($subscriber_data['extra_attributes']) ? 
                    array_merge($subscriber_data['extra_attributes'], $custom_fields) : 
                    $custom_fields;
            }

            // Handle opt-in settings
            if (isset($arguments['mailcoach_optin']) && $arguments['mailcoach_optin'] === 'double') {
                $subscriber_data['requires_confirmation'] = true;
            } else {
                $subscriber_data['requires_confirmation'] = false;
            }
          
            $response = $api->addSubscriber($list_identifier, $subscriber_data);
            
            // Process tags if available
            if (!empty($arguments['mailcoach_tags']) && isset($response['uuid'])) {
                $tags = explode(',', $arguments['mailcoach_tags']);
                $tags = array_map('trim', $tags);
                
                if (!empty($tags)) {
                    $api->addTagsToSubscriber($response['uuid'], $tags);
                }
            }

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Get all available custom fields for a list
     *
     * @param string $list_id
     *
     * @return array
     */
    public function get_api_custom_fields($list_id) {
        $api = $this->get_api_instance();
        $custom_fields = array();

        try {
            $fields = $api->getCustomFields($list_id);

            if (!empty($fields['data'])) {
                foreach ($fields['data'] as $field) {
                    $custom_fields[] = array(
                        'name' => $field['name'],
                        'id'   => $field['name'],
                        'type' => 'text', // Mailcoach doesn't have strict types like Mailchimp
                    );
                }
            }
        } catch (Exception $e) {
            // Just return empty array if there's an error
        }

        return $custom_fields;
    }

    /**
     * Process custom fields and return them in the format expected by Mailcoach
     *
     * @param array $arguments
     *
     * @return array
     */
    protected function _process_custom_fields($arguments) {
        $custom_fields = array();

        if (empty($arguments['extra'])) {
            return $custom_fields;
        }

        foreach ($arguments['extra'] as $key => $value) {
            $custom_fields[$key] = $value;
        }

        return $custom_fields;
    }

    /**
     * Get all available lists
     *
     * @return array
     */
    protected function _get_lists() {
        $lists = array();
        $api = $this->get_api_instance();

        try {
            $response = $api->getLists();

            if (!empty($response['data'])) {
                foreach ($response['data'] as $list) {
                    $lists[] = array(
                        'id'   => $list['uuid'],
                        'name' => $list['name'],
                    );
                }
            }
        } catch (Exception $e) {
            // Just return empty array if there's an error
        }

        return $lists;
    }

    /**
     * Get instance of API class
     *
     * @return Thrive_Dash_Api_Mailcoach
     */
    protected function get_api_instance() {
        $api_key = $this->param('api_key');
        $api_url = $this->param('api_url');

        return new Thrive_Dash_Api_Mailcoach($api_key, $api_url);
    }
}
