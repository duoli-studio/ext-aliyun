## 支持 bower 文档解析

### 简介
Bower 是 twitter 推出的一款包管理工具，基于nodejs的模块化思想，把功能分散到各个模块中，让模块和模块之间存在联系，通过 Bower 来管理模块间的这种联系。

### 命令

```
php artisan system:bower
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
php artisan system:doc api
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

### 定义项目


```
'animate.css' => [
    'css' => [
        'animate.css' => 'animate/animate.css',
    ],
    'key' => 'animate.css',
],

'ace-builds' => [
    'js'   => [
        'main'    => 'src/ace.js',
        'aim'     => 'ace/{VERSION}/ace.js',
        'dispose' => [
            'src/*' => 'ace/{VERSION}/',
        ],
        'config'  => [
            // 和 key 相同, 覆盖掉 key 的定义位置
            '__same' => '',
        ],
    ],
    'key'  => 'ace',
    'shim' => [
        'exports' => 'ace',
    ],
],

'bootstrap' => [
    'js'   => [
        'main' => 'dist/js/bootstrap.js',
        'aim'  => 'bt3/{VERSION}/bootstrap.js',
    ],
    'css'  => [
        'dist/css/bootstrap.css'       => 'bt3/{VERSION}/css/bootstrap.css',
        'dist/css/bootstrap-theme.css' => 'bt3/{VERSION}/css/bootstrap-theme.css',
        'dist/fonts/*'                 => 'bt3/{VERSION}/fonts/',
    ],
    'shim' => ['jquery'],
    'key'  => 'bt3',
],

'bootstrap-hover-dropdown' => [
    'js'   => [
        'aim' => 'bt3/hover-dropdown/{VERSION}/bt3.hover-dropdown.js',
    ],
    'shim' => ['jquery', 'bt3'],
    'key'  => 'bt3.hover-dropdown',
],
'bootstrap-validator'      => [
    'js'   => [
        'aim' => 'bt3/validator/{VERSION}/bt3.validator.js',
    ],
    'shim' => ['jquery', 'bt3'],
    'key'  => 'bt3.validator',
],
'centrifuge'               => [
    'js' => [
        'aim' => 'centrifuge/{VERSION}/centrifuge.js',
    ],
],
'clipboard'                => [
    'js' => [
        'aim' => 'clipboard/{VERSION}/clipboard.min.js',
    ],
],

'datatables.net' => [
    'js'   => [
        'main' => 'js/jquery.dataTables.js',
        'aim'  => 'jquery/data-tables/{VERSION}/jquery.data-tables.js',
    ],
    'shim' => ['jquery'],
],

'datatables.net-bs' => [
    'js'   => [
        'main' => 'js/dataTables.bootstrap.js',
        'aim'  => 'bt3/data-tables/{VERSION}/bt3.data-tables.js',
    ],
    'css'  => [
        'css/dataTables.bootstrap.css' => 'bt3/data-tables/data-tables.bootstrap.css',
    ],
    'key'  => 'bt3.data-tables',
    'shim' => ['jquery', 'bt3'],
],

'fex-webuploader' => [
    'js'   => [
        'aim' => 'jquery/webuploader/{VERSION}/jquery.webuploader.js',
    ],
    'css'  => [
        'dist/*.css' => 'jquery/webuploader/',
    ],
    'key'  => 'jquery.webuploader',
    'shim' => ['jquery'],
],

'jquery' => [
    'js' => [
        'main' => 'jquery.min.js',
        'aim'  => 'jquery/{VERSION}/jquery.min.js',
    ],
],

'layer' => [
    'js'   => [
        'main' => 'src/layer.js',
        'aim'  => 'jquery/layer/{VERSION}/jquery.layer.js',
    ],
    'css'  => [
        'src/theme/*' => 'jquery/layer/',
    ],
    'key'  => 'jquery.layer',
    'shim' => ['jquery'],
],

