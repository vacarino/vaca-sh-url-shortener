<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InviteCode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InviteCodeController extends Controller
{
    /**
     * Display a listing of invite codes.
     */
    public function index(Request $request)
    {
        $query = InviteCode::with(['creator', 'user']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('creator', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            switch ($request->get('status')) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'used':
                    $query->whereNotNull('used_by');
                    break;
                case 'unused':
                    $query->whereNull('used_by');
                    break;
            }
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        if (in_array($sortBy, ['code', 'created_at', 'used_at', 'is_active'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $inviteCodes = $query->paginate(15)->withQueryString();

        // Get stats
        $stats = [
            'total_codes' => InviteCode::count(),
            'active_codes' => InviteCode::where('is_active', true)->count(),
            'used_codes' => InviteCode::whereNotNull('used_by')->count(),
            'unused_codes' => InviteCode::whereNull('used_by')->where('is_active', true)->count(),
        ];

        return view('admin.invite-codes.index', compact('inviteCodes', 'stats'));
    }

    /**
     * Show the form for creating a new invite code.
     */
    public function create()
    {
        return view('admin.invite-codes.create');
    }

    /**
     * Store a newly created invite code.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => [
                'nullable',
                'string',
                'max:20',
                'alpha_num',
                Rule::unique('invite_codes', 'code')
            ],
            'description' => 'nullable|string|max:255',
            'is_single_use' => 'boolean',
        ]);

        $code = $request->filled('code') 
            ? strtoupper($request->get('code'))
            : InviteCode::generateUniqueCode();

        $inviteCode = InviteCode::create([
            'code' => $code,
            'created_by' => auth()->id(),
            'description' => $request->get('description'),
            'is_single_use' => $request->boolean('is_single_use', true),
            'is_active' => true,
        ]);

        return redirect()->route('admin.invite-codes.index')
            ->with('success', "Invite code '{$code}' created successfully.");
    }

    /**
     * Show the form for editing an invite code.
     */
    public function edit(InviteCode $inviteCode)
    {
        return view('admin.invite-codes.edit', compact('inviteCode'));
    }

    /**
     * Update the specified invite code.
     */
    public function update(Request $request, InviteCode $inviteCode)
    {
        $request->validate([
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_single_use' => 'boolean',
        ]);

        $inviteCode->update([
            'description' => $request->get('description'),
            'is_active' => $request->boolean('is_active'),
            'is_single_use' => $request->boolean('is_single_use'),
        ]);

        return redirect()->route('admin.invite-codes.index')
            ->with('success', "Invite code '{$inviteCode->code}' updated successfully.");
    }

    /**
     * Toggle the active status of an invite code.
     */
    public function toggleStatus(InviteCode $inviteCode)
    {
        $inviteCode->update(['is_active' => !$inviteCode->is_active]);

        $status = $inviteCode->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Invite code '{$inviteCode->code}' has been {$status}.");
    }

    /**
     * Remove the specified invite code.
     */
    public function destroy(InviteCode $inviteCode)
    {
        $code = $inviteCode->code;
        $inviteCode->delete();

        return back()->with('success', "Invite code '{$code}' has been deleted.");
    }

    /**
     * Generate multiple invite codes at once.
     */
    public function generateBulk(Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:50',
            'description' => 'nullable|string|max:255',
            'is_single_use' => 'boolean',
        ]);

        $quantity = $request->get('quantity');
        $description = $request->get('description');
        $isSingleUse = $request->boolean('is_single_use', true);
        
        $codes = [];
        
        for ($i = 0; $i < $quantity; $i++) {
            $code = InviteCode::generateUniqueCode();
            
            InviteCode::create([
                'code' => $code,
                'created_by' => auth()->id(),
                'description' => $description ? $description . " (Bulk #" . ($i + 1) . ")" : "Bulk generated #" . ($i + 1),
                'is_single_use' => $isSingleUse,
                'is_active' => true,
            ]);
            
            $codes[] = $code;
        }

        return redirect()->route('admin.invite-codes.index')
            ->with('success', "Generated {$quantity} invite codes successfully.")
            ->with('generated_codes', $codes);
    }

    /**
     * Export invite codes to CSV.
     */
    public function export()
    {
        $inviteCodes = InviteCode::with(['creator', 'user'])->get();

        $filename = 'invite_codes_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Code',
            'Description',
            'Created By',
            'Used By',
            'Status',
            'Type',
            'Created At',
            'Used At'
        ]);
        
        // CSV data
        foreach ($inviteCodes as $inviteCode) {
            fputcsv($output, [
                $inviteCode->code,
                $inviteCode->description ?? '',
                $inviteCode->creator->name ?? '',
                $inviteCode->user->name ?? '',
                $inviteCode->is_active ? 'Active' : 'Inactive',
                $inviteCode->is_single_use ? 'Single Use' : 'Multi Use',
                $inviteCode->created_at->format('Y-m-d H:i:s'),
                $inviteCode->used_at ? $inviteCode->used_at->format('Y-m-d H:i:s') : ''
            ]);
        }
        
        fclose($output);
        exit;
    }
}
