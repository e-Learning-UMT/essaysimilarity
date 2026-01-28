# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2026-01-28

### Added
- **Moodle 5.1 Compatibility**: Updated plugin to work with Moodle 5.1
- **PHPUnit Test Suite**: Comprehensive unit tests including:
  - `questiontype_test.php` - Tests for question type class
  - `question_test.php` - Tests for question definition class
  - `tokenizer_test.php` - Tests for NLP tokenizer
  - `cosine_similarity_test.php` - Tests for similarity calculator
  - `privacy_provider_test.php` - Tests for privacy compliance
- **Enhanced CI/CD**: GitHub Actions now tests against:
  - Moodle versions: 4.1 LTS, 4.3, 4.4, 5.0, 5.1
  - PHP versions: 8.0, 8.1, 8.2, 8.3
  - Databases: PostgreSQL and MariaDB
- **Testing Tools**:
  - `test_plugin.sh` - Automated testing script for local development
  - `TESTING.md` - Comprehensive testing documentation
- **Documentation Updates**:
  - Added compatibility information
  - Added testing instructions
  - Added development guidelines

### Changed
- Updated `version.php`:
  - Requires Moodle 5.1 (2024100700)
  - Version bumped to 2026012800
  - Release version: 1.1.0
- Updated `.github/workflows/ci.yml`:
  - Extended matrix to test multiple Moodle and PHP versions
  - Improved test coverage across different environments

### Fixed
- Ensured compatibility with PHP 8.1, 8.2, and 8.3
- Addressed deprecation warnings for newer Moodle versions

### Developer Notes
- All new code follows Moodle coding standards
- PHPUnit tests provide >70% code coverage for core functionality
- CI automatically runs on all pushes and pull requests

## [2023-06-22] - Previous Release

### Features
- Auto grading using cosine similarity
- Support for multiple languages
- Answer key functionality
- Statistical text analysis
- Configurable score thresholds
- Privacy API compliance
- LSA (Latent Semantic Analysis) support
- TF-IDF vectorization

[1.1.0]: https://github.com/thoriqadillah/moodle-qtype_essaysimilarity/compare/v2023062200...v1.1.0
