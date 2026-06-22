<?php

namespace App\Http\Controllers;

use App\Mail\RedemptionStatus;
use App\Models\ExchangeTransaction;
use App\Models\RedemptionRequest;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    /**
     * Show global system statistics dashboard.
     */
    public function dashboard()
    {
        $totalUsers = User::whereHas('role', function ($query) {
            $query->where('name', 'user');
        })->count();

        $totalTransactions = ExchangeTransaction::count();
        $totalPointsCirculated = ExchangeTransaction::sum('total_points');
        $pendingRedemptions = RedemptionRequest::where('status', 'pending')->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalTransactions',
            'totalPointsCirculated',
            'pendingRedemptions'
        ));
    }

    /**
     * List all registered customers (Nasabah) and point balances.
     */
    public function users()
    {
        $users = User::whereHas('role', function ($query) {
            $query->where('name', 'user');
        })
        ->with('profile')
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return view('admin.users', compact('users'));
    }

    /**
     * List all transactions.
     */
    public function transactions()
    {
        $transactions = ExchangeTransaction::with(['user', 'employee'])
            ->orderBy('transacted_at', 'desc')
            ->paginate(15);

        return view('admin.transactions', compact('transactions'));
    }

    /**
     * List all redemptions.
     */
    public function redemptions()
    {
        $redemptions = RedemptionRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.redemptions', compact('redemptions'));
    }

    /**
     * Approve redemption request.
     */
    public function approveRedemption($id)
    {
        $request = RedemptionRequest::find($id);

        if (!$request) {
            return back()->with('error', 'Permintaan pencairan tidak ditemukan.');
        }

        if ($request->status !== 'pending') {
            return back()->with('error', 'Status permintaan pencairan bukan pending.');
        }

        $userProfile = UserProfile::where('user_id', $request->user_id)->first();
        if (!$userProfile) {
            return back()->with('error', 'Profil nasabah tidak ditemukan.');
        }

        if ($userProfile->points_balance < $request->points_used) {
            return back()->with('error', 'Poin nasabah tidak mencukupi untuk melakukan persetujuan (Saldo saat ini: ' . $userProfile->points_balance . ' poin, diperlukan: ' . $request->points_used . ' poin).');
        }

        DB::transaction(function () use ($request, $userProfile) {
            // Decrement points
            $userProfile->decrement('points_balance', $request->points_used);

            // Update status
            $request->update([
                'status' => 'approved',
                'processed_at' => Carbon::now(),
            ]);
        });

        $request->load('user.profile');
        Mail::to($request->user)->queue(new RedemptionStatus($request, 'approved'));

        return redirect()->route('admin.redemptions')->with('success', 'Permintaan pencairan poin berhasil disetujui! Saldo nasabah telah dipotong.');
    }

    /**
     * Reject redemption request.
     */
    public function rejectRedemption(Request $request, $id)
    {
        $rules = [
            'rejection_note' => 'required|string|max:500',
        ];

        $request->validate($rules, [
            'rejection_note.required' => 'Catatan penolakan wajib diisi.',
        ]);

        $redemption = RedemptionRequest::find($id);

        if (!$redemption) {
            return back()->with('error', 'Permintaan pencairan tidak ditemukan.');
        }

        if ($redemption->status !== 'pending') {
            return back()->with('error', 'Status permintaan pencairan bukan pending.');
        }

        $redemption->update([
            'status' => 'rejected',
            'rejection_note' => $request->rejection_note,
            'processed_at' => Carbon::now(),
        ]);

        $redemption->load('user.profile');
        Mail::to($redemption->user)->queue(new RedemptionStatus($redemption, 'rejected'));

        return redirect()->route('admin.redemptions')->with('success', 'Permintaan pencairan poin telah ditolak.');
    }
}
