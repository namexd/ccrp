<?php


namespace App\Models\Ccrp;


use function App\Utils\is_com_diy;
use function App\Utils\time_format2;
use function App\Utils\to_shidu;
use function App\Utils\to_wendu;
use function App\Utils\vehicle_temp2;
use function App\Utils\vehicle_time2;

class PrinterTemplate extends Coldchain2Model
{

    public function default($type, $title, $datas, $company_id, $subtitle = NULL, $summary = '')
    {
        $setting = CompanyUseSetting::whereHas('setting', function ($query) {
            $query->where('slug', 'printer_template');
        })->where('company_id', $company_id)->first();
        $result = [];
        if ($setting) {
            $template = $this->find($setting->value);
            $result = $this->{$template->function}($type, $title, $datas, $subtitle, $summary);
        } else {
            switch ($type) {
                case 'vehicle':
                    $result = $this->vehicle_print_data_format($title, $datas);
                    break;
                case 'collector':
                    $result = $this->collector_print_data_format($title, $datas, $subtitle, $summary);
                    break;

            }
        }
        return $result;
    }

    private function chongqingYouyang($type, $title, $datas, $subtitle, $summary)
    {
        $result = [];
        switch ($type) {
            case 'vehicle':
                $result = $this->vehicle_chongqingYouyang($title, $datas, $subtitle);
                break;
            case 'collector':
                $result = $this->collector_chongqingYouyang($title, $datas, $subtitle, $summary);
                break;

        }
        return $result;
    }

    private function CommonWithoutPrintTime($type, $title, $datas, $subtitle = NULL, $summary = '')
    {
        $result = [];
        switch ($type) {
            case 'vehicle':
                $result = $this->vehicle_print_data_format_no_time($title, $datas);
                break;
            case 'collector':
                $result = $this->collector_print_data_format_no_time($title, $datas, $subtitle, $summary);
                break;

        }
        return $result;
    }

    private function vehicle_print_data_format($title, $datas)
    {
        $orderInfo = '<CB>'.$title.'</CB><BR>';
        $orderInfo .= $this->vehicle_datas($datas);
        $orderInfo .= '--------------------------------<BR>';
        $orderInfo .= '打印时间：'.date('Y-m-d H:i:s').'<BR>';
        $orderInfo .= '<BR>';
        $orderInfo .= '<BR>';
        $orderInfo .= '签字________________________<BR>';
        $orderInfo .= '<BR>';
        $orderInfo .= ' 技术支持电话：400-681-5218<BR>';
        $orderInfo .= '　上海冷王智能科技有限公司';
        return $orderInfo;
    }

    private function collector_print_data_format($title, $datas, $subtitle, $summary)
    {
        if ($subtitle and gettype($subtitle) == 'string') {
            $orderInfo = '<CB>'.$title.'</CB><BR>';
            $orderInfo .= $subtitle.'<BR>';
            $orderInfo .= '--------------------------------<BR>';


        } else {
            $orderInfo = '<CB>'.$title.'</CB><BR>';
            $orderInfo .= '--------------------------------<BR>';
        }

        if ($summary != '') {
            $orderInfo .= $summary;
            $orderInfo .= '<BR>';
            $orderInfo .= '--------------------------------<BR>';
        }

        $orderInfo .= $this->collector_datas($datas);

        $orderInfo .= '--------------------------------<BR>';
        $orderInfo .= '打印时间：'.date('Y-m-d H:i:s').'<BR>';
        $orderInfo .= '<BR>';
        $orderInfo .= '<BR>';
        $orderInfo .= '签字________________________<BR>';
        $orderInfo .= '<BR>';
        $orderInfo .= ' 技术支持电话：400-681-5218<BR>';
        $orderInfo .= '　上海冷王智能科技有限公司';
        return $orderInfo;
    }

    private function vehicle_print_data_format_no_time($title, $datas)
    {
        $orderInfo = '<CB>'.$title.'</CB><BR>';
        $orderInfo .=$this->vehicle_datas($datas);
        $orderInfo .= '--------------------------------<BR>';
        $orderInfo .= '<BR>';
        $orderInfo .= '<BR>';
        $orderInfo .= '签字________________________<BR>';
        $orderInfo .= '<BR>';
        $orderInfo .= ' 技术支持电话：400-681-5218<BR>';
        $orderInfo .= '　上海冷王智能科技有限公司';
        return $orderInfo;
    }

