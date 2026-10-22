<?php
require_once __DIR__ . '/site_init.php';

if (empty($_SESSION['customer_user']['id'])) {
    app_redirect('log_in.php');
}

$customer = is_array($_SESSION['customer_user']) ? $_SESSION['customer_user'] : [];
$profile = [
    'full_name' => $customer['name'] ?? 'Guest User',
    'email' => $customer['email'] ?? 'Not signed in',
    'phone' => $customer['phone'] ?? 'Not provided',
    'address' => $customer['address'] ?? '',
    'created_at' => $customer['created_at'] ?? '',
    'status' => $customer['status'] ?? 'active',
    'profile_picture' => $customer['profile_picture'] ?? '',
];
$nameParts = preg_split('/\s+/', trim($profile['full_name']), -1, PREG_SPLIT_NO_EMPTY);
$initials = '';
foreach (array_slice($nameParts ?: [], 0, 2) as $namePart) {
    $initials .= strtoupper(substr($namePart, 0, 1));
}
$initials = $initials !== '' ? $initials : 'GU';

$profileMessage = $_SESSION['profile_message'] ?? '';
$profileError = $_SESSION['profile_error'] ?? '';
$reviewMessage = $_SESSION['review_message'] ?? '';
$showDemoRecords = !empty($customer['is_demo']);
$demoBookings = [
    [
        'vehicle_name' => 'Toyota Vios',
        'pickup_date' => '2025-06-12',
        'return_date' => '2025-06-15',
        'rental_days' => 3,
        'daily_rate' => 2600,
        'driver_fee' => 1500,
        'insurance_fee' => 600,
        'security_deposit' => 5000,
        'total_amount' => 14900,
        'amount_paid' => 14900,
        'balance' => 0,
        'status' => 'completed',
        'payment_method' => 'card',
        'payment_status' => 'paid',
    ],
    [
        'vehicle_name' => 'Honda City',
        'pickup_date' => '2025-05-28',
        'return_date' => '2025-05-30',
        'rental_days' => 2,
        'daily_rate' => 3300,
        'driver_fee' => 0,
        'insurance_fee' => 400,
        'security_deposit' => 5000,
        'total_amount' => 12000,
        'amount_paid' => 5000,
        'balance' => 7000,
        'status' => 'completed',
        'payment_method' => 'bank_transfer',
        'payment_status' => 'partial',
    ],
];
$demoReviews = [
    [
        'vehicle_name' => 'Toyota Vios',
        'rating' => 5,
        'review_text' => 'Comfortable, clean, and easy to drive. Pickup was quick and the car worked perfectly for our family weekend.',
        'created_at' => '2025-06-16',
    ],
    [
        'vehicle_name' => 'Honda City',
        'rating' => 4,
        'review_text' => 'A smooth, fuel-efficient ride and a simple rental process. Would happily book it again.',
        'created_at' => '2025-05-31',
    ],
];
$bookings = isset($bookings) && is_array($bookings) ? $bookings : [];
$rentalCharges = isset($rentalCharges) && is_array($rentalCharges) ? $rentalCharges : [];
$payments = isset($payments) && is_array($payments) ? $payments : [];
$outstandingBalances = isset($outstandingBalances) && is_array($outstandingBalances) ? $outstandingBalances : [];
$reviews = isset($reviews) && is_array($reviews) ? $reviews : [];
$rentalCounts = isset($rentalCounts) && is_array($rentalCounts) ? $rentalCounts : [
    'active' => 0,
    'upcoming' => 0,
    'completed' => 0,
    'cancelled' => 0,
];
$rentalCounts = array_merge([
    'active' => 0,
    'upcoming' => 0,
    'completed' => 0,
    'cancelled' => 0,
], $rentalCounts);
$formatMoney = isset($formatMoney) && is_callable($formatMoney)
    ? $formatMoney
    : static function ($amount): string {
        return 'PHP ' . number_format((float) $amount, 2);
    };
$totalCharges = isset($totalCharges)
    ? (float) $totalCharges
    : array_sum(array_map(static function ($booking): float {
        return (float) ($booking['total_amount'] ?? 0);
    }, $rentalCharges));
