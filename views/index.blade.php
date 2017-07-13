<table class="{{$tableView->getTableClass()}}" id="{{$tableView->id()}}">
    <thead>
    <tr>
        @foreach($tableView->columns() as $column)

            <td>

                {{ $column->title() }}

            </td>

        @endforeach
    </tr>
    </thead>
    <tbody>

    @foreach($tableView->data() as $dataModel)
        <tr>

            @foreach($tableView->columns() as $column)
                <td>
                    {!!  $column->rowValue($dataModel)  !!}
                </td>
            @endforeach

        </tr>
    @endforeach
    </tbody>
</table>

@if($tableView->dataTable)

    @push(\Illuminate\Support\Facades\Config::get('tableView.css.stack_name'))

        @foreach(\Illuminate\Support\Facades\Config::get('tableView.css.paths') as $path)
            <link href="{{$path}}" rel="stylesheet">
        @endforeach

    @endpush

    @push(\Illuminate\Support\Facades\Config::get('tableView.js.stack_name'))
        @foreach(\Illuminate\Support\Facades\Config::get('tableView.js.paths') as $path)
            <script src="{{$path}}"></script>
        @endforeach

        <script>
            $(function () {
                ('#{{$tableView->id()}}').DataTable();
            });
        </script>

    @endpush

@endif