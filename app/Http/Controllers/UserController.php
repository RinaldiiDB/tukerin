<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRedemptionRequest;
use App\Models\ExchangeTransaction;
use App\Models\RedemptionRequest;
use App\Notifications\BusinessActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class UserController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $profile = $user->profile;

        // Get recent transactions (limit 5)
        $recentTransactions = ExchangeTransaction::accessibleBy($user)
            ->orderBy('transacted_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent redemptions (limit 5)
        $recentRedemptions = RedemptionRequest::accessibleBy($user)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('user.dashboard', compact('profile', 'recentTransactions', 'recentRedemptions'));
    }

    /**
     * Show the user QR page.
     */
    public function qr()
    {
        $profile = Auth::user()->profile;
        return view('user.qr', compact('profile'));
    }

    /**
     * Show transaction history.
     */
    public function transactions()
    {
        $transactions = ExchangeTransaction::accessibleBy(Auth::user())
            ->orderBy('transacted_at', 'desc')
            ->paginate(10);

        return view('user.transactions', compact('transactions'));
    }

    /**
     * Show redemption history.
     */
    public function rewards()
    {
        $redemptions = RedemptionRequest::accessibleBy(Auth::user())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.rewards', compact('redemptions'));
    }

    /**
     * Show redemption create form.
     */
    public function createReward()
    {
        $profile = Auth::user()->profile;
        return view('user.rewards_create', compact('profile'));
    }

    /**
     * Store redemption request.
     */
    public function storeReward(StoreRedemptionRequest $request)
    {
        // 1 point = Rp 200 (Adjust rate as desired)
        $pointRate = 200;
        $pointsUsed = $request->validated('points_used');
        $amount = $pointsUsed * $pointRate;

        RedemptionRequest::create([
            'user_id' => Auth::id(),
            'points_used' => $pointsUsed,
            'amount' => $amount,
            'method' => $request->validated('method'),
            'bank_name' => $request->validated('bank_name'),
            'recipient_account' => $request->validated('recipient_account'),
            'status' => 'pending',
        ]);

        Notification::route('slack', config('logging.channels.slack.url'))
            ->notify(new BusinessActivity(
                action: 'Pengajuan pencairan',
                actor: Auth::user()->name . ' (User)',
                detail: $pointsUsed . ' poin - Rp' . number_format($amount, 0, ',', '.'),
            ));

        return redirect()->route('user.rewards')->with('success', 'Pengajuan pencairan ' . $pointsUsed . ' poin (Rp ' . number_format($amount, 0, ',', '.') . ') berhasil diajukan dan sedang menunggu persetujuan Admin.');
    }
}
