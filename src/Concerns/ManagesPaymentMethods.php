<?php

namespace BulletDigitalSolutions\DoctrineCashier\Concerns;

//TODO - This is not required anymore

use BulletDigitalSolutions\Gunshot\Cashier\PaymentMethod;
use Illuminate\Support\Collection;
use Laravel\Cashier\Concerns\ManagesPaymentMethods as BaseManagesPaymentMethods;

trait ManagesPaymentMethods
{
    use BaseManagesPaymentMethods;

    /**
     * Determines if the customer currently has a default payment method.
     *
     * @return bool
     */
    public function hasDefaultPaymentMethod()
    {
        return (bool) $this->getPmType();
    }

    /**
     * Get a collection of the customer's payment methods of the given type.
     *
     * @param  string  $type
     * @param  array  $parameters
     * @return \Illuminate\Support\Collection|\Laravel\Cashier\PaymentMethod[]
     */
    public function paymentMethods($type = 'card', $parameters = [])
    {
        if (! $this->hasStripeId()) {
            return new Collection();
        }

        $parameters = array_merge(['limit' => 24], $parameters);

        // "type" is temporarily required by Stripe...
        $paymentMethods = $this->stripe()->paymentMethods->all(
            ['customer' => $this->stripeId(), 'type' => $type] + $parameters
        );

        return Collection::make($paymentMethods->data)->map(function ($paymentMethod) {
            return new PaymentMethod($this, $paymentMethod);
        });
    }

    /**
     * Update customer's default payment method.
     *
     * @param  \Stripe\PaymentMethod|string  $paymentMethod
     * @return \Laravel\Cashier\PaymentMethod
     */
    public function updateDefaultPaymentMethod($paymentMethod)
    {
        $this->assertCustomerExists();

        $customer = $this->asStripeCustomer();

        $stripePaymentMethod = $this->resolveStripePaymentMethod($paymentMethod);

        // If the customer already has the payment method as their default, we can bail out
        // of the call now. We don't need to keep adding the same payment method to this
        // model's account every single time we go through this specific process call.
        if ($stripePaymentMethod->id === $customer->invoice_settings->default_payment_method) {
            return;
        }

        $paymentMethod = $this->addPaymentMethod($stripePaymentMethod);

        $this->updateStripeCustomer([
            'invoice_settings' => ['default_payment_method' => $paymentMethod->id],
        ]);

        // Next we will get the default payment method for this user so we can update the
        // payment method details on the record in the database. This will allow us to
        // show that information on the front-end when updating the payment methods.

        $this->getRepository()->fillPaymentMethodDetails($this, $paymentMethod);

        return $paymentMethod;
    }

    /**
     * Synchronises the customer's default payment method from Stripe back into the database.
     *
     * @return \Laravel\Cashier\Concerns\ManagesPaymentMethods
     */
    public function updateDefaultPaymentMethodFromStripe()
    {
        $defaultPaymentMethod = $this->defaultPaymentMethod();

        if ($defaultPaymentMethod && $defaultPaymentMethod instanceof PaymentMethod) {
            $this->fillPaymentMethodDetails(
                $defaultPaymentMethod->asStripePaymentMethod()
            );
        } else {
            $this->getRepository()->forceFillPaymentMethodDetails($this, [
                'pm_type' => null,
                'pm_last_four' => null,
            ]);
        }

        return $this;
    }


    /**
     * Add a payment method to the customer.
     *
     * @param  \Stripe\PaymentMethod|string  $paymentMethod
     * @return \Laravel\Cashier\PaymentMethod
     */
    public function addPaymentMethod($paymentMethod)
    {
        $this->assertCustomerExists();

        $stripePaymentMethod = $this->resolveStripePaymentMethod($paymentMethod);

        if ($stripePaymentMethod->customer !== $this->stripeId()) {
            $stripePaymentMethod = $stripePaymentMethod->attach(
                ['customer' => $this->stripeId()]
            );
        }

        return new PaymentMethod($this, $stripePaymentMethod);
    }
}