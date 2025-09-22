<?php

namespace App\Services\System;

class CacheService
{

    /**
     * Закешировать результат на одну загрузку страницы. В рамках загрузки страницы результат будет закеширован,
     * но при обновлении страницы результат отчиститься и снова будет выполнен код из функции.
     * Выполняет код из анонимной функции один раз, и при повторном вызове возвращает результат.
     */
    private array $oneLoadCached = [];
    public function oneLoad ( string $key, callable $func ){

        if( array_key_exists($key, $this->oneLoadCached) )
            return $this->oneLoadCached[$key];

        return $this->oneLoadCached[$key] = $func();

    }

}
