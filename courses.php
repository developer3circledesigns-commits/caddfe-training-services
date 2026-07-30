<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="Content-Security-Policy" content="default-src 'self' https://esm.sh; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://esm.sh; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; img-src 'self' https://images.unsplash.com data:; connect-src 'self' https://cdn.jsdelivr.net https://esm.sh; frame-src 'none'; object-src 'none';" />
  <meta name="description" content="Browse CADDFE&#39;s professional Civil CAD training programs — AutoCAD, Revit, Staad Pro, 3ds Max, and more. Flexible schedules for students and professionals." />
  <link rel="icon" href="images/fav_icon.png" type="image/png" />
  <title>Pick Your Course - CADDFE Training Services</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin />
  <link rel="preconnect" href="https://esm.sh" crossorigin />
  <link rel="preload" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=optional" as="style" onload="this.onload=null;this.rel='stylesheet'" />
  <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" /></noscript>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" media="print" onload="this.media='all'" />
  <noscript><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" /></noscript>
  <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'" />
  <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" /></noscript>
  <link rel="stylesheet" href="css/style.css" />
  <style>@font-face{font-family:'bootstrap-icons';src:url(https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/bootstrap-icons.woff2) format('woff2');font-display:swap}</style>
  <style>
    html { scroll-behavior: smooth; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
    .hero-header { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; transition: background 0.3s, backdrop-filter 0.3s, box-shadow 0.3s; }
    .hero-header.scrolled { background: rgba(15,23,42,0.45); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
    .small-hover { position: relative; text-decoration: none !important; }
    .small-hover::after { content: ''; position: absolute; left: 0; bottom: -2px; width: 0; height: 2px; background: #d8000d; transition: width 0.3s ease; }
    .small-hover:hover { color: #fff !important; }
    .small-hover:hover::after { width: 100%; }
    .dropdown-nav .mega-dropdown {
      position: fixed; left: 0; right: 0; top: 72px;
      background: #fff; border-radius: 0;
      box-shadow: 0 20px 50px -20px rgba(0,0,0,0.25);
      opacity: 0; visibility: hidden; transition: opacity 0.25s ease, visibility 0.25s ease;
      z-index: 1050; pointer-events: none;
    }
    .mega-dropdown::before {
      content: ''; position: absolute;
      left: 0; right: 0; bottom: 100%;
      height: 18px; background: #fff;
    }
    .dropdown-nav:hover .mega-dropdown,
    .dropdown-nav.show .mega-dropdown {
      opacity: 1; visibility: visible;
      pointer-events: auto;
    }
    .hero-header:has(.dropdown-nav:hover),
    .hero-header:has(.dropdown-nav.show) {
      background: #fff !important;
      backdrop-filter: none !important;
      -webkit-backdrop-filter: none !important;
      box-shadow: none !important;
    }
    .hero-header:has(.dropdown-nav:hover) .nav-link,
    .hero-header:has(.dropdown-nav.show) .nav-link {
      color: #1e293b !important;
      opacity: 1 !important;
      position: relative !important;
      z-index: 1060 !important;
    }
    .hero-header:has(.dropdown-nav:hover) .small-hover::after,
    .hero-header:has(.dropdown-nav.show) .small-hover::after {
      background: #d8000d !important;
    }
    .mega-dropdown .mega-inner { max-width: 72rem; margin: 0 auto; display: flex; padding: 0 48px; }
    .mega-dropdown .mega-card {
      flex: 1; padding: 32px 24px; text-decoration: none;
      display: flex; flex-direction: column; align-items: center; text-align: center;
      transition: all 0.25s;
    }
    .mega-dropdown .mega-card:first-child { border-right: 1px solid #f1f5f9; }
    .mega-dropdown .mega-card:hover { background: #fef2f2; }
    .mega-dropdown .mega-icon {
      width: 60px; height: 60px; border-radius: 14px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.5rem; margin-bottom: 12px;
      transition: transform 0.25s;
    }
    .mega-dropdown .mega-card:hover .mega-icon { transform: scale(1.1); }
    .mega-dropdown .mega-card h5 { font-size: 1rem; font-weight: 700; margin-bottom: 6px; color: #1e293b; }
    .mega-dropdown .mega-card .mega-desc { font-size: 0.78rem; color: #64748b; line-height: 1.5; margin-bottom: 12px; }
    .mega-dropdown .mega-card .mega-arrow { font-size: 0.78rem; font-weight: 700; color: #d8000d; transition: all 0.25s; }
    .mega-dropdown .mega-card:hover .mega-arrow { letter-spacing: 0.05em; }
    @media (max-width: 991.98px) {
      .dropdown-nav .mega-dropdown {
        position: static; background: transparent; box-shadow: none;
        opacity: 1; visibility: visible; pointer-events: auto;
        display: none; width: 100%; transition: none;
      }
      .dropdown-nav.show .mega-dropdown { display: block; }
      .mega-dropdown::before { display: none; }
      .mega-dropdown .mega-inner { flex-direction: column; padding: 8px 0 0; max-width: 100%; }
      .mega-dropdown .mega-card {
        padding: 14px 16px; flex-direction: row; text-align: left;
        gap: 12px; border-bottom: 1px solid rgba(255,255,255,0.08);
      }
      .mega-dropdown .mega-card:first-child { border-right: none; }
      .mega-dropdown .mega-card:hover { background: rgba(255,255,255,0.05); }
      .mega-dropdown .mega-icon {
        width: 40px; height: 40px; font-size: 1.1rem;
        margin-bottom: 0; flex-shrink: 0;
      }
      .mega-dropdown .mega-card h4 { font-size: 0.85rem; color: #fff; margin-bottom: 2px; }
      .mega-dropdown .mega-card .mega-desc {
        font-size: 0.7rem; color: rgba(255,255,255,0.6); margin-bottom: 2px;
      }
      .mega-dropdown .mega-card .mega-arrow { font-size: 0.7rem; color: #d8000d; }
      .hero-header:has(.dropdown-nav.show) {
        background: transparent !important; backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important; box-shadow: none !important;
      }
      .hero-header:has(.dropdown-nav.show) .nav-link {
        color: #fff !important; opacity: 0.9 !important;
      }
      .hero-header:has(.dropdown-nav.show) .small-hover::after {
        background: #d8000d !important;
      }
    }
    .enroll-btn {
      background: transparent; border: none; cursor: pointer; outline: none;
      padding: 0; font: inherit; color: inherit; display: inline-flex;
      position: relative; overflow: hidden; transition: .5s linear;
    }
    .enroll-btn .box {
      display: block; position: relative; overflow: hidden;
      padding: 10px 20px; text-align: center; background: transparent;
      font-weight: 700; text-transform: uppercase; font-size: 0.9rem;
    }
    .enroll-btn .box::before {
      position: absolute; content: ''; left: 0; bottom: 0;
      height: 4px; width: 100%;
      border-bottom: 3px solid transparent;
      border-left: 3px solid transparent;
      box-sizing: border-box; transform: translateX(100%);
    }
    .enroll-btn .box::after {
      position: absolute; content: ''; top: 0; left: 0;
      width: 100%; height: 4px;
      border-top: 3px solid transparent;
      border-right: 3px solid transparent;
      box-sizing: border-box; transform: translateX(-100%);
    }
    .enroll-btn:hover .box { box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
    .enroll-btn:hover .box::before {
      border-color: #d8000d; height: 100%; transform: translateX(0);
      transition: .3s transform linear, .3s height linear .3s;
    }
    .enroll-btn:hover .box::after {
      border-color: #d8000d; height: 100%; transform: translateX(0);
      transition: .3s transform linear, .3s height linear .5s;
    }
    .btn-danger { background-color: #d8000d !important; border-color: #d8000d !important; }
    .btn-danger:hover { background-color: #d8000d !important; border-color: #d8000d !important; }
    .hero-header .navbar { padding-top: 1rem !important; padding-bottom: 1rem !important; }
    .hero-header .navbar-brand { padding: 0.25rem !important; margin: 0 !important; }
    .hero-header .navbar-toggler { padding: 0.5rem !important; box-shadow: none !important; outline: none !important; }
    .hero-header .navbar-toggler:focus { box-shadow: none !important; }
    .hero-header .nav-link { padding: 0 !important; }
    .hero-header .nav-link:hover { color: #fff !important; }

    .page-hero { position: relative; min-height: 50vh; display: flex; align-items: center; }
    .page-hero-bg { position: absolute; inset: 0; z-index: -10; }
    .page-hero-bg img { width: 100%; height: 100%; object-fit: cover; filter: brightness(1.05); }
    .page-hero-overlay { position: absolute; inset: 0; background: linear-gradient(to right, rgba(0,0,0,0.75), rgba(0,0,0,0.45)); }

    .filter-toggle { display: none; background: #fff; border: 1px solid #dee2e6; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; min-height:44px; width:100%; text-align:left; }
    #filterChevron { transition: transform 0.25s ease; }
    .filter-toggle:hover { border-color: #d8000d; color: #d8000d; }
    .pill-nav { display: flex; flex-wrap: wrap; gap: 0.5rem; }
    .pill-nav button { padding: 0.6rem 1.5rem; border: 1px solid #dee2e6; background: #fff; font-weight: 600; font-size: 0.85rem; transition: all 0.2s; cursor: pointer; }
    .pill-nav button:hover { border-color: #d8000d; color: #d8000d; }
    .pill-nav button.active { background: #d8000d; border-color: #d8000d; color: #fff; }

    .course-card-modern { background: #fff; border-bottom: 3px solid #e9ecef; transition: all 0.3s; position: relative; display: flex; flex-direction: column; height: 100%; }
    .course-card-modern:hover { border-bottom-color: #d8000d; box-shadow: 0 8px 30px rgba(0,0,0,0.06); transform: translateY(-2px); }

    .course-card-modern .card-img { height: 180px; object-fit: cover; width: 100%; flex-shrink: 0; }
    .course-card-modern .card-body-content { flex: 1; }
    .tag-modern { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 0.25rem 0.75rem; display: inline-block; }
    .stat-item { display: flex; align-items: center; gap: 0.35rem; font-size: 0.8rem; color: #64748b; }


    .search-box { border: 1px solid #e9ecef; padding: 0.75rem 1.25rem; font-size: 0.9rem; width: 100%; max-width: 400px; outline: none; transition: border 0.2s; }
    .search-box:focus { border-color: #d8000d; }
    .hero-content { padding-top: 76px; }
    .py-20 { padding-top: 5rem; padding-bottom: 5rem; }

    .footer-link { color: inherit; text-decoration: none; transition: opacity 0.2s; }
    .footer-link:hover { opacity: 0.8; text-decoration: underline; text-underline-offset: 3px; }
    .social-icon { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; transition: all 0.3s; }
    .social-icon:hover { background: #d8000d !important; border-color: #d8000d !important; color: #fff !important; }
    .footer-heading { font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 1.25rem; }
    .footer-bottom { border-top: 1px solid; padding-top: 1.5rem; margin-top: 2.5rem; }

    @media (max-width: 575.98px) {
      #courseList { margin-left: 0; margin-right: 0; }
      #courseList > div { flex: 0 0 100%; max-width: 100%; padding-left: 0.75rem; padding-right: 0.75rem; }
      .filter-toggle { display: block; }
      .pill-nav { display: none; flex-direction: column; gap: 0.35rem; }
      .pill-nav.open { display: flex; }
      .pill-nav button { width: 100%; text-align: left; white-space: normal; }
      .search-box { max-width: 100% !important; width: 100% !important; font-size: 0.8rem; padding: 0.5rem 0.85rem; }
    }
    @media (min-width: 576px) and (max-width: 767px) {
      .pill-nav { flex-direction: row; overflow-x: auto; flex-wrap: nowrap; -webkit-overflow-scrolling: touch; scrollbar-width: none; gap: 0.4rem; padding-bottom: 4px; }
      .pill-nav::-webkit-scrollbar { display: none; }
      .pill-nav button { white-space: nowrap; flex-shrink: 0; min-height: 44px; }
    }
    @media (max-width: 767px) {
      .hero-header .navbar-collapse {
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin-top: 1rem;
        padding: 1.5rem 1rem;
        border-radius: 0.5rem;
      }
      .hero-header nav { padding-left: 16px !important; padding-right: 16px !important; }
      .page-hero { min-height: 40vh; }
      .page-hero h1 { font-size: 2rem !important; }
      .container { padding-left: 20px; padding-right: 20px; }
      .footer-bottom { flex-direction: column; text-align: center; gap: 0.75rem; }
      footer .col-6 { flex: 0 0 100%; max-width: 100%; }
      footer .row > div { margin-bottom: 1.5rem; }
      footer .row > div:last-child { margin-bottom: 0; }
      footer ul[style*="column-count"] { column-count: 1 !important; }
      footer > div { padding-top: 2.5rem !important; padding-bottom: 1.5rem !important; }
      footer .footer-bottom .d-flex.gap-3 { flex-direction: column; gap: 0.25rem; align-items: center; }
      footer .opacity-50 { display: none; }
      footer { text-align: center; }
      footer .d-flex { justify-content: center; }

      .py-20 { padding-top: 3rem !important; padding-bottom: 3rem !important; }
      body { overflow-x: hidden; }
      html { overflow-x: hidden; }
      img { max-width: 100%; height: auto; }
      .search-box { display: none !important; }
      [data-aos="fade-left"] { transform: translateX(0) !important; }
      [data-aos="fade-right"] { transform: translateX(0) !important; }
    }
    @media (min-width: 768px) and (max-width: 1023px) {
      .py-20 { padding-top: 4rem !important; padding-bottom: 4rem !important; }
      .container { padding-left: 32px; padding-right: 32px; }
      body { overflow-x: hidden; }
      html { overflow-x: hidden; }
      img { max-width: 100%; height: auto; }

      [data-aos="fade-left"] { transform: translateX(0) !important; }
      [data-aos="fade-right"] { transform: translateX(0) !important; }
      .hero-header nav { padding-left: 16px !important; padding-right: 16px !important; }
    }
    .small-hover:hover { opacity: 1 !important; }
    .hover-bg-light:hover { background-color: #f8f9fa !important; }
    .py-20 { padding-top: 5rem; padding-bottom: 5rem; }
    @media (min-width: 576px) {
      .py-sm-28 { padding-top: 7rem; padding-bottom: 7rem; }
    }
    .text-balance { text-wrap: balance; }
    .transition-all { transition-property: all; }
    .duration-300 { transition-duration: 300ms; }
    .page-loader {
      position: fixed; inset: 0; z-index: 9999;
      display: flex; align-items: center; justify-content: center;
      background: #fff; transition: opacity 0.35s ease;
    }
    .page-loader.hidden { opacity: 0; pointer-events: none; }
    .banter-loader {
      position: relative;
      width: 72px; height: 72px;
    }
    .banter-loader__box {
      float: left; position: relative;
      width: 20px; height: 20px; margin-right: 6px;
    }
    .banter-loader__box:before {
      content: "";
      position: absolute; left: 0; top: 0;
      width: 100%; height: 100%;
      background: #d8000d;
    }
    .banter-loader__box:nth-child(3n) { margin-right: 0; margin-bottom: 6px; }
    .banter-loader__box:nth-child(1):before,
    .banter-loader__box:nth-child(4):before { margin-left: 26px; }
    .banter-loader__box:nth-child(3):before { margin-top: 52px; }
    .banter-loader__box:last-child { margin-bottom: 0; }
    @keyframes moveBox-1 {
      9.0909090909% { transform: translate(-26px, 0); }
      18.1818181818% { transform: translate(0px, 0); }
      27.2727272727% { transform: translate(0px, 0); }
      36.3636363636% { transform: translate(26px, 0); }
      45.4545454545% { transform: translate(26px, 26px); }
      54.5454545455% { transform: translate(26px, 26px); }
      63.6363636364% { transform: translate(26px, 26px); }
      72.7272727273% { transform: translate(26px, 0px); }
      81.8181818182% { transform: translate(0px, 0px); }
      90.9090909091% { transform: translate(-26px, 0px); }
      100% { transform: translate(0px, 0px); }
    }
    .banter-loader__box:nth-child(1) { animation: moveBox-1 4s infinite; }
    @keyframes moveBox-2 {
      9.0909090909% { transform: translate(0, 0); }
      18.1818181818% { transform: translate(26px, 0); }
      27.2727272727% { transform: translate(0px, 0); }
      36.3636363636% { transform: translate(26px, 0); }
      45.4545454545% { transform: translate(26px, 26px); }
      54.5454545455% { transform: translate(26px, 26px); }
      63.6363636364% { transform: translate(26px, 26px); }
      72.7272727273% { transform: translate(26px, 26px); }
      81.8181818182% { transform: translate(0px, 26px); }
      90.9090909091% { transform: translate(0px, 26px); }
      100% { transform: translate(0px, 0px); }
    }
    .banter-loader__box:nth-child(2) { animation: moveBox-2 4s infinite; }
    @keyframes moveBox-3 {
      9.0909090909% { transform: translate(-26px, 0); }
      18.1818181818% { transform: translate(-26px, 0); }
      27.2727272727% { transform: translate(0px, 0); }
      36.3636363636% { transform: translate(-26px, 0); }
      45.4545454545% { transform: translate(-26px, 0); }
      54.5454545454% { transform: translate(-26px, 0); }
      63.6363636364% { transform: translate(-26px, 0); }
      72.7272727273% { transform: translate(-26px, 0); }
      81.8181818182% { transform: translate(-26px, -26px); }
      90.9090909091% { transform: translate(0px, -26px); }
      100% { transform: translate(0px, 0px); }
    }
    .banter-loader__box:nth-child(3) { animation: moveBox-3 4s infinite; }
    @keyframes moveBox-4 {
      9.0909090909% { transform: translate(-26px, 0); }
      18.1818181818% { transform: translate(-26px, 0); }
      27.2727272727% { transform: translate(-26px, -26px); }
      36.3636363636% { transform: translate(0px, -26px); }
      45.4545454545% { transform: translate(0px, 0px); }
      54.5454545454% { transform: translate(0px, -26px); }
      63.6363636364% { transform: translate(0px, -26px); }
      72.7272727273% { transform: translate(0px, -26px); }
      81.8181818182% { transform: translate(-26px, -26px); }
      90.9090909091% { transform: translate(-26px, 0px); }
      100% { transform: translate(0px, 0px); }
    }
    .banter-loader__box:nth-child(4) { animation: moveBox-4 4s infinite; }
    @keyframes moveBox-5 {
      9.0909090909% { transform: translate(0, 0); }
      18.1818181818% { transform: translate(0, 0); }
      27.2727272727% { transform: translate(0, 0); }
      36.3636363636% { transform: translate(26px, 0); }
      45.4545454545% { transform: translate(26px, 0); }
      54.5454545454% { transform: translate(26px, 0); }
      63.6363636364% { transform: translate(26px, 0); }
      72.7272727273% { transform: translate(26px, 0); }
      81.8181818182% { transform: translate(26px, -26px); }
      90.9090909091% { transform: translate(0px, -26px); }
      100% { transform: translate(0px, 0px); }
    }
    .banter-loader__box:nth-child(5) { animation: moveBox-5 4s infinite; }
    @keyframes moveBox-6 {
      9.0909090909% { transform: translate(0, 0); }
      18.1818181818% { transform: translate(-26px, 0); }
      27.2727272727% { transform: translate(-26px, 0); }
      36.3636363636% { transform: translate(0px, 0); }
      45.4545454545% { transform: translate(0px, 0); }
      54.5454545454% { transform: translate(0px, 0); }
      63.6363636364% { transform: translate(0px, 0); }
      72.7272727273% { transform: translate(0px, 26px); }
      81.8181818182% { transform: translate(-26px, 26px); }
      90.9090909091% { transform: translate(-26px, 0px); }
      100% { transform: translate(0px, 0px); }
    }
    .banter-loader__box:nth-child(6) { animation: moveBox-6 4s infinite; }
    @keyframes moveBox-7 {
      9.0909090909% { transform: translate(26px, 0); }
      18.1818181818% { transform: translate(26px, 0); }
      27.2727272727% { transform: translate(26px, 0); }
      36.3636363636% { transform: translate(0px, 0); }
      45.4545454545% { transform: translate(0px, -26px); }
      54.5454545454% { transform: translate(26px, -26px); }
      63.6363636364% { transform: translate(0px, -26px); }
      72.7272727273% { transform: translate(0px, -26px); }
      81.8181818182% { transform: translate(0px, 0px); }
      90.9090909091% { transform: translate(26px, 0px); }
      100% { transform: translate(0px, 0px); }
    }
    .banter-loader__box:nth-child(7) { animation: moveBox-7 4s infinite; }
    @keyframes moveBox-8 {
      9.0909090909% { transform: translate(0, 0); }
      18.1818181818% { transform: translate(-26px, 0); }
      27.2727272727% { transform: translate(-26px, -26px); }
      36.3636363636% { transform: translate(0px, -26px); }
      45.4545454545% { transform: translate(0px, -26px); }
      54.5454545454% { transform: translate(0px, -26px); }
      63.6363636364% { transform: translate(0px, -26px); }
      72.7272727273% { transform: translate(0px, -26px); }
      81.8181818182% { transform: translate(26px, -26px); }
      90.9090909091% { transform: translate(26px, 0px); }
      100% { transform: translate(0px, 0px); }
    }
    .banter-loader__box:nth-child(8) { animation: moveBox-8 4s infinite; }
    @keyframes moveBox-9 {
      9.0909090909% { transform: translate(-26px, 0); }
      18.1818181818% { transform: translate(-26px, 0); }
      27.2727272727% { transform: translate(0px, 0); }
      36.3636363636% { transform: translate(-26px, 0); }
      45.4545454545% { transform: translate(0px, 0); }
      54.5454545454% { transform: translate(0px, 0); }
      63.6363636364% { transform: translate(-26px, 0); }
      72.7272727273% { transform: translate(-26px, 0); }
      81.8181818182% { transform: translate(-52px, 0); }
      90.9090909091% { transform: translate(-26px, 0); }
      100% { transform: translate(0px, 0); }
    }
    .banter-loader__box:nth-child(9) { animation: moveBox-9 4s infinite; }
  </style>
</head>
<body>
<div id="pageLoader" class="page-loader">
  <div class="banter-loader">
    <div class="banter-loader__box"></div><div class="banter-loader__box"></div><div class="banter-loader__box"></div>
    <div class="banter-loader__box"></div><div class="banter-loader__box"></div><div class="banter-loader__box"></div>
    <div class="banter-loader__box"></div><div class="banter-loader__box"></div><div class="banter-loader__box"></div>
  </div>
</div>

<header class="hero-header">
  <nav class="navbar navbar-expand-lg navbar-dark px-4 px-lg-5 py-3" aria-label="Global">
    <a href="/" class="navbar-brand p-1">
        <img src="images/logo.png" srcset="images/logo.png 1x, images/logo_2x.png 2x" alt="CADDFE" height="48" style="filter:brightness(1.2)" loading="eager" width="180">
      </a>

    <button class="navbar-toggler border-0 p-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" width="24" height="24">
        <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-4">
        <li class="nav-item">
          <a class="nav-link text-white fw-semibold small-hover" style="opacity:0.9;" href="/">Home</a>
        </li>
          <li class="nav-item dropdown-nav">
            <a class="nav-link text-white fw-semibold small-hover d-flex align-items-center gap-1" style="opacity:0.9;" href="#" onclick="event.preventDefault();this.parentElement.classList.toggle('show');" id="coursesToggle">Courses</a>
            <div class="mega-dropdown">
              <div class="mega-inner">
                <a href="courses.php?cat=diploma" class="mega-card">
                  <div class="mega-icon" style="background:#fef2f2;color:#d8000d;"><i class="bi bi-mortarboard"></i></div>
                  <h4>Diploma Programs</h4>
                  <p class="mega-desc">Architectural &amp; Interior Design diplomas with hands-on training</p>
                  <span class="mega-arrow">Browse Courses &rarr;</span>
                </a>
                <a href="courses.php?cat=bim" class="mega-card">
                  <div class="mega-icon" style="background:#eff6ff;color:#2563eb;"><i class="bi bi-cpu"></i></div>
                  <h4>BIM Programs</h4>
                  <p class="mega-desc">Industry-aligned BIM certification courses for modern careers</p>
                  <span class="mega-arrow">Browse Courses &rarr;</span>
                </a>
              </div>
            </div>
          </li>
        <li class="nav-item">
            <a class="nav-link text-white fw-semibold small-hover" style="opacity:0.9;" href="/services">Services</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white fw-semibold small-hover" style="opacity:0.9;" href="/projects">Projects</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white fw-semibold small-hover" style="opacity:0.9;" href="/contact_us">Contact Us</a>
        </li>
      </ul>
      <div class="d-lg-none pb-3">
        <a href="/enroll" class="btn btn-danger w-100 fw-semibold py-3 rounded-0 d-flex align-items-center justify-content-center gap-2">
          Enroll Now <i class="bi bi-arrow-right"></i>
        </a>
      </div>
      <div class="d-none d-lg-flex align-items-center ms-lg-3">
        <button class="enroll-btn text-white" onclick="window.location.href='/enroll'">
          <span class="box">Enroll Now &rarr;</span>
        </button>
      </div>
    </div>
  </nav>
</header>

<div class="page-hero hero-content">
  <div class="page-hero-bg">
    <picture>
      <source srcset="images/hero3-960.webp 960w, images/hero3-1920.webp 1920w" type="image/webp" sizes="100vw" />
      <source srcset="images/hero3-960.jpg 960w, images/hero3-1920.jpg 1920w" type="image/jpeg" sizes="100vw" />
      <img src="images/hero3-1920.jpg" alt="CADDFE Training Services" width="1920" height="1080" loading="eager" fetchpriority="high" />
    </picture>
    <div class="page-hero-overlay"></div>
  </div>
  <div class="container text-center text-white position-relative">
    <h1 class="fw-bold" style="font-size:2.5rem;letter-spacing:-0.025em;">Pick Your Preferred Courses</h1>
    <p class="text-white-50 mt-2 mb-0" style="max-width:500px;margin:0 auto;">Browse, search, and select the programs that match your career goals.</p>
  </div>
</div>

<section class="py-20">
  <div class="container" style="max-width:80rem;">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
      <div class="d-flex flex-column gap-2" style="min-width:0;">
        <button class="filter-toggle py-2 px-3" id="filterToggle" onclick="toggleFilters()"><i class="bi bi-funnel me-2"></i>Filters <i class="bi bi-chevron-down float-end" id="filterChevron"></i></button>
        <div class="pill-nav" id="pillNav">
          <button class="active" data-filter="all">All</button>
          <button data-filter="architecture">Architecture</button>
          <button data-filter="interior">Interior</button>
          <button data-filter="bim">BIM</button>
          <button data-filter="civil">Civil</button>
        </div>
      </div>
      <input class="search-box" type="text" placeholder="Search courses..." id="searchInput" oninput="filterCourses()" />
    </div>

    <h2 class="visually-hidden">Available Courses</h2>
    <div class="row g-4" id="courseList">
    </div>
  </div>
</section>

<footer style="background:#0f172a;color:#cbd5e1;">
  <div style="padding:4rem 0 2.5rem;">
    <div class="container" style="max-width:80rem;">
      <div class="row g-5">
        <div class="col-lg-4">
          <div class="d-flex align-items-center gap-2 mb-3">
            <img src="images/logo.png" alt="CADDFE" height="38" style="filter:brightness(1.2)" loading="lazy">
            <!-- <span class="fw-bold text-white fs-5">CADDFE</span> -->
          </div>
          <p class="small" style="line-height:1.7;">CADDFE Training Services bridges the gap between academic learning and industry demands through hands-on Civil CAD training and professional architectural design services.</p>
          <div class="d-flex gap-2 mt-3">
            <a href="#" class="social-icon border rounded" style="border-color:#334155!important;color:#94a3b8;" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
            <a href="#" class="social-icon border rounded" style="border-color:#334155!important;color:#94a3b8;" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
            <a href="#" class="social-icon border rounded" style="border-color:#334155!important;color:#94a3b8;" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" class="social-icon border rounded" style="border-color:#334155!important;color:#94a3b8;" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <h3 class="footer-heading text-white">Quick Links</h3>
          <ul class="list-unstyled small d-flex flex-column gap-2">
            <li><a href="index.php" class="footer-link">Home</a></li>
            <li><a href="courses.php" class="footer-link">Programs</a></li>
            <li><a href="#" class="footer-link">About Us</a></li>
            <li><a href="contact_us.php" class="footer-link">Contact</a></li>
            <li><a href="enroll.php" class="footer-link">Enroll Now</a></li>
          </ul>
        </div>
        <div class="col-6 col-lg-3">
          <h3 class="footer-heading text-white">Programs</h3>
          <ul class="list-unstyled small" style="column-count:2;display:block!important;">
            <li style="break-inside:avoid;margin-bottom:0.25rem;"><a href="courses.php?course=bim-ready-post-graduation" class="footer-link">BIM-Ready+ Post Graduation</a></li>
            <li style="break-inside:avoid;margin-bottom:0.25rem;"><a href="courses.php?course=bim-ready-architecture-advanced" class="footer-link">BIM-Ready Architecture Advanced</a></li>
            <li style="break-inside:avoid;margin-bottom:0.25rem;"><a href="courses.php?course=bim-ready-civil" class="footer-link">BIM-Ready Civil Course</a></li>
            <li style="break-inside:avoid;margin-bottom:0.25rem;"><a href="courses.php?course=michigan-state-university-bim" class="footer-link">MSU Certification in BIM</a></li>
            <li style="break-inside:avoid;margin-bottom:0.25rem;"><a href="courses.php?course=building-smart-bim" class="footer-link">Building - SMART Certification</a></li>
            <li style="break-inside:avoid;margin-bottom:0.25rem;"><a href="courses.php?course=master-diploma-architectural-design" class="footer-link">Master Diploma in Architectural Design</a></li>
            <li style="break-inside:avoid;margin-bottom:0.25rem;"><a href="courses.php?course=advanced-diploma-architectural-design" class="footer-link">Advanced Diploma in Architectural Design</a></li>
            <li style="break-inside:avoid;margin-bottom:0.25rem;"><a href="courses.php?course=diploma-architectural-design" class="footer-link">Diploma in Architectural Design</a></li>
            <li style="break-inside:avoid;margin-bottom:0.25rem;"><a href="courses.php?course=master-diploma-interior-design" class="footer-link">Master Diploma in Interior Design</a></li>
            <li style="break-inside:avoid;margin-bottom:0.25rem;"><a href="courses.php?course=advanced-diploma-interior-design" class="footer-link">Advanced Diploma in Interior Design</a></li>
            <li style="break-inside:avoid;margin-bottom:0.25rem;"><a href="courses.php?course=diploma-interior-design" class="footer-link">Diploma in Interior Design</a></li>
          </ul>
        </div>
        <div class="col-lg-3">
          <h3 class="footer-heading text-white">Contact</h3>
          <ul class="list-unstyled small d-flex flex-column gap-3">
            <li class="d-flex gap-2">
              <i class="bi bi-geo-alt text-danger mt-1"></i>
              <span>No:23, Thiruvasagam Street,<br />Avadi, Chennai - 600072</span>
            </li>
            <li class="d-flex gap-2">
              <i class="bi bi-telephone text-danger mt-1"></i>
              <a href="tel:+919952403574" class="footer-link">+91 99524 03574</a>
            </li>
            <li class="d-flex gap-2">
              <i class="bi bi-envelope text-danger mt-1"></i>
              <a href="mailto:Caddfe90@gmail.com" class="footer-link">Caddfe90@gmail.com</a>
            </li>
            <li class="d-flex gap-2">
              <i class="bi bi-clock text-danger mt-1"></i>
              <span>Mon – Sat: 9 AM – 8 PM</span>
            </li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-2" style="border-color:#1e293b;">
        <p class="small mb-0">&copy; 2026 CADDFE. All rights reserved.</p>
        <div class="d-flex gap-3">
          <a href="https://www.thecircledesigns.com" target="_blank" class="footer-link small">Design and Developed by Circle Designs</a>
          <span class="text-muted small opacity-50">|</span>
          <a href="admin/" target="_blank" class="footer-link small text-muted opacity-50">Admin</a>
        </div>
      </div>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script>
(function(){
  var header = document.querySelector('.hero-header');
  if (header) {
    var ticking = false;
    function update(){
      if (window.scrollY > 10) header.classList.add('scrolled');
      else header.classList.remove('scrolled');
      ticking = false;
    }
    window.addEventListener('scroll', function(){
      if (!ticking) { requestAnimationFrame(update); ticking = true; }
    });
    update();
  }
})();
</script>
<script>
const coursesData = [
  { id: 1, name: 'Master Diploma in Architectural Design', category: 'architecture', hrs: 370, levels: 3, tag: 'Master', tagColor: 'danger', img: 'images/courses_first/cad-course-clean-01.jpg', levelsDetail: 'Level 1: 2D Architectural Presentation<br>Level 2: Advanced 3D Modelling<br>Level 3: Advanced Architectural Visualisation' },
  { id: 2, name: 'Advanced Diploma in Architectural Design', category: 'architecture', hrs: 200, levels: 2, tag: 'Advanced', tagColor: 'warning text-dark', img: 'images/courses_first/cad-course-clean-02.jpg', levelsDetail: 'Level 1: Advanced 3D Modelling<br>Level 2: Advanced Architectural Visualisation' },
  { id: 3, name: 'Diploma in Architectural Design', category: 'architecture', hrs: 100, levels: 3, tag: 'Diploma', tagColor: 'info text-dark', img: 'images/courses_first/cad-course-clean-03.jpg', levelsDetail: 'Level 1: Basic 2D Drafting<br>Level 2: Basic 3D Modelling<br>Level 3: Basic Architectural Visualisation' },
  { id: 4, name: 'Master Diploma in Interior Design', category: 'interior', hrs: 250, levels: 3, tag: 'Master', tagColor: 'danger', img: 'images/courses_first/cad-course-clean-04.jpg', levelsDetail: 'Level 1: 2D Space Planning<br>Level 2: 3D Modelling<br>Level 3: Architectural Visualisation' },
  { id: 5, name: 'Advanced Diploma in Interior Design', category: 'interior', hrs: 160, levels: 3, tag: 'Advanced', tagColor: 'warning text-dark', img: 'images/courses_first/cad-course-clean-05.jpg', levelsDetail: 'Level 1: Basic 2D Drafting<br>Level 2: Advanced 3D Modelling (Interior)<br>Level 3: Advanced Architectural Visualisation (Interior)' },
  { id: 6, name: 'Diploma in Interior Design', category: 'interior', hrs: 70, levels: 2, tag: 'Diploma', tagColor: 'info text-dark', img: 'images/courses_first/cad-course-clean-06.jpg', levelsDetail: 'Level 1: Basic 3D Modelling (Interior)<br>Level 2: Basic Architectural Visualisation (Interior)' },
  { id: 7, name: 'BIM-Ready+ International Post Graduation Certification in BIM Management', category: 'bim', duration: '10 Months', hrs: 200, modules: 10, assessments: 8, tag: 'Post Graduate', tagColor: 'dark', img: 'images/courses_first/cad-course-clean-07.jpg' },
  { id: 8, name: 'BIM-Ready Architecture Advanced', category: 'bim', duration: '8 Months', hrs: 160, modules: 14, assessments: 5, tag: 'Architecture', tagColor: 'primary', img: 'images/courses_first/cad-course-clean-08.jpg' },
  { id: 9, name: 'BIM-Ready Civil Course', category: 'civil', duration: '6 Months', hrs: 120, modules: 6, assessments: 3, tag: 'Civil', tagColor: 'success', img: 'images/courses_first/cad-course-clean-09.jpg' },
  { id: 10, name: 'Michigan State University Certification Program in BIM', category: 'bim', duration: '5 Months', hrs: 100, modules: 11, assessments: 3, tag: 'University Program', tagColor: 'primary', img: 'images/courses_first/cad-course-clean-10.jpg' },
  { id: 11, name: 'BIM-Ready Complete \u2013 International Certification in BIM Modeling & Coordination', category: 'bim', duration: '6 Months', hrs: 120, modules: 9, assessments: 3, tag: 'Professional', tagColor: 'success', img: 'images/courses_first/cad-course-clean-11.jpg' },
  { id: 12, name: 'Building - SMART BIM Professional Certification', category: 'bim', duration: '10 Days', hrs: 10, modules: 6, assessments: 6, tag: 'Professional', tagColor: 'success', img: 'images/courses_first/cad-course-clean-12.jpg' },
];

let whatsappWin = null;

function openWhatsAppEnquiry(el) {
  var name = decodeURIComponent(el.getAttribute('data-course'));
  var text = "I'm interested in enrolling for " + name + " at CADDFE. Please share more details.";
  var encoded = encodeURIComponent(text);
  if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    window.open('https://wa.me/919952403574?text=' + encoded, 'whatsapp');
  } else if (whatsappWin && !whatsappWin.closed) {
    whatsappWin.location.href = 'https://web.whatsapp.com/send?phone=919952403574&text=' + encoded;
    whatsappWin.focus();
  } else {
    whatsappWin = window.open('https://web.whatsapp.com/send?phone=919952403574&text=' + encoded, 'whatsapp');
  }
}
var urlParams = new URLSearchParams(window.location.search);
var catParam = urlParams.get('cat');
var currentFilter = catParam || 'all';

function renderCourses(filter, query) {
  if (filter === undefined) filter = currentFilter;
  if (query === undefined) query = '';
  const container = document.getElementById('courseList');
  let filtered = coursesData;
  if (filter !== 'all') {
    if (filter === 'diploma') {
      filtered = filtered.filter(c => c.category === 'architecture' || c.category === 'interior');
    } else if (filter === 'bim') {
      filtered = filtered.filter(c => c.category === 'bim' || c.category === 'civil');
    } else {
      filtered = filtered.filter(c => c.category === filter);
    }
  }
  if (query) filtered = filtered.filter(c => c.name.toLowerCase().includes(query.toLowerCase()));
  container.innerHTML = filtered.map(c => {
    var detailsHtml = '';
    if (c.levelsDetail) {
      detailsHtml = '<span class="stat-item"><i class="bi bi-layers"></i> ' + c.levels + ' Level' + (c.levels > 1 ? 's' : '') + '</span>';
    }
    if (c.modules) {
      detailsHtml = '<span class="stat-item"><i class="bi bi-journal-text"></i> ' + c.modules + ' Modules</span><span class="stat-item"><i class="bi bi-pencil-square"></i> ' + c.assessments + ' Assessments</span>';
    }
    var durationHtml = c.duration ? '<span class="stat-item"><i class="bi bi-calendar-week"></i> ' + c.duration + '</span>' : '';
    return `<div class="col-6 col-sm-6 col-lg-4 col-xl-3 d-flex">
      <div class="course-card-modern rounded-0">
        <img src="${c.img}" alt="${c.name}" class="card-img" loading="lazy" />
        <div class="card-body-content">
          <div class="p-3 card-body-inner">
            <span class="tag-modern badge bg-${c.tagColor} mb-2">${c.tag}</span>
            <h3 class="fw-bold mb-2" style="font-size:0.9rem;">${c.name}</h3>
            <div class="card-details">
              <div class="d-flex flex-wrap gap-3">
                <span class="stat-item"><i class="bi bi-clock"></i> ${c.hrs} hrs</span>
                ${durationHtml}
                ${detailsHtml}
              </div>
              ${c.levelsDetail ? '<div class="small text-secondary mt-2" style="line-height:1.4;">' + c.levelsDetail + '</div>' : ''}
            </div>
          </div>
        </div>
        <div class="p-3 border-top mt-auto">
          <a href="#" class="btn btn-sm btn-success w-100 rounded-0 fw-semibold" onclick="openWhatsAppEnquiry(this); return false;" data-course="${encodeURIComponent(c.name)}">
            <i class="bi bi-whatsapp me-1"></i> Enquire on WhatsApp
          </a>
        </div>
      </div>
    </div>`;
  }).join('');
}

function getActiveFilter() {
  if (catParam) return catParam;
  var active = document.querySelector('#pillNav .active');
  return active ? active.dataset.filter : 'all';
}

function filterCourses() {
  renderCourses(getActiveFilter(), document.getElementById('searchInput').value);
}

function toggleFilters() {
  var nav = document.getElementById('pillNav');
  nav.classList.toggle('open');
  document.getElementById('filterChevron').style.transform = nav.classList.contains('open') ? 'rotate(180deg)' : '';
}

document.addEventListener('click', function(e) {
  if (window.innerWidth > 575.98) return;
  var btn = e.target.closest('#pillNav button');
  if (!btn) return;
  document.getElementById('pillNav').classList.remove('open');
  document.getElementById('filterChevron').style.transform = '';
});

document.querySelectorAll('#pillNav button').forEach(function(btn) {
  btn.addEventListener('click', function() {
    catParam = null;
    currentFilter = this.dataset.filter;
    document.querySelectorAll('#pillNav button').forEach(function(b) { b.classList.remove('active'); });
    this.classList.add('active');
    filterCourses();
  });
});

if (catParam) {
  document.querySelectorAll('#pillNav button').forEach(function(b) { b.classList.remove('active'); });
  var match = document.querySelector('#pillNav button[data-filter="' + catParam + '"]');
  if (match) match.classList.add('active');
}
renderCourses(currentFilter);

(function(){
  var loader = document.getElementById('pageLoader');
  if (!loader) return;
  function show(){ loader.classList.remove('hidden'); }
  function hide(){ loader.classList.add('hidden'); }
  window.addEventListener('pageshow', hide);
  document.addEventListener('click', function(e){
    var a = e.target.closest('a');
    if (!a || a.hostname !== location.hostname) return;
    var h = a.getAttribute('href');
    if (!h || h === '#' || h.charAt(0) === '#' || a.hasAttribute('download') || a.hasAttribute('data-bs-toggle')) return;
    show();
  });
  document.addEventListener('click', function(e){
    if (!e.target.closest('.dropdown-nav')) {
      document.querySelectorAll('.dropdown-nav.show').forEach(function(el){ el.classList.remove('show'); });
    }
  });
})();
</script>
</body>
</html>
