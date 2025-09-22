<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\System\PromoCode;
use App\Models\User;
use Illuminate\Http\Request;

class AdminPromoCodesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function browse(Request $request)
    {
        roles()->checkAccessWithAbort('promo_codes.browse');

        $items = promoCodes()->getList($request);

        return view('admin.app.promocodes.browse', compact('items'));
    }

    public function add()
    {
        roles()->checkAccessWithAbort('promo_codes.add');
        return view('admin.app.promocodes.form', ['item' => new PromoCode()]);
    }

    public function addForm()
    {
        roles()->checkAccessWithAbort('promo_codes.add');
        return view('admin.app.promocodes.add');
    }

    public function addSave(Request $request)
    {
        roles()->checkAccessWithAbort('promo_codes.add');
        $promo = promoCodes()->add($request);
        if ($request->ajax() || $request->boolean('ajax')) {
            $html = view('admin.app.promocodes.components.table-row', ['item' => $promo])->render();
            return response()->json([
                'success' => true,
                'message' => 'Промокод создан',
                'id' => $promo->id,
                'html_table_row' => $html,
            ]);
        }
        return redirect()->route('admin.promocodes.browse')->with('success', 'Промокод создан');
    }

    public function edit(PromoCode $promocode)
    {
        roles()->checkAccessWithAbort('promo_codes.edit');
        return view('admin.app.promocodes.edit', ['item' => $promocode]);
    }

    public function read(PromoCode $promocode)
    {
        roles()->checkAccessWithAbort('promo_codes.read');
        return view('admin.app.promocodes.read', ['item' => $promocode]);
    }

    public function editSave(PromoCode $promocode, Request $request)
    {
        roles()->checkAccessWithAbort('promo_codes.edit');
        $promocode = promoCodes()->edit($promocode, $request);
        if ($request->ajax() || $request->boolean('ajax')) {
            $html = view('admin.app.promocodes.components.table-row', ['item' => $promocode])->render();
            return response()->json([
                'success' => true,
                'message' => 'Промокод обновлён',
                'id' => $promocode->id,
                'html_table_row' => $html,
            ]);
        }
        return redirect()->route('admin.promocodes.browse')->with('success', 'Промокод обновлён');
    }

    public function delete(PromoCode $promocode)
    {
        roles()->checkAccessWithAbort('promo_codes.delete');
        promoCodes()->delete($promocode);
        if (request()->ajax() || request()->boolean('ajax')) {
            return response('1');
        }
        return back()->with('success', 'Удалено');
    }

    public function deactivate(PromoCode $promocode)
    {
        roles()->checkAccessWithAbort('promo_codes.edit');
        promoCodes()->deactivate($promocode);
        if (request()->ajax() || request()->boolean('ajax')) {
            return response()->json([
                'success' => true,
                'message' => 'Деактивировано',
                'data' => [ 'id' => $promocode->id ],
            ]);
        }
        return back()->with('success', 'Деактивировано');
    }

    public function activate(PromoCode $promocode)
    {
        roles()->checkAccessWithAbort('promo_codes.edit');
        promoCodes()->activate($promocode);
        if (request()->ajax() || request()->boolean('ajax')) {
            return response()->json([
                'success' => true,
                'message' => 'Активировано',
                'data' => [ 'id' => $promocode->id ],
            ]);
        }
        return back()->with('success', 'Активировано');
    }

    // Валидация и синк юзеров перенесены в PromoCodesService
}


