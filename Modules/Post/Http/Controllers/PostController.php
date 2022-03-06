<?php

    namespace Modules\Post\Http\Controllers;

    use App\Http\Controllers\Controller;
    use App\Traits\ImageUpload;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Contracts\View\Factory;
    use Illuminate\Contracts\View\View;
    use Illuminate\Http\RedirectResponse;
    use Modules\Category\Models\Category;
    use Modules\Post\Http\Requests\PostCategoryStore;
    use Modules\Post\Http\Requests\Update;
    use Modules\Post\Models\Post;
    use Modules\Tag\Models\Tag;
    use Modules\User\Models\User;

    class PostController extends Controller
    {
        public function __construct()
        {
            $this->middleware('permission:post-list');
            $this->middleware('permission:post-create', ['only' => ['create', 'store']]);
            $this->middleware('permission:post-edit', ['only' => ['edit', 'update']]);
            $this->middleware('permission:post-delete', ['only' => ['destroy']]);
        }

        use ImageUpload;

        /**
         * Display a listing of the resource.
         *
         * @return Application|Factory|View
         */
        public function index()
        {
            $posts = Post::getAllPost();

            return view('post::index', compact('posts'));
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return Application|Factory|View
         */
        public function create()
        {
            $categories = Category::get();
            $tags       = Tag::get();
            $users      = User::get();

            return view('post::create', compact('users', 'categories', 'tags'));
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param  PostCategoryStore  $request
         *
         * @return RedirectResponse
         */
        public function store(PostCategoryStore $request): RedirectResponse
        {
            $post = Post::create(
                $request->except('photo') + [
                    'photo' => $this->verifyAndStoreImage($request),
                ]
            );
            $post->categories()->attach($request['category']);
            $post->post_tag()->attach($request['tags']);

            request()->session()->flash('success', 'Post Successfully added');

            return redirect()->route('posts.index');
        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param  Post  $post
         *
         * @return Application|Factory|View
         */
        public function edit(Post $post)
        {
            $categories = Category::get();
            $tags       = Tag::get();
            $users      = User::get();

            return view('post::edit', compact('post', 'categories', 'tags', 'users'));
        }

        /**
         * Update the specified resource in storage.
         *
         * @param  Update  $request
         * @param  Post  $post
         *
         * @return RedirectResponse
         */
        public function update(Update $request, Post $post): RedirectResponse
        {
            if ($request->hasFile('photo')) {
                $status = $post->update(
                    $request->except('photo') + [
                        'photo' => $this->verifyAndStoreImage($request),
                    ]
                );
            } else {
                $status = $post->update($request->all());
            }
            $post->post_tag()->sync($request['tags'], true);
            $post->categories()->sync($request['category'], true);

            if ($status) {
                request()->session()->flash('success', 'Post Successfully updated');
            } else {
                request()->session()->flash('error', 'Please try again!!');
            }

            return redirect()->route('post.index');
        }

        /**
         * Remove the specified resource from storage.
         *
         * @param  Post  $post
         *
         * @return RedirectResponse
         */
        public function destroy(Post $post): RedirectResponse
        {
            $status = $post->delete();

            if ($status) {
                request()->session()->flash('success', 'Post successfully deleted');
            } else {
                request()->session()->flash('error', 'Error while deleting post ');
            }

            return redirect()->route('post.index');
        }

        /**
         * Make paths for storing images.
         *
         * @return object
         */
        public function makePaths(): object
        {
            $original  = public_path().'/uploads/images/post/';
            $thumbnail = public_path().'/uploads/images/post/thumbnails/';
            $medium    = public_path().'/uploads/images/post/medium/';

            return (object)compact('original', 'thumbnail', 'medium');
        }
    }
