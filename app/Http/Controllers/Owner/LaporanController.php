<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\LaporanRequest;
use App\Services\LaporanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class LaporanController extends Controller
{
    public function __construct(private LaporanService $service) {}

    private function data(LaporanRequest $request): array
    {
        $awal = $request->validated('awal') ?: today()->startOfMonth()->toDateString();
        $akhir = $request->validated('akhir') ?: today()->toDateString();
        if ($akhir < $awal) {
            throw ValidationException::withMessages(['akhir' => 'Tanggal akhir harus sama atau setelah tanggal awal.']);
        }

        return compact('awal', 'akhir') + $this->service->periode($awal, $akhir);
    }

    public function index(LaporanRequest $request): View
    {
        return view('owner.laporan', $this->data($request));
    }

    public function pdf(LaporanRequest $request): Response
    {
        return Pdf::loadView('pdf.laporan', $this->data($request))->setPaper('a4', 'landscape')->download('Laporan-GRM.pdf');
    }
}
