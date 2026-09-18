@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Kelola informasi pribadi dan keamanan akun Anda')

@section('content')

<div class="max-w-4xl mx-auto space-y-8">

    {{-- Header Profil --}}
    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 flex flex-col md:flex-row items-center md:items-start gap-8 relative overflow-hidden">
        <!-- Dekorasi Background -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-purple-50 rounded-full blur-3xl -mr-20 -mt-20"></div>

        <div class="relative z-10 flex-shrink-0">
            <div class="w-32 h-32 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full flex items-center justify-center border-4 border-white shadow-lg shadow-purple-500/10">
                <span class="text-4xl font-black text-purple-600 tracking-tighter">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
            </div>
            <div class="absolute -bottom-2 -right-2 bg-white p-1 rounded-full shadow-sm">
                <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
                </div>
            </div>
        </div>

        <div class="relative z-10 text-center md:text-left flex-1 mt-2">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-bold uppercase tracking-wide mb-3 border border-purple-100">
                <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                Role: {{ $user->role }}
            </div>
            <h2 class="text-3xl font-extrabold text-slate-800 mb-1">{{ $user->name }}</h2>
            <p class="text-slate-500 font-medium mb-4">{{ $user->email }}</p>
            
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-4">
                <div class="flex items-center gap-2 bg-slate-50 px-4 py-2 rounded-xl text-sm border border-slate-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/></svg>
                    <span class="text-slate-600 font-medium">{{ $user->department?->name ?? 'Belum ada departemen' }}</span>
                </div>
                <div class="flex items-center gap-2 bg-slate-50 px-4 py-2 rounded-xl text-sm border border-slate-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                    <span class="text-slate-600">Bergabung: {{ $user->created_at->translatedFormat('F Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        {{-- Form Edit Profil --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/30">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Informasi Pribadi
                </h3>
                <p class="text-sm text-slate-500 mt-1">Perbarui nama lengkap dan alamat email Anda.</p>
            </div>
            
            <form action="{{ route('profile.update') }}" method="POST" class="p-8">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 focus:bg-white transition-all outline-none @error('name') border-red-300 bg-red-50 @enderror">
                        @error('name') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 focus:bg-white transition-all outline-none @error('email') border-red-300 bg-red-50 @enderror">
                        @error('email') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl shadow-sm transition-colors flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Simpan Profil
                    </button>
                </div>
            </form>
        </div>

        {{-- Form Ubah Password --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/30">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-orange-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Keamanan Akun
                </h3>
                <p class="text-sm text-slate-500 mt-1">Pastikan akun Anda aman dengan password yang kuat.</p>
            </div>
            
            <form action="{{ route('profile.password') }}" method="POST" class="p-8">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Password Saat Ini</label>
                        <input type="password" name="current_password" required placeholder="••••••••"
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 focus:bg-white transition-all outline-none @error('current_password') border-red-300 bg-red-50 @enderror">
                        @error('current_password') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="h-px bg-slate-100 my-4"></div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Password Baru</label>
                        <input type="password" name="password" required placeholder="••••••••"
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 focus:bg-white transition-all outline-none @error('password') border-red-300 bg-red-50 @enderror">
                        @error('password') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                        <p class="text-xs text-slate-400 mt-2">Minimal 8 karakter.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" required placeholder="••••••••"
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 focus:bg-white transition-all outline-none">
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-xl shadow-sm transition-colors flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>

@endsection
