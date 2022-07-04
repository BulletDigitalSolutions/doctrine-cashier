<?php

//TODO: Is this required?

namespace BulletDigitalSolutions\DoctrineCashier\Cashier\Exceptions;

use Laravel\Cashier\Exceptions\InvalidPaymentMethod as BaseInvalidPaymentMethod;
use Stripe\PaymentMethod as StripePaymentMethod;

class InvalidPaymentMethod extends BaseInvalidPaymentMethod
{
    /**
     * Create a new InvalidPaymentMethod instance.
     *
     * @param  \Stripe\PaymentMethod  $paymentMethod
     * @param  \Illuminate\Database\Eloquent\Model  $owner
     * @return \Laravel\Cashier\Exceptions\InvalidPaymentMethod
     */
    public static function invalidOwner(StripePaymentMethod $paymentMethod, $owner)
    {
        return new static(
            "The payment method `{$paymentMethod->id}` does not belong to this customer `$owner->stripeId()`."
        );
    }
}
