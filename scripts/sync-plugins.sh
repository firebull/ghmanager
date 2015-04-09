#!/bin/sh
chmod a+rX -R plugins
rsync -avz -c -e ssh --progress --delete-after /images/plugins/ root@100.100.100.100:/images/plugins/
#rsync -avz -c -e ssh --progress --delete-after /images/plugins/ root@100.100.100.101:/images/plugins/
