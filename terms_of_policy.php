<?php
require_once __DIR__ . '/site_init.php';

$currentPolicy = basename(__FILE__);
$pageTitle = 'Terms of Service';
$pageDescription = 'The general terms for accessing and using Autoride accounts and services.';
$policySections = [
    [
        'heading' => 'Using Autoride',
        'paragraphs' => [
            'Use Autoride only for lawful purposes and provide accurate information when creating an account, contacting us, or requesting a service.',
            'You are responsible for keeping your sign-in credentials confidential and for activity performed using your account. Notify Autoride promptly if you believe your account has been used without permission.',
        ],
    ],
    [
        'heading' => 'Bookings and Availability',
        'paragraphs' => [
            'A rental request is not confirmed until Autoride communicates its acceptance. Availability, rates, and vehicle information may change until confirmation.',
            'Rental-specific eligibility, payment, deposits, cancellations, pickup, return, and vehicle-use rules are described in the Rental Terms & Conditions and the terms shown during booking.',
        ],
    ],
    [
        'heading' => 'Acceptable Conduct',
        'paragraphs' => [
            'Do not misuse the service, attempt unauthorized access, interfere with its operation, submit fraudulent information, or use another person’s account without permission.',
        ],
    ],
    [
        'heading' => 'Service and Changes',
        'paragraphs' => [
            'We may update or discontinue features to maintain or improve the service. These terms may be updated from time to time; continued use after an update takes effect means you accept the revised terms.',
        ],
    ],
    [
        'heading' => 'Contact',
        'paragraphs' => [
            'For questions about these terms or a booking, contact Autoride using the contact details provided on the About Us page.',
        ],
    ],
];

require __DIR__ . '/policy_layout.php';
