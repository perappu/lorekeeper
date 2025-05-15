<p>This is a default "game" which is purely for validating that the extension works. It consists entirely of a single button, which upon clicking submits the below score value (and handles it & granting currency accordingly).</p>
<p>Please be aware that for a savvy user, it's extremely easy to change the below value using their browser developer tools. You probably shouldn't be using this in production.</p>

<div class="form-group">
{!! Form::label('Score Value') !!}
{!! Form::text('score', $game->data->data['score'] ?? 0, ['class' => 'form-control']) !!}
</div>