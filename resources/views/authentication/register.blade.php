<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PELIXS - Register Your Cinema Experience</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
        
        :root {
            --primary-color: #6A5ACD;
            --primary-hover: #7B68EE;
            --primary-dark: #483D8B;
            --accent-color: #FF355E;
            --accent-glow: rgba(255, 53, 94, 0.5);
            --secondary-color: #141432;
            --bg-dark: #030318;
            --text-light: #FFFFFF;
            --text-muted: rgba(255, 255, 255, 0.7);
            --text-subtle: rgba(255, 255, 255, 0.5);
            --card-bg: rgba(20, 20, 50, 0.8);
            --card-highlight: rgba(106, 90, 205, 0.25);
            --input-bg: rgba(15, 15, 40, 0.7);
            --input-border: rgba(255, 255, 255, 0.1);
            --input-focus: rgba(106, 90, 205, 0.5);
            --input-active: #6A5ACD;
            --button-shadow: 0 8px 16px rgba(106, 90, 205, 0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-light);
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
            line-height: 1.5;
        }

        /* Movie Covers Background with improved animation */
        .movie-covers-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -3;
            overflow: hidden;
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            grid-template-rows: repeat(4, 1fr);
            gap: 12px;
            opacity: 0.6;
            filter: blur(1px);
        }
        
        .movie-poster-bg {
            background-size: cover;
            background-position: center;
            border-radius: 12px;
            transition: all 1.5s ease;
            width: 100%;
            height: 100%;
            min-height: 200px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            animation: posterGlow 8s infinite alternate;
        }
        
        .movie-poster-bg:nth-child(odd) {
            animation-delay: 2s;
        }
        
        .movie-poster-bg:nth-child(3n) {
            animation-delay: 4s;
        }
        
        @keyframes posterGlow {
            0% {
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 5px 20px rgba(106, 90, 205, 0.5);
                transform: scale(1.03);
            }
            100% {
                box-shadow: 0 5px 15px rgba(255, 53, 94, 0.4);
                transform: scale(1);
            }
        }

        /* Improved Overlay Gradient with particle effect */
        .overlay-gradient {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            background: 
                radial-gradient(circle at 25% 25%, rgba(106, 90, 205, 0.15), transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(255, 53, 94, 0.15), transparent 70%),
                linear-gradient(to bottom, rgba(3, 3, 24, 0.7), rgba(20, 20, 60, 0.6), rgba(3, 3, 24, 0.8));
        }
        
        /* Particle effect */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            pointer-events: none;
        }
        
        .particle {
            position: absolute;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            opacity: 0;
            animation: particleFloat 10s infinite linear;
        }
        
        @keyframes particleFloat {
            0% {
                opacity: 0;
                transform: translateY(0) rotate(0deg);
            }
            10% {
                opacity: 0.5;
            }
            90% {
                opacity: 0.5;
            }
            100% {
                opacity: 0;
                transform: translateY(-100vh) rotate(360deg);
            }
        }

        /* Improved Floating Movie Cards - MORE CARDS */
        .floating-covers {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }

        .movie-card {
            position: absolute;
            border-radius: 16px;
            background-size: cover;
            background-position: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            transition: all 0.5s ease;
            animation: float 15s infinite ease-in-out;
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }
        
        .movie-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: cardShine 6s infinite linear;
        }

        .movie-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(
                135deg,
                rgba(106, 90, 205, 0.3) 0%,
                rgba(255, 53, 94, 0.2) 100%
            );
            border-radius: 16px;
        }
        
        @keyframes cardShine {
            0% {
                left: -50%;
                top: -50%;
            }
            100% {
                left: 150%;
                top: 150%;
            }
        }

        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); filter: drop-shadow(0 15px 15px rgba(0, 0, 0, 0.4)); }
            25% { transform: translateY(-15px) rotate(1deg); filter: drop-shadow(0 25px 15px rgba(106, 90, 205, 0.3)); }
            50% { transform: translateY(0) rotate(0deg); filter: drop-shadow(0 15px 15px rgba(0, 0, 0, 0.4)); }
            75% { transform: translateY(10px) rotate(-1deg); filter: drop-shadow(0 25px 15px rgba(255, 53, 94, 0.3)); }
            100% { transform: translateY(0) rotate(0deg); filter: drop-shadow(0 15px 15px rgba(0, 0, 0, 0.4)); }
        }

        /* Main Container with improved lighting */
        .main-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            z-index: 1;
            background: radial-gradient(circle at center, transparent 30%, rgba(3, 3, 24, 0.4) 100%);
        }

        /* Enhanced Auth Card Container */
        .auth-card {
            width: 100%;
            max-width: 480px;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(106, 90, 205, 0.2),
                0 0 30px rgba(106, 90, 205, 0.2),
                inset 0 0 0 1px rgba(255, 255, 255, 0.05);
            padding: 2.5rem;
            position: relative;
            transform: translateZ(0);
            transition: all 0.3s ease;
        }
        
        .auth-card:hover {
            transform: translateY(-5px) translateZ(0);
            box-shadow: 
                0 30px 60px rgba(0, 0, 0, 0.35),
                0 0 0 1px rgba(106, 90, 205, 0.3),
                0 0 40px rgba(106, 90, 205, 0.3),
                inset 0 0 0 1px rgba(255, 255, 255, 0.07);
        }
        
        .glass-effect {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            z-index: -1;
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color), transparent);
            z-index: 1;
        }
        
        /* Animated corner decorations */
        .corner-decoration {
            position: absolute;
            width: 60px;
            height: 60px;
            border-color: var(--primary-color);
            opacity: 0.5;
            z-index: 1;
        }
        
        .corner-top-left {
            top: 0;
            left: 0;
            border-top: 2px solid;
            border-left: 2px solid;
            border-radius: 8px 0 0 0;
        }
        
        .corner-top-right {
            top: 0;
            right: 0;
            border-top: 2px solid;
            border-right: 2px solid;
            border-radius: 0 8px 0 0;
        }
        
        .corner-bottom-left {
            bottom: 0;
            left: 0;
            border-bottom: 2px solid;
            border-left: 2px solid;
            border-radius: 0 0 0 8px;
        }
        
        .corner-bottom-right {
            bottom: 0;
            right: 0;
            border-bottom: 2px solid;
            border-right: 2px solid;
            border-radius: 0 0 8px 0;
        }

        /* Enhanced Logo & Header */
        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .logo-glow {
            position: absolute;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, var(--primary-color) 0%, transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.3;
            filter: blur(20px);
            z-index: -1;
            animation: logoGlow 4s infinite alternate;
        }
        
        @keyframes logoGlow {
            0% {
                opacity: 0.2;
                filter: blur(20px);
            }
            100% {
                opacity: 0.4;
                filter: blur(25px);
            }
        }

        .brand-logo {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            background: linear-gradient(135deg, #6A5ACD, #FF355E);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 0.5rem;
            display: inline-block;
            text-shadow: 0 0 20px rgba(106, 90, 205, 0.5);
            position: relative;
        }
        
        .brand-logo::after {
            content: 'PELIXS';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #6A5ACD, #FF355E);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            opacity: 0.5;
            filter: blur(8px);
            z-index: -1;
        }

        .brand-tagline {
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 400;
            letter-spacing: 0.5px;
        }

        .form-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: var(--text-light);
            text-align: center;
            position: relative;
        }
        
        .form-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            border-radius: 3px;
        }

        .gradient-text {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            position: relative;
        }
        
        .gradient-text::after {
            content: attr(data-text);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            filter: blur(4px);
            opacity: 0.6;
        }

        /* Enhanced Form Elements */
        .form-row {
            display: flex;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
            flex: 1;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }

        .input-container {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-subtle);
            transition: color 0.3s, transform 0.3s;
            font-size: 1.1rem;
        }

        .input-field {
            width: 100%;
            padding: 1rem 1rem 1rem 2.75rem;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 14px;
            color: var(--text-light);
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
            font-family: 'Poppins', sans-serif;
        }

        .input-field:focus {
            outline: none;
            border-color: var(--input-active);
            box-shadow: 
                0 0 0 2px var(--input-focus),
                inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .input-field:focus + .input-icon {
            color: var(--primary-color);
            transform: translateY(-50%) scale(1.1);
        }

        .validation-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0;
            transition: opacity 0.3s, transform 0.3s;
            font-size: 1.1rem;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-subtle);
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1.1rem;
            padding: 5px;
        }

        .password-toggle:focus {
            outline: none;
            color: var(--primary-color);
        }
        
        .password-toggle:hover {
            color: var(--primary-color);
            transform: translateY(-50%) scale(1.1);
        }

        /* Enhanced Terms Checkbox */
        .terms-agreement {
            display: flex;
            align-items: flex-start;
            margin: 1.5rem 0;
        }

        .custom-checkbox {
            width: 1.2rem;
            height: 1.2rem;
            margin-right: 0.75rem;
            margin-top: 0.2rem;
            appearance: none;
            border: 1px solid var(--input-border);
            border-radius: 6px;
            background: var(--input-bg);
            cursor: pointer;
            position: relative;
            transition: all 0.3s;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }

        .custom-checkbox:checked {
            background: var(--primary-color);
            border-color: var(--primary-color);
            box-shadow: 0 0 10px rgba(106, 90, 205, 0.5);
        }

        .custom-checkbox:checked::after {
            content: '✓';
            color: white;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 0.75rem;
        }

        .terms-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .terms-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
        }

        .terms-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--primary-color);
            transition: width 0.3s;
        }

        .terms-link:hover {
            color: var(--primary-hover);
        }

        .terms-link:hover::after {
            width: 100%;
        }

        /* Enhanced Button & Actions */
        .primary-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            box-shadow: var(--button-shadow);
            font-family: 'Poppins', sans-serif;
        }

        .primary-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.5s;
        }

        .primary-button:hover {
            background: linear-gradient(135deg, var(--primary-hover), var(--primary-color));
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(106, 90, 205, 0.4);
        }

        .primary-button:hover::before {
            left: 100%;
        }
        
        .primary-button:active {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(106, 90, 205, 0.3);
        }

        .btn-icon {
            margin-right: 0.75rem;
            font-size: 1.2rem;
        }

        /* Enhanced Divider */
        .divider {
            display: flex;
            align-items: center;
            margin: 1.75rem 0;
            color: var(--text-subtle);
            font-size: 0.85rem;
            position: relative;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.1), transparent);
        }

        .divider::before {
            margin-right: 1rem;
        }

        .divider::after {
            margin-left: 1rem;
        }
        
        .divider span {
            position: relative;
            z-index: 1;
            padding: 0 0.5rem;
            background: var(--card-bg);
        }

        /* Enhanced Social Options */
        .social-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .social-button {
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            padding: 0.9rem;
            border-radius: 14px;
            color: var(--text-light);
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .social-button::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            transition: all 0.6s;
            opacity: 0;
        }

        .social-button:hover {
            background: rgba(30, 30, 70, 0.8);
            transform: translateY(-3px);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .social-button:hover::before {
            left: 100%;
            opacity: 1;
        }

        .social-icon {
            margin-right: 0.75rem;
            font-size: 1.2rem;
            transition: transform 0.3s;
        }
        
        .social-button:hover .social-icon {
            transform: scale(1.2);
        }

        /* Enhanced Login Prompt */
        .login-prompt {
            text-align: center;
            margin-top: 1.75rem;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .login-link {
            display: inline-flex;
            align-items: center;
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            margin-left: 0.5rem;
        }

        .login-link:hover {
            color: var(--primary-hover);
        }

        .login-icon {
            margin-left: 0.5rem;
            transition: transform 0.3s;
        }

        .login-link:hover .login-icon {
            transform: translateX(6px);
        }

        /* Enhanced Footer */
        .footer {
            text-align: center;
            color: var(--text-subtle);
            font-size: 0.8rem;
            padding: 1.5rem 0;
            width: 100%;
            margin-top: 2rem;
            position: relative;
        }
        
        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--text-subtle), transparent);
            border-radius: 2px;
        }
    </style>
