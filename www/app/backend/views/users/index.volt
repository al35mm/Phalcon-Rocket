{# Admin's Home View  #}
<div class="ui grid">
    <div class="column">
        <h1 class="ui header"><i class="users icon"></i> {{ __('Users') }}</h1>

        <table class="ui celled table">
            <thead>
            <tr>
                <th>ID</th>
                <th>
                    Username
                </th>
                <th>
                    Email
                </th>
                <th>
                    Last Login
                </th>
                <th>
                    Logins
                </th>
            </tr>
            </thead>
            <tbody>
            {% for user in users %}
            <tr>
                <td>
                    {{ user.id }}
                </td>
                <td>
                    {{ user.username }}
                </td>
                <td>
                    {{ user.email }}
                </td>
                <td>
                    {{ from_sql(user.last_login) }}
                </td>
                <td>
                    {{ user.logins }}
                </td>
            </tr>
            {% endfor %}
            </tbody>
        </table>

        </div>
    </div>