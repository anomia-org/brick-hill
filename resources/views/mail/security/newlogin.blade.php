Your Brick Hill account {{ $user->username }} has been logged in from a new device.<br>
<ul>
    <li>Time: {{ $data['time']->toDayDateTimeString() }} UTC</li>
    <li>IP address: {{ $data['ip'] }}</li>
</ul>
If this wasn't you, change your password in the <a href="{{ config('site.url') }}/settings">settings</a> page 
or <a href="{{ config('site.url') }}/password/forgot">recover your account here</a>
<br><br>
Make sure to use a secure and memorable password.
<br><br>
Happy Hilling!<br>
Brick Hill