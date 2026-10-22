<?php
require_once __DIR__ . '/site_init.php';

if (!isset($vehicle) || !is_array($vehicle)) {
    $vehicle = [
        'slug' => 'toyota-vios',
        'type' => 'Car',
        'brand' => 'Toyota',
        'name' => 'Toyota Vios',
        'summary' => 'A dependable and efficient city car designed for comfort, fuel economy, and stress-free daily driving.',
        'description' => 'The Toyota Vios is ideal for business travel, city exploration, and quick family rides, offering a refined cabin, smooth handling, and straightforward reliability.',
        'features' => ['Air Conditioning', 'Automatic Transmission', 'Bluetooth Audio', 'Backup Camera', 'USB Charging', 'Dual Airbags'],
        'tags' => ['Comfortable', 'Reliable', 'Value'],
        'daily_rate' => 2600,
        'deposit' => 5000,
        'transmission' => 'Automatic',
        'fuel_type' => 'Gas',
        'fuel' => 'Gas',
        'seats' => 4,
        'status' => 'Available',
        'price' => 2600,
        'year' => '2024',
        'mileage' => '18,000 km',
        'engine' => '1.3L DOHC',
        'plate' => 'ABC 1234',
        'location' => 'Quezon City',
        'insurance' => 'Comprehensive',
    ];
}

if (!isset($vehicleId)) {
    $vehicleId = $vehicle['slug'] ?? 'toyota-vios';
}
if (!isset($bookingMessage)) {
    $bookingMessage = '';
}
if (!isset($bookingError)) {
    $bookingError = '';
}
if (!isset($bookingForm)) {
    $bookingForm = [
        'days' => 1,
        'pickup_date' => date('Y-m-d', strtotime('+1 day')),
        'return_date' => date('Y-m-d', strtotime('+2 day')),
    ];
}
if (!isset($reviewMessage)) {
    $reviewMessage = '';
}
if (!isset($reviewError)) {
    $reviewError = '';
}
if (!isset($reviewRating)) {
    $reviewRating = '5';
}
if (!isset($reviewText)) {
    $reviewText = '';
}
$vehicleReviews = isset($vehicleReviews) && is_array($vehicleReviews) ? $vehicleReviews : [];
$reviewDistribution = array_fill(1, 5, 0);
foreach ($vehicleReviews as $vehicleReview) {
    $rating = (int) ($vehicleReview['rating'] ?? 0);
    if (isset($reviewDistribution[$rating])) {
        $reviewDistribution[$rating]++;
    }
}
$averageRating = $vehicleReviews
    ? array_sum(array_column($vehicleReviews, 'rating')) / count($vehicleReviews)
    : 0;
