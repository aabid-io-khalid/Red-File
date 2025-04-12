<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'PELIXS - Premium Streaming Experience')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Remix Icon CDN -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- GSAP for animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/gsap.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#e50914', 
                        secondary: '#6366f1',
                        dark: '#141414',
                        darker: '#0b0b0b'
                    },
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                        'playfair': ['"Playfair Display"', 'serif']
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s infinite',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0b0b0b;
        }
        
        /* Cinematic background effect */
        .cinematic-bg {
            position: relative;
            overflow: hidden;
            background-size: cover;
            background-position: center;
        }
        
        .cinematic-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(229, 9, 20, 0.7) 0%, rgba(11, 11, 11, 0.9) 100%);
            z-index: 1;
        }
        
        /* Particle effect */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        
        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            pointer-events: none;
            z-index: 0;
        }
        
        /* Neon glow effects */
        .neon-glow {
            box-shadow: 0 0 15px rgba(229, 9, 20, 0.7), 
                        0 0 30px rgba(229, 9, 20, 0.4), 
                        0 0 45px rgba(229, 9, 20, 0.2);
            transition: all 0.3s ease;
        }
        
        .neon-text {
            text-shadow: 0 0 5px rgba(229, 9, 20, 0.7), 
                         0 0 10px rgba(229, 9, 20, 0.4);
        }
        
        /* Glass morphism */
        .glass-card {
            background: rgba(20, 20, 20, 0.6);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }
        
        .glass-input {
            background: rgba(30, 30, 30, 0.6);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .glass-input:focus {
            background: rgba(40, 40, 40, 0.7);
            box-shadow: 0 0 0 2px rgba(229, 9, 20, 0.5);
            border-color: rgba(229, 9, 20, 0.5);
        }
        
        /* Button effects */
        .btn-glow {
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
        }
        
        .btn-glow:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.6s ease;
        }
        
        .btn-glow:hover:before {
            left: 100%;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(20, 20, 20, 0.1);
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(229, 9, 20, 0.5);
            border-radius: 4px;
        }
        
        /* 3D perspective card effect */
        .card-3d-container {
            perspective: 1000px;
        }
        
        .card-3d {
            transition: transform 0.6s ease;
            transform-style: preserve-3d;
        }
        
        .card-3d:hover {
            transform: translateY(-5px) rotateX(5deg);
        }
        
        /* Gradient text */
        .gradient-text {
            background: linear-gradient(to right, #e50914, #ff3366);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        /* Loading effect */
        .loading-bar {
            background: linear-gradient(90deg, #e50914, #ff3366);
            height: 3px;
            width: 0%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;
            animation: loading 2s ease forwards;
        }
        
        @keyframes loading {
            0% { width: 0%; }
            100% { width: 100%; }
        }
        
        /* Background image hover zoom effect */
        .zoom-bg {
            transition: transform 10s ease;
        }
        
        .zoom-trigger:hover .zoom-bg {
            transform: scale(1.1);
        }
    </style>
    
    @yield('additional_styles')
</head>

<body class="text-white min-h-screen overflow-x-hidden">
    <div class="loading-bar"></div>
    
    <div class="min-h-screen">
        @yield('content')
    </div>
    
    <script>
        // Initialize particle effect
        document.addEventListener("DOMContentLoaded", function() {
            // Create particles
            const particlesContainer = document.querySelectorAll('.particles');
            particlesContainer.forEach(container => {
                for (let i = 0; i < 50; i++) {
                    const size = Math.random() * 5 + 1;
                    const particle = document.createElement('div');
                    particle.classList.add('particle');
                    particle.style.width = size + 'px';
                    particle.style.height = size + 'px';
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.top = Math.random() * 100 + '%';
                    particle.style.opacity = Math.random() * 0.5 + 0.1;
                    container.appendChild(particle);
                    
                    // Animate particles
                    gsap.to(particle, {
                        x: Math.random() * 100 - 50,
                        y: Math.random() * 100 - 50,
                        duration: Math.random() * 20 + 10,
                        repeat: -1,
                        yoyo: true,
                        ease: "sine.inOut"
                    });
                }
            });
            
            // Password toggle
            const togglePassword = document.getElementById('togglePassword');
            if (togglePassword) {
                const passwordInput = document.getElementById('password');
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.innerHTML = type === 'password' ? '<i class="ri-eye-off-line"></i>' : '<i class="ri-eye-line"></i>';
                });
            }
            
            const toggleRegPassword = document.getElementById('toggleRegPassword');
            if (toggleRegPassword) {
                const passwordInput = document.getElementById('password');
                toggleRegPassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.innerHTML = type === 'password' ? '<i class="ri-eye-off-line"></i>' : '<i class="ri-eye-line"></i>';
                });
            }
            
            // Password strength indicator
            const passwordStrength = document.getElementById('password');
            const strengthBar = document.getElementById('passwordStrength');
            const strengthText = document.getElementById('passwordStrengthText');
            
            if (passwordStrength && strengthBar && strengthText) {
                passwordStrength.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;
                    
                    if (password.length > 6) strength += 20;
                    if (password.length > 10) strength += 20;
                    if (/[A-Z]/.test(password)) strength += 20;
                    if (/[0-9]/.test(password)) strength += 20;
                    if (/[^A-Za-z0-9]/.test(password)) strength += 20;
                    
                    strengthBar.style.width = strength + '%';
                    
                    if (strength < 40) {
                        strengthBar.style.backgroundColor = '#e74c3c';
                        strengthText.textContent = 'Weak';
                        strengthText.style.color = '#e74c3c';
                    } else if (strength < 80) {
                        strengthBar.style.backgroundColor = '#f39c12';
                        strengthText.textContent = 'Medium';
                        strengthText.style.color = '#f39c12';
                    } else {
                        strengthBar.style.backgroundColor = '#2ecc71';
                        strengthText.textContent = 'Strong';
                        strengthText.style.color = '#2ecc71';
                    }
                });
            }
            
            // Card animations
            gsap.from(".card-3d", {
                y: 30,
                opacity: 0,
                duration: 1,
                stagger: 0.2,
                ease: "power3.out"
            });
            
            // Text animations
            gsap.from("h1, h2", {
                y: -20,
                opacity: 0,
                duration: 0.8,
                stagger: 0.1,
                ease: "back.out(1.7)"
            });
            
            // Form animations
            gsap.from("form .glass-input", {
                x: -20,
                opacity: 0,
                duration: 0.5,
                stagger: 0.1,
                delay: 0.3,
                ease: "power2.out"
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>