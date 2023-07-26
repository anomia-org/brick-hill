@extends('layouts.header')

@section('title', 'Report')

@section('content')
<div class="col-10-12 push-1-12">
	<div class="card">
		<div class="top blue">
			Report
		</div>
		<div class="content">
			<span class="darker-grey-text bold block">Tell us how you think this {{ request()->type }} is breaking the Brick Hill rules.</span>
			<form method="POST" action="/report/send">
				@csrf
				<input type="hidden" name="reportable_type" value="{{ $report_type }}">
				<input type="hidden" name="reportable_id" value="{{ request()->id }}">
				<select name="reason">
					@foreach($reasons as $reason)
						<option value="{{ $reason->id }}">{{ $reason->reason }}</option>
					@endforeach
				</select>
				<textarea name="note" style="width:100%;box-sizing:border-box;margin-top:10px;height:100px;" placeholder="Explain more (optional)">{{ old('note') }}</textarea>
				<button type="submit" class="blue">SUBMIT</button>
			</form>
		</div>
	</div>
</div>
@endsection