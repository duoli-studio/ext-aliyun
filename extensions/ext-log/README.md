# Lemon Framework For Develop
sour-lemon framework develop for laravel 5 log viewer

## 安装

使用以下命令安装
```
composer require duoli/l5-log
```

更新 composer 后, 添加 Service Provider 到 `config/app.php`
```
Poppy\Extension\Log\L5LogServiceProvider::class
```

## 配置路由

```
\Route::get('l5_log', '\Poppy\Extension\Log\Http\L5LogController@index')
    ->name('duoli.l5_log');
```