#!/bin/bash
STEAMEXE=steamcmd ./current/steam.sh +runscript ../css_update.txt
find ./current/css_ds/bin -type f -exec chmod 644 {} \;
find ./current/css_ds/cstrike -type f -exec chmod 644 {} \;
find ./current/css_ds/platform -type f -exec chmod 644 {} \;
find ./current/linux32 -type f -exec chmod 644 {} \;
find ./current/package -type f -exec chmod 644 {} \;
find ./current/public -type f -exec chmod 644 {} \;
chmod ug+x ./current/linux32/steamcmd
chmod a+rX -R current