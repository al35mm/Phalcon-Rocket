{# Template View #}
<!DOCTYPE html>
<html lang="{{ substr(i18n.lang(), 0, 2) }}">
    <head>
        <meta charset="utf-8">
        {{ getTitle() }}
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
        <meta name="description" content="{{ siteDesc }}">
        {{ stylesheetLink('semantic/semantic.min.css') }}
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.4.0/animate.min.css">
        {{ this.assets.outputCss() }}
        <!-- Fav and touch icons -->
        <link rel="icon" type="image/x-icon" href="{{ this.url.getStatic('favicon.ico') }}">
    </head>
    <body>

    <script>
        var base_url = "{{ this.config.app.static_uri }}";
        var uri_segment_1 = "";
        var uri_segment_2 = "";
    </script>

            <header class="ui grid">
                    <div class="computer only sixteen wide column">


                {# MENU FOR COMPUTER SCREENS#}
                <div class="ui inverted menu" style="border-radius: 0;">
                    <div class="item">
                {{ linkTo([null, image('src': 'img/logo1.png', 'alt': config.app.name), 'class' : 'ui image']) }}
                        </div>
                    {{ linkTo([NULL, '<i class="home icon"></i> ' ~ __('Home'), 'class' : pageName == 'home' ? 'active item' : 'item']) }}
                    {{ linkTo(['doc/', '<i class="book icon"></i> ' ~ __('Documentation'), 'class' : pageName == 'docs' ? 'active item' : 'item']) }}
                        {% if ! auth.logged_in() %}
                            <div class="right menu">
                            {{ linkTo(['user/signin/', '<i class="sign in icon"></i>' ~ __('Sign in'), 'class' : 'item']) }}
                                <div class="item">
                                {{ linkTo(['user/signup/', '<i class="signup icon"></i>' ~ __('Join'), 'class' : 'ui green tiny button']) }}
                                    </div>
                                </div>
                        {% else %}
                                <div class="ui right dropdown item">
                                    <div class="text">{{ auth.get_user().username }}</div>
                                    <i class="dropdown icon"></i>
                                    <div class="menu">
                                        <div class="header">{{ auth.get_user().email }}</div>
                                        {{ linkTo(['user/', '<i class="user icon"></i> ' ~ __('Account'), 'class' : pageName == 'account' ? 'active item' : 'item']) }}
                                        {% if auth.logged_in('admin') %}
                                            {{ linkTo([this.config.app.admin_uri ~ '/', '<i class="wrench icon"></i> ' ~ __('Admin panel'), 'class' : 'item']) }}
                                        {% endif %}
                                        <div class="divider"></div>
                                        {{ linkTo(['user/signout/', '<i class="log out icon"></i> ' ~ __('Sign out'), 'class' : 'item']) }}
                                    </div>
                                </div>
                        {% endif%}
                    </div>
                        </div>



                {# MENU FOR MOBILE SCREENS #}
                    <div class="mobile tablet only sixteen wide column">
                <div class="ui large inverted menu" id="mobile-header-nav"
                     style="margin-top: 0; border-radius: 0;">
                    <a class="item"><i class="content icon"></i> Menu</a>

                    <div class="right header item">
                        {{ linkTo([null, image('src': 'img/logo1.png', 'alt': config.app.name), 'class' : 'ui image']) }}
                    </div>

                </div>
                        </div>


                <div class="ui left huge vertical inverted sidebar menu hidden computer" id="mobile-sidebar">
                    {% if ! auth.logged_in() %}
                    <div class="item">
                        {{ linkTo(['user/signin/', '<i class="sign in icon"></i>' ~ __('Sign in'), 'class' : 'ui grey small button']) }}
                        {{ linkTo(['user/signup/', '<i class="signup icon"></i>' ~ __('Join'), 'class' : 'ui green small button']) }}
                    </div>
                    {% else %}
                    <div class="ui inverted blue segment item">
                    <div class="ui fluid inverted accordion">
                        <div class="title">
                            <i class="user icon"></i>
                            {#<div class="text">{{ auth.get_user().username }}</div>#}
                        </div>

                                <div class="content">
                                    {#<div class="header">{{ auth.get_user().email }}</div>#}
                                    {{ linkTo(['user/', '<i class="user icon"></i> ' ~ __('Account'), 'class' : pageName == 'account' ? 'active item' : 'item']) }}
                                    {% if auth.logged_in('admin') %}
                                        {{ linkTo(['admin/', '<i class="wrench icon"></i> ' ~ __('Admin panel'), 'class' : 'item']) }}
                                    {% endif %}
                                    {#<div class="divider"></div>#}
                                    {{ linkTo(['user/signout/', '<i class="log out icon"></i> ' ~ __('Sign out'), 'class' : 'item']) }}
                                </div>
                    </div>
                    </div>
                    {% endif%}
                    {{ linkTo([NULL, '<i class="home icon"></i> ' ~ __('Home'), 'class' : pageName == 'home' ? 'active item' : 'item']) }}
                    {{ linkTo(['doc/', '<i class="book icon"></i> ' ~ __('Documentation'), 'class' : pageName == 'docs' ? 'active item' : 'item']) }}
                    {{ linkTo(['contact/', '<i class="mail icon"></i> ' ~ __('Contact'), 'class' : 'item']) }}


                </div>
            </header>
            <div class="ui grid container" style="min-height: 800px;">
                <div class="row">
                    <div class="column">
                {{ content() }}
                        </div>
                    </div>
            </div>



        <footer class="ui relaxed grid" style="margin-top: 50px;">
                <div class="large screen only row">
                <div class="black column">
                    <div class="ui padded grid">
                        <div class="ui four column row">
                            <div class="column">
                                <div class="ui mini list">
                                    <div class="item">
                                        {{ this.config.app.name ~ ' ' ~ this.config.app.version }}
                                    </div>
                                    <div class="item">
                                Phalcon {{ version() }}
                                        </div>
                                    <div class="item">
                                        PHP: {{ php_version() }}
                                    </div>
                                    <div class="item">
                                        MySQL: {{ mysqlVersion() }}
                                    </div>
                                    <div class="item">

                                    </div>
                                    </div>
                                </div>
                            <div class="column">
                    <div class="ui inverted link list">
                        {{ linkTo([NULL, __('Home'), 'class' : 'item']) }}
                        {{ linkTo(['doc/', __('Documentation'), 'class' : 'item']) }}
                    </div>
                                </div>
                            <div class="column">
                                <div class="ui inverted link list">
                                    {{ linkTo(['contact/', __('Contact'), 'class' : 'item']) }}
                                    {{ linkTo(['user/signup/', __('Sign up'), 'class' : 'item']) }}
                                </div>
                                </div>
                            <div class="right aligned column">
                                <div class="ui dropdown">
                                    <div class="text">{{ __('Language') }}</div>
                                    <i class="dropdown icon"></i>
                                    <div class="menu">
                                        {% for lang, language in siteLangs %}
                                            {{ linkTo(['lang/set/' ~ lang, language, 'class' : 'item']) }}
                                        {% endfor %}
                                    </div>
                                    {#{ linkTo([ '#', 'class' : 'dropdown-togle', 'data-toggle' : 'dropdown', __('Language') ~ '<b class="caret"></b>' ]) }#}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    </div>
            <div class="row">
                <div class="black column">
                    <div class="ui grid">
                        <div class="one column centered row">
                            <div class="four wide center aligned column">
                    <div class="ui tiny link inverted list">
                        <div class="item">
                        &copy; {{ linkTo(NULL, this.config.app.name) }} {{ date('Y') }}
                            </div>
                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </footer>


        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
    <script src='//www.google.com/recaptcha/api.js'></script>
        {{ javascriptInclude('semantic/semantic.min.js') }}
        <!-- Enable responsive features in IE8 -->
        <!--[if lt IE 9]>
        {{ javascriptInclude('js/respond.min.js') }}
        <![endif]-->
        {{ this.assets.outputJs() }}
        {% if count(scripts) %}
            {% for script in scripts %}
            <script type="text/javascript">{{ script }}</script>
            {% endfor %}
        {% endif %}
    </body>
</html>