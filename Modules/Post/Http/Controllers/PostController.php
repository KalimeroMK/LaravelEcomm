<?php

declare(strict_types=1);

namespace Modules\Post\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Post\Actions\CreatePostAction;
use Modules\Post\Actions\DeletePostAction;
use Modules\Post\Actions\GetAllCategoriesAction;
use Modules\Post\Actions\GetAllPostsAction;
use Modules\Post\Actions\GetAllTagsAction;
use Modules\Post\Actions\GetAllUsersAction;
use Modules\Post\Actions\UpdatePostAction;
use Modules\Post\DTOs\PostDTO;
use Modules\Post\Export\PostExport;
use Modules\Post\Http\Requests\ImportRequest;
use Modules\Post\Http\Requests\Store;
use Modules\Post\Http\Requests\Update;
use Modules\Post\Import\PostImport;
use Modules\Post\Models\Post;
use Modules\Post\Repository\PostRepository;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PostController extends Controller
{
    private readonly GetAllPostsAction $getAllAction;
    private readonly CreatePostAction $createAction;
    private readonly UpdatePostAction $updateAction;
    private readonly DeletePostAction $deleteAction;
    private readonly GetAllCategoriesAction $getAllCategoriesAction;
    private readonly GetAllTagsAction $getAllTagsAction;
    private readonly GetAllUsersAction $getAllUsersAction;
    private readonly PostRepository $postRepository;

    public function __construct(
        GetAllPostsAction $getAllAction,
        CreatePostAction $createAction,
        UpdatePostAction $updateAction,
        DeletePostAction $deleteAction,
        GetAllCategoriesAction $getAllCategoriesAction,
        GetAllTagsAction $getAllTagsAction,
        GetAllUsersAction $getAllUsersAction,
        PostRepository $postRepository
    ) {
        $this->getAllAction = $getAllAction;
        $this->createAction = $createAction;
        $this->updateAction = $updateAction;
        $this->deleteAction = $deleteAction;
        $this->getAllCategoriesAction = $getAllCategoriesAction;
        $this->getAllTagsAction = $getAllTagsAction;
        $this->getAllUsersAction = $getAllUsersAction;
        $this->postRepository = $postRepository;
        $this->authorizeResource(Post::class, 'post');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $postsDto = $this->getAllAction->execute();
        return view('post::index', ['posts' => $postsDto->posts]);
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @param  Store  $request
     * @return RedirectResponse
     */
    public function store(Store $request): RedirectResponse
    {
        $dto = PostDTO::fromRequest($request);
        $this->createAction->execute($dto);

        return redirect()->route('posts.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('post::create', [
            'categories' => $this->getAllCategoriesAction->execute(),
            'tags' => $this->getAllTagsAction->execute(),
            'users' => $this->getAllUsersAction->execute(),
            'post' => new Post,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return Application|Factory|View
     */
    public function edit(Post $post)
    {
        $categories = $this->getAllCategoriesAction->execute();
        $tags = $this->getAllTagsAction->execute();
        $users = $this->getAllUsersAction->execute();
        return view('post::edit', ['categories' => $categories, 'tags' => $tags, 'users' => $users, 'post' => $post]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Update $request, Post $post): RedirectResponse
    {
        $dto = PostDTO::fromRequest($request, $post->id);
        $this->updateAction->execute($dto);

        return redirect()->route('posts.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->deleteAction->execute($post->id);

        return redirect()->route('posts.index');
    }

    /**
     * @return BinaryFileResponse
     *
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export()
    {
        return Excel::download(new PostExport($this->postRepository), 'Products.xlsx');
    }

    /**
     * Import posts from an uploaded Excel file.
     */
    public function import(ImportRequest $request): RedirectResponse
    {
        // Ensure there is a file and it is not an array of files
        $file = $request->file('file');
        if (!$file) {
            return back()->withErrors(['error' => 'Please upload a file.']);
        }

        if (is_array($file)) {
            return back()->withErrors(['error' => 'Please upload only one file.']);
        }

        try {
            Excel::import(new PostImport, $file);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An error occurred during import: '.$e->getMessage()]);
        }

        return redirect()->back()->with('success', 'Posts imported successfully.');
    }


    public function deleteMedia(int $modelId, int $mediaId): RedirectResponse
    {
        $model = Post::findOrFail($modelId);
        $model->media()->where('id', $mediaId)->first()->delete();

        return back()->with('success', 'Media deleted successfully.');
    }
}
