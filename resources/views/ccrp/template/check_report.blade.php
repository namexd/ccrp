<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{$result->task->company->title}}</title>
    <style>

        * {
            font-family: '宋体';
            font-size: 8pt
        }

        body {
            padding-top: 0px;
            font-family: '宋体';
            font-size: 8pt;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0
        }

        table td {
            font-family: '宋体';
            font-size: 8pt;
        }

        h2 {
            font-family: '宋体';
            font-size: 16pt;
        }

        h3 {
            font-family: '宋体';
            font-size: 10pt;
        }

        h4 {
            font-family: '宋体';
            font-size: 13pt;
        }

        u {
            padding: 0 5px;
            font-weight: bold
        }
        div  {
            margin-top: 0.5em;
        }

    </style>
</head>
<body>
<div>
    <h4 align=center>{{$result->task->company->title}}冷链资源管理系统<u style="font-size: 13pt">第{{\Carbon\Carbon::createFromTimestamp($result->task->start)->quarter}}{{\App\Models\CheckTemplate::CYCLE_TYPE[$result->task->template->cycle_type]}}</u>巡检报告</h4>

    <h3><b>一、概述</b></h3>

    <h3><b>1.</b><b>巡检基本信息</b></h3>

    <div>使用单位名称：<u>{{$result->task->company->title}}</u></div>

    <div>巡检单位：上海冷王智能科技有限公司</div>

    <div>巡检时间：<u>{{date('Y-m-d',$result->task->start)}}
            至{{date('Y-m-d',$result->task->end)}}</u></div>

    <div>
        单位概况：本季度辖区内期末冷链监测单位<u>{{$company_count}}</u>个，在监测冰箱<u>{{$cooler['bx_count']}}</u>台、冷库<u>{{$cooler['lk_count']}}</u>座、冷藏车<u>{{$cooler['lcc_count']+$vehicle_count}} </u>辆，监测探头总计<u>{{$collector_count}}</u>个。
    </div>

    <h3><b>2.</b><b>巡检内容</b></h3>

    <div>a.单位信息录入完整性：巡检单位信息录入完整性，并备注标明不完整部分，形成“单位信息一览表”（详见附件1）</div>

    <div>b.冷链装备信息登记规范性：巡检冷链装备信息录入情况，形成“冷链装备信息不规范清单”（详见附件2）</div>

    <div>c.冷链装备状态评估：确认装备使用状态是否无误（针对备用、报废装备），备用装备形成“备用冷链装备清单”；对冷链装备性能进行评估，冷链装备评估值<b>≥5</b>的形成“冷链设备评估状态异常表”（评估值越小越好）（详见附件3、4）
    </div>

    <div>d.冷链监测设备工况统计：巡检冷链监测一体机及探头工况，形成“监测设备维护统计表”（详见附件5）</div>

    <div>e.冷链监测系统预警开启情况：巡检所有启用状态的冷链状态预警开启情况，形成“启用冷链装备预警未开启清单”（详见附件6）</div>

    <div>f.预警联系人设置情况：巡检预警联系人设置为空的情况，形成“预警联系人信息不完整清单”（详见附件7）</div>

    <div>g.平台登录及管理情况：统计季度单位冷链监测平台登录次数及合格登录率，形成“平台登录及管理情况表”（详见附件8）</div>

    <div>h.预警总数及原因分析：统计各单位预警总数及处理情况，并对预警原因进行分析，形成“预警情况统计及分析表”（详见附件9）（详见附件10）</div>

    <h3><b>3.巡检结果</b></h3>

    <div>本季度巡检发现的问题如下：</div>

    <table border="1" cellspacing="0" cellpadding="0" align="center" width="100%" style="text-align: center;">
        <tr>
            <td>
                类别
            </td>
            <td>
                情况描述
            </td>
            <td>
                数量
            </td>
            <td>
                备注
            </td>
        </tr>
        <tr>
            <td rowspan="3">
                冷链装备情况
            </td>
            <td>
                装备信息不规范（台）
            </td>
            <td>
                {{count($un_complete_cooler)}}
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>
                装备状态待确认（台）
            </td>
            <td>
                {{count($useless_cooler)}}
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>
                装备性能评估值≥5（台）
            </td>
            <td>
                {{count($unusual_evaluates)}}
            </td>
            <td>
                评估值越小越好
            </td>
        </tr>
        <tr>
            <td rowspan="4">
                监测设备情况
            </td>
            <td>
                监测设备维护数据（次）
            </td>
            <td>
                {{count($useless_collector)+count($change_collector)+count($add_collector)}}
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>
                冷链装备预警未开启（个）
            </td>
            <td>
                {{count($warning_unable_collector)}}
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>
                主机断电预警未开启（个）
            </td>
            <td>
                {{count($warning_unable_sender)}}
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>
                低电压（个）
            </td>
            <td>
                {{count($power_unusual_collector)}}
            </td>
            <td>
                监测设备低电压建议更换电池
            </td>
        </tr>
        <tr>
            <td rowspan="3">
                冷链管理情况
            </td>
            <td>
                有效登录异常情况（单位）
            </td>
            <td>
                {{count(array_where($login_and_manage,function ($value,$key) use($result){
               return array_first($value['stat_manage'])['correct']<(\Carbon\Carbon::createFromTimestamp($result->task->end)->diffInDays(\Carbon\Carbon::createFromTimestamp($result->task->start))+1);
               }))}}
            </td>
            <td>
                有效登录：每天至少登录两次
            </td>
        </tr>
        <tr>
            <td>
                预警处理情况(单位)
            </td>
            <td>
                {{count(array_where($warning_analysis,function ($value,$key){
                 return (array_first($value['warning_events'])['count_temp_unhandled']+array_first($value['warning_sender_events'])['count_power_unhandled'])>0;
                 }))}}
            </td>
            <td>
                不及时处理
            </td>
        </tr>
        <tr>
            <td>
                管理评估值≤60（单位）
            </td>
            <td>
                {{count(array_where($login_and_manage,function ($value,$key){
                return array_first($value['stat_manage'])['grade']<=60;
                }))}}
            </td>
            <td>

            </td>
        </tr>
    </table>


    <div>冷链管理建议如下：</div>

    <table border="1" cellspacing="0" cellpadding="0" align="center" width="100%" style="text-align: center;">
        <tr>
            <td>
                类别
            </td>
            <td>
                情况描述
            </td>
            <td>
                处理建议
            </td>
            <td>
                备注
            </td>
        </tr>
        <tr>
            <td rowspan="3">
                冷链装备情况
            </td>
            <td>
                装备信息不规范
            </td>
            <td>
                及时登录完善信息，链接如下：
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>
                装备状态异常
            </td>
            <td>
                建议备用及报废的冰箱统一由CDC处理
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>
                装备性能评估值≥5
            </td>
            <td>
                建议及时进行维修或更换医用冰箱
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td rowspan="4">
                监测设备情况
            </td>
            <td>
                监测设备维护数据
            </td>
            <td>
                监测设备异常情况售后及时处理，联系方式：400-681-5218
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>
                冷链装备预警未开启
            </td>
            <td>
                建议及时开启预警
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>
                主机断电预警未开启
            </td>
            <td>
                建议及时开启预警
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>
                低电压
            </td>
            <td>
                及时更换电池
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td rowspan="3">
                冷链管理情况
            </td>
            <td>
                有效登录异常情况
            </td>
            <td>
                提供微信登录提醒服务
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>
                预警处理情况
            </td>
            <td>
                加强此方面的培训和督导
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>
                管理评估值≤60
            </td>
            <td>
                积极处理各种异常情况
            </td>
            <td>

            </td>
        </tr>
    </table>


    <div>各数据报表详见附件。</div>

    <br clear=all>


    <h3><b>附件1 单位信息一览表</b></h3>

    <div>本巡检周期内以下单位信息不完整：<u>@if($un_complete_company){{implode('、',array_pluck($un_complete_company,'title'))}} @else
                本巡检周期内单位信息完整。  @endif
        </u>
    </div>

    <div>（（修改路径如下：电脑登录冷链监测系统→点击用户名→“参数设置”→添加电子邮箱））</div>
    @if(count($un_complete_company)>0)
    <table border="1" cellspacing="0" cellpadding="0" align="center" width="100%" style="text-align: center;" align=lef>
        <tr>
            <td>
                序号
            </td>
            <td>
                单位名称
            </td>
            <td>
                负责人
            </td>
            <td>
                联系方式
            </td>
            <td>
                科室邮箱
            </td>
            <td>
                地址
            </td>
        </tr>
            @foreach($un_complete_company as $key=> $company)
                <tr>
                    <td>
                        {{$key+1}}
                    </td>
                    <td>
                        {{$company['title']}}
                    </td>
                    <td>
                        {{$company['manager']}}
                    </td>
                    <td>
                        {{$company['phone']}}
                    </td>
                    <td>
                        {{$company['email']}}
                    </td>
                    <td>
                        {{$company['address']}}
                    </td>
                </tr>
            @endforeach

    </table>
        @else
        无
    @endif
    <br clear=all>
    <h3><b>附件2 冷链装备信息不规范清单</b></h3>

    <div>本巡检周期存在冷链装备信息不规范的情况，部分情况已解决，其他需要疾控协调处理，详见备注。</div>

    <div>（修改路径如下：电脑登录冷链监测系统→“设置管理”→“冷链装备”）</div>
    @if(count($un_complete_cooler)>0)
    <table border="1" cellspacing="0" cellpadding="0" align="center" width="100%" style="text-align: center;">
        <tr>
            <td>
                序号
            </td>
            <td>
                单位名称
            </td>
            <td>
                名称
            </td>
            <td>
                编号
            </td>
            <td>
                品牌
            </td>
            <td>
                型号
            </td>
            <td>
                容积
            </td>
            <td>
                启用时间
            </td>
            <td>
                是否医用
            </td>
            <td>
                备注
            </td>
        </tr>

            @foreach($un_complete_cooler as $key=> $cooler)
                <tr>
                    <td>
                        {{$key+1}}
                    </td>
                    <td>
                        {{$cooler['company']['title']}}
                    </td>
                    <td>
                        {{$cooler['cooler_name']}}
                    </td>
                    <td>
                        {{$cooler['cooler_sn']}}
                    </td>
                    <td>
                        {{$cooler['cooler_brand']}}
                    </td>
                    <td>
                        {{$cooler['cooler_model']}}
                    </td>
                    <td>
                        {{$cooler['cooler_size']??$cooler['cooler_size2']}}L
                    </td>
                    <td>
                        {{$cooler['cooler_starttime']}}
                    </td>
                    <td>
                        {{$cooler['is_medical']==0?'未设置':'已设置'}}
                    </td>
                    <td>

                    </td>
                </tr>
            @endforeach

    </table>
    @else
        无
    @endif
    <br clear=all>
    <h3><b>附件3 冷链装备状态清单</b></h3>
    <div>本巡检周期内备用、报废装备清单如下：</div>
    @if(count($useless_cooler)>0)
    <table border="1" cellspacing="0" cellpadding="0" align="center" width="100%" style="text-align: center;"
           align=left>
        <tr>
            <td>
                序号
            </td>
            <td>
                单位名称
            </td>
            <td>
                设备名称
            </td>
            <td>
                设备编号
            </td>
            <td>
                探头数量
            </td>
            <td>
                设备状态
            </td>
            <td>
                备注
            </td>
        </tr>

            @foreach($useless_cooler as $key=> $cooler)
                <tr>
                    <td>
                        {{$key+1}}
                    </td>
                    <td>
                        {{$cooler['company']['title']}}
                    </td>
                    <td>
                        {{$cooler['cooler_name']}}
                    </td>
                    <td>
                        {{$cooler['cooler_sn']}}
                    </td>
                    <td>
                        {{$cooler['collector_num']}}
                    </td>
                    <td>
                        {{\App\Models\Ccrp\Cooler::$status[$cooler['status']]}}@if($cooler['status']==4)(
                        {{date('Y-m-d',$cooler['uninstall_time'])}})
                        @endif
                    </td>
                    <td>

                    </td>
                </tr>
            @endforeach

    </table>
