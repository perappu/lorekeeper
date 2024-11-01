<li class="list-group-item">
    <a class="card-title h5 collapse-title" data-toggle="collapse" href="#redeemTheme">Redeem Theme</a>
    <div id="redeemTheme" class="collapse">
        {!! Form::hidden('tag', $tag->tag) !!}

        <p>
            This action is not reversible. This will redeem a random theme from the following list to your account.
            <br>
            Please be careful to not select a higher quantity to redeem than options listed below.
        </p>

        <p class="mb-0"><strong>Possible Results:</strong></p>
        <div class="row mb-2">
            @if (is_array($tag->getData()) && count($tag->getData()))
                @foreach ($tag->getData() as $loot)
                    <div class="col-md-3" style="{{ Auth::user()->hasTheme($loot->rewardable_id) ? 'text-decoration: line-through; opacity:0.5;' : '' }}">{!! App\Models\Theme::find($loot->rewardable_id)->displayName !!}</div>
                @endforeach
            @else
                @foreach (App\Models\Theme::orderBy('name')->where('is_user_selectable', 0)->get() as $loot)
                    <div class="col-md-3" style="{{ Auth::user()->hasTheme($loot->id) ? 'text-decoration: line-through; opacity:0.5;' : '' }}">{!! $loot->displayName !!}</div>
                @endforeach
            @endif
        </div>
        <p>
            Crossed out results above mean that you already have them.
            <br>
            If there are no themes that aren't crossed out, you have all themes that can be found via this rewarding item!
        </p>

        <div class="text-right">
            {!! Form::button('Redeem', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'act', 'type' => 'submit']) !!}
        </div>
    </div>
</li>
