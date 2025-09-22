<?php

namespace App\Extra\DevTools\Services\Traits;

trait Files
{

    /**
     * Получить короткий path для файла
     */
    private function getShortFilePath( string $path ): string
    {
        $dirs = [
            'app',
            'bootstrap',
            'config',
            'database',
            'lang',
            'public',
            'resources',
            'routes',
            'storage',
            'tests',
            'vendor'
        ];

        $short_path = $path;
        $exploded = explode('/', $path );

        foreach( $exploded as $key => $s ){

            if( !$s )
                continue;

            if( in_array($s, $dirs) ){

                $pre_last = $exploded[$key - 2] ?? '';
                $last = $exploded[$key - 1] ?? '';

                $str = '';

                if( $pre_last )
                    $str .= '/'.$pre_last;

                if( $last )
                    $str .= '/'.$last;

                $short_path = str_replace($str, '|||', $path);
                $short_path = explode('|||', $short_path);
                $short_path = $short_path[1] ?? $short_path[0];

                break;

            }

        }

        return $short_path;

    }


    /**
     * Получить часть кода из файла
     * @param int $spread - Сколько строк кода показываем до и после нужно строки
     */
    public function getPieceCodeFromFile( string $file_path, int $line, int $spread = 8 ): array
    {

        if( !$this->normalizeFilePath($file_path) )
            return ['first_line' => 0, 'code' => 'File does not exist.'];

        $start_line = $line - $spread;
        if( $start_line < 0 )
            $start_line = 0;

        $end_line = $line + $spread;

        $first_line = 0;
        $code = '';

        foreach( explode($this->getEOL(), $this->getFile($file_path)) as $key => $line ){

            $line_number = $key + 1;

            if( $line_number < $start_line || $line_number > $end_line )
                continue;

            if( $first_line == 0 )
                $first_line = $line_number;

            $code .= $this->getEOL() . $line;

        }

        return [
            'first_line' => $first_line,
            'code' => $code
        ];

    }


    /**
     * Получить кусок кода из файла между строками
     */
    public function getCodeFromFile( string $file_path, int $start_line = 0, ?int $end_line = null ): string
    {

        $file_path = $this->normalizeFilePath($file_path);


        if( !file_exists($file_path) )
            return '';


        if( is_null($end_line) )
            return $this->getFile($file_path);


        $code = '';

        foreach( explode($this->getEOL(), $this->getFile($file_path)) as $key => $line ){

            $line_number = $key + 1;

            if( $line_number >= $start_line )
                $code .= $this->getEOL() . $line;

            if( $end_line && $line_number == $end_line )
                break;

        }

        return $code;

    }


    /**
     * Получить контент файла
     */
    public function getFile( string $path ): bool|string
    {

        return file_get_contents($this->normalizeFilePath($path));

    }


    /**
     * Сохранить файл
     */
    public function saveFile( string $path, string $content ): bool|int
    {

        $path = $this->normalizeFilePath($path);

        $this->checkAndCreateFolder($path);

        return file_put_contents($path, $content);

    }


    /**
     * Проверка существования файла
     */
    public function fileExists( string $path ): bool
    {
        return file_exists($this->normalizeFilePath($path));
    }



    /**
     * Проверить существование папки, и если ее нет - создать рекурсивно
     */
    public function checkAndCreateFolder( string $path ): bool
    {

        $folder = dirname($this->normalizeFilePath($path));

        if( !file_exists($folder) )
            return mkdir($folder, 0777, true);

        return true;

    }


    /**
     * Подготовка пути файла к получению / сохранению контента
     */
    public function normalizeFilePath( string $path ): string
    {

        $path = $this->pathClearSlashes($path);

        if( !str_contains($path, $this->pathClearSlashes(base_path())) ){

            $path = base_path($path);
            $path = $this->pathClearSlashes($path);

        }

        $pos = strpos($path, '/App/');
        if( $pos !== false)
            $path = substr_replace($path, '/app/', $pos, strlen('/app/'));

        return $path;

    }


    /**
     * Обратные слэши преобразовать в обычные
     */
    public function pathClearSlashes( string $path ): string
    {
        return str($path)->replace('\\', '/')->replace('//', '/')->toString();
    }

}
