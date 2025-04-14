<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PELIXS - Premium Cinema Experience</title>
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

        /* Improved Floating Movie Cards */
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
            max-width: 420px;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(106, 90, 205, 0.2),
                0 0 30px rgba(106, 90, 205, 0.2),
                inset 0 0 0 1px rgba(255, 255, 255, 0.05);
            padding: 2.75rem;
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
            margin-bottom: 2.25rem;
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
        .form-group {
            margin-bottom: 1.5rem;
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

        .form-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .forgot-password {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s;
            position: relative;
        }

        .forgot-password::after {
            content: '';
            position: absolute;
            width: 0;
            height: 1px;
            bottom: -2px;
            left: 0;
            background: var(--primary-color);
            transition: width 0.3s;
        }

        .forgot-password:hover {
            color: var(--primary-color);
        }

        .forgot-password:hover::after {
            width: 100%;
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

        /* Enhanced Remember Me Checkbox */
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1.75rem;
        }

        .custom-checkbox {
            width: 1.2rem;
            height: 1.2rem;
            border-radius: 6px;
            margin-right: 0.75rem;
            appearance: none;
            border: 1px solid var(--input-border);
            background: var(--input-bg);
            cursor: pointer;
            position: relative;
            transition: all 0.3s;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
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

        .remember-label {
            font-size: 0.85rem;
            color: var(--text-muted);
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

        /* Enhanced Signup Prompt */
        .signup-prompt {
            text-align: center;
            margin-top: 1.75rem;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .signup-link {
            display: inline-flex;
            align-items: center;
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            margin-left: 0.5rem;
        }

        .signup-link:hover {
            color: var(--primary-hover);
        }

        .signup-icon {
            margin-left: 0.5rem;
            transition: transform 0.3s;
        }

        .signup-link:hover .signup-icon {
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
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/q6y0Go1tsGEsmtFryDOJo3dEmqu.jpg');"></div> <!-- The Shawshank Redemption -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg');"></div> <!-- The Dark Knight -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/edv5CZvWj09upOsy2Y6IwDhK8bt.jpg');"></div> <!-- Inception -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/pB8BM7pdSp6B6Ih7QZ4DrQ3PmJK.jpg');"></div> <!-- Fight Club -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/saHP97rTPS5eLmrLQEcANmKrsFl.jpg');"></div> <!-- Forrest Gump -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/6oom5QYQ2yQTMJIbnvbkBL9cHo6.jpg');"></div> <!-- The Lord of the Rings: The Fellowship of the Ring -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/or06FN3Dka5tukK1e9sl16pB3iy.jpg');"></div> <!-- Avengers: Endgame -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/f89U3ADr1oiB1s9GkdPOEpXUk5H.jpg');"></div> <!-- The Matrix -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/aKuFiU82s5ISJpGZp7YkIr3kCUd.jpg');"></div> <!-- Goodfellas -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/69Sns8WoET6CfaYlIkHbla4l7nC.jpg');"></div> <!-- Se7en -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/4w5nNgGHTgNB8wzefU1uV9lt4Qm.jpg');"></div> <!-- City of God -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/oRvMaJOmapypFUcQqpgHMZA6qL9.jpg');"></div> <!-- Spirited Away -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/7IiTTgloJzvGI1TAYymCfbfl3vT.jpg');"></div> <!-- Parasite -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/udDclJoHjfjb8Ekgsd4FDteOkCU.jpg');"></div> <!-- Joker -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/rr7E0NoGKxvbkb89eR1GwfoYjpA.jpg');"></div> <!-- Pulp Fiction -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/hEjK9A9BkNXejFW4tfacVPrlpbQ.jpg');"></div> <!-- The Godfather -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/iVZ3JAcAjmguBPLRquCPtdNbL5A.jpg');"></div> <!-- Interstellar -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/s16H6tpK2utvwDtzZ8Qy4qm5Emw.jpg');"></div> <!-- Blade Runner 2049 -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/mSDsSDwaP3E7dEfUPWy4J0djt4O.jpg');"></div> <!-- Coco -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/dW3fQJrshh2wYDoW4HUA7Zab7l.jpg');"></div> <!-- Whiplash -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/8kSerJrhrJWKLk1LViesGcnrUPE.jpg');"></div> <!-- Spider-Man: Into the Spider-Verse -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/velWPhVMQeQKcxggNEU8YmIo52R.jpg');"></div> <!-- Your Name -->
        <div class="movie-poster-bg" style="background-image: url('https://image.tmdb.org/t/p/w500/5KCVkau1HEl7ZzfPsKAPM0sMiKc.jpg');"></div> <!-- Eternal Sunshine of the Spotless Mind -->
    </div>

    <!-- Overlay Gradient -->
    <div class="overlay-gradient"></div>

    <!-- Particle Effects -->
    <div class="particles">
        <div class="particle" style="width: 3px; height: 3px; left: 15%; top: 80%; animation-delay: 0s;"></div>
        <div class="particle" style="width: 2px; height: 2px; left: 25%; top: 90%; animation-delay: 1s;"></div>
        <div class="particle" style="width: 4px; height: 4px; left: 35%; top: 85%; animation-delay: 2s;"></div>
        <div class="particle" style="width: 2px; height: 2px; left: 45%; top: 95%; animation-delay: 3s;"></div>
        <div class="particle" style="width: 3px; height: 3px; left: 55%; top: 85%; animation-delay: 4s;"></div>
        <div class="particle" style="width: 2px; height: 2px; left: 65%; top: 90%; animation-delay: 5s;"></div>
        <div class="particle" style="width: 4px; height: 4px; left: 75%; top: 85%; animation-delay: 6s;"></div>
        <div class="particle" style="width: 3px; height: 3px; left: 85%; top: 95%; animation-delay: 7s;"></div>
    </div>

    <!-- Floating Movie Cards -->
    <div class="floating-covers">
        <div class="movie-card" style="background-image: url('https://image.tmdb.org/t/p/w500/velWPhVMQeQKcxggNEU8YmIo52R.jpg'); width: 150px; height: 225px; top: 15%; left: 10%; animation-delay: 1s;"></div>
        <div class="movie-card" style="background-image: url('https://image.tmdb.org/t/p/w500/f89U3ADr1oiB1s9GkdPOEpXUk5H.jpg'); width: 130px; height: 195px; top: 65%; left: 5%; animation-delay: 2s;"></div>
        <div class="movie-card" style="background-image: url('https://image.tmdb.org/t/p/w500/iVZ3JAcAjmguBPLRquCPtdNbL5A.jpg'); width: 170px; height: 255px; top: 20%; right: 8%; animation-delay: 3s;"></div>
        <div class="movie-card" style="background-image: url('https://image.tmdb.org/t/p/w500/69Sns8WoET6CfaYlIkHbla4l7nC.jpg'); width: 140px; height: 210px; top: 70%; right: 10%; animation-delay: 4s;"></div>
        <div class="movie-card" style="background-image: url('https://image.tmdb.org/t/p/w500/5KCVkau1HEl7ZzfPsKAPM0sMiKc.jpg'); width: 160px; height: 240px; top: 30%; left: 20%; animation-delay: 5s;"></div>
        <div class="movie-card" style="background-image: url('https://image.tmdb.org/t/p/w500/6FfCtAuVAW8XJjZ7eWeLibRLWTw.jpg'); width: 150px; height: 225px; top: 50%; right: 15%; animation-delay: 6s;"></div>
        <div class="movie-card" style="background-image: url('https://image.tmdb.org/t/p/w500/4mChO0ChDLkeUHyFQ0dA2mD4MD8.jpg'); width: 140px; height: 210px; top: 40%; left: 5%; animation-delay: 7s;"></div>
        <div class="movie-card" style="background-image: url('https://image.tmdb.org/t/p/w500/3bhkrj58Vtu7enYsRolD1fZdja1.jpg'); width: 170px; height: 255px; top: 60%; right: 5%; animation-delay: 8s;"></div>
      </div>
      

    <!-- Main Container -->
    <div class="main-container">
        <!-- Auth Card -->
        <div class="auth-card">
            <div class="glass-effect"></div>
            <div class="corner-decoration corner-top-left"></div>
            <div class="corner-decoration corner-top-right"></div>
            <div class="corner-decoration corner-bottom-left"></div>
            <div class="corner-decoration corner-bottom-right"></div>
            
            <!-- Logo & Header -->
            <div class="brand-header">
                <div class="logo-glow"></div>
                <h1 class="brand-logo">PELIXS</h1>
                <p class="brand-tagline">Premium Cinema Experience</p>
            </div>
            
            <h2 class="form-title">
                Welcome <span class="gradient-text" data-text="Back">Back</span>
            </h2>
            
            <!-- Login Form -->
            <form>
                <div class="form-group">
                    <label class="form-label" for="email-input">Email Address</label>
                    <div class="input-container">
                        <input type="email" id="email-input" class="input-field" placeholder="Enter your email" required>
                        <i class="input-icon ri-mail-line"></i>
                        <i class="validation-icon ri-checkbox-circle-fill"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="form-row">
                        <label class="form-label" for="password-input">Password</label>
                        <a href="#" class="forgot-password">Forgot Password?</a>
                    </div>
                    <div class="input-container">
                        <input type="password" id="password-input" class="input-field" placeholder="Enter your password" required>
                        <i class="input-icon ri-lock-line"></i>
                        <button type="button" class="password-toggle">
                            <i class="ri-eye-off-line"></i>
                        </button>
                    </div>
                </div>
                
                <div class="remember-me">
                    <input type="checkbox" id="remember" class="custom-checkbox">
                    <label for="remember" class="remember-label">Remember me for 30 days</label>
                </div>
                
                <button type="submit" class="primary-button">
                    <i class="btn-icon ri-login-circle-line"></i>
                    Sign In
                </button>
            </form>
            
            <div class="divider">
                <span>or continue with</span>
            </div>
            
            <!-- Social Login Options -->
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
            
            <!-- Signup Prompt -->
            <div class="signup-prompt">
                Don't have an account?
                <a href="#" class="signup-link">
                    Sign up
                    <i class="signup-icon ri-arrow-right-line"></i>
                </a>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            &copy; 2025 PELIXS. All rights reserved.
        </div>
    </div>

    <!-- JavaScript for Password Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password-input');
            const passwordToggle = document.querySelector('.password-toggle');
            const toggleIcon = passwordToggle.querySelector('i');
            
            passwordToggle.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleIcon.classList.remove('ri-eye-off-line');
                    toggleIcon.classList.add('ri-eye-line');
                } else {
                    passwordInput.type = 'password';
                    toggleIcon.classList.remove('ri-eye-line');
                    toggleIcon.classList.add('ri-eye-off-line');
                }
            });
            
            // Optional: Add dynamic particles
            function createParticles() {
                const particles = document.querySelector('.particles');
                for (let i = 0; i < 10; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    particle.style.width = Math.floor(Math.random() * 3 + 2) + 'px';
                    particle.style.height = particle.style.width;
                    particle.style.left = Math.floor(Math.random() * 90 + 5) + '%';
                    particle.style.top = Math.floor(Math.random() * 20 + 80) + '%';
                    particle.style.animationDelay = Math.floor(Math.random() * 8) + 's';
                    particles.appendChild(particle);
                }
            }
            
            createParticles();
            
            // Email validation visual feedback
            const emailInput = document.getElementById('email-input');
            const emailValidationIcon = emailInput.nextElementSibling.nextElementSibling;
            
            emailInput.addEventListener('blur', function() {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailPattern.test(emailInput.value)) {
                    emailValidationIcon.style.opacity = 1;
                    emailValidationIcon.style.color = '#4BB543';
                } else if (emailInput.value) {
                    emailValidationIcon.style.opacity = 1;
                    emailValidationIcon.style.color = '#FF355E';
                    emailValidationIcon.className = 'validation-icon ri-close-circle-fill';
                } else {
                    emailValidationIcon.style.opacity = 0;
                }
            });
        });
    </script>
</body>
</html>