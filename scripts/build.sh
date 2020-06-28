#!/bin/sh

if [ $# -gt 1 ] ; then
    docker build -t app-backend2:$1 -t  app-backend2:latest  .
else
    docker build -t  app-backend2:latest  .
fi