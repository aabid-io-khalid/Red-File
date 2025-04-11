@extends('components.layouts.app')

@section('title', 'PELIXS - Reset Password')

@section('additional_styles')
<style>
    .login-image {
        background-image: url('https://images.unsplash.com/photo-1568876694728-451bbf694b83?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80');
        background-size: cover;
        background-position: center;
        position: relative;
    }
    
    .floating-elements > div {
        animation: float 6s infinite ease-in-out;
    }
    
    .floating-elements > div:nth-child(2) {
        animation-delay: 1s;
    }
    
    .floating-elements > div:nth-child(3) {
        animation-delay: 2s;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-15px) rotate(2deg); }
    }
    
    .form-appear {
        animation: formAppear 0.8s forwards;
        opacity: 0;
        transform: translateY(30px);
    }
    
    @keyframes formAppear {
        to { opacity: 1; transform: translateY(0); }
    }
    
    .shimmer {
        position: relative;
        overflow: hidden;
    }
    
    .shimmer::after {
        content: '';
        position: absolute;
        top: -100%;
        left: -100%;
        width: 50%;
        height: 300%;
        background: linear-gradient(
            120deg,
            transparent 20%,
            rgba(255, 255, 255, 0.1) 40%,
            transparent 60%
        );
        transform: rotate(25deg);
        animation: shimmer 6s infinite linear;
    }
    
    @keyframes shimmer {
        to { transform: rotate(25deg) translateX(200%); }
    }
    
    .pulse-btn {
        position: relative;
    }
    
    .pulse-btn::before {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 12px;
        background: linear-gradient(90deg, #e50914, #ff3366, #e50914);
        opacity: 0;
        transition: opacity 0.3s;
        z-index: -1;
        background-size: 200% 100%;
        animation: gradientMove 3s infinite linear;
    }
    
    .pulse-btn:hover::before {
        opacity: 1;
    }
    
    @keyframes gradientMove {
        0% { background-position: 0% 0%; }
        100% { background-position: 200% 0%; }
    }
    
    .letter-animation span {
        display: inline-block;
        opacity: 0;
        transform: translateY(20px);
        animation: letterAppear 0.5s forwards;
    }
    
    @keyframes letterAppear {
        to { opacity: 1; transform: translateY(0); }
    }
    
    .reset-process-item {
        transition: all 0.4s ease;
    }
    
    .reset-process-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(229, 9, 20, 0.2);
    }
    
    .wave-animation {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100px;
        background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg"><path fill="%23e50914" fill-opacity="0.1" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,218.7C672,235,768,245,864,234.7C960,224,1056,192,1152,160C1248,128,1344,96,1392,80L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
        background-size: cover;
        background-repeat: no-repeat;
        z-index: 1;
    }
    
    .particle-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 2;
    }

    .spotlight {
        position: absolute;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(229, 9, 20, 0.2) 0%, transparent 70%);
        border-radius: 50%;
        filter: blur(50px);
        opacity: 0.6;
        z-index: 0;
        animation: movespotlight 15s infinite alternate ease-in-out;
    }

    @keyframes movespotlight {
        0% { transform: translate(-100px, -100px); }
        100% { transform: translate(100px, 100px); }
    }
</style>
@endsection

