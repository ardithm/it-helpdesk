@extends('layouts.app')

@section('title', 'Beri Nilai Pelayanan')
@section('page-title', 'Penilaian Layanan IT')
@section('page-subtitle', 'Tiket: ' . $ticket->ticket_number)

@section('content')

<div class="max-w-xl mx-auto mt-8">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden text-center">
        <div class="px-6 py-8">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
            </div>
            
            <h2 class="text-xl font-bold text-slate-800 mb-2">Tiket Selesai!</h2>
            <p class="text-sm text-slate-500 mb-8">Terima kasih telah melakukan konfirmasi. Bagaimana tingkat kepuasan Anda terhadap pelayanan teknisi kami untuk tiket <span class="font-medium text-slate-700">{{ $ticket->title }}</span>?</p>

            <form action="{{ route('user.tickets.rate.submit', $ticket) }}" method="POST">
                @csrf
                
                {{-- Bintang Rating (Custom Radio) --}}
                <div class="flex items-center justify-center gap-2 mb-6 rating-group" dir="rtl">
                    {{-- CSS hack for star rating using flex-row-reverse (rtl) --}}
                    @for($i = 5; $i >= 1; $i--)
                        <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" class="peer hidden" required>
                        <label for="star{{ $i }}" class="cursor-pointer text-slate-200 peer-checked:text-yellow-400 peer-hover:text-yellow-400 hover:text-yellow-400 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 fill-current" viewBox="0 0 24 24"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a.53.53 0 0 0 .4.29l5.16.756a.53.53 0 0 1 .294.904l-3.733 3.638a.53.53 0 0 0-.153.469l.882 5.14a.53.53 0 0 1-.77.56l-4.614-2.425a.53.53 0 0 0-.494 0L6.44 18.73a.53.53 0 0 1-.77-.56l.882-5.14a.53.53 0 0 0-.153-.47L2.665 8.924a.53.53 0 0 1 .294-.903l5.16-.756a.53.53 0 0 0 .4-.29z"/></svg>
                        </label>
                    @endfor
                </div>
                
                <style>
                    /* Style agar hover state bintang sebelumnya ikut berubah */
                    .rating-group label:hover ~ label { color: #facc15; } /* Tailwind yellow-400 */
                    .rating-group input:checked ~ label { color: #facc15; }
                </style>
                
                @error('rating') <p class="mb-4 text-sm text-red-500">{{ $message }}</p> @enderror

                <div class="text-left mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Komentar Tambahan (Opsional)</label>
                    <textarea name="comment" rows="3" placeholder="Beritahu kami apa yang sudah baik atau yang perlu ditingkatkan..." 
                              class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white text-sm transition-colors resize-none"></textarea>
                </div>

                <button type="submit" class="w-full py-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors shadow-sm">
                    Kirim Penilaian
                </button>
            </form>
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
            <a href="{{ route('user.tickets.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Lewati penilaian untuk saat ini</a>
        </div>
    </div>
</div>

@endsection
