<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.0/swiper-bundle.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.0/swiper-bundle.min.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <title>PELIXS - Subscription</title>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#e50914',
                        dark: '#141414',
                        darker: '#0b0b0b'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        .subscription-card {
            background: linear-gradient(135deg, #1f1f1f 0%, #0d0d0d 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
        }
        .feature-check {
            color: #e50914;
        }
        .payment-form input {
            background-color: #333;
            border: 1px solid #444;
            color: white;
            transition: all 0.3s ease;
        }
        .payment-form input:focus {
            border-color: #e50914;
            box-shadow: 0 0 0 2px rgba(229, 9, 20, 0.25);
        }
        .payment-form label {
            color: #ccc;
        }
        .hero-gradient {
            background: linear-gradient(to top, rgba(11, 11, 11, 1) 0%, rgba(11, 11, 11, 0) 100%);
        }
        .StripeElement {
            background-color: #333;
            border: 1px solid #444;
            border-radius: 0.375rem;
            padding: 12px;
            width: 100%;
            color: white;
            transition: all 0.3s ease;
        }
        .StripeElement--focus {
            border-color: #e50914;
            box-shadow: 0 0 0 2px rgba(229, 9, 20, 0.25);
        }
        .StripeElement--invalid {
            border-color: #e50914;
        }
        #payment-message {
            margin-top: 0.5rem;
            color: #f87171;
            font-size: 0.875rem;
        }
        #payment-message.success {
            color: #34d399;
        }
        input, select, textarea {
            color: white !important;
        }
        .StripeElement iframe {
            color: white !important;
        }
        .header-container {
            background: rgba(11, 11, 11, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        }
        .nav-link {
            position: relative;
            padding: 0.5rem 0;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #e50914;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }
        .nav-link.active {
            color: #e50914;
            font-weight: 600;
        }
        .logo-text {
            font-weight: 800;
            letter-spacing: 1px;
            text-shadow: 0 0 10px rgba(229, 9, 20, 0.5);
            background: linear-gradient(135deg, #ff0a18, #e50914);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .logout-button {
            background: linear-gradient(135deg, #ff0a18, #e50914);
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(229, 9, 20, 0.3);
        }
        .logout-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(229, 9, 20, 0.4);
        }
    </style>
</head>
<body class="bg-darker text-white font-sans">
    <!-- Header -->
    <header class="header-container py-4 fixed top-0 w-full z-50 transition-all duration-300">
        <div class="container mx-auto px-6">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <h1 class="logo-text text-3xl">PELIXS</h1>
                <!-- Navigation -->
                <nav class="hidden md:flex space-x-8">
                    <a href="/home" class="nav-link {{ request()->is('home') ? 'active' : '' }}">Home</a>
                    <a href="/browse" class="nav-link {{ request()->is('browse') ? 'active' : '' }}">Browse</a>
                    <a href="/movies" class="nav-link {{ request()->is('movies') ? 'active' : '' }}">Movies</a>
                    <a href="/shows" class="nav-link {{ request()->is('shows') ? 'active' : '' }}">TV Shows</a>
                    <a href="/anime" class="nav-link {{ request()->is('anime') ? 'active' : '' }}">Anime</a>
                    @auth
                        @can('access-community-chat')
                            <a href="{{ url('/community') }}" class="nav-link {{ request()->is('community') ? 'active' : '' }}">Community</a>
                            <a href="/mylist" class="nav-link {{ request()->is('mylist') ? 'active' : '' }}">My List</a>
                        @endcan
                        <a href="{{ url('/subscription') }}" class="nav-link {{ request()->is('subscription') ? 'active' : '' }}">Subscription</a>
                    @else
                        <a href="{{ url('/login') }}" class="nav-link">Community</a>
                    @endauth
                </nav>
                <!-- Auth -->
                <div class="flex items-center space-x-5">
                    <form action="{{ route('logout') }}" method="POST" class="inline-flex">
                        @csrf
                        <button type="submit" class="logout-button text-white px-5 py-2 rounded-full flex items-center">
                            <i class="ri-logout-box-r-line mr-2"></i> Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>



    <main class="pt-24 pb-16">
        <div class="container mx-auto px-4">
            <!-- Subscription Hero -->
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">PELIXS Premium</h1>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">Unlock unlimited access to our entire library of movies and TV shows for just $2 per month.</p>
            </div>
            
            <!-- Subscription Card -->
            <div class="max-w-4xl mx-auto">
                <div class="subscription-card rounded-2xl p-8 mb-10">
                    <div class="flex flex-col md:flex-row gap-8">
                        <!-- Plan Info -->
                        <div class="w-full md:w-1/2">
                            <div class="bg-gradient-to-r from-primary to-red-700 inline-block px-4 py-1 rounded-full text-sm font-semibold mb-4">
                                PREMIUM PLAN
                            </div>
                            <h2 class="text-3xl font-bold mb-2">$2<span class="text-xl text-gray-400">/month</span></h2>
                            <p class="text-gray-300 mb-6">Billed monthly, cancel anytime</p>
                            
                            <h3 class="text-xl font-semibold mb-4">Features included:</h3>
                            <ul class="space-y-3 mb-6">
                                <li class="flex items-center">
                                    <i class="ri-check-line mr-3 text-xl feature-check"></i>
                                    <span>Unlimited access to all movies and TV shows</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="ri-check-line mr-3 text-xl feature-check"></i>
                                    <span>Stream on any device</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="ri-check-line mr-3 text-xl feature-check"></i>
                                    <span>HD and 4K streaming</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="ri-check-line mr-3 text-xl feature-check"></i>
                                    <span>Download and watch offline</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="ri-check-line mr-3 text-xl feature-check"></i>
                                    <span>No ads or interruptions</span>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Custom Payment Form -->
                        <div class="w-full md:w-1/2">
                            <form id="payment-form" class="payment-form">
                                @csrf
                                <div class="mb-5 bg-gray-900 p-4 rounded-lg">
                                    <div class="text-sm text-gray-400 mb-2">Secure payment powered by</div>
                                    <div class="flex items-center">
                                        <i class="ri-lock-line mr-2 text-primary"></i>
                                        <span class="font-semibold">Stripe</span>
                                        <div class="ml-auto flex space-x-2">
                                            <i class="ri-visa-line text-xl"></i>
                                            <i class="ri-mastercard-line text-xl"></i>
                                            <i class="ri-bank-card-line text-xl"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <label for="card-holder-name" class="block mb-2 text-sm font-medium">Cardholder Name</label>
                                    <input type="text" id="card-holder-name" class="w-full rounded-lg p-3 bg-gray-800 text-white" placeholder="John Smith" required />
                                </div>
                                
                                <!-- Separate Card Number Field -->
                                <div class="mb-5">
                                    <label for="card-number-element" class="block mb-2 text-sm font-medium">Card Number</label>
                                    <div id="card-number-element" class="rounded-lg p-3 bg-gray-800"></div>
                                </div>
                                
                                <!-- Card Expiry and CVC in same row -->
                                <div class="flex space-x-4 mb-5">
                                    <div class="w-1/2">
                                        <label for="card-expiry-element" class="block mb-2 text-sm font-medium">Expiration Date</label>
                                        <div id="card-expiry-element" class="rounded-lg p-3 bg-gray-800"></div>
                                    </div>
                                    <div class="w-1/2">
                                        <label for="card-cvc-element" class="block mb-2 text-sm font-medium">CVC</label>
                                        <div id="card-cvc-element" class="rounded-lg p-3 bg-gray-800"></div>
                                    </div>
                                </div>
                                
                                <div id="payment-message" class="hidden mb-4 text-red-500"></div>

                                <div id="loading-spinner" class="hidden mb-4">
                                    <div class="flex items-center justify-center py-2">
                                        <svg class="animate-spin h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="ml-2">Processing payment...</span>
                                    </div>
                                </div>
                                
                                <button id="submit-button" type="submit" class="w-full bg-primary hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                                    <i class="ri-secure-payment-line mr-2"></i>
                                    Subscribe Now
                                </button>
                                
                                <div class="text-center mt-4 text-gray-400 text-sm">
                                    <p>Your payment is secured with industry-standard encryption</p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonials -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <div class="bg-dark p-6 rounded-xl">
                        <div class="flex items-center mb-4">
                            <div class="text-primary">
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                            </div>
                        </div>
                        <p class="text-gray-300 mb-4">"The best value streaming service out there. So many great movies for just $2!"</p>
                        <div class="font-semibold">Jane D.</div>
                    </div>
                    <div class="bg-dark p-6 rounded-xl">
                        <div class="flex items-center mb-4">
                            <div class="text-primary">
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                            </div>
                        </div>
                        <p class="text-gray-300 mb-4">"I love being able to watch all the latest releases and classics. Great interface too!"</p>
                        <div class="font-semibold">Michael T.</div>
                    </div>
                    <div class="bg-dark p-6 rounded-xl">
                        <div class="flex items-center mb-4">
                            <div class="text-primary">
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                            </div>
                        </div>
                        <p class="text-gray-300 mb-4">"Finally a streaming service that's actually affordable and has a great selection!"</p>
                        <div class="font-semibold">Sarah K.</div>
                    </div>
                </div>
                
                <!-- FAQ Section -->
                <div class="mb-10">
                    <h2 class="text-2xl font-bold mb-6 text-center">Frequently Asked Questions</h2>
                    <div class="space-y-4">
                        <div class="bg-dark p-4 rounded-lg">
                            <div class="flex justify-between items-center cursor-pointer faq-toggle">
                                <h3 class="font-semibold">How does billing work?</h3>
                                <i class="ri-arrow-down-s-line"></i>
                            </div>
                            <div class="faq-content hidden mt-2 text-gray-300">
                                <p>You'll be charged $2 monthly. Your subscription will automatically renew each month until you cancel. You can cancel anytime through your account settings.</p>
                            </div>
                        </div>
                        <div class="bg-dark p-4 rounded-lg">
                            <div class="flex justify-between items-center cursor-pointer faq-toggle">
                                <h3 class="font-semibold">Can I watch on multiple devices?</h3>
                                <i class="ri-arrow-down-s-line"></i>
                            </div>
                            <div class="faq-content hidden mt-2 text-gray-300">
                                <p>Yes! You can watch on your computer, smartphone, tablet, or smart TV. You can use up to 3 devices simultaneously with one account.</p>
                            </div>
                        </div>
                        <div class="bg-dark p-4 rounded-lg">
                            <div class="flex justify-between items-center cursor-pointer faq-toggle">
                                <h3 class="font-semibold">How do I cancel my subscription?</h3>
                                <i class="ri-arrow-down-s-line"></i>
                            </div>
                            <div class="faq-content hidden mt-2 text-gray-300">
                                <p>You can cancel anytime by going to your account settings and clicking on "Manage Subscription". Your benefits will continue until the end of your billing period.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="bg-dark py-8 border-t border-gray-800">
        <div class="container mx-auto px-4">
            <div class="text-center text-gray-400 text-sm">
                <p>© 2025 PELIXS. All rights reserved. Powered by TMDB API.</p>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('profile-toggle').addEventListener('click', function() {
            document.getElementById('profile-dropdown').classList.toggle('hidden');
        });
        
        document.addEventListener('click', function(event) {
            if (!event.target.closest('#profile-toggle') && !event.target.closest('#profile-dropdown')) {
                document.getElementById('profile-dropdown').classList.add('hidden');
            }
        });
        
        document.querySelectorAll('.faq-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const content = this.nextElementSibling;
                content.classList.toggle('hidden');
                
                const icon = this.querySelector('i');
                if (content.classList.contains('hidden')) {
                    icon.classList.replace('ri-arrow-up-s-line', 'ri-arrow-down-s-line');
                } else {
                    icon.classList.replace('ri-arrow-down-s-line', 'ri-arrow-up-s-line');
                }
            });
        });
    
        document.addEventListener('DOMContentLoaded', function() {
            const stripe = Stripe('pk_test_51R9o0LIOTpVa0jApQMTWFDOU7L4m18guEouxfCP3P1rQUUMJToIgYuC4totrULzeHMeCqDE7t4nTmGVqzwOjVMZn00SHwsW2s2');
            
            const elements = stripe.elements({
                appearance: {
                    theme: 'night',
                    variables: {
                        colorPrimary: '#e50914',
                        colorBackground: '#333',
                        colorText: '#ffffff',
                        colorDanger: '#e50914',
                        fontFamily: 'system-ui, sans-serif',
                        spacingUnit: '4px',
                        borderRadius: '4px',
                    },
                },
            });
            
            const cardNumberElement = elements.create('cardNumber', {
                placeholder: '1234 1234 1234 1234',
                style: {
                    base: {
                        color: '#ffffff',
                        fontWeight: '500',
                        fontSize: '16px',
                        fontSmoothing: 'antialiased',
                    }
                }
            });
            
            const cardExpiryElement = elements.create('cardExpiry', {
                style: {
                    base: {
                        color: '#ffffff',
                        fontWeight: '500',
                        fontSize: '16px',
                        fontSmoothing: 'antialiased',
                    }
                }
            });
            
            const cardCvcElement = elements.create('cardCvc', {
                style: {
                    base: {
                        color: '#ffffff',
                        fontWeight: '500',
                        fontSize: '16px',
                        fontSmoothing: 'antialiased',
                    }
                }
            });
            
            cardNumberElement.mount('#card-number-element');
            cardExpiryElement.mount('#card-expiry-element');
            cardCvcElement.mount('#card-cvc-element');
            
            const form = document.getElementById('payment-form');
            const submitButton = document.getElementById('submit-button');
            const loadingSpinner = document.getElementById('loading-spinner');
            const paymentMessage = document.getElementById('payment-message');
            
            form.addEventListener('submit', async function(event) {
                event.preventDefault();
                
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50');
                loadingSpinner.classList.remove('hidden');
                
                const cardHolderName = document.getElementById('card-holder-name').value;
                
                try {
                    const { paymentMethod, error } = await stripe.createPaymentMethod({
                        type: 'card',
                        card: cardNumberElement,
                        billing_details: { name: cardHolderName },
                    });
                    
                    if (error) {
                        handleError(error);
                        return;
                    }

                    const response = await fetch('/subscription/create', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            payment_method_id: paymentMethod.id,
                            plan: 'premium'
                        })
                    });

                    const result = await response.json();
                    
                    if (result.requires_action) {
                        const { error: confirmError } = await stripe.confirmCardPayment(result.client_secret);
                        if (confirmError) {
                            handleError(confirmError);
                        } else {
                            const finalResponse = await fetch('/subscription/create', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    payment_method_id: paymentMethod.id,
                                    plan: 'premium'
                                })
                            });
                            const finalResult = await finalResponse.json();
                            if (finalResult.success) {
                                window.location.href = '/subscription/success';
                            } else {
                                handleError({ message: finalResult.message || 'Subscription failed after authentication.' });
                            }
                        }
                    } else if (result.success) {
                        window.location.href = '/subscription/success';
                    } else {
                        handleError({ message: result.message || 'An unexpected error occurred.' });
                    }
                } catch (serverError) {
                    handleError({ message: 'Error connecting to the server. Please try again.' });
                }
            });
                
            function handleError(error) {
                loadingSpinner.classList.add('hidden');
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-50');
                
                paymentMessage.textContent = error.message;
                paymentMessage.classList.remove('hidden');
                paymentMessage.classList.remove('success');
                
                setTimeout(() => {
                    paymentMessage.classList.add('hidden');
                }, 5000);
            }
        });
    </script>
</body>
</html>