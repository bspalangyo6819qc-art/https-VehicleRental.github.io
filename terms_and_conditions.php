<?php
require_once __DIR__ . '/site_init.php';

$currentPolicy = basename(__FILE__);
$pageTitle = 'Rental Terms & Conditions';
$pageDescription = 'Important conditions for vehicle rental requests, pickup, use, and return.';
$policySections = [
    [
        'heading' => 'Eligibility and Documents',
        'paragraphs' => [
            'The renter must meet applicable age and licensing requirements and present a valid driver’s license and government-issued identification at pickup. Additional requirements may apply to international drivers.',
            'Only drivers approved for the rental may operate the vehicle. The renter is responsible for ensuring all required documents are available before pickup.',
        ],
    ],
    [
        'heading' => 'Rental Period and Payment',
        'paragraphs' => [
            'The rental period, rate, pickup time, and return time are those confirmed by Autoride. Minimum rental periods and charges are shown during booking and may vary by vehicle.',
            'Any deposit, reservation fee, driver fee, insurance option, or other charge will be shown in the confirmed booking details. A deposit is refundable subject to inspection and outstanding charges.',
        ],
    ],
    [
        'heading' => 'Vehicle Use and Care',
        'paragraphs' => [
            'Use the vehicle responsibly and only as permitted by law and the rental agreement. Do not allow an unapproved driver to operate it, use it for unlawful activity, or take it outside authorized areas.',
            'The renter is responsible for traffic violations, tolls, parking fees, and damage or loss arising during the rental, subject to applicable law and any agreed coverage.',
        ],
    ],
    [
        'heading' => 'Fuel, Pickup, and Return',
        'paragraphs' => [
            'The vehicle should be returned at the agreed time and location in the condition and with the fuel level specified at pickup. Additional fees may apply for late return, refueling, damage, or other costs identified in the booking.',
        ],
    ],
    [
        'heading' => 'Cancellations and Changes',
        'paragraphs' => [
            'Cancellation, extension, and change requests are subject to availability and the conditions communicated with the booking confirmation. Contact Autoride promptly to request a change.',
        ],
    ],
];

require __DIR__ . '/policy_layout.php';
