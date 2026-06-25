<?php

namespace App\Orchid\Screens;

use App\Models\User;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class UsersListScreen extends Screen
{
    public function name(): ?string
    {
        return 'Пользователи';
    }

    public function description(): ?string
    {
        return 'Список всех зарегистрированных пользователей';
    }

    public function query(): iterable
    {
        return [
            'users' => User::latest()->paginate(15),
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Создать пользователя')
                ->icon('plus')
                ->route('platform.users.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('users', [
                TD::make('id', 'ID')->width('80')->render(fn ($user) => $user->id),
                TD::make('name', 'Имя')->render(fn ($user) => $user->name),
                TD::make('email', 'Email')->render(fn ($user) => $user->email),
                TD::make('is_admin', 'Админ')->width('100')->render(fn ($user) => $user->is_admin ? 'Да' : 'Нет'),
                TD::make('created_at', 'Дата')->render(fn ($user) => $user->created_at?->format('d.m.Y H:i')),

                TD::make('actions', 'Действия')
                    ->align('center')
                    ->width('150')
                    ->render(fn ($user) => Link::make('Редактировать')
                        ->icon('pencil')
                        ->route('platform.users.edit', $user)),
            ]),
        ];
    }
}
