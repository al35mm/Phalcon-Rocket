{# Messages View #}
<div>
    <h2>{{ title }}</h2><hr />
    <meta http-equiv="Refresh" content="5; url={{ config.app.base_uri ~ redirect|default('') }}" />
    {{ flashSession.output() }}
    {{ content|default('') }}
</div>