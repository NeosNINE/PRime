<?php

namespace App\Services\Services;

use App\Extra\Services\Service;
use App\Extra\Services\Traits\ServiceTrait;
use App\Models\Provider;
use App\Models\Service as ServiceModel;
use App\Models\ServiceCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ServicesService extends Service
{
    use ServiceTrait;

    protected ?string $model_key = 'Service';

    public function __construct(private readonly ServiceImporter $importer)
    {
    }

    public function list(array $filters): LengthAwarePaginator
    {
        $query = ServiceModel::query()
            ->with(['provider', 'category'])
            ->withCount(['orders as orders_count']);

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('external_id', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['provider_id'])) {
            $query->where('provider_id', $filters['provider_id']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('service_category_id', $filters['category_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('is_active', $filters['status'] === 'active');
        }

        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $allowedSorts = [
            'name' => 'name',
            'price' => 'price',
            'total_orders' => 'orders_count',
            'created_at' => 'created_at',
        ];

        $sortColumn = $allowedSorts[$sortField] ?? 'created_at';
        $query->orderBy($sortColumn, $sortOrder === 'asc' ? 'asc' : 'desc');

        if ($sortColumn !== 'created_at') {
            $query->orderByDesc('created_at');
        }

        return $query->paginate(25)->appends($filters);
    }

    public function createManual(array $data): ServiceModel
    {
        return DB::transaction(function () use ($data) {
            $category = $this->resolveCategory($data);

            $service = new ServiceModel();
            $service->service_category_id = $category->id;
            $service->provider_id = $data['provider_id'] ?? null;
            $service->name = $data['name'];
            $service->description = Arr::get($data, 'description');
            $service->min_quantity = (int) $data['min_quantity'];
            $service->max_quantity = (int) $data['max_quantity'];
            $service->cost_price = (float) Arr::get($data, 'cost_price', 0);
            $service->price = (float) Arr::get($data, 'price', 0);
            $service->is_active = Arr::get($data, 'is_active', true);
            $service->is_manual = true;
            $service->meta = Arr::get($data, 'meta', []);
            $service->save();

            return $service->fresh(['provider', 'category']);
        });
    }

    public function updateService(ServiceModel $service, array $data): ServiceModel
    {
        return DB::transaction(function () use ($service, $data) {
            if (!empty($data['category_id']) || !empty($data['new_category_name'])) {
                $category = $this->resolveCategory($data, $service->provider);
                $service->service_category_id = $category->id;
            }

            $service->name = $data['name'] ?? $service->name;
            $service->description = array_key_exists('description', $data) ? $data['description'] : $service->description;
            $service->min_quantity = array_key_exists('min_quantity', $data) ? (int) $data['min_quantity'] : $service->min_quantity;
            $service->max_quantity = array_key_exists('max_quantity', $data) ? (int) $data['max_quantity'] : $service->max_quantity;

            if ($service->is_manual) {
                if (array_key_exists('cost_price', $data)) {
                    $service->cost_price = (float) $data['cost_price'];
                }
                if (array_key_exists('price', $data)) {
                    $service->price = (float) $data['price'];
                }
            } elseif (array_key_exists('price', $data)) {
                $service->price = (float) $data['price'];
            }

            if (array_key_exists('is_active', $data)) {
                $service->is_active = (bool) $data['is_active'];
            }

            $service->save();

            return $service->fresh(['provider', 'category']);
        });
    }

    public function bulkSetStatus(array $ids, bool $active): int
    {
        return ServiceModel::query()->whereIn('id', $ids)->update(['is_active' => $active]);
    }

    public function delete(ServiceModel $service): void
    {
        if (!$service->is_manual) {
            throw new \RuntimeException('Удалить можно только вручную созданные услуги.');
        }

        $service->delete();
    }

    public function importForProvider(Provider $provider): void
    {
        $this->importer->import($provider);
    }

    protected function resolveCategory(array $data, ?Provider $provider = null): ServiceCategory
    {
        if (!empty($data['category_id'])) {
            return ServiceCategory::query()->findOrFail($data['category_id']);
        }

        $name = trim((string) Arr::get($data, 'new_category_name'));
        if ($name === '') {
            throw new \InvalidArgumentException('Не указана категория для услуги.');
        }

        return ServiceCategory::query()->firstOrCreate([
            'provider_id' => $provider?->id,
            'name' => $name,
        ], [
            'is_manual_only' => true,
            'is_active' => true,
        ]);
    }
}
