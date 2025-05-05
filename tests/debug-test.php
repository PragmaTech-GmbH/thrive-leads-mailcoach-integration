<?php
/**
 * Debug test script
 */

// Include the bootstrap
require_once __DIR__ . '/bootstrap.php';

// Create a simple test
class SimpleTest extends \PHPUnit\Framework\TestCase {
    public function testTrue() {
        $this->assertTrue(true);
    }
}

// Run the test manually
$test = new SimpleTest();
$result = $test->testTrue();
echo "Test passed!\n";