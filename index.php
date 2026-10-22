<?php
require_once __DIR__ . '/site_init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Find and rent your perfect ride with Autoride. Browse verified vehicles with clear, affordable daily rates.">
  <title>Autoride | Find Your Perfect Ride</title>
  <style>
    :root {
      color-scheme: light;
      --ink: #111827;
      --text: #374151;
      --muted: #6b7280;
      --line: #d1d5db;
      --soft: #e5e7eb;
      --wash: #f7f8fa;
      --footer: #f2f3f5;
      --white: #fff;
    }

    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body {
      zoom: 1.2;
      margin: 0;
      color: var(--text);
      background: var(--white);
      font-family: Arial, Helvetica, sans-serif;
      font-size: 13px;
      line-height: 1.45;
    }

    a { color: inherit; text-decoration: none; }
    button, input, select { font: inherit; }
    :focus-visible { outline: 2px solid var(--ink); outline-offset: 3px; }
    .top-rule { display: none; }

    .site-header {
      height: 52px;
      padding: 0 max(24px, calc((100% - 1140px) / 2));
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: var(--white);
      border-bottom: 1px solid var(--soft);
    }
    .brand { color: var(--ink); font-size: 14px; font-weight: 800; letter-spacing: .2px; }
    .nav { display: flex; align-items: center; gap: 24px; }
    .nav a { color: var(--text); font-size: 11px; }
    .nav-logout { padding: 0; border: 0; background: none; color: var(--text); font: inherit; font-size: 11px; cursor: pointer; }
    .nav-logout:hover { color: var(--ink); text-decoration: underline; text-underline-offset: 4px; }
    .nav a:hover, .nav a[aria-current="page"] { color: var(--ink); text-decoration: underline; text-underline-offset: 4px; }
    .search-mark {
      width: 11px;
      height: 11px;
      margin-left: 3px;
      border: 1.5px solid var(--ink);
      border-radius: 50%;
      position: relative;
    }
    .search-mark::after {
      content: "";
      position: absolute;
      width: 5px;
      height: 1.5px;
      right: -4px;
      bottom: -2px;
      background: var(--ink);
      transform: rotate(45deg);
    }

    .hero { background: var(--wash); }
    .hero-inner {
      width: min(1140px, calc(100% - 48px));
      min-height: 310px;
      margin: auto;
      padding: 34px 0;
      display: grid;
      grid-template-columns: 1.05fr .95fr;
      align-items: center;
      gap: 48px;
    }
    .hero-copy h1 {
      margin: 0 0 12px;
      color: var(--ink);
      font-size: clamp(28px, 3vw, 38px);
      line-height: 1.12;
      letter-spacing: -.7px;
    }
    .hero-copy > p { max-width: 490px; margin: 0 0 16px; color: var(--muted); font-size: 13px; }
    .button-dark {
      min-height: 34px;
      padding: 0 15px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: 1px solid var(--ink);
      border-radius: 3px;
      background: var(--ink);
      color: var(--white);
      font-size: 10px;
      font-weight: 700;
      letter-spacing: .25px;
    }
    .button-dark:hover { background: #303847; }
    .quick-search { margin-top: 20px; }
    .quick-search-title { margin-bottom: 6px; color: var(--muted); font-size: 9px; font-weight: 700; }
    .search-fields { display: grid; grid-template-columns: 1.1fr 1fr 1fr .85fr auto; gap: 7px; align-items: end; }
    .search-fields label { min-width: 0; color: var(--muted); font-size: 8px; }
    .search-fields input, .search-fields select {
      width: 100%;
      height: 29px;
      margin-top: 4px;
      padding: 0 6px;
      border: 1px solid var(--line);
      border-radius: 2px;
      background: var(--white);
      color: var(--text);
      font-size: 9px;
    }
    .search-submit { height: 29px; padding: 0 10px; cursor: pointer; }
    .hero-placeholder {
      min-height: 215px;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      border: 1px solid var(--line);
      background: #f1f2f4;
    }
    .hero-placeholder::before {
      content: "";
      position: absolute;
      width: 62%;
      height: 56%;
      border: 1px solid rgba(209, 213, 219, .55);
      transform: rotate(28deg);
    }
    .placeholder-label {
      position: relative;
      z-index: 1;
      padding: 5px 9px;
      border: 1px solid var(--line);
      background: var(--white);
      color: var(--text);
      font-size: 8px;
      text-align: center;
      text-transform: uppercase;
    }

    .content-section { width: min(1140px, calc(100% - 48px)); margin: 38px auto 0; }
    .section-heading { padding-bottom: 8px; border-bottom: 1px solid var(--soft); }
    .section-heading h2 { margin: 0; color: var(--ink); font-size: 16px; line-height: 1.25; }
    .section-heading p { margin: 3px 0 0; color: var(--muted); font-size: 10px; }
    .steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 28px; padding-top: 16px; }
    .step-number {
      width: 27px;
      height: 27px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: 1px solid var(--line);
      border-radius: 50%;
      color: var(--ink);
      font-size: 11px;
    }
    .step h3 { margin: 7px 0 3px; color: var(--ink); font-size: 11px; }
    .step p { max-width: 290px; margin: 0; color: var(--muted); font-size: 9px; }

    .vehicle-grid { display: grid; gap: 16px; margin: 15px 12px 0; }
    .vehicle-grid.featured { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .vehicle-grid.popular { grid-template-columns: repeat(3, minmax(0, 1fr)); margin-right: 5px; margin-left: 5px; }
    .vehicle-card { min-width: 0; overflow: hidden; border: 1px solid #e9ebef; border-radius: 3px; background: var(--white); }
    .vehicle-image {
      height: 126px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-bottom: 1px solid var(--line);
      background: #f1f2f4;
    }
    .vehicle-image .placeholder-label { padding: 5px 8px; font-size: 7px; }
    .vehicle-info { padding: 10px; }
    .vehicle-type { color: var(--muted); font-size: 8px; text-transform: uppercase; }
    .vehicle-info h3 { overflow: hidden; margin: 2px 0 4px; color: var(--ink); font-size: 12px; white-space: nowrap; text-overflow: ellipsis; }
    .vehicle-meta { color: var(--muted); font-size: 9px; }
    .vehicle-bottom { display: flex; align-items: center; justify-content: space-between; gap: 6px; margin-top: 9px; }
    .vehicle-price { color: var(--ink); font-size: 11px; font-weight: 700; white-space: nowrap; }
    .vehicle-price small { display: block; color: var(--muted); font-size: 8px; font-weight: 400; }
    .details-button {
      padding: 6px 8px;
      border: 1px solid var(--line);
      border-radius: 2px;
      background: var(--white);
      color: var(--ink);
      font-size: 8px;
      white-space: nowrap;
    }
    .details-button:hover { border-color: var(--ink); }

    .feedback { margin-top: 38px; padding: 34px 0; background: var(--wash); }
    .feedback-inner { width: min(1140px, calc(100% - 48px)); margin: auto; }
    .review-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 14px; }
    .review-card { min-height: 92px; padding: 11px; border: 1px solid #e9ebef; background: var(--white); }
    .stars { color: var(--ink); font-size: 11px; letter-spacing: 1px; }
    .review-card p { margin: 5px 0 9px; color: var(--muted); font-size: 8px; line-height: 1.5; }
    .reviewer { display: flex; align-items: center; gap: 7px; color: var(--ink); font-size: 8px; font-weight: 700; }
    .avatar { width: 20px; height: 20px; flex: 0 0 auto; border: 1px solid var(--line); border-radius: 50%; background: #f1f2f4; }

    .site-footer { padding: 30px max(24px, calc((100% - 1140px) / 2)) 12px; background: var(--footer); }
    .footer-top { display: grid; grid-template-columns: 1.6fr 1fr 1fr 1fr; gap: 24px; }
    .footer-brand { display: inline-block; margin-bottom: 8px; color: var(--ink); font-size: 12px; font-weight: 800; }
    .site-footer h3 { margin: 0 0 9px; color: var(--ink); font-size: 9px; font-weight: 800; }
    .site-footer p, .site-footer li { color: var(--text); font-size: 8px; }
    .site-footer p { max-width: 205px; margin: 0; }
    .site-footer ul { margin: 0; padding: 0; list-style: none; }
    .site-footer li { margin-bottom: 5px; }
    .site-footer a:hover { text-decoration: underline; }
    .footer-bottom {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      margin-top: 22px;
      padding-top: 10px;
      border-top: 1px solid #cbd0d8;
      color: var(--muted);
      font-size: 7px;
    }

    @media (max-width: 700px) {
      .site-header { height: auto; min-height: 48px; padding: 10px 18px; gap: 12px; }
      .brand { font-size: 12px; }
      .nav { justify-content: flex-end; gap: clamp(8px, 2.4vw, 17px); }
      .nav a { font-size: 9px; }
      .hero-inner { width: calc(100% - 36px); min-height: 245px; gap: 18px; }
      .hero-copy h1 { font-size: clamp(22px, 5vw, 30px); }
      .hero-copy > p { font-size: 11px; }
      .hero-placeholder { min-height: 160px; }
      .search-fields { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .search-submit { grid-column: 2; }
      .content-section, .feedback-inner { width: calc(100% - 36px); }
      .vehicle-grid { gap: 10px; margin-right: 0; margin-left: 0; }
      .vehicle-info { padding: 8px; }
      .vehicle-info h3 { font-size: 10px; }
      .vehicle-meta { font-size: 8px; }
      .details-button { padding: 5px; font-size: 7px; }
      .vehicle-price { font-size: 9px; }
      .vehicle-bottom { gap: 3px; }
      .footer-top { gap: 18px; }
    }

    @media (max-width: 480px) {
      body { font-size: 6px; }
      .top-rule { height: 2px; }
      .site-header { height: 22px; min-height: 0; padding: 0 20px; gap: 5px; }
      .brand { font-size: 6px; }
      .nav { gap: 8px; }
      .nav a { font-size: 4.5px; }
      .search-mark { width: 6px; height: 6px; border-width: 1px; }
      .search-mark::after { width: 3px; height: 1px; right: -3px; bottom: -1px; }
      .hero-inner {
        width: calc(100% - 40px); min-height: 145px; padding: 13px 0;
        grid-template-columns: 1.05fr .95fr; gap: 11px;
      }
      .hero-copy h1 { margin-bottom: 5px; font-size: 15px; letter-spacing: -.2px; }
      .hero-copy > p { margin-bottom: 6px; font-size: 5px; }
      .button-dark { min-height: 13px; padding: 0 6px; border-radius: 1px; font-size: 4px; }
      .quick-search { margin-top: 7px; }
      .quick-search-title { margin-bottom: 2px; font-size: 3.5px; }
      .search-fields { grid-template-columns: 1fr 1fr 1fr .8fr auto; gap: 2px; }
      .search-fields label { font-size: 3px; }
      .search-fields input, .search-fields select { height: 12px; margin-top: 2px; padding: 0 2px; font-size: 3.5px; }
      .search-submit { grid-column: auto; height: 12px; padding: 0 3px; font-size: 3px; }
      .hero-placeholder { min-height: 108px; }
      .hero-placeholder .placeholder-label { padding: 3px 4px; font-size: 3.5px; }
      .content-section, .feedback-inner { width: calc(100% - 40px); margin-top: 22px; }
      .section-heading { padding-bottom: 4px; }
      .section-heading h2 { font-size: 7px; }
      .section-heading p { margin-top: 1px; font-size: 4px; }
      .steps { gap: 12px; padding-top: 9px; }
      .step-number { width: 14px; height: 14px; font-size: 6px; }
      .step h3 { margin: 4px 0 2px; font-size: 4px; }
      .step p { font-size: 3.5px; }
      .vehicle-grid { gap: 6px; margin: 8px 10px 0; }
      .vehicle-grid.featured { grid-template-columns: repeat(3, minmax(0, 1fr)); }
      .vehicle-grid.popular { grid-template-columns: repeat(3, minmax(0, 1fr)); margin-right: 4px; margin-left: 4px; }
      .vehicle-image { height: 46px; }
      .vehicle-image .placeholder-label { padding: 2px 3px; font-size: 3px; }
      .vehicle-info { padding: 4px 4px 3px; }
      .vehicle-type { font-size: 3px; }
      .vehicle-info h3 { margin: 1px 0 2px; font-size: 4px; }
      .vehicle-meta { font-size: 3px; }
      .vehicle-bottom { gap: 2px; margin-top: 3px; }
      .vehicle-price { font-size: 4px; }
      .vehicle-price small { font-size: 3px; }
      .details-button { padding: 3px 4px; font-size: 3px; }
      .feedback { margin-top: 22px; padding: 22px 0; }
      .review-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 6px; margin-top: 8px; }
      .review-card { min-height: 47px; padding: 5px; }
      .stars { font-size: 5px; }
      .review-card p { margin: 2px 0 3px; font-size: 3px; }
      .reviewer { gap: 3px; font-size: 3px; }
      .avatar { width: 10px; height: 10px; }
      .site-footer { padding: 18px 20px 7px; }
      .footer-top { grid-template-columns: 1.6fr 1fr 1fr 1fr; gap: 12px; }
      .footer-brand { margin-bottom: 5px; font-size: 5px; }
      .site-footer h3 { margin-bottom: 4px; font-size: 3.5px; }
      .site-footer p, .site-footer li { font-size: 3px; }
      .site-footer li { margin-bottom: 2px; }
      .site-footer p { max-width: 85px; }
      .footer-bottom { flex-direction: row; gap: 8px; margin-top: 10px; padding-top: 5px; font-size: 2.8px; }
    }

    @media (max-width: 340px) {
      .site-header { padding: 0 10px; }
      .nav { gap: 5px; }
      .nav a { font-size: 4px; }
      .hero-inner, .content-section, .feedback-inner { width: calc(100% - 20px); }
      .hero-inner { gap: 7px; }
      .search-fields { gap: 1px; }
      .vehicle-grid { gap: 3px; margin-right: 0; margin-left: 0; }
      .vehicle-info { padding: 3px 2px; }
      .details-button { padding: 2px; font-size: 2.5px; }
      .site-footer { padding-right: 10px; padding-left: 10px; }
    }

    body.home-page {
      zoom: 1 !important;
      color: var(--ink);
      font-size: 14px;
      line-height: 1.5;
    }
    body.home-page .hero {
      min-height: 560px;
      display: flex;
      align-items: center;
      background: #292a2d;
      color: #fff;
    }
    body.home-page .hero-inner {
      width: min(1280px, calc(100% - 64px));
      min-height: 560px;
      grid-template-columns: 1.05fr .95fr;
      gap: 48px;
      padding: 64px 0;
    }
    body.home-page .hero-copy h1 {
      max-width: 720px;
      margin: 18px 0;
      color: #fff;
      font-size: clamp(42px, 6vw, 76px);
      font-weight: 950;
      line-height: .98;
      letter-spacing: -2.8px;
      text-transform: uppercase;
    }
    body.home-page .hero-copy h1 span { color: var(--yellow); }
    body.home-page .hero-copy > p {
      max-width: 520px;
      margin-bottom: 24px;
      color: #e1e1e3;
      font-size: 15px;
    }
    body.home-page .hero-placeholder {
      min-height: 320px;
      border: 0;
      border-radius: 18px;
      background: #ececef;
    }
    body.home-page .hero-placeholder::before {
      width: 62%;
      height: 56%;
      border-color: #d7d7dc;
    }
    body.home-page .placeholder-label {
      padding: 8px 12px;
      border-color: var(--line);
      border-radius: 99px;
      font-size: 10px;
    }
    body.home-page .button-dark {
      min-height: 44px;
      padding: 0 20px;
      border: 1px solid var(--yellow);
      border-radius: 99px;
      background: var(--yellow);
      color: var(--ink);
      font-size: 10px;
      font-weight: 900;
      letter-spacing: 1px;
    }
    body.home-page .quick-search {
      margin-top: 28px;
      padding: 0;
    }
    body.home-page .quick-search-title {
      margin-bottom: 8px;
      color: #e1e1e3;
      font-size: 10px;
      letter-spacing: 1px;
    }
    body.home-page .search-fields {
      grid-template-columns: 1.2fr 1fr 1fr 1fr auto;
      gap: 8px;
      align-items: end;
    }
    body.home-page .search-fields label {
      color: #e1e1e3;
      font-size: 9px;
      font-weight: 800;
      letter-spacing: .7px;
    }
    body.home-page .search-fields input,
    body.home-page .search-fields select {
      height: 40px;
      padding: 0 9px;
      border: 1px solid #e1e1e3;
      border-radius: 8px;
      background: #fff;
      color: var(--ink);
      font-size: 12px;
    }
    body.home-page .search-submit { height: 40px; }
    body.home-page .content-section {
      width: min(1280px, calc(100% - 64px));
      margin: 0 auto;
      padding: 72px 0 0;
    }
    body.home-page .section-heading {
      margin-bottom: 24px;
      padding-bottom: 12px;
      border-color: var(--line);
    }
    body.home-page .section-heading h2 {
      font-size: clamp(26px, 3vw, 38px);
      line-height: 1.08;
      letter-spacing: -1.2px;
      text-transform: uppercase;
    }
    body.home-page .section-heading p {
      margin-top: 8px;
      color: var(--muted);
      font-size: 13px;
    }
    body.home-page .vehicle-section-heading {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      gap: 20px;
      margin-bottom: 24px;
      padding: 0;
      border: 0;
    }
    body.home-page .vehicle-section-title .section-eyebrow {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 10px;
      color: #4b5563;
      font-size: 10px;
      font-weight: 900;
      letter-spacing: 1.7px;
      text-transform: uppercase;
    }
    body.home-page .vehicle-section-title .section-eyebrow::before {
      width: 22px;
      height: 2px;
      background: var(--yellow);
      content: "";
    }
    body.home-page .vehicle-section-heading h2 {
      margin: 0;
      font-size: clamp(26px, 3vw, 38px);
    }
    body.home-page .vehicle-section-heading p {
      margin: 8px 0 0;
    }
    body.home-page .view-all-vehicles {
      flex: 0 0 auto;
      margin-bottom: 3px;
      padding-bottom: 5px;
      border-bottom: 1px solid var(--yellow);
      color: var(--ink);
      font-size: 10px;
      font-weight: 900;
      letter-spacing: .8px;
      text-transform: uppercase;
    }
    body.home-page .vehicle-card {
      position: relative;
      border-radius: 15px;
      box-shadow: none;
    }
    body.home-page .steps {
      gap: 24px;
      padding: 8px 0 0;
    }
    body.home-page .step-number {
      width: 40px;
      height: 40px;
      border: 0;
      background: var(--yellow);
      font-size: 14px;
      font-weight: 900;
    }
    body.home-page .step h3 { margin: 12px 0 6px; font-size: 16px; }
    body.home-page .step p { max-width: 340px; color: var(--muted); font-size: 12px; }
    body.home-page .vehicle-grid { gap: 20px; margin: 0; }
    body.home-page .vehicle-card {
      border: 1px solid var(--line);
      border-radius: 15px;
      box-shadow: none;
    }
    body.home-page .vehicle-image {
      height: 207px;
      position: relative;
      justify-content: flex-start;
      align-items: flex-start;
      padding: 14px;
      border: 0;
      background: linear-gradient(180deg, #ececef 0%, #ececef 52%, #a5a5a8 100%);
    }
    body.home-page .vehicle-image .placeholder-label { display: none; }
    body.home-page .vehicle-badge {
      padding: 7px 11px;
      border-radius: 99px;
      background: var(--yellow);
      color: var(--ink);
      font-size: 9px;
      font-weight: 900;
      letter-spacing: .8px;
      text-transform: uppercase;
    }
    body.home-page .vehicle-favorite {
      width: 34px;
      height: 34px;
      position: absolute;
      top: 13px;
      right: 13px;
      display: grid;
      place-items: center;
      border-radius: 50%;
      background: #fff;
      color: var(--ink);
      font-size: 16px;
    }
    body.home-page .vehicle-info { padding: 18px; }
    body.home-page .vehicle-type {
      font-size: 10px;
      letter-spacing: .8px;
      text-transform: uppercase;
    }
    body.home-page .vehicle-info h3 { margin: 8px 0 4px; font-size: 18px; }
    body.home-page .vehicle-meta {
      display: none;
    }
    body.home-page .vehicle-bottom {
      margin-top: 14px;
      padding-top: 12px;
      border-top: 1px solid var(--line);
    }
    body.home-page .vehicle-price { font-size: 18px; }
    body.home-page .vehicle-price small {
      display: inline;
      margin-left: 4px;
      font-size: 10px;
    }
    body.home-page .details-button {
      width: 36px;
      height: 36px;
      display: grid;
      place-items: center;
      padding: 0;
      border-radius: 50%;
      font-size: 0;
    }
    body.home-page .details-button::after {
      content: "↗";
      font-size: 16px;
      line-height: 1;
    }
    body.home-page .feedback {
      margin-top: 72px;
      padding: 64px 0;
      background: var(--soft);
    }
    body.home-page .feedback-inner { width: min(1280px, calc(100% - 64px)); }
    body.home-page .review-grid { gap: 16px; margin-top: 20px; }
    body.home-page .review-card {
      min-height: 150px;
      padding: 20px;
      border: 1px solid var(--line);
      border-radius: 14px;
    }
    body.home-page .stars { color: #b38c00; font-size: 16px; }
    body.home-page .review-card p { margin: 10px 0 14px; color: var(--muted); font-size: 12px; }
    body.home-page .reviewer { font-size: 11px; }
    body.home-page .avatar { width: 30px; height: 30px; background: var(--yellow); }

    @media (max-width: 760px) {
      body.home-page .hero { min-height: 0; }
      body.home-page .hero-inner {
        width: calc(100% - 40px);
        min-height: 0;
        grid-template-columns: 1fr;
        gap: 24px;
        padding: 48px 0;
      }
      body.home-page .hero-copy h1 { font-size: clamp(38px, 10vw, 62px); }
      body.home-page .hero-placeholder { min-height: 230px; }
      body.home-page .search-fields { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      body.home-page .search-submit { grid-column: 1 / -1; }
      body.home-page .content-section,
      body.home-page .feedback-inner { width: calc(100% - 40px); }
      body.home-page .content-section { padding-top: 54px; }
      body.home-page .vehicle-grid.featured,
      body.home-page .vehicle-grid.popular { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      body.home-page .vehicle-section-heading { align-items: flex-start; }
      body.home-page .review-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 480px) {
      body.home-page .hero-inner { width: calc(100% - 32px); padding: 36px 0; }
      body.home-page .hero-copy h1 { font-size: clamp(34px, 11vw, 48px); letter-spacing: -1.5px; }
      body.home-page .hero-copy > p { font-size: 14px; }
      body.home-page .search-fields { grid-template-columns: 1fr; gap: 10px; }
      body.home-page .search-submit { grid-column: auto; }
      body.home-page .content-section,
      body.home-page .feedback-inner { width: calc(100% - 32px); }
      body.home-page .vehicle-grid.featured,
      body.home-page .vehicle-grid.popular,
      body.home-page .steps { grid-template-columns: 1fr; }
      body.home-page .vehicle-image { height: 190px; }
      body.home-page .vehicle-section-heading {
        flex-direction: column;
        gap: 12px;
      }
      body.home-page .feedback { padding: 44px 0; }
      body.home-page .site-footer .footer-top { grid-template-columns: 1fr 1fr; }
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
<body class="home-page">
  <div class="top-rule" aria-hidden="true"></div>
  <header class="site-header">
    <a class="brand" href="index.php">AUTORIDE</a>
    <nav class="nav" aria-label="Main navigation">
      <a href="index.php" aria-current="page">Home</a>
      <a href="brows_vehicle.php">Browse Vehicle</a>
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

  <main id="home">
    <section class="hero" aria-labelledby="hero-title">
      <div class="hero-inner home-hero-inner">
        <div class="hero-copy">
          <div class="hero-eyebrow"><span></span> Your journey starts here</div>
          <h1 id="hero-title">Find your<br>next<br><span>great drive.</span></h1>
          <p>From everyday trips to weekend escapes, discover a vehicle that fits your plans. Simple booking, flexible choices, and a smoother ride with AUTORIDE.</p>
          <div class="hero-actions">
            <a class="button-dark" href="brows_vehicle.php">Explore Vehicles <span aria-hidden="true">→</span></a>
            <a class="button-outline" href="about_us.php">Discover Autoride</a>
          </div>
        </div>
        <div class="hero-tag"><span></span> Find your ride. Own the road.</div>
      </div>
      <form class="quick-search home-quick-search" action="brows_vehicle.php" method="get">
        <label>Vehicle type
          <select name="type">
            <option value="">All vehicles</option>
            <option value="Car">Car</option>
            <option value="SUV">SUV</option>
            <option value="Van">Van</option>
            <option value="Motorcycle">Motorcycle</option>
          </select>
        </label>
        <label>Pick-up date<input type="date" name="pickup"></label>
        <label>Return date<input type="date" name="return"></label>
        <label>Passengers
          <select name="passengers">
            <option value="">Any number</option>
            <option value="2">2 or more</option>
            <option value="4">4 or more</option>
            <option value="6">6 or more</option>
          </select>
        </label>
        <button class="button-dark search-submit" type="submit">Find a vehicle <span aria-hidden="true">→</span>        </button>
      </form>
    </section>

    <section class="content-section" id="about" aria-labelledby="how-title">
      <div class="section-heading">
        <h2 id="how-title">How It Works</h2>
        <p>Rent a vehicle in three simple steps.</p>
      </div>
      <div class="steps">
        <article class="step">
          <span class="step-number">1</span>
          <h3>Search &amp; Select</h3>
          <p>Browse our fleet and filter by location, date, type, and price. Choose the vehicle that fits your needs.</p>
        </article>
        <article class="step">
          <span class="step-number">2</span>
          <h3>Submit a Request</h3>
          <p>Register or log in to submit your rental details, and our team will confirm your request instantly.</p>
        </article>
        <article class="step">
          <span class="step-number">3</span>
          <h3>Get Approved &amp; Ride</h3>
          <p>Receive confirmation, complete verification, pick up your vehicle, and enjoy your ride.</p>
        </article>
      </div>
    </section>

    <section class="content-section" id="featured" aria-labelledby="featured-title">
      <div class="section-heading vehicle-section-heading">
        <div class="vehicle-section-title">
          <div class="section-eyebrow">Handpicked for you</div>
          <h2 id="featured-title">Featured Vehicles</h2>
          <p>Explore popular options for different journeys and travel needs.</p>
        </div>
        <a class="view-all-vehicles" href="brows_vehicle.php">View All Vehicles <span aria-hidden="true">↗</span></a>
      </div>
      <div class="vehicle-grid featured">
        <?php foreach (array_slice($vehicles, 0, 3) as $index => $vehicle): ?>
          <article class="vehicle-card">
            <div class="vehicle-image" role="img" aria-label="<?php echo e($vehicle['name']); ?> image placeholder">
              <span class="vehicle-badge"><?php echo ['Popular', 'Premium', 'Family Choice'][$index] ?? 'Featured'; ?></span>
              <span class="vehicle-favorite" aria-hidden="true">♡</span>
            </div>
            <div class="vehicle-info">
              <div class="vehicle-type"><?php echo e(strtoupper($vehicle['category'])); ?> &nbsp;·&nbsp; <?php echo e(strtoupper($vehicle['transmission'])); ?> &nbsp;·&nbsp; <?php echo (int) $vehicle['seats']; ?> Seats</div>
              <h3><?php echo e($vehicle['brand']); ?></h3>
              <div class="vehicle-bottom"><div class="vehicle-price">₱<?php echo number_format((float) $vehicle['daily_rate']); ?><small>/ day</small></div><a class="details-button" aria-label="View details for <?php echo e($vehicle['name']); ?>" href="vehicle_details.php?id=<?php echo rawurlencode($vehicle['slug']); ?>">VIEW DETAILS</a></div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="content-section" id="popular" aria-labelledby="popular-title">
      <div class="section-heading vehicle-section-heading">
        <div class="vehicle-section-title">
          <div class="section-eyebrow">Popular picks</div>
          <h2 id="popular-title">Popular Vehicles</h2>
          <p>Customer favorites for your next trip.</p>
        </div>
        <a class="view-all-vehicles" href="brows_vehicle.php">View All Vehicles <span aria-hidden="true">↗</span></a>
      </div>
      <div class="vehicle-grid popular">
        <?php foreach (array_slice($vehicles, 3) as $index => $vehicle): ?>
          <article class="vehicle-card">
            <div class="vehicle-image" role="img" aria-label="<?php echo e($vehicle['name']); ?> image placeholder">
              <span class="vehicle-badge"><?php echo ['Popular', 'Top Rated', 'Staff Pick'][$index] ?? 'Popular'; ?></span>
              <span class="vehicle-favorite" aria-hidden="true">♡</span>
            </div>
            <div class="vehicle-info">
              <div class="vehicle-type"><?php echo e(strtoupper($vehicle['category'])); ?> &nbsp;·&nbsp; <?php echo e(strtoupper($vehicle['transmission'])); ?> &nbsp;·&nbsp; <?php echo (int) $vehicle['seats']; ?> Seats</div>
              <h3><?php echo e($vehicle['brand']); ?></h3>
              <div class="vehicle-bottom"><div class="vehicle-price">₱<?php echo number_format((float) $vehicle['daily_rate']); ?><small>/ day</small></div><a class="details-button" aria-label="View details for <?php echo e($vehicle['name']); ?>" href="vehicle_details.php?id=<?php echo rawurlencode($vehicle['slug']); ?>">VIEW DETAILS</a></div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="feedback" aria-labelledby="feedback-title">
      <div class="feedback-inner">
        <div class="section-heading">
          <h2 id="feedback-title">FEEDBACK</h2>
          <p>Real feedback from verified users.</p>
        </div>
        <div class="review-grid">
          <article class="review-card">
            <div class="stars" aria-label="5 out of 5 stars">★★★★★</div>
            <p>"They made my weekend getaway a great trip! The Fortuner was clean, reliable, and exactly as described."</p>
            <div class="reviewer"><span class="avatar" aria-hidden="true"></span>Juan Reyes</div>
          </article>
          <article class="review-card">
            <div class="stars" aria-label="5 out of 5 stars">★★★★★</div>
            <p>"I needed a Honda Civic for a work trip. The vehicle was in perfect condition, and the team made everything easy."</p>
            <div class="reviewer"><span class="avatar" aria-hidden="true"></span>Maria Lopez</div>
          </article>
          <article class="review-card">
            <div class="stars" aria-label="5 out of 5 stars">★★★★★</div>
            <p>"Excellent selection of vehicles. Transparent pricing and a quick approval process made renting stress-free."</p>
            <div class="reviewer"><span class="avatar" aria-hidden="true"></span>Angelica Cruz</div>
          </article>
        </div>
      </div>
    </section>
  </main>

  <footer class="site-footer">
    <div class="footer-top">
      <div>
        <a class="footer-brand" href="#home">AUTORIDE</a>
        <p>Your trusted partner for affordable vehicle rentals, easy bookings, and reliable rides.</p>
      </div>
      <div>
        <h3>QUICK LINKS</h3>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="brows_vehicle.php">Browse Vehicle</a></li>
          <li><a href="about_us.php">About Us</a></li>
          <li><a href="rentflow.php">Rent</a></li>
          <li><a href="log_in.php">Login</a></li>
        </ul>
      </div>
      <div>
        <h3>SOCIAL MEDIA</h3>
        <ul><li><a href="#">Facebook</a></li><li><a href="#">Instagram</a></li><li><a href="#">TikTok</a></li></ul>
      </div>
      <div>
        <h3>POLICIES</h3>
        <ul><li><a href="privacy_policy.php">Privacy Policy</a></li><li><a href="terms_of_policy.php">Terms of Policy</a></li><li><a href="faqs.php">FAQs</a></li><li><a href="terms_and_conditions.php">Terms and Conditions</a></li><li><a href="return_policy.php">Return Policy</a></li></ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© 2026 Autoride. All rights reserved.</span>
      <span><a href="terms_of_policy.php">TERMS OF SERVICE</a> &nbsp; <a href="privacy_policy.php">PRIVACY POLICY</a></span>
    </div>
  </footer>
</body>
</html>