@section('content')
<div class="min-h-screen flex flex-col md:flex-row relative overflow-hidden">
    <!-- Dynamic Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full z-0">
        <div class="spotlight" style="top: 20%; left: 30%;"></div>
        <div class="spotlight" style="top: 60%; left: 70%;"></div>
    </div>

    <!-- Image Side (Left) -->
    <div class="login-image hidden md:block md:w-1/2 relative">
        <div class="absolute inset-0 backdrop-blur-sm bg-gradient-to-br from-black/60 to-black/20 z-5"></div>
        <div class="wave-animation"></div>
        <div class="particle-overlay" id="particles-left"></div>
        
        <div class="absolute inset-0 flex flex-col justify-between z-10 p-12">
            <div class="letter-animation" id="title-animation">
                <h1 class="text-5xl font-bold text-white tracking-wider">
                    <span class="inline-block" style="animation-delay: 0.1s">P</span>
                    <span class="inline-block" style="animation-delay: 0.15s">E</span>
                    <span class="inline-block" style="animation-delay: 0.2s">L</span>
                    <span class="inline-block" style="animation-delay: 0.25s">I</span>
                    <span class="inline-block" style="animation-delay: 0.3s">X</span>
                    <span class="inline-block" style="animation-delay: 0.35s">S</span>
                </h1>
                <p class="text-gray-200 mt-2 opacity-0" id="tagline">Your premier streaming destination</p>
            </div>
            
            <div class="mb-8 floating-elements">
                <div class="bg-white/10 backdrop-blur-md p-6 rounded-xl max-w-xs glass-card shimmer">
                    <h3 class="font-bold text-xl mb-2 text-primary">Password Recovery</h3>
                    <p class="text-gray-200">We'll send you instructions to reset your password and get you back to watching your favorite content.</p>
                </div>
                
                <div class="mt-6 flex flex-col sm:flex-row gap-4">
                    <div class="bg-white/10 backdrop-blur-md p-5 rounded-xl flex items-center reset-process-item glass-card">
                        <div class="bg-primary/20 p-3 rounded-full mr-4">
                            <i class="ri-mail-check-line text-primary text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold">Check Email</h4>
                            <p class="text-xs text-gray-300">For reset link</p>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md p-5 rounded-xl flex items-center reset-process-item glass-card">
                        <div class="bg-primary/20 p-3 rounded-full mr-4">
                            <i class="ri-lock-unlock-line text-primary text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold">Create New</h4>
                            <p class="text-xs text-gray-300">Secure password</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Form Side (Right) -->
    <div class="md:w-1/2 w-full bg-darker flex items-center justify-center p-8 relative">
        <div class="particle-overlay" id="particles-right"></div>
        <div class="spotlight" style="top: 30%; left: 40%;"></div>
        
        <div class="max-w-md w-full z-10 form-appear">
            <div class="mb-8 text-center md:hidden letter-animation">
                <h1 class="text-4xl font-bold text-primary tracking-wider">
                    <span style="animation-delay: 0.1s">P</span>
                    <span style="animation-delay: 0.15s">E</span>
                    <span style="animation-delay: 0.2s">L</span>
                    <span style="animation-delay: 0.25s">I</span>
                    <span style="animation-delay: 0.3s">X</span>
                    <span style="animation-delay: 0.35s">S</span>
                </h1>
                <p class="text-gray-400 mt-1">Your premier streaming destination</p>
            </div>
            
            <div class="glass-card p-8 rounded-2xl shimmer relative border border-white/5">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-primary/20 to-secondary/20 rounded-2xl blur opacity-30"></div>
                <div class="relative">
                    <h2 class="text-3xl font-bold mb-2 gradient-text">Reset Password</h2>
                    <p class="text-gray-400 text-sm mb-6">Enter your email and we'll send you a link to reset your password</p>
                    
                    @if (session('status'))
                        <div class="bg-green-900/30 border border-green-800 text-green-400 px-4 py-3 rounded-lg mb-6 animate-pulse-slow">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    <form class="space-y-6" action="{{ route('password.email') }}" method="POST" id="reset-form">
                        @csrf
                        
                        <div class="form-group" data-animation-delay="0.2">
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email address</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="ri-mail-line text-gray-500 group-focus-within:text-primary transition-colors"></i>
                                </div>
                                <input id="email" name="email" type="email" autocomplete="email" required
                                    class="block w-full pl-12 pr-4 py-4 border bg-gray-800/50 border-gray-700 placeholder-gray-500 text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all glass-input"
                                    placeholder="Enter your email"
                                    value="{{ old('email') }}">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center opacity-0 group-focus-within:opacity-100 transition-opacity">
                                    <i class="ri-checkbox-circle-line text-primary"></i>
                                </div>
                            </div>
                            @error('email')
                                <p class="text-primary text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group" data-animation-delay="0.4">
                            <button type="submit" 
                                class="w-full flex justify-center items-center py-4 px-4 border border-transparent text-base font-medium rounded-xl text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition duration-300 btn-glow pulse-btn">
                                <i class="ri-mail-send-line mr-2"></i>
                                Send Reset Link
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-8 text-center">
                        <p class="text-sm text-gray-400">
                            <a href="{{ route('login') }}" class="font-medium text-primary hover:text-primary/80 transition flex items-center justify-center group">
                                <i class="ri-arrow-left-line mr-2 transform group-hover:-translate-x-1 transition-transform"></i> 
                                <span>Back to login</span>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-8 text-sm text-gray-500">
                <p>© 2025 PELIXS. All rights reserved.</p>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Enhanced Particle Effect
        const particleContainers = ['particles-left', 'particles-right'];
        
        particleContainers.forEach(containerId => {
            const container = document.getElementById(containerId);
            if (container) {
                for (let i = 0; i < 30; i++) {
                    const size = Math.random() * 4 + 1;
                    const particle = document.createElement('div');
                    particle.classList.add('particle');
                    particle.style.width = size + 'px';
                    particle.style.height = size + 'px';
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.top = Math.random() * 100 + '%';
                    particle.style.opacity = Math.random() * 0.5 + 0.1;
                    container.appendChild(particle);
                    
                    // Animate particles with GSAP
                    gsap.to(particle, {
                        x: Math.random() * 150 - 75,
                        y: Math.random() * 150 - 75,
                        duration: Math.random() * 25 + 15,
                        repeat: -1,
                        yoyo: true,
                        ease: "sine.inOut"
                    });
                }
            }
        });
        
        // Animate tagline
        gsap.to("#tagline", {
            opacity: 1,
            y: 0,
            duration: 1,
            delay: 0.6
        });
        
        // Animate form inputs with stagger
        gsap.from("#reset-form .form-group", {
            y: 30,
            opacity: 0,
            duration: 0.7,
            stagger: 0.2,
            ease: "power3.out",
            delay: 0.3
        });
        
        // Mouse follow effect for spotlight
        const formContainer = document.querySelector('.md\\:w-1/2.w-full');
        if (formContainer) {
            const spotlight = formContainer.querySelector('.spotlight');
            
            formContainer.addEventListener('mousemove', (e) => {
                const rect = formContainer.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                gsap.to(spotlight, {
                    x: x - 150,
                    y: y - 150,
                    duration: 1,
                    ease: "power2.out"
                });
            });
        }
        
        // Interactive button effect
        const submitButton = document.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.addEventListener('mouseenter', () => {
                gsap.to(submitButton, {
                    scale: 1.03,
                    duration: 0.3
                });
            });
            
            submitButton.addEventListener('mouseleave', () => {
                gsap.to(submitButton, {
                    scale: 1,
                    duration: 0.3
                });
            });
            
            submitButton.addEventListener('click', function(e) {
                if (!document.getElementById('email').checkValidity()) {
                    return;
                }
                
                // Prevent default to show animation before form submit
                e.preventDefault();
                
                // Add loading state
                this.innerHTML = '<i class="ri-loader-4-line animate-spin mr-2"></i> Sending...';
                this.disabled = true;
                
                // Create ripple effect
                const ripple = document.createElement('div');
                ripple.style.position = 'absolute';
                ripple.style.borderRadius = '50%';
                ripple.style.backgroundColor = 'rgba(255,255,255,0.3)';
                ripple.style.width = '10px';
                ripple.style.height = '10px';
                ripple.style.transform = 'scale(0)';
                ripple.style.pointerEvents = 'none';
                
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                
                this.appendChild(ripple);
                
                gsap.to(ripple, {
                    scale: 30,
                    opacity: 0,
                    duration: 0.8,
                    onComplete: () => {
                        ripple.remove();
                        // Submit the form after animation completes
                        document.getElementById('reset-form').submit();
                    }
                });
            });
        }
        
        // Floating animation for process cards
        gsap.to(".reset-process-item", {
            y: -10,
            duration: 2,
            repeat: -1,
            yoyo: true,
            stagger: 0.5,
            ease: "sine.inOut"
        });
    });
</script>
@endsection