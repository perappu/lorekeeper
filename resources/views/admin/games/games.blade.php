@extends('admin.layout')

@section('admin-title')
    Games
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Games' => 'admin/data/games']) !!}

    <h1>Games</h1>

    <p>This is a sortable list of your current games.</p>

    <div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/games/create') }}"><i class="fas fa-plus"></i> Create New Game</a></div>
    @if (!count($games))
        <p>No games found.</p>
    @else
        <table class="table table-sm shop-table">
            <tbody id="sortable" class="sortable">
                @foreach ($games as $game)
                    <tr class="sort-item" data-id="{{ $game->id }}">
                        <td>
                            <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                            {!! $game->displayName !!}
                        </td>
                        <td class="text-right">
                            <a href="{{ url('admin/data/games/edit/' . $game->id) }}" class="btn btn-primary">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        <div class="mb-4">
            {!! Form::open(['url' => 'admin/data/games/sort']) !!}
            {!! Form::hidden('sort', '', ['id' => 'sortableOrder']) !!}
            {!! Form::submit('Save Order', ['class' => 'btn btn-primary']) !!}
            {!! Form::close() !!}
        </div>
    @endif

@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.handle').on('click', function(e) {
                e.preventDefault();
            });
            $("#sortable").sortable({
                items: '.sort-item',
                handle: ".handle",
                placeholder: "sortable-placeholder",
                stop: function(event, ui) {
                    $('#sortableOrder').val($(this).sortable("toArray", {
                        attribute: "data-id"
                    }));
                },
                create: function() {
                    $('#sortableOrder').val($(this).sortable("toArray", {
                        attribute: "data-id"
                    }));
                }
            });
            $("#sortable").disableSelection();
        });
    </script>
@endsection
