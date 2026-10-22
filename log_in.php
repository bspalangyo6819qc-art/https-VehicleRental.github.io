<?php
require_once __DIR__ . '/site_init.php';

$showSignup = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'
    && ($_POST['action'] ?? '') === 'register';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Autoride | Login</title>
<style>
  :root {
    --ink: #111827;
    --text: #374151;
    --muted: #6b7280;
    --line: #d1d5db;
    --soft: #e5e7eb;
    --bg: #f9fafb;
    --white: #ffffff;
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    zoom: 1.2;
    font-family: Inter, "Helvetica Neue", Arial, sans-serif;
    font-size: 11px;
    color: var(--text);
    background: var(--bg);
    line-height: 1.4;
  }
  a { color: inherit; text-decoration: none; }
  :focus-visible { outline: 2px solid var(--ink); outline-offset: 2px; }

  /* Header */
  .site-header {
    background: var(--white);
    border-bottom: 1px solid var(--soft);
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 37px;
  }
  .logo { font-weight: 800; font-size: 12px; letter-spacing: .2px; color: var(--ink); }
  .nav { display: flex; align-items: center; gap: 18px; }
  .nav a { font-size: 9px; color: var(--text); }
  .nav a.active { font-weight: 700; color: var(--ink); text-decoration: underline; text-underline-offset: 3px; }
  .nav .search-icon { width: 11px; height: 11px; border: 1.5px solid var(--ink); border-radius: 50%; position: relative; margin-left: 4px; }
  .nav .search-icon::after {
    content: ""; position: absolute; width: 5px; height: 1.5px; background: var(--ink);
    right: -4px; bottom: -1px; transform: rotate(45deg);
  }

  /* Main */
  main { padding: 39px 20px 38px; display: flex; justify-content: center; }
  .card {
    width: 391px;
    max-width: 100%;
    background: var(--white);
    border: 1px solid var(--soft);
    border-radius: 4px;
    overflow: hidden;
  }
  .tabs { display: flex; }
  .tab {
    border: 0; font-family: inherit;
    flex: 1;
    text-align: center;
    padding: 8px 0;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .3px;
    color: var(--muted);
    background: var(--bg);
    cursor: pointer;
  }
  .tab.active { background: var(--white); color: var(--ink); }

  .panels { padding: 20px 19px; }
  .panel h2 { font-size: 10px; font-weight: 700; color: var(--ink); margin-bottom: 9px; }
  .panel.signup { padding: 0; }

  label.field { display: block; margin-bottom: 7px; }
  label.field span { display: block; font-size: 7.5px; font-weight: 700; color: var(--ink); margin-bottom: 3px; }
  input[type="text"], input[type="email"], input[type="tel"], input[type="password"] {
    width: 100%;
    height: 20px;
    padding: 0 6px;
    font: inherit;
    font-size: 8px;
    color: var(--ink);
    background: var(--white);
    border: 1px solid var(--line);
    border-radius: 2px;
  }
  input::placeholder { color: #9ca3af; }

  .row { display: flex; justify-content: space-between; align-items: center; font-size: 7.5px; margin: 2px 0 8px; }
  .check { display: flex; align-items: center; gap: 5px; font-size: 7.5px; color: var(--text); }
  .check input { width: 8px; height: 8px; accent-color: var(--ink); }
  .underline { text-decoration: underline; text-underline-offset: 2px; font-weight: 700; color: var(--ink); }
  .row .underline { font-weight: 500; }

  .btn {
    display: block; width: 100%; height: 20px;
    font: inherit; font-size: 7.5px; font-weight: 700; letter-spacing: .3px;
    border-radius: 2px; cursor: pointer;
  }
  .btn-dark { background: var(--ink); color: var(--white); border: 1px solid var(--ink); }
  .btn-light {
    background: var(--white); color: var(--text); border: 1px solid var(--line);
    font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 5px;
  }
  .g-icon {
    width: 8px; height: 8px; border: 1px solid var(--ink); border-radius: 50%;
    position: relative; display: inline-block;
  }
  .g-icon::before, .g-icon::after {
    content: ""; position: absolute; left: 50%; top: 50%; width: 4px; height: 1px; background: var(--ink);
  }
  .g-icon::before { transform: translate(-50%, -50%) rotate(45deg); }
  .g-icon::after { transform: translate(-50%, -50%) rotate(-45deg); }

  .divider { display: flex; align-items: center; gap: 6px; margin: 7px 0; font-size: 6.5px; color: var(--muted); }
  .divider::before, .divider::after { content: ""; flex: 1; height: 1px; background: var(--soft); }
  .switch { text-align: center; font-size: 7.5px; margin-top: 7px; color: var(--text); }

  /* Messages and errors */
  .msg { display: none; font-size: 8px; padding: 6px 8px; border-radius: 2px; margin-bottom: 9px; border: 1px solid var(--line); }
  .msg.show { display: block; }
  .msg.error { background: #f9fafb; color: #111827; border-color: #111827; }
  .msg.ok { background: #111827; color: #fff; border-color: #111827; }
  .field-error { display: none; font-size: 7px; color: #111827; font-weight: 600; margin-top: 2px; }
  .field-error.show { display: block; }
  input.invalid { border-color: #111827; border-width: 2px; }
  .nav .user { font-size: 9px; font-weight: 700; color: var(--ink); }
  .nav button.logout { font: inherit; font-size: 9px; background: none; border: 0; text-decoration: underline; cursor: pointer; color: var(--ink); }

  /* Footer */
  .site-footer { background: #f3f4f6; border-top: 1px solid var(--soft); padding: 28px 37px 14px; margin-top: 0; }
  .footer-top { display: grid; grid-template-columns: 1.6fr 1fr 1fr 1fr; gap: 20px; }
  .site-footer h3 { font-size: 8px; font-weight: 800; color: var(--ink); margin-bottom: 9px; }
  .site-footer .logo { font-size: 10px; margin-bottom: 8px; display: block; }
  .site-footer p { font-size: 7.5px; color: var(--text); max-width: 150px; }
  .site-footer ul { list-style: none; }
  .site-footer li { margin-bottom: 6px; font-size: 7.5px; color: var(--text); }
  .site-footer li a:hover { text-decoration: underline; }
  .footer-bottom {
    display: flex; justify-content: space-between;
    border-top: 1px solid var(--line); margin-top: 18px; padding-top: 8px;
    font-size: 6.5px; color: var(--muted);
  }

  /* Larger screens: scale up the compact wireframe sizing */
  @media (min-width: 1000px) {
    body { font-size: 12px; }
    .site-header { height: 55px; padding: 0 60px; }
    .logo { font-size: 18px; }
    .nav { gap: 28px; }
    .nav a { font-size: 12px; }
    .nav .search-icon { width: 14px; height: 14px; }
    .nav .user, .nav button.logout { font-size: 12px; }
    main { padding: 64px 20px 56px; }
    .card { width: 100%; max-width: 560px; }
    .tab { padding: 18px 0; font-size: 13px; }
    .panels { padding: 40px 28px 28px; }
    .panel h2 { font-size: 14px; margin-bottom: 18px; }
    .panel.signup { padding: 0; }
    label.field { margin-bottom: 14px; }
    label.field span { font-size: 10px; margin-bottom: 5px; }
    input[type="text"], input[type="email"], input[type="tel"], input[type="password"] { height: 33px; font-size: 11px; padding: 0 10px; }
    .row, .check { font-size: 10.5px; }
    .check input { width: 12px; height: 12px; }
    .btn { height: 33px; font-size: 10.5px; }
    .g-icon { width: 11px; height: 11px; }
    .g-icon::before, .g-icon::after { width: 6px; }
    .divider { font-size: 9px; margin: 14px 0; }
    .switch { font-size: 10.5px; margin-top: 12px; }
    .msg { font-size: 11px; padding: 9px 12px; }
    .field-error { font-size: 9.5px; }
    .site-footer { padding: 40px 60px 20px; margin-top: 44px; }
    .site-footer h3 { font-size: 11px; margin-bottom: 14px; }
    .site-footer .logo { font-size: 15px; }
    .site-footer p, .site-footer li { font-size: 10.5px; }
    .site-footer p { max-width: 230px; }
    .site-footer li { margin-bottom: 10px; }
    .footer-bottom { font-size: 9.5px; margin-top: 30px; padding-top: 14px; }
  }

  /* Mobile layout */
  @media (max-width: 640px) {
    .site-header { padding: 0 16px; }
    .nav { gap: 10px; }
    .panels { padding: 20px; }
    .footer-top { grid-template-columns: 1fr 1fr; }
    .site-footer { padding: 28px 16px 14px; }
    .footer-bottom { flex-direction: column; gap: 4px; }
  }
  .site-header { height: 52px; min-height: 52px; padding: 0 max(24px, calc((100% - 1140px) / 2)); gap: 18px; }
  .logo { font-size: 14px; letter-spacing: .2px; }
  .nav { gap: 24px; }
  .nav a, .nav .user, .nav button.logout { font-size: 11px; white-space: nowrap; }
  .nav a:hover, .nav a.active { color: var(--ink); text-decoration: underline; text-underline-offset: 4px; }
  .nav .search-icon { width: 11px; height: 11px; margin-left: 3px; }
  .nav .search-icon::after { width: 5px; height: 1.5px; right: -4px; bottom: -2px; }
  @media (max-width: 760px) {
    .site-header { height: auto; min-height: 52px; padding: 8px 16px; flex-wrap: wrap; }
    .logo { font-size: 14px; }
    .nav { gap: 12px 18px; flex-wrap: wrap; }
    .nav a, .nav .user, .nav button.logout { font-size: 11px; }
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
</head>
<body class="login-page">

<header class="site-header">
  <a href="index.php" class="logo">AUTORIDE</a>
  <nav class="nav" aria-label="Main navigation">
    <a href="index.php">Home</a>
    <a href="brows_vehicle.php">Browse Vehicle</a>
    <a href="rentflow.php">Rent</a>
    <a href="about_us.php">About Us</a>
    <a href="log_in.php" class="active" aria-current="page" id="nav-auth"<?php echo !empty($_SESSION['customer_user']['id']) ? ' style="display:none"' : ''; ?>>Login/Register</a>
    <?php if (!empty($_SESSION['customer_user']['id'])): ?>
      <span class="user">Hi, <?php echo e(explode(' ', (string) ($_SESSION['customer_user']['name'] ?? 'Customer'))[0]); ?></span>
      <form method="post" style="display:contents">
        <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
        <input type="hidden" name="action" value="logout">
        <button class="logout" type="submit">Logout</button>
      </form>
    <?php endif; ?>
    <?php if (!empty($_SESSION['customer_user']['id'])): ?>
      <a href="user_profile.php">Profile</a>
    <?php endif; ?>
    <a class="search-icon" href="brows_vehicle.php" aria-label="Search vehicles" title="Search vehicles"></a>
  </nav>
</header>

<main>
  <section class="card" aria-label="Account access">
    <div class="tabs">
      <button type="button" class="tab<?php echo $showSignup ? '' : ' active'; ?>" id="tab-login" aria-selected="<?php echo $showSignup ? 'false' : 'true'; ?>">LOGIN</button>
      <button type="button" class="tab<?php echo $showSignup ? ' active' : ''; ?>" id="tab-signup" aria-selected="<?php echo $showSignup ? 'true' : 'false'; ?>">SIGN UP</button>
    </div>

    <div class="panels">
      <!-- LOGIN -->
      <form class="panel login" id="login-form" method="post" novalidate>
        <h2>Welcome back.</h2>
        <p class="login-intro">Log in to manage your profile and rental requests.</p>
        <div class="msg<?php echo $loginError !== '' ? ' show error' : ''; ?>" id="login-msg" role="alert"><?php echo e($loginError); ?></div>
        <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
        <input type="hidden" name="action" value="login">

        <label class="field">
          <span>Email</span>
          <input type="text" id="login-email" name="email" placeholder="Enter your email" autocomplete="username" required value="<?php echo e($username); ?>">
        </label>

        <label class="field">
          <span>Password</span>
          <input type="password" id="login-password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
        </label>

        <div class="row">
          <label class="check"><input type="checkbox" id="remember" name="remember" value="1"> Remember me</label>
          <a href="#" class="underline" id="forgot">Forgot Password?</a>
        </div>

        <button class="btn btn-dark" type="submit">LOGIN <span aria-hidden="true">→</span></button>
      </form>

      <!-- SIGN UP -->
      <form class="panel signup" id="signup-form" method="post" hidden novalidate>
        <h2>New Account Signup</h2>
        <div class="msg<?php echo $signupError !== '' ? ' show error' : ''; ?>" id="signup-msg" role="alert"><?php echo e($signupError); ?></div>
        <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
        <input type="hidden" name="action" value="register">

        <label class="field">
          <span>Full Name</span>
          <input id="su-name" name="name" autocomplete="name" type="text" placeholder="Juan dela Cruz" required>
          <small class="field-error" id="su-name-err"></small>
        </label>

        <label class="field">
          <span>Email Address</span>
          <input id="su-email" name="email" autocomplete="email" type="email" placeholder="juan@domain.com" required>
          <small class="field-error" id="su-email-err"></small>
        </label>

        <label class="field">
          <span>Phone Number</span>
          <input id="su-phone" name="phone" autocomplete="tel" type="tel" placeholder="+63 9XX XXX XXXX" required>
          <small class="field-error" id="su-phone-err"></small>
        </label>

        <label class="field">
          <span>Password</span>
          <input id="su-pass" name="password" autocomplete="new-password" type="password" placeholder="Min. 8 characters" minlength="8" required>
          <small class="field-error" id="su-pass-err"></small>
        </label>

        <label class="field">
          <span>Confirm Password</span>
          <input id="su-confirm" name="confirm_password" autocomplete="new-password" type="password" placeholder="Repeat password" minlength="8" required>
          <small class="field-error" id="su-confirm-err"></small>
        </label>

        <label class="check" style="margin-bottom:10px; align-items:flex-start;">
          <input type="checkbox" id="su-terms" name="terms" value="1" required>
          <span>I agree to the <a href="terms_of_policy.php" class="underline">Terms of Service</a> and <a href="privacy_policy.php" class="underline">Privacy Policy</a></span>
        </label>

        <button class="btn btn-dark" type="submit">SIGN UP</button>

        <div class="divider">OR</div>

        <button class="btn btn-light google" type="button"><span class="g-icon"></span> Sign Up with Google</button>

        <p class="switch">Already have an account? <a href="#" class="underline" id="to-login">Login</a></p>
      </form>
    </div>
  </section>
</main>

<footer class="site-footer">
  <div class="footer-top">
    <div>
      <a href="#" class="logo">AUTORIDE</a>
      <p>Your trusted partner for affordable vehicle rentals, easy bookings, and reliable rides.</p>
    </div>
    <div>
      <h3>QUICK LINKS</h3>
      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">Browse Vehicle</a></li>
        <li><a href="about_us.php">About Us</a></li>
        <li><a href="rentflow.php">Rent</a></li>
        <li><a href="log_in.php">Login</a></li>
      </ul>
    </div>
    <div>
      <h3>SOCIAL MEDIA</h3>
      <ul>
        <li><a href="#">Facebook</a></li>
        <li><a href="#">Instagram</a></li>
        <li><a href="#">TikTok</a></li>
      </ul>
    </div>
    <div>
      <h3>POLICIES</h3>
      <ul>
        <li><a href="privacy_policy.php">Privacy Policy</a></li>
        <li><a href="terms_of_policy.php">Terms of Policy</a></li>
        <li><a href="faqs.php">FAQs</a></li>
        <li><a href="terms_and_conditions.php">Terms and Conditions</a></li>
        <li><a href="return_policy.php">Return Policy</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
      <span>© 2026 Autoride. All rights reserved.</span>
      <span><a href="terms_of_policy.php">TERMS OF SERVICE</a> &nbsp; <a href="privacy_policy.php">PRIVACY POLICY</a></span>
    </div>
</footer>

<script>
(function () {
  function $(id) { return document.getElementById(id); }
  function showMsg(el, text, type) { el.textContent = text; el.className = 'msg show ' + type; }
  function hideMsg(el) { el.className = 'msg'; el.textContent = ''; }
  function setErr(id, text) {
    var input = $(id), err = $(id + '-err');
    if (text) { input.classList.add('invalid'); err.textContent = text; err.classList.add('show'); }
    else { input.classList.remove('invalid'); err.textContent = ''; err.classList.remove('show'); }
    return !text;
  }
  function setActiveTab(which) {
    $('tab-login').classList.toggle('active', which === 'login');
    $('tab-signup').classList.toggle('active', which === 'signup');
    $('tab-login').setAttribute('aria-selected', which === 'login' ? 'true' : 'false');
    $('tab-signup').setAttribute('aria-selected', which === 'signup' ? 'true' : 'false');
    $('login-form').hidden = which !== 'login';
    $('signup-form').hidden = which !== 'signup';
    document.title = 'Autoride | ' + (which === 'login' ? 'Login' : 'Sign Up');
  }
  function goTo(which) {
    setActiveTab(which);
    var form = $(which === 'login' ? 'login-form' : 'signup-form');
    form.querySelector('input').focus({ preventScroll: true });
  }

  var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
  var PH_PHONE_RE = /^(\+63|0)9\d{9}$/;   // +639XXXXXXXXX or 09XXXXXXXXX

  $('signup-form').addEventListener('submit', function (event) {
    event.preventDefault();
    var msg = $('signup-msg'); hideMsg(msg);
    var name = $('su-name').value.trim();
    var email = $('su-email').value.trim().toLowerCase();
    var phone = $('su-phone').value.replace(/[\s-]/g, '');
    var password = $('su-pass').value;
    var confirm = $('su-confirm').value;
    var valid = true;

    valid = setErr('su-name', name.length < 2 ? 'Enter your full name.' : '') && valid;
    valid = setErr('su-email', !EMAIL_RE.test(email) ? 'Enter a valid email address.' : '') && valid;
    valid = setErr('su-phone', !PH_PHONE_RE.test(phone) ? 'Use a valid PH mobile number.' : '') && valid;
    valid = setErr('su-pass', password.length < 8 ? 'Password must be at least 8 characters.' : '') && valid;
    valid = setErr('su-confirm', confirm !== password ? 'Passwords do not match.' : '') && valid;
    if (!$('su-terms').checked) {
      showMsg(msg, 'You must agree to the Terms of Service and Privacy Policy.', 'error');
      valid = false;
    }
    if (valid) HTMLFormElement.prototype.submit.call(this);
  });

  $('login-form').addEventListener('submit', function (event) {
    event.preventDefault();
    var identifier = $('login-email').value.trim();
    if (!identifier || !$('login-password').value) {
      showMsg($('login-msg'), 'Enter your email and password.', 'error');
      return;
    }
    HTMLFormElement.prototype.submit.call(this);
  });

  $('tab-login').addEventListener('click', function () { goTo('login'); });
  $('tab-signup').addEventListener('click', function () { goTo('signup'); });
  $('to-login').addEventListener('click', function (event) { event.preventDefault(); goTo('login'); });
  $('forgot').addEventListener('click', function (event) {
    event.preventDefault();
    showMsg($('login-msg'), 'Password reset needs an email service. Connect one to enable this.', 'error');
  });
  Array.prototype.forEach.call(document.querySelectorAll('.google'), function (button) {
    button.addEventListener('click', function () {
      var message = $(button.closest('form').id === 'login-form' ? 'login-msg' : 'signup-msg');
      showMsg(message, 'Google sign-in needs a Google OAuth client ID and a backend.', 'error');
    });
  });

  ['su-name', 'su-email', 'su-phone', 'su-pass', 'su-confirm'].forEach(function (id) {
    $(id).addEventListener('input', function () { setErr(id, ''); });
  });

  setActiveTab('<?php echo $showSignup ? 'signup' : 'login'; ?>');
})();
</script>

</body>
