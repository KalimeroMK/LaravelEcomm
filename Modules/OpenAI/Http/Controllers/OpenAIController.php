<?php

declare(strict_types=1);

namespace Modules\OpenAI\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\OpenAI\Actions\GenerateTextAction;
use Modules\OpenAI\Http\Requests\OpenAIRequest;

class OpenAIController extends Controller
{
    public function __construct(
        private readonly GenerateTextAction $generateTextAction
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory
    {
        return view('openai::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory
    {
        return view('openai::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OpenAIRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $prompt = $validated['prompt'];

        $options = array_filter([
            'model' => $validated['model'] ?? null,
            'max_tokens' => $validated['max_tokens'] ?? null,
            'temperature' => $validated['temperature'] ?? null,
        ], fn ($value) => $value !== null);

        $text = $this->generateTextAction->execute($prompt, $options);

        return redirect()->back()->with('success', 'Text generated successfully!')->with('generated_text', $text);
    }

    /**
     * Show the specified resource.
     */
    public function show($id): View|Factory
    {
        return view('openai::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View|Factory
    {
        return view('openai::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): void
    {
        //
    }
}
