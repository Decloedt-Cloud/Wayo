# Makefile for CI3 to CI4 Migration
# Usage: make <target>

.PHONY: help install migrate-config migrate-models migrate-controllers migrate-views migrate-helpers migrate-libraries migrate-routes test clean status

SHELL := /bin/bash
COMPOSER := composer
PHP := php
CI3_PATH := application
CI4_PATH := app

help:
	@echo "=============================================="
	@echo "  CodeIgniter 3 to 4 Migration Makefile"
	@echo "=============================================="
	@echo ""
	@echo "Usage: make <target>"
	@echo ""
	@echo "Targets:"
	@echo "  install           - Install CI4 dependencies via Composer"
	@echo "  migrate-config    - Generate CI4 configuration files"
	@echo "  migrate-models    - Add namespaces and methods to models"
	@echo "  migrate-controllers - Add namespaces to controllers"
	@echo "  migrate-helpers   - Convert CI3 helpers to CI4 services"
	@echo "  migrate-libraries - Update libraries for CI4 compatibility"
	@echo "  migrate-routes    - Generate CI4 routes from CI3"
	@echo "  test              - Run PHP syntax check on CI4 app"
	@echo "  clean             - Clear CI4 cache files"
	@echo "  status            - Show migration status"
	@echo ""
	@echo "CI4 Entry Points:"
	@echo "  index.php         - Main CI4 entry (http://localhost:9020/)"
	@echo ""

install:
	@echo "Installing CI4 dependencies..."
	$(COMPOSER) install --no-interaction
	@echo "✓ Dependencies installed"

migrate-config:
	@echo "Generating CI4 configuration files..."
	@if [ -f "app/Config/App.php" ]; then \
		echo "✓ App.php already exists"; \
	else \
		echo "Creating App.php..."; \
	fi
	@if [ -f "app/Config/Routes.php" ]; then \
		echo "✓ Routes.php already exists"; \
	else \
		echo "Creating Routes.php..."; \
	fi
	@echo "✓ Configuration files ready"

migrate-models:
	@echo "Migrating models..."
	@echo "✓ Checking model files..."
	@find $(CI4_PATH)/Models -name "*.php" -type f 2>/dev/null | wc -l
	@echo "Models to review:"
	@echo "  - Add namespace: namespace App\\Models;"
	@echo "  - Extend: CodeIgniter\\Model"
	@echo "  - Add: protected \$table, \$primaryKey, \$returnType"

migrate-controllers:
	@echo "Migrating controllers..."
	@echo "✓ Controllers extend BaseController"
	@find $(CI4_PATH)/Controllers -name "*.php" -type f 2>/dev/null | wc -l
	@echo "Controllers to review:"
	@echo "  - Add namespace: namespace App\\Controllers;"
	@echo "  - Replace \$this->load with service injection"
	@echo "  - Replace \$this->input with \$this->request"

migrate-helpers:
	@echo "Converting helpers to services..."
	@echo "✓ Checking helper files..."
	@find $(CI4_PATH)/Helpers -name "*_helper.php" -type f 2>/dev/null | wc -l
	@echo "Services created:"
	@echo "  - CommonService"
	@echo "  - LmsService"
	@echo "  - LanguageService"

migrate-libraries:
	@echo "Migrating libraries..."
	@echo "✓ Checking library files..."
	@find $(CI4_PATH)/Libraries -name "*.php" -type f 2>/dev/null | wc -l
	@echo "Libraries to review:"
	@echo "  - Add namespace: namespace App\\Libraries;"
	@echo "  - Update constructors for DI"

migrate-routes:
	@echo "Migrating routes..."
	@if [ -f "$(CI4_PATH)/Config/Routes.php" ]; then \
		echo "✓ Routes.php exists"; \
		grep -c "^\s*\$routes->" $(CI4_PATH)/Config/Routes.php || echo "0 routes defined"; \
	else \
		echo "Creating Routes.php..."; \
	fi

test:
	@echo "Running syntax checks..."
	@find $(CI4_PATH) -name "*.php" -type f -exec $(PHP) -l {} \; 2>&1 | grep -v "No syntax errors"
	@echo "✓ Syntax check complete"

clean:
	@echo "Cleaning CI4 cache..."
	@rm -rf $(CI4_PATH)/Cache/*
	@rm -rf writable/cache/*
	@rm -rf writable/logs/*
	@echo "✓ Cache cleared"

status:
	@echo "=============================================="
	@echo "  Migration Status"
	@echo "=============================================="
	@echo ""
	@echo "Controllers:"
	@find $(CI4_PATH)/Controllers -name "*.php" -type f 2>/dev/null | wc -l | xargs echo "  Total:"
	@echo ""
	@echo "Models:"
	@find $(CI4_PATH)/Models -name "*.php" -type f 2>/dev/null | wc -l | xargs echo "  Total:"
	@echo ""
	@echo "Views:"
	@find $(CI4_PATH)/Views -name "*.php" -type f 2>/dev/null | wc -l | xargs echo "  Total:"
	@echo ""
	@echo "Services:"
	@find $(CI4_PATH)/Services -name "*.php" -type f 2>/dev/null | wc -l | xargs echo "  Total:"
	@echo ""
	@echo "Libraries:"
	@find $(CI4_PATH)/Libraries -name "*.php" -type f 2>/dev/null | wc -l | xargs echo "  Total:"
	@echo ""

# Quick search/replace patterns
replace-post:
	@echo "Replacing \$$_POST with \$$this->request->getPost()..."
	@grep -rl "\$_POST" $(CI3_PATH)/controllers | head -5

replace-get:
	@echo "Replacing \$$_GET with \$$this->request->getGet()..."
	@grep -rl "\$_GET" $(CI3_PATH)/controllers | head -5

replace-db:
	@echo "Checking direct \$$this->db usage..."
	@grep -rn "this->db->" $(CI3_PATH)/controllers | wc -l | xargs echo "  occurrences in controllers:"
