<?php

namespace BulletDigitalSolutions\DoctrineCashier\Entities;

use Doctrine\ORM\Mapping as ORM;
use Laravel\Cashier\Cashier;

class Plan
{
    /**
     * @ORM\Column(name="stripe_plan", type="string", length=50, nullable=true)
     */
    protected $stripePlan;

    /**
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    protected $name = '';

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  mixed  $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getStripePlan()
    {
        return $this->stripePlan;
    }

    /**
     * @param  mixed  $stripePlan
     */
    public function setStripePlan($stripePlan): void
    {
        $this->stripePlan = $stripePlan;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param  string  $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \Stripe\Product
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function retrieve()
    {
        if (! $this->getStripePlan()) {
            return null;
        }
        // TODO: Try/catch this
        return $this->stripe()->products->retrieve($this->getStripePlan());
    }

    public function defaultPrice()
    {
        if (! $this->getStripePlan()) {
            return null;
        }

        $price = $this->retrieve()->price;

        dd($this->stripe()->prices->retrieve($price));
    }

    /**
     * @return \Stripe\SearchResult|null
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function prices()
    {
        if (! $this->getStripePlan()) {
            return null;
        }

        return $this->stripe()->prices->search([
            'query' => sprintf('product:"%s"', $this->getStripePlan()),
        ]);
    }

    /**
     * Get the Stripe SDK client.
     *
     * @param  array  $options
     * @return \Stripe\StripeClient
     */
    public static function stripe(array $options = [])
    {
        return Cashier::stripe($options);
    }
}
