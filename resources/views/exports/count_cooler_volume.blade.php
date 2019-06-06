<table>
    <tbody>
    <tr>
        <td rowspan="3">

            地区

        </td>
        <td rowspan="3">

            地区编码

        </td>
        <td rowspan="3">
            总数量
        </td>
        <td rowspan="3">
            总容积(升)
        </td>
        <td colspan="10">
            设备总体情况
        </td>
    </tr>
    <tr>
        <td colspan="2">
            正常
        </td>
        <td colspan="2">
            待修
        </td>
        <td colspan="2">
            报废

        </td>
        <td colspan="2">
            备用

        </td>
        <td colspan="2">

            迁出

        </td>
    </tr>
    <tr>
        <td>

            总数量

        </td>
        <td>

            总容积(升)

        </td>
        <td>

            总数量

        </td>
        <td>

            总容积(升)

        </td>
        <td>

            总数量

        </td>
        <td>

            总容积(升)

        </td>
        <td>

            总数量

        </td>
        <td>

            总容积(升)

        </td>
        <td>

            总数量

        </td>
        <td>

            总容积(升)

        </td>
    </tr>
    @foreach($coolers as $cooler)
    <tr>
      <td>{{$cooler->title}}</td>
      <td>{{$cooler->region_code}}</td>
      <td>{{$cooler->total_count}}</td>
      <td>{{$cooler->total_volume}}</td>
      <td>{{$cooler->total_count_status1}}</td>
      <td>{{$cooler->total_count_volume1}}</td>
      <td>{{$cooler->total_count_status2}}</td>
      <td>{{$cooler->total_count_volume2}}</td>
      <td>{{$cooler->total_count_status3}}</td>
      <td>{{$cooler->total_count_volume3}}</td>
      <td>{{$cooler->total_count_status4}}</td>
      <td>{{$cooler->total_count_volume4}}</td>
      <td>{{$cooler->total_count_status5}}</td>
      <td>{{$cooler->total_count_volume5}}</td>
    </tr>
        @endforeach
    </tbody>
</table>