@php
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
@endphp


@extends('layouts/layoutMaster')

@section('title', 'Login')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection

@section('page-style')
@vite([
  'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection

@section('vendor-script')
{{-- Tidak perlu validation untuk desain custom --}}
@endsection

@section('page-script')
{{-- Custom script untuk toggle password --}}
@endsection

@section('content')
<style>
  .modern-auth-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow: hidden;
    padding: 20px;
  }

  /* Animated Background Circles */
  .modern-auth-wrapper::before,
  .modern-auth-wrapper::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    animation: float 20s infinite ease-in-out;
  }

  .modern-auth-wrapper::before {
    width: 500px;
    height: 500px;
    top: -250px;
    right: -250px;
    animation-delay: 0s;
  }

  .modern-auth-wrapper::after {
    width: 400px;
    height: 400px;
    bottom: -200px;
    left: -200px;
    animation-delay: -10s;
  }

  @keyframes float {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    25% { transform: translate(100px, 100px) rotate(90deg); }
    50% { transform: translate(0, 200px) rotate(180deg); }
    75% { transform: translate(-100px, 100px) rotate(270deg); }
  }

  /* Glass Card */
  .glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    padding: 50px 40px;
    max-width: 480px;
    width: 100%;
    position: relative;
    z-index: 100;
    animation: slideUp 0.6s ease-out;
    pointer-events: auto;
  }

  @keyframes slideUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* Logo Container */
  .logo-container {
    text-align: center;
    margin-bottom: 40px;
  }

  .logo-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
    transition: transform 0.3s ease;
  }

  .logo-wrapper:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 25px rgba(102, 126, 234, 0.5);
  }

  .logo-wrapper img {
    height: 80px;
    width: auto;
    object-fit: contain;
    border-radius: 8px;
  }

  /* Welcome Text */
  .welcome-title {
    font-size: 32px;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 12px;
    text-align: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .welcome-subtitle {
    color: #718096;
    text-align: center;
    margin-bottom: 40px;
    font-size: 15px;
  }

  /* Modern Form Inputs */
  .modern-input-group {
    margin-bottom: 24px;
    position: relative;
  }

  .modern-input-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 8px;
  }

  .modern-input {
    width: 100%;
    padding: 14px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 15px;
    transition: all 0.3s ease;
    background: #f7fafc;
  }

  .modern-input:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
  }

  .password-toggle {
    position: absolute;
    right: 16px;
    top: 42px;
    cursor: pointer;
    color: #a0aec0;
    transition: color 0.2s;
  }

  .password-toggle:hover {
    color: #667eea;
  }

  /* Modern Button */
  .modern-btn {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 12px;
    color: white;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    margin-top: 8px;
    position: relative;
    z-index: 10;
    pointer-events: auto;
  }

  .modern-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
  }

  .modern-btn:active {
    transform: translateY(0);
  }

  /* Divider */
  .modern-divider {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 30px 0;
    color: #a0aec0;
    font-size: 13px;
  }

  .modern-divider::before,
  .modern-divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #e2e8f0;
  }

  .modern-divider span {
    padding: 0 16px;
  }

  /* Social Buttons */
  .social-buttons {
    display: flex;
    gap: 12px;
    justify-content: center;
    margin-top: 24px;
  }

  .social-btn {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #e2e8f0;
    background: white;
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: 20px;
  }

  .social-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .social-btn.facebook { color: #1877f2; }
  .social-btn.facebook:hover { background: #1877f2; color: white; border-color: #1877f2; }

  .social-btn.twitter { color: #1da1f2; }
  .social-btn.twitter:hover { background: #1da1f2; color: white; border-color: #1da1f2; }

  .social-btn.github { color: #333; }
  .social-btn.github:hover { background: #333; color: white; border-color: #333; }

  .social-btn.google { color: #ea4335; }
  .social-btn.google:hover { background: #ea4335; color: white; border-color: #ea4335; }

  /* Register Link */
  .register-link {
    text-align: center;
    margin-top: 24px;
    color: #718096;
    font-size: 14px;
  }

  .register-link a {
    color: #667eea;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s;
  }

  .register-link a:hover {
    color: #764ba2;
    text-decoration: underline;
  }

  /* Responsive */
  @media (max-width: 576px) {
    .glass-card {
      padding: 35px 25px;
    }
    .welcome-title {
      font-size: 26px;
    }
  }
</style>

<div class="modern-auth-wrapper">
  <div class="glass-card">
    <!-- Logo -->
    <div class="logo-container">
      <div class="logo-wrapper">
        <img src="{{ asset('jmk.jpeg') }}" alt="JMK Logo">
      </div>
    </div>

    <!-- Welcome Text -->
    <h1 class="welcome-title">Welcome Back! 👋</h1>
    <p class="welcome-subtitle">Please sign in to your account and start the adventure</p>

    <!-- Login Form -->
    <form id="formAuthentication" action="{{ route('login.create') }}" method="POST">
      @csrf

      <!-- Email Input -->
      <div class="modern-input-group">
        <label class="modern-input-label" for="email">
          <i class="ri-mail-line"></i> Email Address
        </label>
        <input 
          type="email" 
          class="modern-input" 
          id="email" 
          name="email" 
          placeholder="Enter your email"
          required 
          autofocus
        >
      </div>

      <!-- Password Input -->
      <div class="modern-input-group">
        <label class="modern-input-label" for="password">
          <i class="ri-lock-line"></i> Password
        </label>
        <input 
          type="password" 
          class="modern-input" 
          id="password" 
          name="password" 
          placeholder="Enter your password"
          required
        >
        <i class="ri-eye-off-line password-toggle" id="togglePassword"></i>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="modern-btn">
        Sign In <i class="ri-arrow-right-line ms-2"></i>
      </button>
    </form>

    <!-- Register Link -->
    <div class="register-link">
      New on our platform? 
      <a href="{{ url('dashboard/auth/register') }}">Create an account</a>
    </div>

    <!-- Divider -->
    <div class="modern-divider">
      <span>or continue with</span>
    </div>

    <!-- Social Login -->
    <div class="social-buttons">
      <a href="javascript:;" class="social-btn facebook">
        <i class="ri-facebook-fill"></i>
      </a>
      <a href="javascript:;" class="social-btn twitter">
        <i class="ri-twitter-fill"></i>
      </a>
      <a href="javascript:;" class="social-btn github">
        <i class="ri-github-fill"></i>
      </a>
      <a href="javascript:;" class="social-btn google">
        <i class="ri-google-fill"></i>
      </a>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Toggle Password Visibility
    const togglePassword = document.getElementById('togglePassword');
    if (togglePassword) {
      togglePassword.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const password = document.getElementById('password');
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.classList.toggle('ri-eye-off-line');
        this.classList.toggle('ri-eye-line');
      });
    }

    // Ensure form can submit
    const form = document.getElementById('formAuthentication');
    if (form) {
      console.log('Form found and ready to submit');
      
      // Test button click
      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
          console.log('Button clicked');
        });
      }
    }
  });
</script>
@endsection
