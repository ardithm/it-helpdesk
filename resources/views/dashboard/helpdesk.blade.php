@extends('layouts.auth')
@section('title', 'Dashboard Helpdesk')
@section('content')
<div class="min-h-screen bg-slate-50 flex items-center justify-center">
    <div class="text-center">
        <p class="text-slate-500 text-sm mb-4">Dashboard Helpdesk (segera hadir)</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-xs text-purple-600 hover:underline">Logout</button>
        </form>
    </div>
</div>
@endsection
