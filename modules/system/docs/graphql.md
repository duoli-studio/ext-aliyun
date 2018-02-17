### Graphql 常用

#### 后台

角色查询
```

query be_roles ($pagination:InputPagination!){
  be_roles(pagination:$pagination){
    list{
      id,
      title,
      type,
      description
    },
    pagination{
      pages,
      total,
      size
    }
  }
}

# pagination
{
  "pagination": {
    "page": 1
  }
}
```

角色详细
```
query be_role{
  be_role(id:4){
    id,
    title,
    description,
    type
  }
}
```

角色创建
```
mutation be_role($role:InputBeRole!){
  be_role_2e(item:$role){
    status,
    message
  }
}

# 变量
{
  "role": {
    "name": "abc222",
    "title": "TitleAbc",
    "guard": "develop",
    "description": "描述"
  }
}
```
角色编辑
```
mutation be_role($role:InputBeRole!){
  be_role_2e(item:$role, id:4){
    status,
    message
  }
}

# variable
{
  "role": {
    "title": "用户2",
    "description": "用户设置"
  }
}
```
角色操作
```
mutation be_role($id:Int!, $action:BeRoleDoAction!){
  be_role_do(action:$action, id:$id){
    status,
    message
  }
}

# variable
{
  "action": "delete",
  "id": 5
}
```
#### 系统
系统项修改
```

mutation be_role($key:String!, $value:String!){
  be_setting(key:$key, value:$value){
    status,
    message
  }
}

# variable
{
  "key": "system::tezt.delete",
  "value": "6"
}
```
#### 用户查询
用户列表查询
```
query be_accounts($filters:BeAccountFilter, $pagination:InputPagination!){
  be_accounts(filters:$filters, pagination:$pagination){
    list{
      id,
      username
    },
    pagination{
      total,
      page,
      pages,
      size
    }
  }
}

# variable
{
  "pagination": {
    "page": 1
  }
}
```
用户详细查询
```

query be_account($id:Int!){
  be_account(id:$id){
    id,
    username,
    mobile,
    email,
    type,
    is_enable,
    disable_reason
  }
}
# variable
{
  "id": 122
}
```
#### 后台区服
区服创建
```
mutation be_server_create($item:InputBeServer!){
  be_server_2e(item:$item){
    status,
    message
  }
}
# variable
{
  "item": {
    "title": "添加区服11",
    "parent_id": 32
  }
}
```
区服修改
```
mutation be_server_edit($item:InputBeServer!, $id:Int!){
  be_server_2e(item:$item, id:$id){
    status,
    message
  }
}

# variable
{
  "item": {
    "title": "教育网专区2",
    "parent_id": 32
  },
  "id": 31
}}
```
区服删除
```
mutation be_server_delete($action:BeServerDoAction!, $id:Int!){
  be_server_do(action:$action, id:$id){
    status,
    message
  }
}

# variable
{
  "item": {
    "title": "教育网专区2",
    "parent_id": 32
  },
  "id": 31
}
```
### 前台
#### 用户操作
用户资料编辑
```
mutation modify_profile($action:ProfileAction!, $value:String!){
  modify_profile(type:$action, value:$value){
    status,
    message
  }
}

# variable
{
  "action": "nickname",
  "value": "woleteca"
}
```