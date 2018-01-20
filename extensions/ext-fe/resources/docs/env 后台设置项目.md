### Env 后台设置项目

设置运行环境
```
APP_ENV=local
APP_DEBUG=true
```

生成 APP_KEY
```
$ php artisan key:generate
APP_KEY=base64:J3scto3x2wZNnJlqeKcZSBLUuUcSylIj1t+cJCUEbWY=
```
设置运行网址
```
URL_SITE=http://local_play_domain.com
```
设置数据库
```
DB_HOST=192.168.1.21
DB_DATABASE=play_v2
DB_USERNAME=root
DB_PASSWORD=Markzhao123456#
```
设置 JWT key
```
$ php artisan jwt:secret
JWT_SECRET=XgVv3f3yLEBHmkkoipLil22oxaD1ZWaB
```
设置缓存
```
# 缓存驱动
CACHE_DRIVER=redis
# 缓存前缀
CACHE_PREFIX=play_v1
```
设置 Session驱动方式
```
SESSION_DRIVER=redis
```
设置队列驱动
```
# 本地使用 同步驱动, 线上使用redis , 默认为 sync
QUEUE_DRIVER=sync
```
