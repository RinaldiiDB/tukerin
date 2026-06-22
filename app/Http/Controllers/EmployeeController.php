<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Mail\TransactionConfirmation;
use App\Models\BottleType;
use App\Models\ExchangeTransaction;
use App\Models\ExchangeTransactionDetail;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    /**
     * Display employee dashboard with today's stats.
     */
    public function dashboard()
    {
        $employee = Auth::user();

        $todayTransactions = ExchangeTransaction::accessibleBy($employee)
            ->whereDate('transacted_at', Carbon::today())
            ->orderBy('transacted_at', 'desc')
            ->get();

        $todayCount = $todayTransactions->count();
        $todayPoints = $todayTransactions->sum('total_points');

        return view('employee.dashboard', compact('todayTransactions', 'todayCount', 'todayPoints'));
    }

    /**
     * Show scanning page.
     */
    public function scan()
    {
        return view('employee.scan');
    }

    /**
     * Lookup user by QR code (AJAX).
     */
    public function lookupUser($qr_code)
    {
        $profile = UserProfile::where('qr_code', $qr_code)->with('user')->first();

        if (!$profile) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nasabah dengan QR Code "' . $qr_code . '" tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => $profile->user->id,
                'name' => $profile->user->name,
                'phone' => $profile->phone,
                'points_balance' => $profile->points_balance,
            ]
        ]);
    }

    /**
     * Lookup bottle type by barcode (AJAX).
     */
    public function lookupBottle($barcode)
    {
        $bottle = BottleType::where('barcode', $barcode)->first();

        if (!$bottle) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jenis botol dengan barcode "' . $barcode . '" tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'bottle' => [
                'id' => $bottle->id,
                'name' => $bottle->name,
                'barcode' => $bottle->barcode,
                'points_value' => $bottle->points_value,
            ]
        ]);
    }

    /**
     * Store the exchange transaction.
     */
    public function storeTransaction(StoreTransactionRequest $request)
    {
        $userId = $request->user_id;
        $items = $request->items;

        $user = User::find($userId);
        if (!$user || !$user->isUser()) {
            return back()->with('error', 'Nasabah tidak valid.');
        }

        $totalPoints = 0;
        $detailsData = [];

        // Pre-calculate details and total points
        foreach ($items as $item) {
            $bottleType = BottleType::find($item['bottle_type_id']);
            if (!$bottleType) {
                return back()->with('error', 'Jenis botol dengan ID ' . $item['bottle_type_id'] . ' tidak ditemukan.');
            }

            $qty = intval($item['quantity']);
            $pointsEarned = $qty * $bottleType->points_value;
            $totalPoints += $pointsEarned;

            $detailsData[] = [
                'bottle_type_id' => $bottleType->id,
                'quantity' => $qty,
                'points_earned' => $pointsEarned,
            ];
        }

        // Save transactional data using DB transaction
        $transaction = null;

        DB::transaction(function () use ($userId, $totalPoints, $detailsData, &$transaction) {
            $transaction = ExchangeTransaction::create([
                'user_id' => $userId,
                'employee_id' => Auth::id(),
                'total_points' => $totalPoints,
                'transacted_at' => Carbon::now(),
            ]);

            foreach ($detailsData as $detail) {
                ExchangeTransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'bottle_type_id' => $detail['bottle_type_id'],
                    'quantity' => $detail['quantity'],
                    'points_earned' => $detail['points_earned'],
                ]);
            }

            // Increment points_balance on user profile
            UserProfile::where('user_id', $userId)->increment('points_balance', $totalPoints);
        });

        $transaction->load(['details.bottleType', 'employee']);
        $user->load('profile');
        Mail::to($user)->queue(new TransactionConfirmation($user, $transaction));

        return redirect()->route('employee.dashboard')->with('success', 'Transaksi penukaran botol berhasil disimpan! Total poin ditambahkan: ' . $totalPoints . ' poin.');
    }

    /**
     * Show history of transactions processed by this employee.
     */
    public function transactions()
    {
        $transactions = ExchangeTransaction::accessibleBy(Auth::user())
            ->orderBy('transacted_at', 'desc')
            ->paginate(10);

        return view('employee.transactions', compact('transactions'));
    }
}
