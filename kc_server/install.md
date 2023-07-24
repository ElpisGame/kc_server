# 服务器

    新服务器
    47.118.55.226
    root
    Kckj1234

    mysql root 4sziXc8XKEp4X4nF

## 操作系统版本

    CentOS Linux release 7.2.1511

## 操作系统更新

    yum update

## 安装宝塔面板

    yum install -y wget && wget -O install.sh http://download.bt.cn/install/install.sh && sh install.sh

宝塔面板安装完成后会输出如下内容

    Bt-Panel: http://47.118.55.226:8888
    username: su2g3haa
    password: 08265d76

- 登陆宝塔面板，安装LNMP，默认安装位置在：`/www` 目录下。
- 在宝塔面板中升级Mysql到5.7，点击宝塔面板左侧数据库按钮，修改root密码（4sziXc8XKEp4X4nF）

## 初始化数据库

    复制sql脚本文件到服务器
    scp -r /Users/john/Downloads/西游源码/db root@newgame:~/

    创建数据库
    create database `center`;
    create database `cross`;
    create database `xntg1`;

    执行数据库初始化脚本
    use center;
    source ~/db/center.sql;
    use `cross`;
    source ~/db/cross.sql;
    use `xntg1`;
    source ~/db/xntg1.sql;

## 替换下面两个脚本到/usr/lib64目录

    scp /Users/john/Downloads/西游源码/西游单机H5/xiyou/tools/usr-lib64/libstdc++.so.6 root@newgame:/usr/lib64

    scp /Users/john/Downloads/西游源码/西游单机H5/xiyou/tools/usr-lib64/libtcmalloc.so.4 root@newgame:/usr/lib64

## 安装LUA

    wget http://www.lua.org/ftp/lua-5.1.4.tar.gz
    tar zxvf lua-5.1.4.tar.gz
    cd lua-5.1.4
    make linux

## 永久关闭防火墙

    命令重启后，防火墙不会自动启动：systemctl disable firewalld
    查看防火墙状态：sudo systemctl status firewalld
    关闭防火墙：sudo systemctl stop firewalld

## 复制服务器端代码到Server

    scp -r /Users/john/Downloads/西游源码/西游单机H5/xiyou/project/server root@newgame:/Project
    
    修改配置文件
    scp *.xml root@newgame:/Project/server/game/sh

    修改游戏启动器到可执行权限
    chmod u+x /Project/server/game/libc++/App

    启动游戏
    ./gamectl.sh startall

    实际上执行到是下面三个命令，可以 `ps -ef |grep game` 查看
    /Project/server/game/libc++/App -configure=/Project/server/game/sh/configure_center.xml
    /Project/server/game/libc++/App -configure=/Project/server/game/sh/configure_cross.xml
    /Project/server/game/libc++/App -configure=/Project/server/game/sh/configure.xml
    竟然有错误，可能缺少配置文件？
    mysql: [Warning] Using a password on the command line interface can be insecure.
    mysql: [Warning] Using a password on the command line interface can be insecure.
    mysql: [Warning] Using a password on the command line interface can be insecure.
    grep: /Project/server/game/sh/configure_plat.xml: 没有那个文件或目录
    grep: /Project/server/game/sh/configure_plat.xml: 没有那个文件或目录
    grep: /Project/server/game/sh/configure_plat.xml: 没有那个文件或目录
    grep: /Project/server/game/sh/configure_plat.xml: 没有那个文件或目录
    grep: /Project/server/game/sh/configure_plat.xml: 没有那个文件或目录
    ERROR 2005 (HY000): Unknown MySQL server host '-P' (2)
    grep: /Project/server/game/sh/configure_record.xml: 没有那个文件或目录
    grep: /Project/server/game/sh/configure_record.xml: 没有那个文件或目录
    grep: /Project/server/game/sh/configure_record.xml: 没有那个文件或目录
    grep: /Project/server/game/sh/configure_record.xml: 没有那个文件或目录
    grep: /Project/server/game/sh/configure_record.xml: 没有那个文件或目录
    ERROR 2005 (HY000): Unknown MySQL server host '-P' (2)

## 客户端

编译客户端，复制到服务器

    scp /Users/john/Downloads/西游源码/西游单机H5/xiyou/project/client/project/bin-release/web/210319140343.zip root@newgame:/www/wwwroot

    在宝塔面板建立站点，使用999端口

    修复找不到js文件的问题
    scp -r libs root@newgame:/www/wwwroot/172.16.129.58

    http://47.118.55.226:999

## GM

    http://47.118.55.226:9090/gm/

    admin
    123456

## 更新镜像

    docker commit -m="has update" -a="huangsz" 564fb83b6482  misoag/gameserver