@extends('layouts.header')

@section('title', 'Ban')

@section('content')
@isset($ban_content)
<div class="col-10-12 push-1-12">
    <div class="alert success">
        The content will be deleted automatically after you ban the user.
    </div>
</div>
@endif
<div class="col-10-12 push-1-12">
    <div class="card">
        <div class="top blue">
            Ban
        </div>
        <div class="content">
            <form method="POST" action="{{ route('postBan', ['user' => request()->user->id, 'content_type' => request('type'), 'content_id' => request('content')]) }}">
                @csrf
                @if(isset($types))
                <span class="darkest-gray-text bold block">Reason:</span>
                <select class="width-100" name="type" style="margin-bottom:5px;">
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" data-note="{{ $type->default_note }}" data-time="{{ $type->default_length }}">{{ $type->name }}</option>
                    @endforeach
                </select>
                <textarea id="note-area" name="note" style="width:100%;box-sizing:border-box;height:100px;" placeholder="Note">{{ old('note') }}</textarea>
                <textarea name="content" style="width:100%;box-sizing:border-box;height:100px;" placeholder="Content (optional)">{{ $ban_content ?? '' }}</textarea>
                <span class="darkest-gray-text bold block">Length:</span>
                <select name="length" style="margin-bottom:5px;">
                    <option value="0">Warning</option>
                    <option value="60">1 Hour</option>
                    <option value="720">12 Hours</option>
                    <option value="1440">1 Day</option>
                    <option value="4320">3 Days</option>
                    <option value="10080">1 Week</option>
                    <option value="43200">1 Month</option>
                    <option value="129600">3 Months</option>
                    <option value="525600">1 Year</option>
                    <option value="37317600">Terminate</option>
                </select>
                <button type="submit" class="blue">SUBMIT</button>
                @elseif(isset($superban))
                <span class="darkest-gray-text bold block">Are you sure you want to superban?</span>
                <span class="darkest-gray-text bold block">THIS CANNOT BE UNDONE</span>
                <span class="darkest-gray-text bold block">ONLY USE ON NEW ACCOUNTS USED ONLY FOR SPAM OTHERWISE USE A NORMAL BAN</span>
                <span class="darkest-gray-text bold block">ONLY USE ON NEW ACCOUNTS USED ONLY FOR SPAM OTHERWISE USE A NORMAL BAN</span>
                <span class="darkest-gray-text bold block">ONLY USE ON NEW ACCOUNTS USED ONLY FOR SPAM OTHERWISE USE A NORMAL BAN</span>
                <span class="darkest-gray-text bold block">ONLY USE ON NEW ACCOUNTS USED ONLY FOR SPAM OTHERWISE USE A NORMAL BAN</span>
                <span class="darkest-gray-text bold block">ONLY USE ON NEW ACCOUNTS USED ONLY FOR SPAM OTHERWISE USE A NORMAL BAN</span>
                <span class="darkest-gray-text bold block">ONLY USE ON NEW ACCOUNTS USED ONLY FOR SPAM OTHERWISE USE A NORMAL BAN</span>
                <button type="submit" class="red" name="superban" value="superban">SUPERBAN</button>
                @else
                <span class="darkest-gray-text bold block">Are you sure you want to unban?</span>
                <button type="submit" class="red" name="unban" value="unban">UNBAN</button>
                @endif
            </form>
        </div>
    </div>
    @if(isset($ban_history) && count($ban_history) > 0)
    <div class="card">
        <div class="top blue">
            Ban history
        </div>
        <div class="content">
            <table class="gray-text block" style="margin-bottom:10px;">
                <tbody>
                    @foreach($ban_history as $ban)
                    <tr>
                        <td>
                            <a href="/user/{{ $ban->admin_id }}">{{ $ban->admin->username }}</a>
                        </td>
                        <td><b>{{ $ban->banType->name ?? 'None' }}</b>: "<i>{{ $ban->note }}</i>" for <b>{{ $ban->length }} minutes</b> on {{ $ban->created_at }}</td>
                        <td>{{ ($ban->active) ? 'Active' : 'Expired' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    <script>
        $('select[name="type"]').on('change', (e) => {
            d = $(':selected', e.target).data()
            $('select[name="length"]').val(d.time)
            $('#note-area').text(d.note)

        })
    </script>
</div>
@endsection