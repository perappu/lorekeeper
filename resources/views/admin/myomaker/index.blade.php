@extends('admin.layout')

@section('admin-title')
    MYO Maker Config
@endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'MYO Maker' => 'myomaker']) !!}

<h1>MYO Maker</h1>

<p>This is a list of images for the MYO maker. You can think of categories like "sections" -- all the images in the same category will be considered the same "section" of a character.</p>

<div class="text-right mb-3">
<a class="btn btn-primary" href="{{ url('admin/data/myomaker/category') }}"><i class="fas fa-folder"></i> MYO Maker Categories</a>
    <a class="btn btn-primary" href="{{ url('admin/data/myomaker/category/create') }}"><i class="fas fa-plus"></i> Create New Category</a>
    <a class="btn btn-primary" href="{{ url('admin/data/myomaker/create') }}"><i class="fas fa-plus"></i> Create New Image</a>
</div>
@if (!count($images))
    <p>No images found.</p>
@else
    <table class="table table-sm category-table">
    <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th></th>
                </tr>
            </thead>
        <tbody id="sortable" class="sortable">
            @foreach ($images as $image)
                <tr class="sort-item" data-id="{{ $image->id }}">
                    <td>
                        {!! $image->name !!}
                    </td>
                    <td>
                        {!! $image->category->name !!}
                    </td>
                    <td>
                        {!! $image->image !!}
                    </td>
                    <td class="text-right">
                        <a href="{{ url('admin/data/myomaker/edit/' . $image->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
</table>
@endif

@endsection