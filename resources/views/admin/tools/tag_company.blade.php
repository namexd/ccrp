<!-- Button trigger modal -->
<button type="button" id="set_tag" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">
    批量设置标记
</button>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">批量设置标记</h4>
            </div>
            <div class="modal-body">
                <form >
                <div class="form-group  ">

                    <label for="tags" class="col-sm-2  control-label">标记</label>

                    <div class="col-sm-10">
                        <select name="tags[]" id="tags" class="form-control" style="width: 100%">
                        </select>
                    </div>
                </div>
                </form>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" id="save_tags" class="btn btn-primary">保存</button>
            </div>
        </div>
    </div>
</div>