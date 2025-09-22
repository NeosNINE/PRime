<?php

namespace App\Services;

use App\Extra\Services\Service;
use App\Extra\Services\Traits\ServiceTrait;
use App\Models\System\PromoCode;
use App\Models\System\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserBalanceService extends Service
{
    use ServiceTrait;

    public function fakeTopup(Request $request): array
    {
        $request->validate([
            'amount' => ['required','numeric','min:1','max:50000'],
            'method' => ['required','string'],
            'receipt' => ['nullable','file','mimes:jpg,jpeg,png,pdf','max:10240'],
            'promo_code' => ['nullable','string','max:64'],
        ]);

        $user = Auth::user();
        $amount = (float)$request->input('amount');
        $method = (string)$request->input('method');
        $promoCodeStr = trim((string) $request->input('promo_code')) ?: null;
        $appliedPromoAmount = 0.0;

        if ($promoCodeStr) {
            $promo = PromoCode::where('code', $promoCodeStr)->first();
            if ($promo && $promo->isValidForUser($user)) {
                $appliedPromoAmount = max(0.0, (float) $promo->bonus_amount);
            }
        }

        $isManual = in_array($method, ['paypal','manual_test','lolz'], true) || !((bool)(config('payments.methods.'.$method.'.auto') ?? false));

        if ($isManual) {
            $meta = null;
            if ($request->hasFile('receipt')) {
                $file = $request->file('receipt');
                $path = $file->store('receipts', 'public');
                $meta = [
                    'receipt_path' => Storage::url($path),
                    'receipt_name' => $file->getClientOriginalName(),
                    'receipt_mime' => $file->getClientMimeType(),
                ];
            }

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'external_id' => null,
                'amount_usd' => $amount,
                'method' => $method,
                'status' => 'pending',
                'meta' => array_filter([
                    'receipt_path' => $meta['receipt_path'] ?? null,
                    'receipt_name' => $meta['receipt_name'] ?? null,
                    'receipt_mime' => $meta['receipt_mime'] ?? null,
                    'promo_code' => $promoCodeStr,
                    'promo_bonus_usd' => $appliedPromoAmount,
                ], fn($v) => $v !== null),
            ]);

            if ($meta) {
                $transaction->meta = $meta;
                $transaction->save();
            }
        } else {
            $bonus = $this->calculateBonus($amount, (array) config('settings.payments.bonuses', []));

            $user->balance = (float)$user->balance + $amount + $bonus + $appliedPromoAmount;
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'external_id' => null,
                'amount_usd' => $amount,
                'method' => $method,
                'status' => 'completed',
                'meta' => array_filter([
                    'bonus_usd' => $bonus > 0 ? $bonus : null,
                    'promo_code' => $promoCodeStr,
                    'promo_bonus_usd' => $appliedPromoAmount,
                ], fn($v) => $v !== null),
            ]);

            if (!empty($promoCodeStr) && isset($promo) && $promo && $promo->type === 'individual' && $appliedPromoAmount > 0) {
                DB::table('promo_code_user')
                    ->where('promo_code_id', $promo->id)
                    ->where('user_id', $user->id)
                    ->update(['used_at' => now()]);
            }
        }

        return [
            'success' => true,
            'balance_usd' => (float)$user->balance,
        ];
    }

    public function applyPromo(Request $request): array
    {
        $request->validate([
            'code' => ['required','string','max:64'],
        ]);

        $user = Auth::user();
        $code = trim((string)$request->input('code'));
        $promo = PromoCode::where('code', $code)->first();

        if (!$promo || !$promo->isValidForUser($user)) {
            return ['success' => false, 'message' => 'Промокод недействителен'];
        }

        return [
            'success' => true,
            'data' => [
                'code' => $promo->code,
                'type' => $promo->type,
                'bonus_usd' => (float)$promo->bonus_amount,
                'expires_at' => $promo->expires_at ? $promo->expires_at->toIso8601String() : null,
            ]
        ];
    }

    public function listTransactions(Request $request): array
    {
        $user = Auth::user();
        $items = Transaction::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(100)
            ->get(['id','external_id','amount_usd','method','status','created_at']);

        return [
            'success' => true,
            'items' => $items->map(function($t){
                return [
                    'id' => (string)$t->id,
                    'external_id' => $t->external_id,
                    'amount_usd' => (float)$t->amount_usd,
                    'method' => (string)($t->method ?? ''),
                    'status' => (string)$t->status,
                    'created_at' => $t->created_at ? $t->created_at->toIso8601String() : null,
                ];
            }),
        ];
    }

    protected function calculateBonus(float $amount, array $bonuses): float
    {
        $bonus = 0.0;
        foreach ($bonuses as $rule) {
            $min = (float) ($rule['min'] ?? 0);
            $percent = (float) ($rule['percent'] ?? 0);
            if ($amount >= $min && $percent > 0) {
                $bonus = round($amount * $percent / 100, 2);
                break;
            }
        }
        return $bonus;
    }
}


