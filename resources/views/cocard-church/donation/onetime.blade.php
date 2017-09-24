<div id="onetime" class="tab-pane fade in active">
    <div class=" text-center bg-lightblue">
        <div class="row text-center">
            @foreach($donationListsOnetime as $donationList)
            <form id="onetime-form" method="post" action="{{ url('/organization/'.$slug.'/donationonetime',$donationList->id) }}">
                <div class="col-sm-6 col-md-6" >
                    <div class="thumbnail">
                        <h3>{{ $donationList->name }}</h3>
                        <h4>{{ $donationList->description }}</h4>
                        {!! csrf_field() !!}
                        <button type="submit" class="btn btn-darkblue">Donate</button>
                    </div>
                </div>
            </form>
              @endforeach
        </div>
    </div>
</div>
