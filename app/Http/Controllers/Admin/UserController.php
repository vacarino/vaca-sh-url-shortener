<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query()
            ->withCount(['shortUrls'])
            ->withSum('shortUrls as total_clicks', 'clicks');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role') && in_array($request->get('role'), ['admin', 'collaborator'])) {
            $query->where('role', $request->get('role'));
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        if (in_array($sortBy, ['name', 'email', 'role', 'created_at', 'short_urls_count', 'total_clicks'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $users = $query->paginate(15)->withQueryString();

        // Get stats for dashboard
        $stats = [
            'total_users' => User::count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'collaborator_users' => User::where('role', 'collaborator')->count(),
            'users_this_month' => User::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Promote user to admin.
     */
    public function promote(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'User is already an admin.');
        }

        $user->update(['role' => 'admin']);

        return back()->with('success', "User {$user->name} has been promoted to admin.");
    }

    /**
     * Demote admin to collaborator.
     */
    public function demote(User $user)
    {
        if ($user->role === 'collaborator') {
            return back()->with('error', 'User is already a collaborator.');
        }

        // Prevent demoting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot demote yourself.');
        }

        $user->update(['role' => 'collaborator']);

        return back()->with('success', "User {$user->name} has been demoted to collaborator.");
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        // Check if user has short URLs
        $urlCount = $user->shortUrls()->count();
        
        if ($urlCount > 0) {
            // For safety, we'll soft-delete by marking as inactive instead of hard delete
            $user->update(['email' => $user->email . '_deleted_' . time()]);
            return back()->with('warning', "User {$user->name} has been deactivated (had {$urlCount} URLs).");
        }

        $userName = $user->name;
        $user->delete();

        return back()->with('success', "User {$userName} has been deleted.");
    }

    /**
     * Toggle user status (activate/deactivate).
     */
    public function toggleStatus(User $user)
    {
        // For this implementation, we'll use a simple email suffix to mark as inactive
        // In a production app, you'd add an 'is_active' column
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate yourself.');
        }

        $isDeactivated = str_contains($user->email, '_deactivated_');
        
        if ($isDeactivated) {
            // Reactivate user
            $user->update(['email' => preg_replace('/_deactivated_\d+/', '', $user->email)]);
            return back()->with('success', "User {$user->name} has been reactivated.");
        } else {
            // Deactivate user
            $user->update(['email' => $user->email . '_deactivated_' . time()]);
            return back()->with('success', "User {$user->name} has been deactivated.");
        }
    }

    /**
     * Export users to CSV.
     */
    public function export()
    {
        $users = User::with(['shortUrls'])
            ->withCount('shortUrls')
            ->withSum('shortUrls as total_clicks', 'clicks')
            ->get();

        $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'ID',
            'Name',
            'Email',
            'Role',
            'Date Registered',
            'Links Created',
            'Total Clicks',
            'Status'
        ]);
        
        // CSV data
        foreach ($users as $user) {
            $isDeactivated = str_contains($user->email, '_deactivated_');
            $cleanEmail = preg_replace('/_deactivated_\d+/', '', $user->email);
            
            fputcsv($output, [
                $user->id,
                $user->name,
                $cleanEmail,
                ucfirst($user->role),
                $user->created_at->format('Y-m-d H:i:s'),
                $user->short_urls_count ?? 0,
                $user->total_clicks ?? 0,
                $isDeactivated ? 'Deactivated' : 'Active'
            ]);
        }
        
        fclose($output);
        exit;
    }
} 