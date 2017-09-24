@extends('layouts.app')
@section('content')
    <div class="error-body">
        <div class="error-container">
            <div class="error-content">
                <div class="error-title">
                	<i class="fa fa-minus-circle fa-4x" style="color:#ff3333;" aria-hidden="true"></i>
	                <br>
	                	<span>Ooops!</span>
                        <h1>{{$error_message}}</h1>
	                	<h3>Please Contact Your Administrator.</h3>
                </div>
            </div>
        </div>
    </div>
@endsection