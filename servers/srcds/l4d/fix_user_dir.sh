#!/bin/bash
find ./l4d/bin -type f -exec chmod 644 {} \;
find ./l4d/csgo -type f -exec chmod 644 {} \;
find ./l4d/platform -type f -exec chmod 644 {} \;
find ./linux32 -type f -exec chmod 644 {} \;
find ./package -type f -exec chmod 644 {} \;
find ./public -type f -exec chmod 644 {} \;
chmod ug+x ./linux32/steamcmd
