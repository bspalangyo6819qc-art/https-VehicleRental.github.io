<?php
require_once __DIR__ . '/site_init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Learn about AutoRide, our mission, values, and contact information.">
  <title>About Us | Autoride</title>
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
    body { zoom: 1.2; min-height: 100vh; margin: 0; display: flex; flex-direction: column; color: var(--text); background: var(--white); font: 12px/1.45 Arial, Helvetica, sans-serif; }
    a { color: inherit; text-decoration: none; }
    :focus-visible { outline: 2px solid var(--ink); outline-offset: 2px; }

    .site-header { min-height: 48px; display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 0 max(24px, calc((100% - 1140px) / 2)); border-bottom: 1px solid var(--border); background: var(--white); }
    .brand { color: var(--ink); font-size: 14px; font-weight: 800; letter-spacing: .2px; }
    .nav { display: flex; align-items: center; gap: 22px; }
    .nav a { color: var(--text); font-size: 10px; white-space: nowrap; }
    .nav a:hover, .nav a[aria-current="page"] { color: var(--ink); text-decoration: underline; text-underline-offset: 4px; }
    .nav a[aria-current="page"] { font-weight: 700; }
    .search-mark { width: 10px; height: 10px; position: relative; border: 1.4px solid var(--ink); border-radius: 50%; }
    .search-mark::after { content: ""; position: absolute; right: -4px; bottom: -2px; width: 5px; height: 1.4px; background: var(--ink); transform: rotate(45deg); }

    main { flex: 1; }
    .page-content { width: min(1140px, calc(100% - 48px)); margin: 0 auto; padding: 34px 0 40px; }
    .values { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; margin-bottom: 40px; }
    .value-card { min-height: 145px; padding: 18px; border: 1px solid var(--border); border-radius: 5px; background: var(--white); }
    .value-heading { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
    .value-icon, .contact-icon { display: grid; flex: 0 0 auto; place-items: center; border: 1px solid var(--border); border-radius: 50%; background: var(--wash); color: var(--ink); font-weight: 700; }
    .value-icon { width: 32px; height: 32px; font-size: 12px; }
    .value-card h2 { margin: 0; color: var(--ink); font-size: 14px; }
    .value-card p { margin: 0; color: var(--muted); font-size: 10px; }

    .contact h2 { margin: 0; color: var(--ink); font-size: 18px; }
    .contact-intro { margin: 4px 0 14px; padding-bottom: 8px; border-bottom: 1px solid var(--border); color: var(--muted); font-size: 10px; }
    .contact-list { display: grid; gap: 12px; }
    .contact-item { display: flex; align-items: center; gap: 12px; }
    .contact-icon { width: 30px; height: 30px; border-radius: 4px; font-size: 12px; }
    .contact-item strong { display: block; color: var(--ink); font-size: 10px; }
    .contact-item span { display: block; color: var(--muted); font-size: 9px; }

    .footer { padding: 26px max(24px, calc((100% - 1140px) / 2)) 10px; background: #f1f2f4; }
    .footer-grid { display: grid; grid-template-columns: 1.6fr 1fr 1fr 1fr; gap: 28px; }
    .footer-brand { color: var(--ink); font-size: 13px; font-weight: 800; }
    .footer h2 { margin: 0 0 8px; color: var(--ink); font-size: 10px; }
    .footer p, .footer li { color: var(--muted); font-size: 9px; }
    .footer p { max-width: 210px; margin: 8px 0 0; }
    .footer ul { margin: 0; padding: 0; list-style: none; }
    .footer li { margin-bottom: 5px; }
    .footer a:hover { color: var(--ink); text-decoration: underline; }
    .footer-bottom { display: flex; justify-content: space-between; gap: 12px; margin-top: 18px; padding-top: 9px; border-top: 1px solid var(--line); color: var(--muted); font-size: 8px; }

    @media (max-width: 760px) {
      .site-header { padding: 10px 16px; flex-wrap: wrap; }
      .nav { gap: 12px; flex-wrap: wrap; }
      .nav a { font-size: 9px; }
      .page-content { width: min(100% - 32px, 600px); padding: 24px 0 30px; }
      .values { grid-template-columns: 1fr; gap: 10px; margin-bottom: 30px; }
      .value-card { min-height: auto; }
      .footer { padding: 22px 16px 10px; }
      .footer-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
    }
    @media (max-width: 420px) {
      .nav { gap: 9px; }
      .footer-bottom { flex-direction: column; }
    }
    .site-header { min-height: 52px; padding: 0 max(24px, calc((100% - 1140px) / 2)); }
    .brand { font-size: 14px; letter-spacing: .2px; }
    .nav { gap: 24px; }
    .nav a { font-size: 11px; }
    .nav-logout { padding: 0; border: 0; background: none; color: var(--text); font: inherit; font-size: 11px; cursor: pointer; }
    .nav-logout:hover { color: var(--ink); text-decoration: underline; text-underline-offset: 4px; }
    @media (max-width: 760px) {
      .site-header { padding: 8px 16px; flex-wrap: wrap; }
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
.site-header > nav a.search-mark:hover,
.header > nav a.search-mark:hover {
  text-decoration: none;
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
.about-intro {
 width: min(1250px, calc(100% - 48px));
 min-height: 460px;
 margin: 0 auto;
 display: grid;
 grid-template-columns: minmax(0, .92fr) minmax(0, 1.08fr);
 align-items: center;
 gap: 52px;
 padding: 42px 0;
}
.about-intro-copy {
 max-width: 520px;
}
.about-intro .about-eyebrow {
 justify-content: flex-start;
 margin-bottom: 18px;
}
.about-intro h1 {
 max-width: 530px;
 margin: 0 0 16px;
 color: var(--ink);
 font-size: clamp(38px, 4vw, 52px);
 font-weight: 850;
 line-height: 1.04;
 letter-spacing: -1.8px;
}
.about-intro p {
 max-width: 510px;
 margin: 0 0 14px;
 color: #6b7280;
 font-size: 13px;
 line-height: 1.5;
}
.about-intro .button-dark {
 min-height: 44px;
 margin-top: 8px;
 padding: 0 22px;
 border: 1px solid var(--yellow) !important;
 border-radius: 99px;
 background: var(--yellow) !important;
 color: var(--ink) !important;
 font-size: 10px;
 font-weight: 900 !important;
 letter-spacing: .6px;
 text-transform: uppercase;
}
.about-intro .button-dark:hover {
 background: #e7bd19 !important;
}
.about-intro-image {
 min-height: 390px;
 border-radius: 18px;
 background: #e9e9eb !important;
}
.about-overview {
 width: min(1216px, calc(100% - 48px));
 margin: 0 auto;
 padding: 46px 0 0;
}
.about-eyebrow {
  display: flex;
  align-items: center;
  gap: 9px;
  justify-content: center;
  margin-bottom: 12px;
  color: #4b5563;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: 1.8px;
}
.about-eyebrow::before {
  width: 24px;
  height: 2px;
  background: var(--yellow);
  content: "";
}
.about-overview-heading {
  margin: 0;
  color: var(--ink);
  font-size: clamp(30px, 3vw, 42px);
  font-weight: 850;
  line-height: 1.12;
  text-align: center;
}
.about-overview-intro {
  max-width: 560px;
  margin: 12px auto 28px;
  color: #6b7280;
  font-size: 13px;
  line-height: 1.5;
  text-align: center;
}
.overview-feature {
  min-height: 330px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  overflow: hidden;
  border-radius: 18px;
  background: #e9e9eb;
}
.overview-mission {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: center;
  padding: 42px 50px;
  background: #111214;
  color: #fff;
}
.overview-mission .about-eyebrow {
  justify-content: flex-start;
  margin-bottom: 16px;
  color: #aeb0b6;
}
.overview-mission h2 {
  max-width: 430px;
  margin: 0 0 16px;
  color: #fff;
  font-size: clamp(30px, 3.2vw, 46px);
  font-weight: 850;
  line-height: 1.08;
  letter-spacing: -1.3px;
}
.overview-mission p {
  max-width: 440px;
  margin: 0;
  color: #e2e2e6;
  font-size: 13px;
  line-height: 1.6;
}
.overview-image {
  min-height: 330px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: center;
  padding: 42px 50px;
  background: #e9e9eb !important;
}
.overview-image .about-eyebrow {
  justify-content: flex-start;
  margin-bottom: 16px;
}
.overview-image h2 {
  max-width: 430px;
  margin: 0 0 16px;
  color: var(--ink);
  font-size: clamp(30px, 3.2vw, 46px);
  font-weight: 850;
  line-height: 1.08;
  letter-spacing: -1.3px;
}
.overview-image p {
  max-width: 440px;
  margin: 0;
  color: #4b5563;
  font-size: 13px;
  line-height: 1.6;
}
.about-values {
  width: min(1216px, calc(100% - 48px));
  margin: 0 auto;
  padding: 54px 0 20px;
}
.about-values-heading {
  margin-bottom: 26px;
  text-align: center;
}
.about-values-heading .about-eyebrow {
  margin-bottom: 10px;
}
.about-values-heading h2 {
  margin: 0;
  color: var(--ink);
  font-size: clamp(28px, 3vw, 40px);
  font-weight: 850;
  line-height: 1.1;
}
.about-values-heading p {
  margin: 10px 0 0;
  color: #6b7280;
  font-size: 13px;
}
.about-values .values {
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
  margin-bottom: 0;
}
.about-values .value-card {
  min-height: 180px;
  padding: 22px;
  border-radius: 14px;
  box-shadow: none;
}
.value-number {
  width: 42px;
  height: 42px;
  display: grid;
  place-items: center;
  margin-bottom: 24px;
  border-radius: 50%;
  background: var(--yellow);
  color: var(--ink);
  font-size: 16px;
  font-weight: 900;
}
.about-values .value-card h3 {
  margin: 0 0 8px;
  color: var(--ink);
  font-size: 14px;
}
.about-values .value-card p {
  margin: 0;
  color: #6b7280;
  font-size: 12px;
  line-height: 1.5;
}
.page-content {
  width: min(1216px, calc(100% - 48px));
  padding: 36px 0 56px;
}
.contact {
  color: var(--ink);
}
.contact-heading {
  margin-bottom: 28px;
}
.contact-heading .about-eyebrow {
  justify-content: flex-start;
  margin-bottom: 12px;
}
.contact-heading h2 {
  margin: 0;
  color: var(--ink);
  font-size: clamp(32px, 3vw, 42px);
  font-weight: 850;
  line-height: 1.1;
}
.contact-intro {
  margin: 10px 0 0;
  color: #6b7280;
  font-size: 13px;
}
.contact-list {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 16px;
  margin-bottom: 20px;
}
.contact-item {
  min-height: 146px;
  display: block;
  padding: 22px;
  border: 1px solid var(--border);
  border-radius: 14px;
  background: #fff;
}
.contact-icon {
  width: 40px;
  height: 40px;
  margin-bottom: 16px;
  border: 0;
  border-radius: 50%;
  background: var(--yellow);
  color: var(--ink);
  font-size: 16px;
}
.contact-item strong {
  display: block;
  margin-bottom: 5px;
  color: var(--ink);
  font-size: 14px;
}
.contact-item span,
.contact-item a {
  display: block;
  color: #6b7280;
  font-size: 12px;
  line-height: 1.5;
  overflow-wrap: anywhere;
}
.contact-item a:hover {
  color: var(--ink);
  text-decoration: underline;
}
.contact-form {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px;
  padding: 28px;
  border: 1px solid var(--border);
  border-radius: 14px;
  background: #fff;
}
.contact-form label {
  display: grid;
  gap: 8px;
  color: var(--ink);
  font-size: 10px;
  font-weight: 900;
  letter-spacing: .7px;
  text-transform: uppercase;
}
.contact-form input,
.contact-form textarea {
  width: 100%;
  min-height: 46px;
  padding: 12px;
  border: 1px solid #e1e2e6;
  border-radius: 9px;
  background: #f7f7f8;
  color: var(--ink);
  font-size: 12px;
  font-weight: 400;
  letter-spacing: normal;
  text-transform: none;
}
.contact-form textarea {
  min-height: 64px;
  resize: vertical;
}
.contact-form .message-field {
  grid-column: 1 / -1;
}
.contact-form .contact-submit {
  min-height: 42px;
  justify-self: start;
  padding: 0 22px;
  border: 1px solid #111214 !important;
  border-radius: 99px !important;
  background: #111214 !important;
  color: #fff !important;
  font-size: 10px;
  font-weight: 900;
  letter-spacing: .6px;
  text-transform: uppercase;
}
.contact-form .contact-submit:hover {
  background: #343539 !important;
  transform: translateY(-1px);
}
@media (max-width: 760px) {
  .about-intro,
  .about-overview,
  .about-values {
    width: min(100% - 32px, 600px);
  }
  .about-intro {
    min-height: auto;
    grid-template-columns: 1fr;
    gap: 28px;
    padding: 40px 0;
  }
  .about-intro h1 {
    font-size: clamp(38px, 9vw, 48px);
  }
  .about-intro-image {
    min-height: 260px;
  }
  .about-overview {
    padding-top: 36px;
  }
  .overview-feature {
    grid-template-columns: 1fr;
    border-radius: 15px;
  }
  .overview-mission {
    min-height: 250px;
    padding: 30px 24px;
  }
  .overview-image {
    min-height: 260px;
    padding: 30px 24px;
  }
  .about-values {
    padding-top: 42px;
  }
  .about-values .values {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }
  .page-content {
    width: min(100% - 32px, 600px);
    padding: 32px 0 40px;
  }
  .contact-list {
    grid-template-columns: 1fr;
  }
  .contact-form {
    padding: 20px;
  }
}
@media (max-width: 480px) {
  .about-values .values {
    grid-template-columns: 1fr;
  }
  .contact-form {
    grid-template-columns: 1fr;
    gap: 12px;
  }
  .contact-form .message-field {
    grid-column: auto;
  }
}
  </style>
</head>
<body>
  <header class="site-header">
    <a class="brand" href="index.php">AUTORIDE</a>
    <nav class="nav" aria-label="Main navigation">
      <a href="index.php">Home</a>
      <a href="brows_vehicle.php">Browse Vehicle</a>
      <a href="rentflow.php">Rent</a>
      <a href="about_us.php" aria-current="page">About Us</a>
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

  <main>
    <section class="about-intro" aria-labelledby="about-title">
      <div class="about-intro-copy">
        <div class="about-eyebrow">About AutoRide</div>
        <h1 id="about-title">More than a ride.<br>A better way to go.</h1>
        <p>AUTORIDE helps people explore vehicle options and request rentals through one organized, convenient experience.</p>
        <p>From browsing vehicles to checking availability and tracking rental details, our goal is to make every step easier to follow.</p>
        <a class="button-dark" href="brows_vehicle.php">Explore Vehicles <span aria-hidden="true">→</span></a>
      </div>
      <div class="about-intro-image" role="img" aria-label="Vehicle rental experience image placeholder"></div>
    </section>

    <section class="about-overview" aria-labelledby="overview-title">
      <div class="about-eyebrow">Who We Are</div>
      <h2 class="about-overview-heading" id="overview-title">Company Overview</h2>
      <p class="about-overview-intro">A vehicle rental platform designed to connect people with vehicles for their everyday needs and special journeys.</p>
      <div class="overview-feature">
        <div class="overview-mission">
          <div class="about-eyebrow">Our Mission</div>
          <h2>Make every journey easier.</h2>
          <p>Provide a clear and convenient way for customers to browse vehicles, submit rental requests, and manage their rental information.</p>
        </div>
        <div class="overview-image">
          <div class="about-eyebrow">Our Vision</div>
          <h2>A better way to get where you're going.</h2>
          <p>Make vehicle rentals simple, reliable, and accessible for everyday needs and special journeys.</p>
        </div>
      </div>
    </section>

    <section class="about-values" aria-labelledby="values-title">
      <div class="about-values-heading">
        <div class="about-eyebrow">Where We're Going</div>
        <h2 id="values-title">Mission / Vision</h2>
        <p>Our core values guide us in making vehicle rental feel simple, reliable, and accessible.</p>
      </div>
      <div class="values" aria-label="Our core values">
        <article class="value-card">
          <span class="value-number" aria-hidden="true">01</span>
          <h3>Convenience</h3>
          <p>Keep vehicle discovery and rental requests organized in one place.</p>
        </article>
        <article class="value-card">
          <span class="value-number" aria-hidden="true">02</span>
          <h3>Clarity</h3>
          <p>Present vehicle details, dates, availability, and charges clearly.</p>
        </article>
        <article class="value-card">
          <span class="value-number" aria-hidden="true">03</span>
          <h3>Care</h3>
          <p>Support customers throughout the rental request process.</p>
        </article>
        <article class="value-card">
          <span class="value-number" aria-hidden="true">04</span>
          <h3>Confidence</h3>
          <p>Make it easier to understand the next step in every rental.</p>
        </article>
      </div>
    </section>

    <div class="page-content">
      <section class="contact" aria-labelledby="contact-title">
        <div class="contact-heading">
          <div class="about-eyebrow">Get in Touch</div>
          <h2 id="contact-title">Contact Us</h2>
          <p class="contact-intro">Have a question? Use the contact details or send a message.</p>
        </div>
        <div class="contact-list">
          <div class="contact-item"><span class="contact-icon" aria-hidden="true">⌖</span><strong>Address</strong><span>123 AutoRide Hub Building, EDSA Corner, Quezon City, 1100 Philippines</span></div>
          <div class="contact-item"><span class="contact-icon" aria-hidden="true">✉</span><strong>Email</strong><a href="mailto:support@autoride.domain.com">support@autoride.domain.com</a></div>
          <div class="contact-item"><span class="contact-icon" aria-hidden="true">☎</span><strong>Phone number</strong><a href="tel:+630288889999">+63 (02) 8888-9999</a></div>
        </div>
        <form class="contact-form" action="mailto:support@autoride.domain.com" method="post" enctype="text/plain">
          <label for="contact-name">Name
            <input id="contact-name" name="name" type="text" placeholder="Your name" autocomplete="name" required>
          </label>
          <label for="contact-email">Email
            <input id="contact-email" name="email" type="email" placeholder="Your email" autocomplete="email" required>
          </label>
          <label class="message-field" for="contact-message">Message
            <textarea id="contact-message" name="message" placeholder="How can we help?" required></textarea>
          </label>
          <button class="contact-submit" type="submit">Send Message <span aria-hidden="true">→</span></button>
        </form>
      </section>
    </div>
  </main>

  <footer class="footer">
    <div class="footer-grid">
      <div><a class="footer-brand" href="index.php">AUTORIDE</a><p>Your trusted partner for affordable vehicle rentals, easy bookings, and reliable rides.</p></div>
      <div><h2>QUICK LINKS</h2><ul><li><a href="index.php">Home</a></li><li><a href="brows_vehicle.php">Browse Vehicle</a></li><li><a href="about_us.php">About Us</a></li><li><a href="rentflow.php">Rent</a></li><li><a href="log_in.php">Login</a></li></ul></div>
      <div><h2>SOCIAL MEDIA</h2><ul><li><a href="#">Facebook</a></li><li><a href="#">Instagram</a></li><li><a href="#">TikTok</a></li></ul></div>
      <div><h2>POLICIES</h2><ul><li><a href="privacy_policy.php">Privacy Policy</a></li><li><a href="terms_of_policy.php">Terms of Policy</a></li><li><a href="faqs.php">FAQs</a></li><li><a href="terms_and_conditions.php">Terms and Conditions</a></li><li><a href="return_policy.php">Return Policy</a></li></ul></div>
    </div>
    <div class="footer-bottom">
      <span>© 2026 Autoride. All rights reserved.</span>
      <span><a href="terms_of_policy.php">TERMS OF SERVICE</a> &nbsp; <a href="privacy_policy.php">PRIVACY POLICY</a></span>
    </div>
  </footer>
</body>
</html>
