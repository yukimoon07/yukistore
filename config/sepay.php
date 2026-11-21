<?php

return [
    /*
    | SePay webhook credentials stay server-side. Fill these in the deployment
    | environment, never commit them to the repository.
    */
    'webhook_secret' => env('SEPAY_WEBHOOK_SECRET'),
    'signature_header' => env('SEPAY_SIGNATURE_HEADER', 'X-SePay-Signature'),
    'signature_encoding' => env('SEPAY_SIGNATURE_ENCODING', 'hex'),
    'prefix' => env('SEPAY_PAYMENT_PREFIX', 'NAP'),
    'bank_name' => env('SEPAY_BANK_NAME', 'SePay'),
];
