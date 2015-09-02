{# User sign in #}

<div class="ui grid">
    <div class="row">
        <div class="sixteen wide mobile fourteen wide tablet ten wide computer eight wide large screen centered column">
            <h1 class="ui top attached inverted header">{{ __('Log in') }} </h1>
            {% if attempts is not defined OR attempts['block'] == false %}
            {{ form(NULL, 'class' : 'ui form bottom attached inverted segment') }}
                {% if attempts is defined AND attempts['attempts_left'] < 5 %}
                    <div class="ui negative icon message">
                        <i class="bomb icon"></i>
                        <div class="content">
                            <div class="header">{{ attempts['attempts_left'] }} {{ __('attempts left') }}</div>
                            If you do not successfully login within {{ attempts['attempts_left'] }} attempts, the login form will be disabled for a period of time!
                        </div>

                    </div>
                    {% else %}
            {{ flashSession.output() }}
                        {% endif %}
            {% set field = 'username' %}
            <div class="field{{ errors is defined ? ' error' : (_POST[field] is defined ? ' success' : '') }}">
                <label for={{ field }}>{{ __(field|capitalize) }}:</label>
                <div class="ui huge input">
                    {{ textField([ field, 'placeholder' : __(field|capitalize) ]) }}
                </div>
                {% if errors is defined %}
                    {% if errors.filter(field) %}
                        <span class="ui red pointing above label">{{ current(errors.filter(field)).getMessage() }}</span>
                    {% endif %}
                {% endif %}
            </div>
            {% set field = 'password' %}
            <div class="field{{ errors is defined ? ' error' : (_POST[field] is defined ? ' success' : '') }}">
                <label for={{ field }}>{{ __(field|capitalize) }}:</label>

                <div class="ui huge input">
                    {{ passwordField([ field, 'placeholder' : __(field|capitalize) ]) }}
                </div>
                {% if errors is defined %}
                    {% if errors.filter(field) %}
                        <span class="ui red pointing above label">{{ current(errors.filter(field)).getMessage() }}</span>
                    {% endif %}
                {% endif %}
            </div>
            {% set field = 'rememberMe' %}
            <div class="field">
                <div class="ui checkbox">

                    {{ checkField(_POST[field] is defined and _POST[field] == 'on' ? [ field, 'value': 'on', 'checked': 'checked' ] : [ field, 'value': 'on' ]) }}
                    <label>{{ __(field|label) }}</label>
                </div>
            </div>

                {# Recaptcha #}
                {% if attempts is defined and attempts['captcha'] == true and this.config.recaptcha.enabled == '1' %}
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
                <div class="col-lg-offset-2 col-lg-10">
                        <button type="submit" name="submit_signin" class="ui huge grey button submit-spin"><i
                                    class="sign in icon"></i> {{ __('Log in') }}</button>

                </div>
            </div>

            <div class="field">
                <p>{{ linkTo(['user/password_reset/', __('Forgot your password?')]) }}</p>
            </div>

            <div class="field">
                <p class="text-muted">
                    {{ __("Don't have an account?") }} {{ linkTo([ 'user/signup/', '<i class="signup icon"></i> ' ~ __('Sign up') ]) }}
                </p>
            </div>
            {# CSRF Security #}
            <input type="hidden" name="{{ this.security.getTokenKey() }}"
                   value="{{ this.security.getToken() }}"/>
            {{ endForm() }}
{% else %}
            <div class="ui bottom attached segment">
                <div class="ui negative icon message">
                    <i class="lock icon"></i>
                    <div class="content">
                        <div class="header">{{ __('Login failed!') }}</div>
                        {{ __('If you have forgotten your login or password, you can') }} {{ linkTo(['user/password_reset/', 'recover them here']) }}
                    </div>

                </div>
                <p><i class="lock icon"></i> {{ __('Login has been locked. Please try again after :block_time minutes.', [':block_time' : this.config.brute.block_time]) }}</p>
                {{ linkTo(['user/signin/', 'Try again', 'class' : 'ui large center aligned green button']) }}
            </div>
            {% endif %}
        </div>
    </div>
</div>