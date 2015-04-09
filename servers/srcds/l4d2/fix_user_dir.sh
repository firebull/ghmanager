#!/bin/bash
find ./left4dead2/bin -type f -exec chmod 644 {} \;
find ./left4dead2/csgo -type f -exec chmod 644 {} \;
find ./left4dead2/platform -type f -exec chmod 644 {} \;
find ./linux32 -type f -exec chmod 644 {} \;
find ./package -type f -exec chmod 644 {} \;
find ./public -type f -exec chmod 644 {} \;
chmod ug+x ./linux32/steamcmd
