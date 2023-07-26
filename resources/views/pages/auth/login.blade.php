@extends('layouts.header')

@noAds

@section('title', 'Login')

@push('scripts')
<script src="https://hcaptcha.com/1/api.js" async defer></script>
@endpush

@section('content')
<div class="col-1-3 push-1-3">
	<div class="card">
		<div class="top green">
			Login
		</div>
		<div class="content">
			<form method="POST" action="{{ route('login') }}">
				@csrf
				<input id="username" type="username" name="username" value="{{ old('username') }}" placeholder="Username" required autofocus>
				<div style="height: 5px;"></div>
				<input style="display:block;" id="password" type="password" name="password" autocomplete="password" placeholder="Password" required>
				<a href="/password/forgot" style="font-size:15px;">Forgot password?</a>
				<div style="padding-top:5px;"></div>
				<div class="h-captcha" data-sitekey="{{ config('site.captcha.hcaptcha.key') }}"></div>
				<div style="padding-top:5px;"></div>
				<button type="submit" class="green">
					Login
				</button>
			</form>
		</div>
	</div>
</div>
@endsection
