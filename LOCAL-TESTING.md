# Local Testing Guide for Thrive Leads Mailcoach Integration

There are two ways to run tests for this project:

1. Using Docker (recommended, works on all platforms)
2. Running tests directly on your local machine (requires local PHP installation)

## Option 1: Using Docker

### Prerequisites

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

### Running Tests with Docker

```bash
# Run the tests (all unit and integration tests)
./run-tests.sh

# Run only unit tests
./run-tests.sh --unit

# Run only integration tests
./run-tests.sh --integration

# Generate code coverage report
./run-tests.sh --coverage

# View all options
./run-tests.sh --help
```

If you're on Apple Silicon (M1/M2/M3), you might need to adjust the Docker Compose file to use arm64v8 images. The current docker-compose.yml is configured for ARM64 architecture.

## Option 2: Running Tests Directly

If you prefer to run tests directly on your machine, follow these steps:

### Prerequisites

- PHP 7.4 or higher
- MySQL or MariaDB
- Composer

### Setting Up the WordPress Test Environment

1. Install dependencies:
   ```bash
   composer install
   ```

2. Set up the WordPress test environment:
   ```bash
   bash bin/install-wp-tests.sh wordpress_test root 'root_password' localhost latest
   ```
   Replace `root_password` with your MySQL root password.

### Running Tests with Composer

```bash
# Run all tests
composer test

# Run unit tests only
composer test:unit

# Run integration tests only
composer test:integration
```

### Running Tests with PHPUnit Directly

```bash
# Run all tests
./vendor/bin/phpunit

# Run unit tests only
./vendor/bin/phpunit --testsuite=unit

# Run integration tests only
./vendor/bin/phpunit --testsuite=integration

# Generate code coverage report (requires Xdebug)
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html tests/coverage
```

## Understanding Test Files

- **Unit Tests**: Located in `tests/unit/` directory
  - `test-mailcoach-api-improved.php`: Tests the Mailcoach API client
  - `test-mailcoach-connection-improved.php`: Tests the Thrive Leads connection

- **Integration Tests**: Located in `tests/integration/` directory
  - `test-integration-hooks.php`: Tests WordPress hooks and filters
  - `test-subscriber-management.php`: Tests end-to-end subscriber management

## Troubleshooting

### Error: "No such file or directory" for tests/bootstrap.php

Make sure you're running the tests from the plugin's root directory.

### Tests fail with database connection errors

Check that your MySQL server is running and the credentials in `bin/install-wp-tests.sh` are correct.

### PHPUnit not found

Make sure you've run `composer install` and are using the correct path to PHPUnit (./vendor/bin/phpunit).

### Code coverage report not generating

Make sure Xdebug is installed and configured:

```bash
php -m | grep xdebug
```

### API mocking issues

The tests use Brain\Monkey for mocking WordPress functions and MailcoachMockResponses for API responses. Check that these are working correctly.