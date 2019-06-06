<table>
    <tbody>
    <tr>
        <td rowspan="3" >

               冷链设备名称

        </td>
        <td rowspan="3" >

               总数量

        </td>
        <td  colspan="2" rowspan="2" >

               总容积

        </td>
        <td colspan="3" >

               正常

        </td>
        <td colspan="3" >

               待修

        </td>
        <td  colspan="3" >

               报废

        </td>
        <td  colspan="3" >

               备用

        </td>
        <td  colspan="3" >

               迁出

        </td>
    </tr>
    <tr>
        <td rowspan="2" >

               总数量

        </td>
        <td colspan="2" >

               总容积

        </td>
        <td rowspan="2" >

               总数量

        </td>
        <td colspan="2" >

               总容积

        </td>
        <td rowspan="2" >

               总数量

        </td>
        <td colspan="2" >

               总容积

        </td>
        <td rowspan="2" >

               总数量

        </td>
        <td colspan="2" >

               总容积

        </td>
        <td rowspan="2" >

               总数量

        </td>
        <td colspan="2" >

               总容积

        </td>
    </tr>
    <tr>
        <td>

               冷冻

        </td>
        <td>

               冷藏

        </td>
        <td>

               冷冻

        </td>
        <td>

               冷藏

        </td>
        <td>

               冷冻

        </td>
        <td>

               冷藏

        </td>
        <td>

               冷冻

        </td>
        <td>

               冷藏

        </td>
        <td>

               冷冻

        </td>
        <td>

               冷藏

        </td>
        <td>

               冷冻

        </td>
        <td>

               冷藏


        </td>
    </tr>
    @foreach($coolers as $cooler)
    <tr>
        <td>{{$cooler->title}}</td>
        <td>{{$cooler->region_code}}</td>
        <td>{{$cooler->total_count_ld_volume}}</td>
        <td>{{$cooler->total_count_lc_volume}}</td>
        <td>{{$cooler->total_count_status1}}</td>
        <td>{{$cooler->total_count_ld_volume1}}</td>
        <td>{{$cooler->total_count_lc_volume1}}</td>
        <td>{{$cooler->total_count_status2}}</td>
        <td>{{$cooler->total_count_ld_volume2}}</td>
        <td>{{$cooler->total_count_lc_volume2}}</td>
        <td>{{$cooler->total_count_status3}}</td>
        <td>{{$cooler->total_count_ld_volume3}}</td>
        <td>{{$cooler->total_count_lc_volume3}}</td>
        <td>{{$cooler->total_count_status4}}</td>
        <td>{{$cooler->total_count_ld_volume4}}</td>
        <td>{{$cooler->total_count_lc_volume4}}</td>
        <td>{{$cooler->total_count_status5}}</td>
        <td>{{$cooler->total_count_ld_volume5}}</td>
        <td>{{$cooler->total_count_lc_volume5}}</td>
    </tr>
        @endforeach
    </tbody>
</table>