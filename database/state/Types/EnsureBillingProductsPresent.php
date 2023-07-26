<?php

namespace Database\State\Types;

use Database\State\State;

class EnsureBillingProductsPresent extends State
{
    /**
     * Name of table to check
     * 
     * @var string
     */
    protected string $table = 'billing_products';

    /**
     * Array of columns that the $rows array must use
     * 
     * @var array<string>
     */
    protected array $columns = [
        "id", "name", "price_in_cents", "bucks_amount",
        "membership", "membership_length", "stripe_plan_id", "active"
    ];

    /**
     * Array of rows that should exist
     * 
     * @var array
     */
    protected array $rows = [
        // ! WARNING: There are extra rows included in the production DB added after this for sales, simply adding more rows to this will have no result due to the way State::present() works
        // ! WARNING: It is likely better to just add this to a superadmin web interface due to how much these values could potentially change and not tie them directly to the site code
        ['id' => 1, 'name' => '100 Bucks', 'price_in_cents' => 99, 'bucks_amount' => 100, 'membership' => NULL, 'membership_length' => NULL, 'stripe_plan_id' => NULL, 'active' => 0],
        ['id' => 2, 'name' => '500 Bucks', 'price_in_cents' => 489, 'bucks_amount' => 500, 'membership' => NULL, 'membership_length' => NULL, 'stripe_plan_id' => NULL, 'active' => 1],
        ['id' => 3, 'name' => '1000 Bucks', 'price_in_cents' => 959, 'bucks_amount' => 1000, 'membership' => NULL, 'membership_length' => NULL, 'stripe_plan_id' => NULL, 'active' => 1],
        ['id' => 4, 'name' => '2000 Bucks', 'price_in_cents' => 1899, 'bucks_amount' => 2000, 'membership' => NULL, 'membership_length' => NULL, 'stripe_plan_id' => NULL, 'active' => 1],
        ['id' => 5, 'name' => '5000 Bucks', 'price_in_cents' => 4599, 'bucks_amount' => 5000, 'membership' => NULL, 'membership_length' => NULL, 'stripe_plan_id' => NULL, 'active' => 1],
        ['id' => 6, 'name' => '10000 Bucks', 'price_in_cents' => 8899, 'bucks_amount' => 10000, 'membership' => NULL, 'membership_length' => NULL, 'stripe_plan_id' => NULL, 'active' => 1],
        ['id' => 7, 'name' => 'Ace Monthly', 'price_in_cents' => 599, 'bucks_amount' => 0, 'membership' => 3, 'membership_length' => 43800, 'stripe_plan_id' => 'price_1HADmjI5Z7R0wNWVbKS87JAm', 'active' => 1],
        ['id' => 8, 'name' => 'Ace Biannually', 'price_in_cents' => 3299, 'bucks_amount' => 0, 'membership' => 3, 'membership_length' => 262800, 'stripe_plan_id' => 'price_1HADmqI5Z7R0wNWVsUpUBhO5', 'active' => 1],
        ['id' => 9, 'name' => 'Ace Annually', 'price_in_cents' => 5799, 'bucks_amount' => 0, 'membership' => 3, 'membership_length' => 525600, 'stripe_plan_id' => 'price_1HADmtI5Z7R0wNWVuh4gf876', 'active' => 1],
        ['id' => 10, 'name' => 'Royal Monthly', 'price_in_cents' => 1299, 'bucks_amount' => 0, 'membership' => 4, 'membership_length' => 43800, 'stripe_plan_id' => 'price_1HADmxI5Z7R0wNWVEGaBWkQM', 'active' => 1],
        ['id' => 11, 'name' => 'Royal Biannually', 'price_in_cents' => 6899, 'bucks_amount' => 0, 'membership' => 4, 'membership_length' => 262800, 'stripe_plan_id' => 'price_1HADn0I5Z7R0wNWV6vjM8IPh', 'active' => 1],
        ['id' => 12, 'name' => 'Royal Annually', 'price_in_cents' => 12699, 'bucks_amount' => 0, 'membership' => 4, 'membership_length' => 525600, 'stripe_plan_id' => 'price_1HADn2I5Z7R0wNWV1mJCoy8r', 'active' => 1],
        ['id' => 13, 'name' => 'Client Access', 'price_in_cents' => 2450, 'bucks_amount' => 0, 'membership' => NULL, 'membership_length' => NULL, 'stripe_plan_id' => NULL, 'active' => 1],
    ];
}
