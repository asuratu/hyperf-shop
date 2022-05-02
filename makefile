php := $(shell which php)
composer := $(shell which composer)
git := $(shell which git)
folders = storage
step ?= 1

.PHONY: all clean publish cache link migrate rollback patch model ide
.IGNORE: all

all:
	@$(composer) dump-autoload -o
	@$(MAKE) watch

watch:
	@$(php) bin/hyperf.php server:watch

# 生成指定模块的迁移文件，参数：（m）模块 （t）表名
migrate-gen:
	@$(php) bin/hyperf.php mine:migrate-gen --module=$(m) $(t)

# 执行指定模块的迁移表，（m）模块
migrate-run:
	@$(php) bin/hyperf.php mine:migrate-run $(m)

# 指定模块回滚迁移，（m）模块目录名，（s）回滚的步数
migrate-rollback:
	@$(php) bin/hyperf.php migrate:rollback --step=$(s) --path=app/$(m)/Database/Migrations

# 生成迁移补丁，（t）表名，（v）批次
migrate-patch:
	@$(php) bin/hyperf.php gen:migration alter_$(t)_table_v$(v) --table=$(t)

# 指定模块和表生成model，（t）表名，（m）模块目录
model-gen:
	@$(php) bin/hyperf.php mine:model-gen --module=$(m) --table=$(t)

# 生成模块数据表填充命令，（t）表名，（m）模块名
seeder-gen:
	@$(php) bin/hyperf.php mine:seeder-gen --module=$(m) $(t)

# 运行模块数据表填充命令，（t）表名，（m）模块名
seeder-run:
	@$(php) bin/hyperf.php mine:seeder-run $(m)

# 运行某一个填充命令，（s）迁移文件名，（m）模块目录
seeder:
	@$(php) bin/hyperf.php db:seed --path=app/$(m)/Database/Seeders/$(s).php

