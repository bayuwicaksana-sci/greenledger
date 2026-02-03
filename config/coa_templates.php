<?php

return [
    'agricultural_research_revenue' => [
        'name' => 'Agricultural Research Revenue Accounts',
        'description' => 'Standard revenue accounts for agricultural research operations including harvest and testing services.',
        'accounts' => [
            ['code' => '4-1000', 'name' => 'Revenue', 'type' => 'REVENUE', 'description' => 'Main revenue category', 'parent' => null],
            ['code' => '4-1100', 'name' => 'Harvest Revenue', 'type' => 'REVENUE', 'description' => 'Revenue from crop harvests', 'parent' => '4-1000'],
            ['code' => '4-1200', 'name' => 'Testing Services Revenue', 'type' => 'REVENUE', 'description' => 'Revenue from testing services', 'parent' => '4-1000'],
            ['code' => '4-1300', 'name' => 'Research Grants Revenue', 'type' => 'REVENUE', 'description' => 'Revenue from research grants', 'parent' => '4-1000'],
        ],
    ],
    'agricultural_research_expense' => [
        'name' => 'Agricultural Research Expense Accounts',
        'description' => 'Standard expense accounts for agricultural research operations.',
        'accounts' => [
            ['code' => '5-1000', 'name' => 'Operating Expenses', 'type' => 'EXPENSE', 'description' => 'Main expense category', 'parent' => null],
            ['code' => '5-1100', 'name' => 'Fertilizer', 'type' => 'EXPENSE', 'description' => 'Fertilizer purchases', 'parent' => '5-1000'],
            ['code' => '5-1200', 'name' => 'Seeds', 'type' => 'EXPENSE', 'description' => 'Seed purchases for planting', 'parent' => '5-1000'],
            ['code' => '5-1300', 'name' => 'Labor', 'type' => 'EXPENSE', 'description' => 'Agricultural labor costs', 'parent' => '5-1000'],
            ['code' => '5-1400', 'name' => 'Equipment', 'type' => 'EXPENSE', 'description' => 'Equipment and machinery costs', 'parent' => '5-1000'],
            ['code' => '5-2000', 'name' => 'Research Expenses', 'type' => 'EXPENSE', 'description' => 'Research-specific expenses', 'parent' => null],
            ['code' => '5-2100', 'name' => 'Laboratory Supplies', 'type' => 'EXPENSE', 'description' => 'Lab supplies and reagents', 'parent' => '5-2000'],
            ['code' => '5-2200', 'name' => 'Testing Materials', 'type' => 'EXPENSE', 'description' => 'Materials for testing services', 'parent' => '5-2000'],
        ],
    ],
];
