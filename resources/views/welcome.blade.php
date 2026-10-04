<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIPDKS — Koperasi Syariah Modern</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ═══════════════════════════════════════════════════
           RESET & VARIABLES
        ═══════════════════════════════════════════════════ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --green-900: #064e3b;
            --green-700: #047857;
            --green-600: #059669;
            --green-500: #10b981;
            --green-400: #34d399;
            --green-300: #6ee7b7;
            --green-200: #a7f3d0;
            --green-100: #d1fae5;
            --green-50:  #ecfdf5;
            --gold-500:  #eab308;
            --gold-400:  #facc15;
            --gold-300:  #fde047;
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-500: #64748b;
            --slate-400: #94a3b8;
            --slate-300: #cbd5e1;
            --slate-200: #e2e8f0;
            --slate-100: #f1f5f9;
            --slate-50:  #f8fafc;
            --white:     #ffffff;
            --font-display: 'Plus Jakarta Sans', 'Inter', system-ui, sans-serif;
            --font-body: 'Inter', system-ui, sans-serif;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.05);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -4px rgba(0,0,0,0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.08), 0 8px 10px -6px rgba(0,0,0,0.04);
            --shadow-2xl: 0 25px 50px -12px rgba(0,0,0,0.15);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --radius-full: 9999px;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font-body);
            background: var(--white);
            color: var(--slate-800);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ═══════════════════════════════════════════════════
           SCROLL ANIMATIONS
        ═══════════════════════════════════════════════════ */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .reveal-left {
            opacity: 0;
            transform: translateX(-60px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal-left.visible {
            opacity: 1;
            transform: translateX(0);
        }
        .reveal-right {
            opacity: 0;
            transform: translateX(60px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal-right.visible {
            opacity: 1;
            transform: translateX(0);
        }
        .reveal-scale {
            opacity: 0;
            transform: scale(0.9);
            transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal-scale.visible {
            opacity: 1;
            transform: scale(1);
        }
        .stagger-1 { transition-delay: 0.1s; }
        .stagger-2 { transition-delay: 0.2s; }
        .stagger-3 { transition-delay: 0.3s; }
        .stagger-4 { transition-delay: 0.4s; }
        .stagger-5 { transition-delay: 0.5s; }
        .stagger-6 { transition-delay: 0.6s; }

        /* ═══════════════════════════════════════════════════
           NAVBAR
        ═══════════════════════════════════════════════════ */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            padding: 0 2rem;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .navbar.scrolled {
            background: rgba(255,255,255,0.88);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .navbar-inner {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
        }
        .nav-brand {
            display: flex; align-items: center; gap: 12px;
            text-decoration: none; font-weight: 800; font-size: 1.25rem;
            color: var(--green-700);
            font-family: var(--font-display);
        }
        .nav-brand-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--green-600), var(--green-400));
            border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 12px rgba(5,150,105,0.3);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .nav-brand:hover .nav-brand-icon {
            transform: scale(1.05) rotate(-3deg);
            box-shadow: 0 6px 20px rgba(5,150,105,0.4);
        }
        .nav-brand-icon svg { width: 22px; height: 22px; fill: none; stroke: #fff; stroke-width: 2.2; stroke-linecap: round; stroke-linejoin: round; }
        .nav-links { display: flex; align-items: center; gap: 2rem; }
        .nav-link {
            position: relative;
            color: var(--slate-600);
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            padding: 6px 0;
            transition: color 0.25s;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px; left: 50%; width: 0; height: 2px;
            background: linear-gradient(90deg, var(--green-500), var(--green-400));
            border-radius: 999px;
            transition: width 0.3s cubic-bezier(0.16, 1, 0.3, 1), left 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .nav-link:hover { color: var(--green-700); }
        .nav-link:hover::after { width: 100%; left: 0; }
        .nav-actions { display: flex; align-items: center; gap: 0.75rem; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 10px 24px;
            border-radius: var(--radius-full);
            font-family: var(--font-body);
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            border: none; cursor: pointer;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            white-space: nowrap;
        }
        .btn-ghost {
            background: transparent;
            color: var(--slate-700);
            border: 1.5px solid var(--slate-200);
        }
        .btn-ghost:hover {
            background: var(--slate-50);
            border-color: var(--green-300);
            color: var(--green-700);
            transform: translateY(-1px);
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--green-600), var(--green-500));
            color: #fff;
            box-shadow: 0 4px 14px rgba(5,150,105,0.35);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--green-700), var(--green-600));
            box-shadow: 0 6px 20px rgba(5,150,105,0.45);
            transform: translateY(-2px);
        }
        .btn-primary:active { transform: translateY(0); }
        .btn-lg {
            padding: 14px 32px;
            font-size: 1rem;
            font-weight: 700;
        }
        .btn-gold {
            background: linear-gradient(135deg, var(--gold-500), var(--gold-400));
            color: var(--slate-900);
            box-shadow: 0 4px 14px rgba(234,179,8,0.35);
        }
        .btn-gold:hover {
            box-shadow: 0 6px 20px rgba(234,179,8,0.5);
            transform: translateY(-2px);
        }
        .btn-outline-green {
            background: transparent;
            color: var(--green-700);
            border: 2px solid var(--green-200);
        }
        .btn-outline-green:hover {
            background: var(--green-50);
            border-color: var(--green-400);
            transform: translateY(-2px);
        }
        .hamburger {
            display: none;
            flex-direction: column; gap: 5px;
            background: none; border: none; cursor: pointer; padding: 8px;
        }
        .hamburger span {
            width: 24px; height: 2.5px; background: var(--slate-700);
            border-radius: 2px; transition: all 0.3s;
        }

        /* ═══════════════════════════════════════════════════
           HERO
        ═══════════════════════════════════════════════════ */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex; align-items: center;
            padding: 8rem 2rem 4rem;
            overflow: hidden;
            background: linear-gradient(180deg, var(--green-50) 0%, var(--white) 100%);
        }
        .hero-bg {
            position: absolute; inset: 0; z-index: 0; overflow: hidden;
        }
        .hero-bg .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: orbFloat 12s ease-in-out infinite;
        }
        .hero-bg .orb-1 {
            width: 600px; height: 600px;
            background: var(--green-300);
            top: -20%; right: -10%;
            animation-delay: 0s;
        }
        .hero-bg .orb-2 {
            width: 400px; height: 400px;
            background: var(--gold-300);
            bottom: -10%; left: -5%;
            animation-delay: -4s;
        }
        .hero-bg .orb-3 {
            width: 300px; height: 300px;
            background: var(--green-200);
            top: 40%; left: 30%;
            animation-delay: -8s;
        }
        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        /* Floating grid pattern */
        .hero-grid {
            position: absolute; inset: 0; z-index: 0;
            background-image:
                linear-gradient(rgba(5,150,105,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(5,150,105,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse at center, black 30%, transparent 70%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 70%);
        }

        .hero-inner {
            max-width: 1280px;
            margin: 0 auto;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        .hero-content { max-width: 580px; }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--white);
            border: 1px solid var(--green-200);
            border-radius: var(--radius-full);
            padding: 8px 18px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--green-700);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
            animation: badgePulse 3s ease-in-out infinite;
        }
        .hero-badge .dot {
            width: 8px; height: 8px;
            background: var(--green-500);
            border-radius: 50%;
            animation: dotPulse 2s ease-in-out infinite;
        }
        @keyframes dotPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }
        @keyframes badgePulse {
            0%, 100% { box-shadow: var(--shadow-sm); }
            50% { box-shadow: 0 2px 12px rgba(5,150,105,0.12); }
        }
        .hero-title {
            font-family: var(--font-display);
            font-size: clamp(2.5rem, 5vw, 3.75rem);
            font-weight: 800;
            line-height: 1.1;
            color: var(--slate-900);
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
        }
        .hero-title .highlight {
            background: linear-gradient(135deg, var(--green-600), var(--green-400));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-title .highlight-gold {
            background: linear-gradient(135deg, var(--gold-500), var(--gold-400));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-desc {
            font-size: 1.125rem;
            color: var(--slate-500);
            line-height: 1.8;
            margin-bottom: 2.5rem;
            max-width: 500px;
        }
        .hero-cta {
            display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;
            margin-bottom: 3rem;
        }
        .hero-trust {
            display: flex; align-items: center; gap: 1rem;
        }
        .hero-trust-avatars {
            display: flex;
        }
        .hero-trust-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            border: 2px solid var(--white);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700;
            margin-left: -8px;
        }
        .hero-trust-avatar:first-child { margin-left: 0; }
        .hero-trust-avatar.av-1 { background: var(--green-100); color: var(--green-700); }
        .hero-trust-avatar.av-2 { background: #dbeafe; color: #1d4ed8; }
        .hero-trust-avatar.av-3 { background: #fef3c7; color: #b45309; }
        .hero-trust-avatar.av-4 { background: #ede9fe; color: #7c3aed; }
        .hero-trust-text { font-size: 0.85rem; color: var(--slate-500); }
        .hero-trust-text strong { color: var(--slate-800); font-weight: 700; }

        /* Hero Visual */
        .hero-visual {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .hero-card {
            background: rgba(255,255,255,0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.8);
            border-radius: var(--radius-lg);
            padding: 1.25rem 1.5rem;
            box-shadow: var(--shadow-lg);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1),
                        box-shadow 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            animation: heroCardFloat 6s ease-in-out infinite;
        }
        .hero-card:nth-child(2) { animation-delay: -2s; margin-left: 2rem; }
        .hero-card:nth-child(3) { animation-delay: -4s; margin-left: 1rem; }
        .hero-card:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: var(--shadow-2xl);
        }
        @keyframes heroCardFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .hc-label {
            font-size: 0.7rem; font-weight: 600; color: var(--slate-400);
            text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px;
        }
        .hc-value {
            font-family: var(--font-display);
            font-size: 1.75rem; font-weight: 800; color: var(--green-700);
            line-height: 1.2;
        }
        .hc-value .currency { font-size: 0.85rem; font-weight: 500; color: var(--slate-400); font-family: var(--font-body); }
        .hc-trend {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 0.78rem; font-weight: 600; color: var(--green-500);
            margin-top: 4px;
        }
        .hc-trend svg { width: 14px; height: 14px; }
        .hc-row {
            display: flex; align-items: center; gap: 12px;
        }
        .hc-icon {
            width: 44px; height: 44px;
            background: var(--green-50);
            border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .hc-icon svg { width: 22px; height: 22px; stroke: var(--green-600); fill: none; stroke-width: 2; }
        .hc-info { flex: 1; }
        .hc-name { font-weight: 700; font-size: 0.9rem; color: var(--slate-800); }
        .hc-sub { font-size: 0.8rem; color: var(--slate-400); }
        .hc-badge {
            padding: 4px 12px;
            border-radius: var(--radius-full);
            font-size: 0.72rem; font-weight: 700;
        }
        .hc-badge-success { background: var(--green-100); color: var(--green-700); }
        .hc-badge-gold { background: #fef3c7; color: #b45309; }

        /* ═══════════════════════════════════════════════════
           STATS BAR
        ═══════════════════════════════════════════════════ */
        .stats-bar {
            background: var(--green-700);
            position: relative;
            overflow: hidden;
        }
        .stats-bar::before {
            content: '';
            position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .stats-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 3.5rem 2rem;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            position: relative;
            z-index: 1;
        }
        .stat-item { text-align: center; color: var(--white); }
        .stat-number {
            font-family: var(--font-display);
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1;
            color: var(--white);
        }
        .stat-number .accent { color: var(--gold-400); }
        .stat-label {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.8);
            margin-top: 8px;
            font-weight: 500;
        }
        .stat-detail {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.5);
            margin-top: 4px;
        }
        .stat-divider {
            width: 1px;
            background: rgba(255,255,255,0.15);
            margin: 0 auto;
        }

        /* ═══════════════════════════════════════════════════
           SECTION COMMON
        ═══════════════════════════════════════════════════ */
        .section {
            padding: 7rem 2rem;
        }
        .section-inner {
            max-width: 1280px;
            margin: 0 auto;
        }
        .section-header {
            text-align: center;
            max-width: 640px;
            margin: 0 auto 4rem;
        }
        .section-tag {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--green-600);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 1rem;
            background: var(--green-50);
            padding: 6px 16px;
            border-radius: var(--radius-full);
            border: 1px solid var(--green-100);
        }
        .section-title {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 2.75rem);
            font-weight: 800;
            line-height: 1.15;
            color: var(--slate-900);
            letter-spacing: -0.02em;
            margin-bottom: 1rem;
        }
        .section-desc {
            font-size: 1.05rem;
            color: var(--slate-500);
            line-height: 1.8;
        }
        .bg-slate { background: var(--slate-50); }
        .bg-green-subtle {
            background: linear-gradient(180deg, var(--green-50) 0%, var(--white) 100%);
        }

        /* ═══════════════════════════════════════════════════
           FEATURES
        ═══════════════════════════════════════════════════ */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }
        .feature-card {
            background: var(--white);
            border: 1px solid var(--slate-100);
            border-radius: var(--radius-xl);
            padding: 2rem;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--green-500), var(--green-300));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-xl);
            border-color: var(--green-100);
        }
        .feature-card:hover::before { transform: scaleX(1); }
        .feature-icon-wrap {
            width: 56px; height: 56px;
            background: var(--green-50);
            border-radius: var(--radius-lg);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.25rem;
            transition: all 0.3s;
        }
        .feature-card:hover .feature-icon-wrap {
            background: var(--green-100);
            transform: scale(1.05);
        }
        .feature-icon-wrap svg { width: 28px; height: 28px; stroke: var(--green-600); fill: none; stroke-width: 1.8; }
        .feature-name {
            font-family: var(--font-display);
            font-weight: 700; font-size: 1.1rem;
            color: var(--slate-800);
            margin-bottom: 0.5rem;
        }
        .feature-text {
            font-size: 0.9rem;
            color: var(--slate-500);
            line-height: 1.7;
        }

        /* ═══════════════════════════════════════════════════
           HOW IT WORKS (STEPPER)
        ═══════════════════════════════════════════════════ */
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            position: relative;
        }
        .steps-grid::before {
            content: '';
            position: absolute;
            top: 40px; left: 12.5%; right: 12.5%;
            height: 2px;
            background: linear-gradient(90deg, var(--green-200), var(--green-400), var(--green-200));
            z-index: 0;
        }
        .step-item {
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .step-num {
            width: 80px; height: 80px;
            margin: 0 auto 1.25rem;
            background: var(--white);
            border: 2px solid var(--green-200);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-display);
            font-size: 1.5rem; font-weight: 800;
            color: var(--green-600);
            transition: all 0.3s;
        }
        .step-item:hover .step-num {
            background: var(--green-600);
            color: var(--white);
            border-color: var(--green-600);
            transform: scale(1.1);
            box-shadow: 0 8px 24px rgba(5,150,105,0.3);
        }
        .step-title {
            font-family: var(--font-display);
            font-weight: 700; font-size: 1rem;
            color: var(--slate-800);
            margin-bottom: 0.5rem;
        }
        .step-desc {
            font-size: 0.85rem;
            color: var(--slate-500);
            line-height: 1.6;
        }

        /* ═══════════════════════════════════════════════════
           MARKETPLACE SPOTLIGHT
        ═══════════════════════════════════════════════════ */
        .marketplace-section {
            position: relative;
            overflow: hidden;
            background: linear-gradient(180deg, var(--white) 0%, var(--green-50) 50%, var(--white) 100%);
        }
        .marketplace-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }
        .marketplace-info {}
        .marketplace-copy {
            font-size: 1rem;
            color: var(--slate-500);
            line-height: 1.8;
            margin: 1rem 0 2rem;
        }
        .marketplace-highlights {
            display: flex; flex-direction: column; gap: 1rem;
            margin-bottom: 2rem;
        }
        .mh-item {
            display: flex; align-items: flex-start; gap: 12px;
        }
        .mh-icon {
            width: 36px; height: 36px;
            background: var(--green-100);
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .mh-icon svg { width: 18px; height: 18px; stroke: var(--green-600); fill: none; stroke-width: 2; }
        .mh-text { font-size: 0.9rem; color: var(--slate-600); line-height: 1.6; }
        .mh-text strong { color: var(--slate-800); }

        .marketplace-preview {
            position: relative;
        }
        .mp-card {
            background: var(--white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-2xl);
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.06);
        }
        .mp-header {
            background: linear-gradient(135deg, var(--green-600), var(--green-500));
            padding: 1.5rem 2rem;
            color: var(--white);
            display: flex; align-items: center; justify-content: space-between;
        }
        .mp-header-title {
            font-family: var(--font-display);
            font-weight: 700; font-size: 1.1rem;
        }
        .mp-header-badge {
            background: rgba(255,255,255,0.2);
            padding: 4px 12px;
            border-radius: var(--radius-full);
            font-size: 0.75rem; font-weight: 600;
        }
        .mp-body { padding: 1.5rem 2rem 2rem; }
        .mp-product-list { display: flex; flex-direction: column; gap: 1rem; }
        .mp-product {
            display: flex; align-items: center; gap: 1rem;
            padding: 1rem;
            background: var(--slate-50);
            border-radius: var(--radius-md);
            transition: all 0.25s;
        }
        .mp-product:hover {
            background: var(--green-50);
            transform: translateX(4px);
        }
        .mp-product-img {
            width: 56px; height: 56px;
            border-radius: var(--radius-md);
            background-size: cover; background-position: center;
            flex-shrink: 0;
        }
        .mp-product-info { flex: 1; min-width: 0; }
        .mp-product-name { font-weight: 700; font-size: 0.9rem; color: var(--slate-800); }
        .mp-product-shop { font-size: 0.78rem; color: var(--slate-400); margin-top: 2px; }
        .mp-product-price {
            font-family: var(--font-display);
            font-weight: 800; font-size: 0.95rem;
            color: var(--green-700);
            white-space: nowrap;
        }

        /* ═══════════════════════════════════════════════════
           AKAD (PRODUCTS)
        ═══════════════════════════════════════════════════ */
        .akad-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        .akad-card {
            background: var(--white);
            border: 1px solid var(--slate-100);
            border-radius: var(--radius-xl);
            padding: 2rem;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: default;
        }
        .akad-card::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--green-500), var(--gold-400));
            transform: scaleX(0);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .akad-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
            border-color: var(--green-100);
        }
        .akad-card:hover::after { transform: scaleX(1); }
        .akad-head {
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 1rem;
        }
        .akad-num {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--green-600), var(--green-500));
            color: var(--white);
            border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-display);
            font-weight: 800; font-size: 1rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(5,150,105,0.25);
        }
        .akad-title {
            font-family: var(--font-display);
            font-weight: 700; font-size: 1.1rem;
            color: var(--slate-800);
        }
        .akad-type {
            font-size: 0.78rem; font-weight: 600; color: var(--green-600);
            margin-top: 2px;
        }
        .akad-desc {
            font-size: 0.9rem;
            color: var(--slate-500);
            line-height: 1.7;
        }

        /* ═══════════════════════════════════════════════════
           SYARIAH PRINCIPLES
        ═══════════════════════════════════════════════════ */
        .syariah-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5rem;
            align-items: center;
        }
        .syariah-visual {
            background: linear-gradient(135deg, var(--green-700) 0%, var(--green-900) 100%);
            border-radius: var(--radius-xl);
            padding: 3rem;
            color: var(--white);
            position: relative;
            overflow: hidden;
        }
        .syariah-visual::before {
            content: '☽';
            position: absolute;
            top: -1.5rem; right: 1rem;
            font-size: 10rem;
            opacity: 0.04;
            animation: moonGlow 8s ease-in-out infinite;
        }
        @keyframes moonGlow {
            0%, 100% { opacity: 0.04; transform: scale(1); }
            50% { opacity: 0.08; transform: scale(1.05); }
        }
        .sv-title {
            font-family: var(--font-display);
            font-size: 1.5rem; font-weight: 800;
            margin-bottom: 0.5rem;
        }
        .sv-sub { font-size: 0.875rem; color: rgba(255,255,255,0.6); margin-bottom: 2rem; }
        .akad-mini-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .akad-mini {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            transition: all 0.3s;
        }
        .akad-mini:hover {
            background: rgba(255,255,255,0.14);
            transform: translateY(-2px);
        }
        .akad-mini-name {
            font-weight: 700; font-size: 0.95rem;
            color: var(--gold-400);
        }
        .akad-mini-desc {
            font-size: 0.8rem; color: rgba(255,255,255,0.65);
            margin-top: 4px; line-height: 1.5;
        }
        .prinsip-list { list-style: none; display: flex; flex-direction: column; gap: 1.25rem; margin-top: 2rem; }
        .prinsip-item {
            display: flex; align-items: flex-start; gap: 14px;
            font-size: 0.95rem; color: var(--slate-600);
            line-height: 1.6;
        }
        .prinsip-check {
            width: 28px; height: 28px;
            background: var(--green-100);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .prinsip-check svg { width: 14px; height: 14px; stroke: var(--green-600); fill: none; stroke-width: 3; }

        /* ═══════════════════════════════════════════════════
           TESTIMONIALS
        ═══════════════════════════════════════════════════ */
        .testi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }
        .testi-card {
            background: var(--white);
            border: 1px solid var(--slate-100);
            border-radius: var(--radius-xl);
            padding: 2rem;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .testi-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--green-100);
        }
        .testi-stars {
            display: flex; gap: 2px;
            margin-bottom: 1rem;
        }
        .testi-stars svg { width: 18px; height: 18px; fill: var(--gold-400); }
        .testi-quote {
            font-size: 0.95rem;
            color: var(--slate-600);
            line-height: 1.75;
            font-style: italic;
            margin-bottom: 1.5rem;
            position: relative;
            padding-left: 1rem;
            border-left: 3px solid var(--green-200);
        }
        .testi-author {
            display: flex; align-items: center; gap: 12px;
        }
        .testi-avatar {
            width: 44px; height: 44px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 0.85rem;
            flex-shrink: 0;
        }
        .testi-avatar.tav-1 { background: var(--green-100); color: var(--green-700); }
        .testi-avatar.tav-2 { background: #dbeafe; color: #1d4ed8; }
        .testi-avatar.tav-3 { background: #fef3c7; color: #b45309; }
        .testi-name { font-weight: 700; font-size: 0.9rem; color: var(--slate-800); }
        .testi-role { font-size: 0.78rem; color: var(--slate-400); margin-top: 2px; }

        /* ═══════════════════════════════════════════════════
           CTA
        ═══════════════════════════════════════════════════ */
        .cta-section {
            position: relative;
            padding: 7rem 2rem;
            background: linear-gradient(135deg, var(--green-700) 0%, var(--green-900) 100%);
            text-align: center;
            color: var(--white);
            overflow: hidden;
        }
        .cta-section::before {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(circle at 20% 50%, rgba(255,255,255,0.06) 0%, transparent 50%),
                radial-gradient(circle at 80% 50%, rgba(250,204,21,0.08) 0%, transparent 50%);
        }
        .cta-orbs {
            position: absolute; inset: 0; overflow: hidden;
        }
        .cta-orbs .orb {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
            animation: orbFloat 15s ease-in-out infinite;
        }
        .cta-orbs .orb-a { width: 300px; height: 300px; top: -100px; left: -50px; }
        .cta-orbs .orb-b { width: 200px; height: 200px; bottom: -80px; right: -30px; animation-delay: -5s; }
        .cta-content { position: relative; z-index: 1; max-width: 640px; margin: 0 auto; }
        .cta-title {
            font-family: var(--font-display);
            font-size: clamp(2rem, 4vw, 2.75rem);
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 1.25rem;
        }
        .cta-desc {
            font-size: 1.05rem;
            color: rgba(255,255,255,0.7);
            line-height: 1.8;
            margin-bottom: 2.5rem;
        }
        .cta-buttons {
            display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;
        }

        /* ═══════════════════════════════════════════════════
           FOOTER
        ═══════════════════════════════════════════════════ */
        .footer {
            background: var(--slate-900);
            color: rgba(255,255,255,0.6);
        }
        .footer-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 4rem 2rem 3rem;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
        }
        .footer-brand-name {
            font-family: var(--font-display);
            font-size: 1.25rem; font-weight: 800;
            color: var(--white);
            margin-bottom: 1rem;
            display: flex; align-items: center; gap: 10px;
        }
        .footer-brand-icon {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--green-500), var(--green-400));
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
        }
        .footer-brand-icon svg { width: 18px; height: 18px; fill: none; stroke: #fff; stroke-width: 2.2; }
        .footer-about {
            font-size: 0.9rem;
            line-height: 1.8;
            max-width: 320px;
        }
        .footer-heading {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(255,255,255,0.9);
            margin-bottom: 1.25rem;
        }
        .footer-links { list-style: none; display: flex; flex-direction: column; gap: 0.6rem; }
        .footer-links a {
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s, padding-left 0.2s;
        }
        .footer-links a:hover { color: var(--green-400); padding-left: 4px; }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.08);
            padding: 1.5rem 2rem;
            text-align: center;
            font-size: 0.8rem;
            color: rgba(255,255,255,0.3);
        }
        .footer-bottom-inner {
            max-width: 1280px;
            margin: 0 auto;
        }

        /* ═══════════════════════════════════════════════════
           RESPONSIVE
        ═══════════════════════════════════════════════════ */
        @media (max-width: 1024px) {
            .hero-inner { grid-template-columns: 1fr; gap: 3rem; }
            .hero-visual { max-width: 480px; }
            .hero-card:nth-child(2) { margin-left: 0; }
            .hero-card:nth-child(3) { margin-left: 0; }
            .stats-inner { grid-template-columns: repeat(2, 1fr); }
            .features-grid { grid-template-columns: repeat(2, 1fr); }
            .steps-grid { grid-template-columns: repeat(2, 1fr); gap: 2rem; }
            .steps-grid::before { display: none; }
            .marketplace-layout { grid-template-columns: 1fr; gap: 3rem; }
            .akad-grid { grid-template-columns: 1fr; max-width: 640px; margin: 0 auto; }
            .syariah-layout { grid-template-columns: 1fr; gap: 3rem; }
            .testi-grid { grid-template-columns: 1fr; max-width: 540px; margin: 0 auto; }
            .footer-inner { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .hamburger { display: flex; }
            .hero { padding: 7rem 1.5rem 3rem; min-height: auto; }
            .hero-title { font-size: 2.2rem; }
            .hero-desc { font-size: 1rem; }
            .section { padding: 4rem 1.5rem; }
            .stats-inner { grid-template-columns: 1fr; padding: 2.5rem 1.5rem; }
            .features-grid { grid-template-columns: 1fr; }
            .steps-grid { grid-template-columns: 1fr; }
            .akad-mini-grid { grid-template-columns: 1fr; }
            .footer-inner { grid-template-columns: 1fr; gap: 2rem; }
        }
        @media (max-width: 480px) {
            .hero-cta { flex-direction: column; align-items: stretch; }
            .hero-cta .btn { text-align: center; }
            .cta-buttons { flex-direction: column; align-items: stretch; }
            .cta-buttons .btn { text-align: center; }
        }

        /* ═══════════════════════════════════════════════════
           MOBILE NAV OVERLAY
        ═══════════════════════════════════════════════════ */
        .mobile-nav {
            display: none;
            position: fixed; inset: 0; z-index: 999;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .mobile-nav.active { opacity: 1; }
        .mobile-nav-panel {
            position: absolute; top: 0; right: 0;
            width: 280px; height: 100%;
            background: var(--white);
            padding: 2rem;
            transform: translateX(100%);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .mobile-nav.active .mobile-nav-panel { transform: translateX(0); }
        .mobile-nav-links { display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1.5rem; }
        .mobile-nav-links a {
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            color: var(--slate-700);
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s;
        }
        .mobile-nav-links a:hover { background: var(--green-50); color: var(--green-700); }
        .mobile-nav-close {
            position: absolute; top: 1.5rem; right: 1.5rem;
            background: none; border: none; cursor: pointer;
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
        }
        .mobile-nav-close svg { width: 20px; height: 20px; stroke: var(--slate-600); fill: none; stroke-width: 2; }

        @media (max-width: 768px) {
            .mobile-nav { display: block; }
        }
    </style>
</head>
<body>

<!-- ═══════════════════════════════════════════════════
     NAVBAR
═══════════════════════════════════════════════════ -->
<nav class="navbar" id="navbar">
    <div class="navbar-inner">
        <a class="nav-brand" href="/">
            <div class="nav-brand-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            </div>
            SIPDKS
        </a>
        <div class="nav-links">
            <a href="#beranda" class="nav-link">Beranda</a>
            <a href="#fitur" class="nav-link">Fitur</a>
            <a href="#cara-kerja" class="nav-link">Cara Kerja</a>
            <a href="#marketplace" class="nav-link">Marketplace</a>
            <a href="#akad" class="nav-link">Akad Syariah</a>
            <a href="#testimoni" class="nav-link">Testimoni</a>
        </div>
        <div class="nav-actions">
            @if (Route::has('login'))
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">Dashboard</a>
                    @elseif(auth()->user()->isKetua())
                        <a href="{{ route('ketua.dashboard') }}" class="btn btn-ghost">Dashboard</a>
                    @elseif(auth()->user()->isPengurus())
                        <a href="{{ route('pengurus.dashboard') }}" class="btn btn-ghost">Dashboard</a>
                    @elseif(auth()->user()->isDps())
                        <a href="{{ route('dps.dashboard') }}" class="btn btn-ghost">Dashboard</a>
                    @elseif(auth()->user()->isPelanggan())
                        <a href="{{ route('pelanggan.dashboard') }}" class="btn btn-primary">Dashboard</a>
                    @else
                        <a href="{{ route('anggota.dashboard') }}" class="btn btn-primary">Dashboard</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-ghost">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost">Masuk</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary">Daftar Gratis</a>
                    @endif
                @endauth
            @endif
            <button class="hamburger" onclick="toggleMobileNav()" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</nav>

<!-- Mobile Nav -->
<div class="mobile-nav" id="mobileNav" onclick="closeMobileNav(event)">
    <div class="mobile-nav-panel" onclick="event.stopPropagation()">
        <button class="mobile-nav-close" onclick="toggleMobileNav()">
            <svg viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
        <div class="mobile-nav-links">
            <a href="#beranda" onclick="toggleMobileNav()">Beranda</a>
            <a href="#fitur" onclick="toggleMobileNav()">Fitur</a>
            <a href="#cara-kerja" onclick="toggleMobileNav()">Cara Kerja</a>
            <a href="#marketplace" onclick="toggleMobileNav()">Marketplace</a>
            <a href="#akad" onclick="toggleMobileNav()">Akad Syariah</a>
            <a href="#testimoni" onclick="toggleMobileNav()">Testimoni</a>
            <hr style="border:none;border-top:1px solid #e2e8f0;margin:0.5rem 0;">
            @if (Route::has('login'))
                @auth
                @else
                    <a href="{{ route('login') }}">Masuk</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" style="color:#059669;">Daftar Gratis</a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════════ -->
<section class="hero" id="beranda">
    <div class="hero-bg">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>
    <div class="hero-grid"></div>
    <div class="hero-inner">
        <div class="hero-content">
            <div class="hero-badge reveal reveal-left">
                <span class="dot"></span>
                Sistem Informasi Platform Digital Koperasi Syariah
            </div>
            <h1 class="hero-title reveal reveal-left stagger-1">
                Satu Platform untuk<br>
                <span class="highlight">Simpanan, Pembiayaan,</span><br>
                & <span class="highlight-gold">Marketplace Syariah</span>
            </h1>
            <p class="hero-desc reveal reveal-left stagger-2">
                Kelola seluruh aktivitas koperasi dari satu tempat — pantau simpanan, ajukan pembiayaan berbasis akad, buka toko anggota, dan ikuti proses keuangan secara transparan dan syariah.
            </p>
            <div class="hero-cta reveal reveal-left stagger-3">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                        Daftar Sekarang
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                @endif
                <a href="#fitur" class="btn btn-outline-green btn-lg">Pelajari Fitur</a>
            </div>
            <div class="hero-trust reveal reveal-left stagger-4">
                <div class="hero-trust-avatars">
                    <div class="hero-trust-avatar av-1">AH</div>
                    <div class="hero-trust-avatar av-2">SN</div>
                    <div class="hero-trust-avatar av-3">MF</div>
                    <div class="hero-trust-avatar av-4">+99</div>
                </div>
                <div class="hero-trust-text">
                    Bergabung dengan <strong>100+ anggota</strong> aktif
                </div>
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-card reveal reveal-right stagger-1">
                <div class="hc-label">Layanan Koperasi Terintegrasi</div>
                <div class="hc-value">Mudah &amp; Transparan</div>
                <div class="hc-trend">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                    Simpanan, pembiayaan, dan marketplace dalam satu akun
                </div>
            </div>
            <div class="hero-card reveal reveal-right stagger-2">
                <div class="hc-row">
                    <div class="hc-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div class="hc-info">
                        <div class="hc-name">Proses Sesuai Prinsip Syariah</div>
                        <div class="hc-sub">Diperiksa Pengurus dan divalidasi DPS</div>
                    </div>
                    <span class="hc-badge hc-badge-success">Terawasi</span>
                </div>
            </div>
            <div class="hero-card reveal reveal-right stagger-3">
                <div class="hc-label">Transaksi dan Pelaporan</div>
                <div class="hc-value" style="color: var(--gold-500);">Tercatat Otomatis</div>
                <div class="hc-trend" style="color: var(--gold-500);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    Transparan, mudah dipantau, dan siap dilaporkan
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════
     STATS BAR
═══════════════════════════════════════════════════ -->
<div class="stats-bar">
    <div class="stats-inner">
        <div class="stat-item reveal stagger-1">
            <div class="stat-number" data-target="1">0</div>
            <div class="stat-label">Platform Terpadu</div>
            <div class="stat-detail">Simpanan, pembiayaan & marketplace</div>
        </div>
        <div class="stat-item reveal stagger-2">
            <div class="stat-number" data-target="4">0</div>
            <div class="stat-label">Akad Pembiayaan</div>
            <div class="stat-detail">Murabahah, Mudharabah, Musyarakah, Ijarah</div>
        </div>
        <div class="stat-item reveal stagger-3">
            <div class="stat-number"><span class="counter" data-target="24">0</span><span class="accent">/7</span></div>
            <div class="stat-label">Akses Digital</div>
            <div class="stat-detail">Pantau layanan kapan saja & di mana saja</div>
        </div>
        <div class="stat-item reveal stagger-4">
            <div class="stat-number" style="font-size:2rem;">DPS</div>
            <div class="stat-label">Pengawasan Syariah</div>
            <div class="stat-detail">Alur akad lebih mudah ditinjau</div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════
     FITUR UNGGULAN
═══════════════════════════════════════════════════ -->
<section class="section" id="fitur">
    <div class="section-inner">
        <div class="section-header">
            <div class="section-tag reveal">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                Fitur Unggulan
            </div>
            <h2 class="section-title reveal stagger-1">Semua yang Kamu Butuhkan<br>dalam Satu Platform</h2>
            <p class="section-desc reveal stagger-2">Kelola keuangan koperasi syariah dengan mudah — dari simpanan, pembiayaan, marketplace anggota, hingga laporan keuangan real-time.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card reveal stagger-1">
                <div class="feature-icon-wrap">
                    <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                </div>
                <div class="feature-name">Dashboard Real-Time</div>
                <div class="feature-text">Pantau saldo, riwayat transaksi, dan perkembangan simpanan kapan saja dan di mana saja.</div>
            </div>
            <div class="feature-card reveal stagger-2">
                <div class="feature-icon-wrap">
                    <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="feature-name">Pembiayaan Syariah</div>
                <div class="feature-text">Ajukan pembiayaan sesuai tujuan dana, termasuk modal usaha, dengan pilihan akad syariah yang jelas.</div>
            </div>
            <div class="feature-card reveal stagger-3">
                <div class="feature-icon-wrap">
                    <svg viewBox="0 0 24 24"><path d="M3 3h18v18H3z"/><path d="M3 9h18M9 21V9"/></svg>
                </div>
                <div class="feature-name">Laporan Keuangan</div>
                <div class="feature-text">Laporan keuangan otomatis sesuai standar koperasi — neraca, SHU, dan laporan arus kas instan.</div>
            </div>
            <div class="feature-card reveal stagger-1">
                <div class="feature-icon-wrap">
                    <svg viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
                </div>
                <div class="feature-name">Marketplace Anggota</div>
                <div class="feature-text">Anggota dapat membuka toko, menjual produk, mengelola pesanan, dan membangun rekam usaha digital.</div>
            </div>
            <div class="feature-card reveal stagger-2">
                <div class="feature-icon-wrap">
                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                </div>
                <div class="feature-name">Manajemen Anggota</div>
                <div class="feature-text">Data anggota, status, simpanan pokok, dan aktivitas koperasi tercatat rapi dalam satu sistem.</div>
            </div>
            <div class="feature-card reveal stagger-3">
                <div class="feature-icon-wrap">
                    <svg viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
                <div class="feature-name">Analitik & Insight</div>
                <div class="feature-text">Data analitik untuk pengurus: tren pembiayaan, performa simpanan, aktivitas marketplace, dan kesehatan portofolio.</div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════
     CARA KERJA (HOW IT WORKS)
═══════════════════════════════════════════════════ -->
<section class="section bg-green-subtle" id="cara-kerja">
    <div class="section-inner">
        <div class="section-header">
            <div class="section-tag reveal">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Cara Kerja
            </div>
            <h2 class="section-title reveal stagger-1">Mulai dalam 4 Langkah Mudah</h2>
            <p class="section-desc reveal stagger-2">Proses pendaftaran hingga bertransaksi didesain sederhana dan cepat.</p>
        </div>
        <div class="steps-grid">
            <div class="step-item reveal stagger-1">
                <div class="step-num">01</div>
                <div class="step-title">Daftar Akun</div>
                <div class="step-desc">Buat akun gratis sebagai anggota atau pelanggan koperasi.</div>
            </div>
            <div class="step-item reveal stagger-2">
                <div class="step-num">02</div>
                <div class="step-title">Setor Simpanan</div>
                <div class="step-desc">Pilih jenis simpanan dan setor via QRIS atau transfer bank.</div>
            </div>
            <div class="step-item reveal stagger-3">
                <div class="step-num">03</div>
                <div class="step-title">Ajukan Pembiayaan</div>
                <div class="step-desc">Pilih akad yang sesuai, ajukan pembiayaan, dan tunggu persetujuan pengurus.</div>
            </div>
            <div class="step-item reveal stagger-4">
                <div class="step-num">04</div>
                <div class="step-title">Buka Toko & Jualan</div>
                <div class="step-desc">Buka toko di marketplace, unggah produk, dan mulai berjualan.</div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════
     MARKETPLACE SPOTLIGHT
═══════════════════════════════════════════════════ -->
<section class="section marketplace-section" id="marketplace">
    <div class="section-inner">
        <div class="marketplace-layout">
            <div class="marketplace-info reveal-left">
                <div class="section-tag" style="display:inline-flex;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                    Marketplace Anggota
                </div>
                <h2 class="section-title" style="text-align:left; margin-top:1rem;">Etalase UMKM yang Terhubung dengan Ekosistem Koperasi</h2>
                <p class="marketplace-copy">Produk anggota tampil lebih menarik, toko lebih mudah ditemukan, dan aktivitas usaha dapat menjadi konteks saat anggota mengajukan pembiayaan modal usaha.</p>
                <div class="marketplace-highlights">
                    <div class="mh-item">
                        <div class="mh-icon">
                            <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <div class="mh-text"><strong>Toko Terverifikasi</strong> — Hanya toko anggota aktif yang tampil di marketplace.</div>
                    </div>
                    <div class="mh-item">
                        <div class="mh-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div class="mh-text"><strong>Akad Transparan</strong> — Setiap transaksi marketplace mengikuti prinsip murabahah.</div>
                    </div>
                    <div class="mh-item">
                        <div class="mh-icon">
                            <svg viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                        </div>
                        <div class="mh-text"><strong>Rekam Usaha</strong> — Riwayat penjualan mendukung analisis pembiayaan anggota.</div>
                    </div>
                </div>
                <a href="{{ route('marketplace') }}" class="btn btn-primary">
                    Jelajahi Marketplace
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="marketplace-preview reveal-right">
                <div class="mp-card">
                    <div class="mp-header">
                        <div class="mp-header-title">Marketplace Anggota</div>
                        <div class="mp-header-badge">Live</div>
                    </div>
                    <div class="mp-body">
                        @if(isset($kategoriList) && $kategoriList->count())
                            <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1rem;">
                                <a href="{{ route('home') }}" style="padding:6px 14px;border-radius:999px;font-size:0.75rem;font-weight:600;text-decoration:none;{{ empty($kategori) ? 'background:#059669;color:#fff;' : 'background:#f1f5f9;color:#475569;' }}">Semua</a>
                                @foreach($kategoriList as $k)
                                    <a href="{{ route('home', ['kategori' => $k]) }}" style="padding:6px 14px;border-radius:999px;font-size:0.75rem;font-weight:600;text-decoration:none;{{ (isset($kategori) && $kategori == $k) ? 'background:#059669;color:#fff;' : 'background:#f1f5f9;color:#475569;' }}">{{ $k }}</a>
                                @endforeach
                            </div>
                        @endif
                        @if ($featuredProducts && $featuredProducts->count())
                            <div class="mp-product-list">
                                @foreach ($featuredProducts as $product)
                                    <div class="mp-product">
                                        <div class="mp-product-img" style="background-image: url('{{ $product->foto_url ?: 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=200&q=80' }}');"></div>
                                        <div class="mp-product-info">
                                            <div class="mp-product-name">{{ $product->nama }}</div>
                                            <div class="mp-product-shop">{{ $product->toko?->nama_toko ?? 'Toko Anggota' }} · {{ $product->kategori ?? 'Umum' }}</div>
                                        </div>
                                        <div class="mp-product-price">Rp {{ number_format((float) $product->harga, 0, ',', '.') }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div style="text-align:center;padding:2rem;color:#94a3b8;font-size:0.9rem;">
                                Belum ada produk aktif saat ini.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════
     PRODUK SYARIAH (AKAD)
═══════════════════════════════════════════════════ -->
<section class="section bg-slate" id="akad">
    <div class="section-inner">
        <div class="section-header">
            <div class="section-tag reveal">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Produk Keuangan
            </div>
            <h2 class="section-title reveal stagger-1">Akad Syariah yang Tersedia</h2>
            <p class="section-desc reveal stagger-2">Setiap pengajuan pembiayaan tetap memakai akad yang jelas, sementara tujuan dana membantu pengurus memahami kebutuhan anggota.</p>
        </div>
        <div class="akad-grid">
            <div class="akad-card reveal stagger-1">
                <div class="akad-head">
                    <div class="akad-num">01</div>
                    <div>
                        <div class="akad-title">Simpanan Wadi'ah</div>
                        <div class="akad-type">Titipan · Tanpa Risiko</div>
                    </div>
                </div>
                <div class="akad-desc">Dana dititipkan kepada koperasi, dapat diambil kapan saja. Koperasi memberikan bonus (hibah) atas kebijakan manajemen.</div>
            </div>
            <div class="akad-card reveal stagger-2">
                <div class="akad-head">
                    <div class="akad-num">02</div>
                    <div>
                        <div class="akad-title">Simpanan Mudharabah</div>
                        <div class="akad-type">Bagi Hasil · Jangka Pendek/Panjang</div>
                    </div>
                </div>
                <div class="akad-desc">Simpanan dengan sistem bagi hasil (nisbah) yang disepakati bersama. Keuntungan dibagi sesuai porsi yang telah ditentukan.</div>
            </div>
            <div class="akad-card reveal stagger-3">
                <div class="akad-head">
                    <div class="akad-num">03</div>
                    <div>
                        <div class="akad-title">Pembiayaan Murabahah</div>
                        <div class="akad-type">Jual Beli · Margin Tetap</div>
                    </div>
                </div>
                <div class="akad-desc">Pembiayaan pembelian barang dengan harga jual yang transparan. Koperasi membeli barang lalu menjualnya kepada anggota dengan margin yang disepakati.</div>
            </div>
            <div class="akad-card reveal stagger-4">
                <div class="akad-head">
                    <div class="akad-num">04</div>
                    <div>
                        <div class="akad-title">Pembiayaan Musyarakah</div>
                        <div class="akad-type">Kerjasama · Bagi Hasil</div>
                    </div>
                </div>
                <div class="akad-desc">Kemitraan usaha antara koperasi dan anggota. Keuntungan dan kerugian dibagi sesuai porsi modal yang disertakan masing-masing pihak.</div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════
     PRINSIP SYARIAH
═══════════════════════════════════════════════════ -->
<section class="section" id="syariah">
    <div class="section-inner">
        <div class="syariah-layout">
            <div class="reveal-left">
                <div class="section-tag" style="display:inline-flex;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Komitmen Kami
                </div>
                <h2 class="section-title" style="text-align:left; margin-top:1rem;">Berlandaskan Nilai-Nilai Islam yang Kuat</h2>
                <p class="section-desc" style="text-align:left;">Kami berkomitmen menjalankan operasional koperasi sesuai prinsip syariah: transparan, amanah, dan mudah diaudit oleh pengurus serta pengawas syariah.</p>
                <ul class="prinsip-list">
                    <li class="prinsip-item">
                        <div class="prinsip-check">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <span>Akad jelas untuk setiap pembiayaan dan transaksi utama</span>
                    </li>
                    <li class="prinsip-item">
                        <div class="prinsip-check">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <span>Transparan dan amanah lewat histori, dokumen, dan laporan digital</span>
                    </li>
                    <li class="prinsip-item">
                        <div class="prinsip-check">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <span>DPS dapat meninjau akad, produk, opini, dan temuan pengawasan</span>
                    </li>
                    <li class="prinsip-item">
                        <div class="prinsip-check">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <span>Aktivitas anggota, toko, dan pembiayaan tercatat dalam alur yang sama</span>
                    </li>
                </ul>
            </div>
            <div class="syariah-visual reveal-right">
                <div class="sv-title">Prinsip Dasar Syariah</div>
                <div class="sv-sub">Akad yang tersedia di platform kami</div>
                <div class="akad-mini-grid">
                    <div class="akad-mini">
                        <div class="akad-mini-name">Mudharabah</div>
                        <div class="akad-mini-desc">Bagi hasil modal usaha</div>
                    </div>
                    <div class="akad-mini">
                        <div class="akad-mini-name">Musyarakah</div>
                        <div class="akad-mini-desc">Kemitraan usaha bersama</div>
                    </div>
                    <div class="akad-mini">
                        <div class="akad-mini-name">Murabahah</div>
                        <div class="akad-mini-desc">Jual beli dengan margin</div>
                    </div>
                    <div class="akad-mini">
                        <div class="akad-mini-name">Wadi'ah</div>
                        <div class="akad-mini-desc">Titipan aman tanpa riba</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════
     TESTIMONI
═══════════════════════════════════════════════════ -->
<section class="section bg-slate" id="testimoni">
    <div class="section-inner">
        <div class="section-header">
            <div class="section-tag reveal">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                Testimoni
            </div>
            <h2 class="section-title reveal stagger-1">Dipercaya Ribuan Anggota</h2>
            <p class="section-desc reveal stagger-2">Cerita dari anggota dan pengurus yang merasakan manfaat platform digital kami.</p>
        </div>
        <div class="testi-grid">
            <div class="testi-card reveal stagger-1">
                <div class="testi-stars">
                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <div class="testi-quote">"Platform ini benar-benar memudahkan saya memantau simpanan dan pengajuan pembiayaan. Prosesnya cepat dan transparan."</div>
                <div class="testi-author">
                    <div class="testi-avatar tav-1">AR</div>
                    <div>
                        <div class="testi-name">Ahmad Ridwan</div>
                        <div class="testi-role">Anggota sejak 2021 · Medan</div>
                    </div>
                </div>
            </div>
            <div class="testi-card reveal stagger-2">
                <div class="testi-stars">
                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <div class="testi-quote">"Akhirnya ada koperasi yang benar-benar digital. Tidak perlu antri lagi, semua bisa dari HP. Bagi hasilnya juga kompetitif."</div>
                <div class="testi-author">
                    <div class="testi-avatar tav-2">SN</div>
                    <div>
                        <div class="testi-name">Siti Nurhaliza</div>
                        <div class="testi-role">Anggota sejak 2022 · Binjai</div>
                    </div>
                </div>
            </div>
            <div class="testi-card reveal stagger-3">
                <div class="testi-stars">
                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <div class="testi-quote">"Sebagai pengurus, laporan keuangannya sangat membantu. Semua terekam otomatis dan mudah dipahami."</div>
                <div class="testi-author">
                    <div class="testi-avatar tav-3">MF</div>
                    <div>
                        <div class="testi-name">Muhammad Fauzi</div>
                        <div class="testi-role">Pengurus Koperasi · Deli Serdang</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════
     CTA
═══════════════════════════════════════════════════ -->
<section class="cta-section">
    <div class="cta-orbs">
        <div class="orb orb-a"></div>
        <div class="orb orb-b"></div>
    </div>
    <div class="cta-content reveal-scale">
        <h2 class="cta-title">Mulai Kelola Koperasi<br>Secara Digital</h2>
        <p class="cta-desc">Daftar sebagai anggota, pantau simpanan, ajukan pembiayaan, dan kembangkan toko dalam satu platform yang transparan dan syariah.</p>
        <div class="cta-buttons">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn btn-gold btn-lg">
                    Daftar Sekarang
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            @endif
            <a href="{{ route('marketplace') }}" class="btn btn-outline-green btn-lg" style="border-color:rgba(255,255,255,0.3);color:#fff;">
                Lihat Marketplace
            </a>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════════════ -->
<footer class="footer">
    <div class="footer-inner">
        <div>
            <div class="footer-brand-name">
                <div class="footer-brand-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                </div>
                SIPDKS
            </div>
            <div class="footer-about">Platform digital koperasi syariah yang transparan, aman, dan mudah diaudit. Mendukung simpanan, pembiayaan, marketplace anggota, dan pengawasan syariah internal.</div>
        </div>
        <div>
            <div class="footer-heading">Produk</div>
            <ul class="footer-links">
                <li><a href="#">Simpanan Wadi'ah</a></li>
                <li><a href="#">Simpanan Mudharabah</a></li>
                <li><a href="#">Pembiayaan Murabahah</a></li>
                <li><a href="#">Pembiayaan Musyarakah</a></li>
            </ul>
        </div>
        <div>
            <div class="footer-heading">Perusahaan</div>
            <ul class="footer-links">
                <li><a href="#">Tentang Kami</a></li>
                <li><a href="#">DPS & Manajemen</a></li>
                <li><a href="#">Laporan Keuangan</a></li>
                <li><a href="#">Karir</a></li>
            </ul>
        </div>
        <div>
            <div class="footer-heading">Bantuan</div>
            <ul class="footer-links">
                <li><a href="#">FAQ</a></li>
                <li><a href="#">Hubungi Kami</a></li>
                <li><a href="#">Syarat & Ketentuan</a></li>
                <li><a href="#">Kebijakan Privasi</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="footer-bottom-inner">© {{ date('Y') }} SIPDKS. Hak cipta dilindungi. Dibangun untuk tata kelola koperasi syariah yang transparan.</div>
    </div>
</footer>

<!-- ═══════════════════════════════════════════════════
     JAVASCRIPT
═══════════════════════════════════════════════════ -->
<script>
(function() {
    // ── Navbar scroll effect ──
    const navbar = document.getElementById('navbar');
    let lastScroll = 0;
    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY;
        if (scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        lastScroll = scrollY;
    }, { passive: true });

    // ── Scroll reveal (Intersection Observer) ──
    const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');
    const observerOptions = {
        root: null,
        rootMargin: '0px 0px -60px 0px',
        threshold: 0.1
    };
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);
    revealElements.forEach(el => revealObserver.observe(el));

    // ── Animated counters ──
    const counters = document.querySelectorAll('.counter, .stat-number[data-target]');
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.getAttribute('data-target') || el.getAttribute('data-target'));
                if (isNaN(target)) { counterObserver.unobserve(el); return; }
                animateCounter(el, 0, target, 1500);
                counterObserver.unobserve(el);
            }
        });
    }, { threshold: 0.5 });
    counters.forEach(c => {
        if (c.getAttribute('data-target')) counterObserver.observe(c);
    });

    function animateCounter(el, start, end, duration) {
        const startTime = performance.now();
        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.round(start + (end - start) * eased);
            el.textContent = current;
            if (progress < 1) {
                requestAnimationFrame(update);
            }
        }
        requestAnimationFrame(update);
    }

    // ── Smooth scroll for nav links ──
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                const offset = 80;
                const top = target.getBoundingClientRect().top + window.pageYOffset - offset;
                window.scrollTo({ top, behavior: 'smooth' });
            }
        });
    });

    // ── Hero card tilt on mouse move ──
    const heroVisual = document.querySelector('.hero-visual');
    if (heroVisual && window.innerWidth > 1024) {
        heroVisual.addEventListener('mousemove', (e) => {
            const rect = heroVisual.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;
            heroVisual.style.transform = `perspective(1000px) rotateY(${x * 5}deg) rotateX(${-y * 5}deg)`;
        });
        heroVisual.addEventListener('mouseleave', () => {
            heroVisual.style.transform = 'perspective(1000px) rotateY(0) rotateX(0)';
            heroVisual.style.transition = 'transform 0.5s ease';
        });
        heroVisual.addEventListener('mouseenter', () => {
            heroVisual.style.transition = 'none';
        });
    }

    // ── Mobile nav ──
    window.toggleMobileNav = function() {
        document.getElementById('mobileNav').classList.toggle('active');
    };
    window.closeMobileNav = function(e) {
        if (e.target === e.currentTarget) {
            document.getElementById('mobileNav').classList.remove('active');
        }
    };

    // ── Feature card hover ripple effect ──
    document.querySelectorAll('.feature-card, .akad-card, .testi-card').forEach(card => {
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            this.style.setProperty('--mouse-x', x + 'px');
            this.style.setProperty('--mouse-y', y + 'px');
        });
    });
})();
</script>

</body>
</html>
