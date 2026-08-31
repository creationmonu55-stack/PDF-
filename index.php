<?php
declare(strict_types=1);
session_start();
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="#05070b">
<title>VOTIVA — Your Voice. Your Choice.</title>
<meta name="description" content="VOTIVA is a premium public opinion and voting platform.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="noise"></div>
<header class="site-header">
  <div class="nav wrap">
    <a class="brand" href="index.php">VOTI<span>V</span>A</a>
    <nav class="desktop-nav" aria-label="Primary">
      <a href="index.php">Home</a>
      <a href="index.php?category=Cricket">Cricket</a>
      <a href="index.php?category=Football">Football</a>
      <a href="index.php?category=Movie">Movie</a>
      <a href="index.php?category=Celebrity">Celebrity</a>
      <a href="index.php?category=Other">Other</a>
      <a href="information.php">Information</a>
    </nav>
    <button class="menu-btn" id="menuBtn" aria-label="Open menu">☰</button>
  </div>
  <div class="mobile-menu" id="mobileMenu">
    <a href="index.php">Home</a><a href="index.php?category=Cricket">Cricket</a>
    <a href="index.php?category=Football">Football</a><a href="index.php?category=Movie">Movie</a>
    <a href="index.php?category=Celebrity">Celebrity</a><a href="index.php?category=Other">Other</a>
    <a href="information.php">Information</a>
  </div>
</header>

<main>
<section class="hero">
  <div class="wrap hero-grid">
    <div class="hero-copy">
      <div class="kicker"><span class="live-dot"></span> REAL-TIME PUBLIC OPINION</div>
      <h1>Your voice.<br><i>Your choice.</i></h1>
      <p>Vote on the debates that matter to you. Every accepted vote is stored on the server and the result is calculated from the actual vote database.</p>
      <div class="hero-buttons">
        <a class="primary-btn" href="#polls">Explore live polls <span>→</span></a>
        <button class="secondary-btn" id="howBtn">How it works</button>
      </div>
    </div>
    <div class="hero-panel">
      <div class="panel-label">THE VOTIVA STANDARD</div>
      <div class="hero-line"><strong>01</strong><span>One vote per campaign</span></div>
      <div class="hero-line"><strong>02</strong><span>Server-side vote counting</span></div>
      <div class="hero-line"><strong>03</strong><span>Live percentage calculation</span></div>
      <div class="hero-line"><strong>04</strong><span>Public opinions & sharing</span></div>
    </div>
  </div>
</section>

<section class="stats-strip">
  <div class="wrap stats">
    <div><strong id="totalVotes">0</strong><span>Total votes</span></div>
    <div><strong id="totalPolls">0</strong><span>Live campaigns</span></div>
    <div><strong id="totalOpinions">0</strong><span>Opinions</span></div>
  </div>
</section>

<section class="poll-section" id="polls">
  <div class="wrap">
    <div class="section-head">
      <div><span class="kicker">LIVE & POPULAR</span><h2 id="sectionTitle">Choose your side</h2></div>
      <button class="refresh" id="refreshBtn">↻ Refresh</button>
    </div>
    <div id="pollGrid" class="poll-grid"></div>
  </div>
</section>

<section class="journal">
  <div class="wrap journal-inner">
    <span class="kicker">VOTIVA JOURNAL</span>
    <h2>Opinion, made <i>beautiful.</i></h2>
    <p>VOTIVA brings simple public voting into a premium, transparent experience. Results are never generated randomly by the interface: accepted votes are persisted and totals are calculated from the database.</p>
    <a href="information.php" class="text-link">Read the information journal →</a>
  </div>
</section>
</main>

<footer>
  <div class="wrap footer-grid">
    <div><a class="brand" href="index.php">VOTI<span>V</span>A</a><p>© <?php echo date('Y'); ?> VOTIVA. Built for public opinion.</p></div>
    <div class="footer-links"><a href="information.php">Information</a><a href="privacy.php">Privacy</a><a href="terms.php">Terms</a></div>
  </div>
</footer>

<div class="modal" id="modal" aria-hidden="true">
  <div class="modal-card">
    <button class="close" id="closeModal" aria-label="Close">×</button>
    <div id="modalContent"></div>
  </div>
</div>
<div class="toast" id="toast"></div>
<script src="assets/app.js"></script>
</body>
</html>