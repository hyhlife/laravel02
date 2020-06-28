#!/bin/bash

docker login --username=15659036112 -p 1oveni1314 registry.cn-hangzhou.aliyuncs.com
docker tag app-backend:latest registry.cn-hangzhou.aliyuncs.com/hyhlife/app-backend2:latest
docker push registry.cn-hangzhou.aliyuncs.com/hyhlife/app-backend2:latest

if [ $# -gt 0 ] ; then
  tag=$1
  docker tag app-backend:latest registry.cn-hangzhou.aliyuncs.com/hyhlife/app-backend2:${tag}
  docker push registry.cn-hangzhou.aliyuncs.com/hyhlife/app-backend2:${tag}
fi
