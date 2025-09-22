<?php

namespace App\Services;

use App\Extra\Services\Service;
use App\Extra\Services\Traits\ServiceTrait;
use App\Models\System\PromoCode;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PromoCodesService extends Service
{

    use ServiceTrait;

    /**
     * Список промокодов с фильтрами/сортировкой
     */
    public function getList(Request $request): LengthAwarePaginator
    {
        $type = (string) $request->get('type', '');
        $status = $request->get('status', '');
        $sortBy = (string) $request->get('sort_by', 'created_at');
        $sortOrder = (string) $request->get('sort_order', 'desc');

        $query = PromoCode::query()->with('users');

        if ($type !== '') {
            $query->where('type', $type);
        }

        if ($status !== '') {
            $active = in_array((string)$status, ['1', 'true', 'active'], true) ? 1 : 0;
            $query->where('active', $active);
        }

        if (!in_array($sortBy, ['created_at','bonus_amount','code','expires_at'], true)) {
            $sortBy = 'created_at';
        }

        $query->orderBy($sortBy, in_array(strtolower($sortOrder), ['asc','desc'], true) ? $sortOrder : 'desc');

        return $query->paginate(20)->appends($request->all());
    }


    /**
     * Создать промокод и привязать пользователей (для individual)
     */
    public function add(Request $request): PromoCode
    {
        $data = $this->validateData($request->all());

        /** @var PromoCode $promo */
        $promo = PromoCode::create([
            'code' => $data['code'],
            'type' => $data['type'],
            'bonus_amount' => $data['bonus_amount'],
            'expires_at' => $data['expires_at'] ?? null,
            'active' => (bool)($data['active'] ?? true),
        ]);

        $this->syncUsers($promo, $data['usernames'] ?? '');

        return $promo->load('users');
    }


    /**
     * Обновить промокод и привязки пользователей
     */
    public function edit(PromoCode $promo, Request $request): PromoCode
    {
        $data = $this->validateData($request->all(), $promo);

        $promo->code = $data['code'];
        $promo->type = $data['type'];
        $promo->bonus_amount = $data['bonus_amount'];
        $promo->expires_at = $data['expires_at'] ?? null;
        $promo->active = (bool)($data['active'] ?? true);
        $promo->save();

        $this->syncUsers($promo, $data['usernames'] ?? '');

        return $promo->load('users');
    }


    /**
     * Удалить промокод
     */
    public function delete(PromoCode $promo): ?bool
    {
        return $promo->delete();
    }


    /** Активировать */
    public function activate(PromoCode $promo): PromoCode
    {
        $promo->active = true;
        $promo->save();
        return $promo;
    }

    /** Деактивировать */
    public function deactivate(PromoCode $promo): PromoCode
    {
        $promo->active = false;
        $promo->save();
        return $promo;
    }


    /**
     * Валидация входных данных
     */
    protected function validateData(array $data, PromoCode $promo = null): array
    {
        $rules = [
            'code' => ['required','string','max:100','unique:promo_codes,code' . ($promo ? ',' . $promo->id : '')],
            'type' => ['required','in:general,individual'],
            'bonus_amount' => ['required','numeric','min:0'],
            'expires_at' => ['nullable','date'],
            'active' => ['nullable','boolean'],
            'usernames' => ['nullable','string'],
        ];

        $validated = Validator::make($data, $rules)->validate();

        return $validated;
    }


    /**
     * Привязка пользователей к индивидуальному промокоду по строке usernames (логины/емейлы через запятую)
     */
    protected function syncUsers(PromoCode $promo, string $usernamesCsv = ''): void
    {
        if ($promo->type !== 'individual') {
            $promo->users()->detach();
            return;
        }

        $usernamesCsv = trim($usernamesCsv);
        if ($usernamesCsv === '') {
            $promo->users()->detach();
            return;
        }

        $parts = array_values(array_filter(array_map(function($v){
            return trim((string)$v);
        }, explode(',', $usernamesCsv))));

        if (!count($parts)) {
            $promo->users()->detach();
            return;
        }

        $users = User::query()
            ->where(function($q) use ($parts){
                foreach ($parts as $p) {
                    $q->orWhere('login', $p)->orWhere('email', $p);
                }
            })->get();

        $promo->users()->sync($users->pluck('id')->toArray());
    }
}


