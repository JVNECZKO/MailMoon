<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use App\Models\ContactList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(Contact::class, 'contact');
    }

    public function index(ContactList $contactList): RedirectResponse
    {
        return redirect()->route('contact-lists.show', $contactList);
    }

    public function create(ContactList $contactList): View
    {
        return view('contacts.create', compact('contactList'));
    }

    public function store(ContactRequest $request, ContactList $contactList): RedirectResponse
    {
        $contactList->contacts()->create([
            'user_id' => $request->user()->id,
            'email' => $request->input('email'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
        ]);

        return redirect()->route('contact-lists.show', $contactList)->with('status', 'Kontakt został dodany.');
    }

    public function show(ContactList $contactList, Contact $contact): RedirectResponse
    {
        $this->ensureBelongsToList($contactList, $contact);

        return redirect()->route('contact-lists.show', $contactList);
    }

    public function edit(ContactList $contactList, Contact $contact): View
    {
        $this->ensureBelongsToList($contactList, $contact);

        return view('contacts.edit', compact('contactList', 'contact'));
    }

    public function update(ContactRequest $request, ContactList $contactList, Contact $contact): RedirectResponse
    {
        $this->ensureBelongsToList($contactList, $contact);

        $contact->update($request->validated());

        return redirect()->route('contact-lists.show', $contactList)->with('status', 'Kontakt został zaktualizowany.');
    }

    public function destroy(ContactList $contactList, Contact $contact): RedirectResponse
    {
        $this->ensureBelongsToList($contactList, $contact);

        $contact->delete();

        return redirect()->route('contact-lists.show', $contactList)->with('status', 'Kontakt został usunięty.');
    }

    private function ensureBelongsToList(ContactList $contactList, Contact $contact): void
    {
        abort_unless($contact->contact_list_id === $contactList->id, 404);
    }
}
