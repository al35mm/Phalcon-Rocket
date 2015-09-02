{# Admin's Home View  #}
<div class="ui grid">
    <div class="column">
        <h1 class="ui header"><i class="dashboard icon"></i> {{ __('Admin panel') }}</h1>

        <p>{{ __('Use this application as a way to quick start any new project.') }}</p>

        <h2 class="ui header">
            Password hash workload calculator
        </h2>

        <p>Password hashing is an important security feature of any web app. If the hashing algorithm is weak,
            passwords can easily be cracked. The strength of a hash is based on the length of time it takes to
            perform the hash, and therefore, the subsequent cost in terms of hardware it would take to crack it.
            A traditional salted MD5 hash has a cost of only < $1. Stronger algorithms have an associated cost
            of $millions or $billions. That translates to the cost of processing power required to crack those
            hashes. So the higher the work load required to generate the hash, the longer it takes and the higher the
            cost.
        </p>

        <p>You want to set the workload as high as you can with out overloading your server. The following is a
            calculation of
            the workload this server can cope with based on interactive log ins. Bear in mind that this will most likely
            be
            different between your development server and your production server. You can enter the following workload
            in
            the config.ini</p>

        <div class="ui label">Edit the [auth] hash_workload setting in development and production config files
            accordingly
        </div>
        <div class="ui hidden divider"></div>
        <div class="ui center aligned inverted compact circular segment">
            <div class="ui blue inverted statistic" style="padding: 0 10px 0 0;">
                <div class="value">
                    {{ costPhp }}
                </div>
                <div class="label">Cost (using plain PHP)<br> on {{ gethostname() }}</div>
            </div>
            <div class="ui large vertical divider"><i class="latge green line chart icon"></i></div>
            <div class="ui purple inverted statistic" style="padding: 0 0 0 10px;">
                <div class="value">
                    {{ costPhal }}
                </div>
                <div class="label">Cost (using Phalcon)<br> on {{ gethostname() }}</div>
            </div>
        </div>
        <div class="ui hidden divider"></div>
        <p>Note: The above test aims for ≤ 50 milliseconds stretching time, which is a good baseline for systems
            handling interactive logins. So you can use a higher number than the above, but it may cause a performance
            hit on
            a busy server!</p>

        <div class="ui accordion">
            <div class="title ui blue button">
                <i class="dropdown icon"></i>
                View PHP Info
            </div>
            <div class="content">
                <?
                /**
         * setup PHPINFO for display in page
         */
        ob_start();
        phpinfo();

        preg_match ('%<style type="text/css">(.*?)</style>.*?(<body>.*</body>)%s', ob_get_clean(), $matches);

                # $matches [1]; # Style information
# $matches [2]; # Body information

                                                          echo "<div class='phpinfodisplay'><style type='text/css'>\n",
                join( "\n",
                array_map(
                create_function(
                '$i',
                'return ".phpinfodisplay " . preg_replace( "/,/", ",.phpinfodisplay ", $i );'
                ),
                preg_split( '/\n/', $matches[1] )
                )
                ),
                "</style>\n",
                $matches[2],
                "\n</div>\n";
                ?>
            </div>
        </div>
    </div>

</div>
</div>
