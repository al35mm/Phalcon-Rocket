{# Example buy view #}
<div class="ui grid">
    <div class="ten wide column centered row">
        <div class="column">

<h1 class="ui top attached header">{{ __('Buy me some chocolate') }}</h1>
{{ form(NULL, 'class' : 'ui form bottom attached segment') }}
            {{ flashSession.output() }}
<div class="field">
    <label>{{ __('Price') }}:</label>
    <div class="col-lg-10">
        <p class="form-control-static">1USD</p>
    </div>
</div>
{% set field = 'quantity' %}
<div class="field{{ errors is defined and errors.filter(field) ? ' error' : (_POST[field] is defined ? ' has-success' : '') }}">
    <label for={{ field }}>{{ __(field|capitalize) }}:</label>
    <div class="ui huge input">
    {{ textField([ field, 'value': 1, 'placeholder' : __(field|capitalize) ]) }}
    {% if errors is defined and errors.filter(field) %}
        <span class="help-block">{{ current(errors.filter(field)).getMessage() }}</span>
    {% endif %}
    </div>
</div>
<div class="field">
        <button type="submit" name="submit" class="ui huge green button"><i class="shop icon"></i> {{ __('Buy now') }}</button>
</div>
{{ endForm() }}
</div>
</div>
</div>