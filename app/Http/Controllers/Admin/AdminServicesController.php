<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceBulkStatusRequest;
use App\Http\Requests\Admin\ServiceMarkupRequest;
use App\Http\Requests\Admin\ServiceStoreRequest;
use App\Http\Requests\Admin\ServiceUpdateRequest;
use App\Jobs\Services\SyncProviderServicesJob;
use App\Models\Provider;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceMarkup;
use App\Services\Services\ServiceMarkupsService;
use App\Services\Services\ServicesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminServicesController extends Controller
{
    public function __construct(
        private readonly ServicesService $services,
        private readonly ServiceMarkupsService $markups
    ) {
    }

    public function browse(Request $request): View
    {
        roles()->checkAccessWithAbort('services.browse');

        $filters = [
            'search' => $request->input('search'),
            'provider_id' => $request->input('provider_id'),
            'category_id' => $request->input('category_id'),
            'status' => $request->input('status'),
            'sort_by' => $request->input('sort_by', 'created_at'),
            'sort_order' => $request->input('sort_order', 'desc'),
        ];

        $services = $this->services->list($filters);

        return view('admin.app.services.browse', [
            'services' => $services,
            'filters' => $filters,
            'providers' => Provider::orderBy('name')->get(),
            'categories' => ServiceCategory::orderBy('name')->get(),
            'markups' => ServiceMarkup::with(['provider', 'category', 'service'])->orderBy('scope')->get(),
        ]);
    }

    public function add(): View
    {
        roles()->checkAccessWithAbort('services.add');

        return view('admin.app.services.add', [
            'providers' => Provider::orderBy('name')->get(),
            'categories' => ServiceCategory::orderBy('name')->get(),
        ]);
    }

    public function addSave(ServiceStoreRequest $request): RedirectResponse
    {
        $service = $this->services->createManual($request->validated());

        return redirect()->route('admin.services.read', $service)->with('success', 'Услуга добавлена.');
    }

    public function read(Service $service): View
    {
        roles()->checkAccessWithAbort('services.read');

        return view('admin.app.services.read', [
            'service' => $service->load(['provider', 'category']),
            'providers' => Provider::orderBy('name')->get(),
            'categories' => ServiceCategory::orderBy('name')->get(),
        ]);
    }

    public function edit(Service $service): View
    {
        roles()->checkAccessWithAbort('services.edit');

        return view('admin.app.services.edit', [
            'service' => $service->load(['provider', 'category']),
            'providers' => Provider::orderBy('name')->get(),
            'categories' => ServiceCategory::orderBy('name')->get(),
        ]);
    }

    public function editSave(ServiceUpdateRequest $request, Service $service): RedirectResponse
    {
        roles()->checkAccessWithAbort('services.edit');

        $this->services->updateService($service, $request->validated());

        return redirect()->route('admin.services.read', $service)->with('success', 'Услуга обновлена.');
    }

    public function bulk(ServiceBulkStatusRequest $request): RedirectResponse
    {
        roles()->checkAccessWithAbort('services.edit');

        $data = $request->validated();
        $count = $this->services->bulkSetStatus($data['ids'], $data['action'] === 'enable');

        return back()->with('success', "Изменено услуг: {$count}");
    }

    public function delete(Service $service): RedirectResponse
    {
        roles()->checkAccessWithAbort('services.delete');

        $this->services->delete($service);

        return redirect()->route('admin.services.browse')->with('success', 'Услуга удалена.');
    }

    public function syncProvider(Provider $provider): RedirectResponse
    {
        roles()->checkAccessWithAbort('services.edit');

        SyncProviderServicesJob::dispatch($provider);

        return back()->with('success', 'Синхронизация услуг поставлена в очередь.');
    }

    public function saveMarkup(ServiceMarkupRequest $request): RedirectResponse
    {
        roles()->checkAccessWithAbort('services.edit');

        $this->markups->createOrUpdate($request->validated());

        return back()->with('success', 'Наценка сохранена.');
    }

    public function deleteMarkup(ServiceMarkup $markup): RedirectResponse
    {
        roles()->checkAccessWithAbort('services.edit');

        $this->markups->delete($markup);

        return back()->with('success', 'Наценка удалена.');
    }
}
