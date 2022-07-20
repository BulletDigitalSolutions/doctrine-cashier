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
        $hasMany = new HasMany($this, Cashier::$quoteModel, null, 'user');
        return $hasMany->orderBy('created_at', 'desc');
    }
}
