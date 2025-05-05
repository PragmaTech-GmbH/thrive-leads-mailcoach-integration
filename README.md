# Thrive Leads - Mailcoach Integration

This plugin adds Mailcoach integration to Thrive Leads, allowing you to connect your Thrive Leads forms with Mailcoach for email marketing.

## Installation

1. Download the plugin zip file
2. Go to WordPress Admin > Plugins > Add New > Upload Plugin
3. Upload the zip file
4. Activate the plugin

## Setup

1. Go to Thrive Dashboard > API Connections
2. Click "Add New Connection"
3. Select "Mailcoach" from the list
4. Enter your Mailcoach API Key and API URL
5. Click "Save" to connect

## Requirements

- WordPress 5.0 or higher
- Thrive Leads 2.0 or higher
- Active Mailcoach account with API access

## Features

- Connect to Mailcoach API
- Select email lists from your Mailcoach account
- Map form fields to Mailcoach custom fields
- Support for both single and double opt-in
- Tag subscribers based on form submissions

## Development & Testing

The plugin includes a Docker-based testing environment that doesn't require any local PHP, MySQL, or web server installation.

### Requirements for Development

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

### Running Tests

We provide a convenient script to run tests in Docker:

```bash
# Make the script executable (if needed)
chmod +x run-tests.sh

# Run all tests
./run-tests.sh

# Run only unit tests
./run-tests.sh --unit

# Run only integration tests
./run-tests.sh --integration

# Generate code coverage report
./run-tests.sh --coverage
```

For more detailed information about the testing setup, see [LOCAL-TESTING.md](LOCAL-TESTING.md).

### Test Coverage Report

After running tests with the coverage option, open `tests/coverage/index.html` in your browser to view the code coverage report.

## Support

For support, please contact [your support email]

## License

This plugin is licensed under the GPL v2 or later.

## Frequently Asked Questions

### Will this plugin be affected by Thrive Leads updates?

No, this plugin is designed to work alongside Thrive Leads without modifying any core Thrive Leads files. It adds the Mailcoach integration through WordPress hooks, so it will continue to work after Thrive Leads updates.

### How do I get my Mailcoach API key?

Log in to your Mailcoach account, go to Settings > API Tokens, and create a new API token with appropriate permissions.

### How do I configure double opt-in?

When creating or editing a Thrive Leads form, go to the "API Connections" step, select your Mailcoach connection, and you'll see an option for opt-in settings.