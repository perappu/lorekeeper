<ul>
    <li class="sidebar-header"><a href="{{ url('games') }}" class="card-link">Games</a></li>

    @if (Auth::check())
        <li class="sidebar-section">
            <div class="sidebar-section-header">Bank</div>
            @foreach (Auth::user()->getCurrencies(true) as $currency)
                <div class="sidebar-item pr-3">{!! $currency->display($currency->quantity) !!}</div>
            @endforeach
        </li>
    @endif

    <li class="sidebar-section">
        <div class="sidebar-section-header">Games</div>
        @foreach ($games as $categoryId => $categorygames)
            <div class="sidebar-section-header">{{ $categoryId !== '' ? $categories[$categoryId]->name : 'Miscellaneous' }}</div>
            @foreach ($categorygames as $game)
                <div class="sidebar-item"><a href="{{ $game->url }}" class="{{ set_active_full_url($game->url) }}">{{ $game->name }}</a></div>
            @endforeach
        @endforeach
    </li>
</ul>
