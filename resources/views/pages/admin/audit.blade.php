@extends('layouts.header')

@section('title', 'Audit of ' . $user['username'])

@section('content')
<div class="col-1-1 push-1-12">
    @can('view linked accounts')
    <div class="col-6-12">
        <div class="card">
            <div class="top blue">
                IP linked accounts
            </div>
            <div class="content">
                <table class="gray-text block" style="margin-bottom:10px;">
                    <thead>
                        <th>User</th>
                        <th>Matched</th>
                    </thead>
                    <tbody style="width: 100%;">
                        @foreach($user->linked_accounts as $ip)
                        <tr>
                            <td><a target="_blank" href="{{ route('profilePage', [ 'id' => $ip->user_id ]) }}">{{ $ip->account->username }}</td>
                            <td> {{ ($ip->matched == 1) ? 'once' : number_format($ip->matched) . ' times'}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endcan
    
    <div class="col-4-12 no-pad">
        @can('view user economy')
        <div class="card">
            <div class="top blue">
                Currency
            </div>
            <div class="content">
                <span class="darkest-gray-text" style="padding-right:5px;">Bucks:</span>
                <span class="gray-text">{{ $user['bucks'] }}</span>
                <span class="darkest-gray-text" style="padding-right:5px;">Bits:</span>
                <span class="gray-text">{{ $user['bits'] }}</span>
            </div>
        </div>
        @endcan
        @can('view emails')
        <div class="card">
            <div class="top blue">
                Email addresses
            </div>
            <div class="content">
                <table class="gray-text block" style="margin-bottom:10px;">
                    <thead>
                        <th>Email</th>
                        <th>Status</th>
                    </thead>
                    <tbody>
                        @foreach($emails as $email)
                        <tr>
                            <td>{{ $email->email }}</td>
                            <td>{{ ($email->verified) ? '(Verified)' : '(Unverified)' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endcan
        <div class="card">
            <div class="top blue">
                Security
            </div>
            <div class="content">
                <div>
                    <span class="dark-gray-text" style="padding-right:5px;">2FA:</span>
                    <span class="light-gray-text">{{ $user->tfa_active ? 'Active' : 'Disabled' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-10-12 push-1-12">
    <div class="card">
        <div class="top blue">
            Ban history
        </div>
        <div class="content">
            <table class="gray-text block" style="margin-bottom:10px;">
                <thead>
                    <th>Admin</th>
                    <th>Ban</th>
                    <th>Status</th>
                </thead>
                <tbody>
                    @foreach($bans as $ban)
                    <tr>
                        <td>
                            <a href="/user/{{ $ban->admin_id }}">{{ $ban->admin->username }}</a>
                        </td>
                        <td>
                            <a href="/bans/{{ $user->id }}/{{ $ban->id }}">
                                <b>{{ $ban->banType->name ?? "None" }}</b>: "<i>{{ $ban->note }}</i>" for <b>{{ $ban->length }} minutes</b> on {{ $ban->created_at }}
                            </a>
                        </td>
                        <td>{{ ($ban->active) ? 'Active' : 'Expired' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @can('view purchases')
    <div class="card">
        <div class="top blue">
            Payments
        </div>
        <div class="content">
            <table class="gray-text block" style="margin-bottom:10px;">
                <thead>
                    <th>Amount</th>
                    <th>Email</th>
                    <th>Receipt</th>
                    <th>Product ID</th>
                    <th>Created At</th>
                </thead>
                <tbody>
                    @foreach($user->payments as $payment)
                    <tr>
                        <td>
                            {{ $payment->gross_in_cents / 100 }}
                        </td>
                        <td>
                            {{ $payment->email }}
                        </td>
                        <td>
                            {{ $payment->receipt }}
                        </td>
                        <td>
                            {{ $payment->billing_product_id }}
                        </td>
                        <td>
                            {{ $payment->created_at }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endcan
    @can('view user economy')
    <div class="card">
        <div class="top blue">
            Transactions
        </div>
        <div class="content">
            <transactions id="transactions-v" :user_id="{{ $user->id }}"></transactions>
        </div>
    </div>
    <div class="card">
        <div class="top blue">
            Trades
        </div>
        <div class="content">
            <trade-list id="tradelist-v" :user="{{ $user->id }}" :open_tab="true" :more_types="true"></trade-list>
        </div>
    </div>
    @endcan
</div>
@endsection