@extends('home.layout')

@section('home-title')
    Message (#{{ $mail->id }})
@endsection

@section('home-content')

    <style>
        [data-toggle="collapse"] i.collapsible:after {
            content: "\f139";
        }

        [data-toggle="collapse"].collapsed i.collapsible:after {
            content: "\f13a";
        }
    </style>

    {!! breadcrumbs(['Mail' => 'mail', ($mail->recipient_id == Auth::user()->id ? '(Inbox) ' : '(Outbox) ') . $mail->displayName . ' from ' . $mail->sender->displayName => $mail->viewUrl]) !!}

    <h1>
        Replying to {!! $mail->displayName !!}
    </h1>

    @if ($mail->parent)
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">Message History</h5>
        </div>
        <ul class="list-group list-group-flush">
                @php
                    // Get all ancestors in reverse order (oldest first)
                    $parents = [];
                    $parent = $mail->parent;
                    while ($parent) {
                        array_unshift($parents, $parent);
                        $parent = $parent->parent;
                    }
                @endphp

                @foreach ($parents as $index => $parent)
                    <li class="list-group-item card-header collapse-title collapsed" data-toggle="collapse" data-target="#message-{{ $index }}" aria-expanded="false" aria-controls="message-{{ $index }}">
                        <h6 class="font-weight-bold mb-0">"{{ $parent->subject }}" <small>{!! pretty_date($parent->created_at) !!} - {!! $parent->sender->displayName !!}</small> <i class="fa collapsible"></i></h6>
                    </li>
                        <li id="message-{{ $index }}" class="list-group-item collapse">
                            <div class="card-body">
                                {!! $parent->message !!}
                                <div class="text-right">
                                    <a href="{{ $parent->viewUrl }}"><u>View Message</u></a>
                                </div>
                            </div>
                        </li>
                @endforeach
        </ul>
    </div>
    @endif

    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0" style="text-transform: none;">"{{ $mail->subject }}"</h5>
        </div>
        <div class="card-body">
            <div class="row no-gutters">
                <div class="col-6 col-md-1 font-weight-bold">Sent:</div>
                <div class="col-6 col-md-11">{!! pretty_date($mail->created_at) !!}</div>
            </div>
            <div class="row no-gutters">
                <div class="col-6 col-md-1 font-weight-bold">To:</div>
                <div class="col-6 col-md-11">{!! $mail->recipient->displayName !!}</div>
            </div>
            <div class="row no-gutters">
                <div class="col-6 col-md-1 font-weight-bold">From:</div>
                <div class="col-6 col-md-11">{!! $mail->sender->displayName !!}</div>
            </div>
            <hr>
            {!! $mail->message !!}
        </div>
    </div>

    
    @if ($mail->children->count() > 0)    
        <div class="card mb-3">
            <ul class="list-group list-group-flush">
                <li class="list-group-item card-header font-weight-bold">
                    <span class="font-weight-bold mb-0">There are multiple replies to this message. <div class="btn btn-sm btn-faded h6 font-weight-bold collapsed mb-0" data-toggle="collapse" data-target="#child-messages" aria-expanded="false" aria-controls="child-messages">Show Replies <i class="fa collapsible"></i></div></span>
                </li>
                <li class="list-group-item p-0 collapse" id="child-messages">
                    @foreach ($mail->children as $index => $child)
                    <div class="list-group-item collapse-title collapsed" type="button" data-toggle="collapse" data-target="#child-message-{{ $index }}" aria-expanded="false" aria-controls="child-message-{{ $index }}">
                        <h6 class="font-weight-bold mb-0">"{{ $child->subject }}" <small>{!! pretty_date($child->created_at) !!} - {!! $child->sender->displayName !!} <i class="fa collapsible"></i></small></h6>
                    </div>
                    <div id="child-message-{{ $index }}" class="collapse">
                        <div class="card-body">
                            {!! $child->message !!}

                            <div class="text-right">
                                <a href="{{ $child->viewUrl }}"><u>View Reply</u></a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </li>
            </ul>
        </div>
    @endif

    @if (Auth::user()->id != $mail->sender_id)
        {!! Form::open(['url' => 'mail/new/' . $mail->id]) !!}

        <div class="card">
            <div class="card-header">
                <h3>Reply</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    @if ($mail->children->count() > 0)
                        {!! Form::label('message', 'Send New Reply') !!}
                    @else
                        {!! Form::label('message', 'Send Reply') !!}
                    @endif
                    {!! Form::textarea('message', null, ['class' => 'form-control wysiwyg']) !!}
                </div>

                <div class="text-right">
                    {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
                </div>
            </div>
        </div>

        {!! Form::close() !!}
    @endif

@endsection
