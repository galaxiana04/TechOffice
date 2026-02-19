<?php

namespace Tests\Feature;


use Tests\TestCase;
use App\Models\User; // jika pakai login
use App\Models\Unit;
use App\Models\JobticketDocumentKind;
use App\Models\Jobticket;
use App\Models\JobticketIdentity;
use App\Models\ProjectType;

class JobticketControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    public function test_berhasil_menyimpan_jobticket_document_kind()
    {
        // Buat user dengan data lengkap, termasuk username
        $user = User::factory()->create([
            'username' => 'testuser123',
        ]);
        $this->actingAs($user);

        $data = [
            'name' => 'Dokumen Gambar ' . uniqid(), // hindari duplikat
            'description' => 'Untuk gambar teknis',
        ];

        $this->postJson('/jobticket/jobticket-document-kind', $data)
            ->assertStatus(201)
            ->assertJsonFragment(['message' => 'Jobticket Document Kind created successfully!']);
    }

    public function test_gagal_menyimpan_jobticket_document_kind_karena_data_tidak_lengkap()
    {
        $user = User::factory()->create([
            'username' => 'testuser123',
        ]);
        $this->actingAs($user);

        $data = [
            'name' => '', // kosong, pasti gagal validasi
            'description' => 'Untuk gambar teknis',
        ];

        $this->postJson('/jobticket/jobticket-document-kind', $data)
            ->assertStatus(422) // atau sesuai response validasi error di controller
            ->assertJsonValidationErrors('name');
    }

    public function test_berhasil_menambahkan_dokumen_jobticket()
    {
        // Buat user dengan data lengkap termasuk 'username'
        $user = User::factory()->create([
            'username' => 'testuser123',
        ]);
        $this->actingAs($user);

        // Buat dulu unit dan proyek_type yang valid supaya foreign key valid
        $unit = Unit::firstOrCreate(['id' => 14], ['name' => 'QE']);
        $proyekType = ProjectType::factory()->create();

        // Buat jobticket_documentkind agar valid
        $documentKind = JobticketDocumentKind::factory()->create();



        // Data request yang akan dikirim
        $data = [
            'unit_id' => $unit->id,
            'proyek_type_id' => $proyekType->id,
            'jobticket_documentkind_id' => $documentKind->id,
            'documentnumber' => 'DOC-001',
            'rev' => '0',
            'documentname' => 'Dokumen Tes',
            'drafter' => null,
            'checker' => null,
        ];

        // Kirim POST request ke route yang diarahkan ke AddDocument
        $response = $this->postJson('/jobticket/jobticket-add-document', $data);

        // Assert status 200 dan pesan sukses
        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Dokumen berhasil ditambahkan! Tunggu konfirmasi Manager.',
            ]);

        // Assert data jobticket benar-benar tersimpan di DB
        $this->assertDatabaseHas('jobticket', [
            'documentname' => 'Dokumen Tes',
            'rev' => '0',
            'inputer_id' => $user->id,
        ]);
    }

    public function test_gagal_menambahkan_dokumen_jobticket_karena_data_tidak_lengkap()
    {
        // Buat user dengan data lengkap termasuk 'username'
        $user = User::factory()->create([
            'username' => 'testuser123',
        ]);
        $this->actingAs($user);

        // Data request yang tidak lengkap
        $data = [
            'unit_id' => null, // unit_id tidak boleh null
            'proyek_type_id' => 1, // asumsikan ini valid
            'jobticket_documentkind_id' => 1, // asumsikan ini valid
            'documentnumber' => 'DOC-001',
            'rev' => '0',
            'documentname' => '', // documentname harus diisi
            'drafter' => null,
            'checker' => null,
        ];

        // Kirim POST request ke route yang diarahkan ke AddDocument
        $response = $this->postJson('/jobticket/jobticket-add-document', $data);

        // Assert status 422 dan validasi error
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['unit_id', 'documentname']);
    }

    public function test_berhasil_merilis_dokumen_jobticket()
    {
        // Buat user dan login
        $user = \App\Models\User::factory()->create([
            'username' => 'testuser123',
        ]);
        $this->actingAs($user);

        // Buat jobticket dummy
        $identity = JobticketIdentity::factory()->create();

        $jobticket = Jobticket::factory()->create([
            'jobticket_identity_id' => $identity->id,
        ]);

        // Panggil endpoint releasedDocument
        $response = $this->postJson('/jobticket/jobticket-released/' . $jobticket->id);

        // Assert response sukses
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Dokumen berhasil dirilis! Tunggu konfirmasi Manager.',
            ]);

        // Assert status di database berubah
        $this->assertDatabaseHas('jobticket', [
            'id' => $jobticket->id,
            'publicstatus' => 'released',
        ]);
    }

    public function test_gagal_merilis_dokumen_jobticket_id_tidak_ditemukan()
    {
        $user = \App\Models\User::factory()->create([
            'username' => 'testuser123',
        ]);
        $this->actingAs($user);

        // ID yang tidak ada
        $invalidId = 999999;

        $response = $this->postJson('/jobticket/jobticket-released/' .  $invalidId);

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Terjadi kesalahan saat merilis dokumen.',
            ]);
    }
}
