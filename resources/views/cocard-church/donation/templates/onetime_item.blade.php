<div class="container text-center bg-lightblue">
    <div class="row text-center">
        @foreach($donationLists as $donationList)
            <div class="col-sm-6 col-md-6" >
                <div class="thumbnail">
                    <h3>{{ $donationList->name }}</h3>
                    <h4>{{ $donationList->description }}</h4>
                    <button type="button" class="btn btn-darkblue" data-toggle="modal" data-target=".bs-example-modal-sm">Donate</button>
                </div>
            </div>
        @endforeach
    </div>
</div>