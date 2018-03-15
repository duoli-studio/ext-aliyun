## 安装依赖包

```
composer update -vvv
```

## 配置 .env 文件

```
cp .env.example .env
```

## 清理不需要的 module

modules 中有哪些是需要的, 保留, 不需要的删掉. system 不需要删除.是核心模块

## 整合数据库

```
php artisan migrate
```

## 运行

```
php artisan serve
```