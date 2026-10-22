<?php
require_once __DIR__ . '/site_init.php';

if (!empty($_SESSION['admin_user']['id'])) {
    header('Location: admin_panel.php', true, 303);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Sign in to the Autoride administrator dashboard.">
  <title>Administrator Sign In | Autoride</title>
  <style>
    :root { --ink: #111827; --text: #374151; --muted: #6b7280; --line: #d1d5db; --border: #e5e7eb; --wash: #f8f9fb; --white: #fff; }
    * { box-sizing: border-box; }
    body { zoom: 1.2; min-height: 100vh; margin: 0; display: flex; flex-direction: column; color: var(--text); background: var(--wash); font: 14px/1.5 Arial, Helvetica, sans-serif; }
    a { color: inherit; text-decoration: none; }
    a:hover { text-decoration: underline; text-underline-offset: 3px; }
    :focus-visible { outline: 2px solid var(--ink); outline-offset: 3px; }
    .site-header { min-height: 54px; display: flex; align-items: center; justify-content: space-between; padding: 0 max(24px, calc((100% - 1140px) / 2)); border-bottom: 1px solid var(--border); background: var(--white); }
    .brand { color: var(--ink); font-size: 15px; font-weight: 800; }
    .site-header nav { display: flex; gap: 20px; font-size: 11px; }
    main { flex: 1; display: grid; place-items: center; padding: 32px 18px; }
    .login-panel { width: min(420px, 100%); padding: 28px; border: 1px solid var(--border); border-radius: 4px; background: var(--white); }
    .eyebrow { margin: 0 0 5px; color: var(--muted); font-size: 10px; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; }
    h1 { margin: 0; color: var(--ink); font-size: 23px; }
    .intro { margin: 6px 0 20px; color: var(--muted); font-size: 12px; }
    .error { margin: 0 0 14px; padding: 9px 11px; border: 1px solid #c43d3d; background: #fff5f5; color: #8b1d1d; font-size: 12px; }
    label { display: block; margin: 0 0 12px; color: var(--ink); font-size: 11px; font-weight: 700; }
    input { width: 100%; height: 38px; margin-top: 5px; padding: 0 10px; border: 1px solid var(--line); border-radius: 3px; color: var(--text); font: inherit; }
    button { width: 100%; min-height: 38px; margin-top: 4px; border: 1px solid var(--ink); border-radius: 3px; background: var(--ink); color: var(--white); font: inherit; font-size: 12px; font-weight: 700; cursor: pointer; }
    button:hover { background: #303847; }
    .back-link { display: block; margin-top: 16px; color: var(--muted); font-size: 11px; text-align: center; }
    @media (max-width: 480px) { .site-header { padding: 12px 16px; } .login-panel { padding: 22px 18px; } }
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
.login-panel {
  width: min(500px, 100%);
  padding: 40px;
  border-radius: 20px !important;
  background: #fff;
  box-shadow: 0 18px 48px #11121412;
}
.login-panel .eyebrow {
  margin-bottom: 8px;
  color: #5f6067;
  font-size: 12px;
  letter-spacing: 1.2px;
}
.login-panel h1 {
  font-size: clamp(28px, 4vw, 34px);
  line-height: 1.2;
  letter-spacing: -.7px;
}
.login-panel .intro {
  margin: 10px 0 26px;
  color: #55565d;
  font-size: 16px;
  line-height: 1.55;
}
.login-panel .error {
  margin-bottom: 18px;
  padding: 12px 14px;
  border-color: #d99090;
  border-radius: 10px;
  background: #fff5f5;
  color: #7f1717;
  font-size: 14px;
}
.login-panel label {
  margin-bottom: 18px;
  color: #292a2d;
  font-size: 14px;
  font-weight: 750;
  line-height: 1.45;
}
.login-panel input:not([type="hidden"]) {
  height: 52px;
  margin-top: 8px;
  padding: 0 14px;
  border: 1px solid #c9cbd1;
  border-radius: 10px;
  background: #f7f7f8;
  color: #111214;
  font-size: 16px;
}
.login-panel input:not([type="hidden"]):focus {
  border-color: #a78500 !important;
  background: #fff;
  outline: 2px solid #f3c92866;
  outline-offset: 1px;
}
.login-panel button {
  min-height: 52px;
  margin-top: 4px;
  border-radius: 999px;
  font-size: 15px;
  font-weight: 850;
}
.login-panel .back-link {
  margin-top: 20px;
  color: #55565d;
  font-size: 14px;
}
@media (max-width: 480px) {
  .login-panel { padding: 28px 22px; }
  .login-panel .intro { margin-bottom: 22px; }
}
  </style>
</head>
<body>
  <header class="site-header">
    <a class="brand" href="index.php">AUTORIDE</a>
    <nav aria-label="Main navigation"><a href="index.php">Home</a></nav>
  </header>
  <main>
    <section class="login-panel" aria-labelledby="login-title">
      <p class="eyebrow">Admin Console</p>
      <h1 id="login-title">Administrator Sign In</h1>
      <p class="intro">Use an active Autoride administrator account to continue.</p>
      <?php if ($loginError !== ''): ?><p class="error" role="alert"><?php echo e($loginError); ?></p><?php endif; ?>
      <form method="post" action="admin_login.php" autocomplete="on">
        <input type="hidden" name="action" value="admin_login">
        <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
        <label for="username">Username or email<input id="username" name="username" type="text" autocomplete="username" maxlength="254" required value="<?php echo e($username); ?>"></label>
        <label for="password">Password<input id="password" name="password" type="password" autocomplete="current-password" required></label>
        <button type="submit">Sign In</button>
      </form>
    </section>
  </main>
</body>
</html>
