#!/bin/bash
# Local testing script for moodle-qtype_essaysimilarity plugin
# This script helps you run various tests on the plugin before committing

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
MOODLE_DIR="${MOODLE_DIR:-/web/wwww/oceania_50/public}"
PLUGIN_DIR="$MOODLE_DIR/question/type/essaysimilarity"
CI_DIR="${CI_DIR:-/tmp/ci}"

echo "========================================="
echo "Moodle Plugin Testing Script"
echo "========================================="
echo "Moodle Directory: $MOODLE_DIR"
echo "Plugin Directory: $PLUGIN_DIR"
echo "========================================="
echo ""

# Function to check if moodle-plugin-ci is installed
check_ci_tool() {
    if [ ! -d "$CI_DIR" ]; then
        echo -e "${YELLOW}Installing moodle-plugin-ci...${NC}"
        cd /tmp
        composer create-project -n --no-dev --prefer-dist moodlehq/moodle-plugin-ci ci ^4
        echo -e "${GREEN}✓ moodle-plugin-ci installed${NC}"
    else
        echo -e "${GREEN}✓ moodle-plugin-ci already installed${NC}"
    fi
}

# Function to run PHP lint
run_phplint() {
    echo -e "\n${YELLOW}Running PHP Lint...${NC}"
    cd "$MOODLE_DIR"
    if "$CI_DIR/bin/moodle-plugin-ci" phplint question/type/essaysimilarity 2>&1 | grep -q "No syntax error"; then
        echo -e "${GREEN}✓ PHP Lint passed${NC}"
        return 0
    else
        echo -e "${RED}✗ PHP Lint failed${NC}"
        return 1
    fi
}

# Function to validate plugin
run_validate() {
    echo -e "\n${YELLOW}Running Plugin Validation...${NC}"
    cd "$MOODLE_DIR"
    if "$CI_DIR/bin/moodle-plugin-ci" validate question/type/essaysimilarity 2>&1 | grep -q "Found required file"; then
        echo -e "${GREEN}✓ Plugin validation passed${NC}"
        return 0
    else
        echo -e "${RED}✗ Plugin validation failed${NC}"
        return 1
    fi
}

# Function to run codechecker
run_codechecker() {
    echo -e "\n${YELLOW}Running Moodle Code Checker (phpcs)...${NC}"
    echo -e "${YELLOW}Note: This may show style warnings that can be fixed later${NC}"
    cd "$MOODLE_DIR"
    "$CI_DIR/bin/moodle-plugin-ci" phpcs --max-warnings 999 question/type/essaysimilarity 2>&1 | head -100
    echo -e "${YELLOW}⚠ Code checker completed (see output above)${NC}"
}

# Function to initialize PHPUnit
init_phpunit() {
    echo -e "\n${YELLOW}Initializing PHPUnit...${NC}"
    cd "$MOODLE_DIR"
    if timeout 120 php admin/tool/phpunit/cli/init.php 2>&1 | tail -10; then
        echo -e "${GREEN}✓ PHPUnit initialized${NC}"
        return 0
    else
        echo -e "${RED}✗ PHPUnit initialization failed or timed out${NC}"
        return 1
    fi
}

# Function to run PHPUnit tests
run_phpunit() {
    echo -e "\n${YELLOW}Running PHPUnit tests...${NC}"
    cd "$MOODLE_DIR"
    
    if [ ! -f "vendor/bin/phpunit" ]; then
        echo -e "${RED}✗ PHPUnit not found. Run init_phpunit first${NC}"
        return 1
    fi
    
    echo "Running tests for qtype_essaysimilarity..."
    if vendor/bin/phpunit --testsuite="qtype_essaysimilarity_testsuite" 2>&1; then
        echo -e "${GREEN}✓ PHPUnit tests passed${NC}"
        return 0
    else
        echo -e "${RED}✗ Some PHPUnit tests failed${NC}"
        return 1
    fi
}

# Function to run specific test
run_specific_test() {
    echo -e "\n${YELLOW}Running specific test: $1${NC}"
    cd "$MOODLE_DIR"
    
    if [ ! -f "vendor/bin/phpunit" ]; then
        echo -e "${RED}✗ PHPUnit not found. Run init_phpunit first${NC}"
        return 1
    fi
    
    vendor/bin/phpunit "$PLUGIN_DIR/tests/$1"
}

# Function to show help
show_help() {
    cat <<EOF
Usage: $0 [command]

Commands:
    all             Run all tests (except phpunit)
    phplint         Run PHP syntax check
    validate        Run plugin validation
    codechecker     Run Moodle code style checker
    init-phpunit    Initialize PHPUnit environment
    phpunit         Run all PHPUnit tests
    test <file>     Run specific PHPUnit test file
    help            Show this help message

Examples:
    $0 all
    $0 phplint
    $0 phpunit
    $0 test questiontype_test.php

Environment Variables:
    MOODLE_DIR      Path to Moodle installation (default: /web/wwww/oceania_50/public)
    CI_DIR          Path to moodle-plugin-ci (default: /tmp/ci)

EOF
}

# Main script logic
case "${1:-all}" in
    all)
        check_ci_tool
        run_phplint
        run_validate
        run_codechecker
        echo -e "\n${GREEN}=========================================${NC}"
        echo -e "${GREEN}Basic tests completed!${NC}"
        echo -e "${YELLOW}Run '$0 init-phpunit' then '$0 phpunit' for unit tests${NC}"
        echo -e "${GREEN}=========================================${NC}"
        ;;
    phplint)
        check_ci_tool
        run_phplint
        ;;
    validate)
        check_ci_tool
        run_validate
        ;;
    codechecker)
        check_ci_tool
        run_codechecker
        ;;
    init-phpunit)
        init_phpunit
        ;;
    phpunit)
        run_phpunit
        ;;
    test)
        if [ -z "$2" ]; then
            echo -e "${RED}Error: Please specify a test file${NC}"
            echo "Usage: $0 test <filename>"
            exit 1
        fi
        run_specific_test "$2"
        ;;
    help|--help|-h)
        show_help
        ;;
    *)
        echo -e "${RED}Unknown command: $1${NC}"
        show_help
        exit 1
        ;;
esac
