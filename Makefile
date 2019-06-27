COMPOSER_BIN        := composer
SWAGGER_FILE        := public/swagger/openapi.yaml

.PHONY: all build vendor tests deps_update

all: build vendor

list:
	@echo ""
	@echo "Useful targets:"
	@echo ""
	@echo "  build         > Build Services"
	@echo "  rebuild         > Rebuild Services"
	@echo "  start         > Start Service"
	@echo "  stop         > Stop Containers"
	@echo "  destroy         > Destroy Containers"
	@echo "  tests         > run linter and tests (default target)"
	@echo "  deps_update  > explicitly update dependencies (composer update)"
	@echo "  coverage        > install composer dependencies"

build:
	@echo ">>> Build Services ......"
	vagrant up --provision

rebuild: stop destroy build
	@echo ">>> Rebuild Services ......"

start:
	@echo ">>> Start Service ......"
	vagrant up

stop:
	@echo ">>> Stop Containers ......"
	vagrant halt

destroy:
	@echo ">>> Destroy Containers ......"
	vagrant destroy -f

tests:
	php vendor/bin/phpcs
	php vendor/bin/codecept run

coverage:
	php vendor/bin/codecept run --coverage-html

deps_update:
	vagrant ssh -c "cd /var/www/htdocs/jobs/slim && composer update"

swagger:
	vendor/bin/openapi app/ --output ${SWAGGER_FILE}
