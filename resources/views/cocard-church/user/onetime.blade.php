<div id="onetime" class="tab-pane fade in active">
    <div class=" text-center bg-lightblue">
        <div class="row text-center">
            @foreach($donationListsOnetime as $donationList)
                <div class="col-sm-6 col-md-6" >
                    <div class="thumbnail">
                        <h3>{{ $donationList->name }}</h3>
                        <h4>{{ $donationList->description }}</h4>
                        {!! csrf_field() !!}
                        <a href="{{ url('/organization/'.$slug.'/user/donate/donateonetime',$donationList->id) }}"><button type="button" class="btn btn-darkblue">Donate</button></a>
                    </div>
                </div>
              @endforeach
        </div>
    </div>
</div>
