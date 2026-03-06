<?php

declare(strict_types=1);

return [
    'name' => 'Reporting',
    
    /*
    |--------------------------------------------------------------------------
    | Default Export Format
    |--------------------------------------------------------------------------
    */
    'default_format' => 'html',
    
    /*
    |--------------------------------------------------------------------------
    | Available Export Formats
    |--------------------------------------------------------------------------
    */
    'formats' => [
        'html' => 'HTML',
        'pdf' => 'PDF',
        'excel' => 'Excel',
        'csv' => 'CSV',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Report Types
    |--------------------------------------------------------------------------
    */
    'types' => [
        'sales' => 'Sales Report',
        'products' => 'Products Report',
        'customers' => 'Customers Report',
        'inventory' => 'Inventory Report',
        'orders' => 'Orders Report',
        'coupons' => 'Coupons Report',
        'revenue' => 'Revenue Report',
        'tax' => 'Tax Report',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Schedule Frequencies
    |--------------------------------------------------------------------------
    */
    'frequencies' => [
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'biweekly' => 'Bi-weekly',
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Date Range Types
    |--------------------------------------------------------------------------
    */
    'date_ranges' => [
        'last_7_days' => 'Last 7 Days',
        'last_30_days' => 'Last 30 Days',
        'last_month' => 'Last Month',
        'last_quarter' => 'Last Quarter',
        'last_year' => 'Last Year',
        'custom' => 'Custom Range',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Maximum Records Per Report
    |--------------------------------------------------------------------------
    */
    'max_records' => 100000,
    
    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    */
    'storage' => [
        'disk' => env('REPORT_STORAGE_DISK', 'local'),
        'path' => 'reports',
        'retention_days' => 30,
    ],
];
