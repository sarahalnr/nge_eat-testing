<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Barryvdh\DomPDF\Facade\Pdf;

class DownloadController extends Controller
{
    /**
     * Download laporan transaksi sebagai Excel
     */
    public function downloadExcel(Request $request)
    {
        try {
            date_default_timezone_set('Asia/Jakarta');

            $data = $this->getTransactionData($request);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'LAPORAN TRANSAKSI');
            $sheet->mergeCells('A1:G1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A2', 'Tanggal Generate: ' . date('d/m/Y H:i:s'));
            $sheet->mergeCells('A2:G2');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $platform = $request->input('platform', 'Semua Platform');
            $platformText = match($platform) {
                'gofood' => 'GoFood',
                'grabfood' => 'GrabFood',
                'shopeefood' => 'ShopeeFood',
                default => 'Semua Platform'
            };

            $sheet->setCellValue('A3', 'Platform: ' . $platformText);
            $sheet->mergeCells('A3:G3');
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $headers = ['Kategori', 'ID Pesanan', 'Tanggal', 'Waktu', 'Status', 'Metode Pembayaran', 'Total'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '5', $header);
                $col++;
            }

            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F58220']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ];

            $sheet->getStyle('A5:G5')->applyFromArray($headerStyle);

            $row = 6;
            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, $item->kategori ?? '-');
                $sheet->setCellValue('B' . $row, $item->id_pesanan);
                $sheet->setCellValue('C' . $row, \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y'));
                $sheet->setCellValue('D' . $row, \Carbon\Carbon::parse($item->waktu)->format('H:i'));
                $sheet->setCellValue('E' . $row, $item->status ? 'Sukses' : 'Gagal');
                $sheet->setCellValue('F' . $row, $item->metode_pembayaran);
                $sheet->setCellValue('G' . $row, 'Rp ' . number_format($item->total, 0, ',', '.'));

                $sheet->getStyle('A' . $row . ':G' . $row)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                if ($row % 2 == 0) {
                    $sheet->getStyle('A' . $row . ':G' . $row)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('F9F9F9');
                }

                $row++;
            }

            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $totalData = count($data);
            $totalPendapatan = collect($data)->sum('total');

            $sheet->setCellValue('A' . ($row + 1), 'RINGKASAN');
            $sheet->mergeCells('A' . ($row + 1) . ':G' . ($row + 1));
            $sheet->getStyle('A' . ($row + 1))->getFont()->setBold(true);

            $sheet->setCellValue('A' . ($row + 2), 'Total Transaksi: ' . $totalData);
            $sheet->setCellValue('A' . ($row + 3), 'Total Pendapatan: Rp ' . number_format($totalPendapatan, 0, ',', '.'));

            $writer = new Xlsx($spreadsheet);
            $filename = 'nge_eat_transaksi_' . date('Y-m-d_H-i-s') . '.xlsx';

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh file Excel: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadPdf(Request $request)
    {
        try {
            date_default_timezone_set('Asia/Jakarta');

            $data = $this->getTransactionData($request);

            $totalData = count($data);
            $totalPendapatan = collect($data)->sum('total');

            $platform = $request->input('platform', 'all');
            $platformText = match($platform) {
                'gofood' => 'GoFood',
                'grabfood' => 'GrabFood',
                'shopeefood' => 'ShopeeFood',
                default => 'Semua Platform'
            };

            $viewData = [
                'data' => $data,
                'title' => 'Laporan Transaksi',
                'platform' => $platformText,
                'date' => date('d/m/Y H:i:s'),
                'total' => $totalData,
                'totalPendapatan' => $totalPendapatan
            ];

            $pdf = Pdf::loadView('exports.laporan-pdf', $viewData);
            $pdf->setPaper('A4', 'landscape');
            $filename = 'nge_eat_transaksi_' . date('Y-m-d_H-i-s') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh file PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getTransactionData(Request $request)
    {
        $platform = $request->input('platform');

        $selectColumns = [
            'tf.id_pesanan',
            'tf.tanggal',
            'tf.waktu',
            'tf.status',
            'tf.metode_pembayaran',
            'tf.total',
            DB::raw('GROUP_CONCAT(DISTINCT c.name SEPARATOR ", ") as kategori')
        ];

        $groupColumns = [
            'tf.id_pesanan',
            'tf.tanggal',
            'tf.waktu',
            'tf.status',
            'tf.metode_pembayaran',
            'tf.total'
        ];

        if ($platform === 'gofood') {
            $query = DB::table('transaksi_go_food as tf')
                ->join('transaksi_go_food_items as t', 'tf.id', '=', 't.transaksi_id')
                ->join('menus as m', 't.menu_id', '=', 'm.id')
                ->join('categories as c', 'm.category_id', '=', 'c.id')
                ->select($selectColumns)
                ->groupBy($groupColumns);
        } elseif ($platform === 'grabfood') {
            $query = DB::table('transaksi_grab_food as tf')
                ->join('transaksi_grab_food_items as t', 'tf.id', '=', 't.transaksi_id')
                ->join('menus as m', 't.menu_id', '=', 'm.id')
                ->join('categories as c', 'm.category_id', '=', 'c.id')
                ->select($selectColumns)
                ->groupBy($groupColumns);
        } elseif ($platform === 'shopeefood') {
            $query = DB::table('transaksi_shopee_food as tf')
                ->join('transaksi_shopee_food_items as t', 'tf.id', '=', 't.transaksi_id')
                ->join('menus as m', 't.menu_id', '=', 'm.id')
                ->join('categories as c', 'm.category_id', '=', 'c.id')
                ->select($selectColumns)
                ->groupBy($groupColumns);
        } else {
            $gofood = DB::table('transaksi_go_food as tf')
                ->join('transaksi_go_food_items as t', 'tf.id', '=', 't.transaksi_id')
                ->join('menus as m', 't.menu_id', '=', 'm.id')
                ->join('categories as c', 'm.category_id', '=', 'c.id')
                ->select(array_merge($selectColumns, [DB::raw("'GoFood' as platform_name")]))
                ->groupBy($groupColumns);

            $grabfood = DB::table('transaksi_grab_food as tf')
                ->join('transaksi_grab_food_items as t', 'tf.id', '=', 't.transaksi_id')
                ->join('menus as m', 't.menu_id', '=', 'm.id')
                ->join('categories as c', 'm.category_id', '=', 'c.id')
                ->select(array_merge($selectColumns, [DB::raw("'GrabFood' as platform_name")]))
                ->groupBy($groupColumns);

            $shopeefood = DB::table('transaksi_shopee_food as tf')
                ->join('transaksi_shopee_food_items as t', 'tf.id', '=', 't.transaksi_id')
                ->join('menus as m', 't.menu_id', '=', 'm.id')
                ->join('categories as c', 'm.category_id', '=', 'c.id')
                ->select(array_merge($selectColumns, [DB::raw("'ShopeeFood' as platform_name")]))
                ->groupBy($groupColumns);

            $union = $gofood->unionAll($grabfood)->unionAll($shopeefood);
            $query = DB::query()->fromSub($union, 'sub')->orderByDesc('tanggal');
        }

        return $query->get();
    }
}
