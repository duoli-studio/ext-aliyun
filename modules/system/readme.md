key 命名规范

```
namespace::group.item
```

poppy::module_system.enabled


```$xslt
/* chrome-extension://pkgccpejnmalmdinmhkkfafefagiiiad/template/fehelper_jsonformat.html */
{
    "name": "后台管理",
    "identification": "system",
    "description": "Notadd 后台管理模块",
    "authors": [
        {
            "name": "Notadd",
            "email": "notadd@ibenchu.com"
        }
    ],
    "version": "2.0.0",
    "csrf": [
        "admin*",
        "api*",
        "editor*"
    ],
    "settings": {
        "attachment.manager.image": {
            "type": "string",
            "default": [
                ".png",
                ".jpg",
                ".jpeg",
                ".gif",
                ".bmp"
            ]
        },
       ...
        "attachment.limit.video": {
            "type": "integer",
            "default": 2048
        }
    },
    "permissions": [
        {
            "description": "全局权限",
            "identification": "global",
            "groups": [
                {
                    "description": "插件权限定义",
                    "identification": "addon",
                    "name": "插件权限",
                    "permissions": [
                        {
                            "default": false,
                            "description": "全局插件管理权限",
                            "identification": "manage"
                        }
                    ]
                },
                {
                    "description": "邮件权限定义",
                    "identification": "mail",
                    "name": "邮件权限",
                    "permissions": [
                        {
                            "default": false,
                            "description": "全局邮件配置管理权限",
                            "identification": "manage"
                        }
                    ]
                },
                {
                    "description": "模块权限定义",
                    "identification": "module",
                    "name": "模块权限",
                    "permissions": [
                        {
                            "default": false,
                            "description": "全局模块管理权限",
                            "identification": "manage"
                        }
                    ]
                },
                {
                    "description": "导航权限定义",
                    "identification": "navigation",
                    "name": "导航权限",
                    "permissions": [
                        {
                            "default": false,
                            "description": "全局导航分组管理权限",
                            "identification": "group"
                        },
                        {
                            "default": false,
                            "description": "全局导航项管理权限",
                            "identification": "item"
                        }
                    ]
                },
                {
                    "description": "搜索引擎权限定义",
                    "identification": "search-engine",
                    "name": "搜索引擎权限",
                    "permissions": [
                        {
                            "default": false,
                            "description": "获取全局 SEO 配置项",
                            "identification": "get"
                        },
                        {
                            "default": false,
                            "description": "设置全局 SEO 配置项",
                            "identification": "set"
                        }
                    ]
                },
                {
                    "description": "全局权限定义",
                    "identification": "global",
                    "name": "全局权限",
                    "permissions": [
                        {
                            "default": false,
                            "description": "获取全局配置项",
                            "identification": "get"
                        },
                        {
                            "default": false,
                            "description": "设置全局配置项",
                            "identification": "set"
                        }
                    ]
                }
            ],
            "name": "全局"
        }
    ],
    "dashboards": [
        {
            "identification": "systeminfo",
            "title": "系统信息",
            "template": "Notadd\\Administration\\SystemInformation@handler"
        },
        {
            "identification": "development",
            "title": "开发团队",
            "template": [
                {
                    "tag": "team",
                    "props": {
                        "auxiliaryList": [
                            {
                                "name": "半缕阳光",
                                "url": "https://github.com/ganlanshu0211"
                            }
                        ],
                        "list": [
                            {
                                "image": "https://ww4.sinaimg.cn/large/0060lm7Tly1flat9vypmpj302o02odg7.jpg",
                                "name": "寻风",
                                "url": "https://github.com/twilroad"
                            }
                        ]
                    }
                }
            ]
        }
    ],
    "pages": {
        "configurations": {
            "initialization": {
                "name": "参数配置",
                "path": "configurations",
                "tabs": true,
                "target": "global"
            },
            "tabs": {
                "configuration": {
                    "default": true,
                    "show": true,
                    "submit": "api/setting/set",
                    "title": "全局设置",
                    "fields": {
                        "name": {
                            "default": "",
                            "description": "",
                            "label": "网站名称",
                            "key": "site.name",
                            "placeholder": "请输入网站名称",
                            "required": true,
                            "type": "input",
                            "validates": [
                                {
                                    "message": "请输入网站名称",
                                    "required": true,
                                    "trigger": "change",
                                    "type": "string"
                                }
                            ]
                        },
                        "enabled": {
                            "default": false,
                            "description": "关闭后网站将不能访问",
                            "format": "boolean",
                            "label": "站点开启",
                            "key": "site.enabled",
                            "required": false,
                            "type": "switch"
                        },
                        "domain": {
                            "default": "",
                            "description": "不带 http:// 或 https://",
                            "label": "网站域名",
                            "key": "site.domain",
                            "required": false,
                            "type": "input"
                        },
                        "multidomain": {
                            "default": false,
                            "description": "由于前后端分离机制，官方不对多域名做特殊支持，可能导致其他未知问题",
                            "format": "boolean",
                            "label": "开启多域名",
                            "key": "site.multidomain",
                            "required": false,
                            "type": "switch"
                        },
                        "beian": {
                            "default": "",
                            "label": "备案信息",
                            "key": "site.beian",
                            "required": false,
                            "type": "input"
                        },
                        "company": {
                            "default": "",
                            "label": "公司名称",
                            "key": "site.company",
                            "required": false,
                            "type": "input"
                        },
                        "copyright": {
                            "default": "",
                            "label": "版权信息",
                            "key": "site.copyright",
                            "required": false,
                            "type": "input"
                        },
                        "statistics": {
                            "default": "",
                            "label": "统计代码",
                            "key": "site.statistics",
                            "required": false,
                            "type": "textarea"
                        }
                    }
                }
            }
        }
    },
    "menus": {
        "global": {
            "icon": "settings",
            "permission": null,
            "path": "/",
            "text": "全局",
            "children": [
                {
                    "icon": "ios-cog",
                    "injection": "global",
                    "text": "全局设置"
                },
                {
                    "children": [
                        {
                            "path": "/upload",
                            "text": "上传设置"
                        }
                    ],
                    "icon": "ios-paper",
                    "text": "附件设置"
                },
                {
                    "children": [
                        {
                            "path": "/extension",
                            "text": "拓展配置"
                        },
                        {
                            "path": "/module",
                            "text": "模块配置"
                        },
                        {
                            "path": "/addon",
                            "text": "插件配置"
                        }
                    ],
                    "icon": "cube",
                    "text": "应用管理"
                },
                {
                    "icon": "shuffle",
                    "injection": "addon",
                    "text": "全局插件"
                },
                {
                    "children": [
                        {
                            "path": "/menu",
                            "text": "菜单管理"
                        },
                        {
                            "path": "/seo",
                            "text": "SEO 管理"
                        },
                        {
                            "path": "/mail",
                            "text": "邮件设置"
                        },
                        {
                            "path": "/debug",
                            "text": "调试工具"
                        }
                    ],
                    "icon": "stats-bars",
                    "text": "系统插件"
                }
            ]
        }
    }
}
```