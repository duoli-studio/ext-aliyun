## 支持 bower 文档解析

### 简介
Bower 是 twitter 推出的一款包管理工具，基于nodejs的模块化思想，把功能分散到各个模块中，让模块和模块之间存在联系，通过 Bower 来管理模块间的这种联系。

### 命令

```
php artisan ext:fe_bower
```

### 配置文件解析

```
/*
|--------------------------------------------------------------------------
| 配置 scss / js 文件夹位置
|--------------------------------------------------------------------------
*/
'folder'  => [
    'js_dir'   => 'resources/js',
    'font_dir' => 'resources/fonts',
    'scss_dir' => 'resources/scss',
],

/*
|--------------------------------------------------------------------------
| 配置 bower 文件的定义的
|--------------------------------------------------------------------------
*/
"ace-builds" => [
    // requirejs 引用 key 的定义
    'key'  => 'ace',

    // js 文件定义
    "js"   => [
        // 定义 main 的位置
        'main'    => 'src/ace.js',
        // 定义目标文件
        'aim'     => 'ace/{VERSION}/ace.js',
        // js dispose 文件内容
        'dispose' => [
            'src/*' => 'ace/{VERSION}/',
        ],
        'config'  => [
            // 和 key 相同, 覆盖掉 key 的定义位置
            '__same' => '',
        ],
    ],

    // shim 配置, 原封不动输出
    'shim' => [
        "exports" => "ace",
    ],
],

/*
|--------------------------------------------------------------------------
| 配置全局变量的导入
|--------------------------------------------------------------------------
*/
'global'  => [
    'url_site' => env('URL_SITE'),
    'url_js'   => env('URL_SITE') . '/assets/js',
],

/*
|--------------------------------------------------------------------------
| 引入 require js 文件定义引入文件夹 alias
|--------------------------------------------------------------------------
*/
'appends' => [
    'poppy'   => env('URL_SITE') . "/assets/js/poppy",
    'slt'     => env('URL_SITE') . "/modules/slt/js",
    'develop' => env('URL_SITE') . "/modules/develop/js",
],
```

## 生成 apidoc 文档 / 接口文档

apidoc 是一个简单的 RESTful API 文档生成工具，它从代码注释中提取特定格式的内容，生成文档。

```
php artisan ext:fe_doc api
```

配置信息
```
'apidoc' => [
    // key : 标识
    'backend' => [
        // 标题
        'title'       => '后台接口',
        // 源文件夹
        'origin'      => 'modules',
        // 过滤器
        'filter'      => [
            'system/src/request/api_v1/backend/.*\.php$',
        ],
        // 生成目录
        'doc'         => 'public/docs/backend',
        // 默认访问的url
        'default_url' => 'api_v1/backend/system/role/permissions',
    ],
],
```

## 生成 PHP 文档

sami 作为一款优秀的生成 PHP 文档的工具也已经纳入到这个项目中来, [Sami](https://github.com/FriendsOfPHP/Sami) , 这个工具生成的是 `modules` 文件夹下面的文档, 然后生成项目的技术文档.

```
php artisan ext:fe_doc php
```

## 生成项目文档

项目文档使用 : [Docsify](https://docsify.js.org/) 这个优秀的工具, 所有放到 `resources/docs` 文件夹下的目录都会作为项目文档生成.访问地址是
`(url_site)docs/poppy/`.

```
php artisan ext:fe_doc poppy
```