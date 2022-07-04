<?php

//TODO: Is this required?

namespace BulletDigitalSolutions\DoctrineCashier\Cashier\Exceptions;

use Laravel\Cashier\Exceptions\InvalidCustomerBalanceTransaction as BaseInvalidCustomerBalanceTransaction;
use Stripe\CustomerBalanceTransaction as StripeCustomerBalanceTransaction;

class InvalidCustomerBalanceTransaction extends BaseInvalidCustomerBalanceTransaction
{
    /**
     * Create a new CustomerBalanceTransaction instance.
     *
     * @param  \Stripe\CustomerBalanceTransaction  $transaction
     * @param  \Illuminate\Database\Eloquent\Model  $owner
     * @return \Laravel\Cashier\Exceptions\InvalidCustomerBalanceTransaction
     */
    public static function invalidOwner(StripeCustomerBalanceTransaction $transaction, $owner)
    {
        return new static("The transaction `{$transaction->id}` does not belong to customer `$owner->stripeId()`.");
    }
}
