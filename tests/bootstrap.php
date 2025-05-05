<?php
/**
 * PHPUnit bootstrap file for Thrive Leads Mailcoach Integration plugin tests
 */

// Check if Composer autoloader exists
if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    require_once dirname(__DIR__) . '/vendor/autoload.php';
} else {
    echo "Error: Composer autoloader not found. Run 'composer install' first.\n";
    exit(1);
}

// Initialize Brain\Monkey before defining WordPress functions
// This prevents the "Patchwork\Exceptions\DefinedTooEarly" error
Brain\Monkey\setUp();

/**
 * Setup testing environment helper function
 */
function _tl_mailcoach_set_env() {
    // Register mock Thrive Leads classes
    if (!class_exists('Thrive_Leads_Plugin')) {
        class Thrive_Leads_Plugin {}
    }

    if (!class_exists('Thrive_Dash_List_Connection_Abstract')) {
        abstract class Thrive_Dash_List_Connection_Abstract {
            public function param($key) {
                return 'test-value';
            }

            public function set_credentials($data) {
                return true;
            }

            public function save() {
                return true;
            }

            public function output_controls_html($provider) {
                return true;
            }

            public function display_video_link() {
                return true;
            }

            public abstract function get_title();
            public abstract function output_setup_form();
            public abstract function read_credentials($post_data);
            public abstract function test_connection();
            public abstract function add_subscriber($list_id, $arguments);
            
            protected abstract function _get_lists();
        }
    }

    if (!class_exists('Thrive_Dash_List_Manager')) {
        class Thrive_Dash_List_Manager {
            public static $AVAILABLE = array();
        }
    }

    // Do NOT define any WordPress functions here
    // They will be mocked by Brain\Monkey in each test
    
    // Make sure the Thrive Leads Mailcoach Integration class is available for mocking
    require_once dirname(__FILE__) . '/mocks/class-thrive-leads-mailcoach-integration-mock.php';
}

/**
 * Set up WordPress tests if available, otherwise use mocks
 */
function _tl_mailcoach_bootstrap() {
    // Check if we're in a WordPress test environment
    if (getenv('WP_TESTS_DIR')) {
        $tests_dir = getenv('WP_TESTS_DIR');
        
        // Check if the directory exists
        if (file_exists($tests_dir . '/includes/functions.php')) {
            require_once $tests_dir . '/includes/functions.php';
            require_once $tests_dir . '/includes/bootstrap.php';
            
            // Load our plugin
            function _manually_load_plugin() {
                require dirname(dirname(__FILE__)) . '/thrive-leads-mailcoach.php';
            }
            tests_add_filter('muplugins_loaded', '_manually_load_plugin');
            
            return true;
        }
    }
    
    // If we don't have WordPress tests available, use mocks
    _tl_mailcoach_set_env();
    
    // Create a minimal WP_UnitTestCase
    if (!class_exists('WP_UnitTestCase')) {
        class WP_UnitTestCase extends \PHPUnit\Framework\TestCase {
            // Minimal implementation with PHPUnit 8 compatibility
            public function setUp(): void {
                parent::setUp();
                // Brain\Monkey is already set up in the bootstrap file
                // No need to call Brain\Monkey\setUp() here
            }
            
            public function tearDown(): void {
                parent::tearDown();
            }
        }
    }
    
    // Define WP_Error class for use in tests
    if (!class_exists('WP_Error')) {
        class WP_Error {
            protected $code;
            protected $message;
            protected $data;

            public function __construct($code = '', $message = '', $data = '') {
                $this->code = $code;
                $this->message = $message;
                $this->data = $data;
            }

            public function get_error_message() {
                return $this->message;
            }

            public function get_error_code() {
                return $this->code;
            }

            public function get_error_data($key = '') {
                return $this->data;
            }
        }
    }
    
    return false;
}

// Create test directory structure if it doesn't exist
$dirs = array(
    dirname(__FILE__) . '/unit',
    dirname(__FILE__) . '/integration',
    dirname(__FILE__) . '/mocks',
);

foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Register our autoloader
spl_autoload_register(function($class) {
    // First check if the class is in our primary code
    $inc_path = dirname(__DIR__) . '/inc/';
    $class_file = str_replace('_', '/', $class) . '.php';
    $inc_file = $inc_path . $class_file;
    
    if (file_exists($inc_file)) {
        require_once $inc_file;
        return;
    }
    
    // Then check if it's in our mocks
    $mocks_path = __DIR__ . '/mocks/';
    $mocked_file = $mocks_path . $class_file;

    if (file_exists($mocked_file)) {
        require_once $mocked_file;
    }
});

// Initialize the test environment
_tl_mailcoach_bootstrap();

// Register teardown function to be called at the end of tests
register_shutdown_function(function() {
    Brain\Monkey\tearDown();
});