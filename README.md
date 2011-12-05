`habari_xhprof` is a plugin for Habari that profiles code using the XHProf PHP extension, which must be installed to activate and run the plugin.

=Install=

    cd /path/to/your/habari/
    git clone git://github.com/michaeltwofish/habari_xhprof.git user/plugins/xhprof

=Install XHProf=

At the time of writing, pecl is unable to install the XHProf extension. Instead, do something like this, modifying paths where necessary.

    wget http://pecl.php.net/get/xhprof-0.9.2.tgz
    tar xvf xhprof-0.9.2.tgz
    cd ./xhprof-0.9.2/extension/
    phpize
    ./configure --with-php-config=/usr/local/bin/php-config
    make
    make test
    make install

Enable the extension by adding the following to the appropriate php.ini file. Feel free to modify the output directory.

    [xhprof]
    extension=xhprof.so
    xhprof.output_dir="/var/tmp/xhprof"

Check the output of `phpinfo()` or `php -m` to ensure the extension has loaded correctly.

=Connect the plugin to XHProf=

    cd /path/to/your/habari/user/plugins/xhprof
    ln -s /usr/share/php/xhprof_lib lib
    ln -s /usr/share/php/xhprof_html html
