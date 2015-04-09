#!/bin/bash
find ./csgo_ds/bin -type f -exec chmod 644 {} \;
find ./csgo_ds/csgo -type f -exec chmod 644 {} \;
find ./csgo_ds/platform -type f -exec chmod 644 {} \;
find ./linux32 -type f -exec chmod 644 {} \;
find ./package -type f -exec chmod 644 {} \;
find ./public -type f -exec chmod 644 {} \;
chmod ug+x ./linux32/steamcmd
