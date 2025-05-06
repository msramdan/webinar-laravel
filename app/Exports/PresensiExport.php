<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class PresensiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $sesiId;

    public function __construct($sesiId)
    {
        $this->sesiId = $sesiId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Ambil data yang sama seperti di PDF, hanya peserta yang approved
        return DB::table('pendaftaran')
            ->join('peserta', 'pendaftaran.peserta_id', '=', 'peserta.id')
            ->leftJoin('presensi', 'pendaftaran.id', '=', 'presensi.pendaftaran_id')
            ->where('pendaftaran.sesi_id', $this->sesiId)
            ->where('pendaftaran.status', 'Approved') // Hanya ambil yang approved
            ->select(
                'peserta.nama',
                'peserta.no_telepon',
                'peserta.email', // Tambahkan email
                DB::raw('CASE WHEN presensi.id IS NOT NULL THEN "Hadir" ELSE "Tidak Hadir" END as status_presensi'),
                'presensi.waktu_presensi'
            )
            ->orderBy('peserta.nama')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Definisikan header kolom Excel
        return [
            'No',
            'Nama Peserta',
            'No Telepon',
            'Email',
            'Status Presensi',
            'Waktu Presensi',
        ];
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        static $index = 0;
        $index++;

        // Mapping data ke kolom Excel
        return [
            $index,
            $row->nama,
            $row->no_telepon,
            $row->email,
            $row->status_presensi,
            $row->waktu_presensi ? Carbon::parse($row->waktu_presensi)->format('d M Y H:i:s') : '-', // Format waktu
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:F1')->applyFromArray([ // Sesuaikan range F1 jika kolom bertambah
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // Putih
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF2563EB'], // Biru primary
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFdee2e6'],
                ],
            ],
        ]);

        // Style untuk seluruh data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:F' . $lastRow)->applyFromArray([ // Sesuaikan range F jika kolom bertambah
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFdee2e6'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER, // Tengah secara vertikal
            ],
        ]);

        // Pusatkan kolom No
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return []; // Kembalikan array kosong jika tidak ada style spesifik per cell
    }
}
