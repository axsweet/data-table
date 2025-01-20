@isset($options['id'])
    @php($id = $options['id'])
@else
    @php($id = 'history-' . uniqid())
@endisset

<table id="{{ $id }}" {!! $attributes !!} style="width:100%">
    <thead>
    <tr>
        @foreach($headers as $header)
            <th>{{ $header }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>

    </tbody>
</table>

<script>

    $(function () {
        $('#{{ $id }}').DataTable(
                {!! $options !!}
        );
        var table = $('#{{ $id }}').DataTable();

        $('#{{ $id }} tbody').on('click', 'button', function (event) {

            var data = table.row($(this).parents('tr')).data();
            var queryString = $.param({ids: data[data.length - 1], email: data[1]});
            var row = table.row($(this).parents('tr')).node();
            //  alert(event.target.id+" and "+$(event.target).attr('class')+" and "+ data[0]);

            if (event.target.id == 'undo') {
                $.get("/api/check-in?undo=yes&" + queryString, function (resp, status) {
                    console.log(resp);
                    if (resp['status'] == '200') {
                        $.admin.toastr.success(resp['message'], '', {positionClass: "toast-top-center"});
                        $(event.target).tooltip("hide")
                        $(event.target).remove();
                        table.cell(row, 5).data(0).draw();
                        table.cell(row, 7).data(0).draw();
                     //   $.pjax.reload('#pjax-container');
                    } else {
                        $.admin.toastr.error(resp['message'], '', {positionClass: "toast-top-center"});
                    }
                });
            } else if (event.target.id == 'print') {
                $.get("/api/check-in?print=print&" + queryString, function (resp, status) {
                    console.log(resp);
                    //  alert(resp.message);
                    if (resp['status'] == 200) {
                        $.admin.toastr.success(resp['message'], '', {positionClass: "toast-top-center"});
                    } else {
                        $.admin.toastr.error(resp['message'], '', {positionClass: "toast-top-center"});
                    }
                });
            }
        });
    });
</script>