$paidTotal = isset($paidTotal) ? (float) $paidTotal : 0.0;
$outstandingTotal = isset($outstandingTotal) ? (float) $outstandingTotal : array_sum(array_map(static function ($booking): float {
    return (float) ($booking['balance'] ?? 0);
}, $outstandingBalances));
$refundedTotal = isset($refundedTotal) ? (float) $refundedTotal : 0.0;
$vehicleRentalTotal = 0.0;
$additionalRentalChargesTotal = 0.0;
foreach ($rentalCharges as $rentalCharge) {
    $vehicleRentalTotal += (float) ($rentalCharge['daily_rate'] ?? 0) * (int) ($rentalCharge['rental_days'] ?? 0);
    $additionalRentalChargesTotal += (float) ($rentalCharge['driver_fee'] ?? 0)
        + (float) ($rentalCharge['insurance_fee'] ?? 0)
        + (float) ($rentalCharge['security_deposit'] ?? 0);
}
$profileBookings = $bookings;
$profileRentalCharges = $rentalCharges;
$profilePayments = $payments;
$profileOutstandingBalances = $outstandingBalances;
$profileReviews = $reviews;
if ($showDemoRecords) {
    foreach ($demoBookings as $index => $demoBooking) {
        $demoBooking['id'] = 'S-' . ($index + 1);
        $demoBooking['is_sample'] = true;
        $demoBooking['created_at'] = $demoBooking['return_date'];
        $profileBookings[] = $demoBooking;
        $profileRentalCharges[] = $demoBooking;
        $profilePayments[] = [
            'reference_code' => 'EXAMPLE-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
            'vehicle_name' => $demoBooking['vehicle_name'],
            'paid_at' => $demoBooking['return_date'],
            'payment_method' => $demoBooking['payment_method'],
            'status' => $demoBooking['payment_status'],
            'amount' => $demoBooking['amount_paid'],
            'is_sample' => true,
        ];
        if ($demoBooking['balance'] > 0) {
            $profileOutstandingBalances[] = [
                'vehicle_name' => $demoBooking['vehicle_name'],
                'id' => $demoBooking['id'],
                'balance' => $demoBooking['balance'],
                'payment_status' => $demoBooking['payment_status'],
                'is_sample' => true,
            ];
        }
    }
    foreach ($demoReviews as $demoReview) {
        $demoReview['is_sample'] = true;
        $demoReview['status'] = 'example';
        $profileReviews[] = $demoReview;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Profile | Autoride</title>
  <style>
    :root {
      --ink: #111827;
      --text: #374151;
      --muted: #6b7280;
      --line: #d1d5db;
      --border: #e7e9ed;
      --light: #f8f9fb;
      --white: #fff;
      --green: #087b51;
      --orange: #ba6810;
      --red: #c43d3d;
    }
    * { box-sizing: border-box; }
    body { zoom: 1.2; margin: 0; color: var(--text); background: #fff; font: 10px/1.45 Arial, Helvetica, sans-serif; }
    a { color: inherit; text-decoration: none; }
    button, input, select { font: inherit; }
    .header { height: 32px; display: flex; align-items: center; justify-content: space-between; padding: 0 6%; border-bottom: 1px solid var(--border); }
    .logo { color: var(--ink); font-size: 8px; font-weight: 800; }
    .nav { display: flex; align-items: center; gap: 13px; }
    .nav a { color: var(--text); font-size: 5px; }
    .nav a:hover, .nav .active { color: var(--ink); text-decoration: underline; }
    .search { width: 6px; height: 6px; border: 1px solid var(--ink); border-radius: 50%; position: relative; }
    .search::after { content: ""; position: absolute; right: -3px; bottom: -1px; width: 3px; height: 1px; background: var(--ink); transform: rotate(45deg); }

    .layout { width: min(100% - 40px, 900px); margin: 16px auto 24px; display: grid; grid-template-columns: 112px minmax(0, 1fr); gap: 20px; align-items: start; }
    .sidebar { padding: 8px; border: 1px solid var(--border); border-radius: 3px; background: #fafbfc; }
    .avatar { width: 32px; height: 32px; margin: 0 auto 5px; display: grid; place-items: center; border: 1px solid var(--line); border-radius: 50%; background: #eef0f3; color: #737b87; font-size: 8px; font-weight: 700; }
    .side-name { color: var(--ink); font-size: 6px; font-weight: 700; text-align: center; }
    .side-email { margin: 2px 0 8px; color: var(--muted); font-size: 4.5px; text-align: center; overflow-wrap: anywhere; }
    .side-menu { display: grid; gap: 3px; }
    .side-menu a { padding: 4px 5px; border: 1px solid var(--border); border-radius: 2px; background: #fff; color: var(--text); font-size: 5px; }
    .side-menu a.active { border-color: #d5d9df; background: #eceff3; color: var(--ink); font-weight: 700; }
    .side-menu a:hover { color: var(--ink); border-color: var(--line); }
    .content { min-width: 0; }
    .page-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; margin-bottom: 8px; }
    .page-heading h1 { margin: 0; color: var(--ink); font-size: 9px; }
    .page-heading p { margin: 2px 0 0; color: var(--muted); font-size: 5px; }
    .button { padding: 4px 7px; border: 1px solid var(--line); border-radius: 2px; background: #fff; color: var(--ink); font-size: 5px; cursor: pointer; }
    .button:hover { border-color: var(--ink); }
    .section { margin-bottom: 12px; }
    .section-title { margin: 0 0 5px; padding-bottom: 4px; border-bottom: 1px solid var(--border); color: var(--ink); font-size: 7px; }
    .panel { padding: 8px; border: 1px solid var(--border); border-radius: 3px; background: #fff; }
    .overview-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 5px 12px; }
    .info-item small { display: block; margin-bottom: 2px; color: var(--muted); font-size: 4.5px; }
    .info-item strong { display: block; overflow: hidden; color: var(--ink); font-size: 5px; font-weight: 600; text-overflow: ellipsis; white-space: nowrap; }
    .profile-panel { margin-top: 6px; }
    .profile-panel h3 { margin: 0 0 6px; color: var(--ink); font-size: 6px; }
    .profile-form { display: grid; grid-template-columns: 1fr 1fr; gap: 5px 8px; }
    .profile-form label { color: var(--muted); font-size: 4.5px; }
    .profile-form input { width: 100%; height: 17px; margin-top: 2px; padding: 0 5px; border: 1px solid var(--border); border-radius: 2px; background: #fff; color: var(--text); font-size: 5px; }
    .form-actions { grid-column: 1 / -1; display: flex; gap: 5px; margin-top: 1px; }
    .button-dark { border-color: var(--ink); background: var(--ink); color: #fff; }
    .stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 6px; margin-bottom: 7px; }
    .stat { min-height: 35px; padding: 6px; border: 1px solid var(--border); border-radius: 3px; background: #fff; }
    .stat span { display: inline-block; padding: 2px 4px; border-radius: 2px; background: #e9f5ee; color: var(--green); font-size: 4px; font-weight: 700; }
    .stat:nth-child(2) span { background: #fff3e6; color: var(--orange); }
    .stat:nth-child(3) span { background: #eef1f5; color: var(--text); }
    .stat:nth-child(4) span { background: #fff0f0; color: var(--red); }
    .stat strong { display: block; margin-top: 4px; color: var(--ink); font-size: 9px; }
    .table-wrap { overflow-x: auto; border: 1px solid var(--border); border-radius: 3px; }
    table { width: 100%; border-collapse: collapse; text-align: left; white-space: nowrap; }
    th { padding: 5px 6px; background: #f0f1f3; color: var(--muted); font-size: 4.5px; font-weight: 700; }
    td { padding: 5px 6px; border-top: 1px solid #edf0f3; color: var(--text); font-size: 4.5px; }
    td:first-child { color: var(--ink); }
    .status { padding: 2px 4px; border-radius: 2px; background: #e9f5ee; color: var(--green); font-size: 4px; font-weight: 700; }
    .status.pending { background: #fff3e6; color: var(--orange); }
    .status.cancelled { background: #fff0f0; color: var(--red); }
    .table-total { padding: 5px 7px; border: 1px solid var(--border); border-top: 0; color: var(--ink); font-size: 5px; font-weight: 700; text-align: right; }
    .empty-state { padding: 12px; color: var(--muted); font-size: 6px; text-align: center; }
    .footer { padding: 18px 6% 7px; background: #f1f2f4; }
    .footer-grid { display: grid; grid-template-columns: 1.6fr 1fr 1fr 1fr; gap: 14px; }
    .footer h2 { margin: 0 0 5px; color: var(--ink); font-size: 5px; }
    .footer p, .footer li { color: var(--text); font-size: 4.5px; }
    .footer p { max-width: 105px; margin: 0; }
    .footer ul { margin: 0; padding: 0; list-style: none; }
    .footer li { margin-bottom: 2px; }
    .footer-bottom { display: flex; justify-content: space-between; gap: 8px; margin-top: 10px; padding-top: 5px; border-top: 1px solid var(--line); color: var(--muted); font-size: 4px; }

    @media (min-width: 900px) {
      .header { height: 48px; }
      .logo { font-size: 13px; }
      .nav { gap: 21px; }
      .nav a { font-size: 9px; }
      .layout { margin-top: 24px; grid-template-columns: 180px minmax(0, 1fr); gap: 28px; }
      .sidebar { padding: 13px; }
      .avatar { width: 50px; height: 50px; font-size: 12px; }
      .side-name { font-size: 9px; }
      .side-email { font-size: 7px; }
      .side-menu a { padding: 7px; font-size: 8px; }
      .page-heading h1 { font-size: 15px; }
      .page-heading p { font-size: 8px; }
      .button { padding: 6px 10px; font-size: 8px; }
      .section { margin-bottom: 18px; }
      .section-title { margin-bottom: 8px; padding-bottom: 6px; font-size: 11px; }
      .panel { padding: 12px; }
      .overview-grid { gap: 9px 18px; }
      .info-item small { font-size: 7px; }
      .info-item strong { font-size: 8px; }
      .profile-form label { font-size: 7px; }
      .profile-form input { height: 25px; font-size: 8px; }
      .profile-panel h3 { font-size: 9px; }
      .stat { min-height: 52px; padding: 9px; }
      .stat span { font-size: 6px; }
      .stat strong { font-size: 13px; }
      th, td { padding: 7px 9px; font-size: 7px; }
      .status { font-size: 6px; }
      .empty-state { font-size: 8px; }
      .footer { padding-top: 27px; }
      .footer h2 { font-size: 7px; }
      .footer p, .footer li { font-size: 7px; }
      .footer-bottom { font-size: 6px; }
    }
    @media (max-width: 600px) {
      .header { height: auto; min-height: 36px; padding: 7px 10px; gap: 8px; }
      .nav { gap: 6px; }
      .nav a { font-size: 5px; }
      .layout { width: calc(100% - 20px); grid-template-columns: 1fr; gap: 10px; margin-top: 10px; }
      .sidebar { display: flex; align-items: center; gap: 8px; padding: 6px; }
      .avatar { width: 25px; height: 25px; margin: 0; flex: 0 0 auto; }
      .side-name, .side-email { text-align: left; }
      .side-name { font-size: 6px; }
      .side-email { margin: 1px 0 0; font-size: 5px; }
      .side-menu { display: flex; flex-wrap: wrap; gap: 3px; margin-left: auto; }
      .side-menu a { padding: 4px; font-size: 5px; }
      .overview-grid { gap: 6px; }
      .stats { gap: 4px; }
      .stat { min-height: 30px; padding: 4px; }
      .footer-grid { grid-template-columns: 1fr 1fr; gap: 12px; }
    }
    @media (max-width: 380px) {
      .nav { gap: 4px; }
      .nav a { font-size: 4px; }
      .side-menu { display: none; }
      .overview-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .profile-form { grid-template-columns: 1fr; }
      .form-actions { grid-column: 1; }
    }
    .header { height: 52px; padding: 0 max(24px, calc((100% - 1140px) / 2)); }
    .logo { font-size: 14px; letter-spacing: .2px; }
    .nav { gap: 24px; }
    .nav a, .nav span, .nav button { font-size: 11px; white-space: nowrap; }
    .nav a:hover, .nav .active { color: var(--ink); text-decoration: underline; text-underline-offset: 4px; }
    .nav .search { width: 11px; height: 11px; }
    .nav .search::after { width: 5px; height: 1.5px; right: -4px; bottom: -2px; }
    @media (max-width: 760px) {
      .header { height: auto; min-height: 52px; padding: 8px 16px; flex-wrap: wrap; }
      .logo { font-size: 14px; }
      .nav { gap: 12px 18px; flex-wrap: wrap; }
      .nav a, .nav span, .nav button { font-size: 11px; }
    }
    @media (max-width: 420px) {
      .header { align-items: flex-start; }
      .nav { gap: 8px 12px; }
    }
  </style>
  <style>
:root {
  --ink: #111214;
  --yellow: #f3c928;
  --muted: #72747a;
  --line: #e8e8eb;
  --soft: #f5f5f6;
  --white: #fff;
  --dark: #111214;
  --radius: 16px;
  --shadow: 0 16px 42px #11121412;
}

* { box-sizing: border-box; }
html { scroll-behavior: smooth; scroll-padding-top: 100px; }
body {
  zoom: 1 !important;
  color: var(--ink) !important;
  background: #fff !important;
  font-family: Inter, Arial, sans-serif !important;
  font-size: 14px !important;
  line-height: 1.5 !important;
}
a { color: inherit; }
button, input, select, textarea { font: inherit; }
button, a { -webkit-tap-highlight-color: transparent; }
:focus-visible { outline: 2px solid var(--yellow); outline-offset: 3px; }

.site-header,
.header {
  position: sticky;
  top: 0;
  z-index: 20;
  min-height: 72px !important;
  height: auto !important;
  flex-basis: auto !important;
  padding: 12px max(24px, calc((100% - 1280px) / 2)) !important;
  gap: 20px;
  background: #ffffffed !important;
  border-bottom: 1px solid #ededf0 !important;
  box-shadow: 0 5px 20px #11121408;
  backdrop-filter: blur(18px);
}
.brand,
.logo {
  color: var(--ink) !important;
  font-size: 24px !important;
  font-weight: 950 !important;
  letter-spacing: -1.2px !important;
  white-space: nowrap;
}
.nav {
  align-items: center;
  gap: 18px !important;
}
.nav a,
.nav span,
.nav button,
.nav-logout {
  color: var(--ink) !important;
  font-size: 10px !important;
  font-weight: 850 !important;
  letter-spacing: .8px;
  text-transform: uppercase;
  white-space: nowrap;
}
.nav a:hover,
.nav a[aria-current="page"],
.nav a.active,
.nav button:hover {
  color: #a78500 !important;
  text-decoration: underline;
  text-underline-offset: 4px;
}

main { overflow: visible; }
h1, h2, h3, h4 { color: var(--ink); letter-spacing: -.35px; }
p { color: inherit; }
main > h1,
.page-heading h1,
.content > h1 { font-size: clamp(26px, 3vw, 42px) !important; line-height: 1.1; }

.page-wrap,
.browse-layout,
.layout,
.admin-layout,
.content-section,
.page-content {
  max-width: 1280px;
}
body.home-page > main {
  width: 100%;
  max-width: none;
  margin: 0;
}
.browse-layout,
.layout,
.admin-layout,
.page-content,
.page-wrap {
  margin-left: auto !important;
  margin-right: auto !important;
}

.panel,
.card,
.login-panel,
.vehicle-card,
.detail-card,
.booking-card,
.specs-card,
.filters,
.sidebar,
.stat,
.value-card,
.contact-card,
.profile-panel,
.table-wrap,
.panel-heading {
  border-color: var(--line) !important;
  border-radius: var(--radius) !important;
  box-shadow: 0 8px 24px #11121408;
}
.vehicle-card,
.card,
.panel,
.login-panel,
.booking-card,
.specs-card,
.detail-card,
.filters,
.sidebar,
.value-card,
.contact-card {
  background-color: var(--white) !important;
}
.vehicle-card:hover { transform: translateY(-4px); box-shadow: var(--shadow); }
.vehicle-image,
.hero-placeholder,
.gallery-main,
.hero-image,
.image-placeholder,
.promo-image {
  background-color: #ececef !important;
}

button,
input,
select,
textarea {
  border-radius: 9px;
}
input:not([type="checkbox"]):not([type="radio"]),
select,
textarea {
  border-color: var(--line) !important;
}
input:not([type="checkbox"]):not([type="radio"]),
select,
textarea {
  background-color: #f7f7f8;
}
input:focus,
select:focus,
textarea:focus {
  border-color: #d1ad18 !important;
  outline: none;
}
button,
.button,
.button-dark,
.action.primary,
.submit,
.rent-button,
.feedback-form button {
  border: 1px solid var(--yellow) !important;
  border-radius: 99px !important;
  background: var(--yellow) !important;
  color: var(--ink) !important;
  font-weight: 900 !important;
  letter-spacing: .5px;
  cursor: pointer;
  transition: transform .2s, background .2s;
}
button:hover,
.button:hover,
.action:hover,
.rent-button:hover,
.feedback-form button:hover {
  transform: translateY(-2px);
  background: #e7bd19 !important;
  color: var(--ink) !important;
}
.nav button,
.nav-logout {
  border: 0 !important;
  background: transparent !important;
  box-shadow: none !important;
}
.action,
.details-button,
.back-link {
  border-radius: 99px !important;
  border-color: var(--line) !important;
}
.badge,
.status,
.tag {
  border-radius: 99px !important;
}
.badge.approved,
.status:not(.pending):not(.cancelled) {
  background: #f5e9ae !important;
  color: #5a4900 !important;
}
.section-title,
.page-heading,
.section-heading { border-color: var(--line) !important; }
.empty-state,
.empty-row { color: var(--muted) !important; }
table th { background: var(--soft) !important; }
table td { border-color: var(--line) !important; }

.site-footer,
.footer {
  padding: 42px max(24px, calc((100% - 1280px) / 2)) 18px !important;
  background: var(--dark) !important;
  color: #fff !important;
  border-top: 0 !important;
  font-size: 14px !important;
  line-height: 1.6 !important;
}
.site-footer h2,
.site-footer h3,
.footer h2,
.footer h3 {
  color: var(--yellow) !important;
  font-size: 13px !important;
  font-weight: 900;
  letter-spacing: 1px;
  text-transform: uppercase;
}
.site-footer p,
.site-footer li,
.footer p,
.footer li {
  color: #e2e2e6 !important;
  font-size: 14px !important;
  line-height: 1.6 !important;
}
.site-footer a,
.footer a { color: #eee !important; }
.site-footer a:hover,
.footer a:hover { color: var(--yellow) !important; }
.footer-brand,
.site-footer .logo,
.footer .logo {
  color: #fff !important;
  font-size: 18px !important;
}
.footer-bottom,
.footer-bottom span {
  color: #d0d0d5 !important;
  border-color: #343539 !important;
  font-size: 12px !important;
  line-height: 1.6 !important;
}

body.login-page > main {
  min-height: calc(100vh - 72px);
  padding: 56px 20px !important;
  align-items: center;
  background: #f3f3f5;
}
body.login-page .card {
  width: min(612px, 100%);
  max-width: none;
  padding: 40px;
  overflow: visible;
  border: 1px solid #e2e2e7 !important;
  border-radius: 22px !important;
  box-shadow: 0 18px 48px #1112140d;
}
body.login-page .tabs {
  display: flex;
  gap: 4px;
  margin-bottom: 34px;
  padding: 6px;
  border-radius: 999px;
  background: #f1f1f3;
}
body.login-page .tab,
body.login-page .tab.active {
  min-height: 46px;
  padding: 10px 16px;
  border: 0 !important;
  border-radius: 999px;
  background: transparent !important;
  color: #111214 !important;
  font-size: 14px !important;
  font-weight: 850;
  letter-spacing: .3px;
  transform: none !important;
}
body.login-page .tab.active {
  background: var(--yellow) !important;
}
body.login-page .tab:not(.active):hover {
  background: #e7e7eb !important;
}
body.login-page .panels {
  padding: 0;
}
body.login-page .panel h2 {
  margin: 0 0 12px;
  color: #111214;
  font-size: 34px !important;
  font-weight: 800;
  line-height: 1.15;
}
body.login-page .login-intro {
  margin: 0 0 24px;
  color: #28292d;
  font-size: 18px;
  line-height: 1.5;
}
body.login-page label.field {
  margin-bottom: 16px;
}
body.login-page label.field span {
  margin-bottom: 8px;
  color: #111214;
  font-size: 13px !important;
  font-weight: 850;
  letter-spacing: .3px;
  text-transform: uppercase;
}
body.login-page input[type="text"],
body.login-page input[type="email"],
body.login-page input[type="tel"],
body.login-page input[type="password"] {
  height: 56px;
  padding: 0 16px;
  border: 1px solid #dedee4;
  border-radius: 12px;
  background: #f7f7f8;
  color: #111214;
  font-size: 14px;
}
body.login-page input::placeholder {
  color: #73747a;
  opacity: 1;
}
body.login-page .row {
  margin: 4px 0 26px;
  color: #55565d;
  font-size: 14px;
}
body.login-page .check {
  gap: 8px;
  color: #55565d;
  font-size: 14px;
}
body.login-page .check input {
  width: 16px;
  height: 16px;
}
body.login-page .underline {
  color: #55565d;
  font-weight: 500;
}
body.login-page .btn-dark {
  display: flex;
  min-height: 54px;
  height: auto;
  align-items: center;
  justify-content: center;
  gap: 8px;
  border: 0;
  border-radius: 999px;
  background: var(--yellow);
  color: #111214;
  font-size: 15px;
  font-weight: 850;
}
body.login-page .btn-dark:hover {
  background: #e7bd1e;
}
body.login-page .msg {
  margin-bottom: 16px;
  padding: 10px 12px;
  font-size: 13px;
}
body.login-page .msg.error {
  border-color: #b42318;
  background: #fff1f0;
  color: #8a1c13;
}

.hero { background-color: #292a2d !important; }
.hero h1 span { color: var(--yellow) !important; }
.section-heading h2,
.section-head h2 { text-transform: uppercase; }
.step-number,
.stepnum,
.avatar {
  border-color: var(--line) !important;
  background: var(--yellow) !important;
  color: var(--ink) !important;
}
.filter-option input,
.check input,
input[type="checkbox"],
input[type="radio"] { accent-color: #d1ad18; }

@media (max-width: 760px) {
  .site-header,
  .header {
    min-height: 64px !important;
    padding: 10px 16px !important;
    flex-wrap: wrap;
  }
  .brand,
  .logo { font-size: 22px !important; }
  .nav { gap: 10px 14px !important; flex-wrap: wrap; }
  .nav a,
  .nav span,
  .nav button { font-size: 9px !important; }
  .site-footer,
  .footer { padding: 32px 18px 16px !important; }
}
@media (max-width: 480px) {
  .site-header,
  .header { align-items: flex-start; }
  .nav { gap: 8px 10px !important; }
  .site-footer .footer-top,
  .footer-grid,
  .footer-top { grid-template-columns: 1fr 1fr !important; }
  body.login-page > main { min-height: auto; padding: 32px 16px !important; }
  body.login-page .card { padding: 24px; border-radius: 18px !important; }
  body.login-page .tabs { margin-bottom: 28px; }
  body.login-page .panel h2 { font-size: 30px !important; }
  body.login-page .login-intro { font-size: 16px; }
  body.login-page .row,
  body.login-page .check { font-size: 13px; }
}

body.home-page .hero {
  min-height: 582px;
  position: relative;
  display: block;
  overflow: visible;
  background: linear-gradient(110deg, #101113 0%, #1b1c1f 62%, #232427 100%) !important;
}
body.home-page .home-hero-inner {
  width: min(1344px, calc(100% - 64px));
  min-height: 582px;
  position: relative;
  display: flex;
  align-items: flex-start;
  padding: 88px 0 100px;
}
body.home-page .hero-copy { max-width: 700px; }
body.home-page .hero-eyebrow,
body.home-page .hero-tag {
  display: flex;
  align-items: center;
  gap: 9px;
  color: #fff;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: 1.6px;
  text-transform: uppercase;
}
body.home-page .hero-eyebrow span,
body.home-page .hero-tag span {
  width: 22px;
  height: 2px;
  flex: 0 0 auto;
  background: var(--yellow);
}
body.home-page .hero-copy h1 {
  margin: 28px 0 14px;
  color: #fff;
  font-size: clamp(58px, 5vw, 82px);
  font-weight: 950;
  line-height: .97;
  letter-spacing: -2.8px;
  text-transform: uppercase;
}
body.home-page .hero-copy h1 span { color: var(--yellow) !important; }
body.home-page .hero-copy > p {
  max-width: 490px;
  margin: 0 0 28px;
  color: #f0f0f1;
  font-size: 14px;
  line-height: 1.55;
}
body.home-page .hero-actions {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 12px;
}
body.home-page .hero-actions .button-dark,
body.home-page .home-quick-search .button-dark {
  min-height: 40px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 0 20px;
  border: 0 !important;
  border-radius: 999px !important;
  background: var(--yellow) !important;
  color: #111214 !important;
  font-size: 10px;
  font-weight: 950;
  letter-spacing: 1px;
  text-transform: uppercase;
}
body.home-page .hero-actions .button-dark:hover,
body.home-page .home-quick-search .button-dark:hover {
  background: #e7bd19 !important;
}
body.home-page .button-outline {
  min-height: 40px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0 20px;
  border: 1px solid #77787c;
  border-radius: 999px;
  color: #fff;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: .8px;
  text-transform: uppercase;
}
body.home-page .button-outline:hover { border-color: var(--yellow); color: var(--yellow); }
body.home-page .hero-tag {
  position: absolute;
  right: 0;
  bottom: 56px;
  font-size: 9px;
  letter-spacing: 1px;
}
body.home-page .home-quick-search {
  width: min(966px, calc(100% - 48px));
  min-height: 80px;
  position: absolute;
  z-index: 2;
  right: auto;
  bottom: -44px;
  left: 50%;
  display: grid;
  grid-template-columns: 1.15fr 1fr 1fr 1fr auto;
  align-items: center;
  gap: 0;
  margin: 0;
  padding: 12px;
  transform: translateX(-50%);
  border: 1px solid #e2e2e6;
  border-radius: 15px;
  background: #fff;
  box-shadow: 0 16px 35px #11121412;
}
body.home-page .home-quick-search label {
  min-width: 0;
  display: block;
  padding: 4px 18px;
  border-right: 1px solid #e4e4e8;
  color: #111214;
  font-size: 9px;
  font-weight: 900;
  letter-spacing: 1px;
  text-transform: uppercase;
}
body.home-page .home-quick-search label:first-child { padding-left: 18px; }
body.home-page .home-quick-search label:nth-last-of-type(1) { border-right: 1px solid #e4e4e8; }
body.home-page .home-quick-search input,
body.home-page .home-quick-search select {
  width: 100%;
  height: 28px;
  margin-top: 3px;
  padding: 0 3px;
  border: 0 !important;
  border-radius: 0;
  background: transparent !important;
  color: #60616a;
  font-size: 11px;
  font-weight: 400;
  letter-spacing: 0;
  text-transform: none;
}
body.home-page .home-quick-search .search-submit {
  min-height: 40px;
  margin-left: 12px;
  white-space: nowrap;
}
body.home-page .content-section#about { padding-top: 92px; }

@media (max-width: 1100px) {
  body.home-page .home-hero-inner { width: calc(100% - 72px); }
  body.home-page .home-quick-search { width: calc(100% - 72px); }
}
@media (max-width: 760px) {
  body.home-page .hero,
  body.home-page .home-hero-inner { min-height: 570px; }
  body.home-page .home-hero-inner {
    width: calc(100% - 48px);
    padding: 66px 0 112px;
  }
  body.home-page .hero-copy { max-width: 620px; }
  body.home-page .hero-copy h1 { font-size: clamp(52px, 10vw, 72px); }
  body.home-page .hero-tag { display: none; }
  body.home-page .home-quick-search {
    width: calc(100% - 40px);
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px 0;
    bottom: -98px;
    padding: 12px;
  }
  body.home-page .home-quick-search label { padding: 6px 12px; }
  body.home-page .home-quick-search label:nth-of-type(2) { border-right: 0; }
  body.home-page .home-quick-search label:nth-of-type(4) { border-right: 0; }
  body.home-page .home-quick-search .search-submit {
    grid-column: 1 / -1;
    margin: 4px 0 0;
  }
  body.home-page .content-section#about { padding-top: 144px; }
}
@media (max-width: 480px) {
  body.home-page .hero,
  body.home-page .home-hero-inner { min-height: 520px; }
  body.home-page .home-hero-inner {
    width: calc(100% - 36px);
    padding: 55px 0 112px;
  }
  body.home-page .hero-eyebrow { font-size: 8px; letter-spacing: 1.1px; }
  body.home-page .hero-copy h1 {
    margin: 24px 0 16px;
    font-size: clamp(46px, 12vw, 58px);
    letter-spacing: -2px;
  }
  body.home-page .hero-copy > p { max-width: 360px; font-size: 13px; }
  body.home-page .hero-actions { gap: 8px; }
  body.home-page .hero-actions .button-dark,
  body.home-page .button-outline { min-height: 38px; padding: 0 13px; font-size: 9px; }
  body.home-page .home-quick-search { bottom: -105px; }
  body.home-page .home-quick-search label { padding: 5px 8px; font-size: 8px; }
  body.home-page .home-quick-search input,
  body.home-page .home-quick-search select { font-size: 10px; }
  body.home-page .content-section#about { padding-top: 146px; }
}

.site-header,
.header {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  flex-wrap: nowrap;
  gap: 20px !important;
}
.site-header > .brand,
.site-header > .logo,
.header > .brand,
.header > .logo {
  flex: 0 0 auto;
  color: var(--ink) !important;
  font-size: 24px !important;
  font-weight: 950 !important;
  letter-spacing: -1.2px !important;
  line-height: 1;
}
.site-header > nav,
.header > nav {
  display: flex !important;
  flex: 0 1 auto;
  align-items: center;
  justify-content: flex-end;
  flex-wrap: nowrap;
  gap: 18px !important;
  min-width: 0;
  margin-left: auto;
}
.site-header > nav a,
.header > nav a,
.site-header > nav span,
.header > nav span,
.site-header > nav button,
.header > nav button {
  color: #292a2d !important;
  font-size: 10px !important;
  font-weight: 850 !important;
  letter-spacing: .7px;
  line-height: 1.4;
  text-transform: uppercase;
  white-space: nowrap;
}
.site-header > nav a:hover,
.header > nav a:hover,
.site-header > nav a.active,
.header > nav a.active,
.site-header > nav a[aria-current="page"],
.header > nav a[aria-current="page"] {
  color: #a78500 !important;
  text-decoration: underline;
  text-underline-offset: 4px;
}
.site-header > nav form,
.header > nav form {
  display: contents !important;
  margin: 0;
}
.site-header > nav button,
.header > nav button {
  padding: 0 !important;
  border: 0 !important;
  border-radius: 0 !important;
  background: transparent !important;
  box-shadow: none !important;
  cursor: pointer;
  transform: none !important;
}
.site-header > nav button:hover,
.header > nav button:hover {
  background: transparent !important;
  color: #a78500 !important;
  text-decoration: underline;
  text-underline-offset: 4px;
}
.site-header > nav .search-mark,
.site-header > nav .search-icon,
.site-header > nav .search,
.header > nav .search-mark,
.header > nav .search-icon,
.header > nav .search {
  width: 12px !important;
  height: 12px !important;
  position: relative;
  display: inline-block;
  flex: 0 0 12px;
  margin: 0 3px 0 0;
  border: 1.5px solid #292a2d;
  border-radius: 50%;
}
.site-header > nav .search-mark::after,
.site-header > nav .search-icon::after,
.site-header > nav .search::after,
.header > nav .search-mark::after,
.header > nav .search-icon::after,
.header > nav .search::after {
  content: "";
  width: 5px;
  height: 1.5px;
  position: absolute;
  right: -4px;
  bottom: -2px;
  background: #292a2d;
  transform: rotate(45deg);
}
@media (max-width: 760px) {
  .site-header,
  .header {
    align-items: flex-start !important;
    flex-wrap: wrap;
    gap: 12px !important;
  }
  .site-header > nav,
  .header > nav {
    flex: 1 0 100%;
    justify-content: flex-start;
    flex-wrap: wrap;
    gap: 10px 18px !important;
    margin-left: 0;
  }
}
@media (max-width: 420px) {
  .site-header > nav,
  .header > nav {
    gap: 8px 12px !important;
  }
}
  </style>
  <style>
    body.profile-page .layout {
      width: min(1480px, calc(100% - 56px)) !important;
      margin: 32px auto 48px !important;
      grid-template-columns: 300px minmax(0, 1fr) !important;
      gap: 28px !important;
      min-height: 500px;
    }
    body.profile-page .account-heading {
      width: min(1480px, calc(100% - 56px));
      margin: 30px auto 0;
    }
    body.profile-page .account-eyebrow {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 8px;
      color: #4b5563;
      font-size: 10px;
      font-weight: 900;
      letter-spacing: 1.7px;
      text-transform: uppercase;
    }
    body.profile-page .account-eyebrow::before {
      width: 22px;
      height: 2px;
      background: var(--yellow);
      content: "";
    }
    body.profile-page .account-heading h1 {
      margin: 0;
      color: var(--ink);
      font-size: clamp(30px, 3vw, 40px);
      font-weight: 900;
      line-height: 1.1;
      text-transform: uppercase;
    }
    body.profile-page .account-heading p {
      margin: 8px 0 0;
      color: #72747a;
      font-size: 13px;
    }
    body.profile-page .sidebar {
      padding: 20px !important;
      border-radius: 18px !important;
    }
    body.profile-page .sidebar-heading {
      grid-column: 1 / -1;
      margin: 0 0 8px;
      color: #111214;
      font-size: 14px;
      font-weight: 800;
    }
    body.profile-page .section[hidden] { display: none !important; }
    body.profile-page .profile-card,
    body.profile-page .account-panel { border: 1px solid #e5e5e9 !important; border-radius: 18px !important; }
    body.profile-page .account-panel { padding: 30px 32px !important; }
    body.profile-page .account-panel-heading {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 18px;
      margin-bottom: 28px;
    }
    body.profile-page .account-panel-heading h2 {
      margin: 0;
      font-size: 26px;
      font-weight: 800;
    }
    body.profile-page .account-badge {
      padding: 7px 13px;
      border-radius: 999px;
      background: #f5eab9;
      color: #111214;
      font-size: 11px;
      font-weight: 800;
      white-space: nowrap;
    }
    body.profile-page .record-empty-badge {
      display: inline-block;
      margin-top: 3px;
      padding: 2px 6px;
      border-radius: 4px;
      background: #f1f2f4;
      color: #60646d;
      font-size: 9px;
      font-weight: 600;
      line-height: 1.3;
      white-space: nowrap;
    }
    body.profile-page .account-form { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
    body.profile-page .account-form label {
      color: #111214;
      font-size: 12px !important;
      font-weight: 900;
      letter-spacing: .7px;
      text-transform: uppercase;
    }
    body.profile-page .profile-form.account-form input {
      height: 55px;
      margin-top: 8px;
      padding: 0 16px;
      border-radius: 11px;
      background: #f7f7f8;
      font-size: 14px !important;
      font-weight: 400;
      letter-spacing: 0;
      text-transform: none;
    }
    body.profile-page .profile-form.account-form input::placeholder { color: #73747a; opacity: 1; }
    body.profile-page .account-form .form-actions { grid-column: 1 / -1; }
    body.profile-page .account-form .button-dark {
      min-width: 166px;
      min-height: 52px;
      border-radius: 999px !important;
      border-color: #111214 !important;
      background: #111214 !important;
      color: #fff !important;
      font-size: 12px !important;
      font-weight: 900;
      letter-spacing: .8px;
      text-transform: uppercase;
    }
    body.profile-page .account-form .button:not(.button-dark) { min-height: 52px; border-radius: 999px !important; }
    body.profile-page .account-panel .section-title {
      border-bottom: 0;
      font-size: 27px !important;
    }
    body.profile-page .profile-form.account-form label { color: #111214; font-size: 12px !important; }
    body.profile-page .side-menu a { border: 0; background: transparent; font-weight: 750; }
    body.profile-page .side-menu a.active { background: #f0f0f2; }
    body.profile-page .sidebar > .avatar,
    body.profile-page .sidebar > div { display: none; }
    body.profile-page .profile-overview-info { margin-top: 20px; }
    body.profile-page .member-since {
      margin: 16px 0 0;
      color: #72747a;
      font-size: 12px;
    }
    body.profile-page .charge-summary { display: grid; gap: 0; }
    body.profile-page .charge-summary-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      padding: 15px 0;
      border-bottom: 1px solid #e5e5e9;
      color: #111214;
      font-size: 14px;
    }
    body.profile-page .charge-summary-row strong { white-space: nowrap; }
    body.profile-page .charge-summary-total { border-bottom: 0; font-size: 20px; font-weight: 850; }
    body.profile-page > .footer {
      width: 100%;
      padding: 54px max(32px, calc((100% - 1560px) / 2)) 20px !important;
    }
    body.profile-page > .footer .footer-grid {
      grid-template-columns: 2fr 1.1fr 1.1fr 1.1fr;
      gap: 48px;
      max-width: 1560px;
      margin: 0 auto;
    }
    body.profile-page > .footer .footer-grid > div { min-width: 0; }
    body.profile-page > .footer .footer-grid > div:first-child p {
      width: min(280px, 100%);
      max-width: none;
      margin-top: 4px;
      overflow-wrap: normal;
    }
    body.profile-page > .footer .footer-grid h2 { margin: 0 0 8px; }
    body.profile-page > .footer .footer-grid ul { margin: 0; padding: 0; }
    body.profile-page > .footer .footer-grid li { margin-bottom: 3px; }
    body.profile-page > .footer .footer-bottom {
      max-width: 1560px;
      margin: 16px auto 0;
      padding-top: 9px;
    }
    body.profile-page .avatar {
      width: 56px !important;
      height: 56px !important;
      margin-bottom: 12px !important;
      font-size: 18px !important;
    }
    body.profile-page .profile-picture { object-fit: cover; }
    body.profile-page .side-name { font-size: 16px !important; }
    body.profile-page .side-email { margin: 4px 0 16px !important; font-size: 12px !important; }
    body.profile-page .side-menu { gap: 7px !important; }
    body.profile-page .side-menu a {
      padding: 10px 12px !important;
      border-radius: 9px !important;
      font-size: 13px !important;
      line-height: 1.4;
    }
    body.profile-page .content { font-size: 15px !important; }
    body.profile-page .page-heading { gap: 16px; margin-bottom: 20px; }
    body.profile-page .page-heading h1 {
      margin: 0;
      font-size: 34px !important;
      line-height: 1.15 !important;
    }
    body.profile-page .page-heading p {
      margin-top: 6px;
      font-size: 14px !important;
      line-height: 1.5;
    }
    body.profile-page .button {
      padding: 10px 16px !important;
      font-size: 13px !important;
      white-space: nowrap;
    }
    body.profile-page .section { margin-bottom: 28px; }
    body.profile-page .section-title {
      margin-bottom: 12px;
      padding-bottom: 9px;
      font-size: 19px !important;
      line-height: 1.3;
    }
    body.profile-page .panel { padding: 20px !important; }
    body.profile-page .overview-grid { gap: 18px 24px; }
    body.profile-page .info-item small { margin-bottom: 4px; font-size: 12px !important; }
    body.profile-page .info-item strong {
      overflow-wrap: anywhere;
      font-size: 15px !important;
      line-height: 1.45;
      white-space: normal;
    }
    body.profile-page .profile-panel { margin-top: 18px; }
    body.profile-page .profile-panel h3 { margin-bottom: 12px; font-size: 16px !important; }
    body.profile-page .profile-form { gap: 12px 16px; }
    body.profile-page .profile-form label { font-size: 13px !important; }
    body.profile-page .profile-form input {
      height: 44px;
      margin-top: 6px;
      padding: 0 12px;
      border-radius: 9px;
      font-size: 14px !important;
    }
    body.profile-page .form-actions { gap: 10px; margin-top: 4px; }
    body.profile-page .form-actions button { min-height: 42px; }
    body.profile-page .stats { gap: 12px; margin: 14px 0 18px; }
    body.profile-page .stat { min-height: 74px; padding: 14px; }
    body.profile-page .stat span { padding: 4px 7px; font-size: 10px !important; }
    body.profile-page .stat strong { margin-top: 8px; font-size: 21px !important; }
    body.profile-page .table-wrap { border-radius: 12px; }
    body.profile-page table { min-width: 620px; }
    body.profile-page th,
    body.profile-page td {
      padding: 12px 14px;
      font-size: 12px !important;
      line-height: 1.5;
    }
    body.profile-page .status { padding: 4px 8px; font-size: 11px !important; }
    body.profile-page .table-total { padding: 12px; font-size: 13px !important; }
    body.profile-page .empty-state { padding: 20px; font-size: 14px !important; }
    body.profile-page .panel > .side-email { font-size: 13px !important; line-height: 1.5; }
    @media (max-width: 760px) {
      body.profile-page .account-heading {
        width: calc(100% - 32px);
        margin-top: 24px;
      }
      body.profile-page .layout {
        width: calc(100% - 32px) !important;
        margin: 20px auto 36px !important;
        grid-template-columns: minmax(0, 1fr) !important;
        gap: 20px !important;
      }
      body.profile-page .sidebar {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        align-items: center;
        gap: 4px 14px;
      }
      body.profile-page .side-menu {
        grid-column: 1 / -1;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin: 12px 0 0;
      }
      body.profile-page .overview-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 480px) {
      body.profile-page .account-heading {
        width: calc(100% - 24px);
      }
      body.profile-page .layout { width: calc(100% - 24px) !important; }
      body.profile-page .sidebar,
      body.profile-page .panel { padding: 14px !important; }
      body.profile-page .page-heading { flex-direction: column; }
      body.profile-page .page-heading h1 { font-size: 29px !important; }
      body.profile-page .overview-grid { grid-template-columns: 1fr 1fr; gap: 14px; }
      body.profile-page .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      body.profile-page .profile-form { grid-template-columns: 1fr; }
      body.profile-page .form-actions { grid-column: 1; }
      body.profile-page .account-panel { padding: 20px 16px !important; }
      body.profile-page .account-form { grid-template-columns: 1fr; }
      body.profile-page .account-form .form-actions { grid-column: 1; }
      body.profile-page .account-panel-heading h2 { font-size: 22px; }
    }
    @media (max-width: 760px) {
      body.profile-page > .footer { padding: 36px 24px 16px !important; }
      body.profile-page > .footer .footer-grid { gap: 24px; }
    }
    @media (max-width: 480px) {
      body.profile-page > .footer .footer-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 22px 16px; }
      body.profile-page > .footer .footer-grid > div:first-child { grid-column: 1 / -1; }
      body.profile-page > .footer .footer-grid > div:first-child p { width: min(280px, 100%); }
      body.profile-page > .footer .footer-bottom { align-items: flex-start; flex-direction: column; }
    }
  </style>
</head>
<body class="profile-page">
  <header class="header">
    <a class="logo" href="index.php">AUTORIDE</a>
    <nav class="nav" aria-label="Main navigation">
      <a href="index.php">Home</a>
      <a href="brows_vehicle.php">Browse Vehicle</a>
      <a href="rentflow.php">Rent</a>
      <a href="about_us.php">About Us</a>
      <?php if (!empty($_SESSION['customer_user']['name'])): ?>
        <form action="log_in.php" method="post" style="display:inline">
          <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
          <input type="hidden" name="action" value="logout">
          <button class="button" type="submit">Logout</button>
        </form>
      <?php else: ?>
        <a href="log_in.php">Login/Register</a>
      <?php endif; ?>
      <a class="active" href="user_profile.php" aria-current="page">Profile</a>
      <a class="search" href="brows_vehicle.php" aria-label="Search vehicles" title="Search vehicles"></a>
    </nav>
  </header>

  <div class="account-heading">
    <div class="account-eyebrow">Your Account</div>
    <h1>User Profile</h1>
    <p>View and manage your profile, rental history, rental status, and charges.</p>
  </div>

  <div class="layout">
    <aside class="sidebar">
      <?php if ($profile['profile_picture'] !== ''): ?>
        <img class="avatar profile-picture" src="<?php echo e($profile['profile_picture']); ?>" alt="<?php echo e($profile['full_name']); ?> profile picture">
      <?php else: ?>
        <div class="avatar" aria-label="<?php echo e($profile['full_name']); ?> profile initials"><?php echo e($initials); ?></div>
      <?php endif; ?>
      <div>
        <div class="side-name"><?php echo e($profile['full_name']); ?></div>
        <div class="side-email"><?php echo e($profile['email']); ?></div>
      </div>
      <nav class="side-menu" aria-label="Profile sections">
        <h2 class="sidebar-heading">My account</h2>
        <a class="active" href="#overview">User Profile</a>
        <a href="#rentals">View Rental History</a>
        <a href="#rental-status">View Rental Status</a>
        <a href="#rental-charges">View Rental Charges</a>
        <a href="#charges">Billing &amp; Payment History</a>
        <a href="#reviews">My Reviews</a>
        <a href="brows_vehicle.php">Browse Vehicles</a>
      </nav>
    </aside>

    <main class="content">
      <section class="section" id="overview">
        <div class="panel account-panel profile-card">
          <div class="account-panel-heading">
            <h2>User profile</h2>
            <span class="account-badge"><?php echo !empty($customer['is_demo']) ? 'Example account' : 'Account'; ?></span>
          </div>
            <?php if ($profileMessage !== ''): ?><p role="status"><?php echo e($profileMessage); ?></p><?php endif; ?>
            <?php if ($profileError !== ''): ?><p role="alert"><?php echo e($profileError); ?></p><?php endif; ?>
            <form class="profile-form account-form" method="post" action="user_profile.php#overview">
              <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
              <input type="hidden" name="action" value="update_profile">
              <label>Full Name<input type="text" name="full_name" value="<?php echo e($profile['full_name']); ?>" placeholder="Enter full name" required minlength="2" maxlength="120"></label>
              <label>Email<input type="email" name="email" value="<?php echo e($profile['email']); ?>" placeholder="Enter email" required maxlength="254"></label>
              <label>Phone Number<input type="tel" name="phone" value="<?php echo e($profile['phone']); ?>" placeholder="Enter phone number" required pattern="(\+63|0)9[0-9]{9}"></label>
              <label>Address<input type="text" name="address" value="<?php echo e($profile['address']); ?>" placeholder="Enter address" maxlength="255"></label>
              <div class="form-actions"><button class="button button-dark" type="submit">Save Profile</button><button class="button" type="reset">Cancel</button></div>
            </form>
            <p class="member-since">
              Member since: <?php echo $profile['created_at'] !== '' && strtotime($profile['created_at']) !== false ? e(date('F j, Y', strtotime($profile['created_at']))) : 'Not available'; ?>
              · Account status: <span class="status"><?php echo e(ucfirst($profile['status'])); ?></span>
            </p>
            <?php if ($showDemoRecords): ?><p class="member-since">This example account includes example history. Rows marked “Example” are not actual rentals, payments, or reviews.</p><?php endif; ?>
        </div>
      </section>

      <section class="section" id="rentals">
        <div class="panel account-panel">
          <h2 class="section-title">View rental history</h2>
          <div class="table-wrap">
            <table>
              <thead><tr><th>VEHICLE</th><th>RENTAL DATES</th><th>DURATION</th><th>TOTAL COST</th><th>STATUS</th></tr></thead>
              <tbody>
                <?php if ($profileBookings): ?>
                  <?php foreach ($profileBookings as $booking): ?>
                    <tr>
                      <td><?php echo e($booking['vehicle_name']); ?><?php if (!empty($booking['is_sample']) || !empty($booking['is_demo'])): ?><br><span class="record-empty-badge">Example</span><?php endif; ?></td>
                      <td><?php echo e($booking['pickup_date']); ?> — <?php echo e($booking['return_date']); ?></td>
                      <td><?php echo (int) ($booking['rental_days'] ?? $booking['days'] ?? 0); ?> days</td>
                      <td><?php echo $formatMoney($booking['total_amount']); ?></td>
                      <?php $bookingStatus = $booking['status'] === 'confirmed' ? 'approved' : ($booking['status'] === 'requested' ? 'pending' : (in_array($booking['status'], ['cancelled', 'rejected'], true) ? 'cancelled' : '')); ?>
                      <td><span class="status <?php echo e($bookingStatus); ?>"><?php echo e($booking['status'] === 'confirmed' ? 'Approved' : ($booking['status'] === 'requested' ? 'Pending' : ucfirst($booking['status']))); ?></span></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td class="empty-state" colspan="5">No rental activity yet. Your requests and completed rentals will appear here.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <section class="section" id="rental-status">
        <div class="panel account-panel">
          <h2 class="section-title">View rental status</h2>
          <p class="side-email" style="text-align:left"><strong><?php echo count($bookings); ?> actual <?php echo count($bookings) === 1 ? 'rental' : 'rentals'; ?></strong> · Sample rows in rental history are excluded from these counts.</p>
          <div class="stats">
            <div class="stat"><span>ACTIVE</span><strong><?php echo $rentalCounts['active']; ?></strong></div>
            <div class="stat"><span>UPCOMING</span><strong><?php echo $rentalCounts['upcoming']; ?></strong></div>
            <div class="stat"><span>COMPLETED</span><strong><?php echo $rentalCounts['completed']; ?></strong></div>
            <div class="stat"><span>CANCELLED</span><strong><?php echo $rentalCounts['cancelled']; ?></strong></div>
          </div>
          <?php if ($showDemoRecords && $demoBookings): ?>
            <h3 class="section-title" style="margin-top:24px">Example rental statuses</h3>
            <div class="table-wrap">
              <table>
                <thead><tr><th>VEHICLE</th><th>RENTAL DATES</th><th>STATUS</th></tr></thead>
                <tbody>
                  <?php foreach ($demoBookings as $booking): ?>
                    <tr>
                      <td><?php echo e($booking['vehicle_name']); ?></td>
                      <td><?php echo e($booking['pickup_date']); ?> — <?php echo e($booking['return_date']); ?></td>
                      <td><span class="status"><?php echo e(ucfirst($booking['status'])); ?></span> <span class="record-empty-badge">Example</span></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </section>

      <section class="section" id="rental-charges">
        <div class="panel account-panel">
          <h2 class="section-title">View rental charges</h2>
          <p class="side-email" style="text-align:left">Example rental charges appear in the breakdown below for reference. Actual totals include account activity only; example charges are excluded.</p>
          <div class="charge-summary">
            <div class="charge-summary-row"><span>Vehicle rental</span><strong><?php echo $rentalCharges ? $formatMoney($vehicleRentalTotal) : '—'; ?></strong></div>
            <div class="charge-summary-row"><span>Additional charges</span><strong><?php echo $rentalCharges ? $formatMoney($additionalRentalChargesTotal) : '—'; ?></strong></div>
            <div class="charge-summary-row charge-summary-total"><span>Total actual charges</span><strong><?php echo $rentalCharges ? $formatMoney($totalCharges) : '₱—'; ?></strong></div>
          </div>
          <h3 class="section-title" style="margin-top:24px">Rental charge breakdown</h3>
          <div class="table-wrap">
            <table>
              <thead><tr><th>VEHICLE</th><th>DAYS</th><th>RENTAL FEE</th><th>DRIVER / INSURANCE</th><th>SECURITY DEPOSIT</th><th>TOTAL</th></tr></thead>
              <tbody>
                <?php if ($profileRentalCharges): ?>
                  <?php foreach ($profileRentalCharges as $booking): ?>
                    <?php $baseRentalFee = (float) $booking['daily_rate'] * (int) $booking['rental_days']; ?>
                    <tr>
                      <td><?php echo e($booking['vehicle_name']); ?><?php if (!empty($booking['is_sample']) || !empty($booking['is_demo'])): ?><br><span class="record-empty-badge">Example</span><?php endif; ?></td>
                      <td><?php echo (int) $booking['rental_days']; ?></td>
                      <td><?php echo $formatMoney($baseRentalFee); ?></td>
                      <td><?php echo $formatMoney((float) $booking['driver_fee'] + (float) $booking['insurance_fee']); ?></td>
                      <td><?php echo $formatMoney($booking['security_deposit']); ?></td>
                      <td><?php echo $formatMoney($booking['total_amount']); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td class="empty-state" colspan="6">No rental charges yet.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <div class="table-total">Total Actual Charges: <?php echo $formatMoney($totalCharges); ?></div>
        </div>
      </section>

      <section class="section" id="charges">
        <h2 class="section-title">Billing &amp; Charges</h2>
        <div class="panel">
          <p class="side-email" style="text-align:left">Payment summary totals use actual account activity only. Sample rows are shown in the tables but are not transactions.</p>
          <h3 class="section-title">Payment Status</h3>
          <div class="stats">
            <div class="stat"><span>PAID</span><strong><?php echo $formatMoney($paidTotal); ?></strong></div>
            <div class="stat"><span>OUTSTANDING</span><strong><?php echo $formatMoney($outstandingTotal); ?></strong></div>
            <div class="stat"><span>REFUNDED</span><strong><?php echo $formatMoney($refundedTotal); ?></strong></div>
          </div>
          <h3 class="section-title">Payment History</h3>
          <div class="table-wrap">
            <table>
              <thead><tr><th>REF. NO.</th><th>VEHICLE</th><th>DATE PAID</th><th>METHOD</th><th>STATUS</th><th>AMOUNT</th></tr></thead>
              <tbody>
                <?php if ($profilePayments): ?>
                  <?php foreach ($profilePayments as $payment): ?>
                    <tr>
                      <td><?php echo e($payment['reference_code'] ?: 'Payment'); ?></td>
                      <td><?php echo e($payment['vehicle_name']); ?><?php if (!empty($payment['is_sample']) || !empty($payment['is_demo'])): ?><br><span class="record-empty-badge">Example</span><?php endif; ?></td>
                      <td><?php echo e($payment['paid_at'] ? date('Y-m-d', strtotime($payment['paid_at'])) : '—'); ?></td>
                      <td><?php echo e(ucwords(str_replace('_', ' ', $payment['payment_method']))); ?></td>
                      <td><span class="status <?php echo in_array($payment['status'], ['pending', 'partial'], true) ? 'pending' : ($payment['status'] === 'failed' ? 'cancelled' : ''); ?>"><?php echo e(strtoupper($payment['status'])); ?></span></td>
                      <td><?php echo $formatMoney($payment['amount']); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td class="empty-state" colspan="6">No payment transactions yet.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <h3 class="section-title" style="margin-top:9px">Outstanding Balances</h3>
          <div class="table-wrap">
            <table>
              <thead><tr><th>VEHICLE</th><th>BOOKING</th><th>STATUS</th><th>BALANCE DUE</th></tr></thead>
              <tbody>
                <?php if ($profileOutstandingBalances): ?>
                  <?php foreach ($profileOutstandingBalances as $booking): ?>
                    <tr>
                      <td><?php echo e($booking['vehicle_name']); ?><?php if (!empty($booking['is_sample']) || !empty($booking['is_demo'])): ?><br><span class="record-empty-badge">Example</span><?php endif; ?></td>
                      <td>#<?php echo !empty($booking['is_sample']) ? e($booking['id']) : (int) $booking['id']; ?></td>
                      <td><span class="status pending"><?php echo e(strtoupper($booking['payment_status'] ?? 'OUTSTANDING')); ?></span></td>
                      <td><?php echo $formatMoney($booking['balance']); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td class="empty-state" colspan="4">No outstanding charges.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <div class="table-total">Total Outstanding: <?php echo $formatMoney($outstandingTotal); ?></div>
        </div>
      </section>

      <section class="section" id="reviews">
        <h2 class="section-title">My Reviews</h2>
        <div class="panel">
          <?php if ($reviewMessage !== ''): ?><p role="status"><?php echo e($reviewMessage); ?></p><?php endif; ?>
          <p class="side-email" style="text-align:left"><strong><?php echo count($reviews); ?> actual <?php echo count($reviews) === 1 ? 'review' : 'reviews'; ?></strong><?php if ($reviews): ?> · Average rating: <?php echo number_format(array_sum(array_column($reviews, 'rating')) / count($reviews), 1); ?>/5<?php endif; ?> · Sample rows are excluded from actual review totals.</p>
          <div class="table-wrap">
            <table>
              <thead><tr><th>VEHICLE</th><th>RATING</th><th>REVIEW</th><th>DATE</th></tr></thead>
              <tbody>
                <?php if ($profileReviews): ?>
                  <?php foreach ($profileReviews as $review): ?>
                    <tr>
                      <td><?php echo e($review['vehicle_name']); ?><?php if (!empty($review['is_sample']) || !empty($review['is_demo'])): ?><br><span class="record-empty-badge">Example</span><?php endif; ?></td>
                      <td><?php echo str_repeat('★', (int) $review['rating']) . str_repeat('☆', 5 - (int) $review['rating']); ?> (<?php echo (int) $review['rating']; ?>/5)</td>
                      <td><?php echo e($review['review_text']); ?><br><span class="record-empty-badge"><?php echo e(!empty($review['is_sample']) ? 'Example' : ucfirst($review['status'] ?? 'approved')); ?></span></td>
                      <td><?php echo e(date('Y-m-d', strtotime($review['created_at']))); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td class="empty-state" colspan="4">No reviews yet. You have 0 reviews.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </main>
  </div>

  <footer class="footer">
    <div class="footer-grid">
      <div><a class="logo" href="index.php">AUTORIDE</a><p>Your trusted partner for affordable vehicle rentals, easy bookings, and reliable rides.</p></div>
      <div><h2>QUICK LINKS</h2><ul><li><a href="index.php">Home</a></li><li><a href="brows_vehicle.php">Browse Vehicle</a></li><li><a href="about_us.php">About Us</a></li><li><a href="rentflow.php">Rent</a></li><li><a href="log_in.php">Login</a></li></ul></div>
      <div><h2>SOCIAL MEDIA</h2><ul><li><a href="#">Facebook</a></li><li><a href="#">Instagram</a></li><li><a href="#">TikTok</a></li></ul></div>
      <div><h2>POLICIES</h2><ul><li><a href="privacy_policy.php">Privacy Policy</a></li><li><a href="terms_of_policy.php">Terms of Policy</a></li><li><a href="faqs.php">FAQs</a></li><li><a href="terms_and_conditions.php">Terms and Conditions</a></li><li><a href="return_policy.php">Return Policy</a></li></ul></div>
    </div>
    <div class="footer-bottom">
      <span>© 2026 Autoride. All rights reserved.</span>
      <span><a href="terms_of_policy.php">TERMS OF SERVICE</a> &nbsp; <a href="privacy_policy.php">PRIVACY POLICY</a></span>
    </div>
  </footer>
  <script>
    (() => {
      const sections = Array.from(document.querySelectorAll('.profile-page .content > .section'));
      const sectionLinks = Array.from(document.querySelectorAll('.side-menu a[href^="#"]'));
      const updateActiveSection = () => {
        const requestedId = window.location.hash.slice(1);
        const activeId = sections.some((section) => section.id === requestedId) ? requestedId : 'overview';
        sections.forEach((section) => {
          section.hidden = section.id !== activeId;
        });
        sectionLinks.forEach((link) => {
          const isActive = link.hash === `#${activeId}`;
          link.classList.toggle('active', isActive);
          if (isActive) {
            link.setAttribute('aria-current', 'page');
          } else {
            link.removeAttribute('aria-current');
          }
        });
      };
      sectionLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
          const targetId = link.hash.slice(1);
          if (!sections.some((section) => section.id === targetId)) return;
          event.preventDefault();
          history.pushState(null, '', `#${targetId}`);
          updateActiveSection();
        });
      });
      window.addEventListener('popstate', () => {
        updateActiveSection();
        window.scrollTo(0, 0);
      });
      updateActiveSection();
      window.scrollTo(0, 0);
    })();
  </script>
</body>
</html>
