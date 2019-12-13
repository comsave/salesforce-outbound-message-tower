#!/bin/bash
set -m

/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf &
cd /app/project && bin/console cache:clear --env="$APP_ENV"

fg