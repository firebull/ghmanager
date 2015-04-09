#!/bin/bash
STEAMEXE=steamcmd ./current/steam.sh +runscript ../tf_update.txt
find ./current/tf/bin -type f -exec chmod 644 {} \;
find ./current/tf/tf -type f -exec chmod 644 {} \;
find ./current/tf/platform -type f -exec chmod 644 {} \;
find ./current/linux32 -type f -exec chmod 644 {} \;
find ./current/package -type f -exec chmod 644 {} \;
find ./current/public -type f -exec chmod 644 {} \;
chmod ug+x ./current/linux32/steamcmd
chmod a+rX -R current