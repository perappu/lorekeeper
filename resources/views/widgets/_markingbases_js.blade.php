<script>
    $(document).ready(function() {
        $('#markingList').find('.marking-select').selectize();

        $('#add-marking').on('click', function(e) {
            e.preventDefault();
            addMarkingRow();
        });

        $('.remove-marking').on('click', function(e) {
            e.preventDefault();
            removeMarkingRow($(this));
        })

        function addMarkingRow() {
            var $clone = $('.marking-row').clone();
            $('#markingList').append($clone);
            $clone.removeClass('marking-row');
            $clone.removeClass('hide');
            $clone.addClass('d-flex');
            $clone.find('.remove-marking').on('click', function(e) {
                e.preventDefault();
                removeMarkingRow($(this));
            })
            $clone.find('.marking-select').selectize();
        }

        function removeMarkingRow($trigger) {
            $trigger.closest('.marking').remove();
        }
        
        //If Chimera
        $('#is_chimera').on('change', function(e) {
            isChimera = $('#is_chimera').is(':checked');

            if (isChimera) {
                $('[connect="is_chimera"]').show();
            } else {
                $('[connect="is_chimera"]').hide();
            }
        });
    });
</script>
