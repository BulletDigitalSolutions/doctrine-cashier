<?php

//TODO - This is not required anymore

namespace BulletDigitalSolutions\DoctrineCashier\Concerns;

use Illuminate\Support\Collection;
use Laravel\Cashier\Concerns\ManagesInvoices as BaseManagesInvoices;
use Laravel\Cashier\Invoice;
use Laravel\Cashier\Payment;
use Stripe\Exception\CardException as StripeCardException;
use Stripe\Exception\InvalidRequestException as StripeInvalidRequestException;
use Stripe\Invoice as StripeInvoice;

trait ManagesInvoices
{
    use BaseManagesInvoices;

    /**
     * Add an invoice item to the customer's upcoming invoice.
     *
     * @param  string  $description
     * @param  int  $amount
     * @param  array  $options
     * @return \Stripe\InvoiceItem
     */
    public function tab($description, $amount, array $options = [])
    {
        if ($this->isAutomaticTaxEnabled() && ! array_key_exists('price_data', $options)) {
            throw new LogicException('When using automatic tax calculation, you need to define the "price_data" in the options.');
        }

        $this->assertCustomerExists();

        $options = array_merge([
            'customer' => $this->stripeId(),
            'currency' => $this->preferredCurrency(),
            'description' => $description,
        ], $options);

        if (array_key_exists('price_data', $options)) {
            $options['price_data'] = array_merge([
                'unit_amount' => $amount,
                'currency' => $this->preferredCurrency(),
            ], $options['price_data']);
        } elseif (array_key_exists('quantity', $options)) {
            $options['unit_amount'] = $options['unit_amount'] ?? $amount;
        } else {
            $options['amount'] = $amount;
        }

        return $this->stripe()->invoiceItems->create($options);
    }

    /**
     * Add an invoice item for a specific Price ID to the customer's upcoming invoice.
     *
     * @param  string  $price
     * @param  int  $quantity
     * @param  array  $options
     * @return \Stripe\InvoiceItem
     */
    public function tabPrice($price, $quantity = 1, array $options = [])
    {
        $this->assertCustomerExists();

        $options = array_merge([
            'customer' => $this->stripeId(),
            'price' => $price,
            'quantity' => $quantity,
        ], $options);

        return $this->stripe()->invoiceItems->create($options);
    }

    /**
     * Invoice the customer outside of the regular billing cycle.
     *
     * @param  array  $options
     * @return \Laravel\Cashier\Invoice|bool
     *
     * @throws \Laravel\Cashier\Exceptions\IncompletePayment
     */
    public function invoice(array $options = [])
    {
        $this->assertCustomerExists();

        $parameters = array_merge([
            'automatic_tax' => $this->automaticTaxPayload(),
            'customer' => $this->stripeId(),
        ], $options);

        try {
            /** @var \Stripe\Invoice $invoice */
            $stripeInvoice = $this->stripe()->invoices->create($parameters);

            if ($stripeInvoice->collection_method === StripeInvoice::COLLECTION_METHOD_CHARGE_AUTOMATICALLY) {
                $stripeInvoice = $stripeInvoice->pay();
            } else {
                $stripeInvoice = $stripeInvoice->sendInvoice();
            }

            return new Invoice($this, $stripeInvoice);
        } catch (StripeInvalidRequestException $exception) {
            return false;
        } catch (StripeCardException $exception) {
            $payment = new Payment(
                $this->stripe()->paymentIntents->retrieve(
                    $stripeInvoice->refresh()->payment_intent,
                    ['expand' => ['invoice.subscription']]
                )
            );

            $payment->validate();
        }
    }

    /**
     * Get the customer's upcoming invoice.
     *
     * @param  array  $options
     * @return \Laravel\Cashier\Invoice|null
     */
    public function upcomingInvoice(array $options = [])
    {
        if (! $this->hasStripeId()) {
            return;
        }

        $parameters = array_merge([
            'automatic_tax' => $this->automaticTaxPayload(),
            'customer' => $this->stripeId(),
        ], $options);

        try {
            $stripeInvoice = $this->stripe()->invoices->upcoming($parameters);

            return new Invoice($this, $stripeInvoice, $parameters);
        } catch (StripeInvalidRequestException $exception) {
            //
        }
    }

    /**
     * Get a collection of the customer's invoices.
     *
     * @param  bool  $includePending
     * @param  array  $parameters
     * @return \Illuminate\Support\Collection|\Laravel\Cashier\Invoice[]
     */
    public function invoices($includePending = false, $parameters = [])
    {
        if (! $this->hasStripeId()) {
            return new Collection();
        }

        $invoices = [];

        $parameters = array_merge(['limit' => 24], $parameters);

        $stripeInvoices = $this->stripe()->invoices->all(
            ['customer' => $this->stripeId()] + $parameters
        );

        // Here we will loop through the Stripe invoices and create our own custom Invoice
        // instances that have more helper methods and are generally more convenient to
        // work with than the plain Stripe objects are. Then, we'll return the array.
        if (! is_null($stripeInvoices)) {
            foreach ($stripeInvoices->data as $invoice) {
                if ($invoice->paid || $includePending) {
                    $invoices[] = new Invoice($this, $invoice);
                }
            }
        }

        return new Collection($invoices);
    }
}