<?php
require_once __DIR__ . '/site_init.php';

if (empty($_SESSION['admin_user']['id'])) {
    header('Location: admin_login.php', true, 303);
    exit;
}

$flashMessage = $_SESSION['admin_flash_message'] ?? '';
unset($_SESSION['admin_flash_message']);
$dashboardStats = isset($dashboardStats) && is_array($dashboardStats)
    ? $dashboardStats
    : [];
$dashboardStats = array_merge([
    'vehicles' => 0,
    'active_rentals' => 0,
    'pending_requests' => 0,
    'customers' => 0,
    'approved_rentals' => 0,
    'completed_rentals' => 0,
    'revenue' => 0,
    'paid_amount' => 0,
    'outstanding_amount' => 0,
    'sample_revenue' => 0,
], $dashboardStats);
$requests = isset($requests) && is_array($requests) ? $requests : [];
$allBookings = isset($allBookings) && is_array($allBookings) ? $allBookings : [];
$vehicles = isset($vehicles) && is_array($vehicles) ? $vehicles : [];
$categories = isset($categories) && is_array($categories) ? $categories : [];
$customers = isset($customers) && is_array($customers) ? $customers : [];
$allReviews = isset($allReviews) && is_array($allReviews) ? $allReviews : [];
$allPayments = isset($allPayments) && is_array($allPayments) ? $allPayments : [];
$popularVehicles = isset($popularVehicles) && is_array($popularVehicles) ? $popularVehicles : [];
$monthlyRevenue = isset($monthlyRevenue) && is_array($monthlyRevenue) ? $monthlyRevenue : [];
$releaseRecords = isset($releaseRecords) && is_array($releaseRecords) ? $releaseRecords : [];
$maintenanceRecords = isset($maintenanceRecords) && is_array($maintenanceRecords) ? $maintenanceRecords : [];
$rentalReport = isset($rentalReport) && is_array($rentalReport) ? $rentalReport : [];
$vehicleReport = isset($vehicleReport) && is_array($vehicleReport) ? $vehicleReport : [];
$maintenanceReport = isset($maintenanceReport) && is_array($maintenanceReport) ? $maintenanceReport : [];
$revenueReport = isset($revenueReport) ? (float) $revenueReport : 0.0;
$editVehicle = isset($editVehicle) && is_array($editVehicle) ? $editVehicle : null;
$editCategory = isset($editCategory) && is_array($editCategory) ? $editCategory : null;
$editMaintenance = isset($editMaintenance) && is_array($editMaintenance) ? $editMaintenance : null;
$selectedAdminSection = (string) ($_GET['section'] ?? (
    isset($_GET['edit_vehicle']) ? 'vehicle-edit'
        : (isset($_GET['edit_category']) ? 'category-edit'
            : (isset($_GET['edit_maintenance']) ? 'maintenance-add' : 'dashboard'))
));
$paymentBookingMap = [];
foreach ($allBookings as $bookingRecord) {
    $paymentBookingMap[(int) ($bookingRecord['id'] ?? 0)] = $bookingRecord;
}
if ($selectedAdminSection !== 'vehicle-edit') {
    $editVehicle = null;
}
if ($selectedAdminSection !== 'category-edit') {
    $editCategory = null;
}
if ($selectedAdminSection !== 'maintenance-add') {
    $editMaintenance = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Autoride admin dashboard preview for vehicle rentals.">
  <title>Admin Panel | Autoride</title>
  <style>
    :root {
      --ink: #111827;
      --text: #374151;
      --muted: #6b7280;
      --line: #d1d5db;
      --border: #e5e7eb;
      --wash: #f8f9fb;
      --white: #fff;
    }
    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    html { overflow-anchor: none; }
    body { margin: 0; display: flex; flex-direction: column; color: var(--text); background: var(--white); font: 14px/1.5 Arial, Helvetica, sans-serif; }
    a { color: inherit; text-decoration: none; }
    :focus-visible { outline: 2px solid var(--ink); outline-offset: 2px; }

    .site-header { min-height: 54px; display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 0 max(24px, calc((100% - 1140px) / 2)); border-bottom: 1px solid var(--border); background: var(--white); }
    .brand { color: var(--ink); font-size: 15px; font-weight: 800; letter-spacing: .2px; }
    .nav { display: flex; align-items: center; gap: 24px; }
    .nav a { color: var(--text); font-size: 13px; white-space: nowrap; }
    .nav form { margin: 0; }
    .nav button:hover { text-decoration: underline; text-underline-offset: 4px; }
    .nav button { padding: 0; border: 0; background: none; color: var(--text); font-size: 13px; cursor: pointer; }
    .nav a:hover, .nav a[aria-current="page"] { color: var(--ink); text-decoration: underline; text-underline-offset: 4px; }
    .nav a[aria-current="page"] { font-weight: 700; }
    .search-mark { width: 10px; height: 10px; position: relative; border: 1.4px solid var(--ink); border-radius: 50%; }
    .search-mark::after { content: ""; position: absolute; right: -4px; bottom: -2px; width: 5px; height: 1.4px; background: var(--ink); transform: rotate(45deg); }

    .admin-layout { width: min(1140px, calc(100% - 48px)); margin: 24px auto 36px; display: grid; grid-template-columns: 190px minmax(0, 1fr); gap: 28px; align-items: start; }
    .sidebar { padding: 12px 10px; border: 1px solid var(--border); border-radius: 4px; background: var(--wash); }
    .side-label { margin: 0 0 10px; color: var(--muted); font-size: 12px; font-weight: 700; letter-spacing: .35px; text-transform: uppercase; }
    .side-nav { display: grid; gap: 3px; }
    .side-nav a { display: block; padding: 7px 8px; border-radius: 3px; color: var(--text); font-size: 12px; }
    .side-nav a:hover { background: #eef0f3; color: var(--ink); }
    .side-nav a.active { background: var(--ink); color: var(--white); font-weight: 700; }
    .side-group { margin-top: 13px; }
    .side-group h2 { margin: 0 0 4px; padding: 0 7px; color: var(--ink); font-size: 12px; }
    .side-group a { padding: 4px 7px; font-size: 12px; }

    .content { min-width: 0; }
    .selected-view-heading {
      margin: 0 0 18px;
      padding-bottom: 13px;
      border-bottom: 1px solid var(--border);
    }
    .selected-view-heading h1 { margin: 0; color: var(--ink); font-size: 22px; }
    .page-heading { margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--border); }
    .page-heading h1 { margin: 0; color: var(--ink); font-size: 20px; }
    .page-heading p { margin: 4px 0 0; color: var(--muted); font-size: 14px; }
    .notice { margin: -7px 0 14px; color: var(--muted); font-size: 13px; }
    .stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-bottom: 18px; }
    .stat { min-height: 76px; padding: 13px 14px; border: 1px solid var(--border); border-radius: 4px; background: var(--white); }
    .stat span { display: block; color: var(--muted); font-size: 12px; font-weight: 700; }
    .stat strong { display: block; margin-top: 5px; color: var(--ink); font-size: 24px; line-height: 1; }
    .panel { margin-bottom: 22px; overflow: hidden; border: 1px solid var(--border); border-radius: 4px; background: var(--white); }
    .panel-heading { padding: 12px 14px; border-bottom: 1px solid var(--border); color: var(--ink); font-size: 15px; font-weight: 700; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; text-align: left; white-space: nowrap; }
    th { padding: 10px 12px; background: var(--wash); color: var(--muted); font-size: 11px; font-weight: 700; text-transform: uppercase; }
    td { padding: 10px 12px; border-top: 1px solid #edf0f3; color: var(--text); font-size: 13px; }
    td:first-child { color: var(--ink); }
    .badge { display: inline-block; padding: 4px 7px; border-radius: 3px; background: #eef0f3; color: var(--text); font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .badge.approved { background: #111827; color: #fff; }
    .actions { display: flex; gap: 5px; }
    .action { padding: 5px 9px; border: 1px solid var(--line); border-radius: 3px; background: var(--white); color: var(--ink); font-size: 12px; }
    .action.primary { border-color: var(--ink); background: var(--ink); color: var(--white); }
    .admin-form { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; padding: 12px; border-bottom: 1px solid var(--border); }
    .admin-form label { display: grid; gap: 4px; color: var(--muted); font-size: 12px; }
    .admin-form input, .admin-form select, .admin-form textarea { width: 100%; min-width: 0; padding: 6px; border: 1px solid var(--line); border-radius: 3px; background: var(--white); color: var(--text); font: inherit; }
    .admin-form textarea { min-height: 52px; resize: vertical; }
    .admin-form .wide { grid-column: span 2; }
    .admin-form .full { grid-column: 1 / -1; }
    .inline-form { display: inline-flex; align-items: center; gap: 4px; }
    .flash { margin: 0 0 14px; padding: 9px 12px; border: 1px solid #b7dfc9; background: #e8f6ee; color: #065f46; }
    .badge.disabled, .badge.rejected, .badge.cancelled { background: #fce8e8; color: #991b1b; }
    .badge.requested, .badge.scheduled, .badge.in_progress { background: #fff3e0; color: #8a4b08; }
    .empty-row { padding: 18px 14px; color: var(--muted); text-align: center; }
    .section-tools { display: flex; justify-content: flex-end; padding: 9px 12px; border-bottom: 1px solid var(--border); }
    .action:disabled { cursor: not-allowed; opacity: .75; }
    .empty-link { color: var(--muted); }
    .footer { padding: 26px max(24px, calc((100% - 1140px) / 2)) 10px; background: #f1f2f4; }
    .footer-grid { display: grid; grid-template-columns: 1.6fr 1fr 1fr 1fr; gap: 28px; }
    .footer-brand { color: var(--ink); font-size: 15px; font-weight: 800; }
    .footer h2 { margin: 0 0 8px; color: var(--ink); font-size: 13px; }
    .footer p, .footer li { color: var(--muted); font-size: 12px; }
    .footer p { max-width: 210px; margin: 8px 0 0; }
    .footer ul { margin: 0; padding: 0; list-style: none; }
    .footer li { margin-bottom: 5px; }
    .footer a:hover { color: var(--ink); text-decoration: underline; }
    .footer-bottom { display: flex; justify-content: space-between; gap: 12px; margin-top: 18px; padding-top: 9px; border-top: 1px solid var(--line); color: var(--muted); font-size: 11px; }

    @media (max-width: 760px) {
      .site-header { padding: 10px 16px; flex-wrap: wrap; }
      .nav { gap: 12px 18px; flex-wrap: wrap; }
      .nav a, .nav button { font-size: 12px; }
      .admin-layout { width: min(100% - 28px, 600px); margin-top: 16px; grid-template-columns: 1fr; gap: 16px; }
      .sidebar { padding: 10px; }
      .side-nav { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .side-group { grid-column: 1 / -1; }
      .side-group h2 { margin-top: 3px; }
      .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 9px; }
      .admin-form { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .footer { padding: 22px 16px 10px; }
      .footer-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
    }
    @media (max-width: 480px) {
      .admin-form { grid-template-columns: 1fr; }
      .admin-form .wide, .admin-form .full { grid-column: 1; }
    }
    @media (max-width: 420px) {
      .nav { gap: 9px; }
      .side-nav { grid-template-columns: 1fr; }
      .footer-bottom { flex-direction: column; }
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

body {
  background: #f7f7f8 !important;
}
.admin-layout {
  width: min(1280px, calc(100% - 48px));
  grid-template-columns: 250px minmax(0, 1fr);
  gap: 28px;
  margin: 32px auto 48px;
}
.sidebar {
  padding: 18px;
  border-radius: var(--radius) !important;
  background: var(--white);
  box-shadow: 0 8px 24px #11121408;
}
.side-label {
  margin-bottom: 14px;
  color: var(--muted);
  font-size: 10px;
  letter-spacing: 1px;
}
.side-nav { gap: 5px; }
.side-nav a {
  padding: 9px 11px;
  border-radius: 9px;
  font-size: 12px;
  font-weight: 650;
  transition: background .2s, color .2s;
}
.side-nav a:hover {
  background: #f5f5f6;
  color: var(--ink);
}
.side-nav a.active {
  background: var(--yellow);
  color: var(--ink);
  font-weight: 850;
}
.side-group {
  margin-top: 14px;
  padding-top: 13px;
  border-top: 1px solid var(--line);
}
.side-group h2 {
  margin: 0 0 6px;
  padding: 0 8px;
  color: var(--muted);
  font-size: 10px;
  font-weight: 850;
  letter-spacing: .7px;
  text-transform: uppercase;
}
.side-group a { padding: 7px 9px; }
.page-heading {
  display: grid;
  gap: 7px;
  margin-bottom: 22px;
  padding-bottom: 18px;
}
.page-heading h1 {
  font-size: clamp(28px, 3vw, 40px) !important;
  font-weight: 900;
  letter-spacing: -1.2px;
}
.page-heading p {
  margin: 0;
  color: var(--muted);
  font-size: 14px;
}
.stats { gap: 16px; margin-bottom: 22px; }
.stat {
  min-height: 104px;
  padding: 16px;
  border-top: 3px solid var(--yellow) !important;
}
.stat span {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 999px;
  background: #f5e9ae;
  color: #5a4900;
  font-size: 10px;
  letter-spacing: .35px;
}
.stat strong { margin-top: 12px; font-size: 30px; }
.panel {
  margin-bottom: 24px;
  border-radius: var(--radius) !important;
  box-shadow: 0 8px 24px #11121408;
}
.panel-heading {
  padding: 16px 18px;
  border-bottom: 1px solid var(--line);
  color: var(--ink);
  font-size: 15px;
  font-weight: 850;
}
.panel > .table-wrap {
  border: 0;
  border-radius: 0 !important;
  box-shadow: none;
}
.admin-form {
  gap: 12px;
  padding: 16px;
}
.admin-form label {
  gap: 6px;
  color: var(--ink);
  font-size: 11px;
  font-weight: 750;
}
.admin-form input,
.admin-form select,
.admin-form textarea {
  min-height: 40px;
  padding: 9px 11px;
  border-radius: 9px;
  background: #f7f7f8;
  font-size: 13px;
}
.admin-form textarea { min-height: 80px; }
.admin-form .actions { align-items: center; flex-wrap: wrap; }
.actions { flex-wrap: wrap; }
.action {
  min-height: 34px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 7px 12px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 800;
}
.action:not(.primary) {
  border-color: var(--line) !important;
  background: #fff !important;
  color: var(--ink) !important;
}
.inline-form { flex-wrap: wrap; }
.flash {
  border-radius: 10px;
  box-shadow: 0 6px 18px #11121408;
}
table th { padding: 12px 14px; font-size: 10px; letter-spacing: .5px; }
table td { padding: 12px 14px; font-size: 12px; }
.badge { padding: 5px 9px; font-size: 10px; }

@media (max-width: 900px) {
  .admin-layout { grid-template-columns: 210px minmax(0, 1fr); gap: 20px; }
  .admin-form { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 760px) {
  .admin-layout {
    width: min(100% - 28px, 640px);
    margin: 20px auto 36px;
    grid-template-columns: 1fr;
    gap: 18px;
  }
  .sidebar { padding: 14px; }
  .side-group { margin-top: 10px; padding-top: 10px; }
  .page-heading { margin-bottom: 18px; }
  .stats { gap: 10px; }
  .stat { min-height: 90px; padding: 12px; }
  .stat strong { font-size: 26px; }
}
@media (max-width: 480px) {
  .page-heading h1 { font-size: 28px !important; }
  .stats { gap: 8px; }
  .panel-heading { padding: 13px 14px; }
  .admin-form { grid-template-columns: 1fr; padding: 12px; }
  .admin-form .wide,
  .admin-form .full { grid-column: 1; }
  table th,
  table td { padding: 10px; }
}
.admin-page-heading {
  width: min(1280px, calc(100% - 48px));
  margin: 28px auto 22px;
}
.admin-page-heading h1 {
  margin: 0;
  color: var(--ink);
  font-size: clamp(30px, 4vw, 40px);
  font-weight: 900;
  letter-spacing: -1.2px;
  line-height: 1.1;
  text-transform: uppercase;
}
.admin-page-heading p {
  max-width: 540px;
  margin: 8px 0 0;
  color: var(--muted);
  font-size: 13px;
}
.admin-layout {
  width: min(1280px, calc(100% - 48px));
  margin-top: 0;
  grid-template-columns: 230px minmax(0, 1fr);
  gap: 22px;
}
.sidebar {
  position: sticky;
  top: 16px;
  height: min(545px, calc(100vh - 150px));
  overflow-y: auto;
  overscroll-behavior: contain;
  scrollbar-color: #a1a1a1 #f5f5f6;
  scrollbar-width: thin;
}
.side-nav { display: block; }
.side-nav > a { margin-bottom: 4px; }
.side-group { margin-top: 12px; }
.content {
  padding: 22px;
  border: 1px solid var(--line);
  border-radius: var(--radius);
  background: #fff;
  box-shadow: 0 8px 24px #11121408;
}
.content > #dashboard { display: contents; }
.content .page-heading {
  margin-bottom: 18px;
  padding-bottom: 0;
  border: 0;
}
.content .page-heading h1 { font-size: 20px !important; }
.content .stats { grid-template-columns: repeat(4, minmax(0, 1fr)); }
.content .admin-form { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.content .panel {
  border-radius: 12px !important;
  box-shadow: none;
}
.content .panel-heading { padding: 16px 18px; }
.admin-app-ready [hidden],
.admin-app-ready .content .panel[hidden],
.admin-app-ready .content .page-heading[hidden] {
  display: none !important;
}
.admin-app-ready .content [data-console-panel][hidden],
.admin-app-ready .content [data-admin-view][hidden] {
  display: none !important;
}
.side-nav a[aria-current="page"] {
  background: #ececef;
  color: var(--ink);
  font-weight: 850;
}
.side-nav a.active { background: #ececef; color: var(--ink); }
.content .admin-form label { text-transform: uppercase; }
.content .action.primary {
  border-color: #111214 !important;
  background: #111214 !important;
  color: #fff !important;
}
.section-tools {
  display: flex;
  align-items: end;
  justify-content: flex-start;
  flex-wrap: wrap;
  gap: 10px;
}
.section-tools label {
  display: grid;
  gap: 4px;
  color: var(--muted);
  font-size: 11px;
  font-weight: 700;
}
.section-tools input,
.section-tools select { min-height: 36px; padding: 7px 9px; }
.quick-actions { display: flex; flex-wrap: wrap; gap: 10px; padding: 14px; }
.lifecycle-form { display: grid; min-width: 230px; gap: 7px; }
.lifecycle-form label { display: grid; gap: 3px; color: var(--muted); font-size: 11px; }
.lifecycle-form input,
.lifecycle-form select { width: 100%; min-height: 34px; padding: 6px; }
.chart-bar { min-width: 100px; height: 10px; overflow: hidden; border-radius: 99px; background: #ececef; }
.chart-bar span { display: block; height: 100%; border-radius: inherit; background: var(--yellow); }
.payment-stats { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; padding: 12px; }
.muted { color: var(--muted); font-size: 12px; }
@media (max-width: 760px) {
  .admin-page-heading {
    width: min(100% - 28px, 640px);
    margin: 22px auto 16px;
  }
  .admin-layout {
    width: min(100% - 28px, 640px);
    margin-top: 0;
  }
  .sidebar {
    position: sticky;
    top: 12px;
    height: min(280px, calc(100vh - 120px));
  }
  .content { padding: 14px; }
  .content .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 480px) {
  .content .admin-form { grid-template-columns: 1fr; }
}
  </style>
</head>
<body>
  <header class="site-header">
    <a class="brand" href="admin_panel.php">AUTORIDE ADMIN</a>
    <nav class="nav" aria-label="Administrator navigation">
      <a href="admin_panel.php?section=dashboard" aria-current="page">Dashboard</a>
      <a href="admin_profile.php">Profile</a>
      <form action="log_in.php" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
        <input type="hidden" name="action" value="admin_logout">
        <button type="submit">Logout</button>
      </form>
    </nav>
  </header>

  <div class="admin-layout">
    <aside class="sidebar" aria-label="Admin menu">
      <p class="side-label">Admin Console</p>
      <nav class="side-nav">
        <a href="admin_panel.php?section=dashboard" data-console-view="dashboard">Dashboard Overview</a>
        <div class="side-group">
          <h2>Manage Vehicles</h2>
          <a href="admin_panel.php?section=vehicle-add" data-console-view="vehicle-add">Add Vehicle</a>
          <a href="admin_panel.php?section=vehicle-edit" data-console-view="vehicle-edit">Edit Vehicle</a>
          <a href="admin_panel.php?section=vehicle-delete" data-console-view="vehicle-delete">Delete Vehicle</a>
          <a href="admin_panel.php?section=vehicle-view" data-console-view="vehicle-view">View Fleet</a>
        </div>
        <div class="side-group">
          <h2>Manage Vehicle Categories</h2>
          <a href="admin_panel.php?section=category-add" data-console-view="category-add">Add Category</a>
          <a href="admin_panel.php?section=category-edit" data-console-view="category-edit">Edit Category</a>
        </div>
        <div class="side-group">
          <h2>Manage Rental Rates</h2>
          <a href="admin_panel.php?section=rates-set" data-console-view="rates-set">Set Rental Rates</a>
          <a href="admin_panel.php?section=rates-update" data-console-view="rates-update">Update Rental Rates</a>
        </div>
        <div class="side-group">
          <h2>Manage Customers</h2>
          <a href="admin_panel.php?section=customer-view" data-console-view="customer-view">View Customers</a>
          <a href="admin_panel.php?section=customer-edit" data-console-view="customer-edit">Edit Customer</a>
          <a href="admin_panel.php?section=customer-disable" data-console-view="customer-disable">Disable Customer</a>
        </div>
        <div class="side-group">
          <h2>Rental Requests</h2>
          <a href="admin_panel.php?section=requests-view" data-console-view="requests-view">View Requests</a>
          <a href="admin_panel.php?section=requests-approve" data-console-view="requests-approve">Approve / Reject Requests</a>
        </div>
        <div class="side-group">
          <h2>Billing &amp; Payments</h2>
          <a href="admin_panel.php?section=payments" data-console-view="payments">View Payments</a>
        </div>
        <div class="side-group">
          <h2>Reviews</h2>
          <a href="admin_panel.php?section=reviews" data-console-view="reviews">Moderate Reviews</a>
        </div>
        <div class="side-group">
          <h2>Record Release/Return</h2>
          <a href="admin_panel.php?section=release" data-console-view="release">Record Vehicle Release</a>
          <a href="admin_panel.php?section=return" data-console-view="return">Record Vehicle Return</a>
        </div>
        <div class="side-group">
          <h2>Manage Maintenance</h2>
          <a href="admin_panel.php?section=maintenance-add" data-console-view="maintenance-add">Record Maintenance</a>
          <a href="admin_panel.php?section=maintenance-view" data-console-view="maintenance-view">View Maintenance</a>
        </div>
        <div class="side-group">
          <h2>Reports</h2>
          <a href="admin_panel.php?section=report-rental" data-console-view="report-rental">Rental Report</a>
          <a href="admin_panel.php?section=report-vehicle" data-console-view="report-vehicle">Vehicle Report</a>
          <a href="admin_panel.php?section=report-customer" data-console-view="report-customer">Customer Report</a>
        </div>
      </nav>
    </aside>

    <main class="content">
      <header class="selected-view-heading">
        <h1 id="selected-view-title">Dashboard Overview</h1>
      </header>
      <?php if ($flashMessage !== ''): ?><p class="flash" role="status"><?php echo e($flashMessage); ?></p><?php endif; ?>
      <section id="dashboard" data-console-panel="dashboard" aria-labelledby="dashboard-title">
        <p class="notice">Live totals for this session. Customer, vehicle, and transaction examples are marked as demo data.</p>
        <div class="stats" data-admin-view="dashboard">
          <article class="stat"><span>TOTAL VEHICLES</span><strong><?php echo (int) $dashboardStats['vehicles']; ?></strong></article>
          <article class="stat"><span>REGISTERED CUSTOMERS</span><strong><?php echo (int) $dashboardStats['customers']; ?></strong></article>
          <article class="stat"><span>PENDING REQUESTS</span><strong><?php echo (int) $dashboardStats['pending_requests']; ?></strong></article>
          <article class="stat"><span>ACTIVE RENTALS</span><strong><?php echo (int) $dashboardStats['active_rentals']; ?></strong></article>
          <article class="stat"><span>COMPLETED RENTALS</span><strong><?php echo (int) $dashboardStats['completed_rentals']; ?></strong></article>
          <article class="stat"><span>ACTUAL REVENUE</span><strong>₱<?php echo number_format((float) $dashboardStats['revenue'], 2); ?></strong></article>
        </div>
      </section>
      <section class="panel" data-console-panel="dashboard">
        <div class="panel-heading">Quick Actions</div>
        <div class="quick-actions">
          <a class="action primary" href="admin_panel.php?section=vehicle-add" data-console-view="vehicle-add">Add Vehicle</a>
          <a class="action primary" href="admin_panel.php?section=requests-view" data-console-view="requests-view">View Requests</a>
          <a class="action primary" href="admin_panel.php?section=vehicle-view" data-console-view="vehicle-view">View Fleet</a>
        </div>
      </section>
      <section class="panel" data-console-panel="dashboard">
        <div class="panel-heading">Recent Rental Requests</div>
        <div class="table-wrap"><table><thead><tr><th>Request</th><th>Customer</th><th>Vehicle</th><th>Dates</th><th>Status</th></tr></thead><tbody>
          <?php $dashboardBookings = array_slice(array_reverse($allBookings), 0, 5); ?>
          <?php if ($dashboardBookings): foreach ($dashboardBookings as $booking): ?>
            <tr><td>#<?php echo (int) $booking['id']; ?><?php if (!empty($booking['is_demo'])): ?><br><span class="badge">Sample</span><?php endif; ?></td><td><?php echo e($booking['full_name'] ?? 'Customer'); ?></td><td><?php echo e($booking['vehicle_name'] ?? 'Vehicle'); ?></td><td><?php echo e($booking['pickup_date'] ?? '—'); ?> – <?php echo e($booking['return_date'] ?? '—'); ?></td><td><span class="badge <?php echo e($booking['status'] ?? ''); ?>"><?php echo e(ucfirst($booking['status'] ?? 'Unknown')); ?></span></td></tr>
          <?php endforeach; else: ?><tr><td class="empty-row" colspan="5">No rental requests yet. Submitted requests will appear here.</td></tr><?php endif; ?>
        </tbody></table></div>
      </section>
      <section class="panel" data-console-panel="dashboard">
        <div class="panel-heading">Recent Payment Transactions</div>
        <div class="table-wrap"><table><thead><tr><th>Transaction</th><th>Customer</th><th>Rental</th><th>Amount</th><th>Status</th></tr></thead><tbody>
          <?php $dashboardPayments = array_slice(array_reverse($allPayments), 0, 5); ?>
          <?php if ($dashboardPayments): foreach ($dashboardPayments as $payment): ?>
            <tr><td><?php echo e($payment['reference_code'] ?? ('PAY-' . (int) ($payment['id'] ?? 0))); ?><?php if (!empty($payment['is_demo'])): ?><br><span class="badge">Sample</span><?php endif; ?></td><td><?php echo e($payment['customer_name'] ?? 'Customer'); ?></td><td>#<?php echo (int) ($payment['booking_id'] ?? 0); ?></td><td>₱<?php echo number_format((float) ($payment['amount'] ?? 0), 2); ?></td><td><span class="badge <?php echo e($payment['status'] ?? 'pending'); ?>"><?php echo e(ucfirst($payment['status'] ?? 'pending')); ?></span></td></tr>
          <?php endforeach; else: ?><tr><td class="empty-row" colspan="5">No payment transactions have been recorded.</td></tr><?php endif; ?>
        </tbody></table></div>
        <p class="notice">Actual revenue excludes all records marked Sample. Sample payment total: ₱<?php echo number_format((float) $dashboardStats['sample_revenue'], 2); ?></p>
      </section>
      <section class="panel" data-console-panel="dashboard">
        <div class="panel-heading">Vehicle Availability</div>
        <div class="table-wrap"><table><thead><tr><th>Status</th><th>Vehicles</th><th>Fleet Share</th></tr></thead><tbody>
          <?php foreach (['available', 'rented', 'maintenance', 'inactive'] as $availabilityStatus): $availabilityCount = count(array_filter($vehicles, static function ($vehicle) use ($availabilityStatus): bool { return ($vehicle['status'] ?? '') === $availabilityStatus; })); ?>
            <tr><td><span class="badge <?php echo e($availabilityStatus); ?>"><?php echo e(ucwords($availabilityStatus)); ?></span></td><td><?php echo $availabilityCount; ?></td><td><?php echo $vehicles ? number_format($availabilityCount * 100 / count($vehicles), 1) : '0.0'; ?>%</td></tr>
          <?php endforeach; ?>
        </tbody></table></div>
      </section>
      <section class="panel" data-console-panel="dashboard">
        <div class="panel-heading">Latest Customer Reviews</div>
        <div class="table-wrap"><table><thead><tr><th>Customer</th><th>Vehicle</th><th>Rating</th><th>Comment</th><th>Submitted</th></tr></thead><tbody>
          <?php $dashboardReviews = array_slice(array_reverse($allReviews), 0, 5); ?>
          <?php if ($dashboardReviews): foreach ($dashboardReviews as $review): ?>
            <tr><td><?php echo e($review['customer_name'] ?? 'Customer'); ?><?php if (!empty($review['is_demo'])): ?><br><span class="badge">Sample</span><?php endif; ?></td><td><?php echo e($review['vehicle_name'] ?? 'Vehicle'); ?></td><td><?php echo str_repeat('★', (int) ($review['rating'] ?? 0)); ?> (<?php echo (int) ($review['rating'] ?? 0); ?>/5)</td><td><?php echo e($review['review_text'] ?? $review['review'] ?? ''); ?></td><td><?php echo e($review['created_at'] ?? '—'); ?></td></tr>
          <?php endforeach; else: ?><tr><td class="empty-row" colspan="5">No customer reviews have been submitted.</td></tr><?php endif; ?>
        </tbody></table></div>
      </section>
      <section class="panel" data-console-panel="dashboard">
        <div class="panel-heading">Monthly Actual Revenue</div>
        <div class="table-wrap"><table><thead><tr><th>Month</th><th>Paid Revenue</th><th>Visual</th></tr></thead><tbody>
          <?php $chartMax = $monthlyRevenue ? max($monthlyRevenue) : 0; ?>
          <?php if ($monthlyRevenue): foreach (array_slice($monthlyRevenue, 0, 6, true) as $month => $amount): ?>
            <tr><td><?php echo e(date('M Y', strtotime($month . '-01'))); ?></td><td>₱<?php echo number_format((float) $amount, 2); ?></td><td><div class="chart-bar"><span style="width:<?php echo $chartMax > 0 ? (int) round($amount * 100 / $chartMax) : 0; ?>%"></span></div></td></tr>
          <?php endforeach; else: ?><tr><td class="empty-row" colspan="3">No non-sample payment data is available for this chart.</td></tr><?php endif; ?>
        </tbody></table></div>
      </section>
      <section class="panel" id="requests" data-console-panel="requests-view requests-approve" aria-labelledby="requests-title">
          <div class="panel-heading" id="requests-title">Recent Rental Requests and Status</div>
          <div class="table-wrap" id="request-list" data-admin-view="requests-view requests-approve" tabindex="-1">
            <div class="section-tools" data-admin-view="requests-view">
              <label>Search <input id="request-search" type="search" placeholder="Customer or vehicle"></label>
              <label>Status <select id="request-status-filter"><option value="all">All statuses</option><option value="requested">Pending</option><option value="confirmed">Approved</option><option value="rejected">Rejected</option><option value="completed">Completed</option></select></label>
            </div>
            <table>
              <thead>
                <tr><th>ID</th><th>Customer</th><th>Vehicle</th><th>Pickup / Return</th><th>Duration</th><th>Total</th><th>Status</th><th>Action</th></tr>
              </thead>
              <tbody>
                <?php if ($allBookings): ?>
                  <?php foreach (array_slice(array_reverse($allBookings), 0, 10) as $request): ?>
                    <tr data-request-status="<?php echo e($request['status']); ?>">
                      <td><?php echo (int) $request['id']; ?></td>
                      <td><?php echo e($request['full_name']); ?><?php if (!empty($request['is_demo'])): ?><br><span class="badge">Demo</span><?php endif; ?></td>
                      <td><?php echo e($request['vehicle_name']); ?></td>
                      <td><?php echo e($request['pickup_date']); ?><br><?php echo e($request['return_date']); ?></td>
                      <td><?php echo (int) ($request['rental_days'] ?? $request['days'] ?? 0); ?> day(s)</td>
                      <td>PHP <?php echo number_format((float) $request['total_amount'], 2); ?></td>
                      <?php $requestStatus = $request['status'] === 'confirmed' ? 'approved' : ($request['status'] === 'requested' ? 'pending' : $request['status']); ?>
                      <td><span class="badge <?php echo e($requestStatus); ?>"><?php echo e($request['status'] === 'confirmed' ? 'Approved' : ($request['status'] === 'requested' ? 'Pending' : ucfirst($request['status']))); ?></span></td>
                      <td>
                        <?php if ($request['status'] === 'requested'): ?>
                          <form class="inline-form" method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
                            <input type="hidden" name="action" value="booking_decision">
                            <input type="hidden" name="booking_id" value="<?php echo (int) $request['id']; ?>">
                            <button class="action primary" name="decision" value="confirmed" type="submit">Approve</button>
                            <button class="action" name="decision" value="rejected" type="submit">Reject</button>
                          </form>
                        <?php else: ?>
                          <span class="empty-link">No action</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
                <tr class="empty-row" data-request-empty="all" hidden><td colspan="8">No rental requests yet. New customer requests will appear here.</td></tr>
                <tr class="empty-row" data-request-empty="pending" hidden><td colspan="8">There are no pending rental requests to approve or reject.</td></tr>
              </tbody>
            </table>
          </div>
        </section>

        <section class="panel" id="vehicles" data-console-panel="vehicle-add vehicle-edit vehicle-delete vehicle-view">
          <div class="panel-heading">Manage Vehicles</div>
          <form class="admin-form" id="vehicle-form" data-admin-view="vehicle-add vehicle-edit" data-record-selected="<?php echo $editVehicle ? 'true' : 'false'; ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="action" value="<?php echo $editVehicle ? 'vehicle_update' : 'vehicle_add'; ?>">
            <input type="hidden" name="vehicle_id" value="<?php echo (int) ($editVehicle['id'] ?? 0); ?>">
            <label>Vehicle slug<input name="slug" required pattern="[a-z0-9]+(-[a-z0-9]+)*" value="<?php echo e($editVehicle['slug'] ?? ''); ?>" placeholder="toyota-fortuner"></label>
            <label>Brand<input name="brand" required maxlength="80" value="<?php echo e($editVehicle['brand'] ?? ''); ?>"></label>
            <label>Vehicle name<input name="name" required maxlength="140" value="<?php echo e($editVehicle['name'] ?? ''); ?>"></label>
            <label>Model<input name="model" maxlength="100" value="<?php echo e($editVehicle['model'] ?? ''); ?>" placeholder="Vios 1.3 E"></label>
            <label>Plate number<input name="plate_number" maxlength="20" required value="<?php echo e($editVehicle['plate_number'] ?? ''); ?>" placeholder="ABC-1234"></label>
            <label>Image path<input name="image" maxlength="120" value="<?php echo e($editVehicle['image'] ?? 'vehicle-placeholder.svg'); ?>" placeholder="vehicle-placeholder.svg"></label>
            <label>Upload vehicle image<input name="vehicle_image" type="file" accept="image/jpeg,image/png,image/webp"><small>Optional JPG, PNG, or WebP file; maximum 5 MB.</small></label>
            <label>Category<select name="category_id" required><option value="">Select category</option><?php foreach ($categories as $category): ?><option value="<?php echo (int) $category['id']; ?>" <?php echo (int) ($editVehicle['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : ''; ?>><?php echo e($category['name']); ?></option><?php endforeach; ?></select></label>
            <label>Daily rate (PHP)<input name="daily_rate" type="number" min="0" step="0.01" required value="<?php echo e($editVehicle['daily_rate'] ?? ''); ?>"></label>
            <label>Fuel type<input name="fuel_type" required maxlength="40" value="<?php echo e($editVehicle['fuel_type'] ?? ''); ?>"></label>
            <label>Transmission<input name="transmission" required maxlength="40" value="<?php echo e($editVehicle['transmission'] ?? ''); ?>"></label>
            <label>Seats<input name="seats" type="number" min="1" max="255" required value="<?php echo e($editVehicle['seats'] ?? ''); ?>"></label>
            <label>Model year<input name="model_year" type="number" min="1901" max="2155" required value="<?php echo e($editVehicle['model_year'] ?? date('Y')); ?>"></label>
            <label>Mileage (km)<input name="mileage_km" type="number" min="0" required value="<?php echo e($editVehicle['mileage_km'] ?? 0); ?>"></label>
            <label>Engine<input name="engine" required maxlength="100" value="<?php echo e($editVehicle['engine'] ?? ''); ?>"></label>
            <label>Location<input name="location" required maxlength="120" value="<?php echo e($editVehicle['location'] ?? ''); ?>"></label>
            <label>Insurance<input name="insurance" required maxlength="120" value="<?php echo e($editVehicle['insurance'] ?? 'Comprehensive'); ?>"></label>
            <label>Status<select name="status"><?php foreach (['available', 'rented', 'maintenance', 'inactive'] as $status): ?><option value="<?php echo e($status); ?>" <?php echo ($editVehicle['status'] ?? 'available') === $status ? 'selected' : ''; ?>><?php echo e(ucwords($status)); ?></option><?php endforeach; ?></select></label>
            <label class="full">Description<textarea name="description" required><?php echo e($editVehicle['description'] ?? ''); ?></textarea></label>
            <div class="full actions"><button class="action primary" type="submit"><?php echo $editVehicle ? 'Update Vehicle' : 'Add Vehicle'; ?></button><?php if ($editVehicle): ?><a class="action" href="admin_panel.php?section=vehicle-edit">Cancel</a><?php endif; ?></div>
          </form>
          <?php if (!$categories): ?><p class="empty-row" data-admin-view="vehicle-add">No vehicle categories exist yet. Add a category before registering a vehicle.</p><?php endif; ?>
          <div class="table-wrap" id="vehicle-list" data-admin-view="vehicle-edit vehicle-delete vehicle-view" tabindex="-1">
            <div class="section-tools">
              <label>Search fleet <input id="fleet-search" type="search" placeholder="Vehicle, brand, plate"></label>
              <label>Category <select id="fleet-category"><option value="">All categories</option><?php foreach ($categories as $category): ?><option value="<?php echo e(strtolower($category['name'])); ?>"><?php echo e($category['name']); ?></option><?php endforeach; ?></select></label>
              <label>Availability <select id="fleet-status"><option value="">All statuses</option><?php foreach (['available', 'rented', 'maintenance', 'inactive'] as $status): ?><option value="<?php echo e($status); ?>"><?php echo e(ucwords($status)); ?></option><?php endforeach; ?></select></label>
              <label>Sort by <select id="fleet-sort"><option value="">Default order</option><option value="name">Name</option><option value="rate-low">Rate: low to high</option><option value="rate-high">Rate: high to low</option></select></label>
            </div>
            <table id="vehicle-table">
            <thead><tr><th>Image</th><th>Vehicle / Model</th><th>Category</th><th>Plate</th><th>Daily Rate</th><th>Availability</th><th data-vehicle-actions>Actions</th></tr></thead>
            <tbody>
              <?php if ($vehicles): ?><?php foreach ($vehicles as $vehicle): ?>
                <tr data-vehicle-category="<?php echo e(strtolower($vehicle['category'])); ?>" data-vehicle-name="<?php echo e(strtolower($vehicle['brand'] . ' ' . $vehicle['name'])); ?>" data-vehicle-rate="<?php echo e($vehicle['daily_rate']); ?>">
                  <td><img src="<?php echo e($vehicle['image'] ?? 'vehicle-placeholder.svg'); ?>" alt="" width="76" height="44" loading="lazy" style="object-fit:cover;border-radius:4px"></td><td><?php echo e($vehicle['brand'] . ' ' . $vehicle['name']); ?><br><?php echo e($vehicle['model'] ?? $vehicle['name']); ?> · <?php echo (int) $vehicle['model_year']; ?><?php if (!empty($vehicle['is_demo'])): ?><br><span class="badge">Sample fleet</span><?php endif; ?></td><td><?php echo e($vehicle['category']); ?></td><td><?php echo e($vehicle['plate_number'] ?? '—'); ?></td><td>PHP <?php echo number_format((float) $vehicle['daily_rate'], 2); ?></td><td><span class="badge <?php echo e($vehicle['status']); ?>"><?php echo e($vehicle['status']); ?></span></td>
                  <td data-vehicle-actions><div class="actions"><a class="action" data-console-action="edit" href="admin_panel.php?section=vehicle-edit&amp;edit_vehicle=<?php echo (int) $vehicle['id']; ?>">Edit</a><form class="inline-form" data-console-action="delete" method="post" onsubmit="return confirm('Delete this vehicle? Vehicles with rental history cannot be deleted.');"><input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>"><input type="hidden" name="action" value="vehicle_delete"><input type="hidden" name="vehicle_id" value="<?php echo (int) $vehicle['id']; ?>"><button class="action" type="submit">Delete</button></form></div></td>
                </tr>
              <?php endforeach; ?><?php else: ?><tr><td class="empty-row" colspan="7">No vehicles have been added.</td></tr><?php endif; ?>
            </tbody>
          </table></div>
        </section>

        <section class="panel" id="categories" data-console-panel="category-add category-edit">
          <div class="panel-heading">Vehicle Categories</div>
          <form class="admin-form" id="category-form" data-admin-view="category-add category-edit" data-record-selected="<?php echo $editCategory ? 'true' : 'false'; ?>" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>"><input type="hidden" name="action" value="<?php echo $editCategory ? 'category_update' : 'category_add'; ?>"><input type="hidden" name="category_id" value="<?php echo (int) ($editCategory['id'] ?? 0); ?>">
            <label>Category name<input name="name" maxlength="60" required value="<?php echo e($editCategory['name'] ?? ''); ?>"></label>
            <label>Category icon / image path<input name="icon" maxlength="120" value="<?php echo e($editCategory['icon'] ?? ''); ?>" placeholder="category-icon.svg"></label>
            <label class="full">Description<textarea name="description" maxlength="500" placeholder="Describe the vehicles in this category"><?php echo e($editCategory['description'] ?? ''); ?></textarea></label>
            <div class="actions"><button class="action primary" type="submit"><?php echo $editCategory ? 'Update Category' : 'Add Category'; ?></button><?php if ($editCategory): ?><a class="action" href="admin_panel.php?section=category-edit">Cancel</a><?php endif; ?></div>
          </form>
          <div class="table-wrap" id="category-list" data-admin-view="category-edit" tabindex="-1"><table>
            <thead><tr><th>Category</th><th>Description</th><th>Vehicles</th><th>Actions</th></tr></thead>
            <tbody><?php if ($categories): ?><?php foreach ($categories as $category): ?><tr><td><?php echo e($category['name']); ?><?php if (!empty($category['is_demo'])): ?><br><span class="badge">Sample</span><?php endif; ?></td><td><?php echo e($category['description'] ?? 'Vehicle category'); ?></td><td><?php echo (int) $category['vehicle_count']; ?></td><td><div class="actions"><a class="action" href="admin_panel.php?section=category-edit&amp;edit_category=<?php echo (int) $category['id']; ?>">Edit</a><form class="inline-form" method="post" onsubmit="return confirm('Delete this category? Categories assigned to vehicles cannot be deleted.');"><input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>"><input type="hidden" name="action" value="category_delete"><input type="hidden" name="category_id" value="<?php echo (int) $category['id']; ?>"><button class="action" type="submit">Delete</button></form></div></td></tr><?php endforeach; ?><?php else: ?><tr><td class="empty-row" colspan="4">No categories have been added.</td></tr><?php endif; ?></tbody>
          </table></div>
        </section>

        <section class="panel" id="rates" data-console-panel="rates-set rates-update">
          <div class="panel-heading">Rental Rates</div>
          <form class="admin-form" data-admin-view="rates-set" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>"><input type="hidden" name="action" value="rate_update">
            <label>Vehicle<select name="vehicle_id" required><option value="">Select a vehicle</option><?php foreach ($vehicles as $vehicle): ?><option value="<?php echo (int) $vehicle['id']; ?>"><?php echo e($vehicle['name']); ?> — <?php echo e($vehicle['category']); ?></option><?php endforeach; ?></select></label>
            <label>Daily rental rate (PHP)<input name="daily_rate" type="number" min="0" step="0.01" required></label>
            <label>Minimum rental period (days)<input name="minimum_days" type="number" min="1" max="365" value="1" required></label>
            <label>Additional fee (PHP)<input name="additional_fee" type="number" min="0" step="0.01" value="0" required></label>
            <div class="actions"><button class="action primary" type="submit">Set Rental Rate</button></div>
          </form>
          <div class="table-wrap" id="rates-list" data-admin-view="rates-update" tabindex="-1"><table>
            <thead><tr><th>Vehicle / Category</th><th>Daily Rate</th><th>Minimum Days</th><th>Additional Fee</th><th>Status</th><th>Update</th></tr></thead>
            <tbody><?php foreach ($vehicles as $vehicle): ?><tr><td><?php echo e($vehicle['name']); ?><br><span class="muted"><?php echo e($vehicle['category']); ?></span></td><td>PHP <?php echo number_format((float) $vehicle['daily_rate'], 2); ?></td><td><?php echo (int) ($vehicle['minimum_rental_days'] ?? 1); ?></td><td>PHP <?php echo number_format((float) ($vehicle['additional_fee'] ?? 0), 2); ?></td><td><span class="badge <?php echo e($vehicle['status']); ?>"><?php echo e(ucwords($vehicle['status'])); ?></span></td><td><form class="inline-form" method="post"><input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>"><input type="hidden" name="action" value="rate_update"><input type="hidden" name="vehicle_id" value="<?php echo (int) $vehicle['id']; ?>"><input aria-label="Daily rate for <?php echo e($vehicle['name']); ?>" name="daily_rate" type="number" min="0" step="0.01" required value="<?php echo e($vehicle['daily_rate']); ?>"><input aria-label="Minimum rental days for <?php echo e($vehicle['name']); ?>" name="minimum_days" type="number" min="1" max="365" required value="<?php echo (int) ($vehicle['minimum_rental_days'] ?? 1); ?>"><input aria-label="Additional fee for <?php echo e($vehicle['name']); ?>" name="additional_fee" type="number" min="0" step="0.01" required value="<?php echo e($vehicle['additional_fee'] ?? 0); ?>"><button class="action primary" type="submit">Update</button></form></td></tr><?php endforeach; ?><?php if (!$vehicles): ?><tr><td class="empty-row" colspan="6">No vehicle rates are available.</td></tr><?php endif; ?></tbody>
          </table></div>
        </section>

        <section class="panel" id="customers" data-console-panel="customer-view customer-edit customer-disable">
          <div class="panel-heading">Customers</div>
          <div class="section-tools" data-admin-view="customer-view customer-disable">
            <label>Search customers <input id="customer-search" type="search" placeholder="Name, email, phone"></label>
            <label>Account status <select id="customer-status-filter"><option value="">All accounts</option><option value="active">Active</option><option value="disabled">Disabled</option></select></label>
          </div>
          <div class="table-wrap" id="customer-list" data-admin-view="customer-view customer-edit customer-disable" tabindex="-1"><table>
            <thead><tr><th>ID</th><th>Customer</th><th>Contact</th><th>Registered</th><th>Total Rentals</th><th>Status</th><th data-customer-actions>Actions</th></tr></thead>
            <tbody><?php if ($customers): ?><?php foreach ($customers as $customer): ?><tr data-customer-status="<?php echo e($customer['status'] ?? 'active'); ?>">
              <td><?php echo (int) $customer['id']; ?><?php if (!empty($customer['is_demo'])): ?><br><span class="badge">Sample</span><?php endif; ?></td><td><?php echo e($customer['full_name']); ?></td><td><?php echo e($customer['email']); ?><br><?php echo e($customer['phone']); ?></td><td><?php echo e($customer['created_at'] ?? '—'); ?></td><td><?php echo (int) $customer['rental_count']; ?></td><td><span class="badge <?php echo e($customer['status']); ?>"><?php echo e(ucfirst($customer['status'] ?? 'active')); ?></span></td>
              <td data-customer-actions><details><summary>Edit / Manage</summary><form class="admin-form" data-customer-action="edit" method="post"><input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>"><input type="hidden" name="action" value="customer_save"><input type="hidden" name="customer_id" value="<?php echo (int) $customer['id']; ?>"><label>Name<input name="full_name" required maxlength="120" value="<?php echo e($customer['full_name']); ?>"></label><label>Email<input name="email" type="email" required maxlength="254" value="<?php echo e($customer['email']); ?>"></label><label>Phone<input name="phone" required maxlength="40" value="<?php echo e($customer['phone']); ?>"></label><label class="full">Address<input name="address" maxlength="255" value="<?php echo e($customer['address'] ?? ''); ?>"></label><button class="action primary" type="submit">Save</button></form><form class="inline-form" data-customer-action="disable" method="post" onsubmit="return confirm('Change this customer account status?');"><input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>"><input type="hidden" name="action" value="customer_status"><input type="hidden" name="customer_id" value="<?php echo (int) $customer['id']; ?>"><input type="hidden" name="status" value="<?php echo ($customer['status'] ?? 'active') === 'active' ? 'disabled' : 'active'; ?>"><button class="action" type="submit"><?php echo ($customer['status'] ?? 'active') === 'active' ? 'Disable Account' : 'Reactivate Account'; ?></button></form></details></td>
            </tr><?php endforeach; ?><?php else: ?><tr><td class="empty-row" colspan="5">No customer accounts found.</td></tr><?php endif; ?></tbody>
          </table></div>
        </section>

        <section class="panel" id="payments" data-console-panel="payments">
          <div class="panel-heading">Billing and Payments</div>
          <p class="notice">Payments are records entered in this session; no external payment gateway is connected.</p>
          <div class="stats payment-stats">
            <article class="stat"><span>ACTUAL PAID</span><strong>₱<?php echo number_format((float) $dashboardStats['paid_amount'], 2); ?></strong></article>
            <article class="stat"><span>ACTUAL OUTSTANDING</span><strong>₱<?php echo number_format((float) $dashboardStats['outstanding_amount'], 2); ?></strong></article>
            <article class="stat"><span>SAMPLE PAYMENTS</span><strong><?php echo count(array_filter($allPayments, static function ($payment): bool { return !empty($payment['is_demo']); })); ?></strong></article>
          </div>
          <div class="section-tools" data-admin-view="payments">
            <label>Payment status <select id="payment-status-filter"><option value="">All statuses</option><?php foreach (['paid', 'pending', 'failed', 'unpaid', 'partial', 'void'] as $paymentStatus): ?><option value="<?php echo e($paymentStatus); ?>"><?php echo e(ucfirst($paymentStatus)); ?></option><?php endforeach; ?></select></label>
          </div>
          <div class="table-wrap" id="payment-list" data-admin-view="payments" tabindex="-1"><table>
            <thead><tr><th>Transaction</th><th>Customer</th><th>Rental Ref.</th><th>Rental Charge</th><th>Additional Fees</th><th>Amount Paid</th><th>Method / Date</th><th>Status</th></tr></thead>
            <tbody>
              <?php if ($allPayments): ?><?php foreach ($allPayments as $payment): ?>
                <?php $paymentBooking = $paymentBookingMap[(int) ($payment['booking_id'] ?? 0)] ?? []; $paymentStatus = strtolower((string) ($payment['status'] ?? 'pending')); ?>
                <tr data-payment-status="<?php echo e($paymentStatus); ?>"><td><?php echo e($payment['reference_code'] ?? ('PAY-' . (int) ($payment['id'] ?? 0))); ?><?php if (!empty($payment['is_demo'])): ?><br><span class="badge">Sample</span><?php endif; ?></td><td><?php echo e($payment['customer_name'] ?? 'Customer'); ?></td><td>#<?php echo (int) ($payment['booking_id'] ?? 0); ?><br><?php echo e($paymentBooking['vehicle_name'] ?? $payment['vehicle_name'] ?? 'Vehicle'); ?></td><td>₱<?php echo number_format((float) ($paymentBooking['daily_rate'] ?? 0) * (int) ($paymentBooking['days'] ?? 0), 2); ?></td><td>₱<?php echo number_format((float) ($paymentBooking['additional_charges'] ?? 0), 2); ?></td><td>₱<?php echo number_format((float) ($payment['amount'] ?? 0), 2); ?></td><td><?php echo e(ucwords(str_replace('_', ' ', $payment['payment_method'] ?? ''))); ?><br><?php echo e(!empty($payment['paid_at']) ? date('M j, Y', strtotime($payment['paid_at'])) : '—'); ?></td><td><span class="badge <?php echo e($paymentStatus); ?>"><?php echo e(ucfirst($paymentStatus)); ?></span></td></tr>
              <?php endforeach; ?><?php else: ?><tr><td class="empty-row" colspan="8">No payments recorded yet. Rental charges remain outstanding until a payment is recorded.</td></tr><?php endif; ?>
            </tbody>
          </table></div>
          <div class="panel-heading">Outstanding Rental Balances</div>
          <div class="table-wrap" data-admin-view="payments"><table>
            <thead><tr><th>Customer</th><th>Vehicle</th><th>Total Charge</th><th>Paid</th><th>Balance / Status</th><th>Record Payment</th></tr></thead>
            <tbody>
              <?php $outstandingBookings = array_values(array_filter($allBookings, static function ($booking): bool { return (float) ($booking['balance'] ?? $booking['total_amount'] ?? 0) > 0 && in_array($booking['status'] ?? '', ['confirmed', 'active', 'completed'], true); })); ?>
              <?php if ($outstandingBookings): ?><?php foreach ($outstandingBookings as $booking): ?>
                <tr><td><?php echo e($booking['full_name'] ?? 'Customer'); ?><?php if (!empty($booking['is_demo'])): ?><br><span class="badge">Demo</span><?php endif; ?></td><td><?php echo e($booking['vehicle_name'] ?? 'Vehicle'); ?></td><td>PHP <?php echo number_format((float) ($booking['total_amount'] ?? 0), 2); ?></td><td>PHP <?php echo number_format((float) ($booking['amount_paid'] ?? 0), 2); ?></td><td>PHP <?php echo number_format((float) ($booking['balance'] ?? 0), 2); ?><br><span class="badge <?php echo e($booking['payment_status'] ?? 'unpaid'); ?>"><?php echo e(ucfirst($booking['payment_status'] ?? 'unpaid')); ?></span></td><td><form class="inline-form" method="post"><input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>"><input type="hidden" name="action" value="booking_payment"><input type="hidden" name="booking_id" value="<?php echo (int) $booking['id']; ?>"><input name="amount" type="number" min="0.01" max="<?php echo e($booking['balance'] ?? $booking['total_amount']); ?>" step="0.01" value="<?php echo e($booking['balance'] ?? $booking['total_amount']); ?>" aria-label="Payment amount for booking <?php echo (int) $booking['id']; ?>" required><select name="payment_method" aria-label="Payment method for booking <?php echo (int) $booking['id']; ?>"><option value="cash">Cash</option><option value="bank_transfer">Bank transfer</option><option value="card">Card</option></select><button class="action primary" type="submit">Record</button></form></td></tr>
              <?php endforeach; ?><?php else: ?><tr><td class="empty-row" colspan="6">No outstanding rental balances.</td></tr><?php endif; ?>
            </tbody>
          </table></div>
        </section>

        <section class="panel" id="reviews" data-console-panel="reviews">
          <div class="panel-heading">Customer Reviews and Moderation</div>
          <div class="table-wrap" id="review-list" data-admin-view="reviews" tabindex="-1"><table>
            <thead><tr><th>Customer</th><th>Vehicle</th><th>Rating</th><th>Comment</th><th>Date</th><th>Status / Action</th></tr></thead>
            <tbody>
              <?php if ($allReviews): ?><?php foreach ($allReviews as $review): ?>
                <?php $reviewStatus = $review['status'] ?? 'approved'; ?>
                <tr><td><?php echo e($review['customer_name'] ?? 'Customer'); ?><?php if (!empty($review['is_demo'])): ?><br><span class="badge">Sample</span><?php endif; ?></td><td><?php echo e($review['vehicle_name'] ?? 'Vehicle'); ?></td><td><?php echo str_repeat('★', (int) $review['rating']) . str_repeat('☆', max(0, 5 - (int) $review['rating'])); ?> (<?php echo (int) $review['rating']; ?>/5)</td><td><?php echo e($review['review_text'] ?? $review['review'] ?? ''); ?></td><td><?php echo e($review['created_at'] ?? '—'); ?></td><td><span class="badge <?php echo e($reviewStatus); ?>"><?php echo e(ucfirst($reviewStatus)); ?></span><form class="inline-form" method="post"><input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>"><input type="hidden" name="action" value="review_moderation"><input type="hidden" name="review_id" value="<?php echo (int) $review['id']; ?>"><button class="action primary" name="status" value="approved" type="submit">Approve</button><button class="action" name="status" value="hidden" type="submit">Hide</button></form><form class="inline-form" method="post" onsubmit="return confirm('Permanently delete this review?');"><input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>"><input type="hidden" name="action" value="review_delete"><input type="hidden" name="review_id" value="<?php echo (int) $review['id']; ?>"><button class="action" type="submit">Delete</button></form></td></tr>
              <?php endforeach; ?><?php else: ?><tr><td class="empty-row" colspan="6">No customer reviews submitted yet.</td></tr><?php endif; ?>
            </tbody>
          </table></div>
        </section>

        <section class="panel" id="release-return" data-console-panel="release return">
          <div class="panel-heading">Vehicle Release and Return Records</div>
          <div class="table-wrap" id="release-list" data-admin-view="release return" tabindex="-1"><table>
            <thead><tr><th>Rental / Customer</th><th>Vehicle / Dates</th><th>Status</th><th>Release / Return Details</th></tr></thead>
            <tbody><?php if ($releaseRecords): ?><?php foreach ($releaseRecords as $record): ?><tr data-lifecycle-action="<?php echo $record['status'] === 'confirmed' ? 'release' : ($record['status'] === 'active' ? 'return' : 'completed'); ?>">
              <td>#<?php echo (int) $record['id']; ?> · <?php echo e($record['full_name']); ?><?php if (!empty($record['is_demo'])): ?><br><span class="badge">Sample</span><?php endif; ?></td><td><?php echo e($record['vehicle_name']); ?><br><?php echo e($record['pickup_date']); ?> – <?php echo e($record['return_date']); ?></td><td><span class="badge <?php echo $record['status'] === 'confirmed' ? 'approved' : e($record['status']); ?>"><?php echo $record['status'] === 'confirmed' ? 'Approved' : e(ucfirst($record['status'])); ?></span><br>₱<?php echo number_format((float) ($record['total_amount'] ?? 0), 2); ?></td>
              <td><?php if ($record['status'] === 'confirmed'): ?><form class="lifecycle-form" method="post" onsubmit="return confirm('Record this vehicle as released to the customer?');"><input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>"><input type="hidden" name="action" value="booking_lifecycle"><input type="hidden" name="booking_id" value="<?php echo (int) $record['id']; ?>"><input type="hidden" name="from_status" value="confirmed"><input type="hidden" name="to_status" value="active"><label>Release mileage <input name="mileage" type="number" min="0" value="<?php echo (int) ($record['mileage_km'] ?? 0); ?>" required></label><label>Fuel <select name="fuel_level" required><option value="">Select</option><?php foreach (['Full', '3/4', '1/2', '1/4', 'Empty'] as $fuelLevel): ?><option value="<?php echo e($fuelLevel); ?>"><?php echo e($fuelLevel); ?></option><?php endforeach; ?></select></label><label>Condition <select name="condition" required><option value="">Select</option><?php foreach (['Excellent', 'Good', 'Fair'] as $condition): ?><option value="<?php echo e($condition); ?>"><?php echo e($condition); ?></option><?php endforeach; ?></select></label><label>Notes <input name="notes" maxlength="500"></label><button class="action primary" type="submit">Record Release</button></form>
              <?php elseif ($record['status'] === 'active'): ?><form class="lifecycle-form" method="post" onsubmit="return confirm('Record this vehicle as returned and apply the additional charge?');"><input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>"><input type="hidden" name="action" value="booking_lifecycle"><input type="hidden" name="booking_id" value="<?php echo (int) $record['id']; ?>"><input type="hidden" name="from_status" value="active"><input type="hidden" name="to_status" value="completed"><label>Return mileage <input name="mileage" type="number" min="<?php echo (int) ($record['initial_mileage'] ?? 0); ?>" value="<?php echo (int) ($record['initial_mileage'] ?? 0); ?>" required></label><label>Fuel <select name="fuel_level" required><option value="">Select</option><?php foreach (['Full', '3/4', '1/2', '1/4', 'Empty'] as $fuelLevel): ?><option value="<?php echo e($fuelLevel); ?>"><?php echo e($fuelLevel); ?></option><?php endforeach; ?></select></label><label>Condition <select name="condition" required><option value="">Select</option><?php foreach (['Excellent', 'Good', 'Fair', 'Damaged'] as $condition): ?><option value="<?php echo e($condition); ?>"><?php echo e($condition); ?></option><?php endforeach; ?></select></label><label>Additional charges (PHP) <input name="additional_charges" type="number" min="0" step="0.01" value="0" required></label><label>Damage notes <input name="damage_notes" maxlength="1000"></label><button class="action primary" type="submit">Record Return</button></form>
              <?php else: ?>Returned <?php echo e($record['returned_at'] ?? ''); ?><?php if ((float) ($record['additional_charges'] ?? 0) > 0): ?><br>Additional charges: ₱<?php echo number_format((float) $record['additional_charges'], 2); ?><?php endif; ?><?php endif; ?></td>
            </tr><?php endforeach; ?><?php endif; ?><tr class="empty-row" data-lifecycle-empty hidden><td colspan="4"></td></tr></tbody>
          </table></div>
        </section>

        <section class="panel" id="maintenance" data-console-panel="maintenance-add maintenance-view">
          <div class="panel-heading">Maintenance</div>
          <?php if (!$vehicles): ?><p class="empty-row" data-admin-view="maintenance-add">Add a vehicle before scheduling maintenance.</p><?php endif; ?>
          <form class="admin-form" id="maintenance-form" data-admin-view="maintenance-add" data-record-selected="<?php echo $editMaintenance ? 'true' : 'false'; ?>" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>"><input type="hidden" name="action" value="<?php echo $editMaintenance ? 'maintenance_update' : 'maintenance_add'; ?>"><input type="hidden" name="maintenance_id" value="<?php echo (int) ($editMaintenance['id'] ?? 0); ?>">
            <label>Vehicle<select name="vehicle_id" required><option value="">Select vehicle</option><?php foreach ($vehicles as $vehicle): ?><option value="<?php echo (int) $vehicle['id']; ?>" <?php echo (int) ($editMaintenance['vehicle_id'] ?? 0) === (int) $vehicle['id'] ? 'selected' : ''; ?>><?php echo e($vehicle['name']); ?></option><?php endforeach; ?></select></label>
            <label>Maintenance type<input name="maintenance_type" maxlength="160" required value="<?php echo e($editMaintenance['maintenance_type'] ?? ''); ?>"></label>
            <label>Start date<input name="scheduled_date" type="date" required value="<?php echo e($editMaintenance['scheduled_date'] ?? ''); ?>"></label>
            <label>Expected completion<input name="expected_completion_date" type="date" value="<?php echo e($editMaintenance['expected_completion_date'] ?? ''); ?>"></label>
            <label>Actual completion<input name="completed_date" type="date" value="<?php echo e($editMaintenance['completed_date'] ?? ''); ?>"></label>
            <label>Cost (PHP)<input name="cost" type="number" min="0" step="0.01" value="<?php echo e($editMaintenance['cost'] ?? 0); ?>" required></label>
            <label>Status<select name="maintenance_status"><?php foreach (['scheduled', 'in_progress', 'completed', 'cancelled'] as $status): ?><option value="<?php echo e($status); ?>" <?php echo ($editMaintenance['status'] ?? 'scheduled') === $status ? 'selected' : ''; ?>><?php echo e(ucwords(str_replace('_', ' ', $status))); ?></option><?php endforeach; ?></select></label>
            <label class="full">Description<textarea name="description" maxlength="2000" placeholder="Maintenance notes"><?php echo e($editMaintenance['description'] ?? ''); ?></textarea></label>
            <div class="actions"><button class="action primary" type="submit"><?php echo $editMaintenance ? 'Update Maintenance' : 'Schedule Maintenance'; ?></button><?php if ($editMaintenance): ?><a class="action" href="admin_panel.php?section=maintenance-add">Cancel</a><?php endif; ?></div>
          </form>
          <div class="section-tools" data-admin-view="maintenance-view"><label>Search maintenance <input id="maintenance-search" type="search" placeholder="Vehicle or work type"></label><label>Status <select id="maintenance-status-filter"><option value="">All statuses</option><?php foreach (['scheduled', 'in_progress', 'completed'] as $status): ?><option value="<?php echo e($status); ?>"><?php echo e(ucwords(str_replace('_', ' ', $status))); ?></option><?php endforeach; ?></select></label></div>
          <div class="table-wrap" id="maintenance-list" data-admin-view="maintenance-view" tabindex="-1"><table>
            <thead><tr><th>Vehicle</th><th>Maintenance / Details</th><th>Start / Expected / Completed</th><th>Cost</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody><?php if ($maintenanceRecords): ?><?php foreach ($maintenanceRecords as $record): ?><tr data-maintenance-status="<?php echo e($record['status']); ?>"><td><?php echo e($record['vehicle_name']); ?><?php if (!empty($record['is_demo'])): ?><br><span class="badge">Sample</span><?php endif; ?></td><td><?php echo e($record['maintenance_type']); ?><br><?php echo e($record['description'] ?? ''); ?></td><td><?php echo e($record['scheduled_date']); ?><br><?php echo e($record['expected_completion_date'] ?? '—'); ?><br><?php echo e($record['completed_date'] ?? '—'); ?></td><td>₱<?php echo number_format((float) ($record['cost'] ?? 0), 2); ?></td><td><span class="badge <?php echo e($record['status']); ?>"><?php echo e(str_replace('_', ' ', $record['status'])); ?></span></td><td><div class="actions"><a class="action" href="admin_panel.php?section=maintenance-add&amp;edit_maintenance=<?php echo (int) $record['id']; ?>">Edit</a><form class="inline-form" method="post" onsubmit="return confirm('Delete this maintenance record?');"><input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>"><input type="hidden" name="action" value="maintenance_delete"><input type="hidden" name="maintenance_id" value="<?php echo (int) $record['id']; ?>"><button class="action" type="submit">Delete</button></form></div></td></tr><?php endforeach; ?><?php else: ?><tr><td class="empty-row" colspan="6">No maintenance records found.</td></tr><?php endif; ?></tbody>
          </table></div>
        </section>

        <section class="panel" id="reports" data-console-panel="report-rental report-vehicle report-customer">
          <div class="panel-heading">Reports</div>
          <div data-admin-view="report-rental">
            <div class="stats"><article class="stat"><span>RENTAL RECORDS</span><strong><?php echo count($allBookings); ?></strong></article><article class="stat"><span>ACTUAL CHARGES</span><strong>₱<?php echo number_format(array_sum(array_map(static function ($booking): float { return empty($booking['is_demo']) ? (float) ($booking['total_amount'] ?? 0) : 0.0; }, $allBookings)), 2); ?></strong></article><article class="stat"><span>SAMPLE RECORDS</span><strong><?php echo count(array_filter($allBookings, static function ($booking): bool { return !empty($booking['is_demo']); })); ?></strong></article></div>
            <div class="section-tools"><label>From <input id="rental-report-from" type="date"></label><label>To <input id="rental-report-to" type="date"></label></div>
            <div class="table-wrap"><table id="rental-report-table"><thead><tr><th>Request</th><th>Customer</th><th>Vehicle</th><th>Rental Dates / Days</th><th>Rental Charge</th><th>Additional Fees</th><th>Total / Status</th></tr></thead><tbody>
              <?php if ($allBookings): foreach (array_reverse($allBookings) as $booking): ?><tr data-report-date="<?php echo e(substr((string) ($booking['pickup_date'] ?? ''), 0, 10)); ?>"><td>#<?php echo (int) $booking['id']; ?><?php if (!empty($booking['is_demo'])): ?><br><span class="badge">Sample</span><?php endif; ?></td><td><?php echo e($booking['full_name'] ?? 'Customer'); ?></td><td><?php echo e($booking['vehicle_name'] ?? 'Vehicle'); ?></td><td><?php echo e($booking['pickup_date'] ?? '—'); ?> – <?php echo e($booking['return_date'] ?? '—'); ?><br><?php echo (int) ($booking['rental_days'] ?? $booking['days'] ?? 0); ?> days</td><td>₱<?php echo number_format((float) ($booking['daily_rate'] ?? 0) * (int) ($booking['days'] ?? 0), 2); ?></td><td>₱<?php echo number_format((float) ($booking['driver_fee'] ?? 0) + (float) ($booking['additional_charges'] ?? 0), 2); ?></td><td>₱<?php echo number_format((float) ($booking['total_amount'] ?? 0), 2); ?><br><span class="badge <?php echo e($booking['status'] ?? ''); ?>"><?php echo e(ucfirst($booking['status'] ?? 'Unknown')); ?></span></td></tr><?php endforeach; else: ?><tr><td colspan="7" class="empty-row">No rental activity is available for this report yet.</td></tr><?php endif; ?>
            </tbody></table></div>
          </div>
          <div data-admin-view="report-vehicle">
            <div class="stats"><article class="stat"><span>TOTAL FLEET</span><strong><?php echo count($vehicles); ?></strong></article><article class="stat"><span>AVAILABLE</span><strong><?php echo count(array_filter($vehicles, static function ($vehicle): bool { return ($vehicle['status'] ?? '') === 'available'; })); ?></strong></article><article class="stat"><span>CURRENTLY RENTED</span><strong><?php echo count(array_filter($vehicles, static function ($vehicle): bool { return ($vehicle['status'] ?? '') === 'rented'; })); ?></strong></article><article class="stat"><span>UNDER MAINTENANCE</span><strong><?php echo count(array_filter($vehicles, static function ($vehicle): bool { return ($vehicle['status'] ?? '') === 'maintenance'; })); ?></strong></article></div>
            <div class="table-wrap"><table><thead><tr><th>Vehicle</th><th>Category</th><th>Status</th><th>Rental Frequency</th><th>Daily Rate</th></tr></thead><tbody>
              <?php if ($vehicles): foreach ($vehicles as $vehicle): $frequency = count(array_filter($allBookings, static function ($booking) use ($vehicle): bool { return (int) ($booking['vehicle_id'] ?? 0) === (int) $vehicle['id']; })); ?><tr><td><?php echo e($vehicle['name']); ?><?php if (!empty($vehicle['is_demo'])): ?><br><span class="badge">Sample</span><?php endif; ?></td><td><?php echo e($vehicle['category']); ?></td><td><span class="badge <?php echo e($vehicle['status']); ?>"><?php echo e(ucfirst($vehicle['status'])); ?></span></td><td><?php echo $frequency; ?></td><td>₱<?php echo number_format((float) $vehicle['daily_rate'], 2); ?></td></tr><?php endforeach; else: ?><tr><td colspan="5" class="empty-row">No vehicles are available for this report.</td></tr><?php endif; ?>
            </tbody></table></div>
          </div>
          <div data-admin-view="report-customer">
            <div class="stats"><article class="stat"><span>CUSTOMER ACCOUNTS</span><strong><?php echo count($customers); ?></strong></article><article class="stat"><span>ACTUAL CUSTOMER SPEND</span><strong>₱<?php echo number_format(array_sum(array_map(static function ($booking): float { return empty($booking['is_demo']) && in_array($booking['status'] ?? '', ['confirmed', 'active', 'completed'], true) ? (float) ($booking['amount_paid'] ?? 0) : 0.0; }, $allBookings)), 2); ?></strong></article></div>
            <div class="section-tools"><label>Search customers <input id="customer-report-search" type="search" placeholder="Name or email"></label><label>Registered from <input id="customer-report-from" type="date"></label><label>Registered to <input id="customer-report-to" type="date"></label></div>
            <div class="table-wrap"><table id="customer-report-table"><thead><tr><th>Customer</th><th>Registration</th><th>Total / Completed Rentals</th><th>Actual Spend</th><th>Activity / Status</th></tr></thead><tbody>
              <?php if ($customers): foreach ($customers as $customer): $customerBookings = array_values(array_filter($allBookings, static function ($booking) use ($customer): bool { return (int) ($booking['customer_id'] ?? 0) === (int) $customer['id']; })); $completedCount = count(array_filter($customerBookings, static function ($booking): bool { return ($booking['status'] ?? '') === 'completed'; })); $customerSpend = array_sum(array_map(static function ($booking): float { return empty($booking['is_demo']) ? (float) ($booking['amount_paid'] ?? 0) : 0.0; }, $customerBookings)); ?><tr data-customer-date="<?php echo e(substr((string) ($customer['created_at'] ?? ''), 0, 10)); ?>"><td><?php echo e($customer['full_name'] ?? 'Customer'); ?> · <?php echo e($customer['email'] ?? ''); ?><?php if (!empty($customer['is_demo'])): ?><br><span class="badge">Sample</span><?php endif; ?></td><td><?php echo e($customer['created_at'] ?? '—'); ?></td><td><?php echo count($customerBookings); ?> / <?php echo $completedCount; ?></td><td>₱<?php echo number_format($customerSpend, 2); ?></td><td><span class="badge <?php echo e($customer['status'] ?? 'active'); ?>"><?php echo e(ucfirst($customer['status'] ?? 'active')); ?></span></td></tr><?php endforeach; else: ?><tr><td colspan="5" class="empty-row">No registered customers are available for this report.</td></tr><?php endif; ?>
            </tbody></table></div>
          </div>
        </section>
    </main>
  </div>

  <script>
    (function () {
      var content = document.querySelector('.content');
      var sidebarLinks = Array.prototype.slice.call(
        document.querySelectorAll('.side-nav a[data-console-view]')
      );
      var panels = Array.prototype.slice.call(content.querySelectorAll('[data-console-panel]'));
      var viewParts = Array.prototype.slice.call(content.querySelectorAll('[data-admin-view]'));
      var viewTitle = content.querySelector('#selected-view-title');
      var sidebar = document.querySelector('.sidebar');
      var releaseRows = Array.prototype.slice.call(
        content.querySelectorAll('#release-list tbody tr[data-lifecycle-action]')
      );
      var releaseEmptyRow = content.querySelector('[data-lifecycle-empty]');
      var reportRows = Array.prototype.slice.call(
        content.querySelectorAll('#reports-list tbody tr[data-report-kind]')
      );
      var reportEmptyRow = content.querySelector('[data-report-empty]');
      var requestRows = Array.prototype.slice.call(
        content.querySelectorAll('#request-list tbody tr[data-request-status]')
      );
      var requestEmptyRows = Array.prototype.slice.call(
        content.querySelectorAll('#request-list tbody tr[data-request-empty]')
      );
      var requestSearch = content.querySelector('#request-search');
      var requestStatusFilter = content.querySelector('#request-status-filter');
      var validViews = sidebarLinks.map(function (link) {
        return link.dataset.consoleView;
      });

      function includesView(element, viewId, attributeName) {
        return (element.getAttribute(attributeName) || '').split(/\s+/).indexOf(viewId) !== -1;
      }

      function filterRequests(viewId) {
        var query = viewId === 'requests-view' && requestSearch
          ? requestSearch.value.trim().toLowerCase()
          : '';
        var status = viewId === 'requests-view' && requestStatusFilter
          ? requestStatusFilter.value
          : 'all';
        requestRows.forEach(function (row) {
          var baseVisible = viewId === 'requests-approve'
            ? row.dataset.requestStatus === 'requested'
            : viewId === 'requests-view';
          var matchesStatus = status === 'all' || row.dataset.requestStatus === status;
          var matchesQuery = query === '' || row.textContent.toLowerCase().indexOf(query) !== -1;
          row.hidden = !baseVisible || !matchesStatus || !matchesQuery;
        });
        var visibleCount = requestRows.filter(function (row) { return !row.hidden; }).length;
        requestEmptyRows.forEach(function (row) {
          var pendingOnly = row.dataset.requestEmpty === 'pending';
          row.hidden = visibleCount > 0
            || (viewId !== 'requests-view' && viewId !== 'requests-approve')
            || (viewId === 'requests-approve') !== pendingOnly;
          if (!row.hidden && viewId === 'requests-view' && status !== 'all') {
            row.firstElementChild.textContent = 'No requests match the selected search and status filters.';
          }
        });
      }

      function activate(viewId) {
        if (validViews.indexOf(viewId) === -1) {
          viewId = 'dashboard';
        }
        var selectedLink = sidebarLinks.find(function (link) {
          return link.dataset.consoleView === viewId;
        });
        if (!selectedLink) {
          return;
        }
        panels.forEach(function (item) {
          item.hidden = !includesView(item, viewId, 'data-console-panel');
        });
        viewParts.forEach(function (item) {
          var visible = includesView(item, viewId, 'data-admin-view');
          if (visible && item.dataset.recordSelected === 'false'
            && ['vehicle-edit', 'category-edit', 'maintenance-add'].indexOf(viewId) !== -1) {
            visible = viewId === 'vehicle-edit' || viewId === 'category-edit'
              ? item.id !== 'vehicle-form' && item.id !== 'category-form'
              : true;
          }
          if (visible && item.dataset.recordSelected === 'true'
            && ['vehicle-edit', 'category-edit'].indexOf(viewId) !== -1
            && ['vehicle-list', 'category-list'].indexOf(item.id) !== -1) {
            visible = false;
          }
          item.hidden = !visible;
        });

        if (viewId === 'vehicle-edit' || viewId === 'category-edit') {
          var editForm = content.querySelector(viewId === 'vehicle-edit' ? '#vehicle-form' : '#category-form');
          var editList = content.querySelector(viewId === 'vehicle-edit' ? '#vehicle-list' : '#category-list');
          if (editForm && editList) {
            var recordSelected = editForm.dataset.recordSelected === 'true';
            editForm.hidden = !recordSelected;
            editList.hidden = recordSelected;
          }
        }

        content.querySelectorAll('#vehicle-list [data-vehicle-actions]').forEach(function (cell) {
          cell.hidden = viewId === 'vehicle-view';
        });
        var emptyVehicleRow = content.querySelector('#vehicle-table tbody .empty-row');
        if (emptyVehicleRow) {
          emptyVehicleRow.colSpan = viewId === 'vehicle-view' ? 6 : 7;
        }
        content.querySelectorAll('#vehicle-list [data-console-action]').forEach(function (item) {
          var action = item.dataset.consoleAction;
          item.hidden = action === 'edit'
            ? viewId !== 'vehicle-edit'
            : viewId !== 'vehicle-delete';
        });
        content.querySelectorAll('#customer-list [data-customer-actions]').forEach(function (cell) {
          cell.hidden = viewId === 'customer-view';
          var details = cell.querySelector('details');
          if (details && viewId === 'customer-disable') {
            details.open = true;
          }
          cell.querySelectorAll('[data-customer-action]').forEach(function (form) {
            form.hidden = viewId === 'customer-edit'
              ? form.dataset.customerAction !== 'edit'
              : viewId === 'customer-disable'
                ? form.dataset.customerAction !== 'disable'
                : true;
          });
        });
        var requestList = content.querySelector('#request-list');
        if (requestList) {
          filterRequests(viewId);
          requestList.querySelectorAll('tr').forEach(function (row) {
            var lastCell = row.lastElementChild;
            if (row.dataset.requestStatus && lastCell) {
              lastCell.hidden = viewId === 'requests-view';
            }
          });
          requestList.querySelectorAll('thead th:last-child').forEach(function (cell) {
            cell.hidden = viewId === 'requests-view';
          });
        }
        releaseRows.forEach(function (row) {
          row.hidden = row.dataset.lifecycleAction !== viewId;
        });
        if (releaseEmptyRow) {
          var hasLifecycleRecords = releaseRows.some(function (row) { return !row.hidden; });
          releaseEmptyRow.hidden = !['release', 'return'].includes(viewId) || hasLifecycleRecords;
          if (!hasLifecycleRecords && releaseEmptyRow.firstElementChild) {
            releaseEmptyRow.firstElementChild.textContent = viewId === 'release'
              ? 'No approved rentals are awaiting vehicle release.'
              : 'No active rentals are awaiting vehicle return.';
          }
        }
        reportRows.forEach(function (row) {
          row.hidden = row.dataset.reportKind !== viewId.replace('report-', '');
        });
        if (reportEmptyRow) {
          var reportKind = viewId.replace('report-', '');
          var hasReport = reportRows.some(function (row) { return !row.hidden; });
          reportEmptyRow.hidden = viewId.indexOf('report-') !== 0 || hasReport;
          if (!hasReport && reportEmptyRow.firstElementChild) {
            reportEmptyRow.firstElementChild.textContent = reportKind === 'rental'
              ? 'No rental activity is available for this report yet.'
              : 'No vehicles are available for this report yet.';
          }
        }
        sidebarLinks.forEach(function (link) {
          var isActive = link.dataset.consoleView === viewId;
          link.classList.toggle('active', isActive);
          if (isActive) {
            link.setAttribute('aria-current', 'page');
          } else {
            link.removeAttribute('aria-current');
          }
        });
        viewTitle.textContent = selectedLink.textContent.trim();
        document.body.classList.add('admin-app-ready');
      }

      var searchInput = content.querySelector('#fleet-search');
      var statusFilter = content.querySelector('#fleet-status');
      var categoryFilter = content.querySelector('#fleet-category');
      var sortFilter = content.querySelector('#fleet-sort');
      var vehicleRows = Array.prototype.slice.call(content.querySelectorAll('#vehicle-table tbody tr'));
      var vehicleTbody = content.querySelector('#vehicle-table tbody');
      function filterFleet() {
        var query = searchInput ? searchInput.value.trim().toLowerCase() : '';
        var status = statusFilter ? statusFilter.value.toLowerCase() : '';
        var category = categoryFilter ? categoryFilter.value.toLowerCase() : '';
        vehicleRows.forEach(function (row) {
          var rowStatus = row.cells.length > 5 ? row.cells[5].textContent.trim().toLowerCase() : '';
          row.hidden = (query !== '' && row.textContent.toLowerCase().indexOf(query) === -1)
            || (status !== '' && rowStatus !== status)
            || (category !== '' && row.dataset.vehicleCategory !== category);
        });
        if (vehicleTbody && sortFilter && sortFilter.value) {
          var sortedRows = vehicleRows.slice().sort(function (left, right) {
            if (sortFilter.value === 'rate-low' || sortFilter.value === 'rate-high') {
              var difference = parseFloat(left.dataset.vehicleRate) - parseFloat(right.dataset.vehicleRate);
              return sortFilter.value === 'rate-low' ? difference : -difference;
            }
            return (left.dataset.vehicleName || '').localeCompare(right.dataset.vehicleName || '');
          });
          sortedRows.forEach(function (row) { vehicleTbody.appendChild(row); });
        }
      }
      if (searchInput) { searchInput.addEventListener('input', filterFleet); }
      if (statusFilter) { statusFilter.addEventListener('change', filterFleet); }
      if (categoryFilter) { categoryFilter.addEventListener('change', filterFleet); }
      if (sortFilter) { sortFilter.addEventListener('change', filterFleet); }
      if (requestSearch) { requestSearch.addEventListener('input', function () { filterRequests('requests-view'); }); }
      if (requestStatusFilter) { requestStatusFilter.addEventListener('change', function () { filterRequests('requests-view'); }); }
      var customerSearch = content.querySelector('#customer-search');
      var customerStatusFilter = content.querySelector('#customer-status-filter');
      function filterCustomers() {
        var query = customerSearch ? customerSearch.value.trim().toLowerCase() : '';
        var status = customerStatusFilter ? customerStatusFilter.value : '';
        content.querySelectorAll('#customer-list tbody tr[data-customer-status]').forEach(function (row) {
          row.hidden = (query !== '' && row.textContent.toLowerCase().indexOf(query) === -1)
            || (status !== '' && row.dataset.customerStatus !== status);
        });
      }
      if (customerSearch) { customerSearch.addEventListener('input', filterCustomers); }
      if (customerStatusFilter) { customerStatusFilter.addEventListener('change', filterCustomers); }
      var paymentStatusFilter = content.querySelector('#payment-status-filter');
      if (paymentStatusFilter) {
        paymentStatusFilter.addEventListener('change', function () {
          content.querySelectorAll('#payment-list tbody tr[data-payment-status]').forEach(function (row) {
            row.hidden = paymentStatusFilter.value !== '' && row.dataset.paymentStatus !== paymentStatusFilter.value;
          });
        });
      }
      var maintenanceSearch = content.querySelector('#maintenance-search');
      var maintenanceStatusFilter = content.querySelector('#maintenance-status-filter');
      function filterMaintenance() {
        var query = maintenanceSearch ? maintenanceSearch.value.trim().toLowerCase() : '';
        var status = maintenanceStatusFilter ? maintenanceStatusFilter.value : '';
        content.querySelectorAll('#maintenance-list tbody tr[data-maintenance-status]').forEach(function (row) {
          row.hidden = (query !== '' && row.textContent.toLowerCase().indexOf(query) === -1)
            || (status !== '' && row.dataset.maintenanceStatus !== status);
        });
      }
      if (maintenanceSearch) { maintenanceSearch.addEventListener('input', filterMaintenance); }
      if (maintenanceStatusFilter) { maintenanceStatusFilter.addEventListener('change', filterMaintenance); }
      var reportFrom = content.querySelector('#rental-report-from');
      var reportTo = content.querySelector('#rental-report-to');
      function filterRentalReport() {
        var from = reportFrom ? reportFrom.value : '';
        var to = reportTo ? reportTo.value : '';
        content.querySelectorAll('#rental-report-table tbody tr[data-report-date]').forEach(function (row) {
          row.hidden = (from !== '' && row.dataset.reportDate < from)
            || (to !== '' && row.dataset.reportDate > to);
        });
      }
      if (reportFrom) { reportFrom.addEventListener('change', filterRentalReport); }
      if (reportTo) { reportTo.addEventListener('change', filterRentalReport); }
      var customerReportSearch = content.querySelector('#customer-report-search');
      var customerReportFrom = content.querySelector('#customer-report-from');
      var customerReportTo = content.querySelector('#customer-report-to');
      function filterCustomerReport() {
        var query = customerReportSearch ? customerReportSearch.value.trim().toLowerCase() : '';
        var from = customerReportFrom ? customerReportFrom.value : '';
        var to = customerReportTo ? customerReportTo.value : '';
        content.querySelectorAll('#customer-report-table tbody tr[data-customer-date]').forEach(function (row) {
          row.hidden = (query !== '' && row.textContent.toLowerCase().indexOf(query) === -1)
            || (from !== '' && row.dataset.customerDate < from)
            || (to !== '' && row.dataset.customerDate > to);
        });
      }
      if (customerReportSearch) { customerReportSearch.addEventListener('input', filterCustomerReport); }
      if (customerReportFrom) { customerReportFrom.addEventListener('change', filterCustomerReport); }
      if (customerReportTo) { customerReportTo.addEventListener('change', filterCustomerReport); }

      var initialView = new URLSearchParams(window.location.search).get('section') || 'dashboard';
      if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
      }
      var savedScroll = sessionStorage.getItem('admin-panel-scroll');
      var scrollState = null;
      if (savedScroll) {
        sessionStorage.removeItem('admin-panel-scroll');
        try {
          scrollState = JSON.parse(savedScroll);
          if (typeof scrollState.timestamp !== 'number'
            || Date.now() - scrollState.timestamp >= 30000) {
            scrollState = null;
          }
        } catch (error) {
          console.error('Unable to read saved admin panel position.', error);
          scrollState = null;
        }
      }
      activate(initialView);

      function saveScrollPosition() {
        var section = new URLSearchParams(window.location.search).get('section') || 'dashboard';
        sessionStorage.setItem('admin-panel-scroll', JSON.stringify({
          y: window.scrollY,
          section: section,
          timestamp: Date.now()
        }));
      }

      function restoreWindowScroll(scrollY) {
        window.requestAnimationFrame(function () {
          window.requestAnimationFrame(function () {
            window.scrollTo({ left: 0, top: scrollY, behavior: 'instant' });
          });
        });
      }

      document.addEventListener('click', function (event) {
        var target = event.target instanceof Element ? event.target : null;
        var link = target ? target.closest('a') : null;
        if (!link || event.defaultPrevented || event.button !== 0
          || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
          return;
        }
        if (!link.matches('a[data-console-view]')) {
          if (link.href.indexOf(new URL('admin_panel.php', window.location.href).href) === 0) {
            saveScrollPosition();
          }
          return;
        }
        event.preventDefault();
        var currentScrollY = window.scrollY;
        var viewId = link.dataset.consoleView;
        var nextUrl = new URL(window.location.href);
        var hadEditSelection = ['edit_vehicle', 'edit_category', 'edit_maintenance']
          .some(function (key) { return nextUrl.searchParams.has(key); });
        nextUrl.searchParams.delete('edit_vehicle');
        nextUrl.searchParams.delete('edit_category');
        nextUrl.searchParams.delete('edit_maintenance');
        nextUrl.searchParams.set('section', viewId);
        nextUrl.hash = '';
        if (hadEditSelection && viewId !== initialView) {
          saveScrollPosition();
          window.location.assign(nextUrl.href);
          return;
        }
        if (nextUrl.href !== window.location.href) {
          history.replaceState(Object.assign({}, history.state, { scrollY: currentScrollY }), '', window.location.href);
          history.pushState({ adminSection: viewId, scrollY: currentScrollY }, '', nextUrl.href);
        }
        initialView = viewId;
        activate(viewId);
        restoreWindowScroll(currentScrollY);
      });

      window.addEventListener('popstate', function (event) {
        var viewId = new URLSearchParams(window.location.search).get('section') || 'dashboard';
        initialView = viewId;
        activate(viewId);
        if (event.state && typeof event.state.scrollY === 'number') {
          restoreWindowScroll(event.state.scrollY);
        }
      });
      document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!(form instanceof HTMLFormElement) || form.method.toLowerCase() !== 'post') {
          return;
        }
        var action = form.querySelector('input[name="action"]');
        if (action && action.value === 'admin_logout') {
          return;
        }
        saveScrollPosition();
      });

      if (scrollState && typeof scrollState.y === 'number') {
        restoreWindowScroll(scrollState.y);
      }
    }());
  </script>
</body>
</html>