</head>
<body>
<!-- Movie Covers Background -->
<div class="movie-covers-bg">
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/1g0dhYtq4irTY1GPXvft6k4YLjm.jpg');"></div> <!-- The Godfather II -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/bwVhmPpydv8P7mWfrmL3XVw0MV5.jpg');"></div> <!-- Back to the Future -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/3bhkrj58Vtu7enYsRolD1fZdja1.jpg');"></div> <!-- The Lion King -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/eP5NL7ZlGoW9tE9qnCdHpOLH1Ke.jpg');"></div> <!-- The Green Mile -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/8tZYtuWezp8JbcsvHYO0O46tFbo.jpg');"></div> <!-- Mad Max: Fury Road -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/9xjZS2rlVxm8SFx8kPC3aIGCOYQ.jpg');"></div> <!-- Titanic -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/u8BMLmwoc7YPHKm7RQ5nkDQfz0H.jpg');"></div> <!-- The Prestige -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/Ab8mkHmkYADjU7wQiOkia9BzGvS.jpg');"></div> <!-- Harry Potter -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/bdHQKEnx9RsMPs298cDNM4PresQ.jpg');"></div> <!-- Eternal Sunshine -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/7sfbEnaARXDDhXz9O6twrqeIlDe.jpg');"></div> <!-- La La Land -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/4ssDuvEDkSArWEdyBl2X5EHvYKU.jpg');"></div> <!-- Parasite -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/wCHtYD50ZBGGzWC5QFRtKl8xy2g.jpg');"></div> <!-- No Country for Old Men -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/8lI1p5cPqgXN2qrps8mNmvKkc5j.jpg');"></div> <!-- Arrival -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/eHuGQ10FUzK1mdOY69wF5pGgEf5.jpg');"></div> <!-- Interstellar -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/m1UfecL7qa0TYwxn4AJKFSXgySL.jpg');"></div> <!-- Blade Runner 2049 -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/hEpWvX6Bp79eLxY1kX5ZZJcme5U.jpg');"></div> <!-- The Matrix -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/saHP97rTPS5eLmrLQEcANmKrsFl.jpg');"></div> <!-- Inception -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/velWPhVMQeQKcxggNEU8YmIo52R.jpg');"></div> <!-- Pulp Fiction -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/q6y0Go1tsGEsmtFryDOJo3dEmqu.jpg');"></div> <!-- The Shawshank Redemption -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/7IiTTgloJzvGI1TAYymCfbfl3vT.jpg');"></div> <!-- Parasite -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/rr7E0NoGKxvbkb89eR1GwfoYjpA.jpg');"></div> <!-- Fight Club -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/xJHokMbljvjADYdit5fK5VQsXEG.jpg');"></div> <!-- Joker -->
    <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/zb6fM1CX41D9rF9hdgclu0peUmy.jpg');"></div> <!-- Oppenheimer -->
