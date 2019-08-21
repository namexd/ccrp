<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">统计信息</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>

    <!-- /.box-header -->
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-striped">

                @foreach($stats as $stat)
                <tr>
                    <td width="00px">{{ $stat['name'] }}</td>
                    <td><span class="label label-danger">{{ $stat['value'] }}</span> </td>
                    <td>
                        @if($stat['link'])
                            <a href="/admin/{{ $stat['link'] }}">查看</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        <!-- /.table-responsive -->
    </div>
    <!-- /.box-body -->
</div>