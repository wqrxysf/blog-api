<?php

namespace App\Orchid\Screens;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Illuminate\Support\Facades\Route;

class UsersEditScreen extends Screen
{
    public ?User $user = null;

    public function name(): ?string
    {
        return $this->user?->exists ? 'Редактирование пользователя' : 'Создание пользователя';
    }

    public function description(): ?string
    {
        return 'Управление данными пользователя';
    }

    public function query(User $user = null): iterable
    {
        $this->user = $user;

        return [
            'user' => $user ?? new User(),
        ];
    }


    public function registerRoutes(): void
    {
        Route::match(['get', 'post'], '/users/{user?}', [self::class, 'handle'])
            ->name('platform.users.edit');
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Сохранить')
                ->icon('check')
                ->method('save')
                ->type(Color::SUCCESS),

            Button::make('Удалить')
                ->icon('trash')
                ->method('remove')
                ->type(Color::DANGER)
                ->confirm('Вы уверены, что хотите удалить этого пользователя?')
                ->canSee($this->user?->exists),
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
                    ->placeholder($this->user?->exists ? 'Оставьте пустым, если не меняете' : 'Введите пароль')
                    ->required(!$this->user?->exists),

                Select::make('user.is_admin')
                    ->title('Роль')
                    ->options([
                        0 => 'Пользователь',
                        1 => 'Администратор',
                    ])
                    ->required(),
            ]),
        ];
    }

    public function save(Request $request)
    {
        $data = $request->input('user', []);

        $request->validate([
            'user.name' => 'required|string|max:255',
            'user.email' => 'required|email|max:255|unique:users,email,' . ($this->user?->id ?? 'NULL'),
            'user.password' => ($this->user?->exists ? 'nullable' : 'required') . '|string|min:6',
            'user.is_admin' => 'required',
        ]);

        if ($this->user?->exists) {
            $this->user->name = $data['name'];
            $this->user->email = $data['email'];
            $this->user->is_admin = (bool) $data['is_admin'];

            if (!empty($data['password'])) {
                $this->user->password = Hash::make($data['password']);
            }

            $this->user->save();
            Alert::info('Пользователь успешно обновлен');
        } else {
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_admin' => (bool) $data['is_admin'],
            ]);
            Alert::info('Пользователь успешно создан');
        }

        return redirect()->route('platform.users.list');
    }

    public function remove()
    {
        if ($this->user?->exists) {
            $this->user->delete();
            Alert::info('Пользователь успешно удален');
        }

        return redirect()->route('platform.users.list');
    }
}
