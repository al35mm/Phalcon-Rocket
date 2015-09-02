{# User sign up #}
<div class="ui grid">
    <div class="row">
        <div class="sixteen wide mobile fourteen wide tablet ten wide computer eight wide large screen centered column">
            <h1 class="ui inverted top attached header">{{ __('Join') }}</h1>
            {{ form(NULL, 'class' : 'ui bottom attached inverted signup form segment') }}
            {{ flashSession.output() }}
            {% set field = 'username' %}
            <div class="field{{ errors is defined and errors.filter(field) ? ' error' : (_POST[field] is defined ? ' has-success' : '') }}">
                <label for={{ field }}>{{ __(field|capitalize) }}:</label>

                <div class="ui huge input">
                    {{ textField([ field, 'placeholder' : __(field|capitalize) ]) }}
                </div>
                {% if errors is defined and errors.filter(field) %}
                    <span class="ui red pointing above label">{{ current(errors.filter(field)).getMessage() }}</span>
                {% endif %}
            </div>
            {% set field = 'password' %}
            <div class="field{{ errors is defined and errors.filter(field) ? ' error' : (_POST[field] is defined ? ' has-success' : '') }}">
                <label for={{ field }}>{{ __(field|capitalize) }}:</label>

                <div class="ui huge input">
                    {{ passwordField([ field, 'placeholder' : __(field|capitalize) ]) }}
                </div>
                {% if errors is defined and errors.filter(field) %}
                    <span class="ui red pointing above label">{{ current(errors.filter(field)).getMessage() }}</span>
                {% endif %}
            </div>
            {% set field = 'repeatPassword' %}
            <div class="field{{ errors is defined and errors.filter(field) ? ' error' : (_POST[field] is defined ? ' has-success' : '') }}">
                <label for={{ field }}>{{ __(field|label) }}:</label>

                <div class="ui huge input">
                    {{ passwordField([ field, 'placeholder' : __(field|label) ]) }}
                </div>
                {% if errors is defined and errors.filter(field) %}
                    <span class="ui red pointing above label">{{ current(errors.filter(field)).getMessage() }}</span>
                {% endif %}
            </div>
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

            {% set field = 'agree' %}
            <div class="field{{ errors is defined and errors.filter(field) ? ' error' : (_POST[field] is defined ? ' has-success' : '') }}">
                <div class="ui checkbox" style="float: left;">
                    {{ checkField(_POST[field] is defined and _POST[field] == 'yes' ? [ field, 'value': 'yes', 'checked': 'checked' ] : [ field, 'value': 'yes' ]) }}
                    <label>{{ __(field|label) }}</label>
                </div>
                <div style="float: left;">&nbsp; to&nbsp; {{ linkTo(['terms/', __('our terms of use'), 'target' : '_blank']) }}</div>
                {% if errors is defined and errors.filter(field) %}
                    <br><div style="clear: left;" class="ui red pointing above label">{{ current(errors.filter(field)).getMessage() }}</div>
                {% endif %}
            </div>


            <div class="ui hidden divider" style="clear: left;"></div>
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

<div class="ui hidden divider" style="clear: left;"></div>
            <div class="field">
                <button type="submit" name="submit_signup" class="ui huge blue button"><i
                            class="signup icon"></i> {{ __('Join') }}</button>
            </div>
            {{ endForm() }}
        </div>
    </div>
</div>