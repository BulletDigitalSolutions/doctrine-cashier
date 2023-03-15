<?php

namespace BulletDigitalSolutions\DoctrineCashier;

use BulletDigitalSolutions\DoctrineCashier\Entities\UserQuote;
use BulletDigitalSolutions\DoctrineCashier\Entities\UserQuoteItem;
use Laravel\Cashier\Cashier as BaseCashier;
use Stripe\Customer as StripeCustomer;

class DoctrineCashier extends BaseCashier
{
    /**
     * Indicates if Cashier migrations will be run.
     *
     * @var bool
     */
    public static $runsMigrations = true;

    /**
     * The default customer model class name.
     *
     * @var string
     */
    public static $customerModel = 'App\\Entities\\User';

    /**
     * The quote model class name.
     *
     * @var string
     */
    public static $quoteModel = UserQuote::class;

    /**
     * The quote item model class name.
     *
     * @var string
     */
    public static $quoteItemModel = UserQuoteItem::class;

    /**
     * Indicates if Cashier routes will be registered.
     *
     * @var bool
     */
    public static $registersRoutes = true;

    /**
     * Get the customer instance by its Stripe ID.
     *
     * @param  \Stripe\Customer|string|null  $stripeId
     * @return \Laravel\Cashier\Billable|null
     */
    public static function findBillable($stripeId)
    {
        $stripeId = $stripeId instanceof StripeCustomer ? $stripeId->id : $stripeId;

        return $stripeId ? (new static::$customerModel)->getRepository()->findOneBy(['stripeId' => $stripeId]) : null;
    }

    /**
     * Set the quote model class name.
     *
     * @param  string  $quoteModel
     * @return void
     */
    public static function useQuoteModel($quoteModel)
    {
        static::$quoteModel = $quoteModel;
    }

    /**
     * @return string
     */
    public static function getQuoteModel(): string
    {
        return self::$quoteModel;
    }

    /**
     * Set the quote item model class name.
     *
     * @param  string  $quoteItemModel
     * @return void
     */
    public static function useQuoteItemModel($quoteItemModel)
    {
        static::$quoteItemModel = $quoteItemModel;
    }
}
