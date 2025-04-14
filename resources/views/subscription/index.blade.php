<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Remix Icon CDN -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Stripe JS -->
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
                    }
                }
            }
        }
    </script>
    <style>
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
        /* Stripe Elements custom styling */
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
    </style>
</head>
<body class="bg-darker text-white font-sans">
    <!-- Header -->
    <header class="bg-dark py-4 px-6 shadow-lg fixed top-0 w-full z-50">
        <div class="container mx-auto flex justify-between items-center">
            <!-- Logo -->
            <h1 class="text-3xl font-bold text-primary tracking-wider">PELIXS</h1>

            <!-- Navigation -->
            <nav class="hidden md:flex space-x-6">
                <a href="/home" class="hover:text-primary transition">Home</a>
                <a href="/browse" class="hover:text-primary transition">Browse</a>
                <a href="/movies" class="hover:text-primary transition">Movies</a>
                <a href="/shows" class="hover:text-primary transition">TV Shows</a>
                <a href="/anime" class="hover:text-primary transition">Anime</a>
                <a href="/mylist" class="hover:text-primary transition">My List</a>
                <a href="/community" class="hover:text-primary transition">Community</a>
            </nav>

            <!-- Profile & Notifications -->
            <div class="flex items-center space-x-4">
                <button class="text-xl p-2 rounded-full hover:bg-gray-800 transition">
                    <i class="ri-notification-3-line"></i>
                </button>

                <!-- Profile Dropdown -->
                <div class="relative">
                    <button id="profile-toggle" class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                        <i class="ri-user-line text-white"></i>
                    </button>
                    <div id="profile-dropdown" class="profile-dropdown absolute right-0 top-full mt-2 w-48 bg-dark border border-gray-700 rounded-lg shadow-lg hidden">
                        <ul class="py-1">
                            <li>
                                <a href="/profile" class="block px-4 py-2 hover:bg-gray-800 transition flex items-center">
                                    <i class="ri-user-line mr-2"></i> Profile
                                </a>
                            </li>
                            <li>
                                <a href="/settings" class="block px-4 py-2 hover:bg-gray-800 transition flex items-center">
                                    <i class="ri-settings-3-line mr-2"></i> Settings
                                </a>
                            </li>
                            <li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                                   class="block px-4 py-2 hover:bg-gray-800 transition text-red-500 hover:text-red-400 flex items-center">
                                    <i class="ri-logout-box-r-line mr-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
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
                                <div class="mb-4 bg-gray-900 p-4 rounded-lg">
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

                                <div class="mb-4">
                                    <label for="card-holder-name" class="block mb-2 text-sm font-medium">Cardholder Name</label>
                                    <input type="text" id="card-holder-name" class="w-full rounded-lg p-3" placeholder="John Smith" required />
                                </div>
                                
                                <div class="mb-4">
                                    <label for="card-element" class="block mb-2 text-sm font-medium">Card Information</label>
                                    <div id="card-element" class="rounded-lg"></div>
                                    <div id="payment-message" class="hidden"></div>
                                </div>

                                <div id="loading-spinner" class="hidden">
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
        // Toggle profile dropdown
        document.getElementById('profile-toggle').addEventListener('click', function() {
            document.getElementById('profile-dropdown').classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('#profile-toggle') && !event.target.closest('#profile-dropdown')) {
                document.getElementById('profile-dropdown').classList.add('hidden');
            }
        });
        
        // FAQ toggles
        document.querySelectorAll('.faq-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const content = this.nextElementSibling;
                content.classList.toggle('hidden');
                
                // Change the icon
                const icon = this.querySelector('i');
                if (content.classList.contains('hidden')) {
                    icon.classList.replace('ri-arrow-up-s-line', 'ri-arrow-down-s-line');
                } else {
                    icon.classList.replace('ri-arrow-down-s-line', 'ri-arrow-up-s-line');
                }
            });
        });
    
        // Stripe Integration
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Stripe - Replace with your publishable key
            const stripe = Stripe('pk_test_51R9o0LIOTpVa0jApQMTWFDOU7L4m18guEouxfCP3P1rQUUMJToIgYuC4totrULzeHMeCqDE7t4nTmGVqzwOjVMZn00SHwsW2s2');
            
            // Create an instance of Elements
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
            
            // Create an instance of the card Element
            const cardElement = elements.create('card');
            
            // Add an instance of the card Element into the `card-element` div
            cardElement.mount('#card-element');
            
            // Handle form submission
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
            card: cardElement,
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
                // After 3D Secure confirmation, check status again
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