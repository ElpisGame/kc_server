#!/usr/bin/env sh

git add .
git commit -m "deploy"

SUFFIX=`date +"%Y%m%d-%H%M%S"`
BRANCH_NAME="deploy-"$SUFFIX

git branch $BRANCH_NAME

git branch

git push server $BRANCH_NAME

ssh root@newgame "cd /Project/server; git checkout $BRANCH_NAME"
