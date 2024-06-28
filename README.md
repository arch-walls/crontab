#定时任务管理

####安装
```php
    // 支持 Tp5.0、Tp5.1、Tp6+
    composer require arches/crontab
```

```PHP
    // Tp5.0
    application/config.php
    // default: php
    + 'app_path' => 'PHP executable file'

    // Tp5.1、Tp6+
    config/app.php
    // default: php
    + 'app_path' => 'PHP executable file'

    // 2 继承 1
    1: Arches\Crontab\Command\BaseCommand::class
    2: Arches\Crontab\Command\Command::class
    // 2支持属性设置Option参数[-开头的参数]

    // 添加测试命令
    // app/command/TestCommand
    // TestCommand extends Arches\Crontab\Command\BaseCommand
    // TestCommand extends Arches\Crontab\Command\Command

    // defatult crontab
    php think crontab
    // 从crontab表读取可执行状态的任务执行。
```
