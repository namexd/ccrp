<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExpoter extends AbstractExporter
{
    const 单元格格式是否 = '[=1]"是";[=0]"否";""';
    const 单元格格式字符 = '0';
    const 单元格格式日期 = 'yyyy-mm-dd';
    const 转换格式时间戳成日期 = 'date';
    const 转换格式时间戳成时间 = 'datetime';
    private $cellKey = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
        'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM',
        'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'
    ];
    protected $filename = 'excel';
    protected $head = [];
    protected $body = [];
    protected $columnFormat = null;
    protected $columnTransfer = null;

    public function setFileName($filename)
    {
        $this->filename = $filename;
    }

    public function setColumn($column)
    {
        $this->head = array_values($column);
        $this->body = array_keys($column);
    }

    public function setColumnFormat(array $value)
    {
        $array_keys = array_flip($this->body);
        $keys = [];
        foreach ($value as $key => $item) {
            $keys[] = $this->cellKey[$array_keys[$key]];
        }
        $arr = array_combine($keys, array_values($value));
        $this->columnFormat = $arr;
    }

    public function setColumnTransfer(array $value)
    {
        $this->columnTransfer = $value;
    }

    public function ColumnTransfer(array $row)
    {
        if ($this->columnTransfer) {
            foreach ($this->columnTransfer as $key => $value) {
                switch ($value) {
                    case 'date':
                        $row[$key] = date('Y-m-d', $row[$key]);
                        break;
                    case 'datetime':
                        $row[$key] = date('Y-m-d H:i:s', $row[$key]);
                        break;
                }
            }
        }
        return $row;
    }

    public function setAttr($head, $body)
    {
        $this->head = $head;
        $this->body = $body;
    }

    public function export()
    {
        $filename = $this->filename;
        $filename = $filename . date('_YmdHis');
        /** @Excel */
        Excel::create($filename, function ($excel) {
            $excel->setTitle('LengWang');
            // Chain the setters
            $excel->setCreator('LengWang')
                ->setCompany('LengWang');
            $excel->setDescription('LengWang export documents.');
            $excel->sheet('Sheet1', function ($sheet) {
                $sheet->setAutoSize(true);
                if ($this->columnFormat) {
                    $sheet->setColumnFormat($this->columnFormat);
                }
                $head = $this->head;
                $body = $this->body;
                $bodyRows = collect($this->getData())->map(function ($item) use ($body) {
                    if ($this->columnTransfer) {
                        $item = $this->ColumnTransfer($item);
                    }
                    $arr=[];
                    foreach ($body as $keyName) {
                        $arr[] = array_get($item, $keyName);
                    }
                    return $arr;
                });
                $rows = collect([$head])->merge($bodyRows);
                $sheet->rows($rows);
                $countRows = count($rows);
                $countColmn = count($this->body) - 1;
                if($countColmn<=0)$countColmn=0;
//                $sheet->setBorder('A1:F10', 'thin');

                $sheet->getStyle('A1:' . $this->cellKey[$countColmn] . $countRows)->getAlignment()->setHorizontal('center');
//                $sheet->getStyle('A1:' . $this->cellKey[$countColmn] . $countRows)->getBorders()->getAllBorders()->setBorderStyle('thin');

            });

        })->export('xls');
    }
}