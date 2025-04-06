cp .env.example .env
##
./vendor/bin/sail up -d
##
./vendor/bin/sail composer install
##
./vendor/bin/sail npm install
##
./vendor/bin/sail npm run build
##
./vendor/bin/sail artisan migrate
##
./vendor/bin/sail artisan test
##
./vendor/bin/sail artisan queue:listen --queue=chunks --timeout=300
##
./vendor/bin/sail artisan queue:work --queue=default
##
sail artisan reverb:start
