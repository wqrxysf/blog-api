<?php

namespace App\Orchid\Screens;

use App\Models\Post;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class PostsListScreen extends Screen
{
    public function name(): ?string
    {
        return 'Публикации';
    }

    public function description(): ?string
    {
        return 'Все публикации в блоге';
    }

    public function query(): iterable
    {
        return [
            'posts' => Post::with('user')->latest()->paginate(15),
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Создать публикацию')
                ->icon('plus')
                ->route('platform.posts.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('posts', [
                TD::make('id', 'ID')->width('80')->render(fn ($post) => $post->id),
                TD::make('title', 'Заголовок')->render(fn ($post) => $post->title),
                TD::make('author', 'Автор')->render(fn ($post) => $post->user->name ?? '—'),
                TD::make('created_at', 'Дата')->render(fn ($post) => $post->created_at?->format('d.m.Y H:i')),

                TD::make('actions', 'Действия')
                    ->align('center')
                    ->width('150')
                    ->render(fn ($post) => Link::make('Редактировать')
                        ->icon('pencil')
                        ->route('platform.posts.edit', $post)),
            ]),
        ];
    }
}
