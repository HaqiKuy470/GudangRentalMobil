<?php

namespace Tests\Feature;

use App\Enums\StatusPesanan;
use App\Models\Mobil;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use App\Models\User;
use App\Services\BiayaService;
use App\Services\PesananService;
use App\Services\WhatsAppLinkBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RentalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 11, 9)->setTime(10, 0));
        Cache::flush();
    }

    private function owner(): User
    {
        $owner = User::factory()->create();
        $this->actingAs($owner);

        return $owner;
    }

    private function payload(?Mobil $mobil = null): array
    {
        return ['mobil_id' => ($mobil ?? Mobil::factory()->create())->id, 'pelanggan_id' => Pelanggan::factory()->create()->id,
            'jenis_sewa' => 'lepas_kunci', 'tgl_ambil' => '2026-11-10', 'tgl_kembali' => '2026-11-12'];
    }

    private function order(array $extra = []): Pesanan
    {
        return app(PesananService::class)->simpan(array_replace($this->payload(), $extra));
    }

    /** SKPL-F01–F07, NF05: Halaman, filter, dan data kosong. */
    public function test_public_pages_and_catalog_filters_render(): void
    {
        $car = Mobil::factory()->create(['model' => 'Avanza', 'harga_per_hari' => 350000]);
        $excluded = Mobil::factory()->create(['model' => 'Innova', 'harga_per_hari' => 550000, 'transmisi' => 'otomatis']);
        foreach (['/', '/mobil', '/tentang', '/login', '/mobil/'.$car->id] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/mobil?harga_max=400000&transmisi=manual')->assertOk()
            ->assertSee('href="'.route('katalog.show', $car).'"', false)
            ->assertDontSee('href="'.route('katalog.show', $excluded).'"', false);
        $this->get('/mobil?q=TidakAdaMobil')->assertOk()->assertSee('Belum ada mobil yang cocok');
        $this->get('/mobil/99999')->assertNotFound();
        $this->get('/register')->assertNotFound();
    }

    /** SKPL-F08, NF07: Halaman Owner wajib login. */
    public function test_owner_pages_require_login(): void
    {
        foreach (['/owner', '/owner/mobil', '/owner/pelanggan', '/owner/pesanan', '/owner/laporan', '/owner/laporan/pdf', '/owner/pengaturan'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
    }

    public function test_owner_pages_render_with_real_order_data(): void
    {
        $this->owner();
        $order = $this->order();
        foreach (['/owner', '/owner/mobil', '/owner/mobil/create', '/owner/mobil/'.$order->mobil_id.'/edit', '/owner/pelanggan', '/owner/pelanggan/create', '/owner/pelanggan/'.$order->pelanggan_id, '/owner/pelanggan/'.$order->pelanggan_id.'/edit', '/owner/pesanan', '/owner/pesanan/create', '/owner/pesanan/'.$order->id, '/owner/pesanan/'.$order->id.'/edit', '/owner/pengaturan', '/owner/laporan'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    /** SKPL-F05/F14: Tanggal inklusif dan biaya dalam rupiah bulat. */
    public function test_inclusive_rental_cost_and_driver_cost(): void
    {
        $data = $this->payload();
        $data['jenis_sewa'] = 'dengan_sopir';
        $order = app(PesananService::class)->simpan($data);
        $this->assertSame(3, $order->lama_sewa);
        $this->assertSame(450000, $order->biaya_sopir);
        $this->assertSame(1500000, $order->total_biaya);
        $this->assertSame(1, app(BiayaService::class)->lamaSewa(today(), today()));
        $this->assertMatchesRegularExpression('/^GRM-2611-\d{4}$/', $order->kode);
    }

    /** SKPL-F15: Tanggal bersinggungan bentrok; hari berikutnya tersedia. */
    public function test_overlapping_booking_rejected_and_next_day_accepted(): void
    {
        $this->owner();
        $data = $this->payload();
        app(PesananService::class)->simpan($data);
        $this->post(route('owner.pesanan.store'), array_replace($data, ['tgl_ambil' => '2026-11-12', 'tgl_kembali' => '2026-11-14']))->assertSessionHasErrors('tgl_ambil');
        $this->assertDatabaseCount('pesanan', 1);
        $this->post(route('owner.pesanan.store'), array_replace($data, ['tgl_ambil' => '2026-11-13', 'tgl_kembali' => '2026-11-14']))->assertRedirect();
        $this->assertDatabaseCount('pesanan', 2);
    }

    public function test_maintenance_vehicle_cannot_be_booked(): void
    {
        $this->owner();
        $car = Mobil::factory()->create(['status' => 'perawatan']);
        $this->post(route('owner.pesanan.store'), $this->payload($car))->assertSessionHasErrors('tgl_ambil');
        $this->getJson(route('katalog.estimasi', [$car, 'tgl_ambil' => '2026-11-10', 'tgl_kembali' => '2026-11-11']))->assertOk()->assertJsonPath('tersedia', false)->assertJsonPath('wa', null);
    }

    /** SKPL-F16/F18: Perubahan status dan denda keterlambatan. */
    public function test_order_transitions_and_fine_recalculate_using_snapshot(): void
    {
        $this->owner();
        $order = $this->order(['tgl_ambil' => '2026-11-05', 'tgl_kembali' => '2026-11-07']);
        $order->mobil->update(['harga_per_hari' => 900000]);
        $this->patch(route('owner.pesanan.status', $order), ['status' => 'selesai', 'tgl_dikembalikan' => '2026-11-09'])->assertSessionHasErrors('status');
        $this->patch(route('owner.pesanan.status', $order), ['status' => 'disewa'])->assertSessionHasNoErrors();
        $this->patch(route('owner.pesanan.status', $order), ['status' => 'selesai', 'tgl_dikembalikan' => '2026-11-09', 'denda' => 100000])->assertSessionHasNoErrors();
        $this->assertSame(1150000, $order->fresh()->total_biaya);
        $this->assertSame(StatusPesanan::Selesai, $order->fresh()->status);
        $this->patch(route('owner.pesanan.status', $order), ['status' => 'disewa'])->assertSessionHasErrors('status');
    }

    public function test_cancellation_releases_dates_and_cannot_be_reopened(): void
    {
        $this->owner();
        $data = $this->payload();
        $order = app(PesananService::class)->simpan($data);
        $this->patch(route('owner.pesanan.status', $order), ['status' => 'batal'])->assertSessionHasNoErrors();
        $this->post(route('owner.pesanan.store'), $data)->assertSessionHasNoErrors();
        $this->assertDatabaseCount('pesanan', 2);
        $this->patch(route('owner.pesanan.status', $order), ['status' => 'dipesan'])->assertSessionHasErrors('status');
    }

    public function test_existing_order_can_be_edited_without_conflicting_with_itself_and_keeps_price(): void
    {
        $this->owner();
        $data = $this->payload();
        $order = app(PesananService::class)->simpan($data);
        $order->mobil->update(['harga_per_hari' => 800000]);
        $this->put(route('owner.pesanan.update', $order), array_replace($data, ['tgl_kembali' => '2026-11-13']))->assertSessionHasNoErrors();
        $this->assertSame(1400000, $order->fresh()->total_biaya);
    }

    /** SKPL-F12/F13: Penghapusan mobil dan pelanggan tetap menjaga riwayat. */
    public function test_active_car_and_customer_with_history_cannot_be_deleted(): void
    {
        $this->owner();
        $order = $this->order();
        $this->delete(route('owner.mobil.destroy', $order->mobil_id))->assertSessionHasErrors('mobil');
        $this->delete(route('owner.pelanggan.destroy', $order->pelanggan_id))->assertSessionHasErrors('pelanggan');
        $this->assertNotNull($order->mobil->fresh());
    }

    public function test_car_soft_deletion_preserves_order_history(): void
    {
        $this->owner();
        $order = $this->order();
        $order->update(['status' => 'selesai']);
        $this->delete(route('owner.mobil.destroy', $order->mobil_id))->assertSessionHasNoErrors();
        $this->assertSoftDeleted('mobil', ['id' => $order->mobil_id]);
        $this->get(route('owner.pesanan.show', $order))->assertOk()->assertSee('Avanza');
    }

    /** SKPL-F17: Pembayaran manual, sisa tagihan, dan penghapusan. */
    public function test_payments_update_balance_and_invalid_amount_is_rejected(): void
    {
        $this->owner();
        $order = $this->order();
        $data = ['jenis' => 'dp', 'metode' => 'transfer', 'tgl_bayar' => '2026-11-09', 'jumlah' => 300000];
        $this->post(route('owner.pembayaran.store', $order), $data)->assertSessionHasNoErrors();
        $this->assertSame(750000, $order->fresh()->sisaTagihan());
        $this->assertSame('DP', $order->fresh()->statusBayar());
        $this->post(route('owner.pembayaran.store', $order), array_replace($data, ['jumlah' => 0]))->assertSessionHasErrors('jumlah');
        $this->post(route('owner.pembayaran.store', $order), array_replace($data, ['jenis' => 'pelunasan', 'jumlah' => 750000]))->assertSessionHasNoErrors();
        $this->assertSame('Lunas', $order->fresh()->statusBayar());
        $this->delete(route('owner.pembayaran.destroy', $order->pembayaran()->first()))->assertSessionHasNoErrors();
        $this->assertSame(300000, $order->fresh()->sisaTagihan());
    }

    /** SKPL-F06: Isi pesan WhatsApp dan ketersediaan mobil. */
    public function test_whatsapp_message_contains_booking_details_and_is_url_encoded(): void
    {
        Pengaturan::nilai()->update(['no_whatsapp' => '6281234567890']);
        $car = Mobil::factory()->create();
        $response = $this->getJson(route('katalog.estimasi', [$car, 'tgl_ambil' => '2026-11-10', 'tgl_kembali' => '2026-11-12', 'jenis_sewa' => 'dengan_sopir']))->assertOk()->assertJsonPath('tersedia', true)->assertJsonPath('total', 'Rp1.500.000');
        $url = $response->json('wa');
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $url);
        $this->assertStringContainsString('%0A', $url);
        $this->assertStringContainsString($car->plat_nomor, rawurldecode($url));
        $this->assertStringContainsString('Dengan Sopir', rawurldecode($url));
        app(PesananService::class)->simpan($this->payload($car));
        $this->getJson(route('katalog.estimasi', [$car, 'tgl_ambil' => '2026-11-10', 'tgl_kembali' => '2026-11-12']))->assertJsonPath('wa', null)->assertJsonPath('tersedia', false);
    }

    public function test_public_dates_reject_past_and_reversed_or_missing_dates(): void
    {
        $car = Mobil::factory()->create();
        foreach ([['tgl_ambil' => '2026-11-01', 'tgl_kembali' => '2026-11-12'], ['tgl_ambil' => '2026-11-12', 'tgl_kembali' => '2026-11-10'], ['tgl_ambil' => '2026-11-12']] as $data) {
            $this->getJson(route('katalog.estimasi', [$car] + $data))->assertUnprocessable();
        }
    }

    public function test_owner_can_save_and_clear_catalog_price(): void
    {
        $this->owner();
        $data = Mobil::factory()->make()->getAttributes();
        $data['harga_per_hari'] = '';
        $this->post(route('owner.mobil.store'), $data)->assertSessionHasNoErrors()->assertRedirect();
        $car = Mobil::firstOrFail();
        $this->assertNull($car->harga_per_hari);
        $this->get(route('owner.mobil.index'))->assertOk()->assertSee('Hubungi untuk harga');
        $this->put(route('owner.mobil.update', $car), array_replace($data, ['harga_per_hari' => 400000]))->assertSessionHasNoErrors();
        $this->assertSame(400000, $car->fresh()->harga_per_hari);
        $this->put(route('owner.mobil.update', $car), $data)->assertSessionHasNoErrors();
        $this->assertNull($car->fresh()->harga_per_hari);
        $this->put(route('owner.mobil.update', $car), array_replace($data, ['harga_per_hari' => 0]))->assertSessionHasErrors('harga_per_hari');
    }

    public function test_unpriced_car_allows_whatsapp_inquiry_without_a_zero_estimate(): void
    {
        Pengaturan::nilai()->update(['no_whatsapp' => '6281234567890']);
        $car = Mobil::factory()->create(['harga_per_hari' => null]);
        $this->get(route('katalog.index'))->assertOk()->assertSee('Hubungi untuk harga')->assertDontSee('Rp0');
        $this->get(route('katalog.show', $car))->assertOk()->assertSee('Tanya harga via WhatsApp')
            ->assertSee(app(WhatsAppLinkBuilder::class)->tanya($car), false)->assertDontSee('Rp0');
        $dates = ['tgl_ambil' => '2026-11-10', 'tgl_kembali' => '2026-11-12', 'jenis_sewa' => 'dengan_sopir'];
        $this->get(route('katalog.show', [$car] + $dates))->assertOk()->assertSee('Hubungi untuk harga')->assertDontSee('Rp0');
        $response = $this->getJson(route('katalog.estimasi', [$car] + $dates))->assertOk()
            ->assertJsonPath('tersedia', true)->assertJsonPath('lama', 3)->assertJsonPath('total', 'Hubungi untuk harga');
        $message = rawurldecode($response->json('wa'));
        $this->assertStringContainsString($car->plat_nomor, $message);
        $this->assertStringContainsString('Mohon informasi harga sewa', $message);
        $this->assertStringNotContainsString('Rp0', $message);
        app(PesananService::class)->simpan($this->payload($car) + ['harga_kesepakatan' => 400000]);
        $this->getJson(route('katalog.estimasi', [$car] + $dates))->assertOk()->assertJsonPath('tersedia', false)->assertJsonPath('wa', null);
    }

    public function test_unpriced_car_requires_agreed_order_price_and_preserves_it_on_edit(): void
    {
        $this->owner();
        $car = Mobil::factory()->create(['harga_per_hari' => null]);
        $data = $this->payload($car);
        $this->post(route('owner.pesanan.store'), $data)->assertSessionHasErrors('harga_kesepakatan');
        $this->assertDatabaseCount('pesanan', 0);
        $this->post(route('owner.pesanan.store'), $data + ['harga_kesepakatan' => -1])->assertSessionHasErrors('harga_kesepakatan');
        $this->post(route('owner.pesanan.store'), $data + ['harga_kesepakatan' => 400000])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseCount('pesanan', 1);
        $order = Pesanan::firstOrFail();
        $this->assertSame(400000, $order->harga_per_hari);
        $this->assertSame(1200000, $order->total_biaya);
        $this->assertNull($car->fresh()->harga_per_hari);
        $this->put(route('owner.pesanan.update', $order), array_replace($data, ['tgl_kembali' => '2026-11-13']))->assertSessionHasNoErrors();
        $this->assertSame(1600000, $order->fresh()->total_biaya);
        $this->get(route('owner.pesanan.edit', $order))->assertOk();
    }

    public function test_new_customer_and_order_are_saved_together(): void
    {
        $this->owner();
        $data = $this->payload();
        unset($data['pelanggan_id']);
        $this->post(route('owner.pesanan.store'), $data + ['nama_pelanggan' => 'Siti Aminah', 'no_hp_pelanggan' => '6281234567890'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('pelanggan', ['nama' => 'Siti Aminah']);
        $this->assertDatabaseCount('pesanan', 1);
    }

    /** SKPL-NF08: Batas unggahan foto. */
    public function test_car_upload_validates_type_and_stores_image(): void
    {
        Storage::fake('public');
        $this->owner();
        $car = Mobil::factory()->make();
        $data = $car->getAttributes();
        $this->post(route('owner.mobil.store'), $data + ['foto' => UploadedFile::fake()->create('script.php', 10, 'text/plain')])->assertSessionHasErrors('foto');
        $this->post(route('owner.mobil.store'), $data + ['foto' => UploadedFile::fake()->image('mobil.jpg')])->assertSessionHasNoErrors();
        Storage::disk('public')->assertExists(Mobil::first()->foto);
    }

    /** SKPL-F19: Laporan tanggal bayar, PDF kosong, dan periode tidak valid. */
    public function test_reports_sum_received_payments_by_payment_date_and_generate_pdf(): void
    {
        $this->owner();
        $order = $this->order();
        Pembayaran::factory()->create(['pesanan_id' => $order->id, 'jumlah' => 200000, 'tgl_bayar' => '2026-11-09']);
        Pembayaran::factory()->create(['pesanan_id' => $order->id, 'jumlah' => 400000, 'tgl_bayar' => '2026-10-01']);
        $this->get('/owner/laporan?awal=2026-11-01&akhir=2026-11-09')->assertOk()->assertSee('Rp200.000')->assertDontSee('Rp400.000');
        $this->get('/owner/laporan/pdf?awal=2026-11-01&akhir=2026-11-09')->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->get('/owner/laporan/pdf?awal=2026-09-01&akhir=2026-09-30')->assertOk();
        $this->get('/owner/laporan?awal=2026-11-12&akhir=2026-11-01')->assertSessionHasErrors('akhir');
    }

    public function test_settings_invalidate_cached_whatsapp_configuration(): void
    {
        $this->owner();
        $setting = Pengaturan::nilai();
        $this->assertNull(app(WhatsAppLinkBuilder::class)->kontak());
        $data = $setting->only($setting->getFillable());
        $data['no_whatsapp'] = '6281234567890';
        $data['jam_operasional'] = 'Setiap hari, 08.00–20.00';
        $this->put(route('owner.pengaturan.update'), $data)->assertSessionHasNoErrors();
        $this->assertStringContainsString('6281234567890', app(WhatsAppLinkBuilder::class)->kontak());
        $data['biaya_sopir_per_hari'] = -1;
        $this->put(route('owner.pengaturan.update'), $data)->assertSessionHasErrors('biaya_sopir_per_hari');
    }

    public function test_login_logout_rate_limit_and_password_update(): void
    {
        $user = User::factory()->create(['email' => 'owner@example.test', 'password' => 'ValidPassword123']);
        $credentials = ['email' => $user->email, 'password' => 'wrong'];
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', $credentials)->assertSessionHasErrors('email');
        }
        $this->post('/login', ['email' => $user->email, 'password' => 'ValidPassword123'])->assertSessionHasErrors('email');
        $this->travel(61)->seconds();
        $this->post('/login', ['email' => $user->email, 'password' => 'ValidPassword123'])->assertRedirect(route('owner.dashboard'));
        $this->put(route('owner.password.update'), ['current_password' => 'wrong', 'password' => 'NewPassword12345', 'password_confirmation' => 'NewPassword12345'])->assertSessionHasErrors('current_password');
        $this->put(route('owner.password.update'), ['current_password' => 'ValidPassword123', 'password' => 'NewPassword12345', 'password_confirmation' => 'NewPassword12345'])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NewPassword12345', $user->fresh()->password));
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_settings_survive_database_cache_serialization(): void
    {
        config(['cache.default' => 'database']);
        Cache::forget('pengaturan');
        $setting = Pengaturan::nilai();
        $this->assertInstanceOf(Pengaturan::class, Pengaturan::nilai());
        $setting->update(['no_whatsapp' => '6281234567890']);
        $this->assertSame('6281234567890', Pengaturan::nilai()->no_whatsapp);
    }

    public function test_conflicting_order_does_not_create_a_new_customer(): void
    {
        $this->owner();
        $order = $this->order();
        $before = Pelanggan::count();
        $this->post(route('owner.pesanan.store'), ['mobil_id' => $order->mobil_id, 'jenis_sewa' => 'lepas_kunci',
            'tgl_ambil' => '2026-11-10', 'tgl_kembali' => '2026-11-12', 'nama_pelanggan' => 'Tidak Tersimpan', 'no_hp_pelanggan' => '6281234567890'])->assertSessionHasErrors('tgl_ambil');
        $this->assertSame($before, Pelanggan::count());
    }

    public function test_driver_snapshot_is_preserved_on_edit_and_negative_fine_is_rejected(): void
    {
        $this->owner();
        $data = $this->payload();
        $data['jenis_sewa'] = 'dengan_sopir';
        $order = app(PesananService::class)->simpan($data);
        Pengaturan::nilai()->update(['biaya_sopir_per_hari' => 900000]);
        $this->put(route('owner.pesanan.update', $order), array_replace($data, ['tgl_kembali' => '2026-11-13']))->assertSessionHasNoErrors();
        $this->assertSame(600000, $order->fresh()->biaya_sopir);
        $this->patch(route('owner.pesanan.status', $order), ['status' => 'disewa'])->assertSessionHasNoErrors();
        $this->patch(route('owner.pesanan.status', $order), ['status' => 'selesai', 'tgl_dikembalikan' => '2026-11-09', 'denda' => -1])->assertSessionHasErrors('denda');
    }

    public function test_large_photo_and_duplicate_plate_are_rejected(): void
    {
        $this->owner();
        $car = Mobil::factory()->create();
        $data = $car->getAttributes();
        unset($data['foto']);
        $this->post(route('owner.mobil.store'), $data)->assertSessionHasErrors('plat_nomor');
        $data['plat_nomor'] = 'B 9999 QA';
        $this->post(route('owner.mobil.store'), $data + ['foto' => UploadedFile::fake()->image('large.jpg')->size(2049)])->assertSessionHasErrors('foto');
    }
}
