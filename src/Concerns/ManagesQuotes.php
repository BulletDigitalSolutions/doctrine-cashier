<?php

namespace BulletDigitalSolutions\DoctrineCashier\Concerns;

use BulletDigitalSolutions\DoctrineCashier\Builders\QuoteBuilder;
use BulletDigitalSolutions\DoctrineCashier\Cashier;
use BulletDigitalSolutions\DoctrineEloquent\Relationships\HasMany;

trait ManagesQuotes
{
    /**
     * Begin creating a new quote.
     *
     * @param  string  $name
     * @param  string|string[]  $prices
     * @return QuoteBuilder
     */
    public function newQuote($name, $prices = [])
    {
        return new QuoteBuilder($this, $name, $prices);
    }

    /**
     * @param $name
     * @param $prices
     * @return QuoteBuilder
     */
    public function newOrUpdateLatestQuote($name, $prices = [])
    {
        $quote = $this->getLatestQuote();

        if ($quote) {
            return $quote->builder($name, $prices);
        }

        return $this->newQuote($name, $prices);
    }

    /**
     * @return null
     */
    public function getLatestQuote()
    {
        // TODO: Not expired at
        return $this->quotes()
            ->where('cancelled_at', null)
            ->where('accepted_at', null)
            ->where('finalised_at', null)
            ->where('expires_at', '>', now())
            ->first();
    }

    /**
     * Get a quote instance by id.
     *
     * @param  string  $name
     * @return \Laravel\Cashier\Subscription|null
     */
    public function quote($id = 'default')
    {
        return $this->quotes()->where('stripe_id', $id)->first();
    }

    /**
     * @return HasMany
     */
    public function quotes()
    {
        $hasMany = new HasMany($this, Cashier::$quoteModel, null, 'user', 'getQuotes');

        return $hasMany->orderBy('created_at', 'desc');
    }
}
