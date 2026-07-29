<?php
require_once __DIR__ . '/config/db_connect.php';
require_once __DIR__ . '/config/mail_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$form_submitted = false;
$form_errors = [];

// Post/Redirect/Get: show success from session flag
$show_success_modal = false;
if (isset($_SESSION['contact_sent'])) {
    $show_success_modal = true;
    unset($_SESSION['contact_sent']);
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function safe_truncate(string $value, int $length): string
{
    if (function_exists('mb_substr')) {
        return mb_substr($value, 0, $length);
    }
    return substr($value, 0, $length);
}

function getClientIP(): string
{
    $candidates = [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_REAL_IP',
        'REMOTE_ADDR',
    ];
    foreach ($candidates as $key) {
        $value = $_SERVER[$key] ?? '';
        if ($value === '') {
            continue;
        }
        if (strpos($value, ',') !== false) {
            $value = trim(explode(',', $value)[0]);
        }
        if (filter_var($value, FILTER_VALIDATE_IP)) {
            return $value;
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $form_submitted = true;

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $form_errors[] = 'Invalid or expired form. Please reload the page and try again.';
        error_log('Contact form CSRF token mismatch');
    }

    if (empty($form_errors) && isset($_SESSION['last_contact_time']) && time() - $_SESSION['last_contact_time'] < 30) {
        $form_errors[] = 'You are submitting too quickly. Please wait a moment.';
    }

    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $subject   = trim($_POST['subject'] ?? '');
    $message   = trim($_POST['message'] ?? '');
    $honeypot  = trim($_POST['website'] ?? '');

    if ($honeypot !== '') {
        $form_errors[] = 'Bot detected.';
        error_log('Contact form honeypot triggered');
    }

    if ($full_name === '') {
        $form_errors[] = 'Full name is required.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($full_name) : strlen($full_name)) < 2 || (function_exists('mb_strlen') ? mb_strlen($full_name) : strlen($full_name)) > 100) {
        $form_errors[] = 'Full name must be between 2 and 100 characters.';
    }

    if ($email === '') {
        $form_errors[] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $form_errors[] = 'Please enter a valid email address.';
    }

    if ($phone !== '' && !preg_match('/^[+\d][\d\s\-().]{6,20}$/', $phone)) {
        $form_errors[] = 'Please enter a valid phone number.';
    }

    $valid_subjects = ['General Enquiry', 'Course Info', 'Service Request'];
    if ($subject === '' || $subject === 'Subject') {
        $form_errors[] = 'Please select a subject.';
    } elseif (!in_array($subject, $valid_subjects, true)) {
        $form_errors[] = 'Invalid subject selected.';
    }

    if ($message === '') {
        $form_errors[] = 'Message is required.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($message) : strlen($message)) < 10) {
        $form_errors[] = 'Message must be at least 10 characters.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($message) : strlen($message)) > 5000) {
        $form_errors[] = 'Message must not exceed 5000 characters.';
    }

    if (empty($form_errors)) {
        try {
            if ($pdo === null) {
                throw new \RuntimeException('Database connection not available');
            }
            $stmt = $pdo->prepare('INSERT INTO contact_submissions (full_name, email, phone, subject, message, ip_address, user_agent) VALUES (:full_name, :email, :phone, :subject, :message, :ip_address, :user_agent)');
            $stmt->execute([
                ':full_name'  => safe_truncate($full_name, 255),
                ':email'      => safe_truncate($email, 255),
                ':phone'      => $phone !== '' ? safe_truncate($phone, 50) : null,
                ':subject'    => $subject !== '' ? safe_truncate($subject, 500) : null,
                ':message'    => $message,
                ':ip_address' => getClientIP(),
                ':user_agent' => safe_truncate($_SERVER['HTTP_USER_AGENT'] ?? '', 65535),
            ]);
            $_SESSION['last_contact_time'] = time();

            sendContactEmail([
                'full_name' => $full_name,
                'email'     => $email,
                'phone'     => $phone,
                'subject'   => $subject,
                'message'   => $message,
            ]);

            $redirect = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if (!$redirect) {
                $redirect = $_SERVER['SCRIPT_NAME'];
            }
            $_SESSION['contact_sent'] = true;
            header('Location: ' . $redirect);
            exit;
        } catch (\Throwable $e) {
            $form_errors[] = 'Something went wrong. Please try again later.';
            error_log('Contact form insert failed: ' . $e->getMessage());
        }
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="Content-Security-Policy" content="default-src 'self' https://esm.sh; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://esm.sh; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; img-src 'self' https://images.unsplash.com data:; connect-src 'self' https://cdn.jsdelivr.net https://esm.sh; frame-src 'self' https://www.google.com; object-src 'none';" />
  <meta name="description" content="Get in touch with CADDFE Training Services. Contact us for course enquiries, design services, and career guidance." />
  <link rel="icon" href="images/fav_icon.png" type="image/png" />
  <title>Contact Us - CADDFE Training Services</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin />
  <link rel="preconnect" href="https://esm.sh" crossorigin />
  <link rel="preload" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=optional" as="style" onload="this.onload=null;this.rel='stylesheet'" />
  <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" /></noscript>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
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
    .hero-content { padding-top: 76px; }
    .hero-overlay { position: absolute; inset: 0; background: linear-gradient(to right, rgba(0,0,0,0.8), rgba(0,0,0,0.4)); }
    [data-aos] { opacity: 0; transform: translateY(40px); transition: opacity 0.9s cubic-bezier(0.22, 0.61, 0.36, 1), transform 0.9s cubic-bezier(0.22, 0.61, 0.36, 1); }
    [data-aos].aos-animate { opacity: 1; transform: translateY(0); }



    .footer-link { color: inherit; text-decoration: none; transition: opacity 0.2s; }
    .footer-link:hover { opacity: 0.8; text-decoration: underline; text-underline-offset: 3px; }
    .social-icon { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; transition: all 0.3s; }
    .social-icon:hover { background: #d8000d !important; border-color: #d8000d !important; color: #fff !important; }
    .footer-heading { font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 1.25rem; }
    .footer-bottom { border-top: 1px solid; padding-top: 1.5rem; margin-top: 2.5rem; }

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
      .page-hero { min-height: 40vh; }
      .page-hero h1 { font-size: 2rem !important; }
      .contact-info-card { margin-bottom: 1rem; }
      .contact-info-card:last-child { margin-bottom: 0; }
      .col-md-3 { flex: 0 0 100%; max-width: 100%; margin-bottom: 1rem; }
      .col-md-3:last-child { margin-bottom: 0; }
      .form-control, .form-select { min-height: 44px; }
      .btn, .btn-lg, .btn-sm { min-height: 44px; }
      .d-flex.gap-2.mt-4 { flex-direction: column; }
      .d-flex.gap-2.mt-4 .btn { width: 100%; }
      iframe { height: 250px !important; }
      body { overflow-x: hidden; }
      html { overflow-x: hidden; }
      img { max-width: 100%; height: auto; }
      .col-sm-6 { flex: 0 0 100%; max-width: 100%; margin-bottom: 0; }
      .col-sm-6:last-child { margin-bottom: 0; }
      [data-aos="fade-left"] { transform: translateX(0) !important; }
      [data-aos="fade-right"] { transform: translateX(0) !important; }
    }
    @media (min-width: 768px) and (max-width: 1023px) {
      .container { padding-left: 32px; padding-right: 32px; }
      body { overflow-x: hidden; }
      img { max-width: 100%; height: auto; }
      iframe { height: 280px !important; }
      [data-aos="fade-left"] { transform: translateX(0) !important; }
      [data-aos="fade-right"] { transform: translateX(0) !important; }
      .hero-header nav { padding-left: 16px !important; padding-right: 16px !important; }
      .col-lg-7 { margin-top: 0; }
    }
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
      54.5454545454% { transform: translate(26px, 26px); }
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
            <a class="nav-link text-white fw-semibold small-hover" style="opacity:0.9;" 
href="/services">Services</a>
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
    <img src="images/contact-hero.png" alt="Contact CADDFE" loading="eager" fetchpriority="high" />
    <div class="page-hero-overlay"></div>
  </div>
  <div class="container text-center text-white position-relative">
    <h1 class="fw-bold" style="font-size:2.5rem;letter-spacing:-0.025em;">Get in Touch</h1>
    <p class="text-white-50 mt-2" style="max-width:650px;margin:0 auto;">Have a question, a project, or just want to say hello? Drop us a message.</p>
  </div>
</div>
  <div data-aos="fade-up" class="container pb-5" style="max-width:72rem;margin-top:-3rem;">
    <div class="row g-3 mb-5">
      <div class="col-md-3">
        <div class="bg-white rounded-3 shadow-sm text-center p-4 h-100 border-bottom border-3 border-danger">
        <div style="font-size:2rem;color:#d8000d;" class="mb-2"><i class="bi bi-geo-alt"></i></div>
        <h6 class="fw-bold">Address</h6>
        <p class="small text-secondary mb-0">No:23, Thiruvasagam St, Avadi, Chennai - 600072</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="bg-white rounded-3 shadow-sm text-center p-4 h-100 border-bottom border-3 border-danger">
        <div style="font-size:2rem;color:#d8000d;" class="mb-2"><i class="bi bi-telephone"></i></div>
        <h6 class="fw-bold">Phone</h6>
        <p class="small text-secondary mb-0">+91 99524 03574</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="bg-white rounded-3 shadow-sm text-center p-4 h-100 border-bottom border-3 border-danger">
        <div style="font-size:2rem;color:#d8000d;" class="mb-2"><i class="bi bi-envelope"></i></div>
        <h6 class="fw-bold">Email</h6>
        <p class="small text-secondary mb-0">magendhiran@caddfe.com</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="bg-white rounded-3 shadow-sm text-center p-4 h-100 border-bottom border-3 border-danger">
        <div style="font-size:2rem;color:#d8000d;" class="mb-2"><i class="bi bi-clock"></i></div>
        <h6 class="fw-bold">Hours</h6>
        <p class="small text-secondary mb-0">Mon &ndash; Sat: 9 AM &ndash; 8 PM</p>
      </div>
    </div>
  </div>
  <div class="row g-5">
    <div class="col-lg-7">
      <div class="bg-white p-4 p-lg-5 rounded-3 shadow-sm">
        <h4 class="fw-bold mb-1">Send a Message</h4>
        <p class="small text-secondary mb-4">Fill out the form and we'll respond within 24 hours.</p>
        <form method="POST" action="" novalidate id="contactForm">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
          <div class="row g-3">
            <div class="col-sm-6">
              <div class="form-floating">
                <input type="text" name="full_name" id="full_name" class="form-control rounded-0" placeholder="Full Name" value="<?= htmlspecialchars($_POST['full_name'] ?? '', ENT_QUOTES) ?>" required minlength="2" maxlength="100" />
                <label for="full_name">Full Name <span class="text-danger">*</span></label>
                <div class="invalid-feedback">Please enter your full name (2-100 characters).</div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-floating">
                <input type="email" name="email" id="email" class="form-control rounded-0" placeholder="Email Address" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES) ?>" required />
                <label for="email">Email Address <span class="text-danger">*</span></label>
                <div class="invalid-feedback">Please enter a valid email address.</div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-floating">
                <input type="tel" name="phone" id="phone" class="form-control rounded-0" placeholder="Phone Number" value="<?= htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES) ?>" pattern="[+\d][\d\s\-().]{6,20}" maxlength="21" />
                <label for="phone">Phone Number</label>
                <div class="invalid-feedback">Please enter a valid phone number.</div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-floating">
                <select name="subject" id="subject" class="form-select rounded-0" required>
                  <option value="" <?= empty($_POST['subject']) ? 'selected' : '' ?>>Subject</option>
                  <option value="General Enquiry" <?= ($_POST['subject'] ?? '') === 'General Enquiry' ? 'selected' : '' ?>>General Enquiry</option>
                  <option value="Course Info" <?= ($_POST['subject'] ?? '') === 'Course Info' ? 'selected' : '' ?>>Course Info</option>
                  <option value="Service Request" <?= ($_POST['subject'] ?? '') === 'Service Request' ? 'selected' : '' ?>>Service Request</option>
                </select>
                <label for="subject">Subject <span class="text-danger">*</span></label>
                <div class="invalid-feedback">Please select a subject.</div>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <textarea name="message" id="message" class="form-control rounded-0" rows="5" placeholder="Your message..." required minlength="10" maxlength="5000" style="min-height:140px"><?= htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES) ?></textarea>
                <label for="message">Your message <span class="text-danger">*</span></label>
                <div class="invalid-feedback">Please enter your message (at least 10 characters).</div>
              </div>
            </div>
          </div>
          <div style="position:absolute;left:-9999px" aria-hidden="true">
            <input type="text" name="website" tabindex="-1" autocomplete="off" />
          </div>
          <div class="d-flex gap-2 mt-4">
            <button type="submit" name="contact_submit" class="btn btn-danger fw-semibold rounded-0 px-5 py-2" id="submitBtn">Send <i class="bi bi-send ms-1"></i></button>
            <button type="button" class="btn btn-outline-secondary rounded-0 px-4 py-2" id="resetBtn">Reset</button>
          </div>
        </form>
      </div>

      <div id="successModal" class="modal fade" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true" data-bs-backdrop="false" data-bs-keyboard="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
          <div class="modal-content rounded-0" style="border-top:4px solid #198754;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <div class="modal-body text-center p-5">
              <div class="mb-3" style="font-size:3.5rem;color:#198754;"><i class="bi bi-check-circle-fill"></i></div>
              <h5 class="fw-bold" id="successModalLabel">Message Sent Successfully!</h5>
              <p class="text-secondary small mt-2 mb-4">Thank you for reaching out. We'll get back to you within 24 hours.</p>
              <button type="button" class="btn btn-success rounded-0 fw-semibold px-4" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <div id="errorModal" class="modal fade" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true" data-bs-backdrop="false" data-bs-keyboard="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
          <div class="modal-content rounded-0" style="border-top:4px solid #dc3545;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <div class="modal-body text-center p-5">
              <div class="mb-3" style="font-size:3.5rem;color:#dc3545;"><i class="bi bi-x-circle-fill"></i></div>
              <h5 class="fw-bold" id="errorModalLabel">Something Went Wrong</h5>
              <ul class="text-secondary small mt-2 mb-4 text-start" id="errorList">
              </ul>
              <button type="button" class="btn btn-danger rounded-0 fw-semibold px-4" data-bs-dismiss="modal">Try Again</button>
            </div>
          </div>
        </div>
      </div>

    </div>
    <div class="col-lg-5">
      <div class="bg-white rounded-3 shadow-sm overflow-hidden">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3885.5!2d80.1!3d13.1!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTPCsDA2JzAwLjAiTiA4MMKwMDYnMDAuMCJF!5e0!3m2!1sen!2sin!4v1" width="100%" height="320" style="border:0;display:block;" allowfullscreen loading="lazy"></iframe>
        <div class="p-3">
          <div class="d-flex justify-content-between align-items-center">
            <span class="small text-secondary"><i class="bi bi-geo-alt text-danger me-1"></i> Avadi, Chennai</span>
            <a href="#" class="small text-danger fw-semibold text-decoration-none">Get Directions &rarr;</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  var els = document.querySelectorAll('[data-aos]');
  if (!els.length) return;
  var obs = new IntersectionObserver(function(entries){
    entries.forEach(function(e){
      if (e.isIntersecting) { e.target.classList.add('aos-animate'); obs.unobserve(e.target); }
    });
  }, { threshold: 0.1 });
  els.forEach(function(el){ obs.observe(el); });
})();

