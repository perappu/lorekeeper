@extends('admin.layout')

@section('admin-title')
    Random Generators
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Random Generators' => 'admin/data/random']) !!}

    <h1>Random Generators</h1>

    <p>This is a list of the different random generators you have created.</p>
    <p>Click "View" to add objects to that generator.</p>

    <div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/random/generator/create') }}"><i class="fas fa-plus"></i> Create New Generator</a></div>
    @if (!count($generators))
        <p>No generators found.</p>
    @else
        <table class="table table-sm">
            <thead>
                <th>Name</th>
                <th></th>
            </thead>
            <tr>
                @foreach ($generators as $generator)
                        <td>
                            <a href="{{ $generator->url }}">{!! $generator->name !!}</a>
                        </td>
                        <td class="text-right">
                            <a href="{{ url('admin/data/random/generator/view/' . $generator->id) }}" class="btn btn-primary">View</a>
                            <a href="{{ url('admin/data/random/generator/edit/' . $generator->id) }}" class="btn btn-primary">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    @endif

@endsection

@section('scripts')
    @parent
@endsection
