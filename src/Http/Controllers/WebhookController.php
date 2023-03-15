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

//    TODO: Make this work via doctrine-eloquent
    /**
     * Handle a canceled customer from a Stripe subscription.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleCustomerSubscriptionDeleted(array $payload)
    {
        if ($user = $this->getUserByStripeId($payload['data']['object']['customer'])) {
            $subscriptions = $user->subscriptions()->get()->filter(function ($subscription) use ($payload) {
                return $subscription->stripe_id === $payload['data']['object']['id'];
            });

            foreach ($subscriptions as $subscription) {
                $subscription->markAsCanceled();
            }
        }

        return $this->successMethod();
    }
}
