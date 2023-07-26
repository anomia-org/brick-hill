@extends('layouts.header')

@noAds

@section('title', 'Forgot Password')

@section('content')
<div class="col-1-3 push-1-3">
	<div class="card">
		<div class="top red">
			Forgot Password
		</div>
		<div class="content" style="text-align:center;">
			<form method="POST" action="{{ route('forgotPassword') }}">
				@csrf
				<input type="email" name="email" placeholder="Email" required autofocus style="width:100%;box-sizing:border-box;">
				@if($errors->has('email'))
					<span class="invalid-feedback" style="display:block;">
						<strong>{{ $errors->first('email') }}</strong>
					</span>
				@endif
				<div style="height: 5px;"></div>
				<button type="submit" class="red">
					Reset Password
				</button>
			</form>
		</div>
	</div>
</div>
@endsection