<?php

namespace App\Exports\LeftoverAllowance;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class RecapExport
{
    protected $request;
    protected $data;
    protected $dataTp;
    protected $type;
    protected $component_cut;
    protected $component_income;
    protected $component_inout;

    public function __construct($request, $data, $dataTp, $type, $component_cut, $component_income, $component_inout)
    {
        $this->request = $request;
        $this->data = $data;
        $this->dataTp = $dataTp;
        $this->type = $type;
        $this->component_cut = $component_cut;
        $this->component_income = $component_income;
        $this->component_inout = $component_inout;
    }

    public function download($filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Initialize totals
        $rit_activity = 0;
        $success_fee = 0;
        $total_income = 0;
        $total_tunai = 0;
        $total_non_tunai = 0;
        
        $compincome = [];
        $compinout = [];
        $compcut = [];
        
        foreach ($this->component_income as $ci) {
            $compincome[$ci->code] = 0;
        }
        foreach ($this->component_inout as $cio) {
            $compinout[$cio->code] = 0;
        }
        foreach ($this->component_cut as $cc) {
            $compcut[$cc->code] = 0;
        }

        // Title
        $title = 'REKAP UPAH SOPIR KERNET';
        $month = Carbon::parse($this->request->month)->locale('id')->translatedFormat('F Y');
        
        $region = '';
        if (isset($this->data[0])) {
            if ($this->request->region == 'KB') {
                $region = 'KENDARAAN BESAR';
            } else {
                $region = strtoupper($this->data[0]->employee->region->name);
            }
        }

        $sheet->setCellValue('A1', $title);
        $sheet->setCellValue('A2', $month);
        $sheet->setCellValue('A3', $region);

        // Calculate total columns
        $totalCols = 3 + count($this->component_inout) + 3 + count($this->component_cut) + count($this->component_inout) + 2;

        // Merge title cells
        $sheet->mergeCells('A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols) . '1');
        $sheet->mergeCells('A2:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols) . '2');
        $sheet->mergeCells('A3:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols) . '3');

        // Style title
        $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Headers - Row 1
        $row = 5;
        $sheet->setCellValue('A' . $row, 'NO');
        $sheet->setCellValue('B' . $row, 'NAMA');
        $sheet->setCellValue('C' . $row, 'JABATAN');
        
        $penghasilanCols = count($this->component_inout) + 3;
        $potonganCols = count($this->component_cut) + count($this->component_inout);
        
        $col = 4;
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row, 'PENGHASILAN');
        $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row . ':' . 
                          \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + $penghasilanCols - 1) . $row);
        
        $col += $penghasilanCols;
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row, 'POTONGAN');
        $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row . ':' . 
                          \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + $potonganCols - 1) . $row);
        
        $col += $potonganCols;
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row, 'GAJI YANG DITERIMA');
        $sheet->mergeCells(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row . ':' . 
                          \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . $row);

        // Headers - Row 2
        $row++;
        $sheet->setCellValue('A' . $row, 'NO');
        $sheet->setCellValue('B' . $row, 'NAMA');
        $sheet->setCellValue('C' . $row, 'JABATAN');
        $sheet->setCellValue('D' . $row, 'AKTIVITAS RIT');
        $sheet->setCellValue('E' . $row, 'SUCCESS FEE');

        $col = 6;
        foreach ($this->component_inout as $cio) {
            $sheet->setCellValueByColumnAndRow($col, $row, $cio->name);
            $col++;
        }
        $sheet->setCellValueByColumnAndRow($col, $row, 'TOTAL PENGHASILAN');
        $col++;

        foreach ($this->component_cut as $cc) {
            $sheet->setCellValueByColumnAndRow($col, $row, $cc->name);
            $col++;
        }

        foreach ($this->component_inout as $cio) {
            $sheet->setCellValueByColumnAndRow($col, $row, $cio->name);
            $col++;
        }

        $sheet->setCellValueByColumnAndRow($col, $row, 'TUNAI');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'TRANSFER');
        $lastDataCol = $col;

        // Merge first row headers
        $sheet->mergeCells('A5:A6');
        $sheet->mergeCells('B5:B6');
        $sheet->mergeCells('C5:C6');

        // Style headers
        $headerRange1 = 'A5:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastDataCol) . '5';
        $headerRange2 = 'A6:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastDataCol) . '6';
        
        $sheet->getStyle($headerRange1)->getFont()->setBold(true);
        $sheet->getStyle($headerRange2)->getFont()->setBold(true);
        $sheet->getStyle($headerRange1)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');
        $sheet->getStyle($headerRange2)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');
        $sheet->getStyle($headerRange1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($headerRange2)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($headerRange1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Data rows
        $row = 7;
        $no = 1;
        foreach ($this->data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->employee->name);
            $sheet->setCellValue('C' . $row, $item->employee->position->name);
            $sheet->setCellValue('D' . $row, $item->rit_activity);
            $sheet->setCellValue('E' . $row, $item->success_fee);

            // Update totals
            $rit_activity += $item->rit_activity;
            $success_fee += $item->success_fee;

            $col = 6;
            $inout_total_row = 0;
            foreach ($this->component_inout as $cio) {
                $inout = $item->salaryApplicationDetail
                    ->where('salary_application_id', $item->id)
                    ->where('component_id', $cio->code)
                    ->first();
                $inoutValue = $inout ? $inout->value : 0;
                $sheet->setCellValueByColumnAndRow($col, $row, $inoutValue);
                $compinout[$cio->code] += $inoutValue;
                $inout_total_row += $inoutValue;
                $col++;
            }

            // Total penghasilan
            $total_penghasilan = $item->rit_activity + $item->success_fee + $inout_total_row;
            $sheet->setCellValueByColumnAndRow($col, $row, $total_penghasilan);
            $total_income += $total_penghasilan;
            $col++;

            // Cut components
            foreach ($this->component_cut as $cc) {
                $cut = $item->salaryApplicationDetail
                    ->where('salary_application_id', $item->id)
                    ->where('component_id', $cc->code)
                    ->first();
                $cutValue = $cut ? $cut->value : 0;
                $sheet->setCellValueByColumnAndRow($col, $row, $cutValue);
                $compcut[$cc->code] += $cutValue;
                $col++;
            }

            // Inout components (second time)
            foreach ($this->component_inout as $cio) {
                $inout = $item->salaryApplicationDetail
                    ->where('salary_application_id', $item->id)
                    ->where('component_id', $cio->code)
                    ->first();
                $inoutValue = $inout ? $inout->value : 0;
                $sheet->setCellValueByColumnAndRow($col, $row, $inoutValue);
                $col++;
            }

            // Payment method
            if ($item->payment_method == 'Tunai') {
                $sheet->setCellValueByColumnAndRow($col, $row, $item->total);
                $total_tunai += $item->total;
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, '');
            } else {
                $sheet->setCellValueByColumnAndRow($col, $row, '');
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, $item->total);
                $total_non_tunai += $item->total;
            }

            // Format currency
            $sheet->getStyle('D' . $row . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastDataCol) . $row)
                  ->getNumberFormat()->setFormatCode('#,##0');
            
            // Add borders
            $sheet->getStyle('A' . $row . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastDataCol) . $row)
                  ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $row++;
        }

        // Summary row
        $sheet->setCellValue('A' . $row, 'JUMLAH');
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->setCellValue('D' . $row, $rit_activity);
        $sheet->setCellValue('E' . $row, $success_fee);

        $col = 6;
        foreach ($this->component_inout as $cio) {
            $sheet->setCellValueByColumnAndRow($col, $row, $compinout[$cio->code]);
            $col++;
        }
        $sheet->setCellValueByColumnAndRow($col, $row, $total_income);
        $col++;

        foreach ($this->component_cut as $cc) {
            $sheet->setCellValueByColumnAndRow($col, $row, $compcut[$cc->code]);
            $col++;
        }

        foreach ($this->component_inout as $cio) {
            $sheet->setCellValueByColumnAndRow($col, $row, $compinout[$cio->code]);
            $col++;
        }

        $sheet->setCellValueByColumnAndRow($col, $row, $total_tunai);
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, $total_non_tunai);

        // Style summary row
        $sheet->getStyle('A' . $row . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastDataCol) . $row)
              ->getFont()->setBold(true);
        $sheet->getStyle('D' . $row . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastDataCol) . $row)
              ->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('A' . $row . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastDataCol) . $row)
              ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // TP Section (jika ada data TP)
        if ($this->dataTp->count() > 0) {
            $row += 2;
            
            // Pool TP names
            $pool_tp_names = collect($this->dataTp)->pluck('employee.poolTp.name')->unique()->filter()->sort()->values();
            $formatted_pool_names = '';
            if ($pool_tp_names->count() > 1) {
                $formatted_pool_names = $pool_tp_names->slice(0, -1)->implode(', ') . ' & ' . $pool_tp_names->last();
            } elseif ($pool_tp_names->count() == 1) {
                $formatted_pool_names = $pool_tp_names->first();
            }

            $sheet->setCellValue('A' . $row, 'DAFTAR UPAH SOPIR & KERNET ' . strtoupper($formatted_pool_names));
            $sheet->mergeCells('A' . $row . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastDataCol) . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);

            // Add TP table similar to main table...
            // (Implementation similar to above but for TP data)
        }

        // Auto-size columns
        for ($i = 1; $i <= $lastDataCol; $i++) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        // Output file
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
