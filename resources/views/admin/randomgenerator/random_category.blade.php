@extends('admin.layout')

@section('admin-title')
    Random Generators
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Random Generators' => 'admin/data/randomgenerator', 'Random Category' => 'admin/data/randomgenerator/view']) !!}

    <h1>Random Generator - {!! $category->name !!}</h1>

    <p>This is a list of the different random generators you have created. They are considered "categories", which have objects that are randomly selected within them.</p>
    <p>Click "View" to add objects to that category.</p>

    <div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/randomgenerator/create') }}"><i class="fas fa-plus"></i> Create New Object</a></div>
    @if (!count($category->objects))
        <p>No objects found.</p>
    @else
        <table class="table table-sm">
            <thead>
                <th>Value</th>
                <th></th>
            </thead>
            <tr>
                @foreach ($category->objects as $object)
                        <td>
                            {!! $object->name !!}
                        </td>
                        <td class="text-right">
                            <a href="{{ url('admin/data/randomgenerator/view/' . $category->id) }}" class="btn btn-primary">View</a>
                            <a href="{{ url('admin/data/randomgenerator/edit/' . $category->id) }}" class="btn btn-primary">Edit</a>
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
