Hey there, <b>{{ $user['username'] }}</b><br>
We noticed that you changed your email from {{ $current_email->send_email }} to {{ $new_email->send_email }}. If you didn't make this change or wish to revert it please click the link below.
<br><br>
This link will be active for a month or until it has been used.
If the link is expired and you still wish to revert the email you can contact us at <a href="mailto:help@brick-hill.com">help@brick-hill.com</a>.
<br><br>
<a href="{{ config('site.url') }}/email/revert?key={{ $current_email['revert_code'] }}">{{ config('site.url') }}/email/revert</a>
<br><br>
Happy Hilling!<br>
Brick Hill