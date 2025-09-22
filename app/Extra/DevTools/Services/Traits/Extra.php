<?php

namespace App\Extra\DevTools\Services\Traits;

trait Extra
{

    /**
     * PHP_EOL
     */
    public function getEOL(): string
    {
        return PHP_EOL;
    }


    /**
     * Получить контент между тегами
     */
    public function getTagContent( string $str, string $tag, bool $ony_one_result = true, bool $trim = true ): array|string
    {

        preg_match_all('#\['.$tag.'](.+?)\[/'.$tag.']#is', $str, $result);

        if( !count($result[1]) )
            return $ony_one_result ? '' : [];

        if( $ony_one_result ){

            $return = $trim ? trim($result[1][0]) : $result[1][0];

        }else{

            $return = [];

            foreach( $result[1] as $val )
                $return[] = $trim ? trim($val) : $val;

        }

        return $return;

    }


    /**
     * Увеличиваем лимиты для импорта
     */
    private function phpIniSetToImport(): void
    {

        ini_set('memory_limit', '14000M');
        ini_set('max_execution_time', 20000);

    }



    /**
     * Print array for insert to PHP code
     */
    public function printArrayForPHPCode( array $array, bool $with_pre = true, int $tabs = 0, bool $print = true )
    {

        $array_as_code = '';

        if( $with_pre )
            $array_as_code .= '<pre>';

        $lines = 0;

        $array_as_code .= str_repeat('   ', $tabs)."[".$this->getEOL();
        foreach( $array as $key => $val ){

            $lines++;

            if( $lines > 1 )
                $array_as_code .= ",".$this->getEOL();

            $array_as_code .= str_repeat('   ', $tabs+1)."'".$key."' => ";

            if( is_array($val) ){

                $array_as_code .= $this->printArrayForPHPCode($val, false, $tabs+1, false);

            }elseif( is_numeric($val) ){

                $array_as_code .= $val;

            }else{

                $array_as_code .= '"'.stripcslashes($val).'"';

            }

        }
        $array_as_code .= $this->getEOL() . str_repeat('   ', $tabs)."]";

        if( $with_pre )
            $array_as_code .= '</pre>';

        if( !$print ){

            return $array_as_code;

        }else{

            echo $array_as_code;

        }

    }


    /**
     * Получить ссылку на Favicon (для разработчиков)
     */
    public function getFaviconUrl(): string
    {

        return asset('favicon-dev.png');

    }

}
