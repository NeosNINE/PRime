<?php

namespace App\Console\Commands;

use App\Console\Commands\Revered\ReveredConsoleTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;


class CheckJobQueueCommand extends Command
{

    use ReveredConsoleTrait;

    protected $signature = 'check:queue {--restart}';

    protected $description = 'Проверяет запущен ли демон Queue для очередей.';

    public function handle(): int
    {

        $check = DB::table('jobs')->where('created_at', '<', now()->subMinutes(3)->unix())->first();

        if( $check ){

            $data = json_decode($check->payload,1);

            $msg_line_1 = 'Скорее всего Queue Worker не работает. Есть в очереди задание, которое весит уже: '. Carbon::createFromTimestamp( $check->created_at)->diffInMinutes(now()) . ' мин.';
            $msg_line_2 = 'Задание: #'.$check->id.'   '.$data['displayName'] ?? null;

            $this->print($msg_line_1, 'error');
            $this->print($msg_line_2, 'error');


            if( $this->option('restart') ){

                Artisan::call('queue:restart');
                sleep(10);
                Artisan::call('queue:work');

                $this->print('Queue Worker successfully restarted.');


            }else{

                revered()->sendCriticalMessage( $msg_line_1."\n".$msg_line_2 );

            }

            return self::FAILURE;

        }else{

            $this->print('Заданий в очереди не найдено. Вероятно все работает хорошо.');

            return self::SUCCESS;

        }


    }
}
