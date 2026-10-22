<?php
require_once __DIR__ . '/site_init.php';

if (!isset($vehicle) || !is_array($vehicle)) {
    $vehicle = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Rental Request | Autoride</title>
  <style>
    :root {
      --ink: #111827;
      --text: #374151;
      --muted: #6b7280;
      --line: #d1d5db;
      --border: #e9ebef;
      --light: #f8f9fb;
      --white: #fff;
    }
    * { box-sizing: border-box; }
    body { zoom: 1.2; min-height: 100vh; margin: 0; display: flex; flex-direction: column; color: var(--text); background: var(--light); font: 11px/1.4 Arial, Helvetica, sans-serif; }
    a { color: inherit; text-decoration: none; }
    button, input, select { font: inherit; }
    .site-header { height: 52px; display: flex; align-items: center; justify-content: space-between; padding: 0 max(24px, calc((100% - 1140px) / 2)); background: var(--white); border-bottom: 1px solid var(--border); }
    .brand { color: var(--ink); font-size: 14px; font-weight: 800; letter-spacing: .2px; }
    .nav { display: flex; align-items: center; gap: 24px; }
    .nav a { color: var(--text); font-size: 11px; white-space: nowrap; }
    .nav a:hover, .nav a[aria-current="page"] { color: var(--ink); text-decoration: underline; text-underline-offset: 4px; }
    .progress { display: flex; justify-content: space-between; padding: 8px max(16px, calc((100% - 740px) / 2)); background: #fff; border-bottom: 1px solid var(--border); }
    .step { position: relative; display: flex; min-height: 28px; align-items: center; gap: 5px; padding: 4px 6px; border: 0; background: none; color: var(--muted); font: inherit; font-size: 6px; white-space: nowrap; text-decoration: none; cursor: pointer; }
    .step span { width: 11px; height: 11px; display: grid; place-items: center; border: 1px solid var(--line); border-radius: 50%; background: #f4f5f7; font-size: 6px; }
    .step.active { color: var(--ink); font-weight: 700; }
    .step.active span { color: #fff; background: var(--ink); border-color: var(--ink); }
    .step.completed span { color: #fff; background: #047857; border-color: #047857; }
    .step:hover { color: var(--ink); text-decoration: underline; text-underline-offset: 3px; }
    .availability-status { margin-top: 8px; padding: 6px 8px; border: 1px solid var(--line); border-radius: 2px; background: #f4f5f7; color: var(--muted); font-size: 6px; }
    .availability-status.available { border-color: #b7dfc9; background: #e8f6ee; color: #047857; }
    .availability-status.unavailable { border-color: #efb4b4; background: #fff5f5; color: #8b1d1d; }
    main { width: min(100%, 820px); flex: 1; margin: auto; padding: 20px 16px 28px; }
    .layout { display: grid; grid-template-columns: minmax(0, 1.9fr) minmax(180px, 1fr); align-items: start; gap: 15px; }
    .card { margin-bottom: 10px; padding: 11px; border: 1px solid var(--border); border-radius: 3px; background: var(--white); }
    .selected { display: flex; align-items: center; gap: 8px; }
    .thumb { width: 38px; height: 32px; display: grid; place-items: center; border: 1px solid var(--line); background: #f1f2f4; color: var(--ink); font-size: 4px; }
    .selected small { display: block; color: var(--muted); font-size: 5px; font-weight: 700; }
    .selected h1 { margin: 2px 0; color: var(--ink); font-size: 8px; }
    .selected-price { margin-left: auto; color: var(--ink); font-size: 7px; white-space: nowrap; }
    h2 { margin: 0 0 3px; color: var(--ink); font-size: 8px; }
    .hint { margin: 0 0 7px; color: var(--muted); font-size: 6px; }
    .options, .fields { display: grid; grid-template-columns: 1fr 1fr; gap: 7px; }
    .option { padding: 7px; border: 1px solid var(--line); border-radius: 3px; }
    .option strong { color: var(--ink); font-size: 6px; }
    .option p { margin: 4px 0; color: var(--muted); font-size: 5px; }
    .option label { display: flex; align-items: center; gap: 4px; }
    input[type="radio"], input[type="checkbox"] { accent-color: var(--ink); }
    .option input { width: 8px; height: 8px; margin: 0; }
    .tag { display: inline-block; margin: 1px; padding: 2px 4px; border: 1px solid var(--border); border-radius: 2px; color: var(--muted); font-size: 4px; }
    .field label { display: block; margin-bottom: 3px; color: var(--ink); font-size: 5px; font-weight: 700; }
    .field input, .field select { width: 100%; height: 20px; padding: 0 5px; border: 1px solid var(--line); border-radius: 2px; background: #fff; color: var(--text); font-size: 6px; }
    .status { margin-top: 8px; padding: 6px 8px; border: 1px solid #b7dfc9; border-radius: 2px; background: #e8f6ee; color: #047857; font-size: 5px; }
    .status::before { content: "✓"; margin-right: 5px; }
    .documents { display: grid; gap: 4px; }
    .document, .agree { display: flex; align-items: flex-start; gap: 6px; padding: 5px 6px; border: 1px solid var(--border); background: #fafbfc; }
    .document input, .agree input { width: 8px; height: 8px; margin: 1px 0 0; }
    .document strong { display: block; color: var(--ink); font-size: 5px; }
    .document small { color: var(--muted); font-size: 4.5px; }
    .agree { margin-top: 6px; color: var(--muted); font-size: 5px; }
    .agree b { color: var(--ink); }
    .summary h2 { padding-bottom: 6px; border-bottom: 1px solid var(--border); font-size: 8px; }
    .summary-row, .total { display: flex; justify-content: space-between; gap: 6px; margin: 6px 0; color: var(--muted); font-size: 5px; }
    .summary-row strong { color: var(--ink); text-align: right; }
    .total { padding-top: 6px; border-top: 1px solid var(--border); color: var(--ink); font-size: 7px; font-weight: 700; }
    .fuel, .request { margin-top: 8px; padding: 7px; border: 1px solid var(--border); border-radius: 2px; background: #fafbfc; }
    .fuel strong, .request strong { color: var(--ink); font-size: 5px; }
    .fuel p { margin: 2px 0 0; color: var(--muted); font-size: 4.5px; }
    .request h3 { margin: 0 0 5px; color: var(--ink); font-size: 5px; }
    .request-row { display: flex; justify-content: space-between; gap: 6px; margin-top: 4px; color: var(--muted); font-size: 4.5px; }
    .request-row strong { max-width: 60%; text-align: right; overflow-wrap: anywhere; }
    .submit { width: 100%; margin-top: 8px; padding: 7px; border: 0; border-radius: 2px; background: var(--ink); color: #fff; font-size: 5px; font-weight: 700; cursor: pointer; }
    .privacy { margin: 6px 0 0; color: var(--muted); text-align: center; font-size: 4.5px; }
    .footer { padding: 22px 5% 8px; background: #f1f2f4; }
    .footer-grid { display: grid; grid-template-columns: 1.6fr 1fr 1fr 1fr; gap: 15px; }
    .footer h3 { margin: 0 0 6px; color: var(--ink); font-size: 6px; }
    .footer p, .footer li { color: var(--text); font-size: 5px; }
    .footer p { max-width: 130px; margin: 0; }
    .footer ul { margin: 0; padding: 0; list-style: none; }
    .footer li { margin-bottom: 3px; }
    .footer-bottom { display: flex; justify-content: space-between; gap: 8px; margin-top: 12px; padding-top: 6px; border-top: 1px solid var(--line); color: var(--muted); font-size: 4.5px; }
    .not-found { margin: auto; padding: 30px; text-align: center; }

    @media (min-width: 900px) {
      .progress { padding-top: 10px; padding-bottom: 10px; }
      .step { font-size: 9px; }
      .step span { width: 16px; height: 16px; font-size: 8px; }
      main { width: min(100%, 1100px); padding: 28px 28px 38px; }
      .layout { grid-template-columns: minmax(0, 1.9fr) minmax(280px, 1fr); gap: 24px; }
      .card { margin-bottom: 14px; padding: 16px; }
      .thumb { width: 58px; height: 48px; font-size: 6px; }
      .selected small { font-size: 7px; }
      .selected h1 { font-size: 12px; }
      .selected-price { font-size: 9px; }
      h2, .summary h2 { font-size: 12px; }
      .hint { font-size: 8px; }
      .option { padding: 10px; }
      .option strong { font-size: 8px; }
      .option p { font-size: 7px; }
      .tag { font-size: 6px; }
      .field label { font-size: 7px; }
      .field input, .field select { height: 28px; font-size: 8px; }
      .status { font-size: 7px; }
      .document { padding: 7px 9px; }
      .document input, .agree input { width: 11px; height: 11px; }
      .document strong { font-size: 7px; }
      .document small, .agree { font-size: 6px; }
      .summary-row { font-size: 8px; }
      .total { font-size: 10px; }
      .fuel strong, .request strong, .request h3 { font-size: 7px; }
      .fuel p, .request-row { font-size: 6px; }
      .submit { padding: 9px; font-size: 7px; }
      .privacy { font-size: 6px; }
      .footer h3 { font-size: 8px; }
      .footer p, .footer li { font-size: 8px; }
      .footer-bottom { font-size: 7px; }
    }
    @media (max-width: 560px) {
      .progress { padding: 7px 6px; }
      .step { gap: 3px; font-size: 4.5px; }
      .step span { width: 9px; height: 9px; font-size: 5px; }
      main { padding: 12px 10px 20px; }
      .layout { grid-template-columns: 1fr; gap: 0; }
      .right { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
      .footer-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 360px) {
      .nav { gap: 4px; }
      .nav a, .step { font-size: 4px; }
      .options { grid-template-columns: 1fr; }
      .right { grid-template-columns: 1fr; gap: 0; }
      .fields { gap: 6px; }
    }
    .nav-logout { padding: 0; border: 0; background: none; color: inherit; font: inherit; font-size: 11px; cursor: pointer; }
    .nav-logout:hover { color: var(--ink); text-decoration: underline; text-underline-offset: 4px; }
    @media (max-width: 760px) {
      .site-header { height: auto; min-height: 52px; padding: 8px 16px; flex-wrap: wrap; }
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
body.rentflow-page {
  background: #f5f5f6 !important;
  color: #111214 !important;
  font-size: 14px !important;
  line-height: 1.5 !important;
}
body.rentflow-page .site-header {
  background: #f5f5f6 !important;
  box-shadow: none;
}
.rentflow-intro {
  width: min(1280px, calc(100% - 64px));
  margin: 0 auto;
  padding: 30px 0 18px;
}
.rentflow-eyebrow {
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
.rentflow-eyebrow::before {
  width: 22px;
  height: 2px;
  background: #f3c928;
  content: "";
}
.rentflow-intro h1 {
  margin: 0;
  color: #111214;
  font-size: clamp(28px, 3vw, 40px);
  font-weight: 900;
  line-height: 1.1;
  letter-spacing: -1px;
  text-transform: uppercase;
}
.rentflow-intro p {
  max-width: 560px;
  margin: 8px 0 0;
  color: #72747a;
  font-size: 12px;
}
body.rentflow-page .progress {
  width: min(1280px, calc(100% - 64px));
  justify-content: stretch;
  gap: 12px;
  margin: 0 auto;
  padding: 0 0 18px;
  border: 0;
  background: transparent;
}
body.rentflow-page .step {
  min-width: 0;
  flex: 1 1 0;
  gap: 9px;
  padding: 4px 0;
  color: #72747a;
  font-size: 11px;
  font-weight: 700;
}
body.rentflow-page .step span {
  width: 30px;
  height: 30px;
  flex: 0 0 30px;
  border: 0;
  background: #e6e6e9;
  color: #4b4b50;
  font-size: 12px;
  font-weight: 900;
}
body.rentflow-page .step.active {
  color: #111214;
}
body.rentflow-page .step.active span,
body.rentflow-page .step.completed span {
  background: #f3c928;
  color: #111214;
}
body.rentflow-page main:not(.not-found) {
  width: min(1280px, calc(100% - 64px));
  max-width: none;
  padding: 0 0 48px;
}
body.rentflow-page .layout {
  grid-template-columns: minmax(0, 1.9fr) minmax(300px, .8fr);
  gap: 24px;
  align-items: start;
}
body.rentflow-page .layout > div > .card,
body.rentflow-page .summary {
  margin-bottom: 16px;
  padding: 24px;
  border: 1px solid #e3e3e7 !important;
  border-radius: 14px !important;
  background: #fff !important;
  box-shadow: none;
}
body.rentflow-page .layout > div > .selected {
  align-items: center;
  padding: 20px 22px;
}
body.rentflow-page .thumb {
  width: 230px;
  height: 150px;
  flex: 0 0 230px;
  border: 0;
  border-radius: 14px;
  background: linear-gradient(180deg, #ececef 0%, #ececef 52%, #a5a5a8 100%);
  font-size: 13px;
}
body.rentflow-page .selected small {
  font-size: 10px;
  letter-spacing: .7px;
}
body.rentflow-page .selected h1 {
  margin: 4px 0;
  font-size: 18px;
}
body.rentflow-page .selected-price {
  font-size: 13px;
}
body.rentflow-page h2,
body.rentflow-page .summary h2 {
  margin: 0 0 6px;
  color: #111214;
  font-size: 17px;
}
body.rentflow-page .hint {
  margin: 0 0 14px;
  color: #72747a;
  font-size: 12px;
}
body.rentflow-page .options {
  gap: 12px;
}
body.rentflow-page .option {
  padding: 14px;
  border: 1px solid #e1e2e6;
  border-radius: 10px;
}
body.rentflow-page .option strong {
  color: #111214;
  font-size: 13px;
}
body.rentflow-page .option p {
  margin: 8px 0;
  color: #72747a;
  font-size: 12px;
  line-height: 1.5;
}
body.rentflow-page .option input,
body.rentflow-page .document input,
body.rentflow-page .agree input {
  width: 16px;
  height: 16px;
}
body.rentflow-page .tag {
  margin: 2px 4px 2px 0;
  padding: 4px 8px;
  border-radius: 99px;
  font-size: 10px;
}
body.rentflow-page .fields {
  gap: 14px 12px;
}
body.rentflow-page .field label {
  margin-bottom: 7px;
  color: #111214;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: .7px;
  text-transform: uppercase;
}
body.rentflow-page .field input,
body.rentflow-page .field select {
  height: 44px;
  padding: 0 12px;
  border: 1px solid #e1e2e6;
  border-radius: 9px;
  background: #f7f7f8;
  color: #111214;
  font-size: 13px;
}
body.rentflow-page .availability-status {
  margin-top: 14px;
  padding: 12px;
  border-radius: 9px;
  font-size: 12px;
}
body.rentflow-page .documents {
  gap: 8px;
}
body.rentflow-page .document,
body.rentflow-page .agree {
  gap: 10px;
  padding: 11px 12px;
  border: 1px solid #e3e3e7;
  border-radius: 9px;
  background: #fafafa;
  font-size: 12px;
}
body.rentflow-page .document strong {
  margin-bottom: 3px;
  font-size: 12px;
}
body.rentflow-page .document small {
  color: #72747a;
  font-size: 11px;
}
body.rentflow-page .agree {
  margin-top: 12px;
  line-height: 1.5;
}
body.rentflow-page .requirements-copy {
  margin: 12px 0 18px;
  padding: 14px 16px;
  border: 1px solid #e3e3e7;
  border-radius: 10px;
  background: #fafafa;
}
body.rentflow-page .requirements-copy p,
body.rentflow-page .requirements-copy li {
  color: #4b5563;
  font-size: 12px;
  line-height: 1.6;
}
body.rentflow-page .requirements-copy p {
  margin: 0 0 8px;
}
body.rentflow-page .requirements-copy ul {
  margin: 0;
  padding-left: 20px;
}
body.rentflow-page .requirements-copy li {
  margin: 3px 0;
}
body.rentflow-page .document-upload,
body.rentflow-page .additional-notes {
  display: block;
  margin-top: 18px;
}
body.rentflow-page .document-upload > span,
body.rentflow-page .additional-notes > span {
  display: block;
  margin-bottom: 7px;
  color: #111214;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: .7px;
  text-transform: uppercase;
}
body.rentflow-page .document-upload small {
  display: block;
  margin: -2px 0 8px;
  color: #72747a;
  font-size: 11px;
  font-weight: 400;
  letter-spacing: normal;
  line-height: 1.5;
  text-transform: none;
}
body.rentflow-page .document-upload input[type="file"] {
  width: 100%;
  min-height: 42px;
  padding: 8px 10px;
  border: 1px solid #e1e2e6;
  border-radius: 9px;
  background: #f7f7f8;
  color: #4b5563;
  font-size: 12px;
}
body.rentflow-page .additional-notes textarea {
  width: 100%;
  min-height: 76px;
  padding: 12px;
  border: 1px solid #e1e2e6;
  border-radius: 9px;
  background: #f7f7f8;
  color: #111214;
  font-size: 13px;
  resize: vertical;
}
body.rentflow-page .check-availability-action {
  min-height: 44px;
  padding: 0 20px;
  border-radius: 99px !important;
  font-size: 10px;
  font-weight: 900 !important;
  letter-spacing: .7px;
  text-transform: uppercase;
}
body.rentflow-page .check-availability-action {
  border: 1px solid #d9d9df !important;
  background: #fff !important;
  color: #111214 !important;
}
body.rentflow-page .check-availability-action:hover {
  background: #f5f5f6 !important;
  color: #111214 !important;
}
body.rentflow-page .summary h2 {
  padding-bottom: 12px;
  border-bottom: 1px solid #e5e5e9;
}
body.rentflow-page .summary-row,
body.rentflow-page .total {
  gap: 12px;
  margin: 12px 0;
  font-size: 12px;
}
body.rentflow-page .summary-row strong {
  color: #111214;
}
body.rentflow-page .total {
  padding-top: 12px;
  font-size: 16px;
}
body.rentflow-page .fuel,
body.rentflow-page .request {
  margin-top: 14px;
  padding: 13px;
  border: 1px solid #e3e3e7;
  border-radius: 9px;
  background: #fafafa;
}
body.rentflow-page .fuel strong,
body.rentflow-page .request strong,
body.rentflow-page .request h3 {
  color: #111214;
  font-size: 11px;
}
body.rentflow-page .fuel p,
body.rentflow-page .request-row {
  margin-top: 6px;
  color: #5f6065;
  font-size: 11px;
}
body.rentflow-page .submit {
  min-height: 46px;
  margin-top: 16px;
  border: 1px solid #f3c928 !important;
  border-radius: 99px !important;
  background: #f3c928 !important;
  color: #111214 !important;
  font-size: 11px;
  font-weight: 900;
  letter-spacing: .7px;
}
body.rentflow-page .privacy {
  margin-top: 10px;
  font-size: 10px;
}
@media (max-width: 760px) {
  .rentflow-intro,
  body.rentflow-page .progress,
  body.rentflow-page main:not(.not-found) {
    width: calc(100% - 36px);
  }
  body.rentflow-page .layout {
    grid-template-columns: 1fr;
    gap: 0;
  }
  body.rentflow-page .right {
    display: block;
  }
  body.rentflow-page .summary {
    margin-bottom: 0;
  }
  body.rentflow-page .step {
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
    white-space: normal;
    font-size: 10px;
  }
}
@media (max-width: 480px) {
  .rentflow-intro,
  body.rentflow-page .progress,
  body.rentflow-page main:not(.not-found) {
    width: calc(100% - 28px);
  }
  body.rentflow-page .progress {
    gap: 4px;
  }
  body.rentflow-page .step {
    align-items: center;
    text-align: center;
    font-size: 8px;
  }
  body.rentflow-page .step span {
    width: 26px;
    height: 26px;
    flex-basis: 26px;
  }
  body.rentflow-page .options,
  body.rentflow-page .fields {
    grid-template-columns: 1fr;
  }
  body.rentflow-page .layout > div > .card,
  body.rentflow-page .summary {
    padding: 18px;
  }
  body.rentflow-page .thumb {
    width: 160px;
    height: 110px;
    flex-basis: 160px;
  }
}
  </style>
</head>
<body class="rentflow-page">
  <header class="site-header">
    <a class="brand" href="index.php">AUTORIDE</a>
    <nav class="nav" aria-label="Main navigation">
      <a href="index.php">Home</a><a href="brows_vehicle.php">Browse Vehicle</a>
      <a href="rentflow.php" aria-current="page">Rent</a><a href="about_us.php">About Us</a>
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
    </nav>
  </header>

  <div class="rentflow-intro">
    <div class="rentflow-eyebrow">A Few Easy Steps</div>
    <h1>Rent Your Vehicle</h1>
    <p>Select a vehicle, choose your rental dates, check availability, review charges, then submit your request.</p>
  </div>

  <nav class="progress" aria-label="Rental steps">
    <a class="step completed" href="brows_vehicle.php" aria-label="Step 1: Select Vehicle"><span>1</span>Select Vehicle</a>
    <a class="step active" href="#rental-dates" aria-current="step" aria-label="Step 2: Select Dates"><span>2</span>Select Dates</a>
    <a class="step" href="#rental-dates" aria-label="Step 3: Check Availability"><span>3</span>Availability</a>
    <a class="step" href="#rental-charges" aria-label="Step 4: Rental Charges"><span>4</span>Rental Charges</a>
    <a class="step" href="#request-booking" aria-label="Step 5: Request"><span>5</span>Request</a>
  </nav>

  <?php if (!$vehicle): ?>
    <main class="not-found">
      <div><h1>Choose a vehicle to start</h1><p><a href="brows_vehicle.php">Return to Browse Vehicles</a></p></div>
    </main>
  <?php else: ?>
    <main>
      <form class="layout" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo $escape($_SESSION['csrf_token'] ?? ''); ?>">
        <input type="hidden" name="action" value="request_booking">
        <input type="hidden" name="vehicle_id" value="<?php echo $escape($_GET['id'] ?? ''); ?>">
        <div>
          <section class="card selected">
            <div class="thumb">[ <?php echo $escape($vehicle[0]); ?> ]</div>
            <div><small>SELECTED VEHICLE</small><h1><?php echo $escape($vehicle[1]); ?></h1><span class="selected-price">PHP <?php echo number_format($vehicle[3]); ?> / day</span></div>
          </section>

          <section class="card">
            <h2>Rental Options</h2><p class="hint">Choose how you want to rent this vehicle.</p>
            <div class="options">
              <div class="option"><label><input type="radio" name="rental_type" value="self_drive" <?php echo $rentalType === 'self_drive' ? 'checked' : ''; ?>><strong>Self-Drive</strong></label><p>Drive the vehicle yourself. Valid driver's license required.</p><span class="tag">No driver fee</span><span class="tag">License required</span></div>
              <div class="option"><label><input type="radio" name="rental_type" value="with_driver" <?php echo $rentalType === 'with_driver' ? 'checked' : ''; ?>><strong>With Driver</strong></label><p>A professional driver will be assigned. Additional fee applies.</p><span class="tag">+ PHP 500 / day</span><span class="tag">Fuel included</span></div>
            </div>
          </section>

          <section class="card" id="rental-dates">
            <h2>Select Rental Dates</h2><p class="hint">Choose your rental dates and pickup details.</p>
            <div class="fields">
              <div class="field"><label for="pickup">Pick-up Date</label><input id="pickup" name="pickup" type="date" value="<?php echo $escape($pickup); ?>" required></div>
              <div class="field"><label for="return">Return Date</label><input id="return" name="return" type="date" value="<?php echo $escape($returnDate); ?>" required></div>
              <div class="field"><label for="time">Pick-up Time</label><select id="time" name="pickup_time" required><option value="">Select a time</option><option value="08:00" <?php echo $pickupTime === '08:00' ? 'selected' : ''; ?>>8:00 AM</option><option value="10:00" <?php echo $pickupTime === '10:00' ? 'selected' : ''; ?>>10:00 AM</option><option value="12:00" <?php echo $pickupTime === '12:00' ? 'selected' : ''; ?>>12:00 PM</option><option value="14:00" <?php echo $pickupTime === '14:00' ? 'selected' : ''; ?>>2:00 PM</option></select></div>
              <div class="field"><label for="location">Pick-up Location</label><select id="location" name="pickup_location" required><option value="">Select branch or address...</option><option <?php echo $pickupLocation === 'Makati' ? 'selected' : ''; ?>>Makati</option><option <?php echo $pickupLocation === 'Quezon City' ? 'selected' : ''; ?>>Quezon City</option><option <?php echo $pickupLocation === 'Pasig' ? 'selected' : ''; ?>>Pasig</option><option <?php echo $pickupLocation === 'Taguig' ? 'selected' : ''; ?>>Taguig</option></select></div>
            </div>
            <p class="availability-status" id="availability-status" role="status" aria-live="polite">Select your dates, then choose “Check Availability”.</p>
            <button class="check-availability-action" id="check-availability" type="button">Check Availability</button>
          </section>

          <section class="card">
            <h2>Rental Requirements</h2>
            <div class="requirements-copy">
              <p>Before submitting, make sure you meet the legal driving-age requirement and have a valid driver's license.</p>
              <ul>
                <li>Valid driver's license</li>
                <li>Valid government-issued ID</li>
                <li>Proof of address, if requested</li>
                <li>Security deposit, subject to rental policy</li>
              </ul>
            </div>
            <label class="agree"><input type="checkbox" name="terms" value="1" required><span>I have read and agree to the <a href="terms_and_conditions.php"><b>Rental Terms &amp; Conditions</b></a> and <a href="privacy_policy.php"><b>Privacy Policy</b></a>.</span></label>
            <label class="document-upload" for="rental-documents">
              <span>Upload Required Documents</span>
              <small>Attach a clear image or PDF of your driver's license and valid ID. This demo form does not upload files to a server.</small>
              <input id="rental-documents" name="rental_documents[]" type="file" accept="image/*,.pdf" multiple>
            </label>
            <label class="additional-notes" for="rental-notes">
              <span>Additional Notes (Optional)</span>
              <textarea id="rental-notes" name="notes" placeholder="Add any details for your request"></textarea>
            </label>
          </section>
        </div>

        <aside class="right">
          <section class="card summary" id="rental-charges">
            <h2>Rental Charges</h2>
            <?php if ($bookingError !== ''): ?><p class="hint" role="alert"><?php echo $escape($bookingError); ?><?php if (empty($_SESSION['customer_user']['id'])): ?> <a href="log_in.php">Log in</a><?php endif; ?></p><?php endif; ?>
            <?php if ($bookingSuccess !== ''): ?><div class="status" role="status"><?php echo $escape($bookingSuccess); ?></div><?php endif; ?>
            <div class="summary-row"><span>Daily Base Rate</span><strong>PHP <?php echo number_format($vehicle[3], 2); ?></strong></div>
            <div class="summary-row"><span>Rental Duration</span><strong id="rental-duration">Select dates</strong></div>
            <div class="summary-row"><span>Driver Fee</span><strong id="driver-fee">PHP 0.00</strong></div>
            <div class="summary-row"><span>Subtotal</span><strong id="rental-subtotal">Select dates</strong></div>
            <div class="summary-row"><span>Insurance Waiver (Optional)</span><strong>PHP 500.00</strong></div>
            <div class="summary-row"><span>Security Deposit</span><strong>PHP 5,000.00 *</strong></div>
            <div class="total"><span>Estimated Total</span><strong id="estimated-total">Select dates</strong></div>
            <div class="fuel"><strong>FUEL POLICY</strong><p>Full-to-Full: Return with same fuel level as received.</p></div>
            <div class="request">
              <h3>REQUEST SUMMARY</h3>
              <div class="request-row"><span>Vehicle</span><strong><?php echo $escape($vehicle[1]); ?></strong></div>
              <div class="request-row"><span>Vehicle Type</span><strong><?php echo $escape($vehicle[2]); ?></strong></div>
              <div class="request-row"><span>Dates</span><strong id="request-dates"><?php echo $pickup ? $escape($pickup) : 'YYYY-MM-DD'; ?> → <?php echo $returnDate ? $escape($returnDate) : 'YYYY-MM-DD'; ?></strong></div>
              <div class="request-row"><span>Pick-up Location</span><strong id="request-location"><?php echo $pickupLocation !== '' ? $escape($pickupLocation) : '[ BRANCH ]'; ?></strong></div>
            </div>
            <button class="submit" id="request-booking" type="submit">REQUEST VEHICLE</button>
            <p class="privacy">By requesting, you agree to our Rental Terms &amp; Conditions.</p>
          </section>
        </aside>
      </form>
    </main>
  <?php endif; ?>

  <footer class="footer">
    <div class="footer-grid">
      <div><a class="logo" href="index.php">AUTORIDE</a><p>Your trusted partner for affordable vehicle rentals, easy bookings, and reliable rides.</p></div>

      <div><h3>QUICK LINKS</h3><ul><li><a href="index.php">Home</a></li><li><a href="brows_vehicle.php">Browse Vehicle</a></li><li><a href="about_us.php">About Us</a></li><li><a href="rentflow.php">Rent</a></li><li><a href="log_in.php">Login</a></li></ul></div>
      <div><h3>SOCIAL MEDIA</h3><ul><li><a href="#">Facebook</a></li><li><a href="#">Instagram</a></li><li><a href="#">TikTok</a></li></ul></div>
      <div><h3>POLICIES</h3><ul><li><a href="privacy_policy.php">Privacy Policy</a></li><li><a href="terms_of_policy.php">Terms of Policy</a></li><li><a href="faqs.php">FAQs</a></li><li><a href="terms_and_conditions.php">Terms and Conditions</a></li><li><a href="return_policy.php">Return Policy</a></li></ul></div>
    </div>
     <div class="footer-bottom">
      <span>© 2026 Autoride. All rights reserved.</span>
      <span><a href="terms_of_policy.php">TERMS OF SERVICE</a> &nbsp; <a href="privacy_policy.php">PRIVACY POLICY</a></span>
    </div>
  </footer>
  <script>
    (function () {
      var form = document.querySelector('.layout');
      if (!form) return;

      var pickup = document.getElementById('pickup');
      var returnDate = document.getElementById('return');
      var rentalTypeInputs = form.querySelectorAll('input[name="rental_type"]');
      var dailyRate = <?php echo json_encode($vehicle ? $vehicle[3] : 0); ?>;
      var status = document.getElementById('availability-status');
      var checkButton = document.getElementById('check-availability');
      var stepLinks = document.querySelectorAll('.progress .step');
      var available = false;

      function money(amount) {
        return 'PHP ' + amount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      }

      function updateCharges() {
        var datePattern = /^\d{4}-\d{2}-\d{2}$/;
        var start = datePattern.test(pickup.value) ? new Date(pickup.value + 'T00:00:00') : null;
        var end = datePattern.test(returnDate.value) ? new Date(returnDate.value + 'T00:00:00') : null;
        var validDates = start && end && end > start;
        if (!validDates) {
          document.getElementById('rental-duration').textContent = 'Select valid dates';
          document.getElementById('driver-fee').textContent = 'Select dates';
          document.getElementById('rental-subtotal').textContent = 'Select dates';
          document.getElementById('estimated-total').textContent = 'Select dates';
          document.getElementById('request-dates').textContent =
            (pickup.value || 'YYYY-MM-DD') + ' → ' + (returnDate.value || 'YYYY-MM-DD');
          stepLinks[1].classList.add('active');
          stepLinks[1].classList.remove('completed');
          stepLinks[2].classList.remove('active', 'completed');
          stepLinks[3].classList.remove('completed');
          stepLinks[4].classList.remove('active');
          return;
        }

        var days = Math.round((end - start) / 86400000);
        var withDriver = form.querySelector('input[name="rental_type"]:checked').value === 'with_driver';
        var driverFee = withDriver ? 500 * days : 0;
        var subtotal = (dailyRate * days) + driverFee;
        document.getElementById('rental-duration').textContent = days + (days === 1 ? ' day' : ' days');
        document.getElementById('driver-fee').textContent = money(driverFee);
        document.getElementById('rental-subtotal').textContent = money(subtotal);
        document.getElementById('estimated-total').textContent = money(subtotal + 5000);
        document.getElementById('request-dates').textContent = pickup.value + ' → ' + returnDate.value;
        stepLinks[1].classList.remove('active');
        stepLinks[1].classList.add('completed');
        stepLinks[2].classList.add('active');
        stepLinks[3].classList.remove('completed');
      }

      function resetAvailability() {
        available = false;
        status.className = 'availability-status';
        status.textContent = 'Select your dates, then choose “Check Availability”.';
        stepLinks[2].classList.remove('completed');
        stepLinks[3].classList.remove('completed');
        stepLinks[4].classList.remove('active');
        updateCharges();
      }

      pickup.addEventListener('change', resetAvailability);
      returnDate.addEventListener('change', resetAvailability);
      rentalTypeInputs.forEach(function (input) {
        input.addEventListener('change', updateCharges);
      });
      document.getElementById('location').addEventListener('change', function () {
        document.getElementById('request-location').textContent = this.value || '[ BRANCH ]';
      });

      checkButton.addEventListener('click', async function () {
        updateCharges();
        var datePattern = /^\d{4}-\d{2}-\d{2}$/;
        if (!datePattern.test(pickup.value) || !datePattern.test(returnDate.value)
          || new Date(returnDate.value + 'T00:00:00') <= new Date(pickup.value + 'T00:00:00')) {
          status.className = 'availability-status unavailable';
          status.textContent = 'Choose a valid return date after the pick-up date.';
          pickup.reportValidity();
          returnDate.reportValidity();
          return;
        }

        available = false;
        stepLinks[2].classList.remove('completed');
        stepLinks[3].classList.remove('completed');
        stepLinks[4].classList.remove('active');
        checkButton.disabled = true;
        status.textContent = 'Checking availability...';
        try {
          var response = await fetch(window.location.pathname + window.location.search, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
            body: new URLSearchParams({
              action: 'check_availability',
              csrf_token: <?php echo json_encode($_SESSION['csrf_token'] ?? ''); ?>,
              vehicle_id: <?php echo json_encode($_GET['id'] ?? ''); ?>,
              pickup: pickup.value,
              return: returnDate.value
            })
          });
          var result = await response.json();
          if (!response.ok) throw new Error(result.error || 'Could not check availability right now.');
          available = result.available === true;
          status.className = 'availability-status ' + (available ? 'available' : 'unavailable');
          status.textContent = available
            ? 'This vehicle is available for your selected dates.'
            : (result.error || 'This vehicle is not available for those dates.');
          if (available) {
            stepLinks[2].classList.remove('active');
            stepLinks[2].classList.add('completed');
            stepLinks[3].classList.add('completed');
            stepLinks[4].classList.add('active');
          } else {
            stepLinks[2].classList.add('active');
            stepLinks[2].classList.remove('completed');
            stepLinks[3].classList.remove('completed');
            stepLinks[4].classList.remove('active');
          }
        } catch (error) {
          status.className = 'availability-status unavailable';
          status.textContent = error.message || 'Could not check availability right now. Please try again.';
        } finally {
          checkButton.disabled = false;
        }
      });

      form.addEventListener('submit', function (event) {
        if (!available) {
          event.preventDefault();
          status.className = 'availability-status unavailable';
          status.textContent = 'Check availability for your selected dates before sending the request.';
          checkButton.focus();
        }
      });

      updateCharges();
    })();
  </script>
</body>
</html>
