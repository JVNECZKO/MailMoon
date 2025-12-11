<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendingIdentityRequest;
use App\Models\SendingIdentity;
use App\Services\SmtpTestService;
use App\Services\ImapTestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SendingIdentityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(SendingIdentity::class, 'sending_identity');
    }

    public function index(Request $request): View
    {
        $sendingIdentities = $request->user()->sendingIdentities()->latest()->get();

        return view('sending-identities.index', compact('sendingIdentities'));
    }

    public function create(): View
    {
        return view('sending-identities.create');
    }

    public function store(SendingIdentityRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['is_active'] = $request->boolean('is_active');
        $data['imap_password'] = $data['imap_password'] ?? null;

        SendingIdentity::create($data);

        return redirect()->route('sending-identities.index')->with('status', 'Tożsamość nadawcy została utworzona.');
    }

    public function show(SendingIdentity $sendingIdentity): RedirectResponse
    {
        return redirect()->route('sending-identities.edit', $sendingIdentity);
    }

    public function edit(SendingIdentity $sendingIdentity): View
    {
        return view('sending-identities.edit', compact('sendingIdentity'));
    }

    public function update(SendingIdentityRequest $request, SendingIdentity $sendingIdentity): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        if (blank($data['smtp_password'])) {
            unset($data['smtp_password']);
        }
        if (blank($data['imap_password'])) {
            unset($data['imap_password']);
        }

        $sendingIdentity->update($data);

        return redirect()->route('sending-identities.index')->with('status', 'Tożsamość nadawcy została zaktualizowana.');
    }

    public function destroy(SendingIdentity $sendingIdentity): RedirectResponse
    {
        $sendingIdentity->delete();

        return redirect()->route('sending-identities.index')->with('status', 'Tożsamość nadawcy została usunięta.');
    }

    public function test(Request $request, SendingIdentity $sendingIdentity, SmtpTestService $smtpTestService, ImapTestService $imapTestService): RedirectResponse
    {
        $recipient = $request->input('recipient', $request->user()->email);

        try {
            $smtpTestService->sendTestEmail($sendingIdentity, $recipient);

            $imapResult = null;
            if ($sendingIdentity->send_mode === 'imap') {
                $imapResult = $imapTestService->appendTest($sendingIdentity);
            }

            $msg = 'Test SMTP wysłany na ' . $recipient . '.';
            if ($imapResult === true) {
                $msg .= ' Test IMAP (Sent) zakończony powodzeniem.';
            } elseif (is_string($imapResult)) {
                $msg .= ' IMAP: ' . $imapResult;
            }

            return back()->with('status', $msg);
        } catch (\Throwable $e) {
            return back()->with('error', 'Test SMTP nie powiódł się: ' . $e->getMessage());
        }
    }
}
