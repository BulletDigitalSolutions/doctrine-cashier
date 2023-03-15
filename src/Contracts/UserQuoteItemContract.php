<?php

namespace BulletDigitalSolutions\DoctrineCashier\Contracts;

interface UserQuoteItemContract
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

    /**
     * @return mixed
     */
    public function getQuote();

    /**
     * @param  mixed  $user
     */
    public function setQuote($user): void;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    public function getRepository();
}
