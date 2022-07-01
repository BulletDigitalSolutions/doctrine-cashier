<?php

namespace BulletDigitalSolutions\DoctrineCashier;

use Illuminate\Support\Facades\Facade;

/**
 * @see \BulletDigitalSolutions\DoctrineCashier\Skeleton\SkeletonClass
 */
class DoctrineCashierFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'doctrine-cashier';
    }
}
