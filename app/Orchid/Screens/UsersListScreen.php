<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;

class UsersListScreen extends Screen
{

    public function query(): iterable
    {
        return [
            'users' => User::paginate(15),
        ];
    }

    public function name(): ?string
    {
        return 'Пользователи';
    }

    public function description(): ?string
    {
        return 'Список всех зарегистрированных пользователей';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Создать пользователя')->icon('plus')->route('platform.users.edit'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('users', [
                TD::make('id', 'ID')->width('100'),
                TD::make('name', 'Имя'),
                TD::make('email', 'Email'),
                TD::make('is_admin', 'Админ')->width('100')->render(function ($user) {
                    return $user->is_admin ? 'Да' : 'Нет';
                }),
                TD::make('created_at', 'Создан'),
                TD::make('Действия')
                    ->align('center')
                    ->width('150')
                    ->render(function ($user) {
                        return Link::make('Редактировать')
                            ->icon('pencil')
                            ->route('platform.users.edit', $user);
                    }),
            ]),
        ];
    }
}
