<?php

namespace App\Models\Traits;

/**
 * @property array|null $data
 */
trait ModelSetDataAttrTrait
{

    public function setDataAttr( array|string $key, $value = null, string $field = 'data' ): static
    {

        $data_to_update = is_array($key) ? $key : [ $key => $value ];

        $data = $this->{$field};

        foreach ($data_to_update as $k => $v)
            $data[$k] = $v;

        $this->{$field} = $data;

        return $this;

    }


    public function setDataAttrIncrement( string $key, float|int $increment = 1, string $field = 'data' ): static
    {

        $data = $this->{$field};

        if( isset($data[$key]) ){

            $data[$key] += $increment;

        }else{

            $data[$key] = $increment;

        }

        $this->{$field} = $data;

        return $this;

    }


    public function setDataAttrDecrement( string $key, float|int $increment = 1, string $field = 'data' ): static
    {

        $data = $this->{$field};

        if( isset($data[$key]) ){

            $data[$key] -= $increment;

        }else{

            $data[$key] = 0 - $increment;

        }

        $this->{$field} = $data;

        return $this;

    }

}