    private function collector_print_data_format_no_time($title, $datas, $subtitle, $summary)
    {
        if ($subtitle and gettype($subtitle) == 'string') {
            $orderInfo = '<CB>'.$title.'</CB><BR>';
            $orderInfo .= $subtitle.'<BR>';
            $orderInfo .= '--------------------------------<BR>';


        } else {
            $orderInfo = '<CB>'.$title.'</CB><BR>';
            $orderInfo .= '--------------------------------<BR>';
        }

        if ($summary != '') {
            $orderInfo .= $summary;
            $orderInfo .= '<BR>';
            $orderInfo .= '--------------------------------<BR>';
        }

        $orderInfo .=$this->collector_datas($datas);

        $orderInfo .= '--------------------------------<BR>';


        $orderInfo .= '<BR>';
        $orderInfo .= '<BR>';
        $orderInfo .= '签字________________________<BR>';
        $orderInfo .= '<BR>';
        $orderInfo .= ' 技术支持电话：400-681-5218<BR>';
        $orderInfo .= '　上海冷王智能科技有限公司';
        return $orderInfo;
    }

    private function vehicle_chongqingYouyang($title, $datas, $subtitle)
    {
        if ($subtitle and gettype($subtitle) == 'string') {
            $orderInfo = '<CB>'.$title.'</CB><BR>';
            $orderInfo .= '配送单位<BR>';
            $orderInfo .= $subtitle.'<BR>';
            $orderInfo .= '--------------------------------<BR>';


        } else {
            $orderInfo = '<CB>'.$title.'</CB><BR>';
            $orderInfo .= '--------------------------------<BR>';
        }

        $orderInfo .=$this->vehicle_datas($datas);
        $orderInfo .= '--------------------------------<BR>';
        $orderInfo .= '接收单位:<BR>';
        $orderInfo .= '<BR>';
        $orderInfo .= '<BR>';
        $orderInfo .= '签字________________________<BR>';
        $orderInfo .= '<BR>';
        $orderInfo .= ' 技术支持电话：400-681-5218<BR>';
        $orderInfo .= '　上海冷王智能科技有限公司';
        return $orderInfo;
    }

    private function collector_chongqingYouyang($title, $datas, $subtitle, $summary)
    {
        if ($subtitle and gettype($subtitle) == 'string') {
            $orderInfo = '<CB>'.$title.'</CB><BR>';
            $orderInfo .= '配送单位:<BR>';
            $orderInfo .= $subtitle.'<BR>';
            $orderInfo .= '--------------------------------<BR>';


        } else {
            $orderInfo = '<CB>'.$title.'</CB><BR>';
            $orderInfo .= '--------------------------------<BR>';
        }

        if ($summary != '') {
            $orderInfo .= $summary;
            $orderInfo .= '<BR>';
            $orderInfo .= '--------------------------------<BR>';
        }
        $orderInfo .= $this->collector_datas($datas);

        $orderInfo .= '--------------------------------<BR>';


        $orderInfo .= '接收单位:<BR>';
        $orderInfo .= '<BR>';
        $orderInfo .= '<BR>';
        $orderInfo .= '签字________________________<BR>';
        $orderInfo .= '<BR>';
        $orderInfo .= ' 技术支持电话：400-681-5218<BR>';
        $orderInfo .= '　上海冷王智能科技有限公司';
        return $orderInfo;
    }

    private function collector_datas($datas)
    {
        if (is_null($datas)) {
            return '';
        }
        $orderInfo = '';
        $orderInfo .= '时间 | 温度<BR>';
        $orderInfo .= '--------------------------------<BR>';
        foreach ($datas as $vo) {
            $orderInfo .= ''.time_format2($vo['collect_time'], 'Y-m-d H:i').'　'.to_wendu($vo['temp']).','.to_shidu($vo['humi']).' <BR>';
        }
        return $orderInfo;
    }

    private function vehicle_datas($datas)
    {

        if (is_null($datas)) {
            return '';
        }

        $orderInfo = '';
        $orderInfo .= '时间|温度1|温度2|温度3|温度4<BR>';
        $orderInfo .= '--------------------------------<BR>';
        foreach ($datas as $vo) {
            $orderInfo .= ''.vehicle_time2($vo['RcvDT']).'　'.vehicle_temp2($vo['Temperature']).','.vehicle_temp2($vo['Temperature2']).','.vehicle_temp2($vo['Temperature3']).','.vehicle_temp2($vo['Temperature4']).' <BR>';
        }
        return $orderInfo;
    }
}
