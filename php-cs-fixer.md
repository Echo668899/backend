# 代码格式化,有需要可以使用

## 安装 php-cs-fixer

```bash
composer require friendsofphp/php-cs-fixer --dev
```

## composer.json

```json
{
    "scripts": {
        "fix": "php-cs-fixer fix --config=.php-cs-fixer.dist.php"
    },
    "scripts-descriptions": {
        "fix": "格式化代码样式"
    }
}
```

## 修改配置文件

```txt
.php-cs-fixer.dist.php
```

## 执行

```bash
composer fix
```
