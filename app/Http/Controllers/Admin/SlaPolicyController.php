<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SlaPolicy;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SlaPolicyController extends Controller
{
    public function index()
    {
        $priorities = [
            Ticket::PRIORITY_CRITICAL,
            Ticket::PRIORITY_HIGH,
            Ticket::PRIORITY_MEDIUM,
            Ticket::PRIORITY_LOW,
        ];

        $policies = SlaPolicy::orderByRaw("FIELD(priority, 'critical','high','medium','low')")->get();

        return view('admin.sla.index', compact('policies', 'priorities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'priority'               => ['required', 'in:low,medium,high,critical', Rule::unique('sla_policies', 'priority')],
            'response_time_minutes'  => ['required', 'integer', 'min:1'],
            'resolution_time_minutes' => ['required', 'integer', 'min:1'],
            'is_active'              => ['boolean'],
        ], [
            'priority.unique' => 'SLA policy untuk prioritas ini sudah ada.',
        ]);

        SlaPolicy::create([
            'priority'               => $request->priority,
            'response_time_minutes'  => $request->response_time_minutes,
            'resolution_time_minutes' => $request->resolution_time_minutes,
            'is_active'              => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'SLA Policy berhasil ditambahkan.');
    }

    public function update(Request $request, SlaPolicy $slaPolicy)
    {
        $request->validate([
            'response_time_minutes'  => ['required', 'integer', 'min:1'],
            'resolution_time_minutes' => ['required', 'integer', 'min:1'],
            'is_active'              => ['boolean'],
        ]);

        $slaPolicy->update([
            'response_time_minutes'  => $request->response_time_minutes,
            'resolution_time_minutes' => $request->resolution_time_minutes,
            'is_active'              => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'SLA Policy berhasil diperbarui.');
    }
}
