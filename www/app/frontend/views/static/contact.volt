{# User contact #}
<div class="ui grid">
    <div class="row">
        <div class="sixteen wide mobile fourteen wide tablet ten wide computer eight wide large screen centered column">
            <h1 class="ui top attached inverted header">{{ __('Contact') }}</h1>
            {{ form(NULL, 'class' : 'ui bottom attached inverted contact form segment') }}
            {{ flashSession.output() }}
            {% set field = 'email' %}
            <div class="field{{ errors is defined and errors.filter(field) ? ' error' : (_POST[field] is defined ? ' has-success' : '') }}">
                <label for={{ field }}>{{ __(field|capitalize) }}:</label>

                <div class="ui huge input">
                    {{ textField([ field, 'placeholder' : __(field|capitalize) ]) }}
                </div>
                {% if errors is defined and errors.filter(field) %}
                    <span class="ui red pointing above label">{{ current(errors.filter(field)).getMessage() }}</span>
                {% endif %}
            </div>
            {% set field = 'repeatEmail' %}
            <div class="field{{ errors is defined and errors.filter(field) ? ' error' : (_POST[field] is defined ? ' has-success' : '') }}">
                <label for={{ field }}>{{ __(field|label) }}:</label>

                <div class="ui huge input">
                    {{ textField([ field, 'placeholder' : __(field|label) ]) }}
                </div>
                {% if errors is defined and errors.filter(field) %}
                    <span class="ui red pointing above label">{{ current(errors.filter(field)).getMessage() }}</span>
                {% endif %}
            </div>
            {% set field = 'fullName' %}
            <div class="field{{ errors is defined and errors.filter(field) ? ' error' : (_POST[field] is defined ? ' has-success' : '') }}">
                <label for={{ field }}>{{ __(field|label) }}:</label>

                <div class="ui huge input">
                    {{ textField([ field, 'placeholder' : __(field|label) ]) }}
                </div>
                {% if errors is defined and errors.filter(field) %}
                    <span class="ui red pointing above label">{{ current(errors.filter(field)).getMessage() }}</span>
                {% endif %}
            </div>
            {% set field = 'content' %}
            <div class="field{{ errors is defined and errors.filter(field) ? ' error' : (_POST[field]|isset ? ' has-success' : '') }}">
                <label for={{ field }}>{{ __(field|capitalize) }}:</label>

                <div class="ui huge input">
                    {{ textarea([ field, 'placeholder' : __(field|capitalize), 'rows': '5', 'onclick': "this.rows='10'" ]) }}
                </div>
                {% if errors is defined and errors.filter(field) %}
                    <span class="ui red pointing above label">{{ current(errors.filter(field)).getMessage() }}</span>
                {% endif %}
            </div>

            {# Recaptcha #}
            {% if this.config.recaptcha.enabled == '1' %}
                {% set field = 'g-recaptcha-response' %}
            <div class="field{{ errors is defined and errors.filter(field) ? ' error' : '' }}">
                <label>Security</label>
                {{ recaptcha() }}
                {% if errors is defined and errors.filter(field) %}
                    <span class="ui red pointing above label">{{ current(errors.filter(field)).getMessage() }}</span>
                {% endif %}
            </div>
            {% endif %}

            <div class="field">
                <button type="submit" name="submit" class="ui huge teal button"><i
                            class="mail icon"></i> {{ __('Send') }}</button>
            </div>
            {{ endForm() }}
        </div>
    </div>
</div>