composer dump-autoload -o
dockerize -template .env.example:.env
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
