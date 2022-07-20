# Installation

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

use BulletDigitalSolutions\DoctrineCashier\Contracts\UserSubscriptionContract;
use BulletDigitalSolutions\DoctrineCashier\Entities\UserSubscription as BaseSubscription;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table
 * @ORM\Entity
 * @Gedmo\Loggable(logEntryClass="App\Entities\Audit")
 */
class Subscription extends BaseSubscription implements UserSubscriptionContract
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

use BulletDigitalSolutions\DoctrineCashier\Contracts\UserSubscriptionItemContract;
use BulletDigitalSolutions\DoctrineCashier\Entities\UserSubscriptionItem as BaseSubscriptionItem;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table
 * @ORM\Entity
 * @Gedmo\Loggable(logEntryClass="App\Entities\Audit")
 */
class SubscriptionItem extends BaseSubscriptionItem implements UserSubscriptionItemContract
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