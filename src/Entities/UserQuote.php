<?php

namespace BulletDigitalSolutions\DoctrineCashier\Entities;

use BulletDigitalSolutions\DoctrineCashier\Builders\QuoteBuilder;
use BulletDigitalSolutions\DoctrineCashier\Traits\Entities\Timestampable;
use BulletDigitalSolutions\DoctrineEloquent\Traits\Entities\Modelable;
use Doctrine\ORM\Mapping as ORM;

class UserQuote
{
    use Timestampable, Modelable;

    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    protected $stripeId;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $finalisedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $acceptedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $cancelledAt;

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
    public function getStripePlanId()
    {
        return $this->stripePlanId;
    }

    /**
     * @param  mixed  $stripePlanId
     */
    public function setStripePlanId($stripePlanId): void
    {
        $this->stripePlanId = $stripePlanId;
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

    /**
     * @return mixed
     */
    public function getFinalisedAt()
    {
        return $this->finalisedAt;
    }

    /**
     * @param  mixed  $finalisedAt
     */
    public function setFinalisedAt($finalisedAt): void
    {
        $this->finalisedAt = $finalisedAt;
    }

    /**
     * @return mixed
     */
    public function getAcceptedAt()
    {
        return $this->acceptedAt;
    }

    /**
     * @param  mixed  $acceptedAt
     */
    public function setAcceptedAt($acceptedAt): void
    {
        $this->acceptedAt = $acceptedAt;
    }

    /**
     * @return mixed
     */
    public function getCancelledAt()
    {
        return $this->cancelledAt;
    }

    /**
     * @param  mixed  $cancelledAt
     */
    public function setCancelledAt($cancelledAt): void
    {
        $this->cancelledAt = $cancelledAt;
    }

    /**
     * @return mixed
     */
    public function owner()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function retrieve()
    {
        return $this->owner()->stripe()->quotes->retrieve($this->getStripeId());
    }

    /**
     * @return mixed
     */
    public function price()
    {
        return $this->retrieve()->amount_total;
    }

    /**
     * @return bool
     */
    public function cancel()
    {
        try {
            $this->owner()->stripe()->quotes->cancel($this->getStripeId());
            $this->setCancelledAt(new \DateTime());
            $this->save();

            return true;
        } catch (\Exception $e) {
            return false;
            // TODO
        }
    }

    public function finalise()
    {
        try {
            $this->owner()->stripe()->quotes->finalizeQuote($this->getStripeId());
            $this->setFinalisedAt(new \DateTime());
            $this->save();

            return true;
        } catch (\Exception $e) {
            return false;
            // TODO
        }
    }

    /**
     * @return bool
     */
    public function accept()
    {
        if (! $this->getFinalisedAt()) {
            $this->finalise();
        }

        $sub = $this->owner()->stripe()->quotes->accept($this->getStripeId());
        dd($sub->subscription);
        try {
            $this->setAcceptedAt(new \DateTime());
            $this->save();

            return true;
        } catch (\Exception $e) {
            return false;
            // TODO
        }
    }

    public function update()
    {
        return new QuoteBuilder($this->owner(), null, null, );
    }
}
