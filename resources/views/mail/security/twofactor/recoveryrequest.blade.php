Hey there, <b>{{ $user->username }}</b><br>
Our support team has received a request to recover a Two Factor Authentication code to restore your account. If this wasn't you please contact us at <a href="mailto:help@brick-hill.com">help@brick-hill.com</a> with a link to your account.
<br><br>
If this was you here is the recovery code to sign into your account.
<ul>
    <li>{{ $recoveryCode }}</li>
</ul>
Once you sign into your account, go into settings and press <b>Generate New Codes</b>. You can then use one of those generated recovery codes to disable Two Factor Authentication and then set it up again.
Make sure to save the recovery codes in a safe place for next time.
<br><br>
Happy Hilling!<br>
Brick Hill