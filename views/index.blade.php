<div class="tableView-wrapper">

    @if(count($tableView->searchableFields()))

        <form name="searchForm" class="pull-right" method="GET">

            <div class="pull-right col-sm-3">
                <div class="input-group">
                    <input type="text"
                           placeholder="Search"
                           name="q"
                           class="input-sm form-control"
                           value="{{ Request::get('q') }}">
                    <span class="input-group-btn">
                            <button type="button" class="btn btn-sm btn-primary"> Search </button>
                    </span>
                </div>
            </div>

        </form>

    @endif

    <table class="{{$tableView->getTableClass()}}" id="{{$tableView->id()}}" style="width: 100%">
        <thead>
        <tr>
            @if($tableView->hasChildDetails())
                <th></th>
            @endif
            @foreach($tableView->columns() as $column)

                <th>

                    {{ $column->title() }}

                </th>

            @endforeach
        </tr>
        </thead>
        <tbody class="{{ $tableView->geTableBodyClass() }}">

        @forelse($tableView->data() as $dataModel)
            <tr
                    @if($tableView->hasChildDetails() && $tableView->dataTable)
                    data-child-content="{{ $tableView->getChildDetails($dataModel) }}"
            @endif
            @foreach($tableView->getTableRowAttributes($dataModel) as $attribute => $value)
                {{$attribute}}='{{$value}}'
            @endforeach
            >
            @if($tableView->hasChildDetails())
                <td class="details-control">
                    <svg style="cursor: pointer" width=14 height=14 aria-hidden="true" data-prefix="fas"
                         data-icon="angle-down"
                         class="svg-inline--fa fa-angle-down fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 320 512">
                        <path fill="currentColor"
                              d="M143 352.3L7 216.3c-9.4-9.4-9.4-24.6 0-33.9l22.6-22.6c9.4-9.4 24.6-9.4 33.9 0l96.4 96.4 96.4-96.4c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9l-136 136c-9.2 9.4-24.4 9.4-33.8 0z"></path>
                    </svg>
                    <svg style="display: none; cursor: pointer" width=14 height=14 aria-hidden="true" data-prefix="fas"
                         data-icon="angle-up"
                         class="svg-inline--fa fa-angle-up fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 320 512">
                        <path fill="currentColor"
                              d="M177 159.7l136 136c9.4 9.4 9.4 24.6 0 33.9l-22.6 22.6c-9.4 9.4-24.6 9.4-33.9 0L160 255.9l-96.4 96.4c-9.4 9.4-24.6 9.4-33.9 0L7 329.7c-9.4-9.4-9.4-24.6 0-33.9l136-136c9.4-9.5 24.6-9.5 34-.1z"></path>
                    </svg>
                </td>
            @endif
            @foreach($tableView->columns() as $column)
                <td class="{{ $column->cssClasses() }}">
                    {!!  $column->rowValue($dataModel)  !!}
                </td>
                @endforeach
                </tr>
                @if($tableView->hasChildDetails() && ! $tableView->dataTable)
                    <tr style="display: none">
                        <td colspan="{{ count($tableView->columns()) + 1 }}">
                            {!! $tableView->getChildDetails($dataModel) !!}
                        </td>
                    </tr>
                @endif
                @empty

                    <tr>
                        <td colspan="{{ count($tableView->columns()) + ($tableView->hasChildDetails() ? 1 : 0) }}">
                            <div>
                                <p>
                                    <b>{{  __('No data matched the given criteria.')  }}</b>
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse

        </tbody>
    </table>
</div>

<div class="tableView-pagination">
    @if($tableView->hasPagination())
        {{ $tableView->data()->links() }}
    @endif
</div>

@if($tableView->dataTable)

    @push(config('tableView.dataTable.css.stack_name'))

        @foreach(config('tableView.dataTable.css.paths',[]) as $path)
            <link href="{{$path}}" rel="stylesheet">
        @endforeach

    @endpush

    @push(config('tableView.dataTable.js.stack_name'))
        @foreach(config('tableView.dataTable.js.paths',[]) as $path)
            <script src="{{$path}}"></script>
        @endforeach

        <script>
            $(function () {
                var table = $('#{{$tableView->id()}}').DataTable({
                    "bSort": true,
                    "aaSorting": [],
                    "scrollX": true,
                    "search": {
                        "search": '{{ Request::get('q') }}'
                    }
                });
                // Add event listener for opening and closing details
                $(document).on('click', '.details-control', function () {
                    var tr = $(this).closest('tr');
                    var row = table.row(tr);

                    if (row.child.isShown()) {
                        // This row is already open - close it
                        row.child.hide();
                        tr.removeClass('shown');
                        $(this).find('[data-icon="angle-down"]').show()
                        $(this).find('[data-icon="angle-up"]').hide()

                    } else {
                        // Open this row
                        row.child(tr.data('child-content')).show();
                        tr.addClass('shown');
                        $(this).find('[data-icon="angle-down"]').hide()
                        $(this).find('[data-icon="angle-up"]').show()
                    }
                });
            });
        </script>

    @endpush

@elseif($tableView->hasChildDetails())
    @push(config('tableView.dataTable.js.stack_name'))
        <script>
            $(function () {
                $(document).on('click', '.details-control', function (e) {
                    e.preventDefault()
                    var tr = $(this).closest('tr');
                    tr.next().toggle();


                    if (tr.next().is(':visible')) {
                        $(this).find('[data-icon="angle-down"]').hide()
                        $(this).find('[data-icon="angle-up"]').show()
                    } else {
                        $(this).find('[data-icon="angle-down"]').show()
                        $(this).find('[data-icon="angle-up"]').hide()
                    }


                });
            })


        </script>

    @endpush
@endif
