<?php

namespace BulletDigitalSolutions\DoctrineCashier\Concerns;

//TODO - This is not required anymore

use Laravel\Cashier\Concerns\PerformsCharges as BasePerformsCharges;
use Laravel\Cashier\Payment;

trait PerformsCharges
{
    use BasePerformsCharges;

    /**
     * Create a new Payment instance with a Stripe PaymentIntent.
     *
     * @param  int  $amount
     * @param  array  $options
     * @return \Laravel\Cashier\Payment
     */
    public function createPayment($amount, array $options = [])
    {
        $options = array_merge([
            'currency' => $this->preferredCurrency(),
        ], $options);

        $options['amount'] = $amount;

        if ($this->hasStripeId()) {
            $options['customer'] = $this->stripeId();
        }

        return new Payment(
            $this->stripe()->paymentIntents->create($options)
        );
    }
}