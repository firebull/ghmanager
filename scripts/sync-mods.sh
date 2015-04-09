#!/bin/sh
chmod a+rX -R mods
rsync -avz -c -e ssh --progress --delete-after /images/mods/ root@100.100.100.100:/images/mods/
#rsync -avz -c -e ssh --progress --delete-after /images/mods/ root@100.100.100.101:/images/mods/
