<div class="col-md-6 col-6 mb-3 text-center ">
    @if (Auth::check() && Auth::user()->hasPower('edit_data'))
        <a data-toggle="tooltip" title="[ADMIN] Edit Fetch Quest"
            href="{{ url('admin/data/fetch-quests/edit/') . '/' . $fetch->id }}" class="mb-2 float-right"><i
                class="fas fa-crown"></i></a>
    @endif
    <div class="shop-name mt-1 h2 mb-0">
        {{ $fetch->name }}
    </div>
    @if ($fetch->has_image)
        <div class="shop-image">
            <img src="{{ $fetch->imageUrl }}" alt="{{ $fetch->name }}" />
        </div>
    @endif
    <h3> {{ $fetch->questgiver_name }} </h3>
    <div class="shop-text mt-1">
        {!! $fetch->parsed_description !!}
    </div>
    <h4>{{ $fetch->questgiver_name }} is currently looking for:</h4>
    @if ($fetch->fetchItem)
        @if ($fetch->fetchItem->imageUrl)
            <div>
                <a href="{{ $fetch->fetchItem->url }}"><img src="{{ $fetch->fetchItem->imageUrl }}" /></a>
            </div>
        @endif
        <div class="mt-1">
            <a href="{{ $fetch->fetchItem->url }}" class="h5 mb-0"> {{ $fetch->fetchItem->name }}</a>
        </div>
    @else
        <p>{{ $fetch->questgiver_name }} isn't looking for anything yet.</p>
    @endif

    <h4>{{ $fetch->questgiver_name }} Is Offering You:</h4>
    @if (isset($fetch->extras['reward_min_min']) &&
            isset($fetch->extras['reward_min_max']) &&
            isset($fetch->extras['reward_max_min']) &&
            isset($fetch->extras['reward_max_max']) &&
            $fetch->current_min &&
            $fetch->current_max &&
            $fetch->fetchCurrency)
        <div>{!! $fetch->fetchCurrency->display($fetch->current_min) !!} - {!! $fetch->fetchCurrency->display($fetch->current_max) !!}</div>
    @elseif(isset($fetch->extras['reward_min_min']) && isset($fetch->extras['reward_max_min']) && $fetch->fetchCurrency)
        <div>{!! $fetch->fetchCurrency->display($fetch->extras['reward_min_min']) !!} - {!! $fetch->fetchCurrency->display($fetch->extras['reward_max_min']) !!}</div>
    @else
        <p>There is no reward.</p>
    @endif

    <p><strong>Cooldown:</strong> {{ $fetch->cooldown }} minutes. </p>

    @if (
        (isset($fetch->extras['reward_min_min']) &&
            isset($fetch->extras['reward_min_max']) &&
            isset($fetch->extras['reward_max_min']) &&
            isset($fetch->extras['reward_max_max']) &&
            $fetch->current_min &&
            $fetch->current_max &&
            $fetch->fetchCurrency &&
            !Auth::user()->fetchCooldown($fetch->id)) ||
            (isset($fetch->extras['reward_min_min']) && isset($fetch->extras['reward_max_min']) && $fetch->fetchCurrency))
        <div class="text-right">
            <a href="#" class="btn btn-primary" id="submitButton">Give Item</a>
        </div>
    @elseif(Auth::user()->fetchCooldown($fetch->id))
        <i>You can complete this again {!! pretty_date(Auth::user()->fetchCooldown) !!}.</i>
    @else
    @endif
</div>

<div class="modal fade" id="confirmationModal-{{ $fetch->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title h5 mb-0">Confirm Submission</span>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>This will submit the fetch quest, remove the item asked for, and add currency to your account. Are
                    you sure?</p>
                {!! Form::open(['url' => 'fetch/new/' . $fetch->id]) !!}
                <button type="submit" class="btn btn-primary">Confirm</button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var $submitButton = $('#submitButton');
        var $confirmationModal = $('#confirmationModal');
        var $formSubmit = $('#formSubmit');

        $submitButton.on('click', function(e) {
            e.preventDefault();
            $confirmationModal.modal('show');
        });

        $formSubmit.on('click', function(e) {
            e.preventDefault();
            $submissionForm.submit();
        });

        $('.is-br-class').change(function(e) {
            console.log(this.checked)
            $('.br-form-group').css('display', this.checked ? 'block' : 'none')
        })
        $('.br-form-group').css('display', $('.is-br-class').prop('checked') ? 'block' : 'none')
    });
</script>
