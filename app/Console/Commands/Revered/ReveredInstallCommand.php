<?php

namespace App\Console\Commands\Revered;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;


/**
 * Данный класс нужен исключительно для разработки и поддержки проекта.
 * Функционал для самого проекта здесь не прописывается.
 * Это авторская разработка, права принадлежат revered.pro
 */
class ReveredInstallCommand extends Command
{

    use ReveredConsoleTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revered:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Запустить проект на Local.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {

        $env_path = base_path('.env');
        if( !devTools()->fileExists($env_path) ){

            $project_name = $this->ask('Введите название проекта');

            $domain = str($project_name)->lower()->replace(' ', '-').'.local';
            $project_domain = $this->ask('Введите локальный домен', $domain);
            $database_name = $this->ask('Название базы данных', str($project_domain)->replace('.local', ''));
            $database_username = $this->ask('Пользователь базы данных', 'root');
            $database_password = $this->ask('Пароль базы данных', 'root');

            devTools()->saveFile($env_path,'APP_NAME="'.$project_name.'"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=https://'.$project_domain.'

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE='.$database_name.'
DB_USERNAME='.$database_username.'
DB_PASSWORD='.$database_password.'

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"');

            $this->error('.env файл создан, запустите команду снова.');
            return self::FAILURE; //Выдаем как ошибку, чтобы привлечь внимание снова запустить команду


        }

        $test_git_ignore_controller_path = base_path('app/Extra/DevTools/Controllers/Debug/TestGitIgnoreController.php');
        if( !devTools()->fileExists($test_git_ignore_controller_path) ){

            devTools()->saveFile($test_git_ignore_controller_path,'<?php

namespace App\Extra\DevTools\Controllers\Debug;

use App\Http\Controllers\Controller;

class TestGitIgnoreController extends Controller
{

    /**
     *  - GIT IGNORE -
     * Здесь можно прописывать код, функции и т.д. и тестировать в браузере по адресу /debug/test_git_ignore
     */
    public function index()
    {

        return view(\'debug.test_git_ignore\',[

        ]);

    }
}');

        }

        $test_git_ignore_view_path = base_path('resources/views/debug/test_git_ignore.blade.php');
        if( !devTools()->fileExists($test_git_ignore_view_path) ){

            devTools()->saveFile($test_git_ignore_view_path,"@extends('debug.layout')

@section('content')

    <div>

        Some Test Page (Git Ignore).

    </div>

@endsection");

        }

        devTools()->createSecretConfigFile();

        Artisan::call('migrate');
        Artisan::call('db:seed');
        Artisan::call('storage:link');
        Artisan::call('key:generate');

        $this->info('Настройка успешно завершена.');
        return self::SUCCESS;

    }

}
