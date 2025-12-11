<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactListRequest;
use App\Models\ContactList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(ContactList::class, 'contact_list');
    }

    public function index(Request $request): View
    {
        $contactLists = $request->user()
            ->contactLists()
            ->withCount('contacts')
            ->latest()
            ->get();

        return view('contact-lists.index', compact('contactLists'));
    }

    public function create(): View
    {
        return view('contact-lists.create');
    }

    public function store(ContactListRequest $request): RedirectResponse
    {
        $contactList = $request->user()->contactLists()->create($request->validated());

        return redirect()->route('contact-lists.show', $contactList)->with('status', 'Lista kontaktów została utworzona.');
    }

    public function show(ContactList $contactList): View
    {
        $contacts = $contactList->contacts()->latest()->paginate(25);

        return view('contact-lists.show', compact('contactList', 'contacts'));
    }

    public function edit(ContactList $contactList): View
    {
        return view('contact-lists.edit', compact('contactList'));
    }

    public function update(ContactListRequest $request, ContactList $contactList): RedirectResponse
    {
        $contactList->update($request->validated());

        return redirect()->route('contact-lists.index')->with('status', 'Lista kontaktów została zaktualizowana.');
    }

    public function destroy(ContactList $contactList): RedirectResponse
    {
        $contactList->delete();

        return redirect()->route('contact-lists.index')->with('status', 'Lista kontaktów została usunięta.');
    }
}
