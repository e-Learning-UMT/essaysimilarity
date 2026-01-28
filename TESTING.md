# Testing Guide for Essay Similarity Plugin

This guide explains how to test the Essay Similarity plugin locally before committing changes.

## Prerequisites

- Moodle 5.1 (or compatible version) installed
- PHP 8.1, 8.2, or 8.3
- Composer installed
- PostgreSQL or MariaDB database

## Quick Start

### Using the Test Script

We've provided a convenient test script that automates most testing tasks:

```bash
cd question/type/essaysimilarity
./test_plugin.sh all
```

This will run:
- PHP syntax checking (phplint)
- Plugin validation
- Moodle code style checker (phpcs)

### Running PHPUnit Tests

1. Initialize PHPUnit (first time only):
```bash
./test_plugin.sh init-phpunit
```

2. Run all PHPUnit tests:
```bash
./test_plugin.sh phpunit
```

3. Run a specific test:
```bash
./test_plugin.sh test questiontype_test.php
```

## Manual Testing

### 1. PHP Syntax Check

Check for PHP syntax errors:

```bash
cd /tmp
composer create-project -n --no-dev --prefer-dist moodlehq/moodle-plugin-ci ci ^4
export PATH="/tmp/ci/bin:$PATH"

cd /path/to/moodle/question/type/essaysimilarity
moodle-plugin-ci phplint .
```

### 2. Plugin Validation

Validate plugin structure and required files:

```bash
cd /path/to/moodle/question/type/essaysimilarity
moodle-plugin-ci validate .
```

### 3. Code Style Checking

Check compliance with Moodle coding standards:

```bash
cd /path/to/moodle
moodle-plugin-ci phpcs --max-warnings 0 question/type/essaysimilarity
```

**Note:** The plugin currently has some coding style issues that need to be addressed. These are non-critical but should be fixed for best practices.

### 4. PHPUnit Tests

#### Initialize PHPUnit

First time setup (this may take several minutes):

```bash
cd /path/to/moodle
php admin/tool/phpunit/cli/init.php
```

#### Run Plugin Tests

Run all tests for the essay similarity plugin:

```bash
cd /path/to/moodle
vendor/bin/phpunit --testsuite="qtype_essaysimilarity_testsuite"
```

#### Run Specific Tests

Run a specific test class:

```bash
vendor/bin/phpunit question/type/essaysimilarity/tests/questiontype_test.php
vendor/bin/phpunit question/type/essaysimilarity/tests/question_test.php
vendor/bin/phpunit question/type/essaysimilarity/tests/tokenizer_test.php
vendor/bin/phpunit question/type/essaysimilarity/tests/cosine_similarity_test.php
```

## Test Coverage

The plugin includes the following test files:

### questiontype_test.php
Tests the question type class including:
- Manual grading status
- Extra question fields
- Response file areas
- Save and delete operations
- Default values

### question_test.php
Tests the question definition class including:
- Response validation
- Grading logic
- Text statistics
- Similarity calculation

### tokenizer_test.php
Tests the NLP tokenizer including:
- English text tokenization
- Punctuation handling
- Case conversion
- Empty string handling

### cosine_similarity_test.php
Tests the cosine similarity calculator including:
- Identical vectors
- Orthogonal vectors
- Similar vectors
- Edge cases

### privacy_provider_test.php
Tests GDPR/privacy compliance

## Continuous Integration

The plugin includes GitHub Actions CI configuration that automatically tests:

- Multiple PHP versions (8.0, 8.1, 8.2, 8.3)
- Multiple Moodle versions (4.1 LTS, 4.3, 4.4, 5.0, 5.1)
- Multiple databases (PostgreSQL, MariaDB)

The CI runs on every push and pull request.

## Common Issues

### PHPUnit Init Timeout

If `phpunit init` times out, try:
- Increasing the timeout: `timeout 300 php admin/tool/phpunit/cli/init.php`
- Running in background and monitoring: `php admin/tool/phpunit/cli/init.php > /tmp/phpunit-init.log 2>&1 &`

### Coding Style Errors

The plugin has some coding style issues (spacing, indentation) that can be auto-fixed:

```bash
cd /path/to/moodle
phpcbf --standard=moodle question/type/essaysimilarity
```

### Database Permissions

Ensure your test database user has sufficient permissions:
```sql
GRANT ALL PRIVILEGES ON moodletest.* TO 'moodleuser'@'localhost';
```

## Before Committing

Always run these checks before committing:

1. **PHP Lint**: `./test_plugin.sh phplint`
2. **Validation**: `./test_plugin.sh validate`
3. **PHPUnit**: `./test_plugin.sh phpunit` (after init)
4. **Code Review**: Review your changes for coding standards

## Environment Variables

You can customize paths using environment variables:

```bash
export MOODLE_DIR=/path/to/your/moodle
export CI_DIR=/path/to/plugin-ci

./test_plugin.sh all
```

## Getting Help

- **Moodle Plugin CI**: https://moodlehq.github.io/moodle-plugin-ci/
- **PHPUnit for Moodle**: https://docs.moodle.org/dev/PHPUnit
- **Moodle Coding Style**: https://docs.moodle.org/dev/Coding_style

## Contributing

When contributing to this plugin:

1. Create a feature branch
2. Make your changes
3. Run all tests locally
4. Ensure tests pass
5. Submit a pull request
6. CI will automatically run all tests

The GitHub Actions CI will provide feedback on your pull request.
