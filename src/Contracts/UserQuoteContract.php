<?php

namespace BulletDigitalSolutions\DoctrineCashier\Contracts;

interface UserQuoteContract
{
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
    public function getStripePlanId();

    /**
     * @param  mixed  $stripePlanId
     */
    public function setStripePlanId($stripePlanId): void;

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
    public function getAcceptedAt();

    /**
     * @param  mixed  $acceptedAt
     */
    public function setAcceptedAt($acceptedAt): void;

    /**
     * @return mixed
     */
    public function getUser();

    /**
     * @param  mixed  $user
     */
    public function setUser($user): void;

    /**
     * @return mixed
     */
    public function getRepository();
}
