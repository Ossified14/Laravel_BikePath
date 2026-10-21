@extends('layouts.app')

@section('title', 'Daftar Akun')

@section('content')
    <div class="auth-card">
        <h2 class="auth-title">🚴 Daftar BikeProfile</h2>

        <form action="{{ route('register') }}" method="POST" class="auth-form">
            @csrf

            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    class="input-field @error('name') input-error @enderror"
                    placeholder="Nama kamu"
                    autofocus
                >
                @error('name')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="input-field @error('email') input-error @enderror"
                    placeholder="contoh@email.com"
                >
                @error('email')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="age">Umur</label>
                <input
                    type="number"
                    id="age"
                    name="age"
                    value="{{ old('age') }}"
                    class="input-field @error('age') input-error @enderror"
                    placeholder="21"
                    style="width: 100px"
                >
                @error('age')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="bike_type">Jenis Sepeda</label>
                <select
                    id="bike_type"
                    name="bike_type"
                    class="select-input @error('bike_type') input-error @enderror"
                >
                    <option value="" disabled {{ old('bike_type') ? '' : 'selected' }}>-- Pilih --</option>
                    @foreach(['Road', 'Gravel', 'Mountain'] as $type)
                        <option value="{{ $type }}" {{ old('bike_type') === $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
                @error('bike_type')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="input-field @error('password') input-error @enderror"
                    placeholder="Minimal 6 karakter"
                >
                @error('password')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="input-field"
                    placeholder="Ulangi password"
                >
            </div>

            <button type="submit" class="btn-submit btn-full">Daftar Sekarang</button>
        </form>

        <p class="auth-footer">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="auth-link">Login di sini</a>
        </p>
    </div>
@endsection