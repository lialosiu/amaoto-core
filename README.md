# Amaoto 后端核心

此项目正在开发中...bug较多，发现问题请尽情甩我一脸issues (/ω＼)

## 项目说明

因为之前在学校时写的音乐网太挫(毕竟只用了一个月写..)，然而又没时间更新代码

等想到要更新的时候，发现当时用的很多开发框架都已经升级了不少版本

于是就干脆直接重写了一个，顺便用来练习下新玩具的用法ww

项目用的语言是 PHP， 框架为 Laravel 5.2

## 依赖服务器环境

- PHP >= 5.5.9

## 用例

1. 部署好 WebServer
2. 确认 PHP 版本**高于 5.5.9**
3. 安装好 PHP 包管理工具 **Composer**
4. git clone 本项目
5. cd 到 ./laravel 路径下
6. 执行 ``composer install`` 安装相关依赖包
7. 配置 ``./.env`` 文件，设置数据库相关信息
8. 执行 ``php artisan migrate`` 进数据库结构迁移(如果此步骤报错，请检查数据库配置是否有误)
9. 完成后，执行 ``php artisan app:install`` 进行系统的初始化，并按屏幕提示操作

## 相关项目

[lialosiu/amaoto-ngjs](https://github.com/lialosiu/amaoto-ngjs) 对应的前端项目，使用 **AngularJS** 编写
