<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SYSALMACEN — Sistema de Gestión de Almacenes</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary:    #5EAF4A;
            --primary-dk: #4a8e38;
            --primary-lt: #e8f5e4;
            --dark:       #1e2a1c;
            --gray:       #6b7280;
            --gray-lt:    #f3f4f6;
            --white:      #ffffff;
            --border:     #e5e7eb;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            color: #1f2937;
            background: var(--white);
            line-height: 1.6;
            overflow-x: hidden;
        }

        a { text-decoration: none; color: inherit; }

        /* ── KEYFRAMES ───────────────────────────────────── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(32px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-32px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(32px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-12px); }
        }
        @keyframes pulseDot {
            0%, 100% { box-shadow: 0 0 0 0 rgba(94,175,74,.5); }
            50%       { box-shadow: 0 0 0 8px rgba(94,175,74,0); }
        }
        @keyframes shimmer {
            0%   { background-position: -400px 0; }
            100% { background-position: 400px 0; }
        }
        @keyframes rotateSlow {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
        @keyframes progressLine {
            from { width: 0; }
            to   { width: 100%; }
        }
        @keyframes countUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes ripple {
            0%   { transform: scale(1); opacity: .6; }
            100% { transform: scale(2.5); opacity: 0; }
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes orb1 {
            0%, 100% { transform: translate(0,0) scale(1); }
            33%       { transform: translate(40px,-30px) scale(1.1); }
            66%       { transform: translate(-20px,20px) scale(.9); }
        }
        @keyframes orb2 {
            0%, 100% { transform: translate(0,0) scale(1); }
            33%       { transform: translate(-50px,30px) scale(.85); }
            66%       { transform: translate(30px,-40px) scale(1.15); }
        }
        @keyframes typewriter {
            from { width: 0; }
            to   { width: 100%; }
        }
        @keyframes blink {
            50% { border-color: transparent; }
        }
        @keyframes badge-pulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: .6; }
        }

        /* ── SCROLL REVEAL ───────────────────────────────── */
        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity .65s cubic-bezier(.4,0,.2,1), transform .65s cubic-bezier(.4,0,.2,1);
        }
        .reveal.from-left  { transform: translateX(-28px); }
        .reveal.from-right { transform: translateX(28px); }
        .reveal.visible {
            opacity: 1;
            transform: translate(0);
        }

        /* ── NAVBAR ──────────────────────────────────────── */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 200;
            background: rgba(255,255,255,.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            height: 76px;
            display: flex;
            align-items: center;
            transition: box-shadow .3s, background .3s;
            animation: slideDown .5s ease both;
        }
        .navbar.scrolled {
            box-shadow: 0 2px 20px rgba(0,0,0,.09);
            background: rgba(255,255,255,.98);
        }
        .nav-inner {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .nav-brand img {
            height: 52px;
            width: auto;
            transition: transform .2s;
        }
        .nav-brand:hover img { transform: scale(1.06) rotate(-3deg); }
        .nav-brand-text { display: flex; flex-direction: column; }
        .nav-brand-text .sys-name {
            font-size: 15px; font-weight: 700;
            color: var(--primary-dk); letter-spacing: .5px; line-height: 1.2;
        }
        .nav-brand-text .sys-sub {
            font-size: 10px; font-weight: 400;
            color: var(--gray); text-transform: uppercase; letter-spacing: .8px;
        }
        .nav-links {
            display: flex; align-items: center;
            gap: 28px; list-style: none;
        }
        .nav-links a {
            font-size: 14px; font-weight: 500;
            color: #374151;
            transition: color .2s;
            position: relative;
        }
        .nav-links a:not(.nav-cta)::after {
            content: '';
            position: absolute;
            left: 0; bottom: -3px;
            width: 0; height: 2px;
            background: var(--primary);
            transition: width .25s;
            border-radius: 2px;
        }
        .nav-links a:not(.nav-cta):hover::after { width: 100%; }
        .nav-links a:hover { color: var(--primary); }
        .nav-cta {
            background: var(--primary);
            color: var(--white) !important;
            padding: 9px 20px;
            border-radius: 6px;
            font-weight: 600 !important;
            font-size: 14px;
            transition: background .2s, transform .15s, box-shadow .2s !important;
        }
        .nav-cta:hover {
            background: var(--primary-dk) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 16px rgba(94,175,74,.4) !important;
        }

        /* ── HERO ────────────────────────────────────────── */
        .hero {
            margin-top: 76px;
            background: linear-gradient(135deg, #1e2a1c 0%, #2d4a25 55%, #3a6030 100%);
            padding: 100px 24px 90px;
            position: relative;
            overflow: hidden;
            min-height: 700px;
        }
        /* Animated orbs */
        .hero-orb {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }
        .hero-orb-1 {
            width: 500px; height: 500px;
            top: -120px; right: -100px;
            background: radial-gradient(circle, rgba(94,175,74,.22) 0%, transparent 68%);
            animation: orb1 12s ease-in-out infinite;
        }
        .hero-orb-2 {
            width: 360px; height: 360px;
            bottom: -100px; left: -60px;
            background: radial-gradient(circle, rgba(94,175,74,.15) 0%, transparent 68%);
            animation: orb2 15s ease-in-out infinite;
        }
        .hero-orb-3 {
            width: 200px; height: 200px;
            top: 50%; left: 45%;
            background: radial-gradient(circle, rgba(134,239,172,.08) 0%, transparent 68%);
            animation: orb1 9s ease-in-out infinite reverse;
        }
        /* Particle canvas */
        #hero-canvas {
            position: absolute;
            inset: 0;
            pointer-events: none;
            opacity: .4;
        }
        .hero-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1.3fr;
            gap: 48px;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        /* Hero content stagger */
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(94,175,74,.2);
            border: 1px solid rgba(94,175,74,.4);
            color: #86efac;
            font-size: 12px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 100px;
            text-transform: uppercase;
            letter-spacing: .8px;
            margin-bottom: 20px;
            opacity: 0;
            animation: fadeInUp .6s .2s ease both;
        }
        .hero-badge .dot {
            width: 8px; height: 8px;
            background: #86efac;
            border-radius: 50%;
            animation: pulseDot 2s infinite;
            display: inline-block;
        }
        .hero h1 {
            font-size: 46px;
            font-weight: 800;
            color: var(--white);
            line-height: 1.15;
            margin-bottom: 18px;
            opacity: 0;
            animation: fadeInUp .6s .4s ease both;
        }
        .hero h1 span { color: #86efac; }
        .hero > .hero-inner > .hero-content > p {
            font-size: 17px;
            color: #9ca3af;
            margin-bottom: 36px;
            max-width: 480px;
            opacity: 0;
            animation: fadeInUp .6s .6s ease both;
        }
        .hero-buttons {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            opacity: 0;
            animation: fadeInUp .6s .8s ease both;
        }
        .btn-primary {
            background: var(--primary);
            color: var(--white);
            padding: 13px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background .2s, transform .15s, box-shadow .2s;
            position: relative;
            overflow: hidden;
        }
        .btn-primary::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0);
            transition: background .2s;
        }
        .btn-primary:hover { background: var(--primary-dk); transform: translateY(-2px); box-shadow: 0 6px 24px rgba(94,175,74,.45); }
        .btn-outline {
            background: transparent;
            color: var(--white);
            padding: 13px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            border: 1.5px solid rgba(255,255,255,.3);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: border-color .2s, background .2s, transform .15s;
        }
        .btn-outline:hover { border-color: var(--white); background: rgba(255,255,255,.07); transform: translateY(-2px); }
        .hero-img {
            display: flex;
            justify-content: center;
            opacity: 0;
            animation: fadeInRight .8s .5s ease both;
        }
        .hero-img-card {
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 20px;
            padding: 18px;
            width: 100%;
            max-width: 600px;
            backdrop-filter: blur(6px);
            animation: float 6s ease-in-out infinite;
            box-shadow: 0 30px 80px rgba(0,0,0,.4);
        }
        .hero-img-card img {
            width: 100%;
            height: 380px;
            border-radius: 12px;
            object-fit: cover;
            display: block;
        }
        .hero-meta {
            margin-top: 16px;
            display: flex;
            gap: 14px;
        }
        .hero-meta-item {
            flex: 1;
            background: rgba(255,255,255,.09);
            border-radius: 8px;
            padding: 10px 12px;
            text-align: center;
            transition: background .2s, transform .2s;
        }
        .hero-meta-item:hover { background: rgba(255,255,255,.15); transform: translateY(-3px); }
        .hero-meta-item .val {
            font-size: 20px; font-weight: 700;
            color: #86efac; display: block;
        }
        .hero-meta-item .lbl {
            font-size: 10px; color: #9ca3af;
            text-transform: uppercase; letter-spacing: .5px;
        }

        /* ── STATS BAR ───────────────────────────────────── */
        .stats-bar {
            background: var(--primary-dk);
            padding: 26px 24px;
        }
        .stats-bar-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
        }
        .stat-item {
            text-align: center;
            padding: 8px 16px;
            border-right: 1px solid rgba(255,255,255,.2);
            position: relative;
        }
        .stat-item:last-child { border-right: none; }
        .stat-item .num {
            display: block;
            font-size: 28px;
            font-weight: 800;
            color: var(--white);
            transition: transform .2s;
        }
        .stat-item:hover .num { transform: scale(1.12); }
        .stat-item .desc {
            font-size: 12px;
            color: rgba(255,255,255,.75);
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        /* ── SECTION BASE ────────────────────────────────── */
        section { padding: 84px 24px; }
        .section-inner { max-width: 1200px; margin: 0 auto; }
        .section-header { text-align: center; margin-bottom: 56px; }
        .section-tag {
            display: inline-block;
            background: var(--primary-lt);
            color: var(--primary-dk);
            font-size: 12px; font-weight: 700;
            padding: 4px 14px;
            border-radius: 100px;
            text-transform: uppercase; letter-spacing: .8px;
            margin-bottom: 14px;
        }
        .section-header h2 {
            font-size: 34px; font-weight: 800;
            color: #111827; margin-bottom: 12px;
        }
        .section-header p {
            font-size: 16px; color: var(--gray);
            max-width: 560px; margin: 0 auto;
        }

        /* ── FEATURES ────────────────────────────────────── */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        .feature-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 28px 24px;
            transition: box-shadow .3s, transform .3s, border-color .3s;
            cursor: default;
            position: relative;
            overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), #86efac);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .3s;
        }
        .feature-card:hover::before { transform: scaleX(1); }
        .feature-card:hover {
            box-shadow: 0 12px 40px rgba(0,0,0,.1);
            transform: translateY(-6px);
            border-color: rgba(94,175,74,.25);
        }
        .feature-icon {
            width: 52px; height: 52px;
            background: var(--primary-lt);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 18px;
            transition: background .3s, transform .3s;
        }
        .feature-card:hover .feature-icon {
            background: var(--primary);
            transform: rotate(-6deg) scale(1.1);
        }
        .feature-icon i { font-size: 22px; color: var(--primary-dk); transition: color .3s; }
        .feature-card:hover .feature-icon i { color: var(--white); }
        .feature-card h3 {
            font-size: 17px; font-weight: 700;
            color: #111827; margin-bottom: 8px;
        }
        .feature-card p { font-size: 14px; color: var(--gray); line-height: 1.7; }

        /* ── HOW IT WORKS ────────────────────────────────── */
        .how-bg { background: var(--gray-lt); }
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
            position: relative;
        }
        .steps-line {
            position: absolute;
            top: 35px;
            left: 12.5%; right: 12.5%;
            height: 2px;
            background: #e5e7eb;
            z-index: 0;
            overflow: hidden;
        }
        .steps-line-fill {
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, var(--primary) 0%, #86efac 100%);
            transition: width 1.2s cubic-bezier(.4,0,.2,1);
        }
        .step-item {
            text-align: center;
            padding: 0 16px;
            position: relative;
            z-index: 1;
        }
        .step-num {
            width: 72px; height: 72px;
            background: #e5e7eb;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            font-size: 22px; font-weight: 800;
            color: var(--gray);
            border: 4px solid var(--white);
            box-shadow: 0 0 0 3px #e5e7eb;
            transition: background .5s, color .5s, box-shadow .5s, transform .3s;
            position: relative;
        }
        .step-num.active {
            background: var(--primary);
            color: var(--white);
            box-shadow: 0 0 0 3px var(--primary-lt);
        }
        .step-num::before {
            content: '';
            position: absolute;
            inset: -6px;
            border-radius: 50%;
            border: 2px solid var(--primary);
            opacity: 0;
            transform: scale(1.3);
            transition: opacity .4s, transform .4s;
        }
        .step-num.active::before { opacity: .3; transform: scale(1); }
        .step-item h3 {
            font-size: 15px; font-weight: 700;
            color: #111827; margin-bottom: 8px;
        }
        .step-item p { font-size: 13px; color: var(--gray); line-height: 1.6; }

        /* ── MODULES ─────────────────────────────────────── */
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .module-row {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 22px 20px;
            transition: border-color .25s, box-shadow .25s, transform .25s;
        }
        .module-row:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 20px rgba(94,175,74,.12);
            transform: translateX(4px);
        }
        .module-dot {
            width: 42px; height: 42px; min-width: 42px;
            background: var(--primary-lt);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            transition: background .25s, transform .25s;
        }
        .module-row:hover .module-dot {
            background: var(--primary);
            transform: scale(1.1) rotate(-5deg);
        }
        .module-dot i { font-size: 18px; color: var(--primary); transition: color .25s; }
        .module-row:hover .module-dot i { color: var(--white); }
        .module-row h4 {
            font-size: 15px; font-weight: 700;
            color: #111827; margin-bottom: 4px;
        }
        .module-row p { font-size: 13px; color: var(--gray); line-height: 1.55; }

        /* ── CTA BANNER ──────────────────────────────────── */
        .cta-section {
            background: linear-gradient(135deg, #2d4a25 0%, #1e2a1c 100%);
            text-align: center;
            padding: 80px 24px;
            position: relative;
            overflow: hidden;
        }
        .cta-section::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            border-radius: 50%;
            border: 1px solid rgba(94,175,74,.15);
            top: 50%; left: 50%;
            transform: translate(-50%,-50%);
            animation: rotateSlow 20s linear infinite;
        }
        .cta-section::after {
            content: '';
            position: absolute;
            width: 700px; height: 700px;
            border-radius: 50%;
            border: 1px solid rgba(94,175,74,.08);
            top: 50%; left: 50%;
            transform: translate(-50%,-50%);
            animation: rotateSlow 30s linear infinite reverse;
        }
        .cta-inner { position: relative; z-index: 1; }
        .cta-section h2 {
            font-size: 34px; font-weight: 800;
            color: var(--white); margin-bottom: 14px;
        }
        .cta-section p {
            font-size: 16px; color: #9ca3af;
            margin-bottom: 32px;
            max-width: 500px; margin-left: auto; margin-right: auto;
        }

        /* ── FOOTER ──────────────────────────────────────── */
        footer { background: #111827; padding: 44px 24px 28px; }
        .footer-inner { max-width: 1200px; margin: 0 auto; }
        .footer-top {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 40px;
            padding-bottom: 32px;
            border-bottom: 1px solid #1f2937;
        }
        .footer-brand img { height: 36px; margin-bottom: 14px; filter: brightness(0) invert(1); opacity: .8; }
        .footer-brand p { font-size: 13px; color: #6b7280; line-height: 1.7; }
        .footer-col h5 {
            font-size: 12px; font-weight: 700;
            color: #9ca3af; text-transform: uppercase;
            letter-spacing: .8px; margin-bottom: 14px;
        }
        .footer-col ul { list-style: none; }
        .footer-col ul li { margin-bottom: 8px; }
        .footer-col ul li a {
            font-size: 13px; color: #6b7280;
            transition: color .2s, padding-left .2s;
            display: inline-block;
        }
        .footer-col ul li a:hover { color: var(--primary); padding-left: 4px; }
        .footer-bottom {
            display: flex; align-items: center;
            justify-content: space-between;
            padding-top: 24px; flex-wrap: wrap; gap: 10px;
        }
        .footer-bottom p { font-size: 12px; color: #4b5563; }
        .footer-badge {
            display: inline-flex; align-items: center;
            gap: 6px; font-size: 12px; color: #4b5563;
        }
        .footer-badge i { color: var(--primary); }

        /* ── SCROLL PROGRESS BAR ─────────────────────────── */
        .scroll-progress {
            position: fixed;
            top: 0; left: 0;
            height: 3px;
            width: 0;
            background: linear-gradient(90deg, var(--primary), #86efac);
            z-index: 300;
            transition: width .1s;
        }

        /* ── BACK TO TOP ─────────────────────────────────── */
        .back-top {
            position: fixed;
            bottom: 32px; right: 28px;
            width: 44px; height: 44px;
            background: var(--primary);
            color: var(--white);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            box-shadow: 0 4px 20px rgba(94,175,74,.4);
            cursor: pointer;
            opacity: 0;
            transform: translateY(16px);
            transition: opacity .3s, transform .3s, background .2s;
            z-index: 150;
            border: none;
        }
        .back-top.visible { opacity: 1; transform: translateY(0); }
        .back-top:hover { background: var(--primary-dk); }

        /* ── ECOSISTEMA GOBE ─────────────────────────────── */
        .gobe-bg { background: var(--gray-lt); }
        .gobe-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }
        .gobe-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 28px 20px 24px;
            text-align: center;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            transition: border-color .25s, box-shadow .25s, transform .25s;
            position: relative;
            overflow: hidden;
        }
        .gobe-card::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .3s;
        }
        .gobe-card:hover::after { transform: scaleX(1); }
        .gobe-card:hover {
            border-color: transparent;
            box-shadow: 0 10px 40px rgba(0,0,0,.11);
            transform: translateY(-6px);
            color: inherit;
        }
        .gobe-icon {
            width: 60px; height: 60px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px;
            transition: transform .3s;
        }
        .gobe-card:hover .gobe-icon { transform: scale(1.12) rotate(-5deg); }
        .gobe-card h4 { font-size: 16px; font-weight: 700; color: #111827; margin: 0; }
        .gobe-card p  { font-size: 13px; color: var(--gray); margin: 0; line-height: 1.5; }
        .gobe-card .gobe-link {
            font-size: 12px; font-weight: 600;
            display: inline-flex; align-items: center; gap: 5px;
            margin-top: 4px;
            opacity: .7;
            transition: opacity .2s;
        }
        .gobe-card:hover .gobe-link { opacity: 1; }

        /* colores por sistema */
        .gobe-siscor   .gobe-icon { background: #e8f0fe; color: #1a73e8; }
        .gobe-siscor::after       { background: #1a73e8; }
        .gobe-siscor   .gobe-link { color: #1a73e8; }

        .gobe-mamore   .gobe-icon { background: #fce8e6; color: #d93025; }
        .gobe-mamore::after       { background: #d93025; }
        .gobe-mamore   .gobe-link { color: #d93025; }

        .gobe-auditoria .gobe-icon { background: #fef3cd; color: #b45309; }
        .gobe-auditoria::after     { background: #b45309; }
        .gobe-auditoria .gobe-link { color: #b45309; }

        .gobe-gaceta   .gobe-icon { background: var(--primary-lt); color: var(--primary-dk); }
        .gobe-gaceta::after       { background: var(--primary); }
        .gobe-gaceta   .gobe-link { color: var(--primary-dk); }

        @media (max-width: 900px) { .gobe-grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 500px) { .gobe-grid { grid-template-columns: 1fr; } }

        /* ── RIPPLE ──────────────────────────────────────── */
        .ripple-ring {
            position: absolute;
            border-radius: 50%;
            border: 2px solid rgba(94,175,74,.5);
            width: 10px; height: 10px;
            pointer-events: none;
            animation: ripple .7s ease-out forwards;
        }

        /* ── RESPONSIVE ──────────────────────────────────── */
        @media (max-width: 900px) {
            .hero-inner { grid-template-columns: 1fr; }
            .hero h1 { font-size: 34px; }
            .hero-img { display: none; }
            .features-grid { grid-template-columns: 1fr 1fr; }
            .steps-grid { grid-template-columns: 1fr 1fr; gap: 32px; }
            .steps-line { display: none; }
            .stats-bar-inner { grid-template-columns: 1fr 1fr; }
            .footer-top { grid-template-columns: 1fr; gap: 28px; }
        }
        @media (max-width: 600px) {
            .nav-links { display: none; }
            .features-grid { grid-template-columns: 1fr; }
            .modules-grid { grid-template-columns: 1fr; }
            .steps-grid { grid-template-columns: 1fr; }
            .hero h1 { font-size: 28px; }
            .hero-buttons { flex-direction: column; }
        }
    </style>
</head>
<body>

{{-- Barra de progreso de scroll --}}
<div class="scroll-progress" id="scrollProgress"></div>

{{-- ── NAVBAR ────────────────────────────────────────────── --}}
<nav class="navbar" id="navbar">
    <div class="nav-inner">
        <a href="/" class="nav-brand">
            <img src="{{ asset('images/icon.png') }}" alt="SYSALMACEN">
            <div class="nav-brand-text">
                <span class="sys-name">SYSALMACEN</span>
                <span class="sys-sub">Gobernación del Beni</span>
            </div>
        </a>
        <ul class="nav-links" id="navLinks">
            <li><a href="#funcionalidades" class="nav-link">Funcionalidades</a></li>
            <li><a href="#modulos" class="nav-link">Módulos</a></li>
            <li><a href="#como-funciona" class="nav-link">¿Cómo funciona?</a></li>
            <li><a href="#ecosistema" class="nav-link">Otros sistemas</a></li>
            <li><a href="#contacto" class="nav-link">Contacto</a></li>
            <li><a href="{{ url('/admin') }}" class="nav-cta"><i class="fa-solid fa-right-to-bracket"></i> Ingresar</a></li>
        </ul>
    </div>
</nav>

{{-- ── HERO ──────────────────────────────────────────────── --}}
<section class="hero" id="hero">
    <canvas id="hero-canvas"></canvas>
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>
    <div class="hero-orb hero-orb-3"></div>
    <div class="hero-inner">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="dot"></span> Sistema Oficial — Gobernación del Beni
            </div>
            <h1>Sistema de Gestión de <span>Almacenes</span></h1>
            <p>
                Control integral de inventario, ingresos por compra, egresos, solicitudes entre unidades y reportes anuales para todas las sucursales de la institución.
            </p>
            <div class="hero-buttons">
                <a href="{{ url('/admin') }}" class="btn-primary" id="heroBtn">
                    <i class="fa-solid fa-right-to-bracket"></i> Acceder al sistema
                </a>
                <a href="#funcionalidades" class="btn-outline">
                    <i class="fa-solid fa-circle-info"></i> Conocer más
                </a>
            </div>
        </div>
        <div class="hero-img">
            <div class="hero-img-card">
                <img src="{{ asset('images/banner.jpg') }}" alt="Sistema de almacenes">
                <div class="hero-meta">
                    <div class="hero-meta-item">
                        <span class="val"><i class="fa-solid fa-boxes-stacked"></i></span>
                        <span class="lbl">Inventario</span>
                    </div>
                    <div class="hero-meta-item">
                        <span class="val"><i class="fa-solid fa-truck-ramp-box"></i></span>
                        <span class="lbl">Ingresos</span>
                    </div>
                    <div class="hero-meta-item">
                        <span class="val"><i class="fa-solid fa-file-invoice"></i></span>
                        <span class="lbl">Reportes</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── STATS BAR ─────────────────────────────────────────── --}}
<div class="stats-bar">
    <div class="stats-bar-inner">
        <div class="stat-item">
            <span class="num">Multi</span>
            <span class="desc">Almacenes simultáneos</span>
        </div>
        <div class="stat-item">
            <span class="num">100%</span>
            <span class="desc">Trazabilidad de stock</span>
        </div>
        <div class="stat-item">
            <span class="num">Anual</span>
            <span class="desc">Gestión por gestión</span>
        </div>
        <div class="stat-item">
            <span class="num">GOBE</span>
            <span class="desc">Gobernación del Beni</span>
        </div>
    </div>
</div>

{{-- ── FUNCIONALIDADES ───────────────────────────────────── --}}
<section id="funcionalidades">
    <div class="section-inner">
        <div class="section-header reveal">
            <span class="section-tag">Funcionalidades</span>
            <h2>Todo lo que necesita su almacén</h2>
            <p>Herramientas diseñadas para la gestión eficiente del inventario institucional en el contexto gubernamental boliviano.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card reveal" style="transition-delay:.05s">
                <div class="feature-icon"><i class="fa-solid fa-arrow-trend-up"></i></div>
                <h3>Ingresos por Compra</h3>
                <p>Registro completo de solicitudes de compra con facturación, detalle por artículo y control de stock en tiempo real.</p>
            </div>
            <div class="feature-card reveal" style="transition-delay:.12s">
                <div class="feature-icon"><i class="fa-solid fa-arrow-trend-down"></i></div>
                <h3>Egresos y Salidas</h3>
                <p>Control de salidas directas y por solicitud de pedido. Decremento automático de stock con trazabilidad completa.</p>
            </div>
            <div class="feature-card reveal" style="transition-delay:.19s">
                <div class="feature-icon"><i class="fa-solid fa-envelope-open-text"></i></div>
                <h3>Bandeja de Solicitudes</h3>
                <p>Flujo completo de solicitudes entre unidades: creación, envío, aprobación, entrega y anulación con estados auditables.</p>
            </div>
            <div class="feature-card reveal" style="transition-delay:.26s">
                <div class="feature-icon"><i class="fa-solid fa-calendar-check"></i></div>
                <h3>Gestión Anual</h3>
                <p>Apertura y cierre de gestiones por almacén. Traspaso automático de saldos al inicio del nuevo año fiscal.</p>
            </div>
            <div class="feature-card reveal" style="transition-delay:.33s">
                <div class="feature-icon"><i class="fa-solid fa-chart-bar"></i></div>
                <h3>Reportes y Exportación</h3>
                <p>Reportes por dirección administrativa, partida presupuestaria y artículo. Exportación a Excel e impresión directa.</p>
            </div>
            <div class="feature-card reveal" style="transition-delay:.4s">
                <div class="feature-icon"><i class="fa-solid fa-hand-holding-heart"></i></div>
                <h3>Módulo de Donaciones</h3>
                <p>Gestión de ingresos y egresos por donación (SEDEGES), con donantes, centros de acogida y categorías.</p>
            </div>
        </div>
    </div>
</section>

{{-- ── CÓMO FUNCIONA ─────────────────────────────────────── --}}
<section id="como-funciona" class="how-bg">
    <div class="section-inner">
        <div class="section-header reveal">
            <span class="section-tag">¿Cómo funciona?</span>
            <h2>Flujo de trabajo simplificado</h2>
            <p>El sistema guía a cada funcionario a través de un proceso claro y auditable.</p>
        </div>
        <div class="steps-grid" id="stepsGrid">
            <div class="steps-line"><div class="steps-line-fill" id="stepsLineFill"></div></div>
            <div class="step-item reveal" style="transition-delay:.05s">
                <div class="step-num" id="step1">1</div>
                <h3>Ingreso de material</h3>
                <p>El almacenero registra la solicitud de compra con la factura y el detalle de artículos recibidos.</p>
            </div>
            <div class="step-item reveal" style="transition-delay:.18s">
                <div class="step-num" id="step2">2</div>
                <h3>Solicitud de pedido</h3>
                <p>El funcionario solicita artículos desde su unidad. La solicitud se envía al almacén correspondiente.</p>
            </div>
            <div class="step-item reveal" style="transition-delay:.31s">
                <div class="step-num" id="step3">3</div>
                <h3>Aprobación y entrega</h3>
                <p>El almacenero revisa la solicitud en su bandeja, la aprueba y registra la entrega descontando el stock.</p>
            </div>
            <div class="step-item reveal" style="transition-delay:.44s">
                <div class="step-num" id="step4">4</div>
                <h3>Reporte y cierre</h3>
                <p>Al finalizar la gestión anual, se generan reportes y se traspasan los saldos al siguiente año.</p>
            </div>
        </div>
    </div>
</section>

{{-- ── MÓDULOS ───────────────────────────────────────────── --}}
<section id="modulos">
    <div class="section-inner">
        <div class="section-header reveal">
            <span class="section-tag">Módulos del sistema</span>
            <h2>Cobertura integral del almacén</h2>
            <p>Cada módulo cubre una necesidad específica de la gestión de inventario institucional.</p>
        </div>
        <div class="modules-grid">
            <div class="module-row reveal from-left" style="transition-delay:.05s">
                <div class="module-dot"><i class="fa-solid fa-warehouse"></i></div>
                <div>
                    <h4>Almacenes y Sub-almacenes</h4>
                    <p>Configuración de sucursales con sus direcciones administrativas y unidades principales. Soporte para múltiples sub-almacenes.</p>
                </div>
            </div>
            <div class="module-row reveal from-right" style="transition-delay:.05s">
                <div class="module-dot"><i class="fa-solid fa-tag"></i></div>
                <div>
                    <h4>Artículos y Partidas Presupuestarias</h4>
                    <p>Catálogo de artículos organizados por partida presupuestaria (código boliviano tipo 3.x.x). Búsqueda y filtros avanzados.</p>
                </div>
            </div>
            <div class="module-row reveal from-left" style="transition-delay:.14s">
                <div class="module-dot"><i class="fa-solid fa-users"></i></div>
                <div>
                    <h4>Usuarios y Funcionarios</h4>
                    <p>Registro de usuarios vinculados a funcionarios activos del sistema de personal (MAMORE). Roles y permisos por Voyager.</p>
                </div>
            </div>
            <div class="module-row reveal from-right" style="transition-delay:.14s">
                <div class="module-dot"><i class="fa-solid fa-truck"></i></div>
                <div>
                    <h4>Proveedores y Modalidades</h4>
                    <p>Gestión de proveedores y modalidades de compra. Vinculación directa con las facturas de ingreso.</p>
                </div>
            </div>
            <div class="module-row reveal from-left" style="transition-delay:.23s">
                <div class="module-dot"><i class="fa-solid fa-print"></i></div>
                <div>
                    <h4>Reportes Oficiales</h4>
                    <p>Kardex, inventarios anuales por DA, por partida y detalle general. Formato de impresión institucional.</p>
                </div>
            </div>
            <div class="module-row reveal from-right" style="transition-delay:.23s">
                <div class="module-dot"><i class="fa-solid fa-shield-halved"></i></div>
                <div>
                    <h4>Auditoría y Trazabilidad</h4>
                    <p>Registro de cambios con Laravel Auditing. Historial de egresos, anulaciones y reaperturas de gestión.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── ECOSISTEMA GOBE ──────────────────────────────────── --}}
<section class="gobe-bg" id="ecosistema">
    <div class="section-inner">
        <div class="section-header reveal">
            <span class="section-tag">Gobernación del Beni</span>
            <h2>Ecosistema Digital GOBE</h2>
            <p>Otros sistemas institucionales de la Gobernación del Departamento del Beni.</p>
        </div>
        <div class="gobe-grid">
            <a href="https://siscor.beni.gob.bo/" target="_blank" rel="noopener"
               class="gobe-card gobe-siscor reveal" style="transition-delay:.05s">
                <div class="gobe-icon"><i class="fa-solid fa-file-lines"></i></div>
                <h4>SISCOR</h4>
                <p>Sistema de Correspondencia y gestión documental institucional.</p>
                <span class="gobe-link"><i class="fa-solid fa-arrow-up-right-from-square"></i> siscor.beni.gob.bo</span>
            </a>
            <a href="https://mamore.beni.gob.bo/" target="_blank" rel="noopener"
               class="gobe-card gobe-mamore reveal" style="transition-delay:.13s">
                <div class="gobe-icon"><i class="fa-solid fa-users-gear"></i></div>
                <h4>MAMORÉ</h4>
                <p>Sistema de gestión de personal y recursos humanos de la Gobernación.</p>
                <span class="gobe-link"><i class="fa-solid fa-arrow-up-right-from-square"></i> mamore.beni.gob.bo</span>
            </a>
            <a href="https://auditoria.beni.gob.bo/" target="_blank" rel="noopener"
               class="gobe-card gobe-auditoria reveal" style="transition-delay:.21s">
                <div class="gobe-icon"><i class="fa-solid fa-magnifying-glass-chart"></i></div>
                <h4>AUDITORÍA</h4>
                <p>Sistema de control interno y auditoría gubernamental institucional.</p>
                <span class="gobe-link"><i class="fa-solid fa-arrow-up-right-from-square"></i> auditoria.beni.gob.bo</span>
            </a>
            <a href="https://gaceta.beni.gob.bo/" target="_blank" rel="noopener"
               class="gobe-card gobe-gaceta reveal" style="transition-delay:.29s">
                <div class="gobe-icon"><i class="fa-solid fa-newspaper"></i></div>
                <h4>GACETA</h4>
                <p>Gaceta oficial del Departamento del Beni — publicaciones y normativa.</p>
                <span class="gobe-link"><i class="fa-solid fa-arrow-up-right-from-square"></i> gaceta.beni.gob.bo</span>
            </a>
        </div>
    </div>
</section>

{{-- ── CTA ────────────────────────────────────────────────── --}}
<section class="cta-section" id="contacto">
    <div class="cta-inner">
        <h2 class="reveal">¿Listo para gestionar su almacén?</h2>
        <p class="reveal" style="transition-delay:.12s">Acceda al sistema con sus credenciales institucionales asignadas por el administrador.</p>
        <div class="reveal" style="transition-delay:.24s">
            <a href="{{ url('/admin') }}" class="btn-primary" style="display:inline-flex; font-size:16px; padding:15px 36px;">
                <i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión
            </a>
        </div>
    </div>
</section>

{{-- ── FOOTER ────────────────────────────────────────────── --}}
<footer>
    <div class="footer-inner">
        <div class="footer-top">
            <div class="footer-brand reveal from-left">
                <img src="{{ asset('images/icon.png') }}" alt="SYSALMACEN">
                <p>Sistema de Gestión de Almacenes de la Gobernación del Departamento del Beni — Bolivia. Administrado por la Unidad de Desarrollo de Software.</p>
            </div>
            <div class="footer-col reveal" style="transition-delay:.1s">
                <h5>Acceso</h5>
                <ul>
                    <li><a href="{{ url('/admin') }}">Iniciar sesión</a></li>
                    <li><a href="{{ url('/admin/login') }}">Panel administrativo</a></li>
                </ul>
            </div>
            <div class="footer-col reveal" style="transition-delay:.2s">
                <h5>Institución</h5>
                <ul>
                    <li><a href="https://www.beni.gob.bo" target="_blank">beni.gob.bo</a></li>
                    <li><a href="https://siscor.beni.gob.bo/" target="_blank">SISCOR</a></li>
                    <li><a href="https://mamore.beni.gob.bo/" target="_blank">MAMORÉ</a></li>
                    <li><a href="https://auditoria.beni.gob.bo/" target="_blank">Auditoría</a></li>
                    <li><a href="https://gaceta.beni.gob.bo/" target="_blank">Gaceta</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© {{ date('Y') }} Gobernación del Beni — SYSALMACEN v{{ config('app.version', '1.0') }}</p>
            <span class="footer-badge">
                <i class="fa-solid fa-leaf"></i> Desarrollado por Unidad de Software GOBE
            </span>
        </div>
    </div>
</footer>

{{-- Botón back to top --}}
<button class="back-top" id="backTop" title="Volver arriba">
    <i class="fa-solid fa-chevron-up"></i>
</button>

<script>
(function () {
    'use strict';

    /* ── SCROLL PROGRESS ─────────────────────────────── */
    const progress = document.getElementById('scrollProgress');
    const navbar   = document.getElementById('navbar');
    const backTop  = document.getElementById('backTop');

    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY;
        const total    = document.body.scrollHeight - window.innerHeight;
        progress.style.width = (scrolled / total * 100) + '%';

        navbar.classList.toggle('scrolled', scrolled > 40);
        backTop.classList.toggle('visible', scrolled > 400);
    }, { passive: true });

    backTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    /* ── SMOOTH SCROLL for nav links ─────────────────── */
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const target = document.querySelector(a.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    /* ── ACTIVE NAV LINK on scroll ───────────────────── */
    const sections  = document.querySelectorAll('section[id], div[id="contacto"]');
    const navLinks  = document.querySelectorAll('.nav-link');
    const observer  = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                navLinks.forEach(l => l.style.color = '');
                const active = document.querySelector(`.nav-link[href="#${entry.target.id}"]`);
                if (active) active.style.color = 'var(--primary)';
            }
        });
    }, { rootMargin: '-40% 0px -55% 0px' });
    sections.forEach(s => observer.observe(s));

    /* ── SCROLL REVEAL ───────────────────────────────── */
    const revealObs = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                revealObs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal').forEach(el => revealObs.observe(el));

    /* ── STEPS ANIMATION ─────────────────────────────── */
    const stepsGrid    = document.getElementById('stepsGrid');
    const stepsLineFill = document.getElementById('stepsLineFill');
    const stepNums     = [1,2,3,4].map(n => document.getElementById('step'+n));
    let stepsAnimated  = false;

    const stepsObs = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting && !stepsAnimated) {
            stepsAnimated = true;
            stepsLineFill.style.width = '100%';
            stepNums.forEach((num, i) => {
                setTimeout(() => num && num.classList.add('active'), 200 + i * 280);
            });
        }
    }, { threshold: 0.3 });
    if (stepsGrid) stepsObs.observe(stepsGrid);

    /* ── RIPPLE on btn-primary ───────────────────────── */
    document.querySelectorAll('.btn-primary').forEach(btn => {
        btn.addEventListener('click', function (e) {
            const r    = document.createElement('span');
            const rect = btn.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height) * 2;
            r.className = 'ripple-ring';
            r.style.cssText = `
                width:${size}px; height:${size}px;
                left:${e.clientX - rect.left - size/2}px;
                top:${e.clientY - rect.top  - size/2}px;
            `;
            btn.style.position = 'relative';
            btn.appendChild(r);
            setTimeout(() => r.remove(), 700);
        });
    });

    /* ── CANVAS PARTICLES (hero) ─────────────────────── */
    const canvas = document.getElementById('hero-canvas');
    const ctx    = canvas.getContext('2d');
    let particles = [];
    let W, H;

    function resize() {
        const hero = document.getElementById('hero');
        W = canvas.width  = hero.offsetWidth;
        H = canvas.height = hero.offsetHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    function Particle() {
        this.reset();
    }
    Particle.prototype.reset = function () {
        this.x    = Math.random() * W;
        this.y    = Math.random() * H;
        this.r    = Math.random() * 2 + .5;
        this.dx   = (Math.random() - .5) * .4;
        this.dy   = (Math.random() - .5) * .4;
        this.life = Math.random();
        this.maxL = Math.random() * .6 + .2;
    };
    Particle.prototype.update = function () {
        this.x += this.dx;
        this.y += this.dy;
        this.life += .003;
        if (this.life > 1 || this.x < 0 || this.x > W || this.y < 0 || this.y > H) this.reset();
    };
    Particle.prototype.draw = function () {
        const alpha = Math.sin(this.life * Math.PI) * this.maxL;
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(134,239,172,${alpha})`;
        ctx.fill();
    };

    for (let i = 0; i < 60; i++) particles.push(new Particle());

    /* Draw connecting lines between nearby particles */
    function drawLines() {
        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
                const dx   = particles[i].x - particles[j].x;
                const dy   = particles[i].y - particles[j].y;
                const dist = Math.sqrt(dx*dx + dy*dy);
                if (dist < 100) {
                    ctx.beginPath();
                    ctx.strokeStyle = `rgba(94,175,74,${.12 * (1 - dist/100)})`;
                    ctx.lineWidth   = .5;
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.stroke();
                }
            }
        }
    }

    function animateParticles() {
        ctx.clearRect(0, 0, W, H);
        drawLines();
        particles.forEach(p => { p.update(); p.draw(); });
        requestAnimationFrame(animateParticles);
    }
    animateParticles();

    /* ── MOUSE PARALLAX on hero card ─────────────────── */
    const heroSection = document.getElementById('hero');
    const heroCard    = document.querySelector('.hero-img-card');
    if (heroCard) {
        heroSection.addEventListener('mousemove', e => {
            const rect = heroSection.getBoundingClientRect();
            const cx   = (e.clientX - rect.left) / rect.width  - .5;
            const cy   = (e.clientY - rect.top)  / rect.height - .5;
            heroCard.style.transform = `
                translateY(${-12 + cy * -12}px)
                rotateX(${cy * 6}deg)
                rotateY(${cx * 6}deg)
            `;
        }, { passive: true });
        heroSection.addEventListener('mouseleave', () => {
            heroCard.style.transform = '';
        });
    }

    /* ── FEATURE CARD tilt on hover ──────────────────── */
    document.querySelectorAll('.feature-card').forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const cx   = (e.clientX - rect.left) / rect.width  - .5;
            const cy   = (e.clientY - rect.top)  / rect.height - .5;
            card.style.transform = `translateY(-6px) rotateX(${cy * -5}deg) rotateY(${cx * 5}deg)`;
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });

})();
</script>

</body>
</html>
