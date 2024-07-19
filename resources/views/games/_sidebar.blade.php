<ul>
    <li class="sidebar-header"><a href="{{ url('shops') }}" class="card-link">Games</a></li>

    @if (Auth::check())
        <li class="sidebar-section">
            <div class="sidebar-section-header">My Currencies</div>
            @foreach (Auth::user()->getCurrencies(true) as $currency)
                <div class="sidebar-item pr-3">{!! $currency->display($currency->quantity) !!}</div>
            @endforeach
        </li>
    @endif

    <li class="sidebar-section">
        <div class="sidebar-section-header">Games</div>
        @foreach ($games as $game)
            <div class="sidebar-item"><a href="{{ $game->url }}" class="{{ set_active('game/' . $game->id) }}">{{ $game->name }}</a></div>
        @endforeach
    </li>
</ul>
