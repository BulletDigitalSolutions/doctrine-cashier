<?php

//TODO - This is not required anymore

namespace BulletDigitalSolutions\DoctrineCashier\Traits;

use BulletDigitalSolutions\DoctrineEloquent\Traits\ModelableRepository;
use Illuminate\Support\Arr;

trait BillableRepository
{
    use ModelableRepository;

    /**
     * @param $entity
     * @param $stripeId
     * @return void
     */
    public function setStripeId($entity, $stripeId)
    {
        $entity->setStripeId($stripeId);

        // app em
        $this->_em->persist($entity);
        $this->_em->flush();

        return $entity;
    }

    /**
     * @param  \Laravel\Cashier\PaymentMethod|\Stripe\PaymentMethod|null  $paymentMethod
     * @return $this
     */
    public function fillPaymentMethodDetails($entity, $paymentMethod)
    {
        if ($paymentMethod->type === 'card') {
            $entity->setPmType($paymentMethod->card->brand);
            $entity->setPmLastFour($paymentMethod->card->last4);
        } else {
            $entity->setPmType($type = $paymentMethod->type);
            $entity->setPmLastFour(optional($paymentMethod)->$type->last4);
        }

        // app em
        $this->_em->persist($entity);
        $this->_em->flush();

        return $entity;
    }

    /**
     * @param $entity
     * @param $attributes
     * @return void
     */
    public function forceFillPaymentMethodDetails($entity, $attributes = [])
    {
        if (Arr::get($attributes, 'pm_type')) {
            $entity->setPmType($attributes['pm_type']);
        }

        if (Arr::get($attributes, 'pm_last_four')) {
            $entity->setPmLastFour($attributes['pm_last_four']);
        }

        // app em
        $this->_em->persist($entity);
        $this->_em->flush();

        return $entity;
    }
}
