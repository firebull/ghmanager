#!/bin/bash
#STEAMEXE=steamcmd ./current/steam.sh +runscript ./csgo_update.txt
STEAMEXE=steamcmd ./current/steamcmd/steam.sh +runscript ../../cs16_update.txt
find ./current/cstrike/ -type f -exec chmod 644 {} \;
find ./current/steamcmd/linux32 -type f -exec chmod 644 {} \;
find ./current/steamcmd/package -type f -exec chmod 644 {} \;
find ./current/steamcmd/public -type f -exec chmod 644 {} \;
chmod ug+x ./current/steamcmd/linux32/steamcmd
chmod a+rX -R ./current