@extends('admin.layout')

@section('admin-title')
    MYO Maker Config
@endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'MYO Maker' => 'admin/data/myomaker', 'MYO Maker Categories' => 'myomaker']) !!}

<h1>MYO Maker</h1>

<p>This is a list of categories for the MYO maker. You can think of categories like "sections" -- all the images in the same category will be considered the same "section" of a character.</p>

<div class="text-right mb-3">
    <a class="btn btn-primary" href="{{ url('admin/data/myomaker/category/create') }}"><i class="fas fa-plus"></i> Create New Category</a>
</div>
@if (!count($categories))
    <p>No categories found.</p>
@else
    <table class="table table-sm category-table">
    <thead>
                <tr>
                    <th>Name</th>
                    <th></th>
                </tr>
            </thead>
        <tbody id="sortable" class="sortable">
            @foreach ($categories as $category)
                <tr class="sort-item" data-id="{{ $category->id }}">
                    <td>
                        {!! $category->name !!}
                    </td>
                    <td class="text-right">
                        <a href="{{ url('admin/data/myomaker/category/edit/' . $category->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
</table>
@endif

@endsection