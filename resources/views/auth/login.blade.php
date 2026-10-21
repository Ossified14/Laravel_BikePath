@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="auth-card">
        <h2 class="auth-title">🚴 Login BikeProfile</h2>

        <form action="{{ route('login') }}" method="POST" class="auth-form">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="input-field @error('email') input-error @enderror"
                    placeholder="contoh@email.com"
                    autofocus
                >
                @error('email')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="input-field"
                    placeholder="••••••••"
                >
                @error('password')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group form-check">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember" class="check-label">Ingat saya</label>
            </div>

            <button type="submit" class="btn-submit btn-full">Login</button>
        </form>

        <p class="auth-footer">
            Belum punya akun?
            <a href="{{ route('register') }}" class="auth-link">Daftar di sini</a>
        </p>
    </div>
@endsection