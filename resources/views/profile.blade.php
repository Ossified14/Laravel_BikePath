@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Profile</h2>
            <button id="btn-edit" class="btn-edit" onclick="toggleEdit(true)">Edit</button>
            <button id="btn-batal" class="btn-batal" onclick="toggleEdit(false)" style="display:none">Batal</button>
        </div>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        {{-- ── MODE VIEW ── --}}
        <table class="profile-table" id="mode-view">
            <tr>
                <th>Nama</th>
                <td>{{ $nama }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ Auth::user()->email }}</td>
            </tr>
            <tr>
                <th>Umur</th>
                <td>
                    @if($umur)
                        @if($umur >= 17)
                            {{ $umur }} tahun
                        @else
                            {{ $umur }} tahun (di bawah umur)
                        @endif
                    @else
                        <span class="text-muted">Belum diisi</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Jenis Sepeda</th>
                <td>
                    @if($pilihan)
                        <span class="badge">{{ $pilihan }}</span>
                    @else
                        <span class="text-muted">Belum dipilih</span>
                    @endif
                </td>
            </tr>
        </table>

        {{-- ── MODE EDIT ── --}}
        <form action="{{ route('profile.store') }}" method="POST" id="mode-edit" style="display:none">
            @csrf
            <table class="profile-table">
                <tr>
                    <th>Nama</th>
                    <td>
                        <input type="text" name="nama" value="{{ old('nama', $nama) }}" class="input-field">
                        @error('nama') <span class="error-msg">{{ $message }}</span> @enderror
                    </td>
                </tr>
                <tr>
                    <th>Umur</th>
                    <td>
                        <input type="number" name="umur" value="{{ old('umur', $umur) }}" class="input-field" style="width:80px">
                        @error('umur') <span class="error-msg">{{ $message }}</span> @enderror
                    </td>
                </tr>
                <tr>
                    <th>Jenis Sepeda</th>
                    <td>
                        <select name="jenis_sepeda" class="select-input">
                            <option value="" disabled {{ $pilihan === null ? 'selected' : '' }}>-- Pilih --</option>
                            @foreach($jenis_sepeda as $sepeda)
                                <option value="{{ $sepeda }}" {{ (old('jenis_sepeda', $pilihan) === $sepeda) ? 'selected' : '' }}>
                                    {{ $sepeda }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenis_sepeda') <span class="error-msg">{{ $message }}</span> @enderror
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <button type="submit" class="btn-submit">Simpan</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <script>
        function toggleEdit(isEdit) {
            document.getElementById('mode-view').style.display  = isEdit ? 'none'  : 'table';
            document.getElementById('mode-edit').style.display  = isEdit ? 'block' : 'none';
            document.getElementById('btn-edit').style.display   = isEdit ? 'none'  : 'inline-block';
            document.getElementById('btn-batal').style.display  = isEdit ? 'inline-block' : 'none';
        }

        @if($errors->any())
            toggleEdit(true);
        @endif
    </script>
@endsection