<?php

//TODO: Is this required?

namespace BulletDigitalSolutions\DoctrineCashier\Cashier;

use Laravel\Cashier\CustomerBalanceTransaction as BaseCustomerBalanceTransaction;
use BulletDigitalSolutions\Gunshot\Cashier\Exceptions\InvalidCustomerBalanceTransaction;
use Stripe\CustomerBalanceTransaction as StripeCustomerBalanceTransaction;

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