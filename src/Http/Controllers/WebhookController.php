<?php

namespace BulletDigitalSolutions\DoctrineCashier\Http\Controllers;

use BulletDigitalSolutions\DoctrineCashier\DoctrineCashier;
use Laravel\Cashier\Http\Controllers\WebhookController as BaseWebhookController;

class WebhookController extends BaseWebhookController
{
    /**
     * Get the customer instance by Stripe ID.
     *
     * @param  string|null  $stripeId
     * @return \Laravel\Cashier\Billable|null
     */
    protected function getUserByStripeId($stripeId)
    {
        return DoctrineCashier::findBillable($stripeId);
    }
}
