<?php

namespace BulletDigitalSolutions\DoctrineCashier\Entities;

use BulletDigitalSolutions\DoctrineCashier\Traits\Entities\Timestampable;
use Laravel\Cashier\SubscriptionItem as BaseSubscriptionItem;
use Doctrine\ORM\Mapping as ORM;

class SubscriptionItem extends BaseSubscriptionItem
{
    use Timestampable;

    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    protected $stripeId;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $stripeProduct;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $stripePrice;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $quantity;

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
    public function getStripeProduct()
    {
        return $this->stripeProduct;
    }

    /**
     * @param mixed $stripeProduct
     */
    public function setStripeProduct($stripeProduct): void
    {
        $this->stripeProduct = $stripeProduct;
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

//    /**
//     * Update the quantity of the subscription item.
//     *
//     * @param  int  $quantity
//     * @return \Laravel\Cashier\SubscriptionItem
//     *
//     * @throws \Laravel\Cashier\Exceptions\SubscriptionUpdateFailure
//     */
//    public function updateQuantity($quantity)
//    {
//        //        TODO
//
//        $this->subscription->guardAgainstIncomplete();
//
//        $stripeSubscriptionItem = $this->updateStripeSubscriptionItem([
//            'payment_behavior' => $this->paymentBehavior(),
//            'proration_behavior' => $this->prorateBehavior(),
//            'quantity' => $quantity,
//        ]);
//
//        $this->fill([
//            'quantity' => $stripeSubscriptionItem->quantity,
//        ])->save();
//
//        if ($this->subscription->hasSinglePrice()) {
//            $stripeSubscription = $this->subscription->asStripeSubscription();
//
//            $this->subscription->fill([
//                'stripe_status' => $stripeSubscription->status,
//                'quantity' => $stripeSubscriptionItem->quantity,
//            ])->save();
//        }
//
//        if ($this->subscription->hasIncompletePayment()) {
//            optional($this->subscription->latestPayment())->validate();
//        }
//
//        return $this;
//    }
//
//    /**
//     * Swap the subscription item to a new Stripe price.
//     *
//     * @param  string  $price
//     * @param  array  $options
//     * @return \Laravel\Cashier\SubscriptionItem
//     *
//     * @throws \Laravel\Cashier\Exceptions\SubscriptionUpdateFailure
//     */
//    public function swap($price, array $options = [])
//    {
////        TODO
//        $this->subscription->guardAgainstIncomplete();
//
//        $stripeSubscriptionItem = $this->updateStripeSubscriptionItem(array_merge(
//            array_filter([
//                'price' => $price,
//                'quantity' => $this->quantity,
//                'payment_behavior' => $this->paymentBehavior(),
//                'proration_behavior' => $this->prorateBehavior(),
//                'tax_rates' => $this->subscription->getPriceTaxRatesForPayload($price),
//            ], function ($value) {
//                return ! is_null($value);
//            }),
//            $options));
//
//        $this->fill([
//            'stripe_product' => $stripeSubscriptionItem->price->product,
//            'stripe_price' => $stripeSubscriptionItem->price->id,
//            'quantity' => $stripeSubscriptionItem->quantity,
//        ])->save();
//
//        if ($this->subscription->hasSinglePrice()) {
//            $this->subscription->fill([
//                'stripe_price' => $price,
//                'quantity' => $stripeSubscriptionItem->quantity,
//            ])->save();
//        }
//
//        if ($this->subscription->hasIncompletePayment()) {
//            optional($this->subscription->latestPayment())->validate();
//        }
//
//        return $this;
//    }
//
//    /**
//     * Get the usage records for a metered product.
//     *
//     * @param  array  $options
//     * @return \Illuminate\Support\Collection
//     */
//    public function usageRecords($options = [])
//    {
//        return new Collection($this->subscription->owner->stripe()->subscriptionItems->allUsageRecordSummaries(
//            $this->stripeId(), $options
//        )->data);
//    }
//
//    /**
//     * Update the underlying Stripe subscription item information for the model.
//     *
//     * @param  array  $options
//     * @return \Stripe\SubscriptionItem
//     */
//    public function updateStripeSubscriptionItem(array $options = [])
//    {
//        return $this->subscription->owner->stripe()->subscriptionItems->update(
//            $this->stripeId(), $options
//        );
//    }
//
//    /**
//     * Get the subscription as a Stripe subscription item object.
//     *
//     * @param  array  $expand
//     * @return \Stripe\SubscriptionItem
//     */
//    public function asStripeSubscriptionItem(array $expand = [])
//    {
//        return $this->subscription->owner->stripe()->subscriptionItems->retrieve(
//            $this->stripeId(), ['expand' => $expand]
//        );
//    }

}
