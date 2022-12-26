<?php
namespace Ynotz\EasyAdmin\ImportExports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DefaultArrayExports implements FromArray, WithHeadings
{
    protected $array = [];
    private $colsFormat;
    private $colTitles = null;

    public function __construct($array, $colsFormat, $colTitles = [])
    {
        $this->array = [];
        foreach ($array as $row) {
            $temprow = [];
            foreach ($colsFormat as $col) {
                $temprow[$col] = $row[$col];
            }
            $this->array[] = $temprow;
        }
        $this->colsFormat = $colsFormat;
        $this->colTitles = $colTitles;
    }

    public function array(): array
    {
        return $this->array;
    }

    public function headings(): array
    {
        return count($this->colTitles) > 0 ? $this->colTitles : $this->colsFormat;
    }
}
?>
