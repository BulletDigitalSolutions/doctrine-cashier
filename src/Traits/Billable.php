<?php

namespace BulletDigitalSolutions\DoctrineCashier\Traits;

use BulletDigitalSolutions\DoctrineCashier\Concerns\ManagesCustomer;
use BulletDigitalSolutions\DoctrineCashier\Concerns\ManagesInvoices;
use BulletDigitalSolutions\DoctrineCashier\Concerns\ManagesPaymentMethods;
use BulletDigitalSolutions\DoctrineCashier\Concerns\ManagesQuotes;
use BulletDigitalSolutions\DoctrineCashier\Concerns\ManagesSubscriptions;
use BulletDigitalSolutions\DoctrineCashier\Concerns\PerformsCharges;
use Laravel\Cashier\Concerns\HandlesTaxes;

trait Billable
{
    use HandlesTaxes;
    use ManagesCustomer;
    use ManagesInvoices;
    use ManagesPaymentMethods;
    use ManagesSubscriptions;
    use ManagesQuotes;
    use PerformsCharges;
}