$sampleVehicleReviews = [
    [
        'customer_name' => 'Mia Santos',
        'rating' => 5,
        'created_at' => '2025-06-12',
        'review_text' => 'Clean, comfortable, and easy to drive around the city. A great choice for a weekend trip.',
    ],
    [
        'customer_name' => 'Daniel Cruz',
        'rating' => 5,
        'created_at' => '2025-05-28',
        'review_text' => 'Pickup was straightforward and the vehicle suited our weekend trip perfectly.',
    ],
    [
        'customer_name' => 'Andrea Lim',
        'rating' => 4,
        'created_at' => '2025-05-10',
        'review_text' => 'A reliable ride with plenty of room for a short getaway.',
    ],
];
$sampleAverageRating = array_sum(array_column($sampleVehicleReviews, 'rating')) / count($sampleVehicleReviews);
$displayRatingDistribution = $vehicleReviews ? $reviewDistribution : array_reduce(
    $sampleVehicleReviews,
    static function (array $distribution, array $review): array {
        $rating = (int) $review['rating'];
        $distribution[$rating]++;
        return $distribution;
    },
    array_fill(1, 5, 0)
);
$displayAverageRating = $vehicleReviews ? $averageRating : $sampleAverageRating;
$displayRatingCount = $vehicleReviews ? count($vehicleReviews) : count($sampleVehicleReviews);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $vehicle ? escapeHtml($vehicle['name']) . ' | Autoride' : 'Vehicle Not Found | Autoride'; ?></title>
  <style>
    :root {
      --ink: #111827;
      --text: #374151;
      --muted: #6b7280;
      --line: #d1d5db;
      --soft: #e5e7eb;
      --wash: #f8f9fb;
      --green: #07834f;
      --white: #fff;
    }
    * { box-sizing: border-box; }
    body { zoom: 1.2; margin: 0; color: var(--text); background: var(--white); font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.5; }
    a { color: inherit; text-decoration: none; }
    button, input, select, textarea { font: inherit; }
    :focus-visible { outline: 2px solid var(--ink); outline-offset: 2px; }
    .site-header { height: 36px; display: flex; align-items: center; justify-content: space-between; padding: 0 5.2%; border-bottom: 1px solid var(--soft); }
    .brand { color: var(--ink); font-size: 11px; font-weight: 800; }
    .nav { display: flex; align-items: center; gap: 16px; }
    .nav a { color: var(--text); font-size: 7px; }
    .nav a:hover { color: var(--ink); text-decoration: underline; }
    .search-mark { width: 7px; height: 7px; position: relative; border: 1px solid var(--ink); border-radius: 50%; }
    .search-mark::after { content: ""; position: absolute; width: 3px; height: 1px; right: -3px; bottom: -1px; background: var(--ink); transform: rotate(45deg); }

    .page-wrap { width: min(100%, 1200px); margin: auto; padding: 12px 28px 32px; }
    .breadcrumbs { display: flex; align-items: center; flex-wrap: wrap; gap: 5px; margin-bottom: 10px; color: var(--muted); font-size: 7px; }
    .breadcrumbs a:hover { text-decoration: underline; }
    .breadcrumbs strong { color: var(--ink); font-weight: 700; }
    .back-link { margin-left: auto; padding: 4px 7px; border: 1px solid var(--line); border-radius: 2px; color: var(--ink); background: #fff; font-size: 7px; }
    .detail-layout { display: grid; grid-template-columns: minmax(0, 1.8fr) minmax(180px, 1fr); align-items: start; gap: 16px; }
    .left-column, .right-column { min-width: 0; }
    .gallery-main { height: 161px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--line); background: #f1f2f4; }
    .gallery-placeholder { width: 44%; height: 43%; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(209,213,219,.5); color: var(--muted); font-size: 6px; text-align: center; }
    .gallery-thumbs { display: grid; grid-template-columns: repeat(4, 42px); gap: 5px; margin-top: 5px; }
    .gallery-thumb { height: 32px; display: flex; align-items: center; justify-content: center; padding: 2px; border: 1px solid var(--line); background: #f5f6f8; color: var(--text); font-size: 5px; text-align: center; cursor: pointer; }
    .gallery-thumb.active { border-color: var(--ink); background: #fff; }
    .detail-card { margin-top: 10px; padding: 10px; border: 1px solid #e9ebef; border-radius: 4px; background: #fff; }
    .detail-card h2 { margin: 0 0 7px; padding-bottom: 6px; border-bottom: 1px solid var(--soft); color: var(--ink); font-size: 10px; }
    .detail-card p { margin: 0 0 7px; color: var(--text); font-size: 7px; line-height: 1.55; }
    .detail-card p:last-child { margin-bottom: 0; }
    .features { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 7px 8px; padding: 1px 0 2px; }
    .feature { color: var(--text); font-size: 6px; }
    .feature::before { content: "✓"; margin-right: 4px; color: var(--ink); font-weight: 700; }
    .terms { margin: 0; padding-left: 12px; }
    .terms li { margin: 0 0 5px; font-size: 6px; line-height: 1.5; }
    .terms li:last-child { margin-bottom: 0; }

    .booking-card, .specs-card { padding: 10px; border: 1px solid #e9ebef; border-radius: 4px; background: #fff; }
    .available { margin-bottom: 4px; color: var(--green); font-size: 6px; font-weight: 800; }
    .available::before { content: ""; width: 4px; height: 4px; display: inline-block; margin: 0 4px 1px 0; border-radius: 50%; background: var(--green); }
    .booking-brand { color: var(--muted); font-size: 6px; }
    .booking-card h1 { margin: 1px 0 5px; color: var(--ink); font-size: 11px; line-height: 1.3; }
    .badges { display: flex; gap: 4px; margin-bottom: 8px; }
    .badge { padding: 2px 4px; border: 1px solid var(--soft); border-radius: 2px; color: var(--text); font-size: 5px; text-transform: uppercase; }
    .daily-price { margin: 0 0 8px; padding: 7px 0; border-top: 1px solid #f0f1f3; border-bottom: 1px solid #f0f1f3; color: var(--ink); font-size: 13px; font-weight: 800; }
    .daily-price small { color: var(--muted); font-size: 6px; font-weight: 400; }
    .booking-form label { display: block; margin-bottom: 4px; color: var(--ink); font-size: 6px; font-weight: 700; }
    .booking-form input { width: 100%; height: 22px; margin-bottom: 6px; padding: 0 6px; border: 1px solid var(--line); border-radius: 2px; color: var(--text); background: #fff; font-size: 7px; }
    .duration { margin: 0 0 7px; color: var(--muted); font-size: 6px; }
    .price-line, .total-line { display: flex; justify-content: space-between; gap: 8px; color: var(--muted); font-size: 6px; }
    .total-line { margin-top: 3px; color: var(--ink); font-size: 8px; font-weight: 800; }
    .rent-button { width: 100%; min-height: 23px; display: flex; align-items: center; justify-content: center; gap: 7px; margin-top: 8px; border: 1px solid var(--ink); border-radius: 2px; background: var(--ink); color: #fff; font-size: 6px; font-weight: 700; cursor: pointer; }
    .rent-button:hover { background: #303847; }
    .specs-card { margin-top: 10px; }
    .specs-card h2 { margin: 0 0 7px; padding-bottom: 6px; border-bottom: 1px solid var(--soft); color: var(--ink); font-size: 9px; }
    .spec-row { display: flex; justify-content: space-between; gap: 8px; padding: 4px 0; border-bottom: 1px solid #f0f1f3; font-size: 6px; }
    .spec-row:last-child { border-bottom: 0; }
    .spec-row span { color: var(--muted); }
    .spec-row strong { color: var(--ink); text-align: right; }

    .reviews-card { padding-bottom: 10px; }
    .rating-summary { display: grid; grid-template-columns: auto minmax(0, 1fr); gap: 13px; align-items: center; padding: 8px; border: 1px solid #e9ebef; border-radius: 3px; background: var(--wash); }
    .rating-score { color: var(--ink); font-size: 22px; font-weight: 800; line-height: 1; }
    .rating-stars { margin-top: 4px; color: #d69222; font-size: 8px; letter-spacing: 1px; }
    .rating-count { color: var(--muted); font-size: 5px; }
    .rating-bars { display: grid; grid-template-columns: 12px minmax(0, 1fr) 12px; align-items: center; gap: 3px; color: var(--muted); font-size: 5px; }
    .rating-bars + .rating-bars { margin-top: 2px; }
    .bar-track { height: 4px; overflow: hidden; border-radius: 5px; background: #e1e4e9; }
    .bar-fill { height: 100%; border-radius: inherit; background: #6b7280; }
    .rating-categories { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 5px; margin: 8px 0; }
    .rating-category { padding: 5px 2px; border: 1px solid #e9ebef; border-radius: 3px; text-align: center; }
    .rating-category strong { display: block; color: var(--ink); font-size: 8px; }
    .rating-category span { color: var(--muted); font-size: 5px; }
    .review-list { display: grid; gap: 7px; }
    .review-item { padding: 8px; border: 1px solid #e9ebef; border-radius: 3px; background: var(--wash); }
    .review-head { display: flex; align-items: center; gap: 6px; }
    .review-avatar { width: 16px; height: 16px; flex: 0 0 auto; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: #d9dde3; color: #4b5563; font-size: 6px; font-weight: 700; }
    .review-author { color: var(--ink); font-size: 6px; font-weight: 700; }
    .review-date { display: block; color: var(--muted); font-size: 5px; font-weight: 400; }
    .review-rating { margin-left: auto; padding: 2px 4px; border-radius: 2px; background: var(--ink); color: #fff; font-size: 5px; font-weight: 700; }
    .review-item p { margin: 6px 0; color: var(--text); font-size: 6px; line-height: 1.5; }
    .review-tags { display: flex; flex-wrap: wrap; gap: 3px; }
    .review-tags span { padding: 2px 4px; border: 1px solid var(--soft); border-radius: 2px; color: var(--muted); font-size: 5px; }
    .reviews-link { display: table; margin: 8px auto 0; padding: 5px 9px; border: 1px solid var(--soft); border-radius: 2px; color: var(--ink); background: #fff; font-size: 6px; }
    .review-form { margin-top: 9px; padding: 9px; border: 1px solid #e9ebef; border-radius: 3px; background: var(--wash); }
    .review-form h3 { margin: 0 0 4px; color: var(--ink); font-size: 7px; }
    .review-form > p { margin: 0 0 7px; color: var(--muted); font-size: 6px; }
    .form-row { display: grid; grid-template-columns: 1fr 108px; gap: 7px; }
    .review-form label { display: block; margin-bottom: 5px; color: var(--ink); font-size: 5px; font-weight: 700; }
    .review-form input, .review-form select, .review-form textarea { width: 100%; margin-top: 3px; padding: 5px; border: 1px solid var(--soft); border-radius: 2px; background: #fff; color: var(--text); font-size: 6px; }
    .review-form input, .review-form select { height: 21px; }
    .review-form textarea { min-height: 34px; resize: vertical; }
    .review-form button { min-width: 82px; padding: 6px 10px; border: 0; border-radius: 2px; background: var(--ink); color: #fff; font-size: 6px; font-weight: 700; cursor: pointer; }
    .review-message { display: none; margin: 6px 0 0; color: var(--green); font-size: 6px; }
    .review-message.show { display: block; }

    .site-footer { margin-top: 0; padding: 24px 5.2% 8px; background: #f3f4f6; border-top: 1px solid #eceef1; }
    .footer-top { display: grid; grid-template-columns: 1.6fr 1fr 1fr 1fr; gap: 18px; }
    .footer-brand { display: inline-block; margin-bottom: 6px; color: var(--ink); font-size: 8px; font-weight: 800; }
    .site-footer h2 { margin: 0 0 6px; color: var(--ink); font-size: 6px; }
    .site-footer p, .site-footer li { color: var(--text); font-size: 6px; }
    .site-footer p { max-width: 145px; margin: 0; }
    .site-footer ul { margin: 0; padding: 0; list-style: none; }
    .site-footer li { margin-bottom: 3px; }
    .footer-bottom { display: flex; justify-content: space-between; gap: 10px; margin-top: 14px; padding-top: 6px; border-top: 1px solid #cbd0d8; color: var(--muted); font-size: 5px; }
    .not-found { min-height: 55vh; display: flex; align-items: center; justify-content: center; padding: 24px; text-align: center; }
    .not-found h1 { color: var(--ink); font-size: 22px; }

    @media (min-width: 900px) {
      body { font-size: 14px; }
      .site-header { height: 54px; }
      .brand { font-size: 15px; }
      .nav { gap: 24px; }
      .nav a { font-size: 10px; }
      .search-mark { width: 10px; height: 10px; }
      .page-wrap { padding: 18px 40px 48px; }
      .breadcrumbs { margin-bottom: 14px; font-size: 9px; }
      .back-link { padding: 6px 10px; font-size: 9px; }
      .detail-layout { gap: 28px; grid-template-columns: minmax(0, 1.85fr) minmax(270px, 1fr); }
      .gallery-main { height: 340px; }
      .gallery-placeholder { font-size: 9px; }
      .gallery-thumbs { grid-template-columns: repeat(4, 62px); gap: 8px; margin-top: 8px; }
      .gallery-thumb { height: 48px; font-size: 7px; }
      .detail-card { margin-top: 15px; padding: 16px; }
      .detail-card h2 { margin-bottom: 10px; padding-bottom: 8px; font-size: 14px; }
      .detail-card p { margin-bottom: 10px; font-size: 10px; }
      .features { gap: 10px; }
      .feature { font-size: 9px; }
      .terms li { margin-bottom: 7px; font-size: 9px; }
      .booking-card, .specs-card { padding: 16px; }
      .available, .booking-brand { font-size: 8px; }
      .booking-card h1 { font-size: 17px; }
      .badge { padding: 3px 6px; font-size: 7px; }
      .daily-price { font-size: 20px; }
      .daily-price small { font-size: 9px; }
      .booking-form label { font-size: 8px; }
      .booking-form input { height: 30px; font-size: 10px; }
      .duration, .price-line { font-size: 8px; }
      .total-line { font-size: 11px; }
      .rent-button { min-height: 32px; font-size: 8px; }
      .specs-card { margin-top: 15px; }
      .specs-card h2 { font-size: 13px; }
      .spec-row { padding: 6px 0; font-size: 8px; }
      .rating-summary { padding: 12px; gap: 18px; }
      .rating-score { font-size: 30px; }
      .rating-stars { font-size: 10px; }
      .rating-count, .rating-bars { font-size: 7px; }
      .rating-bars { grid-template-columns: 18px minmax(0, 1fr) 18px; }
      .rating-categories { gap: 8px; margin: 12px 0; }
      .rating-category { padding: 8px 3px; }
      .rating-category strong { font-size: 11px; }
      .rating-category span { font-size: 7px; }
      .review-list { gap: 10px; }
      .review-item { padding: 12px; }
      .review-avatar { width: 23px; height: 23px; font-size: 8px; }
      .review-author { font-size: 8px; }
      .review-date { font-size: 7px; }
      .review-rating { font-size: 7px; }
      .review-item p { font-size: 8px; }
      .review-tags span { font-size: 7px; }
      .reviews-link { padding: 7px 12px; font-size: 8px; }
      .review-form { padding: 13px; }
      .review-form h3 { font-size: 9px; }
      .review-form > p { font-size: 8px; }
      .review-form label { font-size: 7px; }
      .review-form input, .review-form select, .review-form textarea { font-size: 8px; }
      .review-form input, .review-form select { height: 27px; }
      .review-form textarea { min-height: 48px; }
      .review-form button { font-size: 8px; }
      .review-message { font-size: 8px; }
      .site-footer { padding-top: 34px; }
      .footer-brand { font-size: 11px; }
      .site-footer h2 { font-size: 8px; }
      .site-footer p, .site-footer li { font-size: 8px; }
      .footer-bottom { margin-top: 20px; padding-top: 9px; font-size: 7px; }
    }
    @media (max-width: 480px) {
      .site-header { height: auto; min-height: 38px; padding: 8px 12px; gap: 8px; }
      .brand { font-size: 9px; }
      .nav { gap: 7px; }
      .nav a { font-size: 6px; }
      .page-wrap { padding: 10px 12px 24px; }
      .detail-layout { grid-template-columns: 1fr; gap: 10px; }
      .right-column { display: grid; grid-template-columns: 1fr 1fr; align-items: start; gap: 8px; }
      .specs-card { margin-top: 0; }
      .gallery-main { height: 220px; }
      .gallery-thumbs { grid-template-columns: repeat(4, minmax(40px, 58px)); }
      .features { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .footer-top { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 15px; }
      .footer-bottom { font-size: 5px; }
    }
    @media (max-width: 380px) {
      .nav { gap: 5px; }
      .nav a { font-size: 5px; }
      .right-column { grid-template-columns: 1fr; }
      .form-row { grid-template-columns: 1fr; gap: 0; }
      .gallery-main { height: 180px; }
    }
    .site-header { height: 52px; padding: 0 max(24px, calc((100% - 1140px) / 2)); }
    .brand { font-size: 14px; letter-spacing: .2px; }
    .nav { gap: 24px; }
    .nav a { font-size: 11px; white-space: nowrap; }
    .nav-logout { padding: 0; border: 0; background: none; color: var(--text); font: inherit; font-size: 11px; cursor: pointer; }
    .nav-logout:hover { color: var(--ink); text-decoration: underline; text-underline-offset: 4px; }
    .nav a:hover, .nav a[aria-current="page"] { color: var(--ink); text-decoration: underline; text-underline-offset: 4px; }
    .search-mark { width: 11px; height: 11px; }
    .search-mark::after { width: 5px; height: 1.5px; right: -4px; bottom: -2px; }
    @media (max-width: 760px) {
      .site-header { height: auto; min-height: 52px; padding: 8px 16px; flex-wrap: wrap; }
      .brand { font-size: 14px; }
      .nav { gap: 12px 18px; flex-wrap: wrap; }
      .nav a { font-size: 11px; }
    }
    @media (max-width: 420px) {
      .site-header { align-items: flex-start; }
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
body.vehicle-details-page {
  color: #374151 !important;
  font-size: 15px !important;
  line-height: 1.6 !important;
}
body.vehicle-details-page .page-wrap {
  width: min(1280px, calc(100% - 48px));
  max-width: none;
  padding: 28px 0 56px;
}
body.vehicle-details-page .breadcrumbs {
  gap: 8px;
  margin-bottom: 22px;
  font-size: 13px;
}
body.vehicle-details-page .back-link {
  padding: 8px 14px;
  border-radius: 99px;
  font-size: 12px;
}
body.vehicle-details-page .detail-layout {
  grid-template-columns: minmax(0, 1.7fr) minmax(340px, .9fr);
  gap: 28px;
}
body.vehicle-details-page .gallery-main {
  height: clamp(300px, 38vw, 460px);
  overflow: hidden;
  border: 1px solid #e5e5e9;
  border-radius: 16px;
  background: linear-gradient(180deg, #ececef 0%, #ececef 52%, #a5a5a8 100%);
}
body.vehicle-details-page .gallery-placeholder {
  width: min(440px, 70%);
  height: 55%;
  padding: 18px;
  border-radius: 12px;
  background: #ffffff80;
  font-size: 14px;
}
body.vehicle-details-page .gallery-thumbs {
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 10px;
  margin-top: 12px;
}
body.vehicle-details-page .gallery-thumb {
  min-height: 70px;
  padding: 10px;
  border-radius: 10px;
  font-size: 11px;
}
body.vehicle-details-page .detail-card,
body.vehicle-details-page .booking-card,
body.vehicle-details-page .specs-card {
  padding: 22px;
  border: 1px solid #e5e5e9;
  border-radius: 16px !important;
  background: #fff !important;
  box-shadow: 0 8px 24px #11121408;
}
body.vehicle-details-page .detail-card {
  margin-top: 18px;
}
body.vehicle-details-page .detail-card h2,
body.vehicle-details-page .specs-card h2 {
  margin: 0 0 14px;
  padding-bottom: 12px;
  font-size: 20px;
  line-height: 1.3;
}
body.vehicle-details-page .detail-card p {
  margin: 0 0 12px;
  color: #4b5563;
  font-size: 15px;
  line-height: 1.7;
}
body.vehicle-details-page .features {
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px 18px;
}
body.vehicle-details-page .feature {
  color: #374151;
  font-size: 14px;
}
body.vehicle-details-page .feature::before {
  margin-right: 8px;
  color: #07834f;
}
body.vehicle-details-page .terms {
  padding-left: 22px;
}
body.vehicle-details-page .terms li {
  margin-bottom: 10px;
  color: #4b5563;
  font-size: 14px;
  line-height: 1.65;
}
body.vehicle-details-page .booking-card {
  position: static;
  padding: 24px;
}
body.vehicle-details-page .available,
body.vehicle-details-page .booking-brand {
  font-size: 13px;
}
body.vehicle-details-page .available::before {
  width: 8px;
  height: 8px;
  margin-right: 7px;
}
body.vehicle-details-page .booking-card h1 {
  margin: 4px 0 10px;
  font-size: 26px;
  line-height: 1.2;
}
body.vehicle-details-page .badges {
  gap: 8px;
  margin-bottom: 14px;
}
body.vehicle-details-page .badge {
  padding: 5px 9px;
  border-radius: 99px;
  font-size: 11px;
}
body.vehicle-details-page .daily-price {
  margin-bottom: 16px;
  padding: 14px 0;
  font-size: 28px;
}
body.vehicle-details-page .daily-price small {
  font-size: 14px;
}
body.vehicle-details-page .booking-form label {
  margin-bottom: 7px;
  font-size: 13px;
}
body.vehicle-details-page .booking-form input {
  height: 46px;
  margin-bottom: 12px;
  padding: 0 12px;
  border-radius: 9px;
  font-size: 14px;
}
body.vehicle-details-page .duration,
body.vehicle-details-page .price-line {
  font-size: 13px;
}
body.vehicle-details-page .total-line {
  margin-top: 8px;
  font-size: 17px;
}
body.vehicle-details-page .rent-button {
  min-height: 48px;
  margin-top: 16px;
  border-radius: 99px;
  font-size: 13px;
}
body.vehicle-details-page .specs-card {
  margin-top: 18px;
}
body.vehicle-details-page .specs-card h2 {
  display: block;
  margin: 0 0 14px;
  padding: 0 0 12px;
  border-bottom: 2px solid #e5e5e9;
  color: #111214 !important;
  font-size: 24px !important;
  font-weight: 850 !important;
  line-height: 1.25 !important;
  letter-spacing: -.4px;
  overflow-wrap: anywhere;
}
body.vehicle-details-page .spec-row {
  gap: 14px;
  padding: 12px 0;
  font-size: 14px;
}
body.vehicle-details-page .spec-row span {
  color: #55565d;
  font-size: 14px;
}
body.vehicle-details-page .spec-row strong {
  color: #111214;
  font-size: 14px;
  font-weight: 800;
}
body.vehicle-details-page .rating-summary {
  padding: 20px;
}
body.vehicle-details-page .rating-score {
  font-size: 42px;
}
body.vehicle-details-page .rating-stars {
  font-size: 16px;
}
body.vehicle-details-page .rating-count,
body.vehicle-details-page .rating-bars {
  font-size: 12px;
}
body.vehicle-details-page .rating-category strong {
  font-size: 16px;
}
body.vehicle-details-page .rating-category span {
  font-size: 11px;
}
body.vehicle-details-page .review-item {
  padding: 18px;
}
body.vehicle-details-page .review-author,
body.vehicle-details-page .review-rating {
  font-size: 13px;
}
body.vehicle-details-page .review-date {
  font-size: 11px;
}
body.vehicle-details-page .review-item p {
  font-size: 14px;
  line-height: 1.65;
}
body.vehicle-details-page .demo-review-label {
  display: inline-block;
  margin-left: 6px;
  padding: 3px 8px;
  border-radius: 999px;
  background: #fff1bd;
  color: #5a4900;
  font-size: 10px;
  font-weight: 800;
  text-transform: uppercase;
}
body.vehicle-details-page .demo-review-note {
  margin: 0 0 10px;
  color: var(--muted);
  font-size: 12px;
}
body.vehicle-details-page .sample-reviews {
  margin-top: 22px;
  padding: 18px;
  border: 1px dashed #c8b34f;
  border-radius: 12px;
  background: #fffdf5;
}
body.vehicle-details-page .sample-reviews h3 {
  margin: 0 0 6px;
  color: var(--ink);
  font-size: 17px;
}
body.vehicle-details-page .sample-reviews > p {
  margin: 0 0 14px;
  color: var(--muted);
  font-size: 12px;
}
body.vehicle-details-page .sample-review {
  background: #fff;
}
body.vehicle-details-page .review-tags span {
  font-size: 11px;
}
body.vehicle-details-page .review-form {
  padding: 20px;
}
body.vehicle-details-page .review-form h3 {
  font-size: 18px;
}
body.vehicle-details-page .review-form > p,
body.vehicle-details-page .review-form label {
  font-size: 13px;
}
body.vehicle-details-page .review-form input,
body.vehicle-details-page .review-form select {
  height: 42px;
  font-size: 14px;
}
body.vehicle-details-page .review-form textarea {
  min-height: 100px;
  font-size: 14px;
}
@media (max-width: 900px) {
  body.vehicle-details-page .page-wrap {
    width: calc(100% - 36px);
  }
  body.vehicle-details-page .detail-layout {
    grid-template-columns: minmax(0, 1.2fr) minmax(300px, .9fr);
    gap: 18px;
  }
  body.vehicle-details-page .booking-card {
    position: static;
  }
}
@media (max-width: 680px) {
  body.vehicle-details-page .page-wrap {
    width: calc(100% - 32px);
    padding-top: 20px;
  }
  body.vehicle-details-page .detail-layout {
    grid-template-columns: 1fr;
  }
  body.vehicle-details-page .right-column {
    display: flex;
    flex-direction: column;
  }
  body.vehicle-details-page .booking-card,
  body.vehicle-details-page .specs-card {
    width: 100%;
  }
  body.vehicle-details-page .gallery-main {
    height: 280px;
  }
}
@media (max-width: 420px) {
  body.vehicle-details-page .features {
    grid-template-columns: 1fr;
  }
  body.vehicle-details-page .gallery-main {
    height: 220px;
  }
  body.vehicle-details-page .detail-card,
  body.vehicle-details-page .booking-card,
  body.vehicle-details-page .specs-card {
    padding: 18px;
  }
}
  </style>
</head>
<body class="vehicle-details-page">
  <header class="site-header">
    <a class="brand" href="index.php">AUTORIDE</a>
    <nav class="nav" aria-label="Main navigation">
      <a href="index.php">Home</a>
      <a href="brows_vehicle.php" aria-current="page">Browse Vehicle</a>
      <a href="rentflow.php">Rent</a>
      <a href="about_us.php">About Us</a>
      <?php if (empty($_SESSION['customer_user']['id'])): ?>
        <a href="log_in.php">Login/Register</a>
      <?php else: ?>
        <form action="log_in.php" method="post" style="display:contents">
          <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
          <input type="hidden" name="action" value="logout">
          <button class="nav-logout" type="submit">Logout</button>
        </form>
      <?php endif; ?>
      <?php if (!empty($_SESSION['customer_user']['id'])): ?>
        <a href="user_profile.php">Profile</a>
      <?php endif; ?>
      <a class="search-mark" href="brows_vehicle.php" aria-label="Search vehicles" title="Search vehicles"></a>
    </nav>
  </header>

  <?php if ($vehicle === null): ?>
    <main class="not-found">
      <div>
        <h1>Vehicle not found</h1>
        <p>The vehicle may have been removed or the link is incorrect.</p>
        <a class="back-link" href="brows_vehicle.php">Back to Browse</a>
      </div>
    </main>
  <?php else: ?>
    <main class="page-wrap">
      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="index.php">Home</a><span>/</span>
        <a href="brows_vehicle.php">Browse Vehicle</a><span>/</span>
        <a href="brows_vehicle.php?type=<?php echo rawurlencode($vehicle['type']); ?>"><?php echo escapeHtml($vehicle['type']); ?></a><span>/</span>
        <strong><?php echo escapeHtml($vehicle['name']); ?></strong>
        <a class="back-link" href="brows_vehicle.php">← Back to Browse</a>
      </nav>

      <div class="detail-layout">
        <div class="left-column">
          <section aria-label="Vehicle photo gallery">
            <div class="gallery-main">
              <div class="gallery-placeholder" id="main-gallery-label">[ VEHICLE MAIN IMAGE PLACEHOLDER ]</div>
            </div>
            <div class="gallery-thumbs">
              <button class="gallery-thumb active" type="button">[ ANGLE 1 ]</button>
              <button class="gallery-thumb" type="button">[ ANGLE 2 ]</button>
              <button class="gallery-thumb" type="button">[ INTERIOR ]</button>
              <button class="gallery-thumb" type="button">[ SPECS ]</button>
            </div>
          </section>

          <section class="detail-card">
            <h2>Vehicle Description</h2>
            <p><?php echo escapeHtml($vehicle['summary']); ?></p>
            <?php if (trim((string) $vehicle['description']) !== trim((string) $vehicle['summary'])): ?>
              <p><?php echo escapeHtml($vehicle['description']); ?></p>
            <?php endif; ?>
            <p>
              This <?php echo escapeHtml($vehicle['year']); ?> <?php echo escapeHtml($vehicle['brand']); ?> <?php echo escapeHtml($vehicle['name']); ?> is a <?php echo escapeHtml($vehicle['type']); ?> with <?php echo (int) $vehicle['seats']; ?> seats, <?php echo escapeHtml($vehicle['transmission']); ?> transmission, and <?php echo escapeHtml($vehicle['fuel_type']); ?> fuel. It is based in <?php echo escapeHtml($vehicle['location']); ?> and is available for PHP <?php echo number_format((float) $vehicle['price']); ?> per day.
            </p>
          </section>

          <section class="detail-card">
            <h2>Premium Features</h2>
            <div class="features">
              <?php foreach ($vehicle['features'] as $feature): ?>
                <div class="feature"><?php echo escapeHtml($feature); ?></div>
              <?php endforeach; ?>
            </div>
          </section>

          <section class="detail-card">
            <h2>Rental Terms &amp; Conditions</h2>
            <ul class="terms">
              <li>Minimum rental period is 1 day (24 hours). Extensions must be requested at least 3 hours before the scheduled return time.</li>
              <li>A valid driver's license and one (1) government-issued ID are required upon vehicle pickup. International drivers must present an International Driving Permit (IDP).</li>
              <li>A security deposit of PHP 5,000 is collected at pickup and fully refunded within 3-5 business days after vehicle return, provided no damages are found.</li>
              <li>Fuel policy: vehicle is provided with a full tank and must be returned full. A PHP 300/liter refueling fee applies if returned with less fuel.</li>
              <li>Late returns are charged at PHP 500 per hour beyond the agreed return time. Cancellations made within 24 hours of pickup are non-refundable.</li>
              <li>Renter is liable for all traffic violations, toll fees, and parking charges incurred during the rental period.</li>
            </ul>
          </section>

          <section class="detail-card reviews-card" id="reviews">
            <h2>Ratings &amp; Reviews</h2>
            <div class="rating-summary">
              <div>
                <div class="rating-score"><?php echo number_format($displayAverageRating, 1); ?></div>
                <div class="rating-stars" aria-label="<?php echo number_format($displayAverageRating, 1); ?> out of 5 stars"><?php echo str_repeat('★', (int) round($displayAverageRating)); ?></div>
                <div class="rating-count"><?php echo $vehicleReviews ? count($vehicleReviews) . ' APPROVED REVIEWS' : 'SAMPLE RATING · DEMO DATA'; ?></div>
              </div>
              <div aria-label="Rating distribution">
                <?php for ($rating = 5; $rating >= 1; $rating--): ?>
                  <?php $ratingPercent = $displayRatingCount ? (int) round($displayRatingDistribution[$rating] / $displayRatingCount * 100) : 0; ?>
                  <div class="rating-bars"><span><?php echo $rating; ?> ★</span><div class="bar-track"><div class="bar-fill" style="width:<?php echo $ratingPercent; ?>%"></div></div><span><?php echo (int) $displayRatingDistribution[$rating]; ?></span></div>
                <?php endfor; ?>
              </div>
            </div>
            <p class="demo-review-note">Demo ratings are fictional examples for preview only and are not customer-submitted reviews.</p>
            <div class="review-list">
              <?php if (!$vehicleReviews): ?><p class="empty-state">No actual approved reviews yet. The sample ratings below are for demonstration only.</p><?php endif; ?>
              <?php if (!$vehicleReviews): ?>
                <?php foreach ($sampleVehicleReviews as $sampleReview): ?>
                  <article class="review-item sample-review">
                    <div class="review-head">
                      <span class="review-avatar" aria-hidden="true"><?php echo escapeHtml(strtoupper(substr($sampleReview['customer_name'], 0, 1))); ?></span>
                      <div class="review-author"><?php echo escapeHtml($sampleReview['customer_name']); ?><span class="review-date"><?php echo escapeHtml(date('M j, Y', strtotime($sampleReview['created_at']))); ?></span></div>
                      <span class="review-rating">★ <?php echo (int) $sampleReview['rating']; ?>.0</span>
                    </div>
                    <p><?php echo escapeHtml($sampleReview['review_text']); ?><span class="demo-review-label">Demo review</span></p>
                  </article>
                <?php endforeach; ?>
              <?php endif; ?>
              <?php foreach ($vehicleReviews as $review): ?>
                <article class="review-item">
                  <div class="review-head"><span class="review-avatar"><?php echo escapeHtml(strtoupper(substr($review['customer_name'], 0, 2))); ?></span><div class="review-author"><?php echo escapeHtml($review['customer_name']); ?><span class="review-date"><?php echo escapeHtml(date('M j, Y', strtotime($review['created_at']))); ?></span></div><span class="review-rating">★ <?php echo (int) $review['rating']; ?>.0</span></div>
                  <p><?php echo escapeHtml($review['review_text']); ?></p>
                </article>
              <?php endforeach; ?>
            </div>
            <form class="review-form" id="review-form" method="post" action="vehicle_details.php?id=<?php echo rawurlencode($vehicleId); ?>#reviews">
              <h3>SUBMIT YOUR FEEDBACK</h3>
              <p>Share your experience with this vehicle. Completed rentals are linked to verified reviews when possible.</p>
              <?php if ($reviewMessage !== ''): ?><p class="review-message show" role="status"><?php echo escapeHtml($reviewMessage); ?></p><?php endif; ?>
              <?php if ($reviewError !== ''): ?><p class="review-message show" role="alert"><?php echo escapeHtml($reviewError); ?></p><?php endif; ?>
              <?php if (empty($_SESSION['customer_user']['id'])): ?>
                <p><a href="log_in.php">Log in</a> to submit a review.</p>
              <?php else: ?>
                <input type="hidden" name="csrf_token" value="<?php echo escapeHtml($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="action" value="submit_review">
                <label>RATING
                  <select name="rating" required>
                    <option value="5" <?php echo $reviewRating === '5' ? 'selected' : ''; ?>>★★★★★ &nbsp; 5 out of 5</option>
                    <option value="4" <?php echo $reviewRating === '4' ? 'selected' : ''; ?>>★★★★☆ &nbsp; 4 out of 5</option>
                    <option value="3" <?php echo $reviewRating === '3' ? 'selected' : ''; ?>>★★★☆☆ &nbsp; 3 out of 5</option>
                    <option value="2" <?php echo $reviewRating === '2' ? 'selected' : ''; ?>>★★☆☆☆ &nbsp; 2 out of 5</option>
                    <option value="1" <?php echo $reviewRating === '1' ? 'selected' : ''; ?>>★☆☆☆☆ &nbsp; 1 out of 5</option>
                  </select>
                </label>
                <label>YOUR REVIEW<textarea name="review" placeholder="Describe your rental experience..." required minlength="3" maxlength="2000"><?php echo escapeHtml($reviewText); ?></textarea></label>
                <button type="submit">SUBMIT REVIEW</button>
              <?php endif; ?>
            </form>
          </section>
        </div>

        <aside class="right-column">
          <section class="booking-card" aria-labelledby="vehicle-title">
            <div class="available">AVAILABLE FOR RENT</div>
            <div class="booking-brand"><?php echo escapeHtml($vehicle['brand']); ?></div>
            <h1 id="vehicle-title"><?php echo escapeHtml($vehicle['name']); ?></h1>
            <div class="badges">
              <span class="badge"><?php echo escapeHtml($vehicle['fuel']); ?></span>
              <span class="badge"><?php echo escapeHtml($vehicle['transmission']); ?></span>
              <span class="badge"><?php echo escapeHtml($vehicle['seats']); ?> SEATS</span>
            </div>
            <p class="daily-price">PHP <?php echo number_format($vehicle['price']); ?><small> / day</small></p>
            <form class="booking-form" id="booking-form">
              <label for="rental-start">PICKUP &amp; RETURN DATES</label>
              <input id="rental-start" type="date" value="<?php echo escapeHtml($pickup); ?>" required aria-label="Pickup date">
              <input id="rental-end" type="date" value="<?php echo escapeHtml($returnDate); ?>" required aria-label="Return date">
              <p class="duration" id="rental-duration">Select rental dates to see your total.</p>
              <div class="price-line"><span>PHP <?php echo number_format($vehicle['price']); ?> × <span id="rental-days">0 days</span></span><strong id="rental-subtotal">PHP 0</strong></div>
              <div class="price-line"><span>Security Deposit (Refundable)</span><strong>PHP 5,000</strong></div>
              <div class="total-line"><span>Estimated Total</span><span id="rental-total">PHP 5,000</span></div>
              <button class="rent-button" type="submit">RENT &nbsp; →</button>
              <p class="review-message" id="booking-message" role="status"></p>
            </form>
          </section>

          <section class="specs-card">
            <h2>Specifications Sheet</h2>
            <div class="spec-row"><span>Year Model</span><strong><?php echo escapeHtml($vehicle['year']); ?></strong></div>
            <div class="spec-row"><span>Mileage</span><strong><?php echo escapeHtml($vehicle['mileage']); ?></strong></div>
            <div class="spec-row"><span>Engine Capacity</span><strong><?php echo escapeHtml($vehicle['engine']); ?></strong></div>
            <div class="spec-row"><span>Plate Ending</span><strong><?php echo escapeHtml($vehicle['plate']); ?></strong></div>
            <div class="spec-row"><span>Location</span><strong><?php echo escapeHtml($vehicle['location']); ?></strong></div>
            <div class="spec-row"><span>Insurance</span><strong><?php echo escapeHtml($vehicle['insurance']); ?></strong></div>
          </section>
        </aside>
      </div>
    </main>
  <?php endif; ?>

  <footer class="site-footer">
    <div class="footer-top">
      <div><a class="footer-brand" href="index.php">AUTORIDE</a><p>Low-fidelity desktop blueprint wireframe for simplified vehicle search, booking, and profiling.</p></div>
      <div><h2>QUICK LINKS</h2><ul><li><a href="index.php">Home</a></li><li><a href="brows_vehicle.php">Browse Vehicle</a></li><li><a href="about_us.php">About Us</a></li><li><a href="rentflow.php">Rent</a></li><li><a href="log_in.php">Login</a></li></ul></div>
      <div><h2>SOCIAL MEDIA</h2><ul><li><a href="#">Facebook</a></li><li><a href="#">Instagram</a></li><li><a href="#">TikTok</a></li></ul></div>
      <div><h2>POLICIES</h2><ul><li><a href="privacy_policy.php">Privacy Policy</a></li><li><a href="terms_of_policy.php">Terms of Policy</a></li><li><a href="faqs.php">FAQs</a></li><li><a href="terms_and_conditions.php">Terms and Conditions</a></li><li><a href="return_policy.php">Return Policy</a></li></ul></div>
    </div>
    <div class="footer-bottom"><span>© 2026 VehiRent Blueprint. Created in Grayscale Wireframe spec.</span><span><a href="terms_of_policy.php">TERMS OF SERVICE</a> &nbsp; <a href="privacy_policy.php">PRIVACY POLICY</a></span></div>
  </footer>

  <?php if ($vehicle !== null): ?>
  <script>
    (function () {
      var pricePerDay = <?php echo (int) $vehicle['price']; ?>;
      var startInput = document.getElementById('rental-start');
      var endInput = document.getElementById('rental-end');
      var dayLabel = document.getElementById('rental-days');
      var subtotalLabel = document.getElementById('rental-subtotal');
      var totalLabel = document.getElementById('rental-total');
      var durationLabel = document.getElementById('rental-duration');
      var deposit = 5000;

      function updateQuote() {
        var start = startInput.value ? new Date(startInput.value + 'T00:00:00') : null;
        var end = endInput.value ? new Date(endInput.value + 'T00:00:00') : null;
        var days = start && end ? Math.ceil((end.getTime() - start.getTime()) / 86400000) : 0;
        if (days < 1) {
          dayLabel.textContent = '0 days';
          subtotalLabel.textContent = 'PHP 0';
          totalLabel.textContent = 'PHP ' + deposit.toLocaleString('en-PH');
          durationLabel.textContent = 'Select a valid pickup and return date.';
          return false;
        }
        var subtotal = days * pricePerDay;
        dayLabel.textContent = days + (days === 1 ? ' day' : ' days');
        subtotalLabel.textContent = 'PHP ' + subtotal.toLocaleString('en-PH');
        totalLabel.textContent = 'PHP ' + (subtotal + deposit).toLocaleString('en-PH');
        durationLabel.textContent = 'Rental duration: ' + days + (days === 1 ? ' day.' : ' days.');
        return true;
      }

      startInput.addEventListener('change', function () {
        endInput.min = startInput.value;
        updateQuote();
      });
      endInput.addEventListener('change', updateQuote);
      document.getElementById('booking-form').addEventListener('submit', function (event) {
        event.preventDefault();
        var message = document.getElementById('booking-message');
        if (!startInput.value || !endInput.value || !updateQuote()) {
          message.textContent = 'Choose valid pickup and return dates to continue.';
          message.classList.add('show');
          return;
        }
        var params = new URLSearchParams({
          id: <?php echo json_encode($vehicleId); ?>,
          pickup: startInput.value,
          return: endInput.value
        });
        window.location.href = 'rentflow.php?' + params.toString();
      });

      Array.prototype.forEach.call(document.querySelectorAll('.gallery-thumb'), function (button) {
        button.addEventListener('click', function () {
          document.querySelectorAll('.gallery-thumb').forEach(function (thumb) {
            thumb.classList.remove('active');
          });
          button.classList.add('active');
          document.getElementById('main-gallery-label').textContent = button.textContent.replace(/[\[\]]/g, '').trim() + ' IMAGE PLACEHOLDER';
        });
      });

    })();
  </script>
  <?php endif; ?>
</body>
</html>
