<?php

namespace BulletDigitalSolutions\DoctrineCashier\Contracts;

interface SubscriptionContract
{
    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param  mixed  $name
     */
    public function setName($name): void;

    /**
     * @return mixed
     */
    public function getStripeId();

    /**
     * @param  mixed  $stripeId
     */
    public function setStripeId($stripeId): void;

    /**
     * @return mixed
     */
    public function getStripeStatus();

    /**
     * @param  mixed  $stripeStatus
     */
    public function setStripeStatus($stripeStatus): void;

    /**
     * @return mixed
     */
    public function getStripePrice();

    /**
     * @param  mixed  $stripePrice
     */
    public function setStripePrice($stripePrice): void;

    /**
     * @return mixed
     */
    public function getQuantity();

    /**
     * @param  mixed  $quantity
     */
    public function setQuantity($quantity): void;

    /**
     * @return mixed
     */
    public function getTrialEndsAt();

    /**
     * @param  mixed  $trialEndsAt
     */
    public function setTrialEndsAt($trialEndsAt): void;

    /**
     * @return mixed
     */
    public function getEndsAt();

    /**
     * @param  mixed  $endsAt
     */
    public function setEndsAt($endsAt): void;
}
