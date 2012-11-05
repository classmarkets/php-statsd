CODE_COVERAGE_REPORT_DIR := ./reports/coverage
CODE_COVERAGE_REPORT_URL := file://$(shell pwd)/$(CODE_COVERAGE_REPORT_DIR)/index.html

.PHONY: test

test: vendor/autoload.php
	bin/phpunit --coverage-html "$(CODE_COVERAGE_REPORT_DIR)" --exclude-group skip
	@echo To view it point your browser to $(CODE_COVERAGE_REPORT_URL)

vendor/autoload.php: composer.phar
	./composer.phar install --dev

composer.phar:
	curl -s http://getcomposer.org/installer | php && touch composer.json
