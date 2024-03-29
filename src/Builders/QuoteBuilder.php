<?php

namespace BulletDigitalSolutions\DoctrineCashier\Builders;

use BulletDigitalSolutions\DoctrineCashier\Entities\UserQuote;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Laravel\Cashier\Concerns\AllowsCoupons;
use Laravel\Cashier\Concerns\HandlesTaxes;
use Laravel\Cashier\Concerns\InteractsWithPaymentBehavior;
use Laravel\Cashier\Concerns\Prorates;
use Stripe\Quote as StripeQuote;

class QuoteBuilder
{
    use AllowsCoupons;
    use HandlesTaxes;
    use InteractsWithPaymentBehavior;
    use Prorates;

    /**
     * The model that is subscribing.
     *
     * @var \Laravel\Cashier\Billable|\Illuminate\Database\Eloquent\Model
     */
    protected $owner;

    /**
     * The name of the subscription.
     *
     * @var string
     */
    protected $name;

    /**
     * The prices the customer is being subscribed to.
     *
     * @var array
     */
    protected $items = [];

    /**
     * The date and time the trial will expire.
     *
     * @var \Carbon\Carbon|\Carbon\CarbonInterface|null
     */
    protected $trialExpires;

    /**
     * Indicates that the trial should end immediately.
     *
     * @var bool
     */
    protected $skipTrial = false;

    /**
     * The date on which the billing cycle should be anchored.
     *
     * @var int|null
     */
    protected $billingCycleAnchor = null;

    /**
     * The metadata to apply to the subscription.
     *
     * @var array
     */
    protected $metadata = [];

    /**
     * @var mixed|null
     */
    protected $userQuote = null;

    /**
     * @var null|Carbon
     */
    protected $expiresAt = null;

    /**
     * Create a new subscription builder instance.
     *
     * @param  mixed  $owner
     * @param  string  $name
     * @param  string|string[]|array[]  $prices
     * @return void
     */
    public function __construct($owner, $name, $prices = [], $userQuote = null)
    {
        $this->name = $name;
        $this->owner = $owner;
        $this->userQuote = $userQuote;

        foreach ((array) $prices as $price) {
            $this->price($price);
        }
    }

    /**
     * Set a price on the subscription builder.
     *
     * @param  string|array  $price
     * @param  int|null  $quantity
     * @return $this
     */
    public function price($price, $quantity = 1)
    {
        $options = is_array($price) ? $price : ['price' => $price];

        $quantity = $price['quantity'] ?? $quantity;

        if (! is_null($quantity)) {
            $options['quantity'] = $quantity;
        }

        if ($taxRates = $this->getPriceTaxRatesForPayload($price)) {
            $options['tax_rates'] = $taxRates;
        }

        if (isset($options['price'])) {
            $this->items[$options['price']] = $options;
        } else {
            $this->items[] = $options;
        }

        return $this;
    }

    /**
     * @param  Carbon  $dateTime
     * @return $this
     */
    public function expires(Carbon $dateTime)
    {
        $this->expiresAt = $dateTime;

        return $this;
    }

    /**
     * Set a metered price on the subscription builder.
     *
     * @param  string  $price
     * @return $this
     */
    public function meteredPrice($price)
    {
        return $this->price($price, null);
    }

    /**
     * Specify the quantity of a subscription item.
     *
     * @param  int|null  $quantity
     * @param  string|null  $price
     * @return $this
     */
    public function quantity($quantity, $price = null)
    {
        if (is_null($price)) {
            if (count($this->items) > 1) {
                throw new InvalidArgumentException('Price is required when creating multi-price subscriptions.');
            }

            $price = Arr::first($this->items)['price'];
        }

        return $this->price($price, $quantity);
    }

    /**
     * Specify the number of days of the trial.
     *
     * @param  int  $trialDays
     * @return $this
     */
    public function trialDays($trialDays)
    {
        $this->trialExpires = Carbon::now()->addDays($trialDays);

        return $this;
    }

    /**
     * Specify the ending date of the trial.
     *
     * @param  \Carbon\Carbon|\Carbon\CarbonInterface  $trialUntil
     * @return $this
     */
    public function trialUntil($trialUntil)
    {
        $this->trialExpires = $trialUntil;

        return $this;
    }

