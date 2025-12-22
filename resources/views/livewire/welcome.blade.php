<div class="min-h-screen">
    <!-- Static Header -->
    <div class="relative bg-white">
        <div class="relative w-full  h-96 md:h-[400px] bg-gradient-to-r {{ config('app.color') }}  overflow-hidden" >
           
            <div class="w-full px-6 py-12 h-full flex flex-col justify-center relative">
                <div class="text-center text-white">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">Welcome to {{ config('app.name') }}</h1>
                    <p class="text-xl md:text-2xl max-w-2xl mx-auto">{{ config('app.title') }}</p>
                    <div class="mt-8">
                        <x-button link="{{ route('register') }}" class="bg-white hover:bg-gray-100 text-blue-800 font-semibold px-6 py-3 rounded-lg transition duration-300 inline-flex items-center">
                            Get Started
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Call to Action: Find Practitioners & Institutions -->
        <section id="find-assistance" class="py-16 bg-gradient-to-br from-blue-50 to-blue-50">
            <div class="container mx-auto px-6">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Need Professional Assistance?</h2>
                    <p class="text-gray-600 text-lg max-w-3xl mx-auto">
                        Your health and well-being are our priority. Get help from registered practitioners and accredited institutions.
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                    <!-- Find Registered Practitioners Card -->
                    <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border-2 border-green-100">
                        <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-6 mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 text-center mb-4">Find Registered Practitioners</h3>
                        <p class="text-gray-600 text-center mb-6">
                            Search our comprehensive directory of certified practitioners. Filter by profession, city, and more to find the right practitioner for your needs.
                        </p>
                        <div class="text-center">
                            <a href="/compliancereport" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition duration-300 shadow-md hover:shadow-lg">
                                Browse Practitioners
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Find Accredited Institutions Card -->
                    <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border-2 border-blue-100">
                        <div class="flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-6 mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 text-center mb-4">Find Accredited Institutions</h3>
                        <p class="text-gray-600 text-center mb-6">
                            Discover accredited health institutions that offer assistance by registered practitioners.
                        </p>
                        <div class="text-center">
                            <a href="{{ route('registeredinstitutions.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-300 shadow-md hover:shadow-lg">
                                Browse Institutions
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                
            
            </div>
        </section>
    
    <!-- Registration Steps Section -->
    <section id="registration-steps" class="py-16 bg-white ">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Registration Process</h2>
                <p class="text-gray-600 mt-4 max-w-2xl mx-auto">Complete these steps to register as a certified practitioner</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-8">
                <!-- Step 1: Account Creation -->
                <div class="bg-blue-50 rounded-xl p-6 text-center transform transition duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="bg-green-600 text-white w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-xl font-bold">1</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Account Creation</h3>
                    <p class="text-gray-600">Register and create your personal account</p>
                    <a href="{{ route('register') }}" class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-medium">Start Here →</a>
                </div>
                
                <!-- Step 2: Profession Selection -->
                <div class="bg-blue-50 rounded-xl p-6 text-center transform transition duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="bg-green-600 text-white w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-xl font-bold">2</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Profession Selection</h3>
                    <p class="text-gray-600">Choose your specific laboratory profession</p>
                </div>
                
                <!-- Step 3: Document Upload -->
                <div class="bg-blue-50 rounded-xl p-6 text-center transform transition duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="bg-green-600 text-white w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-xl font-bold">3</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Document Upload</h3>
                    <p class="text-gray-600">Submit required certificates and identification</p>
                </div>
                
                <!-- Step 4: Qualifications Review -->
                <div class="bg-blue-50 rounded-xl p-6 text-center transform transition duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="bg-green-600 text-white w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-xl font-bold">4</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2"> Add Qualifications</h3>
                    <p class="text-gray-600">Add your profession related qualifications</p>
                </div>
                
                <!-- Step 5: Payment -->
                <div class="bg-blue-50 rounded-xl p-6 text-center transform transition duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="bg-green-600 text-white w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-xl font-bold">5</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Payment</h3>
                    <p class="text-gray-600">Complete registration fee payment</p>
                </div>
            </div>
        </div>
    </section>
    

    
    <!-- Banking Details Section -->
   
    <livewire:bankdetails />
  
    
<livewire:components.verifycertificate />
    
    <!-- Footer -->
    <footer class="text-white py-12 {{ config('app.color') }}" >
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-semibold mb-4">{{ config('app.title') }}</h3>
                    <p class="text-white">{{ config('app.vision') }}</p>
                </div>
                <div>
                    <h3 class="text-xl font-semibold mb-4">Contact Us</h3>
                    <ul class="space-y-2 text-white">
                        <li>{{ config('app.email') }}
                        </li>
                        <li>Phone: {{ config('app.phone') }} </li>
                        <li>Address: {{ config('app.address') }}</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="hregistration@mlcscz.co.zw" class="text-white hover:text-green-200 transition duration-300">About Us</a></li>
                        <li><a href="https://www.mohcc.gov.zw/" class="text-white hover:text-green-200 transition duration-300">MOHCC</a></li>
                        <li><a href="https://hpa.co.zw/" class="text-white hover:text-green-200 transition duration-300">HPA</a></li>
                       
                    </ul>
                </div>
            </div>
            <div class="border-t border-green-200 mt-8 pt-8 text-center">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- Scripts for Swiper.js -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
</div>
