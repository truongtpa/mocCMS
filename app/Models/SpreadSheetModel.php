<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SpreadSheetModel extends Model
{
    public static function readExcel($file)
    {
        $data = [];
        $row_limit = 0;
        $column_limit = 0;

        try {
            if (!$file || !file_exists($file)) {
                throw new \Exception("Tệp không tồn tại hoặc không hợp lệ.");
            }

            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();

            $data = $sheet->toArray();

            // Xác định dòng và cột thực sự có dữ liệu
            $row_limit = $sheet->getHighestDataRow();
            $column_limit = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString(
                $sheet->getHighestDataColumn()
            );
        } catch (\Exception $e) {
            return [
                'col' => 0,
                'row' => 0,
                'data' => [],
                'error' => $e->getMessage(),
            ];
        }

        return [
            'col' => $column_limit,
            'row' => $row_limit,
            'data' => $data
        ];
    }

}
