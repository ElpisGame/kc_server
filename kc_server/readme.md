# 游戏部署文档

    apt update && apt upgrade && apt install git

## 搭建环境

### 安装docker

参考地址：[https://blog.csdn.net/qq_30935743/article/details/107977665](https://blog.csdn.net/qq_30935743/article/details/107977665)

    sudo mkdir -p /etc/docker
    sudo tee /etc/docker/daemon.json <<-'EOF'
    {
        "registry-mirrors": ["https://524zr4x5.mirror.aliyuncs.com"]
    }
    EOF
    sudo systemctl daemon-reload
    sudo systemctl restart docker

### 安装docker-compose

    apt install python3-pip

    pip3 install docker-compose

### 拉取镜像

    docker pull misoag/gameserver

### 部署服务

    服务器执行
    mkdir -p /Project/game && cd /Project/game/ && git init

    本地执行
    git remote add server root@newgame:/Project/game
    ./deploy.sh

### 初始化游戏

    docker-compose up -d 

    宝塔面板：http://47.118.55.226:8888

## 常用命令

### 启动游戏服务（在docker外部执行）

    docker exec gameserver /server/game/sh/start.sh

### 更新游戏服务（在docker外部执行）

    docker exec gameserver /server/game/sh/update.sh

### 关闭游戏服务（在docker外部执行）

    docker exec gameserver /server/game/sh/stop.sh

### 数据库（在docker内部执行）

    备份数据库
    ./gamectl.sh dumpall

    还原数据库
    ./gamectl.sh recover game ../../data/game.sql.gz
    ./gamectl.sh recover cross ../../data/cross.sql.gz
    ./gamectl.sh recover center ../../data/cross.sql.gz

    删除所有数据库（慎用）
    ./gamectl.sh dropsqlbase