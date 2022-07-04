<?php

namespace BulletDigitalSolutions\DoctrineCashier\Entities;

use BulletDigitalSolutions\DoctrineCashier\Traits\Entities\Timestampable;
use Laravel\Cashier\Subscription as BaseSubscription;
use Doctrine\ORM\Mapping as ORM;

class Subscription extends BaseSubscription
{
    use Timestampable;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    protected $stripeId;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $stripeStatus;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $stripePrice;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $quantity;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $trialEndsAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $endsAt;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getStripeId()
    {
        return $this->stripeId;
    }

    /**
     * @param mixed $stripeId
     */
    public function setStripeId($stripeId): void
    {
        $this->stripeId = $stripeId;
    }

    /**
     * @return mixed
     */
    public function getStripeStatus()
    {
        return $this->stripeStatus;
    }

    /**
     * @param mixed $stripeStatus
     */
    public function setStripeStatus($stripeStatus): void
    {
        $this->stripeStatus = $stripeStatus;
    }

    /**
     * @return mixed
     */
    public function getStripePrice()
    {
        return $this->stripePrice;
    }

    /**
     * @param mixed $stripePrice
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
     * @param mixed $quantity
     */
    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return mixed
     */
    public function getTrialEndsAt()
    {
        return $this->trialEndsAt;
    }

    /**
     * @param mixed $trialEndsAt
     */
    public function setTrialEndsAt($trialEndsAt): void
    {
        $this->trialEndsAt = $trialEndsAt;
    }

    /**
     * @return mixed
     */
    public function getEndsAt()
    {
        return $this->endsAt;
    }

