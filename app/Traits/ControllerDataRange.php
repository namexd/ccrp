<?php

namespace App\Traits;


use Carbon\Carbon;

trait ControllerDataRange
{
    /**
     * @var string
     */
    protected $_date_start = '';

    /**
     * @var string
     */
    protected $_date_end = '';
    /**
     * @var string
     */
    protected $_date_name = '';


    public function get_date_start($format = 'timestamp')
    {
        return $format == 'timestamp' ? strtotime($this->_date_start) : $this->_date_start;
    }

    public function get_date_end($format = 'timestamp')
    {
        return $format == 'timestamp' ? strtotime($this->_date_end) : $this->_date_end;
    }

    public function get_dates($format = 'timestamp', $with_name = false)
    {
        $dates = [
            'date_start' => $this->get_date_start($format),
            'date_end' => $this->get_date_end($format),
        ];
        if ($with_name) {
            $dates['date_name'] = $this->get_date_name();
        }
        return $dates;
    }

    public function get_date_name()
    {
        return $this->_date_name;
    }

    public function set_date_start($value)
    {
        $this->_date_start = $value;
    }

    public function set_date_end($value)
    {
        return $this->_date_end = $value;
    }

    public function default_datas($dates)
    {
        switch ($dates) {
            case '全部':
                $start = Carbon::create(2000, 14, 1, 0, 0, 0)->toDateTimeString();
                $end = Carbon::now()->endOfDay()->toDateTimeString();
                break;
            case '今日':
                $start = Carbon::today()->toDateTimeString();
                $end = Carbon::now()->endOfDay()->toDateTimeString();
                break;
            case '昨日':
                $start = Carbon::now()->yesterday()->startOfDay()->toDateTimeString();
                $end = Carbon::now()->yesterday()->endOfDay()->toDateTimeString();
                break;
            case '本周':
                $start = Carbon::now()->startOfWeek()->toDateTimeString();
                $end = Carbon::now()->endOfWeek()->toDateTimeString();
                break;
            case '上周':
                $start = Carbon::now()->previous()->startOfWeek()->toDateTimeString();
                $end = Carbon::now()->previous()->endOfWeek()->toDateTimeString();
                break;
            case '本月':
                $start = Carbon::now()->startOfMonth()->toDateTimeString();
                $end = Carbon::now()->endOfMonth()->toDateTimeString();
                break;
            case '上月':
                $start = Carbon::now()->subMonth(1)->startOfMonth()->toDateTimeString();
                $end = Carbon::now()->subMonth(1)->endOfMonth()->toDateTimeString();
                break;
            case '本年':
                $start = Carbon::now()->startOfYear()->toDateTimeString();
                $end = Carbon::now()->lastOfYear()->toDateTimeString();
                break;
            case '去年':
                $start = Carbon::now()->subYear(1)->startOfYear()->toDateTimeString();
                $end = Carbon::now()->subYear(1)->endOfYear()->toDateTimeString();
                break;
            case '最近7天':
                $start = Carbon::now()->subDays(7)->startOfDay()->toDateTimeString();
                $end = Carbon::now()->endOfDay()->toDateTimeString();
                break;
            case '最近30天':
                $start = Carbon::now()->subDays(30)->startOfDay()->toDateTimeString();
                $end = Carbon::now()->endOfDay()->toDateTimeString();
                break;
            default:
                $start = null;
                $end = null;
        }
        return ['start' => $start, 'end' => $end];
    }

    public function set_default_datas($dates = null)
    {
        $dates = request()->date_name ?? $dates;
        $start = null;
        $end = null;
        if ($dates) {
            $this->_date_name = $dates;
            extract($this->default_datas($dates));;
        }
        if (
            request()->date_start
            and strtotime(request()->date_start)
            and date('Y-m-d H:i:s', strtotime(request()->date_start)) != $start
        ) {
            $start = date('Y-m-d H:i:s', strtotime(request()->date_start));
            $this->_date_name = '';
        }
        if (request()->date_end
            and strtotime(request()->date_end)
            and date('Y-m-d H:i:s', strtotime(request()->date_end)) != $end
        ) {
            $end = date('Y-m-d H:i:s', strtotime(request()->date_end));
            $this->_date_name = '';
        } else {
            if ($start and $end == null) {
                $end = Carbon::tomorrow()->toDateTimeString();
            }
        }
        $this->set_date_start($start);
        $this->set_date_end($end);
    }

}
