<?php

namespace BulletDigitalSolutions\DoctrineCashier\Concerns;

use Laravel\Cashier\Concerns\ManagesCustomer as BaseManagesCustomer;
use Laravel\Cashier\Exceptions\CustomerAlreadyCreated;

trait ManagesCustomer
{
    use BaseManagesCustomer;

    /**
     * Retrieve the Stripe customer ID.
     *
     * @return string|null
     */
    public function stripeId()
    {
        return $this->getStripeId();
    }

    /**
     * Determine if the customer has a Stripe customer ID.
     *
     * @return bool
     */
    public function hasStripeId()
    {
        return ! is_null($this->getStripeId());
    }

//    TODO: is this required?
    /**
     * Create a Stripe customer for the given model.
     *
     * @param  array  $options
     * @return \Stripe\Customer
     *
     * @throws CustomerAlreadyCreated
     */
    public function createAsStripeCustomer(array $options = [])
    {
        if ($this->hasStripeId()) {
            throw CustomerAlreadyCreated::exists($this);
        }

        if (! array_key_exists('name', $options) && $name = $this->stripeName()) {
            $options['name'] = $name;
        }

        if (! array_key_exists('email', $options) && $email = $this->stripeEmail()) {
            $options['email'] = $email;
        }

        if (! array_key_exists('phone', $options) && $phone = $this->stripePhone()) {
            $options['phone'] = $phone;
        }

        if (! array_key_exists('address', $options) && $address = $this->stripeAddress()) {
            $options['address'] = $address;
        }

        // Here we will create the customer instance on Stripe and store the ID of the
        // user from Stripe. This ID will correspond with the Stripe user instances
        // and allow us to retrieve users from Stripe later when we need to work.
        $customer = $this->stripe()->customers->create($options);

        $this->getRepository()->setStripeId($this, $customer->id);

        return $customer;
    }

    /**
     * Get the name that should be synced to Stripe.
     *
     * @return string|null
     */
    public function stripeName()
    {
        return $this->getName();
    }

    /**
     * Get the email address that should be synced to Stripe.
     *
     * @return string|null
     */
    public function stripeEmail()
    {
        return $this->getEmail();
    }

    /**
     * Get the phone number that should be synced to Stripe.
     *
     * @return string|null
     */
    public function stripePhone()
    {
        return $this->phone;
    }
}