    /**
     * Force the trial to end immediately.
     *
     * @return $this
     */
    public function skipTrial()
    {
        $this->skipTrial = true;

        return $this;
    }

    /**
     * Change the billing cycle anchor on a subscription creation.
     *
     * @param  \DateTimeInterface|int  $date
     * @return $this
     */
    public function anchorBillingCycleOn($date)
    {
        if ($date instanceof DateTimeInterface) {
            $date = $date->getTimestamp();
        }

        $this->billingCycleAnchor = $date;

        return $this;
    }

    /**
     * The metadata to apply to a new subscription.
     *
     * @param  array  $metadata
     * @return $this
     */
    public function withMetadata($metadata)
    {
        $this->metadata = (array) $metadata;

        return $this;
    }

    /**
     * Add a new Stripe subscription to the Stripe model.
     *
     * @param  array  $customerOptions
     * @param  array  $subscriptionOptions
     * @return \Laravel\Cashier\Subscription
     *
     * @throws \Laravel\Cashier\Exceptions\IncompletePayment
     */
    public function add(array $customerOptions = [], array $subscriptionOptions = [])
    {
        return $this->create(null, $customerOptions, $subscriptionOptions);
    }

    /**
     * @param $paymentMethod
     * @param  array  $customerOptions
     * @param  array  $subscriptionOptions
     * @return \Laravel\Cashier\Subscription
     *
     * @throws \Laravel\Cashier\Exceptions\IncompletePayment
     */
    public function save($paymentMethod = null, array $customerOptions = [], array $subscriptionOptions = [])
    {
        if (! $this->userQuote) {
            return $this->create($paymentMethod, $customerOptions, $subscriptionOptions);
        }

        return $this->update($paymentMethod, $customerOptions, $subscriptionOptions);
    }

    /**
     * Create a new Stripe subscription.
     *
     * @param  \Stripe\PaymentMethod|string|null  $paymentMethod
     * @param  array  $customerOptions
     * @param  array  $subscriptionOptions
     * @return \Laravel\Cashier\Subscription
     *
     * @throws \Exception
     * @throws \Laravel\Cashier\Exceptions\IncompletePayment
     */
    public function create($paymentMethod = null, array $customerOptions = [], array $subscriptionOptions = [])
    {
        if (empty($this->items)) {
            throw new Exception('At least one price is required when starting quotes.');
        }

        $stripeCustomer = $this->getStripeCustomer($paymentMethod, $customerOptions);

        $stripeQuote = $this->owner->stripe()->quotes->create(array_merge(
            ['customer' => $stripeCustomer->id],
            $this->buildPayload(),
            $subscriptionOptions
        ));

        return $this->createQuote($stripeQuote);
    }

    /**
     * @param $paymentMethod
     * @param  array  $customerOptions
     * @param  array  $subscriptionOptions
     * @return \Laravel\Cashier\Subscription
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function update($paymentMethod = null, array $customerOptions = [], array $subscriptionOptions = [])
    {
        if (empty($this->items)) {
            throw new Exception('At least one price is required when starting quotes.');
        }

        if (! $this->userQuote) {
            throw new Exception('Stripe ID is required to update a quote.');
        }

        $stripeCustomer = $this->getStripeCustomer($paymentMethod, $customerOptions);

        $stripeQuote = $this->owner->stripe()->quotes->update($this->userQuote->getStripeId(),
            array_merge(
                ['customer' => $stripeCustomer->id],
                $this->buildPayload(),
                $subscriptionOptions
            )
        );

        return $this->updateQuote($stripeQuote);
    }

    /**
     * Create the Eloquent Subscription.
     *
     * @param  \Stripe\Subscription  $stripeSubscription
     * @return \Laravel\Cashier\Subscription
     */
    protected function createQuote(StripeQuote $stripeQuote)
    {
        $dt = new DateTime();
        $dt->setTimestamp($stripeQuote->expires_at);

        $quote = $this->owner->quotes()->create([
            'name' => $this->name,
            'stripe_id' => $stripeQuote->id,
            'expires_at' => $dt,
            'discounts' => $this->getDiscountsArray(),
        ]);

        $lineItems = $this->owner->stripe()->quotes->allLineItems($stripeQuote->id);

        foreach ($lineItems->data as $lineItem) {
            $quote->items()->create([
                'stripe_id' => $lineItem->id,
                'quantity' => $lineItem->quantity,
                'amount_total' => $lineItem->amount_total,
                'stripe_product' => $lineItem->price->product,
                'stripe_price' => $lineItem->price->id,
            ]);
        }

        return $quote;
    }

