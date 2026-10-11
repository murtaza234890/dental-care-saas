<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PatientDocumentController extends Controller
{
    /**
     * Upload and store a new clinical document, photo, or X-ray.
     */
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $user = Auth::user();

        // Enforce multi-tenant access
        abort_unless($patient->clinic_id === $user->clinic_id, 403, 'Unauthorized clinic action.');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'document_type' => ['required', 'in:xray,lab_report,photo,prescription,other'],
            'tooth_number' => ['nullable', 'string', 'max:10'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'file' => [
                'required',
                'file',
                'max:15360', // 15MB maximum
                'mimes:jpeg,jpg,png,webp,pdf',
            ],
        ]);

        $uploadedFile = $request->file('file');
        $directory = 'patient_documents/' . $patient->clinic_id . '/' . $patient->id;
        $path = $uploadedFile->store($directory, 'local');

        PatientDocument::create([
            'clinic_id' => $patient->clinic_id,
            'patient_id' => $patient->id,
            'uploaded_by_user_id' => $user->id,
            'title' => $validated['title'],
            'document_type' => $validated['document_type'],
            'tooth_number' => $validated['tooth_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'file_path' => $path,
            'file_name' => $uploadedFile->getClientOriginalName(),
            'file_size' => $uploadedFile->getSize(),
            'mime_type' => $uploadedFile->getMimeType(),
        ]);

        return redirect()->route('patients.show', ['patient' => $patient->id, 'tab' => 'documents'])
            ->with('status', 'Clinical document / X-Ray uploaded successfully.');
    }

    /**
     * Stream document inline for lightbox / browser preview (image or PDF).
     */
    public function view(Patient $patient, PatientDocument $document): BinaryFileResponse
    {
        $user = Auth::user();

        // Multi-tenant and patient isolation
        abort_unless($document->clinic_id === $user->clinic_id, 403, 'Unauthorized document access.');
        abort_unless($document->patient_id === $patient->id, 404, 'Document does not belong to this patient.');
        abort_unless(Storage::disk('local')->exists($document->file_path), 404, 'File not found on storage disk.');

        return response()->file(Storage::disk('local')->path($document->file_path), [
            'Content-Type' => $document->mime_type,
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * Download document file with original client filename.
     */
    public function download(Patient $patient, PatientDocument $document): StreamedResponse
    {
        $user = Auth::user();

        abort_unless($document->clinic_id === $user->clinic_id, 403, 'Unauthorized document access.');
        abort_unless($document->patient_id === $patient->id, 404, 'Document does not belong to this patient.');
        abort_unless(Storage::disk('local')->exists($document->file_path), 404, 'File not found on storage disk.');

        return Storage::disk('local')->download($document->file_path, $document->file_name);
    }

    /**
     * Delete document and remove physical file from disk.
     */
    public function destroy(Patient $patient, PatientDocument $document): RedirectResponse
    {
        $user = Auth::user();

        abort_unless($document->clinic_id === $user->clinic_id, 403, 'Unauthorized document deletion.');
        abort_unless($document->patient_id === $patient->id, 404, 'Document does not belong to this patient.');

        // Remove from storage disk
        if (Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('patients.show', ['patient' => $patient->id, 'tab' => 'documents'])
            ->with('status', 'Clinical document removed successfully.');
    }
}