@else 无
    @endif
    <br clear=all>
    <h3><b>附件4 冷链设备评估状态异常表</b></h3>

    <div>本巡检周期内冷链装备评估值≥5的设备清单如下：</div>
    @if(count($unusual_evaluates)>0)
    <table border="1" cellspacing="0" cellpadding="0" align="center" width="100%" style="text-align: center;">
        <tr>
            <td>
                序号
            </td>
            <td>
                单位名称
            </td>
            <td>
                设备名称
            </td>
            <td>
                冷链装备信息
            </td>
            <td>
                平均温度
            </td>
            <td>
                最高温度
            </td>
            <td>
                最低温度
            </td>
            <td>
                超温预警次数
            </td>
            <td>
                设备故障次数
            </td>
            <td>
                冷链设备评估值
            </td>
            <td>状态</td>
        </tr>

            @foreach($unusual_evaluates as $key=> $statCooler)
                <tr>
                    <td>
                        {{$key+1}}
                    </td>
                    <td>
                        {{$statCooler['cooler']['company']['title']}}
                    </td>
                    <td>
                        {{$statCooler['cooler']['cooler_name']}}
                    </td>
                    <td>
                        {{\App\Models\Ccrp\Cooler::COOLER_TYPE[$statCooler['cooler']['cooler_type']]}}{{$statCooler['cooler']['cooler_brand']}}{{$statCooler['cooler']['cooler_model']}}
                    </td>
                    <td>
                        {{$statCooler['temp_avg']}}
                    </td>
                    <td>
                        {{$statCooler['temp_high']}}
                    </td>
                    <td>
                        {{$statCooler['temp_low']}}
                    </td>
                    <td>
                        {{$statCooler['warning_times']}}
                    </td>
                    <td>
                        {{$statCooler['error_times']}}
                    </td>
                    <td>
                        {{$statCooler['temp_variance']}}
                    </td>
                    <td>
                        {{\App\Models\Ccrp\Cooler::$status[$statCooler['cooler']['status']]}}
                    </td>
                </tr>
            @endforeach


    </table>
        @else
        无
    @endif

    <br clear=all>
    <h3><b>附件5 监测设备维护统计表</b></h3>
    @if(count($change_collector)>0)
    <table border="1" cellspacing="0" cellpadding="0" align="center" width="100%" style="text-align: center;">
        <tr>
            <td>
                序号
            </td>
            <td>
                单位名称
            </td>
            <td>
                维护时间
            </td>
            <td>
                维护类型
            </td>
            <td>装备编号</td>
            <td>
                更换前设备ID
            </td>
            <td>
                更换后设备ID
            </td>
            <td>
                备注
            </td>
        </tr>

            @foreach($change_collector as $key=> $collector)
                <tr>
                    <td>
                        {{$key+1}}
                    </td>
                    <td>
                        {{$collector['company']['title']}}
                    </td>
                    <td>
                        {{date('Y-m-d',$collector['change_time'])}}
                    </td>
                    <td>
                        更换探头
                    </td>
                    <td>{{$collector['cooler']['cooler_sn']}}</td>
                    <td>
                        {{$collector['supplier_collector_id']}}
                    </td>
                    <td>
                        {{$collector['new_supplier_collector_id']}}
                    </td>
                    <td>

                    </td>
                </tr>
            @endforeach

    </table>
    @endif
