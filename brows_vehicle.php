<?php
require_once __DIR__ . '/site_init.php';

if (!isset($vehicles) || !is_array($vehicles)) {
    $vehicles = [
        [
            'slug' => 'toyota-vios',
            'brand' => 'Toyota',
            'name' => 'Toyota Vios',
            'category' => 'Car',
            'daily_rate' => 2600,
            'seats' => 4,
            'fuel_type' => 'Gas',
            'transmission' => 'Automatic',
        ],
        [
            'slug' => 'honda-city',
            'brand' => 'Honda',
            'name' => 'Honda City',
            'category' => 'Car',
            'daily_rate' => 3300,
            'seats' => 4,
            'fuel_type' => 'Gas',
            'transmission' => 'Automatic',
        ],
        [
            'slug' => 'bmw-x5',
            'brand' => 'BMW',
            'name' => 'BMW X5',
            'category' => 'SUV',
            'daily_rate' => 7200,
            'seats' => 5,
            'fuel_type' => 'Gas',
            'transmission' => 'Automatic',
        ],
        [
            'slug' => 'ford-escape',
            'brand' => 'Ford',
            'name' => 'Ford Escape',
            'category' => 'SUV',
            'daily_rate' => 6100,
            'seats' => 5,
            'fuel_type' => 'Gas',
            'transmission' => 'Automatic',
        ],
        [
            'slug' => 'honda-wave-alpha',
            'brand' => 'Honda',
            'name' => 'Honda Wave Alpha',
            'category' => 'Motorcycle',
            'daily_rate' => 1200,
            'seats' => 2,
            'fuel_type' => 'Gas',
            'transmission' => 'Manual',
        ],
        [
            'slug' => 'mitsubishi-montero',
            'brand' => 'Mitsubishi',
            'name' => 'Mitsubishi Montero',
            'category' => 'SUV',
            'daily_rate' => 8200,
            'seats' => 7,
            'fuel_type' => 'Diesel',
            'transmission' => 'Automatic',
        ],
    ];
}

