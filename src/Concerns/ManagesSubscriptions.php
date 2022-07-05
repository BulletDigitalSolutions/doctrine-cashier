<?php

namespace BulletDigitalSolutions\DoctrineCashier\Concerns;

use Doctrine\Common\Collections\Criteria;
use Laravel\Cashier\Concerns\ManagesSubscriptions as BaseManagesSubscriptions;

trait ManagesSubscriptions
{
    use BaseManagesSubscriptions;

    /**
     * @param  string  $name
     * @return \Laravel\Cashier\Subscription|null
     */
    public function subscription($name = 'default')
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('name', $name));

        return $this->getSubscriptions()->matching($criteria)->first();
    }
}
