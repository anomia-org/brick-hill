@extends('layouts.header')

@section('title', 'Currency')

@section('content')
<div class="col-10-12 push-1-12">
    <div class="tabs">
        <div class="tab active col-3-12" data-tab="1">
            Exchange
        </div>
        <div class="tab col-3-12" data-tab="2">
            Transactions
        </div>
        <div class="tab col-3-12" data-tab="3">
            Items on sale
        </div>
        <div class="tab col-3-12" id="buyrequests" data-tab="4">
            Buy requests
        </div>
        <div class="tab-holder" style="box-shadow:none;">
            <div class="tab-body active" data-tab="1" style="text-align:center;">
                <form method="POST" action="{{ route('transferCurrency') }}">
                    @csrf
                    <div class="block">
                        <select name="type" class="select mb2">
                            <option value="to-bits">To bits</option>
                            <option value="to-bucks">To bucks</option>
                        </select>
                        <input type="number" placeholder="0" min="0" name="amount" style="margin-bottom:10px;">
                    </div>
                    <button type="submit" class="blue smaller-text">CONVERT</button>
                </form>
            </div>
            <div class="tab-body" data-tab="2" id="transactions">
                <transactions id="transactions-v"></transactions>
            </div>
            <div class="tab-body" data-tab="3">
                @if($items_on_sale->count() > 0)
                <table style="width: 100%;">
                    @foreach($items_on_sale as $item)
                    <tr>
                        <th>
                            <a href="{{ route('itemPage', [ 'item' => $item->item_id ]) }}" class="agray-text">
                                <img src="{{ $item->item->thumbnail }}" alt="{{ $item->item->name }}" style="height: 56px;">
                                <div>{{ $item->item->name }}</div>
                            </a>
                        </th>
                        <th><span class="bucks-text">{{ number_format($item->bucks) }} BUCKS</span></th>
                    </tr>
                    @endforeach
                </table>
                @else
                <p>You have no items on sale.</p>
                @endif
            </div>
            <div class="tab-body" data-tab="4">
                @if($buy_requests->count() > 0)
                <table style="width: 100%;">
                    @foreach($buy_requests as $request)
                    <tr>
                        <th>
                            <a href="{{ route('itemPage', [ 'item' => $request->item_id ]) }}" class="agray-text">
                                <img src="{{ $request->item->thumbnail }}" alt="{{ $request->item->name }}" style="height: 56px;">
                                <div>{{ $request->item->name }}</div>
                            </a>
                        </th>
                        <th><span class="bucks-text">{{ number_format($request->bucks) }} BUCKS</span></th>
                        <td>Posted {{ Carbon\Carbon::parse($request->updated_at)->diffForHumans() }}</td>
                        <td>
                            <form method="POST" action="{{ route('cancelBuyRequest') }}">
                                @csrf
                                <input type="hidden" name="id" value="{{ $request->id }}">
                                <button class="button red" style="margin-right:10px" type="submit">Cancel</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </table>
                @else
                <p>You have no buy requests.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