(function(){
  var form = document.getElementById('contactForm');
  if (!form) return;

  var fields = form.querySelectorAll('input, textarea, select');
  var submitBtn = document.getElementById('submitBtn');
  var messageField = document.getElementById('message');
  var charCounter = null;

  var counterEl = document.createElement('div');
  counterEl.className = 'form-text text-end small mt-1';
  counterEl.id = 'charCounter';
  counterEl.textContent = '0 / 5000';
  if (messageField) {
    messageField.parentNode.appendChild(counterEl);
    charCounter = counterEl;
  }

  function validateField(field) {
    var isValid = field.checkValidity();
    if (field.tagName === 'SELECT' && field.value === '') isValid = false;
    if (field.id === 'phone' && field.value !== '' && !new RegExp('^(?:' + field.pattern + ')$').test(field.value)) isValid = false;
    field.classList.toggle('is-invalid', !isValid && field.value !== '');
    field.classList.toggle('is-valid', isValid && field.value !== '');
    return isValid;
  }

  fields.forEach(function(field) {
    field.addEventListener('blur', function() {
      validateField(field);
    });
    field.addEventListener('input', function() {
      if (field.classList.contains('is-invalid') || field.classList.contains('is-valid')) {
        validateField(field);
      }
      if (field.id === 'message' && charCounter) {
        charCounter.textContent = field.value.length + ' / 5000';
      }
    });
    field.addEventListener('change', function() {
      if (field.classList.contains('is-invalid') || field.classList.contains('is-valid')) {
        validateField(field);
      }
    });
  });

  form.addEventListener('submit', function(e) {
    var firstInvalid = null;
    fields.forEach(function(field) {
      if (field.type === 'hidden' || field.name === 'csrf_token' || field.name === 'website') return;
      validateField(field);
      if (!field.checkValidity() && !firstInvalid) firstInvalid = field;
      if (field.tagName === 'SELECT' && field.value === '' && !firstInvalid) firstInvalid = field;
    });
    if (firstInvalid) {
      e.preventDefault();
      e.stopPropagation();
      form.classList.add('was-validated');
      firstInvalid.focus();
      firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
      return;
    }
    if (form.submitting) { e.preventDefault(); return; }
    form.submitting = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sending...';
  });

  var resetBtn = document.getElementById('resetBtn');
  if (resetBtn) {
    resetBtn.addEventListener('click', function() {
      fields.forEach(function(el) {
        if (el.type === 'hidden' || el.type === 'submit' || el.name === 'csrf_token') return;
        if (el.tagName === 'SELECT') { el.selectedIndex = 0; }
        else { el.value = ''; }
        el.classList.remove('is-invalid', 'is-valid');
      });
      form.classList.remove('was-validated');
      if (charCounter) charCounter.textContent = '0 / 5000';
    });
  }
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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

(function(){
  var loader = document.getElementById('pageLoader');
  if (!loader) return;
  function show(){ loader.classList.remove('hidden'); }
  function hide(){ loader.classList.add('hidden'); }
  hide();
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

<?php if ($show_success_modal): ?>
(function(){
  var modal = new bootstrap.Modal(document.getElementById('successModal'));
  modal.show();
  document.getElementById('successModal').addEventListener('hidden.bs.modal', function() {
    var f = document.getElementById('contactForm');
    f.reset();
    f.classList.remove('was-validated');
    f.querySelectorAll('.is-invalid, .is-valid').forEach(function(el){ el.classList.remove('is-invalid', 'is-valid'); });
    var c = document.getElementById('charCounter');
    if (c) c.textContent = '0 / 5000';
    var btn = document.getElementById('submitBtn');
    var contactForm = document.getElementById('contactForm');
    if (contactForm) contactForm.submitting = false;
    btn.innerHTML = 'Send <i class="bi bi-send ms-1"></i>';
  });
})();
<?php elseif ($form_submitted): ?>
(function(){
  var modal = new bootstrap.Modal(document.getElementById('errorModal'));
  var list = document.getElementById('errorList');
  list.innerHTML = '';
  <?php foreach ($form_errors as $error): ?>
  var li = document.createElement('li');
  li.textContent = <?= json_encode($error, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  list.appendChild(li);
  <?php endforeach; ?>
  modal.show();
  document.getElementById('errorModal').addEventListener('hidden.bs.modal', function() {
    var btn = document.getElementById('submitBtn');
    var contactForm = document.getElementById('contactForm');
    if (contactForm) contactForm.submitting = false;
    btn.innerHTML = 'Send <i class="bi bi-send ms-1"></i>';
    document.getElementById('contactForm').querySelectorAll('.is-invalid, .is-valid').forEach(function(el){ el.classList.remove('is-invalid', 'is-valid'); });
  });
})();
<?php endif; ?>
</script>

<footer style="background:#0f172a;color:#cbd5e1;">
  <div style="padding:4rem 0 2.5rem;">
    <div class="container" style="max-width:80rem;">
  <div class="row g-5">
        <div class="col-lg-4">
          <div class="d-flex align-items-center gap-2 mb-3">
            <img src="images/logo.png" alt="CADDFE" height="38" style="filter:brightness(1.2)" loading="lazy">
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
              <a href="mailto:magendhiran@caddfe.com" class="footer-link">magendhiran@caddfe.com</a>
            </li>
            <li class="d-flex gap-2">
              <i class="bi bi-clock text-danger mt-1"></i>
              <span>Mon &ndash; Sat: 9 AM &ndash; 8 PM</span>
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

</body>
</html>
