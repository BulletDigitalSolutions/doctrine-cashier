<?php

namespace BulletDigitalSolutions\DoctrineCashier\Entities;

use BulletDigitalSolutions\DoctrineCashier\Traits\Entities\Timestampable;
use BulletDigitalSolutions\DoctrineEloquent\Traits\Entities\Modelable;
use Doctrine\ORM\Mapping as ORM;

class UserQuoteItem
{
    use Timestampable, Modelable;

    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    protected $stripeId;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $stripeProduct;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $stripePrice;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $quantity;

    /**
     * @return mixed
     */
    public function getStripeId()
    {
        return $this->stripeId;
    }

    /**
     * @param  mixed  $stripeId
     */
    public function setStripeId($stripeId): void
    {
        $this->stripeId = $stripeId;
    }

    /**
     * @return mixed
     */
    public function getStripeProduct()
    {
        return $this->stripeProduct;
    }

    /**
     * @param  mixed  $stripeProduct
     */
    public function setStripeProduct($stripeProduct): void
    {
        $this->stripeProduct = $stripeProduct;
    }

    /**
     * @return mixed
     */
    public function getStripePrice()
    {
        return $this->stripePrice;
    }

    /**
     * @param  mixed  $stripePrice
     */
    public function setStripePrice($stripePrice): void
    {
        $this->stripePrice = $stripePrice;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param  mixed  $quantity
     */
    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
    }
}
