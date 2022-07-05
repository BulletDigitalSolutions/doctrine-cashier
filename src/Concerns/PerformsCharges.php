<?php

namespace BulletDigitalSolutions\DoctrineCashier\Concerns;

use Laravel\Cashier\Concerns\PerformsCharges as BasePerformsCharges;
use Laravel\Cashier\Payment;

trait PerformsCharges
{
    use BasePerformsCharges;

}
