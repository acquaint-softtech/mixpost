<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Inovector\Mixpost\Actions\RedirectAfterDeletedPost;
use Inovector\Mixpost\Builders\PostQuery;
use Inovector\Mixpost\Facades\Services;
use Inovector\Mixpost\Facades\Settings;
use Inovector\Mixpost\Http\Requests\StorePost;
use Inovector\Mixpost\Http\Requests\UpdatePost;
use Inovector\Mixpost\Http\Resources\AccountResource;
use Inovector\Mixpost\Http\Resources\PostResource;
use Inovector\Mixpost\Http\Resources\TagResource;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\Post;
use Inovector\Mixpost\Models\Tag;

class PostsController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection|Response
    {
        $posts = PostQuery::apply($request)->latest('id')->paginate(20)->onEachSide(1)->withQueryString();

        return Inertia::render('Posts/Index', [
            'accounts' => fn() => AccountResource::collection(Account::query()->oldest()->get())->resolve(),
            'tags' => fn() => TagResource::collection(Tag::query()->latest()->get())->resolve(),
            'filter' => [
                'keyword' => $request->query('keyword', ''),
                'status' => $request->query('status'),
                'tags' => $request->query('tags', []),
                'accounts' => $request->query('accounts', [])
            ],
            'posts' => fn() => PostResource::collection($posts)->additional([
                'filter' => [
                    'accounts' => Arr::map($request->query('accounts', []), 'intval')
                ]
            ]),
            'has_failed_posts' => Post::query()->failed()->exists()
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Posts/CreateEdit', [
            'default_accounts' => Settings::get_data('default_accounts'),
            'accounts' => AccountResource::collection(Account::query()->oldest()->get())->resolve(),
            'tags' => TagResource::collection(Tag::query()->latest()->get())->resolve(),
            'post' => null,
            'schedule_at' => [
                'date' => Str::before($request->route('schedule_at'), ' '),
                'time' => Str::after($request->route('schedule_at'), ' '),
            ],
            'has_service' => [
                'unsplash' => !! Services::get_data('unsplash', 'client_id'),
                'tenor' => !! Services::get_data('tenor', 'client_id')
            ]
        ]);
    }

    public function store(StorePost $storePost)
    {
        $post = $storePost->handle();

        return redirect()->route('mixpost.posts.edit', ['post' => $post->id]);
    }

    public function edit(Post $post): Response
    {
        $post->load('accounts', 'versions', 'tags');

        return Inertia::render('Posts/CreateEdit', [
            'accounts' => AccountResource::collection(Account::query()->oldest()->get())->resolve(),
            'tags' => TagResource::collection(Tag::query()->latest()->get())->resolve(),
            'post' => new PostResource($post),
            'has_service' => [
                'unsplash' => !! Services::get_data('unsplash', 'client_id'),
                'tenor' => !! Services::get_data('tenor', 'client_id')
            ]
        ]);
    }

    public function update(UpdatePost $updatePost): HttpResponse
    {
        $updatePost->handle();

        return response()->noContent();
    }

    public function destroy(Request $request, RedirectAfterDeletedPost $redirectAfterPostDeleted, $id): RedirectResponse
    {
        Post::query()->where('id', $id)->delete();

        return $redirectAfterPostDeleted($request);
    }
}
