<?php

namespace Modules\Post\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Category\Models\Category;
use Modules\Post\Export\Posts as PostExport;
use Modules\Post\Http\Requests\ImportRequest;
use Modules\Post\Http\Requests\Store;
use Modules\Post\Http\Requests\Update;
use Modules\Post\Models\Post;
use Modules\Post\Service\PostService;
use Modules\Tag\Models\Tag;
use Modules\User\Models\User;
use PhpOffice\PhpSpreadsheet\Exception;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PostController extends Controller
{
    private PostService $post_service;

    public function __construct(PostService $post_service)
    {
        $this->post_service = $post_service;
        $this->authorizeResource(Post::class, 'post');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('post::index', ['posts' => $this->post_service->getAll()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function store(Store $request): RedirectResponse
    {
        $post = $this->post_service->create($request->validated());

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
            'categories' => Category::all(),
            'tags' => Tag::all(),
            'users' => User::all(),
            'post' => new Post(),
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
        $categories = Category::all();
        $tags = Tag::all();
        $users = User::all();
        $post = $this->post_service->findById($post->id);

        return view('post::edit', compact('categories', 'tags', 'users', 'post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Update $request, Post $post): RedirectResponse
    {
        $this->post_service->update($post->id, $request->validated());

        return redirect()->route('posts.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $this->post_service->delete($post->id);

        return redirect()->back();
    }

    /**
     * @return BinaryFileResponse
     *
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export()
    {
        return Excel::download(new PostExport, 'Products.xlsx');
    }

    /**
     * Import posts from an uploaded Excel file.
     *
     * @param  ImportRequest  $request
     * @return RedirectResponse
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
            Excel::import(new PostExport(), $file);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An error occurred during import: '.$e->getMessage()]);
        }

        return redirect()->back()->with('success', 'Posts imported successfully.');
    }


    /**
     * @return void
     */
    public function upload(Request $request)
    {
        $this->post_service->upload($request);
    }

    public function deleteMedia(int $modelId, int $mediaId): RedirectResponse
    {
        $model = Post::findOrFail($modelId);
        $model->media()->where('id', $mediaId)->first()->delete();

        return back()->with('success', 'Media deleted successfully.');
    }
}
