<?php
require_once __DIR__ . '/site_init.php';

$currentPolicy = basename(__FILE__);
$pageTitle = 'Frequently Asked Questions';
$pageDescription = 'Answers to common questions about Autoride accounts, vehicle rentals, and bookings.';
$policySections = [
    [
        'heading' => 'How do I rent a vehicle?',
        'paragraphs' => [
            'Browse available vehicles, open a vehicle’s details, select your dates and pickup location, then submit a rental request. A request is subject to availability and confirmation.',
        ],
    ],
    [
        'heading' => 'Is a rental request automatically confirmed?',
        'paragraphs' => [
            'No. A request is not confirmed until Autoride confirms it. Check your account or messages for the current status.',
        ],
    ],
    [
        'heading' => 'What documents do I need?',
        'paragraphs' => [
            'Requirements vary by rental. A valid driver’s license and government-issued identification are generally required. Review the Rental Terms & Conditions before requesting a vehicle.',
        ],
    ],
    [
        'heading' => 'How can I cancel or change a rental?',
        'paragraphs' => [
            'Contact Autoride as soon as possible with your booking details. Applicable cancellation and change conditions are described in the Rental Terms & Conditions.',
        ],
    ],
    [
        'heading' => 'How do I leave a review?',
        'paragraphs' => [
            'Sign in and submit feedback from the vehicle or browse page. Reviews linked to completed rentals may be marked as verified.',
        ],
    ],
];

require __DIR__ . '/policy_layout.php';
