## 登录注册
登录 / 注册逻辑

![](http://7xkiq1.com1.z0.glb.clouddn.com/18-1-31/7967268.jpg)
登录获取token

![](http://7xkiq1.com1.z0.glb.clouddn.com/18-1-31/9567256.jpg)

图片
## 用户类型
根据使用分为三种用户类型
```
user
develop
backend
```
根据使用到的用户的类型我们应当分为这几项
```
用户 - api (jwt) 驱动
用户 - web 驱动
开发 - web 驱动
后端 - web 驱动
后端 - api (jwt) 驱动
```

每一种用户定义的角色分为三种
```
root     : 后台超级管理员
user     : 前台普通用户
develop  : 开发者
```
### 密码加密方式
```
$password     : 原始密码
$reg_datetime : 注册时间(datetime) 类型
$randomKey    : 六位随机值
md5(sha1($password . $reg_datetime) . $randomKey);
```