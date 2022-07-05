<?php

namespace BulletDigitalSolutions\DoctrineCashier;

use Laravel\Cashier\Cashier as BaseCashier;
use Stripe\Customer as StripeCustomer;

class Cashier extends BaseCashier
{
    /**
     * Indicates if Cashier migrations will be run.
     *
     * @var bool
     */
    public static $runsMigrations = true;

    /**
     * The default customer model class name.
     *
     * @var string
     */
    public static $customerModel = 'App\\Entities\\User';

    /**
     * Get the customer instance by its Stripe ID.
     *
     * @param  \Stripe\Customer|string|null  $stripeId
     * @return \Laravel\Cashier\Billable|null
     */
    public static function findBillable($stripeId)
    {
        $stripeId = $stripeId instanceof StripeCustomer ? $stripeId->id : $stripeId;

        return $stripeId ? (new static::$customerModel)->getRepository()->findOneBy(['stripeId' => $stripeId]) : null;
    }
}
