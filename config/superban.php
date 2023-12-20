<?php

return [
    /**
     * Sepecify what cache driver you would want to use for your request throttling
     * This accepts any driver available in laravel cache driver
     * Leave as null to use your default cache driver
     */
    'driver' => env('SUPERBAN_CACHE_DRIVER', null),

    /**
     * Set identifier for unauthenticated user
     * The two available is IP and Fingerprint
     * Default is ip address, you can customize it to your feel
     */

     'identifier' => env('SUPERBAN_IDENTIFIER', 'ip')
];
