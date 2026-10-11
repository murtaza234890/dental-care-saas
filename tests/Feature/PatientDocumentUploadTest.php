<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\Patient;
use App\Models\PatientDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PatientDocumentUploadTest extends TestCase
{
    use RefreshDatabase;

    private Clinic $clinic;
    private User $doctor;
    private Patient $patient;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->clinic = Clinic::create([
            'name' => 'Dental Care Studio',
            'slug' => 'dental-care-studio',
            'status' => 'active',
            'trial_ends_at' => now()->addDays(14),
        ]);

        $this->doctor = User::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Dr. Kamran Qureshi',
            'email' => 'dr.kamran@dentalstudio.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $this->patient = Patient::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Tariq Mehmood',
            'phone' => '0301-7654321',
        ]);
    }

    public function test_doctor_can_upload_xray_image_to_patient_dossier(): void
    {
        $fakeXray = UploadedFile::fake()->image('periapical_xray_16.jpg', 1200, 900);

        $response = $this->actingAs($this->doctor)->post(route('patients.documents.store', $this->patient->id), [
            'title' => 'Periapical Radiograph Tooth #16',
            'document_type' => 'xray',
            'tooth_number' => '16',
            'notes' => 'Periapical radiolucency observed with bone rarefaction.',
            'file' => $fakeXray,
        ]);

        $response->assertRedirect(route('patients.show', ['patient' => $this->patient->id, 'tab' => 'documents']));
        $response->assertSessionHas('status', 'Clinical document / X-Ray uploaded successfully.');

        $this->assertDatabaseHas('patient_documents', [
            'clinic_id' => $this->clinic->id,
            'patient_id' => $this->patient->id,
            'uploaded_by_user_id' => $this->doctor->id,
            'title' => 'Periapical Radiograph Tooth #16',
            'document_type' => 'xray',
            'tooth_number' => '16',
            'file_name' => 'periapical_xray_16.jpg',
        ]);

        $document = PatientDocument::first();
        $this->assertNotNull($document);
        Storage::disk('local')->assertExists($document->file_path);
    }

    public function test_doctor_can_upload_pdf_lab_report(): void
    {
        $fakePdf = UploadedFile::fake()->create('biopsy_report.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->doctor)->post(route('patients.documents.store', $this->patient->id), [
            'title' => 'Histopathology Biopsy Report',
            'document_type' => 'lab_report',
            'notes' => 'Benign hyperplastic gingival lesion.',
            'file' => $fakePdf,
        ]);

        $response->assertRedirect(route('patients.show', ['patient' => $this->patient->id, 'tab' => 'documents']));

        $this->assertDatabaseHas('patient_documents', [
            'clinic_id' => $this->clinic->id,
            'patient_id' => $this->patient->id,
            'title' => 'Histopathology Biopsy Report',
            'document_type' => 'lab_report',
            'mime_type' => 'application/pdf',
        ]);
    }

    public function test_upload_validation_rejects_disallowed_file_types(): void
    {
        $fakeScript = UploadedFile::fake()->create('malicious.exe', 100, 'application/octet-stream');

        $response = $this->actingAs($this->doctor)->post(route('patients.documents.store', $this->patient->id), [
            'title' => 'Test File',
            'document_type' => 'other',
            'file' => $fakeScript,
        ]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseCount('patient_documents', 0);
    }

    public function test_strict_multi_tenant_isolation_prevents_unauthorized_clinic_access(): void
    {
        // Clinic B
        $otherClinic = Clinic::create(['name' => 'Other Dental Care', 'slug' => 'other-dental']);
        $otherDoctor = User::create([
            'clinic_id' => $otherClinic->id,
            'name' => 'Dr. Alien Doctor',
            'email' => 'other@dental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        // Clinic A document
        $file = UploadedFile::fake()->image('xray.png');
        $path = $file->store('patient_documents/' . $this->clinic->id . '/' . $this->patient->id, 'local');

        $document = PatientDocument::create([
            'clinic_id' => $this->clinic->id,
            'patient_id' => $this->patient->id,
            'uploaded_by_user_id' => $this->doctor->id,
            'title' => 'Private Panoramic Radiograph',
            'document_type' => 'xray',
            'file_path' => $path,
            'file_name' => 'xray.png',
            'file_size' => 1024,
            'mime_type' => 'image/png',
        ]);

        // Other Doctor from Clinic B cannot upload to Clinic A patient (404 because patient not found in tenant scope, or 403)
        $response = $this->actingAs($otherDoctor)->post(route('patients.documents.store', $this->patient->id), [
            'title' => 'Malicious Upload',
            'document_type' => 'xray',
            'file' => UploadedFile::fake()->image('hack.png'),
        ]);
        $this->assertTrue(in_array($response->getStatusCode(), [403, 404]));

        // Other Doctor cannot view Clinic A document
        $viewResponse = $this->actingAs($otherDoctor)->get(route('patients.documents.view', [
            'patient' => $this->patient->id,
            'document' => $document->id,
        ]));
        // Should be 403 or 404 (due to global tenant scope or controller check)
        $this->assertTrue(in_array($viewResponse->getStatusCode(), [403, 404]));

        // Other Doctor cannot download Clinic A document
        $downloadResponse = $this->actingAs($otherDoctor)->get(route('patients.documents.download', [
            'patient' => $this->patient->id,
            'document' => $document->id,
        ]));
        $this->assertTrue(in_array($downloadResponse->getStatusCode(), [403, 404]));

        // Other Doctor cannot delete Clinic A document
        $deleteResponse = $this->actingAs($otherDoctor)->delete(route('patients.documents.destroy', [
            'patient' => $this->patient->id,
            'document' => $document->id,
        ]));
        $this->assertTrue(in_array($deleteResponse->getStatusCode(), [403, 404]));

        $this->assertDatabaseHas('patient_documents', ['id' => $document->id]);
    }

    public function test_doctor_can_view_and_download_patient_document(): void
    {
        $file = UploadedFile::fake()->image('smile_photo.jpg', 600, 400);
        $path = $file->store('patient_documents/' . $this->clinic->id . '/' . $this->patient->id, 'local');

        $document = PatientDocument::create([
            'clinic_id' => $this->clinic->id,
            'patient_id' => $this->patient->id,
            'uploaded_by_user_id' => $this->doctor->id,
            'title' => 'Pre-op Esthetic Smile Photo',
            'document_type' => 'photo',
            'file_path' => $path,
            'file_name' => 'smile_photo.jpg',
            'file_size' => 2048,
            'mime_type' => 'image/jpeg',
        ]);

        // Stream view
        $viewResponse = $this->actingAs($this->doctor)->get(route('patients.documents.view', [
            'patient' => $this->patient->id,
            'document' => $document->id,
        ]));
        $viewResponse->assertStatus(200);
        $viewResponse->assertHeader('Content-Type', 'image/jpeg');

        // Download
        $downloadResponse = $this->actingAs($this->doctor)->get(route('patients.documents.download', [
            'patient' => $this->patient->id,
            'document' => $document->id,
        ]));
        $downloadResponse->assertStatus(200);
    }

    public function test_doctor_can_delete_document_and_file_is_removed_from_storage(): void
    {
        $file = UploadedFile::fake()->image('old_xray.png');
        $path = $file->store('patient_documents/' . $this->clinic->id . '/' . $this->patient->id, 'local');

        $document = PatientDocument::create([
            'clinic_id' => $this->clinic->id,
            'patient_id' => $this->patient->id,
            'uploaded_by_user_id' => $this->doctor->id,
            'title' => 'Old Blurred X-Ray',
            'document_type' => 'xray',
            'file_path' => $path,
            'file_name' => 'old_xray.png',
            'file_size' => 1024,
            'mime_type' => 'image/png',
        ]);

        Storage::disk('local')->assertExists($path);

        $response = $this->actingAs($this->doctor)->delete(route('patients.documents.destroy', [
            'patient' => $this->patient->id,
            'document' => $document->id,
        ]));

        $response->assertRedirect(route('patients.show', ['patient' => $this->patient->id, 'tab' => 'documents']));
        $this->assertDatabaseMissing('patient_documents', ['id' => $document->id]);
        Storage::disk('local')->assertMissing($path);
    }
}
