<?php

namespace App\Http\Controllers\BusMetro\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusMetro\TransactionPaiement;
use App\Models\BusMetro\AuditLog;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = TransactionPaiement::with('adherent');

        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('statut')) $query->where('statut', $request->statut);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('reference_interne', 'like', "%$s%")
                ->orWhere('token_paiement', 'like', "%$s%")
                ->orWhereHas('adherent', fn($q2) => $q2->where('nom', 'like', "%$s%")));
        }

        $transactions = $query->orderByDesc('created_at')->paginate(30);
        return view('busmetro.admin.transactions.index', compact('transactions'));
    }

    public function show(TransactionPaiement $transaction)
    {
        $transaction->load('adherent', 'payable');
        return view('busmetro.admin.transactions.show', compact('transaction'));
    }
}
