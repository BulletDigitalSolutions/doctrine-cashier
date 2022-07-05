# Very short description of the package

## This package is in early development and not yet tested for production use.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bulletdigitalsolutions/doctrine-cashier.svg?style=flat-square)](https://packagist.org/packages/bulletdigitalsolutions/doctrine-cashier)
[![Total Downloads](https://img.shields.io/packagist/dt/bulletdigitalsolutions/doctrine-cashier.svg?style=flat-square)](https://packagist.org/packages/bulletdigitalsolutions/doctrine-cashier)
![GitHub Actions](https://github.com/bulletdigitalsolutions/doctrine-cashier/actions/workflows/main.yml/badge.svg)

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what PSRs you support to avoid any confusion with users and contributors.

## Installation

You can install the package via composer:

```bash
composer require bulletdigitalsolutions/doctrine-cashier
```

## Usage

Your user entity should extend the BillableEntity class
```php
/**
 * @ORM\Table()
 * @ORM\Entity
 * @Gedmo\Loggable(logEntryClass="App\Entities\Audit")
 */
class User extends BillableEntity
```

Your user entity must implement getRepository, which returns a repository that implement BillableEntityContract
```php
public function getRepository()
{
    return app(UserContract::class);
}
```

```php
class UserRepository extends CoreRepository implements UserContract
{
    use TwoFactorRepository, BillableRepository;
```

You should then extend the Subscription and SubscriptionItem entities
```php
<?php

namespace App\Entities;

use BulletDigitalSolutions\DoctrineCashier\Contracts\SubscriptionContract;
use BulletDigitalSolutions\DoctrineCashier\Entities\Subscription as BaseSubscription;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table
 * @ORM\Entity
 * @Gedmo\Loggable(logEntryClass="App\Entities\Audit")
 */
class Subscription extends BaseSubscription implements SubscriptionContract
{
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    protected $user;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  mixed  $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param  mixed  $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }
}
```

```php
<?php

namespace App\Entities;

use BulletDigitalSolutions\DoctrineCashier\Contracts\SubscriptionItemContract;
use BulletDigitalSolutions\DoctrineCashier\Entities\SubscriptionItem as BaseSubscriptionItem;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table
 * @ORM\Entity
 * @Gedmo\Loggable(logEntryClass="App\Entities\Audit")
 */
class SubscriptionItem extends BaseSubscriptionItem implements SubscriptionItemContract
{
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Subscription", inversedBy="items")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    protected $subscription;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  mixed  $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @param  mixed  $subscription
     */
    public function setSubscription($subscription): void
    {
        $this->subscription = $subscription;
    }
}
````

You then need to register your entities on the CashierServiceProvider
```php
<?php

namespace App\Providers;

use App\Entities\Subscription;
use App\Entities\SubscriptionItem;
use App\Entities\User;
use Illuminate\Support\ServiceProvider;
use BulletDigitalSolutions\DoctrineCashier\Cashier;

class CashierServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Cashier::ignoreMigrations();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Cashier::useSubscriptionModel(Subscription::class);
        Cashier::useSubscriptionItemModel(SubscriptionItem::class);
        Cashier::useCustomerModel(User::class);
    }
}

```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email andrew@bulletdigitalsolutions.co.uk instead of using the issue tracker.

## Credits

-   [Andrew Hargrave](https://github.com/bulletdigitalsolutions)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
