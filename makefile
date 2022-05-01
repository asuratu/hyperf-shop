php := $(shell which php)
composer := $(shell which composer)
folders = storage
step ?= 1

.PHONY: all clean publish cache link migrate rollback patch model ide
.IGNORE: all

all:
	@$(composer) update
	@$(MAKE) publish
	@$(MAKE) link
	@$(MAKE) ide
	@$(php) artisan horizon:terminate

clean:
	@$(php) artisan config:clear
	@$(php) artisan cache:clear
	@$(php) artisan telescope:prune
	@$(php) artisan horizon:terminate
	@$(php) artisan view:clear

publish:
	@$(php) artisan horizon:publish
	@$(php) artisan telescope:publish
	@$(php) artisan vendor:publish --tag=laravel-admin-assets --force
	@$(php) artisan vendor:publish --tag=laravel-admin-lang --force

cache:
	@$(php) artisan config:cache
	@$(php) artisan route:cache

link:
	@$(php) artisan storage:link

migrate:
	@$(php) artisan migrate

rollback:
	$(php) artisan migrate:rollback --step=$(s)

patch:
	$(php) artisan make:migration alter_$(t)_table_v$(v) --table=$(t)

model:
	$(php) artisan make:model Api/$(m) -m

ide:
	@$(php) artisan clear-compiled
	@$(php) artisan ide-helper:eloquent
	@$(php) artisan ide-helper:generate
	@$(php) artisan ide-helper:meta
	@$(php) artisan ide-helper:models -W -R
