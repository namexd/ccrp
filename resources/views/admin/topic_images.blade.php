
<script>
    $(function () {
        $("#aaa").click(function () {
            bbb()
        });

        $("#button").click(function () {
            console.log(111);
            var text = $("#picture").val();
            $("#img").html("");
            for (var i = 0; i < text.split(",").length; i++) {
                $("#img").append('<img src="' + text.split(",")[i] + '" style="margin-bottom: 10px;"  alt="#" width="100%">');
            }

        });

        function bbb() {

            var fd = new FormData();
            var files = $('#filex')[0].files[0];
            fd.append('zzz', files);

            $.ajax({
                method: "POST",
                url: "http://ccrp-ms.coldyun.net/admin/sys-cooler-models/image/upload",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                enctype: 'multipart/form-data',
                async: false,

                cache: false,

                contentType: false,

                processData: false,
                data: fd,
                success: function (data1) {
                    $("input[name=picture]").val(data1);
                    $("#picture").val(data1);
                },

            });
        }
    })

</script>
<input type="file" id="filex">
<div class="row">
  <div class="col-xs-10">
    <input id="picture" style="width:100%">

  </div>
  <div class="col-xs-2">
    <div class="btn btn-default" id="aaa">上传获取地址</div>

  </div>
</div>


<hr>
<div class="row" id="img">
  @if(!empty($picture))
    <div class="col-lg-4">
      <img src="{{$picture}}" alt="#" width="100%">
    </div>
  @endif
</div>

