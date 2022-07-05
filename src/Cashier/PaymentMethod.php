<?php

namespace BulletDigitalSolutions\DoctrineCashier\Cashier;

use BulletDigitalSolutions\DoctrineCashier\Concerns\ManagesPaymentMethods;
use BulletDigitalSolutions\DoctrineCashier\Exceptions\InvalidPaymentMethod;
use Laravel\Cashier\PaymentMethod as BasePaymentMethod;
use Stripe\PaymentMethod as StripePaymentMethod;

class PaymentMethod extends BasePaymentMethod
{
    /**
     * Create a new PaymentMethod instance.
     *
     * @param  ManagesPaymentMethods  $owner
     * @param  \Stripe\PaymentMethod  $paymentMethod
     * @return void
     *
     * @throws \Laravel\Cashier\Exceptions\InvalidPaymentMethod
     */
    public function __construct($owner, StripePaymentMethod $paymentMethod)
    {
        if ($owner->stripeId() !== $paymentMethod->customer) {
            throw InvalidPaymentMethod::invalidOwner($paymentMethod, $owner);
        }

        $this->owner = $owner;
        $this->paymentMethod = $paymentMethod;
    }
}
