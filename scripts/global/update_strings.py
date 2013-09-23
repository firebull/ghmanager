# coding: UTF-8

'''
***********************************************
Returns update command lines.
Copyright (C) 2013 Nikita Bulaev

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.
***********************************************
'''


import sys
sys.path.append("/images/scripts/global")
from string import Template
from datetime import date
from common import *


def dedicatedUpdate(id, user, type, token):

    d = date.today()
    logsDir = "/home/" + user + "/logs/" + type + "_" + str(id) + "/update/"
    logName = d.strftime("%d-%m-%Y") + ".log"

    gameDir = "/home/%s/servers/%s_%s" % (user, type, str(id))

    # pidName   = str(type + "-" + str(id) + "-update")
    # interface = '/usr/bin/screen -U -m -d -S ' + pidName

    if type not in ['csgo', 'csgo-t128', 'css', 'dods', 'cs16']:

        steamPath = "/home/%s/servers/%s_%s/steam" % (user, type, str(id))

        o = Template('echo "############################" >> $log && \
          date >> $log && \
          /home/configurator/public_html/scripts/update_valve.py --command=\'$steam -command update -verify_all -retry -game $game -dir $dir/\' --token=$action_token >> $log && \
          date >> $log \
          && echo "############################" >> $log')

        options = o.substitute(game=srcdsTypeToGameForUpdate(type),
                               log=logsDir + logName,
                               steam=steamPath,
                               dir=gameDir,
                               action_token=token
                               )
    elif type in ['csgo', 'csgo-t128']:

        steamPath = "/home/%s/servers/%s_%s/steam.sh" % (user, type, str(id))

        o = Template('echo "############################" >> $log && \
  date >> $log && \
  /home/configurator/public_html/scripts/update_valve.py --command=\'STEAMEXE=steamcmd $steam +runscript ./csgo_update.txt\' --token=$action_token >> $log &&\
  echo "Установка прав на файлы bin" >> $log && \
  find $dir/csgo_ds/bin -type f -exec chmod 660 {} \; >> $log && \
  echo "Установка прав на файлы csgo" >> $log && \
  find $dir/csgo_ds/csgo -type f -exec chmod 660 {} \; >> $log && \
  echo "Установка прав на файлы platform" >> $log && \
  find $dir/csgo_ds/platform -type f -exec chmod 660 {} \; >> $log && \
  echo "Установка прав на файлы Steam (linux32)" >> $log && \
  find $dir/linux32 -type f -exec chmod 644 {} \; >> $log && \
  echo "Установка прав на файлы package" >> $log && \
  find $dir/package -type f -exec chmod 644 {} \; >> $log && \
  echo "Установка прав на файлы public" >> $log && \
  find $dir/public -type f -exec chmod 644 {} \; >> $log && \
  echo "Установка прав на запуск Steam " >> $log && \
  chmod ug+x $dir/linux32/steamcmd >> $log && \
  echo "Установка корректного владельца" >> $log && \
  chown -R $user_group $dir/csgo_ds >> $log && \
  date >> $log && \
  echo "############################" >> $log')

        options = o.substitute(log=logsDir + logName,
                               steam=steamPath,
                               dir=gameDir,
                               action_token=token,
                               user_group='%s:%s' % (user, user)
                               )
    elif type in ['css']:

        steamPath = "/home/%s/servers/%s_%s/steam.sh" % (user, type, str(id))

        o = Template('echo "############################" >> $log && \
  date >> $log && \
  /home/configurator/public_html/scripts/update_valve.py --command=\'STEAMEXE=steamcmd $steam +runscript ./css_update.txt\' --token=$action_token >> $log &&\
  echo "Установка прав на файлы bin" >> $log && \
  find $dir/css_ds/bin -type f -exec chmod 660 {} \; >> $log && \
  echo "Установка прав на файлы cstrike" >> $log && \
  find $dir/css_ds/cstrike -type f -exec chmod 660 {} \; >> $log && \
  echo "Установка прав на файлы platform" >> $log && \
  find $dir/css_ds/platform -type f -exec chmod 660 {} \; >> $log && \
  echo "Установка прав на файлы Steam (linux32)" >> $log && \
  find $dir/linux32 -type f -exec chmod 644 {} \; >> $log && \
  echo "Установка прав на файлы package" >> $log && \
  find $dir/package -type f -exec chmod 644 {} \; >> $log && \
  echo "Установка прав на файлы public" >> $log && \
  find $dir/public -type f -exec chmod 644 {} \; >> $log && \
  echo "Установка прав на запуск Steam " >> $log && \
  chmod ug+x $dir/linux32/steamcmd >> $log && \
  echo "Установка корректного владельца" >> $log && \
  chown -R $user_group $dir/css_ds >> $log && \
  date >> $log && \
  echo "############################" >> $log')

        options = o.substitute(log=logsDir + logName,
                               steam=steamPath,
                               dir=gameDir,
                               action_token=token,
                               user_group='%s:%s' % (user, user)
                               )
    elif type in ['dods']:

        steamPath = "/home/%s/servers/%s_%s/steam.sh" % (user, type, str(id))

        o = Template('echo "############################" >> $log && \
  date >> $log && \
  /home/configurator/public_html/scripts/update_valve.py --command=\'STEAMEXE=steamcmd $steam +runscript ./dods_update.txt\' --token=$action_token >> $log &&\
  echo "Установка прав на файлы bin" >> $log && \
  find $dir/dods_ds/bin -type f -exec chmod 660 {} \; >> $log && \
  echo "Установка прав на файлы dods" >> $log && \
  find $dir/dods_ds/dods -type f -exec chmod 660 {} \; >> $log && \
  echo "Установка прав на файлы platform" >> $log && \
  find $dir/dods_ds/platform -type f -exec chmod 660 {} \; >> $log && \
  echo "Установка прав на файлы Steam (linux32)" >> $log && \
  find $dir/linux32 -type f -exec chmod 644 {} \; >> $log && \
  echo "Установка прав на файлы package" >> $log && \
  find $dir/package -type f -exec chmod 644 {} \; >> $log && \
  echo "Установка прав на файлы public" >> $log && \
  find $dir/public -type f -exec chmod 644 {} \; >> $log && \
  echo "Установка прав на запуск Steam " >> $log && \
  chmod ug+x $dir/linux32/steamcmd >> $log && \
  echo "Установка корректного владельца" >> $log && \
  chown -R $user_group $dir/dods_ds >> $log && \
  date >> $log && \
  echo "############################" >> $log')

        options = o.substitute(log=logsDir + logName,
                               steam=steamPath,
                               dir=gameDir,
                               action_token=token,
                               user_group='%s:%s' % (user, user)
                               )
    else:
        steamPath = "/home/%s/servers/%s_%s/steamcmd/steamcmd.sh" % (user, type, str(id))
        o = Template('echo "############################" >> $log && \
  date >> $log && \
  /home/configurator/public_html/scripts/update_valve.py --command=\'$steam +login anonymous +force_install_dir ../ +app_set_config 90 mod cstrike +app_update 90 validate +quit\' --token=$action_token >> $log &&\
  echo "Установка прав на основные файлы" >> $log && \
  find $dir/cstrike/ -type f -exec chmod 644 {} \; >> $log && \
  find $dir/valve/ -type f -exec chmod 644 {} \; >> $log && \
  echo "Установка прав на файлы Steam (linux32)" >> $log && \
  find $dir/steamcmd/linux32 -type f -exec chmod 644 {} \; >> $log && \
  echo "Установка прав на файлы package" >> $log && \
  find $dir/steamcmd/package -type f -exec chmod 644 {} \; >> $log && \
  echo "Установка прав на файлы public" >> $log && \
  find $dir/steamcmd/public -type f -exec chmod 644 {} \; >> $log && \
  echo "Установка прав на запуск Steam " >> $log && \
  chmod ug+x $dir/steamcmd/linux32/steamcmd >> $log && \
  echo "Установка корректного владельца" >> $log && \
  chmod $user_group -R $dir/cstrike >> $log && \
  chmod $user_group -R $dir/valve >> $log && \
  date >> $log && \
  echo "############################" >> $log')

        options = o.substitute(log=logsDir + logName,
                               steam=steamPath,
                               dir=gameDir,
                               action_token=token,
                               user_group='%s:%s' % (user, user)
                               )

    # Возвращаем только строку обновления, без screen
    runString = str(options)

    return runString