</div>

<!-- Overlay Gradient -->
<div class="overlay-gradient"></div>

<!-- Particle Effect -->
<div class="particles" id="particles"></div>

<!-- Floating Movie Cards -->
<div class="floating-covers">
    <div class="movie-card" style="background-image: url('https://image.tmdb.org/t/p/w500/saHP97rTPS5eLmrLQEcANmKrsFl.jpg'); width: 180px; height: 270px; left: 10%; top: 20%; animation-delay: 0s;"></div>
    <div class="movie-card" style="background-image: url('https://image.tmdb.org/t/p/w500/hEpWvX6Bp79eLxY1kX5ZZJcme5U.jpg'); width: 160px; height: 240px; right: 15%; top: 15%; animation-delay: 3s;"></div>
    <div class="movie-card" style="background-image: url('https://image.tmdb.org/t/p/w500/m1UfecL7qa0TYwxn4AJKFSXgySL.jpg'); width: 140px; height: 210px; left: 20%; bottom: 20%; animation-delay: 6s;"></div>
    <div class="movie-card" style="background-image: url('https://image.tmdb.org/t/p/w500/zb6fM1CX41D9rF9hdgclu0peUmy.jpg'); width: 150px; height: 225px; right: 20%; bottom: 15%; animation-delay: 8s;"></div>
