<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="{{ asset('js/vendor.js') }}"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script type="text/javascript" src="{{ asset('js/moment-timezone.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('js/moment-recur.min.js')}}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.11/moment-timezone-with-data-2010-2020.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstimezonedetect/1.0.6/jstz.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="{{ asset('js/extensions/jquery-validation/src/core.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/extensions/jquery-validation/src/ajax.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/extensions/jquery-validation/src/additional/creditcard.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/extensions/bootbox.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('tinymce.4.3.2/tinymce.min.js')}}"></script>
<script type="text/javascript">
// search autocomplete
var src = "{{ url('organization/'.(isset($slug)? $slug : ' ').'/administrator/family/family-member/searchajax') }}";
// console.log(src);
$("#search_text").autocomplete({
    source: function(request, response) {
        $.ajax({
            type:'GET',
            url: src,
            data: {
                term : request.term
            },
            success: function(data) {
                response(data);
            }
        });
    },
    min_length: 3,
    select: function (event, ui) {
        event.preventDefault();
        this.value = ui.item.label;
        $('#user_id').val(ui.item.id);
        $('#first_name').val(ui.item.first_name);
        $('#last_name').val(ui.item.last_name);
        $('#middle_name').val(ui.item.middle_name);
        $('#birthdate').val(ui.item.birthdate);
        $('#age').val(ui.item.age);
        $('#marital_status').val(ui.item.marital_status);
        $('#gender').val(ui.item.gender);
        $('#img').val(ui.item.img);
        $('#email').val(ui.item.email);
        $('#name').val(ui.item.label);
    }
});

//couldnt load the script externally, so I just copy/pasted it here
$('.dataTz').each(function(){
   var currentElementDate = moment.utc($(this).data('date'));
   var offset = new Date().getTimezoneOffset();
   $(this).text(moment(currentElementDate).utcOffset(offset * -1));
});
</script>
<script type="text/javascript" src="https://cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>
<script src="{{ asset('js/components.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
