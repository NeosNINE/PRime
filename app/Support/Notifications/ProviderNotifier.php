<?php

namespace App\Support\Notifications;

use App\Models\Provider;
use App\Models\User;
use Illuminate\Support\Collection;

class ProviderNotifier
{
    public function notifyLowBalance(Provider $provider): void
    {
        $title = 'Низкий баланс провайдера';
        $message = sprintf(
            'Баланс провайдера "%s" опустился до %s %s. Проверьте пополнение.',
            $provider->name,
            number_format((float) $provider->balance, 2, '.', ' '),
            $provider->currency
        );

        $recipients = $this->recipients();

        foreach ($recipients as $user) {
            notifyUser(
                $user,
                $title,
                $message,
                route('admin.providers.read', $provider),
                'fas fa-coins',
                'warning'
            );
        }
    }

    private function recipients(): Collection
    {
        $adminRoleKeys = roles()->getAllAccessKeys();

        return User::query()
            ->whereHas('roles', fn ($query) => $query->whereIn('key', $adminRoleKeys))
            ->get();
    }
}
