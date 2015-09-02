{# Reset Email Views #}
<p style="font-size:14px;">{{ __('Hello :user', [':user' : username]) }},</p>
<p style="font-size:14px;">{{ __('If you have forgotten your password, you can reset it.') }}</p>
<p style="font-size:14px;">{{ __('To reset your password, click on this link:') }} <b><a href="{{ url.getStatic('user/password_reset/?c=' ~ encrypted) }}">{{ __('Reset password') }}</a></b>.</p>