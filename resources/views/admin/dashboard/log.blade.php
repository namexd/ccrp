<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">快速入口</h3>

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
                <div class="form-horizontal">
                    <div class="fields-group">
                        <div class="form-group">
                            <label for="name" class="col-sm-2  control-label">单位名称</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <input type="text" id="company" name="title" value=""
                                           class="form-control name"
                                           placeholder="输入冷链的单位名称，回车检索">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-sm-2  control-label">探头</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <input type="number" id="collector" name="collector_sn" value=""
                                           class="form-control name"
                                           placeholder="输入探头编号，回车检索">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-sm-2  control-label">主机</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <input type="number" id="sender" name="sender_sn" value="" class="form-control name"
                                           placeholder="输入主机编号，回车检索">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script language="javascript">
                    $(document).ready(function () {
                        $("#company").bind('keypress', function (event) {
                            if (event.keyCode == "13") {
                                window.location.href = '/admin/ccrp/companies?title='+$(this).val()
                                $(this).attr('disabled','disabled')
                            }
                        })
                        $("#sender").bind('keypress', function (event) {
                            if (event.keyCode == "13") {
                                window.location.href = '/admin/ccrp/senders?sender_id='+$(this).val()
                                $(this).attr('disabled','disabled')
                            }
                        })
                        $("#collector").bind('keypress', function (event) {
                            if (event.keyCode == "13") {
                                window.location.href='/admin/ccrp/collectors?supplier_collector_id='+$(this).val();
                                $(this).attr('disabled','disabled')
                            }
                        })
                    });
                </script>
                {{--<h4 class="box-title">更新日志</h4>--}}
                @foreach($logs as $log)
                    {{--<tr>--}}
                    {{--<td width="120px">{{ $log['name'] }}</td>--}}
                    {{--<td>{{ $log['value'] }}</td>--}}
                    {{--</tr>--}}
                @endforeach
            </table>
        </div>
        <!-- /.table-responsive -->
    </div>
    <!-- /.box-body -->
</div>