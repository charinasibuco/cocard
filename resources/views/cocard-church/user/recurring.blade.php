<div id="recurring" class="tab-pane fade">
    <div class="text-center bg-lightblue">
        <div class="row text-center">
            @foreach($donationListsRecurring as $donationList)
                <div class="col-sm-6 col-md-6" >
                    <div class="thumbnail">
                        <h3>{{ $donationList->name }}</h3>
                        <h4>{{ $donationList->description }}</h4>
                        {!! csrf_field() !!}
                        <a href="{{ url('/organization/'.$slug.'/user/donate/donaterecurring',$donationList->id) }}"><button type="button" class="btn btn-darkblue">Donate</button></a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
