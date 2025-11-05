<div class="bg-gray-50 min-h-screen">
    <!-- Static Header -->
    <div class="relative bg-white">
        <div class="relative rounded-2xl mt-12 h-96 md:h-[400px] bg-gradient-to-r from-blue-300 to-blue-400 overflow-hidden">
           
            <div class="container mx-auto px-6 py-12 h-full flex flex-col justify-center relative z-10">
                <div class="text-center text-white">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">Welcome to {{ config('app.name') }}</h1>
                    <p class="text-xl md:text-2xl max-w-2xl mx-auto">Laboratory and Clinical Scientists Council of Zimbabwe</p>
                    <div class="mt-8">
                        <x-button link="{{ route('register') }}" class="bg-white hover:bg-gray-100 text-blue-800 font-semibold px-6 py-3 rounded-lg transition duration-300 inline-flex items-center">
                            Start Registration
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Registration Steps Section -->
    <section id="registration-steps" class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Registration Process</h2>
                <p class="text-gray-600 mt-4 max-w-2xl mx-auto">Complete these steps to register as a certified medical laboratory professional</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-8">
                <!-- Step 1: Account Creation -->
                <div class="bg-blue-50 rounded-xl p-6 text-center transform transition duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="bg-blue-600 text-white w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-xl font-bold">1</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Account Creation</h3>
                    <p class="text-gray-600">Register and create your personal account</p>
                    <a href="{{ route('register') }}" class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-medium">Start Here →</a>
                </div>
                
                <!-- Step 2: Profession Selection -->
                <div class="bg-blue-50 rounded-xl p-6 text-center transform transition duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="bg-blue-600 text-white w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-xl font-bold">2</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Profession Selection</h3>
                    <p class="text-gray-600">Choose your specific laboratory profession</p>
                </div>
                
                <!-- Step 3: Document Upload -->
                <div class="bg-blue-50 rounded-xl p-6 text-center transform transition duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="bg-blue-600 text-white w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-xl font-bold">3</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Document Upload</h3>
                    <p class="text-gray-600">Submit required certificates and identification</p>
                </div>
                
                <!-- Step 4: Qualifications Review -->
                <div class="bg-blue-50 rounded-xl p-6 text-center transform transition duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="bg-blue-600 text-white w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-xl font-bold">4</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2"> Add Qualifications</h3>
                    <p class="text-gray-600">Add your profession related qualifications</p>
                </div>
                
                <!-- Step 5: Payment -->
                <div class="bg-blue-50 rounded-xl p-6 text-center transform transition duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="bg-blue-600 text-white w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-xl font-bold">5</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Payment</h3>
                    <p class="text-gray-600">Complete registration fee payment</p>
                </div>
            </div>
        </div>
    </section>
    
  
    
    <!-- Certificate Verification Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Certificate Verification</h2>
                <p class="text-gray-600 mt-4 max-w-2xl mx-auto">Verify the authenticity of a medical laboratory professional's certification</p>
            </div>
            
            <div class="max-w-3xl mx-auto">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="p-8">
                        <form class="space-y-6">
                            <div>
                                <label for="certificate-number" class="block text-sm font-medium text-gray-700 mb-1">Certificate Number</label>
                                <input type="text" id="certificate-number" name="certificate-number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Enter certificate number">
                            </div>
                    
                            <div class="flex justify-center">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition duration-300 inline-flex items-center">
                                    Verify Certificate
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
              
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="bg-blue-400 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-semibold mb-4">Medical Laboratory Council</h3>
                    <p class="text-blue-200">Ensuring quality and standards in medical laboratory practice</p>
                </div>
                <div>
                    <h3 class="text-xl font-semibold mb-4">Contact Us</h3>
                    <ul class="space-y-2 text-blue-200">
                        <li>Email: contact@medlabcouncil.org</li>
                        <li>Phone: +1 (123) 456-7890</li>
                        <li>Address: 123 Medical Plaza, Healthcare City</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-blue-200 hover:text-white transition duration-300">About Us</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white transition duration-300">FAQs</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white transition duration-300">Privacy Policy</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white transition duration-300">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-blue-800 mt-8 pt-8 text-center text-blue-200">
                <p>&copy; {{ date('Y') }} Medical Laboratory Council. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- Scripts for Swiper.js -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
</div>
