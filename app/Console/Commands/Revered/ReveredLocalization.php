<?php

namespace App\Console\Commands\Revered;

use Illuminate\Console\Command;

class ReveredLocalization extends Command
{

    use ReveredConsoleTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revered:localization {method}';


    /**
     * Команды:
     * php artisan revered:localization refresh - Обновить все файлы локалей в соответствии с текущими данными в базе данных
     * php artisan revered:localization load_to_db - Выгрузить локали из файлов в базу данных
     */



    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Localization';

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
     * @return void
     */
    public function handle(): mixed
    {

        $method = $this->argument('method');

        if( !method_exists($this, $method) ) {

            $this->print('Метод "' . $method . '" не существует.', 'error');

        }else{

            return $this->{$method}();

        }

    }


    /**
     * Обновить все файлы локалей в соответствии с текущими данными в базе данных
     */
    public function refresh(): int
    {

        try {

            localization()->refreshLocalsFiles();

            $this->print('Локали успешно обновлены.');

            return self::SUCCESS;

        } catch ( \Throwable $e ){

            $this->print('Ошибка при обновлении локалей: '.$e->getMessage(), 'error');
            return self::FAILURE;

        }
    }


    /**
     * Выгрузить локали из файлов в базу данных
     */
    public function load_to_db(): int
    {

        try {

            $statuses = localization()->loadFromFilesToDB();

            foreach( $statuses['success'] as $msg )
                $this->print($msg);

            foreach( $statuses['error'] as $msg )
                $this->print($msg, 'error');

            if( count($statuses['error']) )
                return self::FAILURE;

            $this->print('Выгрузка локалей из файлов в базу данных успешно завершена.');

            return self::SUCCESS;


        } catch ( \Throwable $e ){

            $this->print('Ошибка при выгрузке локалей в базу данных: '.$e->getMessage(), 'error');
            return self::FAILURE;

        }

    }


    /**
     * Проверить локализацию, найти потенциальные ошибки
     */
    public function check(): int
    {

        try {

            $data = localization()->check();

            foreach( $data as $d ){

                if( $d['type'] == 'info' ){

                    $this->print($d['msg']);

                }else{

                    $this->print($d['msg'], 'warn');

                }

            }

            if( count($data) ){

                return self::FAILURE;

            }else{

                $this->print('Check passed successfully.');
                return self::SUCCESS;

            }

        } catch ( \Throwable $e ){

            $this->print('Ошибка при проверки локализации: '.$e->getMessage(), 'error');
            return self::FAILURE;

        }

    }

}