</div>

<!-- Main Container -->
<div class="main-container">
    <div class="auth-card">
        <div class="glass-effect"></div>
        <div class="corner-decoration corner-top-left"></div>
        <div class="corner-decoration corner-top-right"></div>
        <div class="corner-decoration corner-bottom-left"></div>
        <div class="corner-decoration corner-bottom-right"></div>
        
        <div class="brand-header">
            <div class="logo-glow"></div>
            <div class="brand-logo">PELIXS</div>
            <div class="brand-tagline">Your Cinema, Your Experience</div>
        </div>
        
        <h2 class="form-title">Create Your <span class="gradient-text" data-text="Account">Account</span></h2>
        
        <form>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="first-name">First Name</label>
                    <div class="input-container">
                        <input type="text" id="first-name" class="input-field" placeholder="John">
                        <i class="input-icon ri-user-line"></i>
                        <i class="validation-icon ri-checkbox-circle-fill"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="last-name">Last Name</label>
                    <div class="input-container">
                        <input type="text" id="last-name" class="input-field" placeholder="Doe">
                        <i class="input-icon ri-user-line"></i>
                        <i class="validation-icon ri-checkbox-circle-fill"></i>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <div class="input-container">
                    <input type="email" id="email" class="input-field" placeholder="john.doe@example.com">
                    <i class="input-icon ri-mail-line"></i>
                    <i class="validation-icon ri-checkbox-circle-fill"></i>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-container">
                    <input type="password" id="password" class="input-field" placeholder="••••••••">
                    <i class="input-icon ri-lock-line"></i>
                    <button type="button" class="password-toggle">
                        <i class="ri-eye-line"></i>
                    </button>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="confirm-password">Confirm Password</label>
                <div class="input-container">
                    <input type="password" id="confirm-password" class="input-field" placeholder="••••••••">
                    <i class="input-icon ri-lock-line"></i>
                    <button type="button" class="password-toggle">
                        <i class="ri-eye-line"></i>
                    </button>
                </div>
            </div>
            
            <div class="terms-agreement">
                <input type="checkbox" id="terms" class="custom-checkbox">
                <label for="terms" class="terms-label">
                    I agree to the <a href="#" class="terms-link">Terms of Service</a> and <a href="#" class="terms-link">Privacy Policy</a>
                </label>
            </div>
            
            <button type="submit" class="primary-button">
                <i class="btn-icon ri-movie-2-line"></i>
                Register Now
            </button>
            
            <div class="divider">
                <span>or continue with</span>
            </div>
            
            <div class="social-options">
                <a href="#" class="social-button">
                    <i class="social-icon ri-google-fill"></i>
                    Google
                </a>
                <a href="#" class="social-button">
                    <i class="social-icon ri-facebook-fill"></i>
                    Facebook
                </a>
            </div>
            
            <div class="login-prompt">
                Already have an account?
                <a href="#" class="login-link">
                    Sign In
                    <i class="login-icon ri-arrow-right-line"></i>
                </a>
            </div>
        </form>
    </div>
    
    <div class="footer">
        &copy; 2025 PELIXS. All rights reserved. Your ultimate cinema companion.
    </div>
</div>

<script>
    // Create particles
    document.addEventListener('DOMContentLoaded', function() {
        const particlesContainer = document.getElementById('particles');
        
        for (let i = 0; i < 50; i++) {
            const particle = document.createElement('div');
            particle.classList.add('particle');
            
            // Random size between 3 and 8px
            const size = Math.random() * 5 + 3;
            particle.style.width = size + 'px';
            particle.style.height = size + 'px';
            
            // Random position
            particle.style.left = Math.random() * 100 + '%';
            particle.style.bottom = -10 + 'px';
            
            // Random animation duration between 8 and 20s
            const duration = Math.random() * 12 + 8;
            particle.style.animationDuration = duration + 's';
            
            // Random animation delay
            particle.style.animationDelay = Math.random() * 5 + 's';
            
            particlesContainer.appendChild(particle);
        }
    });
    
    // Password toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.password-toggle');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const input = this.previousElementSibling.previousElementSibling;
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('ri-eye-line');
                    icon.classList.add('ri-eye-off-line');
                } else {
                    input.type = 'password';
                    icon.classList.remove('ri-eye-off-line');
                    icon.classList.add('ri-eye-line');
                }
            });
        });
    });
</script>
</body>
</html>