<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

// Redirect if already logged in
if (isset($_SESSION['role']) && $_SESSION['role'] === 'pelanggan') {
    header("Location: index.php");
    exit();
}

// Fetch stats for counters
$total_armada = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM mobil WHERE is_deleted = 0"))['t'] ?? 0;
$total_pelanggan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pelanggan"))['t'] ?? 0;
$total_transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM transaksi_sewa WHERE status_sewa = 'selesai'"))['t'] ?? 0;

// Featured fleet
$featured_cars = [];
$q_cars = mysqli_query($conn, "SELECT * FROM mobil WHERE is_deleted = 0 ORDER BY kode_mobil ASC LIMIT 6");
while ($car = mysqli_fetch_assoc($q_cars)) {
    $featured_cars[] = $car;
}

// Rating stats
$rating_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(rating_pelayanan) as avg_r, COUNT(*) as total FROM rating_sewa"));
$avg_rating = round($rating_data['avg_r'] ?? 4.8, 1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INDOMAX RENTAL — Sewa Mobil Premium, Mudah & Terpercaya</title>
    <meta name="description" content="Sewa mobil premium dari PT INDOMAX RENTAL. Armada terlengkap, harga terbaik, layanan lepas kunci dan dengan sopir profesional di Jawa Tengah.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --crimson: #8B0000;
            --crimson-light: #c0392b;
            --gold: #D4AF37;
            --gold-light: #f0d060;
            --dark: #0d0d0d;
            --dark-2: #141414;
            --dark-3: #1c1c1c;
            --surface: #242424;
            --text: #f5f5f5;
            --text-muted: #a0a0a0;
            --border: rgba(255,255,255,0.08);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--dark);
            color: var(--text);
            overflow-x: hidden;
        }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: var(--dark); }
        ::-webkit-scrollbar-thumb { background: var(--crimson); border-radius: 10px; }

        /* ===== NAVBAR ===== */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            padding: 18px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.4s ease;
        }
        .navbar.scrolled {
            background: rgba(13, 13, 13, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 14px 5%;
        }
        .navbar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .logo-icon {
            width: 42px; height: 42px;
            background: var(--crimson);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }
        .logo-text { line-height: 1; }
        .logo-text .brand { font-size: 1.15rem; font-weight: 800; color: #fff; }
        .logo-text .sub { font-size: 0.55rem; font-weight: 700; color: var(--gold); text-transform: uppercase; letter-spacing: 2px; }
        .navbar-links { display: flex; align-items: center; gap: 10px; }
        .btn-nav-outline {
            padding: 9px 22px;
            border: 1.5px solid rgba(255,255,255,0.25);
            border-radius: 50px;
            color: #fff;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-nav-outline:hover { border-color: #fff; background: rgba(255,255,255,0.05); color: #fff; }
        .btn-nav-filled {
            padding: 10px 24px;
            background: var(--crimson);
            border-radius: 50px;
            color: #fff;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            transition: all 0.3s;
            box-shadow: 0 4px 20px rgba(139, 0, 0, 0.4);
        }
        .btn-nav-filled:hover { background: #a00000; transform: translateY(-1px); box-shadow: 0 6px 24px rgba(139,0,0,0.5); color: #fff; }

        /* ===== HERO ===== */
        .hero {
            min-height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
            padding: 140px 5% 80px;
        }
        .hero-bg {
            position: absolute; inset: 0;
            background: radial-gradient(ellipse 80% 60% at 70% 50%, rgba(139,0,0,0.35) 0%, transparent 70%),
                        radial-gradient(ellipse 50% 80% at 10% 80%, rgba(212,175,55,0.08) 0%, transparent 60%),
                        linear-gradient(160deg, #0d0d0d 0%, #1a0000 50%, #0d0d0d 100%);
        }
        /* Animated grid overlay */
        .hero-grid {
            position: absolute; inset: 0;
            background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse 70% 70% at 50% 50%, black, transparent);
        }
        /* Animated orbs */
        .hero-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            animation: orbFloat 8s ease-in-out infinite;
        }
        .hero-orb-1 { width: 500px; height: 500px; background: rgba(139,0,0,0.2); top: -100px; right: -100px; animation-delay: 0s; }
        .hero-orb-2 { width: 300px; height: 300px; background: rgba(212,175,55,0.1); bottom: 50px; left: -50px; animation-delay: 3s; }
        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -30px) scale(1.1); }
        }
        .hero-content { position: relative; z-index: 2; max-width: 620px; }
        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(212,175,55,0.1);
            border: 1px solid rgba(212,175,55,0.3);
            border-radius: 50px;
            padding: 6px 16px;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--gold);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 24px;
            animation: fadeInUp 0.6s ease both;
        }
        .hero-title {
            font-size: clamp(2.4rem, 5vw, 4rem);
            font-weight: 800;
            line-height: 1.1;
            color: #fff;
            margin-bottom: 20px;
            animation: fadeInUp 0.7s 0.1s ease both;
        }
        .hero-title .accent {
            background: linear-gradient(90deg, #c0392b, var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-desc {
            font-size: 1.05rem;
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 36px;
            animation: fadeInUp 0.7s 0.2s ease both;
        }
        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            animation: fadeInUp 0.7s 0.3s ease both;
        }
        .btn-hero-primary {
            padding: 15px 32px;
            background: linear-gradient(135deg, var(--crimson), #c0392b);
            color: #fff;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            box-shadow: 0 8px 32px rgba(139,0,0,0.5);
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-hero-primary:hover { transform: translateY(-3px); box-shadow: 0 12px 40px rgba(139,0,0,0.6); color: #fff; }
        .btn-hero-secondary {
            padding: 15px 32px;
            border: 1.5px solid rgba(255,255,255,0.2);
            color: #fff;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.03);
        }
        .btn-hero-secondary:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.08); color: #fff; transform: translateY(-2px); }
        /* Rating inline badge */
        .hero-trust {
            margin-top: 32px;
            display: flex;
            align-items: center;
            gap: 16px;
            animation: fadeInUp 0.7s 0.4s ease both;
        }
        .trust-avatars { display: flex; }
        .trust-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            border: 2px solid var(--dark);
            background: linear-gradient(135deg, #555, #888);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700; color: #fff;
        }
        .trust-avatar:not(:first-child) { margin-left: -10px; }
        .trust-text { font-size: 0.82rem; color: var(--text-muted); }
        .trust-text strong { color: #fff; }
        /* Hero Car Visual */
        .hero-visual {
            position: absolute;
            right: 0; top: 50%;
            transform: translateY(-50%);
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }
        .hero-car-wrap {
            position: relative;
            animation: carFloat 6s ease-in-out infinite;
        }
        @keyframes carFloat {
            0%, 100% { transform: translateY(0px) rotate(-1deg); }
            50% { transform: translateY(-20px) rotate(1deg); }
        }
        .hero-car-img {
            width: min(560px, 100%);
            filter: drop-shadow(0 40px 80px rgba(139,0,0,0.5));
            border-radius: 20px;
            object-fit: cover;
        }
        .hero-car-glow {
            position: absolute;
            width: 80%;
            height: 40px;
            background: rgba(139,0,0,0.5);
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            filter: blur(30px);
            border-radius: 50%;
        }

        /* ===== STAT COUNTERS ===== */
        .counters-section {
            padding: 0 5% 80px;
            position: relative;
        }
        .counters-wrap {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 24px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }
        .counter-item {
            padding: 36px 24px;
            text-align: center;
            border-right: 1px solid var(--border);
            position: relative;
            transition: background 0.3s;
        }
        .counter-item:last-child { border-right: none; }
        .counter-item:hover { background: rgba(139,0,0,0.08); }
        .counter-number {
            font-size: 2.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #fff, var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 6px;
        }
        .counter-label { font-size: 0.8rem; color: var(--text-muted); font-weight: 500; }

        /* ===== FEATURES ===== */
        .section {
            padding: 80px 5%;
        }
        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: var(--gold);
            margin-bottom: 16px;
        }
        .section-title {
            font-size: clamp(1.8rem, 3.5vw, 2.8rem);
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
            margin-bottom: 16px;
        }
        .section-desc { color: var(--text-muted); font-size: 1rem; max-width: 540px; line-height: 1.7; }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-top: 60px;
        }
        .feature-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 36px 28px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(139,0,0,0.15), transparent);
            opacity: 0;
            transition: opacity 0.4s;
        }
        .feature-card:hover::before { opacity: 1; }
        .feature-card:hover {
            transform: translateY(-6px);
            border-color: rgba(139,0,0,0.4);
            box-shadow: 0 20px 60px rgba(139,0,0,0.15);
        }
        .feature-icon {
            width: 56px; height: 56px;
            border-radius: 14px;
            background: rgba(139,0,0,0.15);
            border: 1px solid rgba(139,0,0,0.3);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            color: var(--crimson-light);
            margin-bottom: 24px;
        }
        .feature-title { font-size: 1.1rem; font-weight: 700; color: #fff; margin-bottom: 10px; }
        .feature-desc { font-size: 0.88rem; color: var(--text-muted); line-height: 1.7; }

        /* ===== FLEET SECTION ===== */
        .fleet-section { padding: 80px 5%; background: var(--dark-2); }
        .fleet-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-top: 60px;
        }
        .fleet-card {
            background: var(--surface);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--border);
            transition: all 0.4s ease;
            position: relative;
        }
        .fleet-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 24px 60px rgba(0,0,0,0.4);
            border-color: rgba(139,0,0,0.4);
        }
        .fleet-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.6s ease;
        }
        .fleet-card:hover .fleet-img { transform: scale(1.06); }
        .fleet-img-wrap { overflow: hidden; position: relative; }
        .fleet-badge {
            position: absolute;
            top: 12px; left: 12px;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(10px);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 5px 12px;
            border-radius: 50px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .fleet-body { padding: 24px; }
        .fleet-name { font-size: 1.1rem; font-weight: 700; color: #fff; margin-bottom: 6px; }
        .fleet-price {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 16px;
        }
        .fleet-price strong { color: var(--gold); font-size: 1rem; }
        .fleet-specs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .fleet-spec {
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 4px 10px;
            font-size: 0.72rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .fleet-action {
            display: block;
            text-align: center;
            padding: 11px;
            background: linear-gradient(135deg, var(--crimson), #c0392b);
            color: #fff;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .fleet-action:hover { opacity: 0.9; transform: translateY(-1px); color: #fff; }

        /* ===== PROMO BANNER ===== */
        .promo-section { padding: 40px 5% 80px; }
        .promo-card {
            background: linear-gradient(135deg, #8B0000 0%, #3d0000 40%, #1a0000 100%);
            border-radius: 28px;
            padding: 60px 64px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(212,175,55,0.2);
        }
        .promo-card::before {
            content: '';
            position: absolute;
            top: -100px; right: -100px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(212,175,55,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
        .promo-card::after {
            content: '';
            position: absolute;
            bottom: -50px; left: 20%;
            width: 200px; height: 200px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
        }
        .promo-badge {
            display: inline-block;
            background: var(--gold);
            color: #1a1a1a;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 6px 16px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }
        .promo-title { font-size: clamp(1.6rem, 3vw, 2.4rem); font-weight: 800; color: #fff; margin-bottom: 12px; }
        .promo-desc { font-size: 1rem; color: rgba(255,255,255,0.75); margin-bottom: 32px; max-width: 480px; line-height: 1.6; }
        .btn-promo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 32px;
            background: #fff;
            color: var(--crimson);
            border-radius: 50px;
            text-decoration: none;
            font-weight: 800;
            font-size: 0.9rem;
            transition: all 0.3s;
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        }
        .btn-promo:hover { transform: translateY(-3px); box-shadow: 0 12px 40px rgba(0,0,0,0.3); color: var(--crimson); }

        /* ===== TESTIMONIALS ===== */
        .testi-section { padding: 80px 5%; }
        .testi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 50px;
        }
        .testi-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 28px;
            transition: all 0.3s;
        }
        .testi-card:hover { border-color: rgba(212,175,55,0.3); transform: translateY(-4px); }
        .testi-stars { color: var(--gold); font-size: 0.85rem; margin-bottom: 14px; }
        .testi-text { font-size: 0.9rem; color: var(--text-muted); line-height: 1.7; margin-bottom: 20px; font-style: italic; }
        .testi-author { display: flex; align-items: center; gap: 12px; }
        .testi-avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--crimson), #c0392b);
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 0.85rem; color: #fff;
        }
        .testi-name { font-weight: 700; color: #fff; font-size: 0.9rem; }
        .testi-role { font-size: 0.75rem; color: var(--text-muted); }

        /* ===== HOW IT WORKS ===== */
        .how-section { padding: 80px 5%; background: var(--dark-2); }
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-top: 60px;
            position: relative;
        }
        .steps-grid::before {
            content: '';
            position: absolute;
            top: 32px; left: 12%;
            width: 76%;
            height: 2px;
            background: linear-gradient(90deg, var(--crimson), var(--gold), var(--crimson));
            opacity: 0.3;
        }
        .step-card { text-align: center; padding: 20px 16px; position: relative; }
        .step-num {
            width: 64px; height: 64px;
            border-radius: 50%;
            background: var(--dark);
            border: 2px solid var(--crimson);
            color: var(--gold);
            font-size: 1.2rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            position: relative;
            z-index: 2;
            box-shadow: 0 0 0 6px rgba(139,0,0,0.1);
        }
        .step-title { font-size: 0.95rem; font-weight: 700; color: #fff; margin-bottom: 8px; }
        .step-desc { font-size: 0.82rem; color: var(--text-muted); line-height: 1.6; }

        /* ===== FOOTER ===== */
        footer {
            background: var(--dark-2);
            border-top: 1px solid var(--border);
            padding: 60px 5% 30px;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 48px;
            margin-bottom: 48px;
        }
        .footer-brand-name { font-size: 1.4rem; font-weight: 800; color: #fff; margin-bottom: 14px; }
        .footer-brand-name span { color: var(--gold); }
        .footer-desc { font-size: 0.85rem; color: var(--text-muted); line-height: 1.8; max-width: 320px; }
        .footer-social { display: flex; gap: 10px; margin-top: 24px; }
        .footer-social a {
            width: 36px; height: 36px;
            border-radius: 8px;
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--border);
            color: var(--text-muted);
            display: flex; align-items: center; justify-content: center;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        .footer-social a:hover { background: var(--crimson); color: #fff; border-color: var(--crimson); }
        .footer-head { font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; color: #fff; margin-bottom: 18px; }
        .footer-links { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .footer-links a { color: var(--text-muted); text-decoration: none; font-size: 0.85rem; transition: color 0.2s; }
        .footer-links a:hover { color: var(--gold); }
        .footer-contact-item { display: flex; gap: 10px; align-items: flex-start; margin-bottom: 12px; }
        .footer-contact-item i { color: var(--crimson-light); margin-top: 2px; flex-shrink: 0; }
        .footer-contact-item span { font-size: 0.85rem; color: var(--text-muted); }
        .footer-bottom {
            border-top: 1px solid var(--border);
            padding-top: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .footer-copy { font-size: 0.8rem; color: var(--text-muted); }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .hero { padding: 130px 5% 60px; }
            .hero-visual { display: none; }
            .hero-content { max-width: 100%; }
            .features-grid, .fleet-grid, .testi-grid { grid-template-columns: repeat(2, 1fr); }
            .steps-grid { grid-template-columns: repeat(2, 1fr); }
            .steps-grid::before { display: none; }
            .counters-wrap { grid-template-columns: repeat(2, 1fr); }
            .counter-item:nth-child(2) { border-right: none; }
            .counter-item:nth-child(1), .counter-item:nth-child(2) { border-bottom: 1px solid var(--border); }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 640px) {
            .features-grid, .fleet-grid, .testi-grid, .steps-grid { grid-template-columns: 1fr; }
            .counters-wrap { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: 1fr; }
            .promo-card { padding: 36px 24px; }
            .navbar-links .btn-nav-outline { display: none; }
        }
    </style>
</head>
<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar" id="mainNav">
    <a href="#" class="navbar-logo">
        <div class="logo-icon">🚗</div>
        <div class="logo-text">
            <div class="brand">INDOMAX</div>
            <div class="sub">Rental Mobil</div>
        </div>
    </a>
    <div class="navbar-links">
        <a href="#armada" class="btn-nav-outline">Armada</a>
        <a href="#cara-kerja" class="btn-nav-outline">Cara Kerja</a>
        <a href="login_pelanggan.php" class="btn-nav-outline">Masuk</a>
        <a href="register_pelanggan.php" class="btn-nav-filled">Daftar Gratis</a>
    </div>
</nav>

<!-- ===== HERO ===== -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-grid"></div>
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>

    <div class="hero-content">
        <div class="hero-eyebrow">
            <i class="bi bi-star-fill"></i> Dipercaya <?= number_format($total_pelanggan) ?>+ Pelanggan
        </div>
        <h1 class="hero-title">
            Sewa Mobil <span class="accent">Premium</span><br>Mudah, Aman & Tepat Waktu
        </h1>
        <p class="hero-desc">
            Armada terawat, harga transparan, dan layanan pelanggan terbaik dari PT INDOMAX RENTAL. Nikmati perjalanan nyaman dengan sistem booking online 24 jam.
        </p>
        <div class="hero-actions">
            <a href="register_pelanggan.php" class="btn-hero-primary">
                Mulai Sewa Sekarang <i class="bi bi-arrow-right"></i>
            </a>
            <a href="#armada" class="btn-hero-secondary">
                <i class="bi bi-play-circle"></i> Lihat Armada
            </a>
        </div>
        <div class="hero-trust">
            <div class="trust-avatars">
                <div class="trust-avatar">AW</div>
                <div class="trust-avatar">BD</div>
                <div class="trust-avatar">CR</div>
                <div class="trust-avatar" style="background: linear-gradient(135deg, var(--crimson), #c0392b);">+</div>
            </div>
            <div class="trust-text">
                <strong><?= number_format($total_transaksi) ?>+ perjalanan selesai</strong> dengan rating <strong><?= $avg_rating ?>/5.0</strong>
                <div>⭐⭐⭐⭐⭐ dari pelanggan setia kami</div>
            </div>
        </div>
    </div>

    <div class="hero-visual">
        <div class="hero-car-wrap">
            <img src="https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&q=80&w=800"
                 alt="Premium Car" class="hero-car-img">
            <div class="hero-car-glow"></div>
        </div>
    </div>
</section>

<!-- ===== COUNTER STATS ===== -->
<div class="counters-section">
    <div class="counters-wrap">
        <div class="counter-item reveal">
            <div class="counter-number" data-target="<?= $total_armada ?>" data-suffix="">0</div>
            <div class="counter-label">Tipe Armada Tersedia</div>
        </div>
        <div class="counter-item reveal" style="transition-delay: 0.1s;">
            <div class="counter-number" data-target="<?= $total_pelanggan ?>" data-suffix="+">0</div>
            <div class="counter-label">Pelanggan Terdaftar</div>
        </div>
        <div class="counter-item reveal" style="transition-delay: 0.2s;">
            <div class="counter-number" data-target="<?= $total_transaksi ?>" data-suffix="+">0</div>
            <div class="counter-label">Perjalanan Diselesaikan</div>
        </div>
        <div class="counter-item reveal" style="transition-delay: 0.3s;">
            <div class="counter-number" data-target="<?= $avg_rating * 10 ?>" data-suffix="" data-decimal="1">0</div>
            <div class="counter-label">Rating Kepuasan / 5.0</div>
        </div>
    </div>
</div>

<!-- ===== FEATURES ===== -->
<section class="section">
    <div class="reveal">
        <div class="section-label"><i class="bi bi-lightning-fill"></i> Keunggulan Kami</div>
        <h2 class="section-title">Layanan Terbaik Untuk<br>Perjalanan Anda</h2>
        <p class="section-desc">Kami berkomitmen memberikan pengalaman sewa mobil yang menyenangkan dengan armada terawat dan pelayanan prima.</p>
    </div>
    <div class="features-grid">
        <div class="feature-card reveal">
            <div class="feature-icon"><i class="bi bi-key-fill"></i></div>
            <div class="feature-title">Lepas Kunci (Self-Drive)</div>
            <div class="feature-desc">Nikmati kebebasan berkendara sendiri. Proses verifikasi KTP & SIM yang mudah dan cepat, armada siap pakai kapan saja.</div>
        </div>
        <div class="feature-card reveal" style="transition-delay: 0.1s;">
            <div class="feature-icon"><i class="bi bi-person-badge-fill"></i></div>
            <div class="feature-title">Sopir Profesional</div>
            <div class="feature-desc">Ingin bersantai? Driver berpengalaman dan berlisensi resmi kami siap mengantar Anda ke mana saja dengan aman dan nyaman.</div>
        </div>
        <div class="feature-card reveal" style="transition-delay: 0.2s;">
            <div class="feature-icon"><i class="bi bi-credit-card-2-front-fill"></i></div>
            <div class="feature-title">Pembayaran Fleksibel</div>
            <div class="feature-desc">Bayar DP 50% atau langsung lunas. Diterima: Transfer Bank, E-Wallet, dan tunai. Bukti pembayaran otomatis dikirim.</div>
        </div>
        <div class="feature-card reveal" style="transition-delay: 0.3s;">
            <div class="feature-icon"><i class="bi bi-shield-check-fill"></i></div>
            <div class="feature-title">Armada Terawat & Aman</div>
            <div class="feature-desc">Semua unit diperiksa dan di-service secara berkala. Anda hanya akan mendapatkan kendaraan dalam kondisi prima.</div>
        </div>
        <div class="feature-card reveal" style="transition-delay: 0.4s;">
            <div class="feature-icon"><i class="bi bi-phone-fill"></i></div>
            <div class="feature-title">Booking Online 24 Jam</div>
            <div class="feature-desc">Pesan kapan saja dan di mana saja melalui portal pelanggan kami. Konfirmasi instan via notifikasi WhatsApp.</div>
        </div>
        <div class="feature-card reveal" style="transition-delay: 0.5s;">
            <div class="feature-icon"><i class="bi bi-geo-alt-fill"></i></div>
            <div class="feature-title">Antar Jemput Tersedia</div>
            <div class="feature-desc">Layanan antar ke alamat Anda atau jemput di titik yang disepakati. Nyaman tanpa harus datang ke kantor.</div>
        </div>
    </div>
</section>

<!-- ===== FLEET ===== -->
<section class="fleet-section" id="armada">
    <div class="reveal" style="text-align: center;">
        <div class="section-label" style="justify-content: center;"><i class="bi bi-car-front-fill"></i> Pilihan Armada</div>
        <h2 class="section-title" style="text-align: center;">Armada Pilihan Terpopuler</h2>
        <p class="section-desc" style="margin: 0 auto; text-align: center;">Berbagai pilihan kendaraan dari segmen city car hingga SUV premium. Semua dalam kondisi terawat dan siap sewa.</p>
    </div>
    <div class="fleet-grid">
        <?php
        $delay = 0;
        foreach ($featured_cars as $car):
            $img_path = 'img/' . $car['Gambar'];
            $img_src = (!empty($car['Gambar']) && file_exists($img_path))
                ? $img_path
                : 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&q=80&w=600';
        ?>
        <div class="fleet-card reveal" style="transition-delay: <?= $delay ?>s;">
            <div class="fleet-img-wrap">
                <img src="<?= htmlspecialchars($img_src) ?>" alt="<?= htmlspecialchars($car['merk']) ?>" class="fleet-img">
                <span class="fleet-badge"><?= htmlspecialchars($car['jenis']) ?></span>
            </div>
            <div class="fleet-body">
                <div class="fleet-name"><?= htmlspecialchars($car['merk']) ?></div>
                <div class="fleet-price">Mulai <strong>Rp <?= number_format($car['tarif_12_dalam'], 0, ',', '.') ?></strong> / 12 Jam</div>
                <div class="fleet-specs">
                    <span class="fleet-spec"><i class="bi bi-snow2"></i> AC</span>
                    <span class="fleet-spec"><i class="bi bi-music-note"></i> Audio</span>
                    <span class="fleet-spec"><i class="bi bi-upc"></i> <?= htmlspecialchars($car['nopol']) ?></span>
                </div>
                <a href="login_pelanggan.php" class="fleet-action">Sewa Sekarang →</a>
            </div>
        </div>
        <?php $delay += 0.1; endforeach; ?>
    </div>
    <div style="text-align: center; margin-top: 48px;" class="reveal">
        <a href="login_pelanggan.php" style="display: inline-flex; align-items: center; gap: 10px; padding: 14px 36px; border: 1.5px solid rgba(255,255,255,0.15); border-radius: 50px; color: #fff; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.3s; backdrop-filter: blur(10px);" onmouseover="this.style.borderColor='rgba(255,255,255,0.4)';this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.15)';this.style.background='transparent'">
            Lihat Semua Armada <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</section>

<!-- ===== HOW IT WORKS ===== -->
<section class="how-section" id="cara-kerja">
    <div class="reveal" style="text-align: center;">
        <div class="section-label" style="justify-content: center;"><i class="bi bi-diagram-3-fill"></i> Cara Kerja</div>
        <h2 class="section-title" style="text-align: center;">Sewa Mobil Dalam 4 Langkah</h2>
        <p class="section-desc" style="margin: 0 auto; text-align: center;">Proses booking yang mudah dan cepat. Dari daftar hingga kendaraan tiba di depan pintu Anda.</p>
    </div>
    <div class="steps-grid">
        <div class="step-card reveal">
            <div class="step-num">1</div>
            <div class="step-title">Daftar & Verifikasi</div>
            <div class="step-desc">Buat akun gratis dan unggah KTP & SIM untuk verifikasi identitas Anda.</div>
        </div>
        <div class="step-card reveal" style="transition-delay: 0.15s;">
            <div class="step-num">2</div>
            <div class="step-title">Pilih Mobil & Jadwal</div>
            <div class="step-desc">Pilih armada favorit dari katalog, tentukan tanggal sewa dan durasi perjalanan.</div>
        </div>
        <div class="step-card reveal" style="transition-delay: 0.3s;">
            <div class="step-num">3</div>
            <div class="step-title">Konfirmasi & Bayar</div>
            <div class="step-desc">Pesanan di-ACC admin, lanjutkan pembayaran DP atau lunas sesuai pilihan.</div>
        </div>
        <div class="step-card reveal" style="transition-delay: 0.45s;">
            <div class="step-num">4</div>
            <div class="step-title">Kendaraan Siap!</div>
            <div class="step-desc">Mobil diantar ke lokasi Anda atau ambil langsung di kantor. Selamat menikmati perjalanan!</div>
        </div>
    </div>
</section>

<!-- ===== PROMO ===== -->
<section class="promo-section">
    <div class="promo-card reveal">
        <div style="position: relative; z-index: 2; display: flex; flex-wrap: wrap; gap: 40px; align-items: center; justify-content: space-between;">
            <div>
                <div class="promo-badge">⚡ Promo Spesial Bulan Ini</div>
                <h2 class="promo-title">Diskon 20% Weekend Getaway!</h2>
                <p class="promo-desc">Sewa mobil apapun minimal 3 hari di akhir pekan dan dapatkan diskon langsung. Termasuk gratis layanan antar jemput dalam kota.</p>
                <a href="register_pelanggan.php" class="btn-promo">
                    <i class="bi bi-lightning-fill"></i> Klaim Promo Sekarang
                </a>
            </div>
            <div style="font-size: 5rem; opacity: 0.15; font-weight: 900; color: #fff;">20%</div>
        </div>
    </div>
</section>

<!-- ===== TESTIMONIALS ===== -->
<section class="testi-section">
    <div class="reveal">
        <div class="section-label"><i class="bi bi-chat-quote-fill"></i> Testimoni</div>
        <h2 class="section-title">Yang Pelanggan Katakan</h2>
    </div>
    <div class="testi-grid">
        <div class="testi-card reveal">
            <div class="testi-stars">★★★★★</div>
            <div class="testi-text">"Pelayanan sangat memuaskan! Mobil bersih, sopir ramah, dan proses booking di website sangat mudah. Pasti akan sewa lagi!"</div>
            <div class="testi-author">
                <div class="testi-avatar">AR</div>
                <div>
                    <div class="testi-name">Ahmad Ridwan</div>
                    <div class="testi-role">Pelanggan — Solo, Jawa Tengah</div>
                </div>
            </div>
        </div>
        <div class="testi-card reveal" style="transition-delay: 0.1s;">
            <div class="testi-stars">★★★★★</div>
            <div class="testi-text">"Harga sangat kompetitif dan mobil dalam kondisi prima. Antar jemput tepat waktu. Recommended banget untuk yang butuh rental terpercaya!"</div>
            <div class="testi-author">
                <div class="testi-avatar" style="background: linear-gradient(135deg, #166534, #15803d);">SK</div>
                <div>
                    <div class="testi-name">Siti Khoiriyah</div>
                    <div class="testi-role">Pelanggan — Klaten, Jawa Tengah</div>
                </div>
            </div>
        </div>
        <div class="testi-card reveal" style="transition-delay: 0.2s;">
            <div class="testi-stars">★★★★★</div>
            <div class="testi-text">"Sistem pembayaran DP-nya sangat membantu. Konfirmasi cepat via WhatsApp dan kuitansi langsung bisa didownload. Super praktis!"</div>
            <div class="testi-author">
                <div class="testi-avatar" style="background: linear-gradient(135deg, #0e7490, #0891b2);">BP</div>
                <div>
                    <div class="testi-name">Budi Prasetyo</div>
                    <div class="testi-role">Pelanggan — Sukoharjo, Jawa Tengah</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer>
    <div class="footer-grid">
        <div>
            <div class="footer-brand-name">INDOMAX <span>RENTAL</span></div>
            <div class="footer-desc">Penyedia jasa sewa mobil terbaik dan terpercaya di Jawa Tengah. Berkomitmen memberikan armada terawat dengan layanan prima sejak 2018.</div>
            <div class="footer-social">
                <a href="#"><i class="bi bi-instagram"></i></a>
                <a href="https://wa.me/62881010715798" target="_blank"><i class="bi bi-whatsapp"></i></a>
                <a href="#"><i class="bi bi-facebook"></i></a>
                <a href="#"><i class="bi bi-tiktok"></i></a>
            </div>
        </div>
        <div>
            <div class="footer-head">Tautan</div>
            <ul class="footer-links">
                <li><a href="login_pelanggan.php">Masuk Pelanggan</a></li>
                <li><a href="register_pelanggan.php">Daftar Akun</a></li>
                <li><a href="#armada">Pilihan Armada</a></li>
                <li><a href="#cara-kerja">Cara Kerja</a></li>
            </ul>
        </div>
        <div>
            <div class="footer-head">Kontak</div>
            <div class="footer-contact-item">
                <i class="bi bi-geo-alt-fill"></i>
                <span>Solo, Jawa Tengah, Indonesia</span>
            </div>
            <div class="footer-contact-item">
                <i class="bi bi-telephone-fill"></i>
                <span>62 881-0107-15798 (WhatsApp)</span>
            </div>
            <div class="footer-contact-item">
                <i class="bi bi-envelope-fill"></i>
                <span>support@indomaxrental.com</span>
            </div>
            <div class="footer-contact-item">
                <i class="bi bi-clock-fill"></i>
                <span>Senin – Minggu: 07.00 – 21.00 WIB</span>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="footer-copy">© <?= date('Y') ?> PT INDOMAX RENTAL MOBIL. All rights reserved.</div>
        <div class="footer-copy">Dibuat dengan ❤️ untuk pelanggan terbaik kami.</div>
    </div>
</footer>

<script>
    // Navbar scroll effect
    const nav = document.getElementById('mainNav');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 60);
    });

    // Intersection Observer for reveal animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                observer.unobserve(e.target);
            }
        });
    }, { threshold: 0.15 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    // Animated counters
    function animateCounter(el) {
        const target = parseInt(el.getAttribute('data-target')) || 0;
        const suffix = el.getAttribute('data-suffix') || '';
        const isDecimal = el.hasAttribute('data-decimal');
        const duration = 1800;
        const startTime = performance.now();

        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const value = Math.round(target * eased);
            if (isDecimal) {
                el.textContent = (value / 10).toFixed(1) + suffix;
            } else {
                el.textContent = value.toLocaleString('id-ID') + suffix;
            }
            if (progress < 1) requestAnimationFrame(update);
        }
        requestAnimationFrame(update);
    }

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                animateCounter(e.target);
                counterObserver.unobserve(e.target);
            }
        });
    }, { threshold: 0.5 });
    document.querySelectorAll('.counter-number').forEach(el => counterObserver.observe(el));
</script>
</body>
</html>
