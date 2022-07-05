<?php

//TODO - This is not required anymore

namespace BulletDigitalSolutions\DoctrineCashier\Traits;

trait ModelableRepository
{
    /**
     * @param $entity
     * @param $stripeId
     * @return void
     */
    public function saveChanges($entity)
    {
        $this->_em->persist($entity);
        $this->_em->flush();

        return $entity;
    }
}
