<?php

namespace BulletDigitalSolutions\DoctrineCashier\Concerns;

//TODO - This is not required anymore

use Doctrine\Common\Collections\Criteria;
use Laravel\Cashier\Concerns\ManagesSubscriptions as BaseManagesSubscriptions;

trait ManagesSubscriptions
{
    use BaseManagesSubscriptions;

    /**
     * Determine if the Stripe model is on a "generic" trial at the model level.
     *
     * @return bool
     */
    public function onGenericTrial()
    {
        return $this->getTrialEndsAt() && $this->getTrialEndsAt()->isFuture();
    }

    /**
     * Get the ending date of the trial.
     *
     * @param  string  $name
     * @return \Illuminate\Support\Carbon|null
     */
    public function trialEndsAt($name = 'default')
    {
        if (func_num_args() === 0 && $this->onGenericTrial()) {
            return $this->getTrialEndsAt();
        }

        if ($subscription = $this->subscription($name)) {
            return $subscription->getTrialEndsAt();
        }

        return $this->getTrialEndsAt();
    }

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