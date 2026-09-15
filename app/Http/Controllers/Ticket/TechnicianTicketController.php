<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketComment;
use App\Models\TicketHistory;
use App\Models\TicketResolution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TechnicianTicketController extends Controller
{
    /**
     * Daftar tiket yang diassign kepada teknisi ini.
     */
    public function index(Request $request)
    {
        $query = Ticket::whereHas('activeAssignment', fn ($q) => $q->where('technician_id', auth()->id()))
            ->with(['user.department', 'category', 'slaPolicy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('ticket_number', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%"));
        }

        $tickets = $query->orderBy('resolution_deadline')->paginate(10)->withQueryString();

        return view('technician.tickets.index', compact('tickets'));
    }

    /**
     * Detail tiket yang diassign ke teknisi ini.
     */
    public function show(Ticket $ticket)
    {
        // Pastikan teknisi ini memang punya assignment aktif pada tiket ini
        abort_unless(
            $ticket->activeAssignment?->technician_id === auth()->id() ||
            auth()->user()->role === 'admin',
            403
        );

        $ticket->load([
            'user.department',
            'category',
            'asset',
            'slaPolicy',
            'activeAssignment.technician',
            'comments.user',
            'attachments.uploadedBy',
            'histories.user',
            'resolution',
        ]);

        return view('technician.tickets.show', compact('ticket'));
    }

    /**
     * Teknisi mengambil tiket (ASSIGNED → IN PROGRESS).
     */
    public function startWork(Ticket $ticket)
    {
        abort_unless($ticket->activeAssignment?->technician_id === auth()->id(), 403);
        abort_unless($ticket->status === Ticket::STATUS_ASSIGNED, 422);

        $ticket->update(['status' => Ticket::STATUS_IN_PROGRESS]);

        TicketHistory::create([
            'ticket_id'  => $ticket->id,
            'user_id'    => auth()->id(),
            'action'     => 'status.changed',
            'old_value'  => Ticket::STATUS_ASSIGNED,
            'new_value'  => Ticket::STATUS_IN_PROGRESS,
            'created_at' => now(),
        ]);

        return back()->with('success', 'Tiket sedang dalam pengerjaan.');
    }

    /**
     * Teknisi mengubah ke WAITING (menunggu info dari user).
     */
    public function setWaiting(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->activeAssignment?->technician_id === auth()->id(), 403);
        abort_unless($ticket->status === Ticket::STATUS_IN_PROGRESS, 422);

        $request->validate(['reason' => ['required', 'string', 'max:500']]);

        $ticket->update(['status' => Ticket::STATUS_WAITING]);

        TicketComment::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => auth()->id(),
            'comment'     => "Menunggu informasi dari User: {$request->reason}",
            'is_internal' => false,
        ]);

        TicketHistory::create([
            'ticket_id'  => $ticket->id,
            'user_id'    => auth()->id(),
            'action'     => 'status.changed',
            'old_value'  => Ticket::STATUS_IN_PROGRESS,
            'new_value'  => Ticket::STATUS_WAITING,
            'created_at' => now(),
        ]);

        return back()->with('success', 'Status tiket diubah menjadi Waiting.');
    }

    /**
     * Teknisi menyelesaikan tiket — mengisi form resolution (IN PROGRESS → RESOLVED).
     */
    public function resolve(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->activeAssignment?->technician_id === auth()->id(), 403);
        abort_unless(in_array($ticket->status, [Ticket::STATUS_IN_PROGRESS, Ticket::STATUS_WAITING]), 422);

        $validated = $request->validate([
            'diagnosis'   => ['required', 'string', 'min:10'],
            'action_taken' => ['required', 'string', 'min:10'],
            'sparepart'   => ['nullable', 'string', 'max:500'],
            'resolution'  => ['required', 'string', 'min:10'],
            'attachments'  => ['nullable', 'array', 'max:3'],
            'attachments.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,gif,pdf'],
        ], [
            'diagnosis.required'    => 'Diagnosis wajib diisi.',
            'action_taken.required' => 'Tindakan yang dilakukan wajib diisi.',
            'resolution.required'   => 'Resolusi wajib diisi.',
        ]);

        DB::beginTransaction();
        try {
            // Upsert resolution (bisa diedit sebelum ditutup)
            TicketResolution::updateOrCreate(
                ['ticket_id' => $ticket->id],
                [
                    'technician_id' => auth()->id(),
                    'diagnosis'     => $validated['diagnosis'],
                    'action_taken'  => $validated['action_taken'],
                    'sparepart'     => $validated['sparepart'] ?? null,
                    'resolution'    => $validated['resolution'],
                    'resolved_at'   => now(),
                ]
            );

            // Upload bukti pekerjaan jika ada
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store("tickets/{$ticket->id}/resolution", 'public');
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

            $ticket->update([
                'status'      => Ticket::STATUS_RESOLVED,
                'resolved_at' => now(),
            ]);

            TicketHistory::create([
                'ticket_id'  => $ticket->id,
                'user_id'    => auth()->id(),
                'action'     => 'ticket.resolved',
                'old_value'  => $ticket->getOriginal('status'),
                'new_value'  => Ticket::STATUS_RESOLVED,
                'created_at' => now(),
            ]);

            AuditLog::record('ticket.resolved', $ticket, null, ['resolved_by' => auth()->user()->name]);

            DB::commit();
            return redirect()->route('technician.tickets.show', $ticket)
                ->with('success', 'Tiket berhasil diselesaikan. Menunggu konfirmasi dari user.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal menyelesaikan tiket.']);
        }
    }

    /**
     * Teknisi menambah komentar (publik atau internal).
     */
    public function addComment(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->activeAssignment?->technician_id === auth()->id(), 403);

        $request->validate([
            'comment'     => ['required', 'string', 'min:3', 'max:2000'],
            'is_internal' => ['boolean'],
        ]);

        TicketComment::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => auth()->id(),
            'comment'     => $request->comment,
            'is_internal' => $request->boolean('is_internal'),
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}
