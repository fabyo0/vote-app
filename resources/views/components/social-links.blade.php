<!-- Social Buttons -->
<div class="mt-6">
   <p class="text-center text-gray-500 text-sm mb-3">Or sign in with</p>
   <div class="space-y-2">
      <!-- Google -->
      <a href="{{ route('socialite.auth', 'google') }}"
         class="flex items-center gap-2 justify-center w-full py-2 px-4 border border-gray-300 rounded-md text-sm text-gray-800 bg-white hover:bg-gray-100 transition">
         <svg class="w-4 h-4" viewBox="0 0 488 512" fill="currentColor">
            <path fill="#EA4335"
               d="M488 261.8c0-17.8-1.6-35-4.7-51.6H249v97.7h135.5c-5.9 32-23.5 59.2-50 77.3v63h80.8c47.2-43.4 73.7-107.4 73.7-186.4z" />
            <path fill="#34A853"
               d="M249 492c67.2 0 123.6-22.3 164.8-60.4l-80.8-63c-22.4 15-51 23.8-84 23.8-64.5 0-119-43.5-138.4-102.2H28.2v64.2C69.4 438.7 151.6 492 249 492z" />
            <path fill="#4A90E2"
               d="M110.6 289.2c-4.6-13.8-7.3-28.5-7.3-43.6s2.7-29.8 7.3-43.6v-64.2H28.2C10.1 167.3 0 206.6 0 245.6s10.1 78.3 28.2 108.2l82.4-64.6z" />
            <path fill="#FBBC05"
               d="M249 97.6c35.6 0 67.5 12.3 92.7 36.3l69.4-69.4C372.6 27.8 314.4 0 249 0 151.6 0 69.4 53.3 28.2 137.4l82.4 64.2C130 141.1 184.5 97.6 249 97.6z" />
         </svg>
         Google
      </a>
      <!-- Facebook -->
      <a href="{{ route('socialite.auth', 'facebook') }}"
         class="flex items-center gap-2 justify-center w-full py-2 px-4 border border-gray-300 rounded-md text-sm text-gray-800 bg-white hover:bg-gray-100 transition">
         <svg class="w-4 h-4" fill="#1877F2" viewBox="0 0 320 512">
            <path
               d="M279.14 288l14.22-92.66h-88.91V132.1c0-25.35 12.42-50.06 52.24-50.06H293V6.26S259.5 0 225.36 0C141.09 0 89.09 54.42 89.09 154.09V195.3H0v92.66h89.09V512h107.81V288z" />
         </svg>
         Facebook
      </a>
   </div>
</div>
