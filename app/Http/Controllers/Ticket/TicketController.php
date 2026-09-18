<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\SlaPolicy;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketCategory;
use App\Models\TicketComment;
use App\Models\TicketHistory;
use App\Models\TicketRating;
use App\Models\User;
use App\Notifications\NewTicketCommentNotification;
use App\Notifications\TicketCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * Daftar tiket milik user yang sedang login.
     */
    public function index(Request $request)
    {
        $query = Ticket::where('user_id', auth()->id())
            ->with(['category', 'activeAssignment.technician']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('ticket_number', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%"));
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();

        return view('ticket.index', compact('tickets'));
    }

    /**
     * Form buat tiket baru.
     */
    public function create()
    {
        $categories = TicketCategory::active()->orderBy('name')->get();
        $assets     = Asset::where('user_id', auth()->id())->active()->orderBy('brand')->get();

        return view('ticket.create', compact('categories', 'assets'));
    }

    /**
     * Simpan tiket baru — assign SLA otomatis, generate nomor tiket.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'  => ['required', 'exists:ticket_categories,id'],
            'asset_id'     => ['nullable', 'exists:assets,id'],
            'type'         => ['required', 'in:incident,service_request'],
            'title'        => ['required', 'string', 'min:5', 'max:255'],
            'description'  => ['required', 'string', 'min:10'],
            'priority'     => ['required', 'in:low,medium,high,critical'],
            'attachments'  => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip'],
        ], [
            'category_id.required'  => 'Kategori wajib dipilih.',
            'type.required'         => 'Tipe tiket wajib dipilih.',
            'title.required'        => 'Judul tiket wajib diisi.',
            'title.min'             => 'Judul minimal 5 karakter.',
            'description.required'  => 'Deskripsi masalah wajib diisi.',
            'description.min'       => 'Deskripsi minimal 10 karakter.',
            'priority.required'     => 'Prioritas wajib dipilih.',
        ]);

        DB::beginTransaction();
        try {
            // Cari SLA policy berdasarkan priority
            $sla = SlaPolicy::forPriority($validated['priority'])->first();

            $ticket = Ticket::create([
                'ticket_number'       => Ticket::generateTicketNumber(),
                'user_id'             => auth()->id(),
                'category_id'         => $validated['category_id'],
                'asset_id'            => $validated['asset_id'] ?? null,
                'type'                => $validated['type'],
                'title'               => $validated['title'],
                'description'         => $validated['description'],
                'priority'            => $validated['priority'],
                'status'              => Ticket::STATUS_OPEN,
                'sla_policy_id'       => $sla?->id,
                'response_deadline'   => $sla ? now()->addMinutes($sla->response_time_minutes) : null,
                'resolution_deadline' => $sla ? now()->addMinutes($sla->resolution_time_minutes) : null,
            ]);

            // Upload attachment jika ada
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store("tickets/{$ticket->id}", 'public');
                    TicketAttachment::create([
                        'ticket_id'   => $ticket->id,
                        'uploaded_by' => auth()->id(),
                        'file_name'   => $file->getClientOriginalName(),
                        'file_path'   => $path,
                        'file_type'   => $file->getMimeType(),
                        'file_size'   => $file->getSize(),
                        'created_at'  => now(),
                    ]);
                }
            }

            // Catat history
            TicketHistory::create([
                'ticket_id'  => $ticket->id,
                'user_id'    => auth()->id(),
                'action'     => 'ticket.created',
                'new_value'  => "Tiket dibuat dengan status OPEN",
                'created_at' => now(),
            ]);

            AuditLog::record('ticket.created', $ticket, null, ['ticket_number' => $ticket->ticket_number]);

            // Notify Helpdesk & Admin
            $helpdesks = User::whereIn('role', ['admin', 'helpdesk'])->where('is_active', true)->get();
            Notification::send($helpdesks, new TicketCreatedNotification($ticket));

            DB::commit();

            return redirect()->route('user.tickets.show', $ticket)
                ->with('success', "Tiket {$ticket->ticket_number} berhasil dibuat.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal membuat tiket. Silakan coba lagi.']);
        }
    }

    /**
     * Detail tiket — hanya tiket milik sendiri.
     */
    public function show(Ticket $ticket)
    {
        Gate::authorize('view', $ticket);

        $ticket->load([
            'category',
            'asset',
            'slaPolicy',
            'activeAssignment.technician',
            'comments.user',
            'attachments.uploadedBy',
            'histories.user',
            'resolution.technician',
            'rating',
        ]);

        return view('ticket.show', compact('ticket'));
    }

    /**
     * User menambah komentar pada tiket-nya sendiri.
     */
    public function addComment(Request $request, Ticket $ticket)
    {
        Gate::authorize('view', $ticket);

        $request->validate([
            'comment' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        TicketComment::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => auth()->id(),
            'comment'     => $request->comment,
            'is_internal' => false,
        ]);

        TicketHistory::create([
            'ticket_id'  => $ticket->id,
            'user_id'    => auth()->id(),
            'action'     => 'comment.added',
            'new_value'  => 'User menambahkan komentar',
            'created_at' => now(),
        ]);

        // Notify Technician
        if ($ticket->activeAssignment?->technician) {
            $ticket->activeAssignment->technician->notify(new NewTicketCommentNotification($ticket, auth()->user()->name, route('technician.tickets.show', $ticket->id)));
        }

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    /**
     * User mengonfirmasi penyelesaian tiket (RESOLVED → CLOSED).
     */
    public function confirmResolution(Ticket $ticket)
    {
        Gate::authorize('view', $ticket);

        abort_unless($ticket->status === Ticket::STATUS_RESOLVED, 403);

        $ticket->update([
            'status'    => Ticket::STATUS_CLOSED,
            'closed_at' => now(),
        ]);

        TicketHistory::create([
            'ticket_id'  => $ticket->id,
            'user_id'    => auth()->id(),
            'action'     => 'status.changed',
            'old_value'  => Ticket::STATUS_RESOLVED,
            'new_value'  => Ticket::STATUS_CLOSED,
            'created_at' => now(),
        ]);

        AuditLog::record('ticket.closed', $ticket);

        return redirect()->route('user.tickets.rate', $ticket)
            ->with('success', 'Terima kasih telah mengonfirmasi penyelesaian. Mohon berikan rating pelayanan kami.');
    }

    /**
     * Form rating tiket yang sudah resolved/closed.
     */
    public function showRateForm(Ticket $ticket)
    {
        Gate::authorize('view', $ticket);
        abort_unless(in_array($ticket->status, [Ticket::STATUS_RESOLVED, Ticket::STATUS_CLOSED]), 403);
        abort_if($ticket->rating()->exists(), 403);

        return view('ticket.rate', compact('ticket'));
    }

    /**
     * Simpan rating dari user.
     */
    public function submitRating(Request $request, Ticket $ticket)
    {
        Gate::authorize('view', $ticket);
        abort_if($ticket->rating()->exists(), 403);

        $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        TicketRating::create([
            'ticket_id'  => $ticket->id,
            'user_id'    => auth()->id(),
            'rating'     => $request->rating,
            'comment'    => $request->comment,
            'created_at' => now(),
        ]);

        AuditLog::record('ticket.rated', $ticket, null, ['rating' => $request->rating]);

        return redirect()->route('user.tickets.index')
            ->with('success', 'Terima kasih atas rating Anda!');
    }
}
