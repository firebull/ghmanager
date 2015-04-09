#!/bin/bash
STEAMEXE=steamcmd ./current/steam.sh +runscript ../l4d_update.txt
find ./current/l4d/bin -type f -exec chmod 644 {} \;
find ./current/l4d/left4dead -type f -exec chmod 644 {} \;
find ./current/l4d/platform -type f -exec chmod 644 {} \;
find ./current/linux32 -type f -exec chmod 644 {} \;
find ./current/package -type f -exec chmod 644 {} \;
find ./current/public -type f -exec chmod 644 {} \;
chmod ug+x ./current/linux32/steamcmd
chmod a+rX -R current