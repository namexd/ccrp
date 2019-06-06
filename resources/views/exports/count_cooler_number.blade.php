<table >
    <thead>
    <tr>
        <th>地区</th>
        <th>地区编码</th>
        <th>冷藏车（辆）</th>
        <th>疫苗运输车（辆）</th>
        <th>普通冷库（座）</th>
        <th>低温冷库（座）</th>
        <th>普通冰箱（台）</th>
        <th>冰衬冰箱（台）</th>
        <th>低温冰箱（台）</th>
        <th>冷藏箱（个）</th>
        <th>发电机（个）</th>
        <th>冷藏包（个）</th>
        <th>台式小冰箱（台）</th>
    </tr>
    </thead>
    <tbody>
    @foreach($coolers as $cooler)
        <tr>
            <td>{{ $cooler->title }}</td>
            <td>{{ $cooler->region_code }}</td>
            <td>{{ $cooler->type_7 }}</td>
            <td>{{ $cooler->type_14 }}</td>
            <td>{{ $cooler->type_5 }}</td>
            <td>{{ $cooler->type_6 }}</td>
            <td>{{ $cooler->type_3 }}</td>
            <td>{{ $cooler->type_13 }}</td>
            <td>{{ $cooler->type_4 }}</td>
            <td>{{ $cooler->type_1 }}</td>
            <td>{{ $cooler->type_16 }}</td>
            <td>{{ $cooler->type_17 }}</td>
            <td>{{ $cooler->type_12 }}</td>
        </tr>
    @endforeach
    </tbody>
</table>