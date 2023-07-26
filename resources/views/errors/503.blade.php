@extends('layouts.header')

@noAds

@section('title', 'Maintenance')

@section('content')
<div style="text-align:center;padding-top:50px;">
	<div style="font-weight:600;font-size:3rem;">Brick Hill is currently under maintenance.</div>
	<div style="font-weight:500;font-size:2rem;">Please try again soon.</div>
	<div style="font-size:2rem;"><a href="https://discord.gg/brick-hill">Join us on Discord at discord.gg/brick-hill</a></div>
	<form style="margin-top:20px;">
		<input type="password" name="maint_key" placeholder="Maintenance Key">
		<div style="padding:2.5px;"></div>
		<button onclick="window.location.href = `/${document.querySelector('input').value}`;" type="button" class="blue">SUBMIT</button>
	</form>
</div>
<script>
// refresh page after a minute to check if its no longer in maintenance
setTimeout(()=>{
	window.location.reload();
}, 1000 * 60)
</script>
@endsection