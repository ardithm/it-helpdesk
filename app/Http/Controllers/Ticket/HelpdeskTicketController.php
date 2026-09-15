<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SlaPolicy;
use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\TicketComment;
use App\Models\TicketHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HelpdeskTicketController extends Controller
{
    /**
     * Daftar semua tiket dengan filter lengkap.
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['user.department', 'category', 'activeAssignment.technician']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('ticket_number', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")));
        }

        $sortBy  = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $allowedSorts = ['created_at', 'priority', 'status', 'resolution_deadline'];
        $query->orderBy(in_array($sortBy, $allowedSorts) ? $sortBy : 'created_at', $sortDir === 'asc' ? 'asc' : 'desc');

        $tickets     = $query->paginate(15)->withQueryString();
        $technicians = User::where('role', 'technician')->where('is_active', true)->orderBy('name')->get();
        $categories  = \App\Models\TicketCategory::active()->orderBy('name')->get();

        return view('helpdesk.tickets.index', compact('tickets', 'technicians', 'categories'));
    }

    /**
     * Detail tiket — helpdesk bisa lihat semua tiket.
     */
    public function show(Ticket $ticket)
    {
        $ticket->load([
            'user.department',
            'category',
            'asset',
            'slaPolicy',
            'activeAssignment.technician',
            'assignments.technician',
            'comments.user',
            'attachments.uploadedBy',
            'histories.user',
            'resolution.technician',
            'rating',
        ]);

        $technicians = User::where('role', 'technician')->where('is_active', true)->orderBy('name')->get();

        return view('helpdesk.tickets.show', compact('ticket', 'technicians'));
    }

    /**
     * Helpdesk melakukan klasifikasi & update priority/category tiket.
     */
    public function classify(Request $request, Ticket $ticket)
    {
        $request->validate([
            'priority'    => ['required', 'in:low,medium,high,critical'],
            'category_id' => ['required', 'exists:ticket_categories,id'],
            'type'        => ['required', 'in:incident,service_request'],
        ]);

        DB::beginTransaction();
        try {
            $oldPriority = $ticket->priority;

            // Update SLA berdasarkan priority baru
            $sla = SlaPolicy::forPriority($request->priority)->first();

            $ticket->update([
                'priority'            => $request->priority,
                'category_id'         => $request->category_id,
                'type'                => $request->type,
                'sla_policy_id'       => $sla?->id,
                'response_deadline'   => $sla ? now()->addMinutes($sla->response_time_minutes) : $ticket->response_deadline,
                'resolution_deadline' => $sla ? now()->addMinutes($sla->resolution_time_minutes) : $ticket->resolution_deadline,
            ]);

            if ($oldPriority !== $request->priority) {
                TicketHistory::create([
                    'ticket_id'  => $ticket->id,
                    'user_id'    => auth()->id(),
                    'action'     => 'priority.changed',
                    'old_value'  => $oldPriority,
                    'new_value'  => $request->priority,
                    'created_at' => now(),
                ]);
            }

            DB::commit();
            return back()->with('success', 'Tiket berhasil diklasifikasikan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal mengklasifikasi tiket.']);
        }
    }

    /**
     * Assign tiket kepada teknisi.
     */
    public function assign(Request $request, Ticket $ticket)
    {
        $request->validate([
            'technician_id' => ['required', 'exists:users,id'],
        ]);

        $technician = User::where('id', $request->technician_id)->where('role', 'technician')->firstOrFail();

        DB::beginTransaction();
        try {
            // Nonaktifkan assignment lama jika ada
            TicketAssignment::where('ticket_id', $ticket->id)
                ->whereNull('unassigned_at')
                ->update(['unassigned_at' => now()]);

            // Buat assignment baru
            TicketAssignment::create([
                'ticket_id'     => $ticket->id,
                'technician_id' => $technician->id,
                'assigned_by'   => auth()->id(),
                'assigned_at'   => now(),
                'created_at'    => now(),
            ]);

            $oldStatus = $ticket->status;

            // Update status ke ASSIGNED jika masih OPEN
            if ($ticket->status === Ticket::STATUS_OPEN) {
                $ticket->update(['status' => Ticket::STATUS_ASSIGNED]);
            }

            // Catat first_responded_at jika belum pernah direspons
            if (! $ticket->first_responded_at) {
                $ticket->update(['first_responded_at' => now()]);
            }

            TicketHistory::create([
                'ticket_id'  => $ticket->id,
                'user_id'    => auth()->id(),
                'action'     => 'ticket.assigned',
                'old_value'  => null,
                'new_value'  => "Ditugaskan kepada {$technician->name}",
                'created_at' => now(),
            ]);

            if ($oldStatus !== $ticket->fresh()->status) {
                TicketHistory::create([
                    'ticket_id'  => $ticket->id,
                    'user_id'    => auth()->id(),
                    'action'     => 'status.changed',
                    'old_value'  => $oldStatus,
                    'new_value'  => Ticket::STATUS_ASSIGNED,
                    'created_at' => now(),
                ]);
            }

            AuditLog::record('ticket.assigned', $ticket, null, ['technician' => $technician->name]);

            DB::commit();
            return back()->with('success', "Tiket berhasil diassign kepada {$technician->name}.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal mengassign tiket.']);
        }
    }

    /**
     * Helpdesk menambah komentar (bisa internal atau publik).
     */
    public function addComment(Request $request, Ticket $ticket)
    {
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

    /**
     * Update status tiket secara manual oleh Helpdesk.
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => ['required', 'in:open,assigned,in_progress,waiting,resolved,closed'],
        ]);

        $oldStatus = $ticket->status;
        $newStatus = $request->status;

        $updates = ['status' => $newStatus];
        if ($newStatus === Ticket::STATUS_RESOLVED && ! $ticket->resolved_at) {
            $updates['resolved_at'] = now();
        }
        if ($newStatus === Ticket::STATUS_CLOSED && ! $ticket->closed_at) {
            $updates['closed_at'] = now();
        }

        $ticket->update($updates);

        TicketHistory::create([
            'ticket_id'  => $ticket->id,
            'user_id'    => auth()->id(),
            'action'     => 'status.changed',
            'old_value'  => $oldStatus,
            'new_value'  => $newStatus,
            'created_at' => now(),
        ]);

        return back()->with('success', "Status tiket diubah menjadi " . strtoupper($newStatus) . ".");
    }
}
