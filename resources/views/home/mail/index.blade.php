@extends('home.layout')

@section('home-title')
    Mail
@endsection

@section('home-content')
    {!! breadcrumbs(['Mail' => 'mail']) !!}

    <h1>
        Mail
    </h1>

    <div class="text-right">
        <a href="{{ url('mail/new') }}" class="btn btn-success">New Message</a>
    </div>

    <ul class="nav nav-tabs mb-3" id="inboxType" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="inbox-tab" data-toggle="tab" href="#inbox" role="tab">Inbox</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="outbox-tab" data-toggle="tab" href="#outbox" role="tab">Outbox</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="mod-mail-tab" data-toggle="tab" href="#modMail" role="tab">Mod Mail</a>
        </li>
    </ul>
    <div class="tab-content" id="inboxTypeContent">
        <div class="tab-pane fade show active" id="inbox" role="tabpanel">
            @if (count($inbox))
                @include('home.mail._mail', ['mails' => $inbox, 'showSeen' => true])
            @else
                <p>Your inbox is empty.</p>
            @endif
        </div>
        <div class="tab-pane fade" id="outbox" role="tabpanel">
            @if (count($outbox))
                @include('home.mail._mail', ['mails' => $outbox, 'showSeen' => Settings::get('unseen_mail_indicator')])
            @else
                <p>Your outbox is empty.</p>
            @endif
        </div>
        <div class="tab-pane fade" id="modMail" role="tabpanel">
            <p class="alert alert-info">
                This mail is anonymously sent messages from moderators. {{ config('lorekeeper.mod_mail.allow_replies_to_staff') ? 'You can respond to staff mail.' : 'It cannot be responded to.' }}
            </p>
            @if (count($modMail))
                @include('home.mail._mail', ['mails' => $modMail, 'showSeen' => true])
            @else
                <p>No staff messages found.</p>
            @endif
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(function() {
            var hash = window.location.hash;
            hash && $('ul.nav a[href="' + hash + '"]').tab('show');

            $('.nav-tabs a').click(function(e) {
                $(this).tab('show');
                var scrollmem = $('body').scrollTop();
                window.location.hash = this.hash;
                $('html,body').scrollTop(scrollmem);
            });
        });
    </script>
@endsection
