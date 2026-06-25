<?php

namespace App\Orchid\Screens;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Illuminate\Support\Facades\Route;

class PostsEditScreen extends Screen
{
    public ?Post $post = null;

    public function name(): ?string
    {
        return $this->post?->exists ? 'Редактирование публикации' : 'Создание публикации';
    }

    public function description(): ?string
    {
        return 'Управление публикацией';
    }

    public function query(Post $post = null): iterable
    {
        $this->post = $post;

        return [
            'post' => $post ?? new Post(),
            'users' => User::pluck('name', 'id')->toArray(),
        ];
    }

    public function registerRoutes(): void
    {
        Route::match(['get', 'post'], '/posts/{post?}', [self::class, 'handle'])
            ->name('platform.posts.edit');
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
                ->confirm('Вы уверены, что хотите удалить эту публикацию?')
                ->canSee($this->post?->exists),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('post.title')
                    ->title('Заголовок')
                    ->placeholder('Введите заголовок')
                    ->required(),

                TextArea::make('post.text')
                    ->title('Текст')
                    ->placeholder('Введите текст публикации')
                    ->rows(10)
                    ->required(),

                Select::make('post.user_id')
                    ->title('Автор')
                    ->options(User::pluck('name', 'id')->toArray())
                    ->required(),
            ]),
        ];
    }

    public function save(Request $request)
    {
        $data = $request->input('post', []);

        $request->validate([
            'post.title' => 'required|string|max:255',
            'post.text' => 'required|string',
            'post.user_id' => 'required|exists:users,id',
        ]);

        if ($this->post?->exists) {
            $this->post->title = $data['title'];
            $this->post->text = $data['text'];
            $this->post->user_id = $data['user_id'];
            $this->post->save();
            Alert::info('Публикация успешно обновлена');
        } else {
            Post::create([
                'title' => $data['title'],
                'text' => $data['text'],
                'user_id' => $data['user_id'],
            ]);
            Alert::info('Публикация успешно создана');
        }

        return redirect()->route('platform.posts.list');
    }

    public function remove()
    {
        if ($this->post?->exists) {
            $this->post->delete();
            Alert::info('Публикация успешно удалена');
        }

        return redirect()->route('platform.posts.list');
    }
}
