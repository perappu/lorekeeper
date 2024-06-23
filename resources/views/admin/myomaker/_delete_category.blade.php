@if ($category)
    {!! Form::open(['url' => 'admin/data/myomaker/category/delete/' . $category->id]) !!}

    <p>You are about to delete the category <strong>{{ $category->name }}</strong>. This is not reversible.</p>
    <p>Are you sure you want to delete <strong>{{ $category->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Category', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid category selected.
@endif
