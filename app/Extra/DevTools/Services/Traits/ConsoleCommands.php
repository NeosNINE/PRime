<?php

namespace App\Extra\DevTools\Services\Traits;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

trait ConsoleCommands
{

    /**
     * Получить Console Commands которые можно выполнить
     */
    public function getConsoleCommands(): array
    {

        $exclude = [
            'docs',
            '_complete',
            'completion',
            'debugbar:clear',
            'ide-helper*',
            'db*',
            'make*',
            'down',
            'up',
            'key:generate',
            'session:table',
            'serve',
            'vendor*',
            'migrate*',
            'tinker',
            'inspire',
            'env',
            'model*',
            'package*',
            'cache:table',
            'notifications:table',
            'stub:publish',
            'event*'
        ];

        $commands = [];
        foreach( Artisan::all() as $command ){

            $name = $command->getName();

            foreach( $exclude as $value ){

                if( str($value)->endsWith('*') ){

                    if( str($name)->startsWith(substr($value, 0, -1)) )
                        continue 2;

                }else{

                    if( $value == $name )
                        continue 2;

                }

            }

            $commands[] = $command;

        }

        return $commands;

    }


    /**
     * Запустить Artisan Console Command
     */
    public function consoleCommandRun( string $command ): string
    {
        return $this->artisanCommandRun($command, false);
    }


    /**
     * Запустить команду с выводом данных из outputBuffer
     * Если выводиться еще какая-та ошибка, вроде Target class [web] does not exist. - то это из-за того, что base_path не тот, который нужно, когда запускам из php команду
     * Если это команда фреймворка или пакета, то ничего не сделаешь. Если команду можно редактировать, то нужно base_path() прописать к любым path в скрипте
     */
    public function artisanCommandRun( string $command, bool $just_msg_if_no_errors = true ): string
    {

        $buffer = new BufferedOutput();

        $exit_code = Artisan::call($command, outputBuffer: $buffer);

        return $this->getBufferOutputHtml($buffer, $exit_code, $just_msg_if_no_errors);

    }



    /**
     * Получить сообщение от консоли в виде HTML
     * $exit_code = 0 - успешно выполнено
     * $exit_code = 1 - завершено с ошибками
     * $just_msg_if_no_errors - если true, то выведет как обычный текст (а не HTML), при условии, что выполнено успешно
     */
    public function getBufferOutputHtml( BufferedOutput $buffer, $exit_code = 0, $just_msg_if_no_errors = true ): string
    {

        $msg = '';

        foreach( explode($this->getEOL(), $buffer->fetch()) as $line ){

            if( !$line )
                continue;

            $style = false;

            if ( str($line)->startsWith('[info]') ){

                $style = 'info';

            }else if ( str($line)->startsWith('[comment]') ){

                $style = 'comment';

            }else if ( str($line)->startsWith('[question]') ){

                $style = 'question';

            }else if ( str($line)->startsWith('[warn]') ){

                $style = 'warn';

            }else if ( str($line)->startsWith('[error]') ){

                $style = 'error';

            }else if ( str($line)->startsWith('[line]') ){

                $style = 'error';

            }

            if( $style ){

                $line = mb_substr($line, mb_strlen($style) + 2);

            }else{

                $style = 'line';

            }

            if( $exit_code == 1 || !$just_msg_if_no_errors ){

                $msg .= '<p class="'.$style.'">'.$line.'</p>';

            }else{

                $msg .= $line.'<br>';

            }


        }

        if( $exit_code == 0 ){

            if( $just_msg_if_no_errors ){

                return $msg;

            }

        }

        $html = view('admin.app.dev_tools.components.output_buffer_html', [
            'msg' => $msg
        ])->render();

        if( $exit_code != 0 )
            abort(500, $html);

        return $html;

    }

}
