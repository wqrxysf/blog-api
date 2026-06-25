<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;

class UsersEditScreen extends Screen
{

    public function query(User $user = null): iterable
    {
        return [
            'user' => $user,
        ];
    }

    public function name(): ?string
    {
        return $this->user?->exists ? 'Редактирование пользователя' : 'Создание пользователя';
    }

    public function description(): ?string
    {
        return 'Основные данные пользователя';
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Сохранить')
                ->icon('check')
                ->method('save')
                ->type(Color::SUCCESS),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('user.name')
                    ->title('Имя')
                    ->placeholder('Введите имя')
                    ->required(),

                Input::make('user.email')
                    ->title('Email')
                    ->type('email')
                    ->placeholder('Введите email')
                    ->required(),

                Input::make('user.password')
                    ->title('Пароль')
                    ->type('password')
                    ->placeholder('Оставьте пустым, если не меняете'),

                Select::make('user.is_admin')
                    ->title('Роль')
                    ->options([
                        false => 'Пользователь',
                        true => 'Администратор',
                    ])
                    ->required(),
            ]),
        ];
    }

    public function save(Request $request)
    {
        $request->validate([
            'user.name' => 'required|string|max:255',
            'user.email' => 'required|email|unique:users,email,' . ($this->user?->id ?? 'NULL'),
            'user.password' => 'nullable|string|min:6',
            'user.is_admin' => 'required|boolean',
        ]);

        $user = User::updateOrCreate(
            ['id' => $this->user?->id],
            [
                'name' => $request->input('user.name'),
                'email' => $request->input('user.email'),
                'is_admin' => $request->input('user.is_admin'),
            ]
        );

        // Обновляем пароль только если он указан
        if ($request->filled('user.password')) {
            $user->password = bcrypt($request->input('user.password'));
            $user->save();
        }

        Alert::info('Пользователь успешно сохранен');

        return redirect()->route('platform.users.list');
    }
}
