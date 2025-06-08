@isset($options['id'])
    @php($id = $options['id'])
@else
    @php($id = 'datatable-' . uniqid())
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
    @foreach($rows as $row)
        <tr>
            @foreach($row as $item)
                <td>{!! $item !!}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
<script>
$(function () {
    $('#{{ $id }}').DataTable({!! $options !!});

        var table = $('#{{ $id }}').DataTable();

        $(function() {
            $(this.api().table().container()).find('input').attr('autocomplete', 'off');
        });

        $('#{{ $id }} tbody').on('click', 'button', function (event) {
            var data = table.row($(this).parents('tr')).data();
            var queryString = $.param({ids: data[data.length - 1], email: data[0]});
            var row = table.row($(this).parents('tr')).node();

            if (event.target.id == 'attended') {
                $.get("/api/check-in?checkIn=yes&" + queryString, function (resp, status) {
                    console.log(resp);
                    if (resp['status'] == '200') {
                        $.admin.toastr.success(resp['message'], '', {positionClass: "toast-top-center"});
                        $(event.target).tooltip("hide");
                        $(event.target).remove();
                    table.cell(row, 5).data(0).draw(); // Update Owed Class Count
                    table.cell(row, 7).data(0).draw(); // Update Options
                    // Reload DataTable to reflect fresh cache
                    $.get('/api/refresh-expo-cache', function(newData) {
                        table.clear().rows.add(newData).draw();
                    });
                    } else {
                        $.admin.toastr.error(resp['message'], '', {positionClass: "toast-top-center"});
                    }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Check-in request failed:", textStatus, errorThrown);
                $.admin.toastr.error("Check-in failed: " + textStatus, '', {positionClass: "toast-top-center"});
                });
            } else if (event.target.id == 'print') {
                $.get("/api/check-in?print=print&" + queryString, function (resp, status) {
                    console.log(resp);
                    if (resp['status'] == 200) {
                        $.admin.toastr.success(resp['message'], '', {positionClass: "toast-top-center"});
                    $(event.target).prop('disabled', true); // Disable print button
                    } else {
                        $.admin.toastr.error(resp['message'], '', {positionClass: "toast-top-center"});
                    }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Print request failed:", textStatus, errorThrown);
                $.admin.toastr.error("Print failed: " + textStatus, '', {positionClass: "toast-top-center"});
                });
            }
        });
    });
</script>