{# User RESEND ACTIVATION #}
<div class="ui grid">
    <div class="column">
        <div class="ui header">Resend Activation</div>
        <div class="ui segment" id="resend">
            {{ flashSession.output() }}
        {% if user is defined %}
            {{ form('user/resend/', 'class' : 'ui form segment', 'id' : 'resend-form') }}
        <p>We sent your activation email to <b>{{ user.email }}</b>. Is that the correct address?</p>
            <div class="ui accordion">
                <div class="title ui green button">
                    <i class="dropdown icon"></i>
                    Yes my email address is {{ user.email }}
                </div>
                <div class="content">
            <div class="ui segment" id="yes">
                <h5>Yes my email address is {{ user.email }}</h5>
                <p>If you are sure that email address is correct, it's likely the activation email was mistaken for spam by your email system. Please try the following.</p>
                <ul>
                    <li>Look in your email spam or junk folder</li>
                    <li>Look in your email deleted items</li>
                    <li>Add our domain <b>{{ config.app.domain }}</b> to your email white list, safe list or friends/contacts list, and then click the resend button.</li>
                </ul>
                <div class="field">
                    <button class="ui small button submit" type="submit">Resend activation</button>
                </div>
            </div>
                    </div>
                <div class="title ui red button">
                    <i class="dropdown icon"></i>
                    No my email address is incorrect
                </div>
                <div class="content">
            <div class="ui segment" id="no">
                <h5>No my email address is incorrect</h5>
                <p>Please enter the correct email address below.</p>
                <div class="inline field">
                    <label>New email address</label>
                    <input class="input small" type="email" name="email">
                </div>
                <div class="field">
                    <button class="ui small button submit" type="submit">Resend activation</button>
                </div>
                <div class="field" id="errors"></div>
            </div>
                    </div>
            </div>
            {{ endForm() }}
        {% endif %}
    </div>
    </div>
</div>
