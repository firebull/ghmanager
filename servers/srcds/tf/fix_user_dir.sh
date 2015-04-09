#!/bin/bash
find ./tf/bin -type f -exec chmod 644 {} \;
find ./tf/tf -type f -exec chmod 644 {} \;
find ./tf/platform -type f -exec chmod 644 {} \;
find ./linux32 -type f -exec chmod 644 {} \;
find ./package -type f -exec chmod 644 {} \;
find ./public -type f -exec chmod 644 {} \;
chmod ug+x ./linux32/steamcmd
