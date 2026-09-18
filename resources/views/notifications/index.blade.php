@extends('layouts.app')

@section('title', 'Semua Notifikasi')
@section('page-title', 'Semua Notifikasi')
@section('page-subtitle', 'Riwayat pemberitahuan aktivitas Anda')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-800">Riwayat Notifikasi</h2>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-purple-50 text-purple-700 text-sm font-medium rounded-xl hover:bg-purple-100 transition-colors">
                    Tandai semua sudah dibaca
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="divide-y divide-slate-100">
            @forelse($notifications as $notification)
                <a href="{{ route('notifications.markAsRead', $notification->id) }}" class="flex items-start p-4 hover:bg-slate-50 transition-colors {{ is_null($notification->read_at) ? 'bg-purple-50/20' : '' }}">
                    <div class="flex-shrink-0 mr-4">
                        <div class="w-10 h-10 rounded-full bg-{{ $notification->data['color'] ?? 'purple' }}-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-{{ $notification->data['color'] ?? 'purple' }}-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                @if(isset($notification->data['icon']))
                                    @if($notification->data['icon'] == 'plus-circle')
                                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
                                    @elseif($notification->data['icon'] == 'user-check')
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H5c-2.2 0-4 1.8-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/>
                                    @elseif($notification->data['icon'] == 'refresh-cw')
                                        <polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                                    @elseif($notification->data['icon'] == 'message-circle')
                                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                                    @else
                                        <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                                    @endif
                                @else
                                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                                @endif
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 {{ is_null($notification->read_at) ? 'text-purple-700' : '' }}">
                            {{ $notification->data['title'] ?? 'Notifikasi' }}
                        </p>
                        <p class="text-sm text-slate-500 mt-0.5">
                            {{ $notification->data['message'] ?? '' }}
                        </p>
                        <p class="text-xs text-slate-400 mt-1">
                            {{ $notification->created_at->diffForHumans() }} 
                            <span class="mx-1">&bull;</span>
                            {{ $notification->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                    @if(is_null($notification->read_at))
                        <div class="flex-shrink-0 ml-4 flex items-center h-full">
                            <span class="w-2.5 h-2.5 bg-purple-500 rounded-full"></span>
                        </div>
                    @endif
                </a>
            @empty
                <div class="p-8 text-center text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto text-slate-300 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                    </svg>
                    <p class="text-base font-medium text-slate-600">Belum Ada Notifikasi</p>
                    <p class="text-sm mt-1">Saat ini Anda tidak memiliki pemberitahuan apa pun.</p>
                </div>
            @endforelse
        </div>
    </div>

    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
