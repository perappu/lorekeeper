<div class="mb-4 logs-table">
    <div class="logs-table-header">
        <div class="row no-gutters">
            <div class="col-6 col-md-3">
                <div class="logs-table-cell">Subject</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="logs-table-cell">Message</div>
            </div>
            <div class="col-6 {{ $showSeen ? 'col-md-3' : 'col-md-5' }}">
                <div class="logs-table-cell">From</div>
            </div>
            @if($showSeen)
                <div class="col-2 col-md-2">
                    <div class="logs-table-cell">Seen</div>
                </div>
            @endif
            <div class="{{ $showSeen ? 'col-4' : 'col-6' }} col-md-1">
                <div class="logs-table-cell">Details</div>
            </div>
        </div>
    </div>
    <div class="logs-table-body">
        @foreach ($mails as $mail)
            <div class="logs-table-row">
                <div class="row no-gutters flex-wrap">
                    <div class="col-6 col-md-3">
                        <div class="logs-table-cell">{!! $mail->displayName !!}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="logs-table-cell">
                            <span class="ubt-texthide">{{ Illuminate\Support\Str::limit(strip_tags($mail->message), 50, $end = '...') }}</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 {{ $showSeen ? 'col-md-3' : 'col-md-5' }}">
                        <div class="logs-table-cell">{!! $mail->sender?->displayName !!} {!! pretty_date($mail->created_at) !!}</div>
                    </div>
                    @if($showSeen)
                        <div class="col-2 col-md-2">
                            <div class="logs-table-cell">{!! $mail->seen ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</div>
                        </div>
                    @endif
                    <div class="{{ $showSeen ? 'col-4' : 'col-6' }} col-md-1">
                        <div class="logs-table-cell">
                            <a href="{{ $mail->viewUrl }}" class="btn btn-primary btn-sm py-0 px-1">Details</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<div class="text-center mt-4 small text-muted">{{ $mail->count() }} result{{ $mail->count() == 1 ? '' : 's' }} found.</div>
a