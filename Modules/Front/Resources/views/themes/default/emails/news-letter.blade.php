<h1>Newsletter Verification Mail</h1>

Please verify your email with bellow link:
<a href="{{ route('validation', $token) }}">Verify Email</a>


<a href="{{ route('delete-newsletter', $token) }}">Unsubscribe</a>

Thanks,<br>
{{ config('app.name') }}