    /**
     * @param mixed $endsAt
     */
    public function setEndsAt($endsAt): void
    {
        $this->endsAt = $endsAt;
    }

//    /**
//     * Get the model related to the subscription.
//     *
//     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
//     */
//    public function owner()
//    {
//        $model = Cashier::$customerModel;
//
//        return $this->belongsTo($model, (new $model)->getForeignKey());
//    }
//
//    /**
//     * Get the subscription items related to the subscription.
//     *
//     * @return \Illuminate\Database\Eloquent\Relations\HasMany
//     */
//    public function items()
//    {
//        return $this->hasMany(Cashier::$subscriptionItemModel);
//    }
//
//    /**
//     * Determine if the subscription has multiple prices.
//     *
//     * @return bool
//     */
//    public function hasMultiplePrices()
//    {
//        return is_null($this->stripe_price);
//    }
//
//    /**
//     * Determine if the subscription has a specific product.
//     *
//     * @param  string  $product
//     * @return bool
//     */
//    public function hasProduct($product)
//    {
//        return $this->items->contains(function (SubscriptionItem $item) use ($product) {
//            return $item->stripe_product === $product;
//        });
//    }
//
//    /**
//     * Determine if the subscription has a specific price.
//     *
//     * @param  string  $price
//     * @return bool
//     */
//    public function hasPrice($price)
//    {
//        if ($this->hasMultiplePrices()) {
//            return $this->items->contains(function (SubscriptionItem $item) use ($price) {
//                return $item->stripe_price === $price;
//            });
//        }
//
//        return $this->stripe_price === $price;
//    }
//
//    /**
//     * Get the subscription item for the given price.
//     *
//     * @param  string  $price
//     * @return \Laravel\Cashier\SubscriptionItem
//     *
//     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
//     */
//    public function findItemOrFail($price)
//    {
//        return $this->items()->where('stripe_price', $price)->firstOrFail();
//    }
//
//    /**
//     * Determine if the subscription is incomplete.
//     *
//     * @return bool
//     */
//    public function incomplete()
//    {
//        return $this->getStripeStatus() === StripeSubscription::STATUS_INCOMPLETE;
//    }
//
//    /**
//     * Determine if the subscription is past due.
//     *
//     * @return bool
//     */
//    public function pastDue()
//    {
//        return $this->getStripeStatus() === StripeSubscription::STATUS_PAST_DUE;
//    }
//
//    /**
//     * Determine if the subscription is active.
//     *
//     * @return bool
//     */
//    public function active()
//    {
//        return ! $this->ended() &&
//            $this->stripe_status !== StripeSubscription::STATUS_INCOMPLETE &&
//            $this->stripe_status !== StripeSubscription::STATUS_INCOMPLETE_EXPIRED &&
//            (! Cashier::$deactivatePastDue || $this->getStripeStatus() !== StripeSubscription::STATUS_PAST_DUE) &&
//            $this->stripe_status !== StripeSubscription::STATUS_UNPAID;
//    }
//
//    /**
//     * Sync the Stripe status of the subscription.
//     *
//     * @return void
//     */
//    public function syncStripeStatus()
//    {
////        TODO: Implement syncStripeStatus() method.
//        $subscription = $this->asStripeSubscription();
//
//        $this->stripe_status = $subscription->status;
//
//        $this->save();
//    }
//
//    /**
//     * Determine if the subscription is no longer active.
//     *
//     * @return bool
//     */
//    public function canceled()
//    {
//        return ! is_null($this->getEndsAt());
//    }
//
//    /**
//     * Determine if the subscription is within its trial period.
//     *
//     * @return bool
//     */
//    public function onTrial()
//    {
//        return $this->getTrialEndsAt() && $this->getTrialEndsAt()->isFuture();
//    }
//
//    /**
//     * Determine if the subscription is within its grace period after cancellation.
//     *
//     * @return bool
//     */
//    public function onGracePeriod()
//    {
//        return $this->getEndsAt() && $this->getEndsAt()->isFuture();
//    }
//
//    /**
//     * Increment the quantity of the subscription.
//     *
//     * @param  int  $count
//     * @param  string|null  $price
//     * @return \Laravel\Cashier\Subscription
//     *
//     * @throws \Laravel\Cashier\Exceptions\SubscriptionUpdateFailure
//     */
//    public function incrementQuantity($count = 1, $price = null)
//    {
////        TODO: Implement incrementQuantity() method.
//        $this->guardAgainstIncomplete();
//
//        if ($price) {
//            $this->findItemOrFail($price)->setProrationBehavior($this->prorationBehavior)->incrementQuantity($count);
//
//            return $this->refresh();
//        }
//
//        $this->guardAgainstMultiplePrices();
//
//        return $this->updateQuantity($this->quantity + $count, $price);
//    }
//
//    /**
//     * Report usage for a metered product.
//     *
//     * @param  int  $quantity
//     * @param  \DateTimeInterface|int|null  $timestamp
//     * @param  string|null  $price
//     * @return \Stripe\UsageRecord
//     */
//    public function reportUsage($quantity = 1, $timestamp = null, $price = null)
//    {
//        if (! $price) {
//            $this->guardAgainstMultiplePrices();
//        }
//
//        return $this->findItemOrFail($price ?? $this->getStripePrice())->reportUsage($quantity, $timestamp);
//    }
//
//    /**
//     * Get the usage records for a metered product.
//     *
//     * @param  array  $options
//     * @param  string|null  $price
//     * @return \Illuminate\Support\Collection
//     */
//    public function usageRecords(array $options = [], $price = null)
//    {
//        if (! $price) {
//            $this->guardAgainstMultiplePrices();
//        }
//
//        return $this->findItemOrFail($price ?? $this->getStripePrice())->usageRecords($options);
//    }
//
//
//    /**
//     * Force the subscription's trial to end immediately.
//     *
//     * @return $this
//     */
//    public function endTrial()
//    {
////        TODO
//        if (is_null($this->trial_ends_at)) {
//            return $this;
//        }
//
//        $this->updateStripeSubscription([
//            'trial_end' => 'now',
//            'proration_behavior' => $this->prorateBehavior(),
//        ]);
//
//        $this->trial_ends_at = null;
//
//        $this->save();
//
//        return $this;
//    }
}
