Hey there, <b>{{ $user['username'] }}</b><br>
Thanks for verifying your account. This'll make your account more secure if you ever get compromised. There's just one more step though; click the link below to link this email with your account.
<br><br>
<a href="{{ config('site.url') }}/email/verify?key={{ $email['verify_code'] }}">{{ config('site.url') }}/email/verify</a>
<br><br>
If this request wasn't by you, then you can just ignore this email. 
<br><br>
Happy Hilling!<br>
Brick Hill