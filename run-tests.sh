#!/bin/bash
set -e

# Usage function
usage() {
  echo "Usage: $0 [options]"
  echo "Options:"
  echo "  -a, --all        Run all tests (default)"
  echo "  -u, --unit       Run only unit tests"
  echo "  -i, --integration Run only integration tests"
  echo "  -c, --coverage   Generate code coverage report"
  echo "  -v, --verbose    Show verbose output"
  echo "  -h, --help       Display this help message"
  exit 1
}

# Default options
RUN_ALL=true
RUN_UNIT=false
RUN_INTEGRATION=false
GENERATE_COVERAGE=false
VERBOSE=false

# Parse command line arguments
while [[ $# -gt 0 ]]; do
  case "$1" in
    -a|--all)
      RUN_ALL=true
      RUN_UNIT=false
      RUN_INTEGRATION=false
      shift
      ;;
    -u|--unit)
      RUN_ALL=false
      RUN_UNIT=true
      shift
      ;;
    -i|--integration)
      RUN_ALL=false
      RUN_INTEGRATION=true
      shift
      ;;
    -c|--coverage)
      GENERATE_COVERAGE=true
      shift
      ;;
    -v|--verbose)
      VERBOSE=true
      shift
      ;;
    -h|--help)
      usage
      ;;
    *)
      echo "Unknown option: $1"
      usage
      ;;
  esac
done

# Check if Docker and Docker Compose are installed
if ! command -v docker &> /dev/null; then
  echo "Docker is not installed. Please install Docker and try again."
  exit 1
fi

if ! command -v docker-compose &> /dev/null; then
  echo "Docker Compose is not installed. Please install Docker Compose and try again."
  exit 1
fi

echo "===== Setting up test environment ====="
echo "Starting Docker containers..."

if [ "$VERBOSE" = true ]; then
  docker-compose up -d
else
  docker-compose up -d > /dev/null 2>&1
fi

echo "Waiting for containers to be ready..."
sleep 10

# Try to wait for MySQL to be ready
echo "Checking if MySQL is ready..."
for i in {1..30}; do
  if docker-compose exec -T mysql mysqladmin ping -h localhost -u root -prootpassword --silent &> /dev/null; then
    echo "MySQL is ready!"
    break
  fi
  echo "Waiting for MySQL... ($i/30)"
  sleep 2
  if [ "$i" -eq 30 ]; then
    echo "MySQL did not become ready in time. Please try again or check Docker logs."
    exit 1
  fi
done

# Make sure the WordPress test environment is set up
echo "===== Setting up WordPress test environment ====="
docker-compose exec wordpress-test mkdir -p /tmp/wordpress-tests-lib

echo "Installing WordPress test suite..."
INSTALL_OUTPUT=$(docker-compose exec -T wordpress-test bash bin/install-wp-tests.sh wordpress_test root rootpassword mysql latest 2>&1)
if [ "$VERBOSE" = true ]; then
  echo "$INSTALL_OUTPUT"
fi

# Install Xdebug if coverage is requested
if [ "$GENERATE_COVERAGE" = true ]; then
  echo "===== Installing Xdebug for code coverage ====="
  if [ "$VERBOSE" = true ]; then
    docker-compose exec -T wordpress-test bash bin/setup-xdebug.sh
  else
    docker-compose exec -T wordpress-test bash bin/setup-xdebug.sh > /dev/null 2>&1
  fi
  COVERAGE_OPTION="--coverage-html=./tests/coverage"
  echo "Code coverage will be generated in ./tests/coverage directory"
else
  COVERAGE_OPTION=""
fi

# Run tests based on options
echo "===== Running tests ====="
if [ "$RUN_ALL" = true ]; then
  echo "Running all tests..."
  docker-compose exec -T wordpress-test php -d display_errors=1 -d error_reporting=E_ALL ./vendor/bin/phpunit $COVERAGE_OPTION --debug
elif [ "$RUN_UNIT" = true ]; then
  echo "Running unit tests only..."
  docker-compose exec -T wordpress-test ./vendor/bin/phpunit --testsuite=unit $COVERAGE_OPTION
elif [ "$RUN_INTEGRATION" = true ]; then
  echo "Running integration tests only..."
  docker-compose exec -T wordpress-test ./vendor/bin/phpunit --testsuite=integration $COVERAGE_OPTION
fi

TEST_EXIT_CODE=$?

# Display test results location
if [ "$GENERATE_COVERAGE" = true ]; then
  echo "Coverage report is available at: ./tests/coverage/index.html"
fi

echo "===== Test summary ====="
if [ $TEST_EXIT_CODE -eq 0 ]; then
  echo "✅ Tests completed successfully!"
else
  echo "❌ Tests failed with exit code $TEST_EXIT_CODE"
fi

echo "You can stop the Docker containers with: docker-compose down"
echo "Or leave them running for faster subsequent test runs."

exit $TEST_EXIT_CODE