<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactImportRequest;
use App\Models\Contact;
use App\Models\ContactList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ContactImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(ContactImportRequest $request, ContactList $contactList): RedirectResponse
    {
        $this->authorize('update', $contactList);

        $rows = [];

        if ($request->hasFile('file')) {
            $rows = array_merge($rows, $this->parseFile($request->file('file')));
        }

        if ($request->filled('manual_input')) {
            $rows = array_merge($rows, $this->parseManual($request->input('manual_input')));
        }

        $summary = [
            'imported' => 0,
            'duplicates' => 0,
            'invalid' => 0,
        ];

        foreach ($rows as $row) {
            $email = strtolower(trim($row['email'] ?? ''));

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $summary['invalid']++;
                continue;
            }

            $exists = Contact::where('contact_list_id', $contactList->id)
                ->where('email', $email)
                ->exists();

            if ($exists) {
                $summary['duplicates']++;
                continue;
            }

            $contactList->contacts()->create([
                'user_id' => $request->user()->id,
                'email' => $email,
                'first_name' => $row['first_name'] ?? null,
                'last_name' => $row['last_name'] ?? null,
            ]);

            $summary['imported']++;
        }

        $message = sprintf(
            'Zaimportowano: %d, duplikaty: %d, nieprawidłowe: %d.',
            $summary['imported'],
            $summary['duplicates'],
            $summary['invalid']
        );

        return redirect()->route('contact-lists.show', $contactList)->with('status', $message);
    }

    private function parseManual(string $input): array
    {
        $rows = [];
        $lines = preg_split('/\r\n|\r|\n/', $input) ?: [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $parts = array_map('trim', str_getcsv($line));

            if (count($parts) === 0) {
                continue;
            }

            $rows[] = [
                'email' => $parts[0],
                'first_name' => $parts[1] ?? null,
                'last_name' => $parts[2] ?? null,
            ];
        }

        return $rows;
    }

    private function parseFile(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());

        if (!$path) {
            return [];
        }

        if (in_array($extension, ['csv', 'txt'], true)) {
            return $this->parseCsv(file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: []);
        }

        if (in_array($extension, ['xlsx', 'xls'], true)) {
            try {
                $spreadsheet = IOFactory::load($path);
                $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                return $this->parseCsv($sheet);
            } catch (\Throwable $e) {
                return [];
            }
        }

        return [];
    }

    private function parseCsv(array $lines): array
    {
        $rows = [];

        foreach ($lines as $line) {
            $parts = is_array($line) ? array_values($line) : str_getcsv((string) $line);
            $parts = array_map(fn ($value) => is_string($value) ? trim($value) : $value, $parts);

            if (count($parts) === 0 || ($parts[0] ?? '') === '') {
                continue;
            }

            if (strtolower((string) $parts[0]) === 'email') {
                continue;
            }

            $rows[] = [
                'email' => $parts[0],
                'first_name' => $parts[1] ?? null,
                'last_name' => $parts[2] ?? null,
            ];
        }

        return $rows;
    }
}
