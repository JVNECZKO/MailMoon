<?php

namespace App\Http\Controllers;

use App\Http\Requests\TemplateRequest;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(Template::class, 'template');
    }

    public function index(Request $request): View
    {
        $templates = $request->user()->templates()->latest()->get();

        return view('templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('templates.create');
    }

    public function store(TemplateRequest $request): RedirectResponse
    {
        $template = $request->user()->templates()->create($request->validated());

        return redirect()->route('templates.edit', $template)->with('status', 'Szablon został utworzony.');
    }

    public function show(Template $template): RedirectResponse
    {
        return redirect()->route('templates.edit', $template);
    }

    public function edit(Template $template): View
    {
        return view('templates.edit', compact('template'));
    }

    public function update(TemplateRequest $request, Template $template): RedirectResponse
    {
        $template->update($request->validated());

        return redirect()->route('templates.index')->with('status', 'Szablon został zaktualizowany.');
    }

    public function destroy(Template $template): RedirectResponse
    {
        $template->delete();

        return redirect()->route('templates.index')->with('status', 'Szablon został usunięty.');
    }
}
