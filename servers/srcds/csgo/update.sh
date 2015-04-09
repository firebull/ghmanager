#!/bin/bash
#STEAMEXE=steamcmd ./current/steam.sh +runscript ../csgo_update.txt
STEAMEXE=steamcmd ./current/steam.sh +runscript ../csgo_update.txt
find ./current/csgo_ds/bin -type f -exec chmod 644 {} \;
find ./current/csgo_ds/csgo -type f -exec chmod 644 {} \;
find ./current/csgo_ds/platform -type f -exec chmod 644 {} \;
find ./current/linux32 -type f -exec chmod 644 {} \;
find ./current/package -type f -exec chmod 644 {} \;
find ./current/public -type f -exec chmod 644 {} \;
chmod ug+x ./current/linux32/steamcmd
chmod a+rX -R current