{# Error View #}
<p>{{ __('Hello :user', [':user' : 'admin']) }}</p>
<p><strong>{{ __('Something is wrong!') }}</strong></p>
<p>{{ __('Look at the log:') }}</p>
{{ log }}