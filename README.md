## Install project

1. run composer: `composer install`
2. copy file `.env.example` to `.env`
3. in `.env` file set conntection params to database: `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
4. in `.env` file set smtp params to send email: `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`
5. run migrations command: `php artisan migrate`
6. run seeds: `php artisan db:seed --class=InstallSeeder`

## After 'git pull' run commands:

1. `composer update`
2. `php artisan migrate`
3. `php artisan db:seed --class=SyncSeeder`
