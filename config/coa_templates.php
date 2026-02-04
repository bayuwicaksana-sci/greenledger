<?php

return [
    'standard_agricultural' => [
        'name' => 'Standard Agricultural Template',
        'description' => 'Comprehensive Chart of Accounts for agricultural operations including Assets, Liabilities, Equity, Revenue, and Expenses.',
        'accounts' => [
            // ASSETS (1xxx)
            [
                'code' => '1000',
                'name' => 'Assets',
                'type' => 'ASSET',
                'description' => 'General Assets',
                'abbreviation' => 'AST',
                'parent' => null,
            ],
            [
                'code' => '1100',
                'name' => 'Current Assets',
                'type' => 'ASSET',
                'description' => 'Assets expected to be sold or used within a year',
                'abbreviation' => 'CURR-AST',
                'parent' => '1000',
            ],
            [
                'code' => '1110',
                'name' => 'Cash and Equivalents',
                'type' => 'ASSET',
                'description' => 'Cash on hand and in bank',
                'abbreviation' => 'CASH',
                'parent' => '1100',
            ],

            // LIABILITIES (2xxx)
            [
                'code' => '2000',
                'name' => 'Liabilities',
                'type' => 'LIABILITY',
                'description' => 'General Liabilities',
                'abbreviation' => 'LIAB',
                'parent' => null,
            ],
            [
                'code' => '2100',
                'name' => 'Current Liabilities',
                'type' => 'LIABILITY',
                'description' => 'Debts due within a year',
                'abbreviation' => 'CURR-LIAB',
                'parent' => '2000',
            ],

            // EQUITY (3xxx)
            [
                'code' => '3000',
                'name' => 'Equity',
                'type' => 'EQUITY',
                'description' => 'Owner\'s Equity',
                'abbreviation' => 'EQ',
                'parent' => null,
            ],
            [
                'code' => '3100',
                'name' => 'Capital',
                'type' => 'EQUITY',
                'description' => 'Invested Capital',
                'abbreviation' => 'CAP',
                'parent' => '3000',
            ],

            // REVENUE (4xxx)
            [
                'code' => '4000',
                'name' => 'Revenue',
                'type' => 'REVENUE',
                'description' => 'General Revenue',
                'abbreviation' => 'REV',
                'parent' => null,
            ],
            [
                'code' => '4100',
                'name' => 'Operational Revenue',
                'type' => 'REVENUE',
                'description' => 'Revenue from core operations',
                'abbreviation' => 'OP-REV',
                'parent' => '4000',
            ],
            [
                'code' => '4110',
                'name' => 'Harvest Revenue',
                'type' => 'REVENUE',
                'description' => 'Sales from harvest',
                'abbreviation' => 'HARV-REV',
                'parent' => '4100',
            ],

            // EXPENSES (5xxx)
            [
                'code' => '5000',
                'name' => 'Expenses',
                'type' => 'EXPENSE',
                'description' => 'General Expenses',
                'abbreviation' => 'EXP',
                'parent' => null,
            ],
            [
                'code' => '5100',
                'name' => 'Operational Expenses',
                'type' => 'EXPENSE',
                'description' => 'Day-to-day operational costs',
                'abbreviation' => 'OP-EXP',
                'parent' => '5000',
            ],
            [
                'code' => '5211',
                'name' => 'Seeds Material',
                'type' => 'EXPENSE',
                'description' => 'Cost of seeds',
                'abbreviation' => 'MAT-SEED',
                'parent' => '5100',
            ],
            [
                'code' => '5212',
                'name' => 'Fertilizer Material',
                'type' => 'EXPENSE',
                'description' => 'Cost of fertilizer',
                'abbreviation' => 'MAT-FERT',
                'parent' => '5100',
            ],
        ],
    ],
];
