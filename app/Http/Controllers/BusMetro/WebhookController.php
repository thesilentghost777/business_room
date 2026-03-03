<?php

namespace App\Http\Controllers\BusMetro;

use App\Http\Controllers\Controller;
use App\Services\BusMetro\MoneyFusionService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function moneyfusion(Request $request, MoneyFusionService $moneyFusion)
    {
        $data = $request->all();

        \Illuminate\Support\Facades\Log::info('MoneyFusion Webhook reçu', $data);

        $moneyFusion->traiterWebhook($data);

        return response()->json(['status' => 'ok']);
    }
}