'image-picker' => [
    'js'   => [
        'aim' => 'jquery/image-picker/{VERSION}/jquery.image-picker.js',
    ],
    'css'  => [
        'image-picker/image-picker.css' => 'jquery/image-picker/image-picker.css',
    ],
    'key'  => 'jquery.image-picker',
    'shim' => ['jquery'],
],

'sockjs' => [
    'js' => [
        'aim' => 'sockjs/{VERSION}/sockjs.js',
    ],
],

'toastr' => [
    'js'   => [
        'main' => 'toastr.min.js',
        'aim'  => 'jquery/toastr/{VERSION}/jquery.toastr.js',
    ],
    'css'  => [
        'toastr.css' => 'jquery/toastr/toastr.css',
    ],
    'key'  => 'jquery.toastr',
    'shim' => ['jquery'],
],

'tokenize2' => [
    'js'   => [
        'main' => 'dist/tokenize2.min.js',
        'aim'  => 'jquery/tokenize2/{VERSION}/jquery.tokenize2.js',
    ],
    'css'  => [
        'dist/tokenize2.min.css' => 'jquery/tokenize2/tokenize2.min.css',
    ],
    'key'  => 'jquery.tokenize2',
    'shim' => ['jquery'],
],

'jquery-form' => [
    'js'   => [
        'aim' => 'jquery/form/{VERSION}/jquery.form.js',
    ],
    'key'  => 'jquery.form',
    'shim' => ['jquery'],
],

'jquery-validation' => [
    'js'   => [
        'aim' => 'jquery/validation/{VERSION}/jquery.validation.js',
    ],
    'key'  => 'jquery.validation',
    'shim' => ['jquery'],
],

'jquery-slimscroll' => [
    'js'   => [
        'main' => 'jquery.slimscroll.min.js',
        'aim'  => 'jquery/slimscroll/{VERSION}/jquery.slimscroll.min.js',
    ],
    'key'  => 'jquery.slimscroll',
    'shim' => ['jquery'],
],

'js-cookie' => [
    'js'   => [
        'main' => 'src/js.cookie.js',
        'aim'  => 'js-cookie/{VERSION}/js-cookie.js',
    ],
    'shim' => [
        'exports' => 'Cookies',
    ],
],

'json' => [
    'js'   => [
        'aim' => 'json/json2.js',
    ],
    'shim' => [
        'exports' => 'JSON',
    ],
],

'smooth-scroll' => [
    'js'  => [
        'main' => 'smooth-scroll.js',
        'aim'  => 'smooth-scroll/{VERSION}/smooth-scroll.js',
    ],
    'key' => 'smooth-scroll',
],

'vkBeautify' => [
    'js'   => [
        'main' => 'vkbeautify.js',
        'aim'  => 'vkbeautify/vkbeautify.js',
    ],
    'key'  => 'vkbeautify',
    'shim' => [
        'exports' => 'vkbeautify',
    ],
],

'metisMenu' => [
    'js'  => [
        'main' => 'dist/metisMenu.js',
        'aim'  => 'jquery/metis-menu/{VERSION}/jquery.metis-menu.js',
    ],
    'css' => [
        'dist/*.css' => 'jquery/metis-menu/',
    ],
    'key' => 'jquery.metis-menu',
],

'requirejs' => [
    'js'  => [
        'main' => 'require.js',
        'aim'  => 'requirejs/require.js',
    ],
    'key' => 'requirejs',
],

'PACE' => [
    'js'  => [
        'main' => 'pace.min.js',
        'aim'  => 'pace/{VERSION}/pace.min.js',
    ],
    'css' => [
        'themes/*' => 'pace/',
    ],
    'key' => 'pace',
],

'underscore' => [
    'js' => [
        'main' => 'underscore-min.js',
        'aim'  => 'underscore/{VERSION}/underscore-min.js',
    ],
],
'vue'        => [
    'js' => [
        'main' => 'dist/vue.min.js',
        'aim'  => 'vue/{VERSION}/vue.min.js',
    ],
],
```