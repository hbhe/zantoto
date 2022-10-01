这是一个多商户的商城系统, 适合于程序员做二次开发：
1. 支持平台上架商品，也支持普通商户上架商品，即所谓的多商同户系统
2. 支持自定义店铺分类如: 酒店/购物/餐饮/休闲
3. 支持自定义商品分类如：服装/手机数码/家居百货/餐厨/美妆护肤/母婴玩具/蔬果生鲜
4. 支持自定义商品规格如：内存/款式/尺码/颜色
5. 完成的订单流程, 支持订单评价
6. 支持自定义商品品牌, 商品分类(三级), 商品SKU, 商品主图和详情图 
7. 商品先定义基础单价, 再根据这一商品的不同SKU定义价格增量，类似于Opencart的办法
8. 已提供大部分接口(RESTful)
9. 其它：广告栏管理, 完整的角色权限管理

不足：
1. 目前还没有开发商城的前端界面(H5端/Web), 有感兴趣愿意合作的前端童鞋请联系我(微信15527210477)，共同开发  
   
线上Demo后台管理地址:
- http://zantoto.beesoft.ink/backend/web
- 用户名: webmaster
- 密码: webmaster 
- 验证码: 随便输入

## 目录结构

```
common
    config/              放置公共配置文件
    mail/                
    models/              放置前台端共用的公共model文件
    tests/               测试单元    
console
    config/              放置CLI命令行配置文件
    controllers/         CLI脚本文件
    migrations/          数据库迁移命令文件
    models/              -
    runtime/             放置执行过程中产生的文件
backend
    assets/              放置后台静态文件 JavaScript and CSS
    config/              后台配置文件
    controllers/         后台controller文件
    models/              后台相关的model文件
    runtime/             后台脚本执行过程中产生的文件，包括log文件
    tests/               测试单元    
    views/               后台view文件
    web/                 web可访问路径, 脚本入口
frontend
    assets/              放置前台静态文件 JavaScript and CSS
    config/              前台配置文件
    controllers/         前台controller文件
    models/              前台相关的model文件
    runtime/             前台脚本执行过程中产生的文件，包括log文件
    tests/               测试单元 
    views/               前台view文件
    web/                 web可访问路径, 脚本入口
    widgets/             前台挂件
rest
    config/              API 接口配置文件，主要是路由配置！
    controllers/         接口controller文件
    models/              接口相关的model文件
    modules/             前台相关的模块文件    
        v1/              版本
            controllers/ 主要的接口controller文件            
    runtime/             API过程中产生的文件，包括log文件
    web/                 接口脚本入口    
wap
    config/              H5相关配置
    controllers/         
    runtime/             
    web/                 H5脚本入口    
storage
    config/              
    controllers/         
    runtime/             
    web/                 上传图片,文件   

vendor/                  第三方包依赖包文件
environments/            环境文件
```


## 安装步骤

1. 克隆代码到本地目录后使用composer进行安装，如果composer安装失败，可以下载完整项目源代码 http://zantoto.beesoft.ink/backend/web/zantoto-v0.0.1.zip , 然后进行第2步
```
git clone https://gitee.com/hbhe/zantoto.git
composer install
```


2. 执行以下命令, 检查PHP环境是否满足系统要求. 编辑php.ini, 检查php-intl是否被安装(php-intl是必须安装的一个扩展), 将short_open_tag=On
```
cd zantoto
php requirements.php
```

3. 创建数据库, 如
```
CREATE DATABASE zantoto DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

4. 初始化环境
```
php init
```

5. copy .env.dist .env, 然后编辑.env文件, 修改数据库密码，短信密码等参数


6. 安装redis. 在linux上安装较简单不多说; 对于在windows上安装, 如果采用MSI安装，按指引一步一步安装就可以了; 如果是Copy安装的, 类似操作如下:
- cd d:\tools\Redis
- redis-server --service-install redis.windows-service.conf --loglevel verbose
- redis-server --service-start
这样以后每次重启windows，都会有自动开启redis服务，不用每次手工启动, 其它uninstall, stop的命令可以看redis目录下的帮助文档。为了方便使用redis-cli这个工具，环境变量中可加上path=d:\tools\Redis


7. 执行数据库初始化脚本
```
php yii migrate --migrationPath=@yii/rbac/migrations 
php yii migrate --migrationPath=@mdm/admin/migrations
php yii migrate --migrationPath=@noam148/imagemanager/migrations
php yii migrate/up --migrationPath=@hbhe/settings/migrations
php yii migrate/up
```

8. 商品图片如果要支持上传视频，且后台有视频处理能力，需要先安装ffmpeg; 如果商品不需要支持视频，可以跳过此步<br>
对于Centos:
- sudo yum install epel-release
- sudo rpm -v --import http://li.nux.ro/download/nux/RPM-GPG-KEY-nux.ro
- sudo rpm -Uvh http://li.nux.ro/download/nux/dextop/el7/x86_64/nux-dextop-release-0-5.el7.nux.noarch.rpm
- sudo yum install ffmpeg ffmpeg-devel
- ffmpeg -version
- 编辑php.ini, 将proc_get_status从disable_functions列表中去掉<br>
<br>
对于Windows:
- 到https://ffmpeg.zeranoe.com/builds/ 下载windows版
- 解压到c:\ffmpeg目录下,注意调整路径, 保证 C:\ffmpeg\bin\ffmpeg.exe文件存在
- 在环境变量PATH中加入C:\ffmpeg\bin, 进入cmd后, 执行ffmpeg.exe -version, 能显示版本号

<!-- 
1. 启动定时任务
```
crontab -e
0 0 * * * /usr/bin/php /home/wwwroot/zantoto/yii night
*/5 * * * *  /usr/bin/php /home/wwwroot/zantoto/yii min
*/20 * * * *  /usr/bin/php /home/wwwroot/zantoto/yii min-twenty
```
 -->


9. 至此安装完成!，如何访问后台管理？
- 本地地址:http://127.0.0.1/zantoto/backend/web, 免安装线上Demo后台管理地址: http://zantoto.beesoft.ink/backend/web
- 用户名: webmaster
- 密码: webmaster 
- 验证码: 随便输入


10. 常见问题(FAQ)
- 如果要美化URL, 编辑backend\config\_urlManager.php，将'enablePrettyUrl' => true, 同时修改一下nginx.conf, 加上
```
location / {
    try_files $uri $uri/ /index.php?$args;
}
- 如果不需要美化，将'enablePrettyUrl' => false即可
```

- 安装完成后访问页面时报mysql group by错，是由于mysql版本太高不兼容以前的用法，解决办法是如下修改my.cnf, 然后重新启动mysql (service mysqld restart)
```
sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'
```
- 页面上显示\<\? \?\>这种符号怎么办？可修改php.ini, 将short_open_tag = On，然后重启PHP
- 其它问题请联系本人微信:15527210477, 注明zantoto