$feedbackMessage = $_SESSION['feedback_message'] ?? '';
$feedbackError = $_SESSION['feedback_error'] ?? '';
$feedbackVehicle = $_SESSION['feedback_vehicle'] ?? '';
$feedbackRating = (string) ($_SESSION['feedback_rating'] ?? '5');
$feedbackText = $_SESSION['feedback_text'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Browse and filter verified Autoride vehicles available for rent.">
  <title>Browse Vehicles | Autoride</title>
  <style>
    :root {
      --ink: #111827;
      --text: #374151;
      --muted: #6b7280;
      --line: #d1d5db;
      --soft: #e5e7eb;
      --page: #fff;
      --footer: #f3f4f6;
      --placeholder: #f1f2f4;
    }

    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body {
      zoom: 1.2;
      min-height: 100vh;
      margin: 0;
      display: flex;
      flex-direction: column;
      color: var(--text);
      background: var(--page);
      font-family: Arial, Helvetica, sans-serif;
      font-size: 11px;
      line-height: 1.4;
    }
    a { color: inherit; text-decoration: none; }
    :focus-visible { outline: 2px solid var(--ink); outline-offset: 2px; }

    .site-header {
      height: 36px;
      flex: 0 0 36px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 5.2%;
      border-bottom: 1px solid var(--soft);
      background: #fff;
    }
    .brand { color: var(--ink); font-size: 11px; font-weight: 800; letter-spacing: .15px; }
    .nav { display: flex; align-items: center; gap: 16px; }
    .nav a { color: var(--text); font-size: 7px; }
    .nav a:hover, .nav a.active { color: var(--ink); }
    .nav a.active { font-weight: 700; text-decoration: underline; text-underline-offset: 2px; }
    .search-mark {
      width: 7px;
      height: 7px;
      margin-left: 3px;
      position: relative;
      border: 1px solid var(--ink);
      border-radius: 50%;
    }
    .search-mark::after {
      content: "";
      position: absolute;
      width: 3px;
      height: 1px;
      right: -3px;
      bottom: -1px;
      background: var(--ink);
      transform: rotate(45deg);
    }

    .browse-layout {
      width: 100%;
      flex: 1 0 auto;
      display: grid;
      grid-template-columns: 137px minmax(0, 1fr);
      align-content: start;
      gap: 19px;
      padding: 19px 16px;
    }
    .filters {
      align-self: start;
      padding: 12px 11px 10px;
      border: 1px solid #e9ebef;
      border-radius: 4px;
      background: #fafbfc;
    }
    .filters h1 {
      margin: 0 0 10px;
      padding-bottom: 8px;
      border-bottom: 1px solid var(--line);
      color: var(--ink);
      font-size: 8px;
      font-weight: 800;
    }
    .filter-group { margin: 0 0 10px; }
    .filter-group:last-child { margin-bottom: 0; }
    .filter-group h2 {
      margin: 0 0 4px;
      color: var(--ink);
      font-size: 6px;
      font-weight: 800;
    }
    .filter-option {
      display: flex;
      align-items: center;
      gap: 5px;
      min-height: 13px;
      color: var(--text);
      font-size: 7px;
      cursor: pointer;
    }
    .filter-option input {
      width: 8px;
      height: 8px;
      margin: 0;
      accent-color: var(--ink);
    }

    .results { min-width: 0; }
    .results-toolbar {
      min-height: 20px;
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 10px;
      color: var(--muted);
      font-size: 7px;
    }
    .results-count strong { color: var(--ink); }
    .sort-control { display: flex; align-items: center; gap: 5px; white-space: nowrap; }
    .sort-control label { font-size: 7px; }
    .sort-control select {
      height: 14px;
      padding: 0 4px;
      border: 1px solid var(--line);
      border-radius: 2px;
      background: #fff;
      color: var(--ink);
      font-size: 7px;
    }
    .vehicle-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 14px 10px;
    }
    .vehicle-card {
      min-width: 0;
      overflow: hidden;
      border: 1px solid #e8eaee;
      border-radius: 4px;
      background: #fff;
    }
    .vehicle-image {
      height: 77px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-bottom: 1px solid var(--line);
      background: var(--placeholder);
    }
    .image-label {
      max-width: calc(100% - 12px);
      overflow: hidden;
      padding: 3px 5px;
      border: 1px solid var(--line);
      border-radius: 1px;
      background: #fff;
      color: var(--text);
      font-size: 5px;
      text-align: center;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    .vehicle-info { padding: 7px 8px 6px; }
    .vehicle-brand { color: var(--muted); font-size: 6px; text-transform: uppercase; }
    .vehicle-name {
      overflow: hidden;
      margin: 1px 0 4px;
      color: var(--ink);
      font-size: 8px;
      font-weight: 700;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    .vehicle-meta { color: var(--text); font-size: 6px; white-space: nowrap; }
    .vehicle-bottom {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 4px;
      margin-top: 7px;
    }
    .vehicle-price { color: var(--ink); font-size: 8px; font-weight: 800; white-space: nowrap; }
    .vehicle-price small { display: block; color: var(--muted); font-size: 5px; font-weight: 400; }
    .details-button {
      padding: 5px 7px;
      border: 1px solid var(--line);
      border-radius: 2px;
      color: var(--ink);
      background: #fff;
      font-size: 6px;
      font-weight: 700;
      white-space: nowrap;
    }
    .details-button:hover { border-color: var(--ink); }
    .empty-state { display: none; padding: 32px 12px; color: var(--muted); text-align: center; }
    .empty-state.show { display: block; }
    .browse-feedback { width: min(760px, calc(100% - 32px)); margin: 8px auto 28px; padding: 20px; border: 1px solid var(--soft); border-radius: 4px; background: #fafbfc; }
    .browse-feedback h2 { margin: 0 0 5px; color: var(--ink); font-size: 15px; }
    .browse-feedback > p { margin: 0 0 14px; color: var(--muted); font-size: 10px; }
    .feedback-form { display: grid; grid-template-columns: 1fr 150px; gap: 10px; }
    .feedback-form label { display: grid; gap: 5px; color: var(--ink); font-size: 10px; font-weight: 700; }
    .feedback-form label:nth-of-type(3) { grid-column: 1 / -1; }
    .feedback-form select, .feedback-form textarea { width: 100%; padding: 8px; border: 1px solid var(--line); border-radius: 3px; background: #fff; color: var(--text); font: inherit; }
    .feedback-form textarea { min-height: 90px; resize: vertical; }
    .feedback-form button { justify-self: start; min-height: 34px; padding: 0 14px; border: 1px solid var(--ink); border-radius: 3px; background: var(--ink); color: #fff; font: inherit; font-weight: 700; cursor: pointer; }
    .browse-feedback > .feedback-message { grid-column: 1 / -1; margin: 0; padding: 8px 10px; border: 1px solid #b7dfc9; background: #e8f6ee; color: #065f46; }
    .browse-feedback > .feedback-message.error { border-color: #efb4b4; background: #fff5f5; color: #8b1d1d; }
    .feedback-login { margin: 0; }
    .feedback-login a { color: var(--ink); text-decoration: underline; }

    .site-footer {
      flex: 0 0 auto;
      padding: 28px 5.2% 9px;
      background: var(--footer);
      border-top: 1px solid #eceef1;
    }
    .footer-top { display: grid; grid-template-columns: 1.6fr 1fr 1fr 1fr; gap: 18px; }
    .footer-brand { display: inline-block; margin-bottom: 7px; color: var(--ink); font-size: 9px; font-weight: 800; }
    .site-footer h2 { margin: 0 0 7px; color: var(--ink); font-size: 7px; font-weight: 800; }
    .site-footer p, .site-footer li { color: var(--text); font-size: 6px; }
    .site-footer p { max-width: 145px; margin: 0; }
    .site-footer ul { margin: 0; padding: 0; list-style: none; }
    .site-footer li { margin-bottom: 4px; }
    .site-footer a:hover { text-decoration: underline; }
    .footer-bottom {
      display: flex;
      justify-content: space-between;
      gap: 10px;
      margin-top: 14px;
      padding-top: 7px;
      border-top: 1px solid #cbd0d8;
      color: var(--muted);
      font-size: 5.5px;
    }

    @media (min-width: 1000px) {
      .site-header { height: 52px; flex-basis: 52px; }
      .brand { font-size: 15px; }
      .nav { gap: 25px; }
      .nav a { font-size: 10px; }
      .search-mark { width: 10px; height: 10px; }
      .browse-layout { max-width: 1500px; grid-template-columns: 220px minmax(0, 1fr); gap: 28px; margin: 0 auto; padding: 30px 28px; }
      .filters { padding: 18px 16px; }
      .filters h1 { font-size: 12px; }
      .filter-group h2 { font-size: 9px; }
      .filter-option { min-height: 20px; font-size: 10px; }
      .filter-option input { width: 12px; height: 12px; }
      .results-toolbar { min-height: 27px; font-size: 10px; }
      .sort-control label, .sort-control select { font-size: 10px; }
      .sort-control select { height: 22px; }
      .vehicle-grid { gap: 22px 18px; }
      .vehicle-image { height: 135px; }
      .image-label { font-size: 8px; padding: 5px 8px; }
      .vehicle-info { padding: 12px; }
      .vehicle-brand { font-size: 9px; }
      .vehicle-name { font-size: 13px; }
      .vehicle-meta { font-size: 9px; }
      .vehicle-bottom { margin-top: 10px; }
      .vehicle-price { font-size: 12px; }
      .vehicle-price small { font-size: 8px; }
      .details-button { padding: 7px 10px; font-size: 9px; }
      .site-footer { padding-top: 36px; }
      .footer-brand { font-size: 12px; }
      .site-footer h2 { font-size: 9px; }
      .site-footer p, .site-footer li { font-size: 9px; }
      .footer-bottom { margin-top: 22px; font-size: 8px; }
      .browse-feedback { padding: 26px; }
      .browse-feedback h2 { font-size: 18px; }
      .browse-feedback > p { font-size: 12px; }
      .feedback-form label { font-size: 12px; }
    }

    @media (max-width: 520px) {
      .site-header { height: auto; min-height: 38px; padding: 8px 12px; gap: 8px; }
      .brand { font-size: 9px; }
      .nav { gap: 7px; }
      .nav a { font-size: 6px; }
      .browse-layout { grid-template-columns: 1fr; gap: 14px; padding: 12px; }
      .filters { padding: 10px; }
      .filter-groups { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 5px 10px; }
      .filter-group { margin-bottom: 5px; }
      .results-toolbar { font-size: 7px; }
      .vehicle-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
      .vehicle-image { height: 82px; }
      .vehicle-bottom { gap: 2px; }
      .details-button { padding: 4px; font-size: 5px; }
      .footer-top { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 15px; }
      .footer-bottom { font-size: 5px; }
      .browse-feedback { margin-top: 0; padding: 14px; }
      .feedback-form { grid-template-columns: 1fr; }
      .feedback-form label:nth-of-type(3) { grid-column: auto; }
    }

    @media (min-width: 521px) and (max-width: 700px) {
      .site-footer { min-height: 176px; }
    }

    @media (max-width: 350px) {
      .nav { gap: 5px; }
      .nav a { font-size: 5px; }
      .vehicle-grid { grid-template-columns: 1fr; }
      .vehicle-image { height: 105px; }
      .filter-groups { grid-template-columns: 1fr 1fr; }
    }

    .site-header { height: auto; min-height: 52px; flex-basis: auto; padding: 8px max(24px, calc((100% - 1140px) / 2)); gap: 18px; }
    .brand { font-size: 14px; letter-spacing: .2px; }
    .nav { gap: 24px; }
    .nav a { color: var(--text); font-size: 11px; white-space: nowrap; }
    .nav-logout { padding: 0; border: 0; background: none; color: var(--text); font: inherit; font-size: 11px; cursor: pointer; }
    .nav-logout:hover { color: var(--ink); text-decoration: underline; text-underline-offset: 4px; }
    .nav a:hover, .nav a.active, .nav a[aria-current="page"] { color: var(--ink); text-decoration: underline; text-underline-offset: 4px; }
    .nav a.active { font-weight: 700; }
    .search-mark { width: 11px; height: 11px; margin-left: 3px; }
    .search-mark::after { width: 5px; height: 1.5px; right: -4px; bottom: -2px; }
    @media (max-width: 760px) {
      .site-header { padding: 8px 16px; flex-wrap: wrap; }
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
  </style>
  <style>
    body.browse-page {
      background: #f5f5f6 !important;
      color: #111214 !important;
    }
    body.browse-page .site-header {
      background: #f5f5f6 !important;
    }
    .browse-intro {
      width: min(1280px, calc(100% - 64px));
      margin: 0 auto;
      padding: 28px 0 22px;
    }
    .browse-eyebrow {
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
    .browse-eyebrow::before {
      width: 22px;
      height: 2px;
      background: #f3c928;
      content: "";
    }
    .browse-intro h1 {
      margin: 0;
      color: #111214;
      font-size: clamp(28px, 3vw, 40px);
      font-weight: 900;
      line-height: 1.1;
      letter-spacing: -1px;
      text-transform: uppercase;
    }
    .browse-intro p {
      margin: 8px 0 0;
      color: #72747a;
      font-size: 12px;
    }
    body.browse-page .browse-layout {
      width: min(1280px, calc(100% - 64px));
      max-width: none;
      grid-template-columns: 220px minmax(0, 1fr);
      align-content: start;
      gap: 22px;
      margin: 0 auto;
      padding: 0 0 36px;
    }
    body.browse-page .filters {
      position: sticky;
      top: 90px;
      padding: 18px 16px 14px;
      border: 1px solid #e3e3e7;
      border-radius: 14px !important;
      background: #f8f8f9 !important;
      box-shadow: none;
    }
    body.browse-page .filters h1 {
      margin: 0 0 16px;
      padding-bottom: 12px;
      border-bottom: 1px solid #e5e5e8;
      color: #111214;
      font-size: 14px;
      font-weight: 800;
      text-transform: none;
    }
    body.browse-page .filter-group {
      margin: 0 0 14px;
      padding-bottom: 12px;
      border-bottom: 1px solid #e5e5e8;
    }
    body.browse-page .filter-group:last-child {
      margin-bottom: 0;
      padding-bottom: 0;
      border-bottom: 0;
    }
    body.browse-page .filter-group h2 {
      margin: 0 0 8px;
      color: #111214;
      font-size: 9px;
      font-weight: 900;
      letter-spacing: .7px;
      text-transform: uppercase;
    }
    body.browse-page .filter-option {
      min-height: 22px;
      gap: 7px;
      color: #5f6065;
      font-size: 11px;
    }
    body.browse-page .filter-option input {
      width: 13px;
      height: 13px;
      accent-color: #111214;
    }
    .price-range-fields {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
    }
    .price-range-fields label {
      display: block;
      min-width: 0;
    }
    .price-range-fields input {
      width: 100%;
      height: 34px;
      padding: 0 9px;
      border: 1px solid #e1e2e6;
      border-radius: 8px;
      background: #fff !important;
      color: #111214;
      font-size: 11px;
    }
    .clear-filters {
      width: 100%;
      min-height: 38px;
      margin-top: 4px;
      border: 1px solid #111214 !important;
      border-radius: 99px !important;
      background: #111214 !important;
      color: #fff !important;
      font-size: 10px;
      font-weight: 900;
      letter-spacing: .7px;
      text-transform: uppercase;
    }
    .clear-filters:hover {
      background: #343539 !important;
      color: #fff !important;
    }
    body.browse-page .results-toolbar {
      min-height: 34px;
      align-items: center;
      margin-bottom: 12px;
      color: #72747a;
      font-size: 11px;
    }
    body.browse-page .results-count strong {
      color: #72747a;
      font-weight: 400;
    }
    body.browse-page .sort-control {
      gap: 0;
    }
    body.browse-page .sort-control label {
      position: absolute;
      width: 1px;
      height: 1px;
      overflow: hidden;
      clip: rect(0, 0, 0, 0);
    }
    body.browse-page .sort-control select {
      height: 34px;
      padding: 0 12px;
      border: 1px solid #e1e2e6;
      border-radius: 99px;
      background: #fff;
      color: #111214;
      font-size: 11px;
    }
    body.browse-page .vehicle-grid {
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 18px;
    }
    body.browse-page .vehicle-card {
      position: relative;
      border: 1px solid #e5e5e9;
      border-radius: 15px !important;
      background: #fff !important;
      box-shadow: none;
    }
    body.browse-page .vehicle-image {
      height: 150px;
      justify-content: flex-start;
      align-items: flex-start;
      padding: 12px;
      border: 0;
      background: linear-gradient(180deg, #ececef 0%, #ececef 52%, #a5a5a8 100%) !important;
    }
    body.browse-page .image-label {
      display: none;
    }
    .vehicle-status {
      padding: 6px 10px;
      border-radius: 99px;
      background: #f3c928;
      color: #111214;
      font-size: 9px;
      font-weight: 900;
      letter-spacing: .6px;
      text-transform: uppercase;
    }
    body.browse-page .vehicle-info {
      padding: 14px;
    }
    body.browse-page .vehicle-brand {
      color: #72747a;
      font-size: 9px;
      letter-spacing: .3px;
    }
    body.browse-page .vehicle-name {
      margin: 5px 0 10px;
      color: #111214;
      font-size: 16px;
      font-weight: 800;
    }
    body.browse-page .vehicle-meta {
      display: none;
    }
    body.browse-page .vehicle-bottom {
      min-height: 40px;
      margin-top: 10px;
      padding-top: 10px;
      border-top: 1px solid #e5e5e9;
    }
    body.browse-page .vehicle-price {
      color: #111214;
      font-size: 17px;
      font-weight: 900;
    }
    body.browse-page .vehicle-price small {
      display: inline;
      margin-left: 4px;
      color: #72747a;
      font-size: 10px;
    }
    body.browse-page .details-button {
      padding: 9px 13px;
      border: 1px solid #111214 !important;
      border-radius: 99px !important;
      background: #111214 !important;
      color: #fff !important;
      font-size: 9px;
      font-weight: 900;
      letter-spacing: .5px;
    }
    body.browse-page .details-button:hover {
      background: #343539 !important;
      color: #fff !important;
    }
    @media (max-width: 900px) {
      body.browse-page .browse-layout,
      .browse-intro {
        width: calc(100% - 40px);
      }
      body.browse-page .browse-layout {
        grid-template-columns: 190px minmax(0, 1fr);
        gap: 16px;
      }
      body.browse-page .vehicle-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
      }
    }
    @media (max-width: 600px) {
      .browse-intro {
        padding: 22px 0 16px;
      }
      body.browse-page .browse-layout {
        grid-template-columns: 1fr;
      }
      body.browse-page .filters {
        position: static;
      }
      body.browse-page .filter-groups {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
      body.browse-page .vehicle-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
      body.browse-page .vehicle-image {
        height: 120px;
      }
    }
    @media (max-width: 420px) {
      body.browse-page .browse-layout,
      .browse-intro {
        width: calc(100% - 28px);
      }
      body.browse-page .vehicle-grid {
        grid-template-columns: 1fr;
      }
      body.browse-page .vehicle-image {
        height: 170px;
      }
    }
  </style>
</head>
<body class="browse-page">
  <header class="site-header">
    <a class="brand" href="index.php">AUTORIDE</a>
    <nav class="nav" aria-label="Main navigation">
      <a href="index.php">Home</a>
      <a class="active" href="brows_vehicle.php" aria-current="page">Browse Vehicle</a>
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

  <section class="browse-intro" aria-labelledby="browse-title">
    <div class="browse-eyebrow">Find Your Match</div>
    <h1 id="browse-title">Browse Vehicles</h1>
    <p>Filter by vehicle type, fuel, transmission, passengers, brand, and price range.</p>
  </section>

  <main class="browse-layout">
    <aside class="filters" aria-labelledby="filter-title">
      <h1 id="filter-title">Refine your search</h1>
      <div class="filter-groups">
        <section class="filter-group" data-filter-group="fuel">
          <h2>FUEL TYPE</h2>
          <label class="filter-option"><input type="checkbox" value="Gas">Gas</label>
          <label class="filter-option"><input type="checkbox" value="Diesel">Diesel</label>
        </section>

        <section class="filter-group" data-filter-group="transmission">
          <h2>TRANSMISSION</h2>
          <label class="filter-option"><input type="checkbox" value="Automatic">Automatic</label>
          <label class="filter-option"><input type="checkbox" value="Manual">Manual</label>
        </section>

        <section class="filter-group" data-filter-group="passengers">
          <h2>NUMBER OF PASSENGERS</h2>
          <label class="filter-option"><input type="checkbox" value="2">2 passengers</label>
          <label class="filter-option"><input type="checkbox" value="4">4 passengers</label>
          <label class="filter-option"><input type="checkbox" value="6">6 passengers</label>
          <label class="filter-option"><input type="checkbox" value="7+">7+ passengers</label>
        </section>

        <section class="filter-group" data-filter-group="brand">
          <h2>BRAND</h2>
          <label class="filter-option"><input type="checkbox" value="Toyota">Toyota</label>
          <label class="filter-option"><input type="checkbox" value="BMW">BMW</label>
          <label class="filter-option"><input type="checkbox" value="Honda">Honda</label>
          <label class="filter-option"><input type="checkbox" value="Ford">Ford</label>
          <label class="filter-option"><input type="checkbox" value="Mitsubishi">Mitsubishi</label>
        </section>

        <section class="filter-group" data-filter-group="price">
          <h2>PRICE RANGE (PHP)</h2>
          <div class="price-range-fields">
            <label><input id="min-price" type="number" min="0" step="100" placeholder="Min ₱" aria-label="Minimum daily price in pesos"></label>
            <label><input id="max-price" type="number" min="0" step="100" placeholder="Max ₱" aria-label="Maximum daily price in pesos"></label>
          </div>
        </section>

        <section class="filter-group" data-filter-group="type">
          <h2>VEHICLE TYPE</h2>
          <label class="filter-option"><input type="checkbox" value="Car">Car</label>
          <label class="filter-option"><input type="checkbox" value="SUV">SUV</label>
          <label class="filter-option"><input type="checkbox" value="Van">Van</label>
          <label class="filter-option"><input type="checkbox" value="Motorcycle">Motorcycle</label>
          <label class="filter-option"><input type="checkbox" value="Sports Car">Sports Car</label>
        </section>
      </div>
      <button class="clear-filters" type="button" id="clear-filters">Clear Filters</button>
    </aside>

    <section class="results" aria-label="Available vehicles">
      <div class="results-toolbar">
        <div class="results-count"><strong id="vehicle-count"><?php echo count($vehicles); ?> vehicles to explore</strong></div>
        <div class="sort-control">
          <label for="sort-vehicles">Sort by:</label>
          <select id="sort-vehicles">
            <option value="popularity">Sort: Featured</option>
            <option value="price-low">Price: Low to High</option>
            <option value="price-high">Price: High to Low</option>
            <option value="name">Name</option>
          </select>
        </div>
      </div>

      <div class="vehicle-grid" id="vehicle-grid">
        <?php foreach ($vehicles as $index => $vehicle): ?>
          <?php
            $brandFilter = ucwords(strtolower($vehicle['brand']));
            $transmissionFilter = strtolower($vehicle['transmission']) === 'manual' ? 'Manual' : 'Automatic';
          ?>
          <article class="vehicle-card" data-brand="<?php echo e($brandFilter); ?>" data-type="<?php echo e($vehicle['category']); ?>" data-price="<?php echo e($vehicle['daily_rate']); ?>" data-passengers="<?php echo (int) $vehicle['seats']; ?>" data-transmission="<?php echo e($transmissionFilter); ?>" data-fuel="<?php echo e($vehicle['fuel_type']); ?>" data-popularity="<?php echo count($vehicles) - $index; ?>" data-name="<?php echo e($vehicle['name']); ?>">
            <div class="vehicle-image" role="img" aria-label="<?php echo e($vehicle['name']); ?> image placeholder"><span class="vehicle-status"><?php echo $index === count($vehicles) - 1 ? 'Premium' : 'Available'; ?></span></div>
            <div class="vehicle-info">
              <div class="vehicle-brand"><?php echo e(strtoupper($vehicle['category'] . ' · ' . $vehicle['fuel_type'] . ' · ' . $transmissionFilter . ' · ' . (int) $vehicle['seats'] . ' passengers')); ?></div><h2 class="vehicle-name"><?php echo e($vehicle['brand']); ?></h2>
              <div class="vehicle-meta">⚙ &nbsp;<?php echo e($vehicle['fuel_type']); ?> · <?php echo e($vehicle['transmission']); ?> · <?php echo (int) $vehicle['seats']; ?> Seats</div>
              <div class="vehicle-bottom"><div class="vehicle-price">PHP <?php echo number_format((float) $vehicle['daily_rate']); ?><small>/ day</small></div><a class="details-button" href="vehicle_details.php?id=<?php echo rawurlencode($vehicle['slug']); ?><?php echo $searchPickup !== '' ? '&amp;pickup=' . rawurlencode($searchPickup) : ''; ?><?php echo $searchReturn !== '' ? '&amp;return=' . rawurlencode($searchReturn) : ''; ?>">VIEW DETAILS</a></div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <p class="empty-state" id="empty-state">No vehicles match the selected filters.</p>
    </section>
  </main>

  <section class="browse-feedback" id="feedback" aria-labelledby="feedback-title">
    <h2 id="feedback-title">SUBMIT YOUR FEEDBACK</h2>
    <p>Share your experience with any available vehicle. Completed rentals are linked to verified reviews when possible.</p>
    <?php if ($feedbackMessage !== ''): ?><p class="feedback-message" role="status"><?php echo e($feedbackMessage); ?></p><?php endif; ?>
    <?php if ($feedbackError !== ''): ?><p class="feedback-message error" role="alert"><?php echo e($feedbackError); ?></p><?php endif; ?>
    <?php if (empty($_SESSION['customer_user']['id'])): ?>
      <p class="feedback-login"><a href="log_in.php">Log in</a> to submit feedback.</p>
    <?php elseif (!$vehicles): ?>
      <p class="feedback-login">There are no available vehicles to select for feedback right now.</p>
    <?php else: ?>
      <form class="feedback-form" method="post" action="brows_vehicle.php#feedback">
        <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
        <input type="hidden" name="action" value="submit_browse_review">
        <label for="feedback-vehicle">VEHICLE
          <select id="feedback-vehicle" name="vehicle" required>
            <?php foreach ($vehicles as $vehicle): ?>
              <option value="<?php echo e($vehicle['slug']); ?>"<?php echo $feedbackVehicle === $vehicle['slug'] ? ' selected' : ''; ?>><?php echo e($vehicle['brand'] . ' ' . $vehicle['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label for="feedback-rating">RATING
          <select id="feedback-rating" name="rating" required>
            <?php foreach ([5, 4, 3, 2, 1] as $rating): ?>
              <option value="<?php echo $rating; ?>"<?php echo $feedbackRating === (string) $rating ? ' selected' : ''; ?>><?php echo str_repeat('★', $rating) . str_repeat('☆', 5 - $rating); ?> &nbsp; <?php echo $rating; ?> out of 5</option>
            <?php endforeach; ?>
          </select>
        </label>
        <label for="feedback-review">YOUR FEEDBACK
          <textarea id="feedback-review" name="review" placeholder="Describe your rental experience..." required minlength="3" maxlength="2000"><?php echo e($feedbackText); ?></textarea>
        </label>
        <button type="submit">SUBMIT YOUR FEEDBACK</button>
      </form>
    <?php endif; ?>
  </section>

  <footer class="site-footer">
    <div class="footer-top">
      <div>
        <a class="footer-brand" href="index.php">AUTORIDE</a>
         <p>Your trusted partner for affordable vehicle rentals, easy bookings, and reliable rides.</p>
      </div>
      <div>
        <h2>QUICK LINKS</h2>
        <ul><li><a href="index.php">Home</a></li><li><a href="brows_vehicle.php">Browse Vehicle</a></li><li><a href="about_us.php">About Us</a></li><li><a href="rentflow.php">Rent</a></li><li><a href="log_in.php">Login</a></li></ul>
      </div>
      <div>
        <h2>SOCIAL MEDIA</h2>
        <ul><li><a href="#">Facebook</a></li><li><a href="#">Instagram</a></li><li><a href="#">TikTok</a></li></ul>
      </div>
      <div>
        <h2>POLICIES</h2>
        <ul><li><a href="privacy_policy.php">Privacy Policy</a></li><li><a href="terms_of_policy.php">Terms of Policy</a></li><li><a href="faqs.php">FAQs</a></li><li><a href="terms_and_conditions.php">Terms and Conditions</a></li><li><a href="return_policy.php">Return Policy</a></li></ul>
      </div>
    </div>
     <div class="footer-bottom">
      <span>© 2026 Autoride. All rights reserved.</span>
      <span><a href="terms_of_policy.php">TERMS OF SERVICE</a> &nbsp; <a href="privacy_policy.php">PRIVACY POLICY</a></span>
    </div>
  </footer>

  <script>
    (function () {
      var grid = document.getElementById('vehicle-grid');
      var cards = Array.prototype.slice.call(grid.querySelectorAll('.vehicle-card'));
      var count = document.getElementById('vehicle-count');
      var emptyState = document.getElementById('empty-state');
      var sortSelect = document.getElementById('sort-vehicles');
      var minPrice = document.getElementById('min-price');
      var maxPrice = document.getElementById('max-price');
      var clearFiltersButton = document.getElementById('clear-filters');
      var filters = Array.prototype.slice.call(document.querySelectorAll('[data-filter-group]'));

      function selectedFilters(group) {
        if (group.getAttribute('data-filter-group') === 'price') {
          var min = minPrice.value.trim();
          var max = maxPrice.value.trim();
          return min || max ? [min + '|' + max] : [];
        }
        return Array.prototype.slice.call(group.querySelectorAll('input:checked')).map(function (input) {
          return input.value;
        });
      }

      function updateResults() {
        var activeFilters = filters.map(function (group) {
          return { key: group.getAttribute('data-filter-group'), values: selectedFilters(group) };
        }).filter(function (filter) {
          return filter.values.length > 0;
        });
        var visibleCards = [];

        cards.forEach(function (card) {
          var matches = activeFilters.every(function (filter) {
            var cardValue = card.getAttribute('data-' + filter.key);
            if (filter.key === 'price') {
              return filter.values.some(function (range) {
                var bounds = range.split('|');
                var price = Number(cardValue);
                var min = bounds[0] === '' ? 0 : Number(bounds[0]);
                var max = bounds[1] === '' ? Number.POSITIVE_INFINITY : Number(bounds[1]);
                return price >= min && price <= max;
              });
            }
            if (filter.key === 'passengers') {
              return filter.values.some(function (value) {
                return value === '7+' ? Number(cardValue) >= 7 : cardValue === value;
              });
            }
            return filter.values.indexOf(cardValue) !== -1;
          });
          card.hidden = !matches;
          if (matches) visibleCards.push(card);
        });

        var sortBy = sortSelect.value;
        visibleCards.sort(function (a, b) {
          if (sortBy === 'price-low') return Number(a.dataset.price) - Number(b.dataset.price);
          if (sortBy === 'price-high') return Number(b.dataset.price) - Number(a.dataset.price);
          if (sortBy === 'name') return a.dataset.name.localeCompare(b.dataset.name);
          return Number(b.dataset.popularity) - Number(a.dataset.popularity);
        }).forEach(function (card) {
          grid.appendChild(card);
        });

        count.textContent = visibleCards.length + (visibleCards.length === 1 ? ' vehicle to explore' : ' vehicles to explore');
        emptyState.classList.toggle('show', visibleCards.length === 0);
      }

      filters.forEach(function (group) {
        group.addEventListener('change', updateResults);
      });
      minPrice.addEventListener('input', updateResults);
      maxPrice.addEventListener('input', updateResults);
      clearFiltersButton.addEventListener('click', function () {
        filters.forEach(function (group) {
          Array.prototype.forEach.call(group.querySelectorAll('input[type="checkbox"]'), function (input) {
            input.checked = false;
          });
        });
        minPrice.value = '';
        maxPrice.value = '';
        updateResults();
      });
      sortSelect.addEventListener('change', updateResults);
    })();
  </script>
</body>
</html>
