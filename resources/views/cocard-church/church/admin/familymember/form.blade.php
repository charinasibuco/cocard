
@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('style')
<link href="http://demo.expertphp.in/css/jquery.ui.autocomplete.css" rel="stylesheet">
@endsection
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3 class="permissiontitle">{{ $action_name }} Family Member</h3>
            </div>
        </div>
        <div class="table-main panel panel-default">
            @if(count($errors) > 0)
            <!-- <div class="alert alert-warning alert-dismissible">Error: Highlight fields are required! <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div> -->
            @foreach ($errors->all() as $error)

            <div class="alert alert-warning alert-dismissible">{{ $error }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>

            @endforeach
            @endif
            @if(Session::has('message'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {{ Session::get('message') }}
            </div>
            @endif
            <form enctype="multipart/form-data" method="POST" action="{{ $action }}">
                <input type="hidden" class="form-control" name="slug" value="{{ $organization->url }}">
                <input type="hidden" class="form-control" id="count" name="count" value="{{$count}}">
                @include('cocard-church.church.admin.familymember.form-template')
                {!! csrf_field() !!}
                @if($action_name == "Add")
                <button type="button" class="btn btn-default add-family-member" data-add_item_url="{{ url('/organization/'.$slug.'/family/'. $family_id.'/addFamilyMember/'.$count) }}">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </button>
                @endif
                <div class="clearfix">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-darkblue">
                            Submit
                        </button>
                        <a href="{{ url('organization/'. $slug .'/administrator/family/'.$family_id) }}" class="btn btn-red">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
$(document).ready(function() {
    //datepicker

    function datepicker(){
        $('.bdatepicker').datetimepicker({
            format: 'M/D/YYYY',
            viewMode: 'years'
        });
    }
    //hide close button if count 0 fieldset
    $("#close_0").css('display', 'none');
    //count
    var one_time_count = 0;
    //source of data for searching members church goer
    var src = "{{ url('organization/'.(isset($slug)? $slug : ' ').'/administrator/family/family-member/searchajax') }}";

            // search get data ajax and autofill fields
            function addListener(idNum) {
                    $(".search_text_"+idNum).autocomplete({
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
                   min_length: 5,
                  
                   select: function (event, ui) {
                       event.preventDefault();
                       this.value = ui.item.label;
                       $('#user_id_'+idNum).val(ui.item.id);
                       $('#first_name_'+idNum).val(ui.item.first_name);
                       $('#last_name_'+idNum).val(ui.item.last_name);
                       $('#middle_name_'+idNum).val(ui.item.middle_name);
                       $('#membdate_'+idNum).val(ui.item.birthdate);
                       $('#age_'+idNum).val(ui.item.age);
                       $('#marital_status_'+idNum).val(ui.item.marital_status);
                       $('#gender_'+idNum).val(ui.item.gender);
                       $('#img_'+idNum).val(ui.item.img);
                       $('#email_'+idNum).val(ui.item.email);
                       $('#name_'+idNum).val(ui.item.label);
                   }
               });
            }
            //closing form family member
            function deleteFamilyMember(){
              $(".delete-family-member").click(function(){
                var fieldset = $(this).closest("fieldset");
                fieldset.hide("fast",function(){ fieldset.remove(); });
              });
            };

            //start search
            addListener(one_time_count);




            $('.add-family-member').on('click', function(){
                $.post($(this).data("add_item_url"),{_token:$(this).data("csrf_token"),count: one_time_count}).done(function(data){
                    $('#count').val(one_time_count);
                    $(data).insertBefore( $( ".add-family-member" ) );
                    one_time_count++;
                    addListener(one_time_count);
                    $(".delete-family-member").show();
                    $("#close_0").hide();
                    deleteFamilyMember();
                    datepicker();

                });
            });



});
</script>
@endsection
