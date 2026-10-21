@extends('layouts.app')

@section('title', 'Daftar User')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Daftar User</h2>
            <span class="badge">{{ $users->count() }} user</span>
        </div>

        @if($users->isEmpty())
            <div class="empty-state">
                <p>Belum ada user terdaftar.</p>
            </div>
        @else
            <table class="user-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Umur</th>
                        <th>Jenis Sepeda</th>
                        <th>Bergabung</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $i => $user)
                        <tr class="{{ Auth::id() === $user->id ? 'row-me' : '' }}">
                            <td class="td-num">{{ $i + 1 }}</td>
                            <td>
                                {{ $user->name }}
                                @if(Auth::id() === $user->id)
                                    <span class="badge-me">kamu</span>
                                @endif
                            </td>
                            <td class="td-email">{{ $user->email }}</td>
                            <td>
                                @if($user->age)
                                    {{ $user->age }} tahun
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($user->bike_type)
                                    <span class="badge">{{ $user->bike_type }}</span>
                                @else
                                    <span class="text-muted">Belum dipilih</span>
                                @endif
                            </td>
                            <td class="td-date">{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection