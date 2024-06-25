@extends('admin.layout')

@section('admin-title')
    Random Generators
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Random Generators' => 'admin/data/randomgenerator']) !!}

    <h1>Random Generators</h1>

    <p>This is a list of shops that users can use currency to purchase from.</p>

    <div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/randomgenerator/category/create') }}"><i class="fas fa-plus"></i> Create New Category</a></div>
    @if (!count($categories))
        <p>No categories found.</p>
    @else
        <table class="table table-sm shop-table">
            <tbody id="sortable" class="sortable">
                @foreach ($categories as $category)
                    <tr class="sort-item" data-id="{{ $category->id }}">
                        <td>
                            <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                            {!! $category->name !!}
                        </td>
                        <td class="text-right">
                            <a href="{{ url('admin/data/randomgenerator/category/edit/' . $shop->id) }}" class="btn btn-primary">Edit</a>
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
