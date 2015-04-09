#!/bin/bash
STEAMEXE=steamcmd ./current/steam.sh +runscript ../l4d2_update.txt
find ./current/left4dead2/bin -type f -exec chmod 644 {} \;
find ./current/left4dead2/left4dead2 -type f -exec chmod 644 {} \;
find ./current/left4dead2/platform -type f -exec chmod 644 {} \;
find ./current/linux32 -type f -exec chmod 644 {} \;
find ./current/package -type f -exec chmod 644 {} \;
find ./current/public -type f -exec chmod 644 {} \;
chmod ug+x ./current/linux32/steamcmd
chmod a+rX -R current