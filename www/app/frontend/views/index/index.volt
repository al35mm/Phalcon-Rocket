{# Home View  #}
<div class="ui grid">
    <div class="row">
        <div class="column">
            <h1 class="ui icon header"><i class="red rocket icon animated bounceInUp"></i> {{ app_name }} {{ this.config.app.version }}
                <div class="sub header">{{ __('Use this application to kick start any new PhalconPHP project!') }}
                    {#{{ __('See working :link, user/pass: :users.', [':link' : '<a href="http://base-app.mruz.me">base-app</a>', ':users' : 'user user, admin admin']) }}#}
                </div>
            </h1>
            <div class="ui basic segment">
                <h2 class="ui brown header">{{ __('A base application for PhalconPHP') }}
                </h2>

                <p>{{ app_name }} is based on Mruz's base
                        app which he
                    no longer seems to be maintaining. I have found it useful as a foundation for kick starting my own projects so I have made this version available for any one else who may find it useful! As well as updating the base app to work with PhalconPHP 2.x, this
                    version uses <a href="http://semantic-ui.com/" target="_blank">Semantic UI</a>
                    which is a great alternative to Twitter Bootstrap. On top of that, I have made lots of changes,
                    improvements and added many new features to make it
                    a more complete, rapid development solution.</p>
                <p>{{ app_name }} is intended to not make too many assumptions about the kind of app you are developing.
                In other words, it doesn't bundle loads of stuff you are unlikely to ever use, but instead provides the basics
                for you to build upon or change as you see fit. The mundane stuff such as auth system, contact system and general scaffolding
                    are provided as they are features that are most commonly required.</p>

                <div class="ui two column stackable grid">
                    <div class="ten wide column">
                        <h3 class="ui inverted green header">Overview</h3>

                        <div class="ui bulleted list">
                            <div class="item">HMVC multi module app</div>
                            <div class="item">Role based user authentication</div>
                            <div class="item">Multi environments - development, testing, staging & production</div>
                            <div class="item">Assortment of libraries & extensions</div>
                            <div class="item">Volt templating and markdown support</div>
                            <div class="item">CLI & console file</div>
                            <div class="item">I18n language translation</div>
                        </div>
                        <h3 class="ui red header">New features</h3>

                        <div class="ui bulleted list">
                            <div class="item">
                                Improved user system
                                <div class="list">
                                    <div class="item">
                                        Improved security
                                    </div>
                                    <div class="item">
                                        Login brute force protection
                                    </div>
                                    <div class="item">Database sessions</div>
                                    <div class="item">
                                        Forgotten password reset
                                    </div>
                                    <div class="item">User edit password & email</div>
                                    <div class="item">Validation extension for password strength - adjustable in *config.ini*</div>
                                </div>
                            </div>
                            <div class="item">Recaptcha integration</div>
                            <div class="item">New libraries - Time, Utility and Helper</div>
                            <div class="item">Separate development/production configs</div>
                            <div class="item">Obfuscated admin URI</div>
                        </div>
                    </div>
                    <div class="six wide column">
                        <div class="ui inverted segment">
                            <div class="ui red ribbon label">Try it out</div>
                            <div class="ui list">
                                <div class="item">
                                    Admin (login = admin | password = password)
                                </div>
                                <div class="item">
                                    User (login = user | password = password)
                                </div>
                            </div>

                        </div>
                        <div class="ui hidden divider"></div>
                        <p>{{ linkTo(['doc', '<i class="book icon"></i> ' ~  __('Documentation'), 'class' : 'ui big brown button']) }}</p>
                        <p><a href="https://github.com/al35mm/Phalcon-Rocket-App" target="_blank" class="ui massive grey button"><i class="github icon"></i> Fork on GitHub</a></p>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>