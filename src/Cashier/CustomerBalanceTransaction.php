<?php

namespace BulletDigitalSolutions\DoctrineCashier\Cashier;

use BulletDigitalSolutions\DoctrineCashier\Exceptions\InvalidCustomerBalanceTransaction;
use Laravel\Cashier\CustomerBalanceTransaction as BaseCustomerBalanceTransaction;

class CustomerBalanceTransaction extends BaseCustomerBalanceTransaction
{
    /**
     * Create a new CustomerBalanceTransaction instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $owner
     * @param  \Stripe\CustomerBalanceTransaction  $transaction
     * @return void
     *
     * @throws \BulletDigitalSolutions\Gunshot\Cashier\Exceptions\InvalidCustomerBalanceTransaction;
     */
    public function __construct($owner, StripeCustomerBalanceTransaction $transaction)
    {
        if ($owner->stripeId() !== $transaction->customer) {
            throw InvalidCustomerBalanceTransaction::invalidOwner($transaction, $owner);
        }

        $this->owner = $owner;
        $this->transaction = $transaction;
    }
}
