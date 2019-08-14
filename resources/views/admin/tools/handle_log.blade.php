<a data-id="{{$model->id}}" title="点此记录" href="javascript:;" class="grid-check-row" data-toggle="modal" data-target="#myModal{{$model->id}}"><i class="fa fa-hand-o-up" ></i>点此记录</a>


<!-- Modal -->
<div class="modal fade" id="myModal{{$model->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">客户联系记录-({{$model->company->title}})</h4>
            </div>
            <form action="/admin/human_check_logs" method="post">
            <div class="modal-body">

                    <div class="form-group">
                        <label for="recipient-name" class="control-label">事件:</label>
                        <input readonly type="text" name="check_name" class="form-control" id="recipient-name" value="{{$model->check->name}}">
                    </div>
                    <div class="form-group">
                        <label for="record" class="control-label">备注:</label>
                        <textarea class="form-control" name="record" id="record"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="inlineRadioOptions" class="control-label">对外展示:</label>
                        <label class="radio-inline">
                            <input type="radio" name="is_out" id="inlineRadio1" value="1" checked="checked"> 是
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="is_out" id="inlineRadio2" value="0"> 否
                        </label>
                    </div>
                <div class="form-group">
                    <label for="inlineRadioOptions" class="control-label">处理状态:</label>
                    @foreach(\App\Models\CheckResult::HUMAN_CHECK_STATUS as  $key=> $result)
                    <label class="radio-inline">
                        <input type="radio" name="result_status"  value="{{$key}}" @if($key==$model->human_status)  checked="checked"@endif> {{$result}}
                    </label>
                    @endforeach
                </div>
                <input type="hidden" name="company_id" value="{{$model->object_value}}">
                <input type="hidden" name="result_id" value="{{$model->id}}">
                <input type="hidden" name="user_id" value="{{\Encore\Admin\Facades\Admin::user()->id}}">
                {{csrf_field()}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="submit" class="btn btn-primary">保存</button>
                <div style="text-align: left">
                <button class="btn btn-default history" type="button">
                    处理历史 <span class="badge">{{$lists->count()}}</span>
                </button>
                </div>
                <table class="table" id="table_history" style="text-align: left;display: none">
                    <tr>
                        <td>巡检事件</td>
                        <td>备注</td>
                        <td>处理人</td>
                        <td>处理时间</td>
                    </tr>
                    @foreach($lists as $list)
                   <tr>
                       <td>{{$list->check_name}}</td>
                       <td>{{$list->record}}</td>
                       <td>{{$list->user->name}}</td>
                       <td>{{$list->created_at}}</td>
                   </tr>
                        @endforeach
                </table>
            </div>
            </form>
        </div>
    </div>
</div>