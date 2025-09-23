<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProviderStoreRequest;
use App\Http\Requests\Admin\ProviderUpdateRequest;
use App\Models\Provider;
use App\Services\Providers\ProviderBalanceSynchronizer;
use App\Services\Providers\ProvidersService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProvidersController extends Controller
{
    public function __construct(
        private readonly ProvidersService $providers,
        private readonly ProviderBalanceSynchronizer $balanceSynchronizer
    ) {
    }

    public function browse(Request $request): View
    {
        roles()->checkAccessWithAbort('providers.browse');

        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'driver' => $request->input('driver'),
            'sort_by' => $request->input('sort_by', 'created_at'),
            'sort_order' => $request->input('sort_order', 'DESC'),
        ];

        $providers = $this->providers->get($filters);

        return view('admin.app.providers.browse', [
            'providers' => $providers,
            'availableDrivers' => array_keys(config('service-providers.drivers', [])),
            'driverLabels' => config('service-providers.labels', []),
            'filters' => $filters,
        ]);
    }

    public function add(): View
    {
        roles()->checkAccessWithAbort('providers.add');

        return view('admin.app.providers.add', [
            'availableDrivers' => array_keys(config('service-providers.drivers', [])),
            'driverLabels' => config('service-providers.labels', []),
        ]);
    }

    public function addSave(ProviderStoreRequest $request): RedirectResponse
    {
        $provider = $this->providers->add($request->validated());

        return redirect()
            ->route('admin.providers.read', $provider)
            ->with('success', 'Провайдер создан.');
    }

    public function read(Provider $provider): View
    {
        roles()->checkAccessWithAbort('providers.read');

        return view('admin.app.providers.read', [
            'provider' => $provider,
            'availableDrivers' => array_keys(config('service-providers.drivers', [])),
            'driverLabels' => config('service-providers.labels', []),
        ]);
    }

    public function edit(Provider $provider): View
    {
        roles()->checkAccessWithAbort('providers.edit');

        return view('admin.app.providers.edit', [
            'provider' => $provider,
            'availableDrivers' => array_keys(config('service-providers.drivers', [])),
            'driverLabels' => config('service-providers.labels', []),
        ]);
    }

    public function editSave(ProviderUpdateRequest $request, Provider $provider): RedirectResponse
    {
        $this->providers->edit($provider, $request->validated());

        return redirect()
            ->route('admin.providers.read', $provider)
            ->with('success', 'Данные провайдера обновлены.');
    }

    public function activate(Provider $provider): RedirectResponse
    {
        roles()->checkAccessWithAbort('providers.edit');

        $this->providers->edit($provider, ['is_active' => true]);

        return back()->with('success', 'Провайдер активирован.');
    }

    public function deactivate(Provider $provider): RedirectResponse
    {
        roles()->checkAccessWithAbort('providers.edit');

        $this->providers->edit($provider, ['is_active' => false]);

        return back()->with('success', 'Провайдер деактивирован.');
    }

    public function syncBalance(Provider $provider): RedirectResponse
    {
        roles()->checkAccessWithAbort('providers.edit');

        $this->balanceSynchronizer->sync($provider);

        return back()->with('success', 'Баланс провайдера обновлён.');
    }
}
