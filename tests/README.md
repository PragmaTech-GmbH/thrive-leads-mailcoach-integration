# Thrive Leads Mailcoach Integration Tests

This directory contains the test suite for the Thrive Leads Mailcoach Integration plugin.

## Test Structure

The tests are organized into two main categories:

1. **Unit Tests** - Testing individual components in isolation
   - API class tests
   - Connection class tests

2. **Integration Tests** - Testing how components work together
   - WordPress hooks tests
   - Subscriber management tests

## Setting Up the Test Environment

### Prerequisites

- WordPress development environment
- PHPUnit
- WP-CLI (optional, for easier setup)

### Installation

1. Clone the WordPress test suite:

```bash
bash bin/install-wp-tests.sh wordpress_test root '' localhost latest
```

2. Install Composer dependencies:

```bash
composer install
```

## Running Tests

### Running All Tests

```bash
composer test
```

### Running Unit Tests Only

```bash
composer test:unit
```

### Running Integration Tests Only

```bash
composer test:integration
```

### Running Specific Test File

```bash
vendor/bin/phpunit tests/unit/test-mailcoach-api.php
```

## Test Coverage

Generate a test coverage report:

```bash
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html tests/coverage
```

## Mocking

The tests use several mocking strategies:

1. **Function Mocking** - Using Brain\Monkey to mock WordPress functions
2. **Class Mocking** - Using PHPUnit's mocking capabilities
3. **WordPress Hook Mocking** - Testing hooks are properly registered

## Test Data

Test data is generated programmatically to avoid reliance on external services.

## Continuous Integration

These tests are configured to run in CI environments. The phpunit.xml configuration is set up to work with most CI services.

## Common Issues

### Missing WordPress Test Suite

If you get an error about missing WordPress test files, make sure you've run the install-wp-tests.sh script.

### Mocked Classes Not Found

If tests fail with "Class not found" errors, check that the autoloader in tests/bootstrap.php is correctly configured.

### WP_UnitTestCase Not Found

Make sure the WordPress test suite is correctly installed and the WP_TESTS_DIR environment variable is set.