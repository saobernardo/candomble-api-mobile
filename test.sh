#!/bin/bash

curr_uid=$(id -u)
curr_gid=$(id -g)
app_dir=$(pwd)

filter=$1

docker run \
  --rm -it \
  --user $curr_uid:$curr_gid \
  --mount type=bind,source="$app_dir",target=/app \
  --mount type=bind,source="$(pwd)/.env.testing",target=/app/.env.testing,readonly \
  --network candomble \
  --env-file "$(pwd)/.env.testing" \
  jitesoft/phpunit:8.5 php artisan test ${filter:+--filter $filter}
