<?php

namespace App\Extra\DevTools\Services\Traits;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;

trait Logs
{

    /**
     * Получить информацию о логах
     * @throws InvalidArgumentException
     */
    public function getLogsData( string $need_type = null ): array
    {

        $cache_key = 'read_logs_'.$need_type;

        //Если открывается первая страница - отчищаем кеш (т.к. он нужен по сути только для пагинации)
        if( !request()->input('page') || request()->input('page') == 1 ) {
            cache()->forget($cache_key);
            cache()->forget($this->log_types_cache_key);
        }

        if( cache()->has($cache_key) )
            return cache()->get($cache_key);

        ini_set('max_execution_time', 90);
        ini_set('memory_limit', '4096M');

        $logs = [];
        $unique_logs = [];
        $logs_dir = 'storage/logs/';

        $log_files = scandir( base_path($logs_dir));

        foreach( $log_files as $log_path ){

            if( !str($log_path)->endsWith('.log') )
                continue;

            $log_file_content = $this->getFile($logs_dir.$log_path);
            $parsed_logs = $this->getTagContent($log_file_content, 'log', false);

            foreach( $parsed_logs as $log ){

                if( $log == '' )
                    continue;

                $type = str($this->getTagContent($log, 'type'))->lower()->ucfirst()->value();

                if( !in_array($type, $this->log_types) )
                    $this->log_types[] = $type;

                if( $need_type && $need_type != $type )
                    continue;

                $date = $this->getTagContent($log, 'date');
                $stacktrace = null;
                $first_trace = false;
                $first_trace_code = false;
                $errorId = null;
                $msg = $this->getTagContent($log, 'msg');

                $sql = false;
                if( str($msg)->contains('(SQL: ') ){
                    $explode = explode('(SQL: ', $msg);
                    $msg = $explode[0];
                    $sql = substr($explode[1], 0, -1);
                }

                $context = json_decode(
                    str_replace(["\n", "\r"], ['\n', '\r'], $this->getTagContent($log, 'context')),
                    true
                );

                if( is_null($context) )
                    $context = [];


                $extra = $this->getTagContent($log, 'extra');
                if( $extra )
                    $context['log_extra'] = print_r($extra, 1);


                //Если это ошибка (исключение)
                if( isset($context['exception']) ){

                    $exception = json_decode($context['exception'], true);

                    if( isset($exception['errorId']) )
                        $errorId = $exception['errorId'];

                    $stacktrace = [];
                    foreach( $exception['trace'] ?? [] as $trace ){

                        $trace_file = $this->getShortFilePath( $trace['file'] );
                        if( str($trace_file)->startsWith('/vendor/') || str($trace_file)->startsWith('/storage/') )
                            continue;

                        $stacktrace[] = [
                            'file' => $trace_file,
                            'file_full_path' => $trace['file'],
                            'line' => $trace['line'],
                            'msg' => $trace['msg']
                        ];

                    }

                    if( isset($exception['file']) ){

                        $first_trace = [
                            'file' => $this->getShortFilePath($exception['file']),
                            'file_full_path' => $exception['file'],
                            'line' => $exception['line'] ?? '',
                            'msg' => ''
                        ];

                    }

                    if( isset($exception['code']) ){

                        $first_trace_code = [
                            'first_line' => $exception['code_first_line'] ?? 0,
                            'line' => $exception['code_line'] ?? 0,
                            'code' => str_replace(['//n//', '\"'], ['\n', '"'], $exception['code'])
                        ];

                    }

                }


                //Если в сообщение попало от Debugger
                if( str($log)->contains(['class=sf-dump', '{"view":{"view":']) )
                    unset($context['view']['data']);

                unset($context['exception']);

                if( isset($context['view']['view']) && count($context['view']) )
                    $context['view'] = $context['view']['view'];


                $color = $this->getColorClassLogType($type);

                $log_id = $msg;
                if( $first_trace )
                    $log_id .= $first_trace['file'].$first_trace['line'];

                $log_id = md5($log_id);

                if( in_array($log_id, $unique_logs) ){

                    $log_key_for_count = Arr::where($logs, function ($value, $key) use ($log_id) {
                        return $value['log_id'] == $log_id;
                    });

                    $log_key_for_count = array_keys($log_key_for_count)[0];
                    $logs[$log_key_for_count]['count']++;

                    continue;
                }

                $unique_logs[] = $log_id;


                $logs[] = [
                    'date' => Carbon::createFromTimestamp(strtotime($date)),
                    'type' => $type,
                    'msg' => $msg,
                    'sql' => $sql,
                    'color' => $color,
                    'stacktrace' => $stacktrace,
                    'context' => $context,
                    'first_trace' => $first_trace,
                    'first_trace_code' => $first_trace_code,
                    'log_file' => $log_path,
                    'log_file_path' => base_path($logs_dir.$log_path),
                    'count' => 1,
                    'log_id' => $log_id,
                    'errorId' => $errorId
                ];

            }

        }

        krsort($logs);

        cache()->set( $cache_key, $logs, 6000);

        return $logs;

    }



    /**
     * Отчистить логи
     */
    public function clearLogs(): void
    {

        $logs_dir = 'storage/logs/';

        $log_files = scandir( base_path($logs_dir));

        foreach ( $log_files as $log_file ){

            if( $log_file == '.' || $log_file == '..' || $log_file == '.gitignore' )
                continue;

            unlink( base_path($logs_dir.$log_file));

        }

    }


    /**
     * Получить список типов записей в Logs
     */
    private array $log_types = [];
    private string $log_types_cache_key = 'read_logs_types';
    public function getLogTypes(): array
    {
        if( cache()->has($this->log_types_cache_key) )
            return cache()->get($this->log_types_cache_key);

        cache()->set($this->log_types_cache_key, $this->log_types, 6000);

        return $this->log_types;
    }


    /**
     * Получить CCS class для цвета
     */
    public function getColorClassLogType( string $type ): string
    {

        $color = 'default';

        if( $type == 'Error' || $type == 'Critical' || $type == 'Emergency' ){

            $color = 'danger';

        }elseif( $type == 'Warning' || $type == 'Notice' ){

            $color = 'warning';

        }elseif( $type == 'Alert' || $type == 'Info' ){

            $color = 'primary';

        }

        return $color;

    }

}
