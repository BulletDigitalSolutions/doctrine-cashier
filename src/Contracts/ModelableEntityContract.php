<?php

namespace BulletDigitalSolutions\DoctrineCashier\Contracts;

interface ModelableEntityContract
{
    /**
     * @return mixed
     */
    public function getRepository();
}
