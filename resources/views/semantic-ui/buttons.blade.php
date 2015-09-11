<div class="ui buttons buttons-votee">
    <button id="button-like"
       data-url="{{ route('votee.up') }}"
       data-id="{{ $content['id'] }}"
       class="ui button button-vote button-vote-up {{ ($vote && $vote['is_up']) ? 'green' : '' }}">
        <i class="icon thumbs outline up"></i>
        <span class="total-up"> {{ $content['vote_up'] }}</span>
    </button>
    <button id="button-dislike"
       data-url="{{ route('votee.down') }}"
       data-id="{{ $content['id'] }}"
       class="ui button button-vote button-vote-down {{ ($vote && $vote['is_down']) ? 'red' : '' }}">
        <i class="icon thumbs outline down"></i>
        <span class="total-down"> {{ $content['vote_down'] }}</span>
    </button>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        $('.buttons-votee').on('click', '.button', function () {
            var btn = $(this);
            btn.addClass('loading');
            btn.siblings().andSelf().addClass('disabled').attr('disabled', true);

            $.ajax({
                url: btn.data('url'),
                type: "POST",
                dataType: 'json',
                data: {id: btn.data('id')}
            })
                    .done(function (response, status, xhr) {
                        if (xhr.status == 200) {
                            btn.parent().find('.total-up').html(response.data.vote_up);
                            btn.parent().find('.total-down').html(response.data.vote_down);
                            btn.parent().find('.button-vote').removeClass('green red');

                            switch (response.data.value) {
                                case {{ config('votee.values.up') }}:
                                    btn.parent().find('.button-vote-up').addClass('green');
                                    break;
                                case {{ config('votee.values.down') }}:
                                    btn.parent().find('.button-vote-down').addClass('red');
                                    break;
                                case {{ config('votee.values.neutral') }}:
                                    break;
                            }
                        } else {
                            btn.popup({html: xhr.responseJSON.message}).popup('show');
                        }
                    })
                    .fail(function (xhr) {
                        btn.popup({content: xhr.responseJSON.message}).popup('show');
                    })
                    .always(function () {
                        btn.siblings().andSelf().removeClass('disabled').removeAttr('disabled');
                        btn.removeClass('loading');
                    });
        });

        $('.button-vote').popup({
            on: 'manual'
        });

    })
</script>
