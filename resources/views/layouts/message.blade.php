@if( Session::has('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <span><b> Success - </b>{!! session('status') !!}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div> 
@endif
@if( Session::has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <span><b> ERROR - </b>{!! session('error') !!}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div> 
@endif