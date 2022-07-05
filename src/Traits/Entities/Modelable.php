<?php

namespace BulletDigitalSolutions\DoctrineCashier\Traits\Entities;

use Illuminate\Support\Str;

trait Modelable
{
    /**
     * @param $name
     * @param $arguments
     * @return |null
     */
    public function __get($key)
    {
        $function = Str::camel(sprintf('get %s', $key));

        if (method_exists($this, $function)) {
            return $this->{$function}();
        }
    }

    ////    TODO
//    public function save()
//    {
//
//    }
}
