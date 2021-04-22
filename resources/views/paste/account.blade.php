@extends('default')

@section('pagetitle') My Account | DraftBin @endsection

@section('navbar')
<li class="nav-item"><a href="/" class="nav-link">Home</a></li>
@if (Auth::check())
<li class="nav-item"><a href="/users/dashboard" class="nav-link">Dashboard</a></li>
<li class="nav-item active"><a href="/users/account" class="nav-link">My Account</a></li>
<li class="nav-item"><a href="/logout" class="nav-link">Logout <i>({{ Auth::user()->name }})</i></a></li>
@else
<li class="nav-item"><a href="/login" class="nav-link">Login</a></li>
<li class="nav-item"><a href="/register" class="nav-link">Register</a></li>
@endif
@endsection

@section('content')<div class="container">
      <div class="row">
        <h2 class="text-center display-4">Account managment</h2>
      </div>
      @if ( count( $errors ) > 0 )
        <div class="alert alert-danger">
          @foreach ($errors->all() as $error)
            {{ $error }}<br>
          @endforeach
        </div>
      @endif
      <br />
      <form class="form-horizontal" role="form" method="POST" action="/users/account">
        {{ csrf_field() }}

        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
          <label for="name" class="col-md-4 control-label">Name</label>

          <div class="col-md-3">
            <input id="name" type="text" class="form-control" name="name" maxlength="30" value="{{ $user->name }}" >
          </div>
        </div>
        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
          <label for="email" class="col-md-4 control-label">E-mail Address</label>

          <div class="col-md-3">
            <input id="email" type="text" class="form-control" name="email" maxlength="50" value="{{ $user->email }}" >
          </div>
        </div>
        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
          <label for="password" class="col-md-4 control-label">New password</label>
          <div class="col-md-3">
            <input id="password" type="password" class="form-control" name="password" maxlength="100" placeholder="Leave blank if password unchanged" >
          </div>
        </div>
        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
          <label for="passwordconfirm" class="col-md-4 control-label">Confirmation</label>
          <div class="col-md-3">
            <input id="passwordconfirm" type="password" class="form-control" name="passwordconfirm" maxlength="100" placeholder="Leave blank if password unchanged" >
          </div>
        </div>
        <div class="form-group{{ $errors->has('currentpassword') ? ' has-error' : '' }}">
          <label for="currentpassword" class="col-md-4 control-label">Current password</label>
          <div class="col-md-3">
            <input id="currentpassword" type="password" class="form-control" name="currentpassword" maxlength="100" placeholder="Leave blank if password unchanged" >
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-6 col-md-offset-4">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-btn fa-check-circle"></i> Submit
            </button>
          </div>
        </div>
      </form>
    </div>
@endsection