<div><br></div>
    @if(count($useless_collector)>0)
    <table border="1" cellspacing="0" cellpadding="0" align="center" width="100%" style="text-align: center;">
        <tr>
            <td>
                序号
            </td>
            <td>
                单位名称
            </td>
            <td>
                维护时间
            </td>
            <td>
                维护类型

            </td>
            <td>装备编号</td>
            <td>
                设备ID
            </td>

            <td>
                备注
            </td>
        </tr>

            @foreach($useless_collector as $key=> $collector)
                <tr>
                    <td>
                        {{$key+1}}
                    </td>
                    <td>
                        {{$collector['company']['title']}}
                    </td>
                    <td>
                        {{date('Y-m-d',$collector['change_time'])}}
                    </td>
                    <td>
                        报废探头
                    </td>
                    <td>{{$collector['cooler']['cooler_sn']}}</td>
                    <td>
                        {{str_replace('-','',$collector['supplier_collector_id'])}}
                    </td>
                    <td>

                    </td>
                </tr>
            @endforeach

    </table>
    @endif
    <div><br></div>
    @if(count($add_collector)>0)
    <table border="1" cellspacing="0" cellpadding="0" align="center" width="100%" style="text-align: center;">
        <tr>
            <td>
                序号
            </td>
            <td>
                单位名称
            </td>
            <td>
                维护时间
            </td>
            <td>
                维护类型

            </td>
            <td>装备编号</td>
            <td>
                设备ID
            </td>

            <td>
                备注
            </td>
        </tr>

            @foreach($add_collector as $key=> $collector)
                <tr>
                    <td>
                        {{$key+1}}
                    </td>
                    <td>
                        {{$collector['company']['title']}}
                    </td>
                    <td>
                        {{date('Y-m-d',$collector['install_time'])}}
                    </td>
                    <td>
                        新增探头
                    </td>
                    <td>{{$collector['cooler']['cooler_sn']}}</td>
                    <td>
                        {{$collector['supplier_collector_id']}}
                    </td>

                    <td>

                    </td>
                </tr>
            @endforeach

    </table>

    @endif
    <br clear=all>
    <h3><b>附件6 启用冷链装备预警未开启清单</b></h3>

    <div>（针对冷链装备为启用状态）</div>
    @if(count($warning_unable_collector)>0)

    <table border="1" cellspacing="0" cellpadding="0" align="center" width="100%" style="text-align: center;">
        <tr>
            <td>
                序号
            </td>
            <td>
                单位名称
            </td>
            <td>
                装备名称
            </td>
            <td>
                装备编号
            </td>
            <td>装备状态</td>
            <td>
                探头编号
            </td>
            <td>
                预警开启
            </td>
            <td>
                备注
            </td>
        </tr>
            @foreach($warning_unable_collector as $key=> $warning)
                <tr>
                    <td>
                        {{$key+1}}
                    </td>
                    <td>
                        {{$warning['company']['title']}}
                    </td>
                    <td>
                        {{$warning['cooler']['cooler_name']}}
                    </td>
                    <td>
                        {{$warning['cooler']['cooler_sn']}}
                    </td>
                    <td>{{\App\Models\Ccrp\Cooler::$status[$warning['cooler']['status']]}}</td>
                    <td>
                        {{$warning['supplier_collector_id']}}
                    </td>
                    <td>
                        否
                    </td>
                    <td>

                    </td>
                </tr>
            @endforeach
    </table>
        @else 无
    @endif

    <br clear=all>
    <div>主机断电预警未开启的清单</div>
    @if(count($warning_unable_sender)>0)
        <table border="1" cellspacing="0" cellpadding="0" align="center" width="100%" style="text-align: center;">
            <tr>
                <td>
                    序号
                </td>
                <td>
                    单位名称
                </td>
                <td>
                    主机名称
                </td>
                <td>
                    主机编号
                </td>
                <td>
                    断电预警开启
                </td>
                <td>
                    备注
                </td>
            </tr>
            @foreach($warning_unable_sender as $key=> $sender)
                <tr>
                    <td>
                        {{$key+1}}
                    </td>
                    <td>
                        {{$sender['company']['title']}}
                    </td>
                    <td>
                        {{$sender['note']}}
                    </td>
                    <td>
                        {{$sender['sender_id']}}
                    </td>
                    <td>
                        {{$sender['warning_setting']?'未开启':'未设置'}}
                    </td>
                    <td>

                    </td>
                </tr>
            @endforeach
        </table>
        @else 无
    @endif
    <br clear=all>
    <h3><b>附件7 预警信息不完整清单</b></h3>
    @if(count($uncomplete_warnings)>0)
        <table border="1" cellspacing="0" cellpadding="0" align="center" width="100%" style="text-align: center;">
            <tr>
                <td>
                    序号
                </td>
                <td>
                    单位名称
                </td>
                <td>
                    装备编号
                </td>
                <td>
                    探头编号
                </td>
                <td>
                    超温预警
                </td>
                <td>
                    断电预警
                </td>
                <td>
                    离线预警
                </td>
                <td>
                    备注
                </td>
            </tr>
            @foreach($uncomplete_warnings as $key=> $warning)
                <tr>
                    <td>
                        {{$key+1}}
                    </td>
                    <td>
                        {{$warning['company']['title']}}
                    </td>
                    <td>{{$warning['collector']['cooler']['cooler_sn']}}</td>
                    <td>
                        {{$warning['collector']['supplier_collector_id']}}
                    </td>
                    <td>
                        {{$warning['warninger']?\App\Models\Ccrp\Warninger::WARNINGER_TYPES[$warning['warninger']['warninger_type']]:'未设置'}}
                    </td>
                    <td>
                        {{$warning['warninger']?\App\Models\Ccrp\Warninger::WARNINGER_TYPES[$warning['warninger']['warninger_type']]:'未设置'}}
                    </td>
                    <td>
                        @if($warning['company']['offline_send_type']==99)
                            @if($warning['company']['offline_send_warninger_id']==0)
                                未设置
                            @else
                                {{$warning['warninger']?\App\Models\Ccrp\Warninger::WARNINGER_TYPES[$warning['warninger']['warninger_type']]:'未设置'}}
                            @endif
                        @else
                            {{\App\Models\Ccrp\Company::$offline_send_type[$warning['company']['offline_send_type']]}}
                        @endif
                    </td>
                    <td>

                    </td>
                </tr>
            @endforeach
        </table>
        @else
        无
    @endif
    <br clear=all>

    <h3><b>附件8 平台登录及管理情况表</b></h3>

    <div>（有效登录是指每天至少登录两次）</div>
    @if(count($login_and_manage)>0)
        <table border="1" cellspacing="0" cellpadding="0" align="center" width="100%" style="text-align: center;">
            <tr>
                <td>
                    序号
                </td>
                <td>
                    单位名称
                </td>
                <td>
                    登陆次数
                </td>
                <td>
                    PC端
                </td>
                <td>
                    微信端
                </td>
                <td>
                    有效登录天数
                </td>
                <td>
                    冷链管理评估值
                </td>
                <td>
                    备注
                </td>
            </tr>
            @foreach($login_and_manage as $key=> $login)
                <tr>
                    <td>
                        {{$key+1}}
                    </td>
                    <td>
                        {{$login['title']}}
                    </td>
                    <td>
                        {{array_first($login['login_log'])['login_times']??0}}
                    </td>
                    <td>
                        {{array_first($login['login_log'])['pc_times']??0}}
                    </td>
                    <td>
                        {{array_first($login['login_log'])['wx_times']??0}}
                    </td>
                    <td>
                        {{array_first($login['stat_manage'])['correct']??0}}
                    </td>
                    <td>
                        {{array_first($login['stat_manage'])['grade']}}
                    </td>
                    <td>

                    </td>
                </tr>
            @endforeach
        </table>


    @endif
    <br clear=all>
    <h3><b>附件9 监测设备异常情况表</b></h3>
    <div>探头电压异常清单</div>

    @if(count($power_unusual_collector)>0)
        <table border="1" cellspacing="0" cellpadding="0" align="center" width="100%" style="text-align: center;">
            <tr>
                <td>
                    序号
                </td>
                <td>
                    单位名称
                </td>
                <td>
                    装备编号
                </td>
                <td>
                    设备ID
                </td>
                <td>
                    异常值
                </td>
                <td>安全值</td>
                <td>
                    备注
                </td>
            </tr>
            @foreach($power_unusual_collector as $key=> $collector)
                <tr>
                    <td>
                        {{$key+1}}
                    </td>
                    <td>
                        {{$collector['company']['title']}}
                    </td>
                    <td>
                        {{$collector['cooler']['cooler_sn']}}
                    </td>
                    <td>
                        {{$collector['supplier_collector_id']}}
                    </td>
                    <td>
                        {{$collector['volt']}}
                    </td>
                    <td>
                        @if($collector['temp_type']==2)
                           {{$collector['cold_safe_collector_volt_low']}}
                            @else
                            {{$collector['safe_collector_volt_low']}}
                     @endif
                    </td>
                    <td></td>
                </tr>
            @endforeach
        </table>
        @else
        无
    @endif
    <div>主机断电清单</div>
    @if(count($power_off_sender)>0)
        <table border="1" cellspacing="0" cellpadding="0" align="center" width="100%" style="text-align: center;">
            <tr>
                <td>
                    序号
                </td>
                <td>
                    单位名称
                </td>
                <td>
                    主机名称
                </td>
                <td>
                    主机编号
                </td>
                <td>
                    异常情况
                </td>
                <td>
                    备注
                </td>
            </tr>
            @foreach($power_off_sender as $key=> $sender)
                <tr>
                    <td>
                        {{$key+1}}
                    </td>
                    <td>
                        {{$sender['company']['title']}}
                    </td>
                    <td>
                        {{$sender['note']}}
                    </td>
                    <td>
                        {{$sender['sender_id']}}
                    </td>
                    <td>
                        断电(时间:{{\Carbon\Carbon::createFromTimestamp($sender['ischarging_update_time'])->toDateTimeString()}}
                        )
                    </td>
                    <td>

                    </td>
                </tr>
            @endforeach
        </table>
        @else 无
    @endif
    <br clear=all>
    <h3><b>附件10 预警情况统计及分析表</b></h3>
    @if(count($warning_analysis)>0)
        <table border="1" cellspacing="0" cellpadding="0" align="center" width="100%" style="text-align: center;">
            <tr>
                <td>
                    序号
                </td>
                <td>
                    单位名称
                </td>
                <td>
                    预警总数
                </td>
                <td>
                    高温预警
                </td>
                <td>
                    低温预警
                </td>
                <td>
                    断电预警
                </td>
                <td>
                    未及时处理预警
                </td>
                <td>
                    备注
                </td>
            </tr>
            @foreach($warning_analysis as $key=> $warning)
                <tr>
                    <td>
                        {{$key+1}}
                    </td>
                    <td>
                        {{$warning['title']}}
                    </td>
                    <td>
                        {{array_first($warning['warning_events'])['temp_count_all']+array_first($warning['warning_sender_events'])['count_power_off']}}
                    </td>
                    <td>
                        {{array_first($warning['warning_events'])['temp_count_high']??0}}
                    </td>
                    <td>
                        {{array_first($warning['warning_events'])['temp_count_low']??0}}
                    </td>
                    <td>
                        {{array_first($warning['warning_sender_events'])['count_power_off']??0}}

                    </td>
                    <td>
                        {{array_first($warning['warning_events'])['count_temp_unhandled']+array_first($warning['warning_sender_events'])['count_power_unhandled']}}
                    </td>
                    <td></td>
                </tr>
            @endforeach
        </table>
    @endif
    <br clear=all>


</div>
</body>


</html>