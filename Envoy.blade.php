@servers(['web' => 'root@5.35.88.81'])

@task('pull', ['on' => ['web']])
cd /var/www/djigit24_gpt
git pull origin main
php artisan l5-swagger:generate
@endtask
@task('migrate', ['on' => ['web']])
cd /var/www/djigit24_gpt
php artisan migrate
@endtask
@task('clear', ['on' => ['web']])
cd /var/www/djigit24_gpt
php artisan cache:clear
php artisan route:clear
php artisan config:clear
@endtask
@task('install', ['on' => ['web']])
cd /var/www/djigit24_gpt
php composer.phar install
@endtask
@task('create-admin', ['on' => 'web'])
cd /var/www/djigit24_gpt
php artisan orchid:admin admin admin@admin.com zVuIGw
@endtask
