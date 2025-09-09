#!/bin/bash

curr_uid=$(id -u)
curr_gid=$(id -g)

curr_dir=$(pwd)

if [ ! -d "vendor" ]; then
  echo "Downloading dependencies ..."
  docker run \
    --user $curr_uid:$curr_gid \
    --mount type=bind,source="$curr_dir",target="/app" \
    composer:lts composer install --ignore-platform-req=ext-pcntl --ignore-platform-req=ext-gmp
fi

export DEVELOPER_UID_OVERRIDE=$curr_uid
export DEVELOPER_GID_OVERRIDE=$curr_gid

docker compose up