<?php

namespace BulletDigitalSolutions\DoctrineCashier\Contracts;

interface UserSubscriptionItemContract
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
    public function getStripeProduct();

    /**
     * @param  mixed  $stripeProduct
     */
    public function setStripeProduct($stripeProduct): void;

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
}