    /**
     * @return array
     */
    public function getDiscountsArray()
    {
        $discounts = [];

        if ($this->couponId) {
            $discounts[] = $this->couponId;
        }

        if ($this->promotionCodeId) {
            $discounts[] = $this->promotionCodeId;
        }

        return $discounts;
    }

    /**
     * Update the Eloquent Quote.
     *
     * @param  \Stripe\Subscription  $stripeSubscription
     * @return \Laravel\Cashier\Subscription
     */
    protected function updateQuote(StripeQuote $stripeQuote)
    {
        $dt = new DateTime();
        $dt->setTimestamp($stripeQuote->expires_at);

        $this->userQuote->update([
            'name' => $this->name,
            'stripe_id' => $stripeQuote->id,
            'expires_at' => $dt,
            'promo_code' => $this->promotionCodeId,
            'coupon_code' => $this->couponId,
        ]);

        $this->userQuote->items()->delete();

        $lineItems = $this->owner->stripe()->quotes->allLineItems($stripeQuote->id);

        foreach ($lineItems->data as $lineItem) {
            $this->userQuote->items()->create([
                'stripe_id' => $lineItem->id,
                'quantity' => $lineItem->quantity,
                'amount_total' => $lineItem->amount_total,
                'stripe_product' => $lineItem->price->product,
                'stripe_price' => $lineItem->price->id,
            ]);
        }

        return $this->userQuote;
    }

    /**
     * Get the Stripe customer instance for the current user and payment method.
     *
     * @param  \Stripe\PaymentMethod|string|null  $paymentMethod
     * @param  array  $options
     * @return \Stripe\Customer
     */
    protected function getStripeCustomer($paymentMethod = null, array $options = [])
    {
        $customer = $this->owner->createOrGetStripeCustomer($options);

        if ($paymentMethod) {
            $this->owner->updateDefaultPaymentMethod($paymentMethod);
        }

        return $customer;
    }

    /**
     * Build the payload for subscription creation.
     *
     * @return array
     */
    protected function buildPayload()
    {
        $payload = array_filter([
            'automatic_tax' => $this->automaticTaxPayload(),
            'billing_cycle_anchor' => $this->billingCycleAnchor,
            'metadata' => $this->metadata,
            'trial_end' => $this->getTrialEndForPayload(),
            'discounts' => $this->getDiscountArray(),
        ]);

        if ($this->expiresAt) {
            $payload['expires_at'] = $this->expiresAt->timestamp;
        }

        if ($this->items) {
            $payload['line_items'] = Collection::make($this->items)->values()->all();
        }

        if ($taxRates = $this->getTaxRatesForPayload()) {
            $payload['default_tax_rates'] = $taxRates;
        }

        return $payload;
    }

    /**
     * @return array
     */
    public function getDiscountArray()
    {
        $discounts = [];

        if ($this->couponId) {
            $discounts['coupon'] = $this->couponId;
        }

        return $discounts;
    }

    /**
     * Get the trial ending date for the Stripe payload.
     *
     * @return int|string|null
     */
    protected function getTrialEndForPayload()
    {
        if ($this->skipTrial) {
            return 'now';
        }

        if ($this->trialExpires) {
            return $this->trialExpires->getTimestamp();
        }
    }

    /**
     * Get the tax rates for the Stripe payload.
     *
     * @return array|null
     */
    protected function getTaxRatesForPayload()
    {
        if ($taxRates = $this->owner->taxRates()) {
            return $taxRates;
        }
    }

    /**
     * Get the price tax rates for the Stripe payload.
     *
     * @param  string  $price
     * @return array|null
     */
    protected function getPriceTaxRatesForPayload($price)
    {
        if ($taxRates = $this->owner->priceTaxRates()) {
            return $taxRates[$price] ?? null;
        }
    }

    /**
     * Get the items set on the subscription builder.
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param  UserQuote  $quote
     * @return QuoteBuilder
     */
    public static function fromQuote(UserQuote $quote, $name, $prices)
    {
        return new self($quote->owner(), $name, $prices, $quote);
    }
}
