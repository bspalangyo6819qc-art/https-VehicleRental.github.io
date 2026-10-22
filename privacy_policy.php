<?php
require_once __DIR__ . '/site_init.php';

$currentPolicy = basename(__FILE__);
$pageTitle = 'Privacy Policy';
$pageDescription = 'How Autoride handles the personal information you provide when using our vehicle rental services.';
$policySections = [
    [
        'heading' => 'Information We Collect',
        'paragraphs' => [
            'We collect information you provide when creating an account, requesting a rental, contacting support, or submitting feedback. This may include your name, email address, phone number, rental dates, and selected vehicle.',
            'Information needed to verify eligibility, identity, payment, or a rental request should only be submitted through the relevant secure service process.',
        ],
    ],
    [
        'heading' => 'How We Use Information',
        'paragraphs' => [
            'Autoride uses account and rental information to provide and manage bookings, communicate about requests, support customers, improve the service, and meet applicable legal obligations.',
            'We do not sell personal information. Information is shared only when needed to operate the service, complete a customer-requested transaction, protect users, or comply with law.',
        ],
    ],
    [
        'heading' => 'Retention and Security',
        'paragraphs' => [
            'We retain information only as long as reasonably needed for the purposes described in this policy and applicable recordkeeping requirements. We use reasonable safeguards, but no internet-based service can guarantee absolute security.',
        ],
    ],
    [
        'heading' => 'Your Choices',
        'paragraphs' => [
            'You may review or request correction of your account information through your profile or by contacting Autoride. Some information may need to be retained to complete rentals, resolve disputes, or comply with law.',
        ],
    ],
    [
        'heading' => 'Policy Updates',
        'paragraphs' => [
            'We may update this policy as our services or legal requirements change. The current version will be published on this page with its latest content.',
        ],
    ],
];

require __DIR__ . '/policy_layout.php';
