<?php

namespace App\Models\Traits;

trait ModelTrait
{

    use ModelSetDataAttrTrait;
    public $html_table_row; //Игнорируем это поле при сохранении данных в БД
    public $event_data; //Игнорируем это поле при сохранении данных в БД

}
