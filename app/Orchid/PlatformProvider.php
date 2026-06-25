<?php

namespace App\Orchid;

use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

class PlatformProvider extends OrchidServiceProvider
{
    public function menu(): array
    {
        return [
            Menu::make('Пользователи')
                ->icon('bs.people')
                ->route('platform.users.list')
                ->title('Управление'),

            Menu::make('Публикации')
                ->icon('bs.card-text')
                ->route('platform.posts.list'),
        ];
    }
}
