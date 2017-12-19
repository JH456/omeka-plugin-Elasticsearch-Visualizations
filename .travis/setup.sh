#!/bin/sh

set -x

export OMEKA_CORE_DIR=$HOME/omeka

plugin_src=$(pwd)
plugin_name=$(basename $(pwd))
plugin_name=${plugin_name/omeka-plugin-/}
plugin_dest=$OMEKA_CORE_DIR/plugins/$plugin_name

# Init database
mysql -e 'CREATE DATABASE omeka;' -uroot

# Grab specified version of Omeka from github
wget -nv -O /tmp/omeka-2.5.1.zip https://github.com/omeka/Omeka/releases/download/v2.5.1/omeka-2.5.1.zip
unzip -q /tmp/omeka-2.5.1.zip -d /tmp
mv -v /tmp/omeka-2.5.1 $OMEKA_CORE_DIR

# Update Omeka db.ini
sed -i 's/^host.*/host = "localhost"/' $OMEKA_CORE_DIR/application/db.ini
sed -i 's/^user.*/user = "root"/' $OMEKA_CORE_DIR/application/db.ini
sed -i 's/^dbname.*/dbname = "omeka"/' $OMEKA_CORE_DIR/application/db.ini

# move plugin into place and prepare for phpunit call
mv -v $plugin_src $plugin_dest
cd $plugin_dest

set +x
