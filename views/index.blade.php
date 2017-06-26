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