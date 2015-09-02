{# User PASSWORD RESET #}
<div class="ui grid">
    <div class="column">
        {{ flashSession.output() }}
        {% if completed is defined AND completed == '1' %}
        <p>You have finished resetting your password.</p>
        {% else %}
        <h1 class="ui top attached inverted header">Recover Password</h1>

            {% if c is defined %}
                {{ form('user/password_reset/', 'class' : 'ui warning bottom attached pass-reset form segment', 'id' : 'reset-form') }}
                <div class="field">
                    <p>Hello {{ username|capitalize }}, please enter a new password below.</p>
                </div>
                <div class="field{{ errors is defined and errors.filter('pass') ? ' error' : '' }}">
                    <label>New password</label>
                    <input type="password" name="pass">
                    {% if errors is defined and errors.filter('pass') %}
                        <span class="ui red pointing above ui label">{{ current(errors.filter('pass')).getMessage() }}</span>
                    {% endif %}
                </div>
                <div class="field{{ errors is defined and errors.filter('pass_conf') ? ' error' : '' }}">
                    <label>Confirm new password</label>
                    <input type="password" name="pass_conf">
                    {% if errors is defined and errors.filter('pass_conf') %}
                        <span class="ui red pointing above ui label">{{ current(errors.filter('pass_conf')).getMessage() }}</span>
                    {% endif %}
                </div>
                <div class="field">
                    <input type="hidden" name="c" value="{{ c }}">
                    {#<input type="hidden" name="user" value="{{ user }}">#}
                    <input type="hidden" name="action" value="change_pass">
                    <input type="hidden" name="{{ this.security.getTokenKey() }}" value="{{ this.security.getToken() }}" />
                    <button class="ui button" type="submit">Submit</button>
                </div>
                {{ endForm() }}
            {% else %}
                {{ form('user/password_reset/', 'class' : 'ui bottom attached form segment', 'id' : 'reset-form') }}
                <div class="field{{ errors is defined and errors.filter('email') ? ' error' : '' }}">
                    <label>Your registered email address</label>
                    <input type="email" name="email" placeholder="Email address">
                    {% if errors is defined and errors.filter('email') %}
                        <span class="ui red pointing above ui label">{{ current(errors.filter('email')).getMessage() }}</span>
                    {% endif %}
                </div>
                <div class="field">
                    <input type="hidden" name="action" value="request_reset">
                    <input type="hidden" name="{{ this.security.getTokenKey() }}" value="{{ this.security.getToken() }}" />
                    <button class="ui button" type="submit">Send</button>
                </div>
                {{ endForm() }}

            {% endif %}

            {% endif %}
        </div>
    </div>
