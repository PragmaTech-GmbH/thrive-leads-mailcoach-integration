.PHONY: up down restart build tests clean logs shell install-dependencies update-dependencies

# Start the Docker environment
up:
	docker-compose up -d

# Stop the Docker environment
down:
	docker-compose down

# Restart the Docker environment
restart:
	docker-compose restart

# Build the Docker environment
build:
	docker-compose build

# Run PHPUnit tests
tests:
	docker-compose run --rm phpunit

# Run unit tests only
unit-tests:
	docker-compose run --rm phpunit bash -c "cd /var/www/html/wp-content/plugins/thrive-leads-mailcoach-integration && vendor/bin/phpunit --testsuite=unit"

# Run integration tests only
integration-tests:
	docker-compose run --rm phpunit bash -c "cd /var/www/html/wp-content/plugins/thrive-leads-mailcoach-integration && vendor/bin/phpunit --testsuite=integration"

# Clean the Docker environment
clean:
	docker-compose down -v

# View logs
logs:
	docker-compose logs -f

# Open a shell in the WordPress container
shell:
	docker-compose exec wordpress bash

# Install Composer dependencies
install-dependencies:
	docker-compose run --rm composer install

# Update Composer dependencies
update-dependencies:
	docker-compose run --rm composer update

# Install WordPress test environment
install-wp-tests:
	docker-compose run --rm phpunit bash -c "cd /var/www/html/wp-content/plugins/thrive-leads-mailcoach-integration && bash bin/install-wp-tests.sh wordpress_test root rootpassword db latest"

# Generate code coverage report
coverage:
	docker-compose run --rm phpunit bash -c "cd /var/www/html/wp-content/plugins/thrive-leads-mailcoach-integration && XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html ./tests/coverage"

# Run PHP CodeSniffer
lint:
	docker-compose run --rm phpunit bash -c "cd /var/www/html/wp-content/plugins/thrive-leads-mailcoach-integration && vendor/bin/phpcs"

# Fix PHP CodeSniffer errors
lint-fix:
	docker-compose run --rm phpunit bash -c "cd /var/www/html/wp-content/plugins/thrive-leads-mailcoach-integration && vendor/bin/phpcbf"