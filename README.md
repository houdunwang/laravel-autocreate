# 自动构建
根据模型自动创建controller、views、Request。

下面是根据 Article 模块的 Category 模型生成业务框架，系统同时会创建模型表单处理器。

```
php artisan hd:autocreate Modules/Article/Entities/Category.php 文章
```

执行以下命令会创建下列文件

1. 创建控制器 Http/Controllers/CategoryController
2. 表单验证请求 Http/Request/CategoryRequest
3. 添加路由规则 routes.php
4. 生成模版视图

**参数说明**

| 参数   | 说明         |
| ------ | ------------ |
| 参数一 | 模型文件     |
| 参数二 | 模型中文描述 |

