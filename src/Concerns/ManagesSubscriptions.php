<?php

namespace BulletDigitalSolutions\DoctrineCashier\Concerns;

use BulletDigitalSolutions\DoctrineCashier\DoctrineCashier;
use BulletDigitalSolutions\DoctrineEloquent\Relationships\HasMany;
use Laravel\Cashier\Concerns\ManagesSubscriptions as BaseManagesSubscriptions;

trait ManagesSubscriptions
{
    use BaseManagesSubscriptions;

    /**
     * Get a subscription instance by name.
     *
     * @param  string  $name
     * @return \Laravel\Cashier\Subscription|null
     */
    public function subscription($name = 'default')
    {
        return $this->subscriptions()->where('name', $name)->first();
    }

    /**
     * @return HasMany
     */
    public function subscriptions()
    {
        $hasMany = new HasMany($this, DoctrineCashier::$subscriptionModel, null, null, 'getSubscriptions');

        return $hasMany->orderBy('created_at', 'desc');
        // return $this->hasMany(Cashier::$subscriptionModel, $this->getForeignKey())->orderBy('created_at', 'desc');
    }

    //TODO: Public final function
}
