<?php

namespace BulletDigitalSolutions\DoctrineCashier\Concerns;

use BulletDigitalSolutions\DoctrineCashier\Cashier\CustomerBalanceTransaction;
use Illuminate\Support\Collection;
use Laravel\Cashier\Concerns\ManagesCustomer as BaseManagesCustomer;
use Laravel\Cashier\Exceptions\CustomerAlreadyCreated;
use Stripe\Exception\InvalidRequestException as StripeInvalidRequestException;

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

    /**
     * Create a Stripe customer for the given model.
     *
     * @param  array  $options
     * @return \Stripe\Customer
     *
     * @throws \Laravel\Cashier\Exceptions\CustomerAlreadyCreated
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

    /**
     * Get the address that should be synced to Stripe.
     *
     * @return array|null
     */
    public function stripeAddress()
    {
//        TODO
        // return [
        //     'city' => 'Little Rock',
        //     'country' => 'US',
        //     'line1' => '1 Main St.',
        //     'line2' => 'Apartment 5',
        //     'postal_code' => '72201',
        //     'state' => 'Arkansas',
        // ];
    }

    /**
     * Update the underlying Stripe customer information for the model.
     *
     * @param  array  $options
     * @return \Stripe\Customer
     */
    public function updateStripeCustomer(array $options = [])
    {
        return $this->stripe()->customers->update(
            $this->getStripeId(), $options
        );
    }

    /**
     * Get the Stripe customer for the model.
     *
     * @param  array  $expand
     * @return \Stripe\Customer
     */
    public function asStripeCustomer(array $expand = [])
    {
        $this->assertCustomerExists();

        return $this->stripe()->customers->retrieve(
            $this->getStripeId(), ['expand' => $expand]
        );
    }

    /**
     * Apply a new amount to the customer's balance.
     *
     * @param  int  $amount
     * @param  string|null  $description
     * @param  array  $options
     * @return CustomerBalanceTransaction
     */
    public function applyBalance($amount, $description = null, array $options = [])
    {
        $this->assertCustomerExists();

        $transaction = $this->stripe()
            ->customers
            ->createBalanceTransaction($this->getStripeId(), array_filter(array_merge([
                'amount' => $amount,
                'currency' => $this->preferredCurrency(),
                'description' => $description,
            ], $options)));

        return new CustomerBalanceTransaction($this, $transaction);
    }

    /**
     * Get a collection of the customer's TaxID's.
     *
     * @return \Illuminate\Support\Collection|\Stripe\TaxId[]
     */
    public function taxIds(array $options = [])
    {
        $this->assertCustomerExists();

        return new Collection(
            $this->stripe()->customers->allTaxIds($this->getStripeId(), $options)->data
        );
    }

    /**
     * Find a TaxID by ID.
     *
     * @return \Stripe\TaxId|null
     */
    public function findTaxId($id)
    {
        $this->assertCustomerExists();

        try {
            return $this->stripe()->customers->retrieveTaxId(
                $this->getStripeId(), $id, []
            );
        } catch (StripeInvalidRequestException $exception) {
            //
        }
    }

    /**
     * Create a TaxID for the customer.
     *
     * @param  string  $type
     * @param  string  $value
     * @return \Stripe\TaxId
     */
    public function createTaxId($type, $value)
    {
        $this->assertCustomerExists();

        return $this->stripe()->customers->createTaxId($this->getStripeId(), [
            'type' => $type,
            'value' => $value,
        ]);
    }

    /**
     * Delete a TaxID for the customer.
     *
     * @param  string  $id
     * @return void
     */
    public function deleteTaxId($id)
    {
        $this->assertCustomerExists();

        try {
            $this->stripe()->customers->deleteTaxId($this->stripeId(), $id);
        } catch (StripeInvalidRequestException $exception) {
            //
        }
    }
}
