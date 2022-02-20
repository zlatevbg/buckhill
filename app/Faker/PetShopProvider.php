<?php

namespace App\Faker;

use Faker\Provider\Base;

class PetShopProvider extends Base
{
    protected static $categories = [
        'Dry dog food',
        'Pet treats and chews',
        'Flea and tick medication',
        'Pet grooming supplies',
        'Pet vitamins and supplements',
        'Heartworm medication',
        'Pet oral care',
        'Wet pet food',
        'Cat litter',
        'Pet clean-up and odor control',
        'Flea and tick collars',
        'Pet beds',
        'Pet carriers',
        'Pet repellants',
        'Pet toys',
    ];

    protected static $brands = [
        'Alleva Equilibrium',
        'Alleva Holistic',
        'Alleva Natural',
        'Animonda',
        'Aspect',
        'Mars Pet',
        'Nestlé Purina PetCare',
        'Mammoth Pet Products',
        'KONG',
        'Petmate',
        'Happy Pet',
        'Chuckit!',
        'Dogzilla',
        'Booba',
        'JW Dog',
    ];

    protected static $orderStatuses = [
        'Pending',
        'Awaiting Payment',
        'Awaiting Fulfillment',
        'Awaiting Shipment',
        'Awaiting Pickup',
        'Partially Shipped',
        'Completed',
        'Shipped',
        'Cancelled',
        'Declined',
        'Refunded',
        'Disputed',
        'Manual Verification Required',
        'Partially Refunded',
    ];

    public function categories(): string
    {
        return static::randomElement(static::$categories);
    }

    public function brands(): string
    {
        return static::randomElement(static::$brands);
    }

    public function orderStatuses(): string
    {
        return static::randomElement(static::$orderStatuses);
    }
}
