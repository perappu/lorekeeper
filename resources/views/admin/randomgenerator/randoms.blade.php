@extends('admin.layout')

@section('admin-title')
    Random Generators
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Random Generators' => 'admin/data/randomgenerator']) !!}

    <h1>Random Generators</h1>

    <p>This is a list of the different random generators you have created. They are considered "categories", which have objects that are randomly selected within them.</p>
    <p>Click "View" to add objects to that category.</p>

    <div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/randomgenerator/category/create') }}"><i class="fas fa-plus"></i> Create New Category</a></div>
    @if (!count($categories))
        <p>No categories found.</p>
    @else
        <table class="table table-sm">
            <thead>
                <th>Name</th>
                <th></th>
            </thead>
            <tr>
                @foreach ($categories as $category)
                        <td>
                            {!! $category->name !!}
                        </td>
                        <td class="text-right">
                            <a href="{{ url('admin/data/randomgenerator/category/view/' . $category->id) }}" class="btn btn-primary">View</a>
                            <a href="{{ url('admin/data/randomgenerator/category/edit/' . $category->id) }}" class="btn btn-primary">Edit</a>
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
