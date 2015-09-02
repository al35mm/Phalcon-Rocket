{# User View #}
<h1 class="ui header">
    <i class="grey user icon"></i>
    <div class="content">
        User account
    </div>
</h1>
{{ flashSession.output() }}
<div class="ui two column stackable grid">
    <div class="column">
        <h2 class="ui header">
            Logged in as {{ auth.get_user().username|capitalize }}
            <div class="sub header">{{ auth.get_user().email }}</div>
        </h2>
        <p><strong>{{ __('Last login') }}:</strong> {{ from_sql(auth.get_user().last_login) }}</p>
        <div class="ui large list">
            <div class="header">Roles</div>
            {% set roles = this.auth.get_user_roles(this.auth.get_user().id) %}
            {% for role in roles %}
                <div class="item">
                    Role ID: {{ role['id'] }} {{ role['name']|capitalize }}
                </div>
            {% endfor %}
        </div>
        <p>{{ linkTo(['user/signout', '<i class="sign out icon"></i> ' ~ __('Sign out'), 'class' : 'ui red button']) }}</p>
    </div>
    <div class="column">
        {{ form(NULL, 'class' : 'ui user-edit form segment') }}
            <div class="ui large blue right ribbon label"><i class="edit icon"></i> Edit account</div>

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
        <div class="field" style="text-align: right;">
            <button type="submit" class="ui button"><i class="edit icon"></i> Edit</button>
        </div>
        <div style="clear: left;"></div>
        {{ endForm() }}
    </div>
</div>

