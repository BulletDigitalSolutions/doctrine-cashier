<?php

namespace BulletDigitalSolutions\DoctrineCashier\Concerns;

use Illuminate\Support\Collection;
use Laravel\Cashier\Concerns\ManagesInvoices as BaseManagesInvoices;
use Laravel\Cashier\Invoice;
use Laravel\Cashier\Payment;
use LogicException;
use Stripe\Exception\CardException as StripeCardException;
use Stripe\Exception\InvalidRequestException as StripeInvalidRequestException;
use Stripe\Invoice as StripeInvoice;

trait ManagesInvoices
{
    use BaseManagesInvoices;

}
