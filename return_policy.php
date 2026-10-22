<?php
require_once __DIR__ . '/site_init.php';

$currentPolicy = basename(__FILE__);
$pageTitle = 'Return Policy';
$pageDescription = 'What to expect when returning a rented vehicle and settling applicable charges.';
$policySections = [
    [
        'heading' => 'Returning the Vehicle',
        'paragraphs' => [
            'Return the vehicle at the agreed date, time, and location. Return it in substantially the same condition as received, subject to ordinary wear, and with the agreed fuel level.',
            'Remove personal belongings and return keys and any supplied accessories. Please report damage, warning lights, or incidents to Autoride as soon as possible.',
        ],
    ],
    [
        'heading' => 'Inspection and Additional Charges',
        'paragraphs' => [
            'The vehicle may be inspected at return. Additional charges may apply for late returns, missing items, refueling, damage, tolls, or traffic and parking violations, as applicable under the booking terms.',
        ],
    ],
    [
        'heading' => 'Deposits and Refunds',
        'paragraphs' => [
            'Any refundable security deposit is processed after return and inspection, less applicable unpaid charges. Processing times may depend on the payment method or financial institution.',
            'For a question about a charge or refund, contact Autoride with your booking reference and payment details. Do not send full card numbers or passwords.',
        ],
    ],
];

require __DIR__ . '/policy_layout.php';
