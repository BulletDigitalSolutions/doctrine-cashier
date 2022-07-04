<?php

namespace BulletDigitalSolutions\DoctrineCashier\Contracts;

use BulletDigitalSolutions\DoctrineCashier\Traits\Entities\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Laravel\Cashier\Billable;

interface BillableEntityContract
{

    /**
     * @return mixed
     */
    public function getStripeId();

    /**
     * @param mixed $stripeId
     */
    public function setStripeId($stripeId): void;

    /**
     * @return mixed
     */
    public function getPmType();

    /**
     * @param mixed $pmType
     */
    public function setPmType($pmType): void;

    /**
     * @return mixed
     */
    public function getPmLastFour();

    /**
     * @param mixed $pmLastFour
     */
    public function setPmLastFour($pmLastFour): void;

    /**
     * @return mixed
     */
    public function getTrialEndsAt();

    /**
     * @param mixed $trialEndsAt
     */
    public function setTrialEndsAt($trialEndsAt): void;

    /**
     * @return mixed
     */
    public function getIdAttribute();

}
