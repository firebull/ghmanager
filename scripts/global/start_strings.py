# coding: UTF-8

'''
***********************************************
Returns start command lines of game servers.
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
from common import *
from datetime import datetime, date, time


def dedicatedStart(id, user, email, ip, port, slots, slotsMax, type, map, mapGroup, hostmap, hostcollection, authkey, gameMode, autoUpdate, vac, fpsmax, nomaster, tickrate, debug):
    clientPort = str(int(port) + 2015)  # Client Ports
    # MATCH='27029'                      # Match Ports
    systemPort = str(int(port) + 5015)    # System Ports
    steamPort = str(int(port) - 1015)
    # netPort      = str(int(port) + 17914)
    tvPort = str(int(port) + 1015)
    # replayPort   = str(int(port) + 3015)
    autoUpdate = str(autoUpdate)
    vac = str(vac)
    fpsmax = str(fpsmax)
    nomaster = str(nomaster)
    tickrate = str(tickrate)

    pidName = str(type + "-" + str(id))
    pidFile = "/home/pid/" + user + "/" + pidName + ".pid"
    serverDir = type + "_" + str(id)

    if vac == '1':
        vac = ' '
    elif vac == '0':
        vac = '-insecure'

    if fpsmax == 'none':
        fpsmax = ' '
        systicrate = ' '
    else:
        systicrate = '+sys_ticrate ' + fpsmax
        fpsmax = '+fps_max ' + fpsmax

    if nomaster == '1':
        nomaster = '-nomaster'
    else:
        nomaster = ''

    if tickrate in ('30', '33', '60', '64', '66', '90', '100', '128'):
        tickrate = '-tickrate ' + tickrate
    else:
        tickrate = ''

    if debug == '1':
        screenDebug = '-L'
        srvDebug = '-debug'
        nemrunDebug = '-corefile  ../../../logs/' + serverDir + '/startup/' + pidName + '-%Y%m%d.core'
    else:
        screenDebug = ''
        srvDebug = ''
        nemrunDebug = ''

    if autoUpdate == '1' and type == 'csgo':
        update = '-autoupdate -steam_dir /home/%s/servers/%s/ -steamcmd_script /home/%s/servers/%s/csgo_update.txt' % (
            user, serverDir, user, serverDir)
    elif autoUpdate == '1':
        update = '-autoupdate'
    else:
        update = ''

    if hostcollection == None or hostcollection == 'None':
        hostcollection = ''
        if hostmap == None or hostmap == 'None':
            hostmap = ''
        else:
            mapGroup = None
            hostmap = '+host_workshop_map %s' % hostmap
    else:
        mapGroup = None
        hostmap = ''
        hostcollection = '+host_workshop_collection %s' % hostcollection

    if authkey == None or authkey == 'None':
            authkey = ''
    else:
        mapGroup = None
        authkey = '-authkey %s' % authkey

    if type in ('l4d', 'l4d2'):

        nice = str(5 - int(round((float(slots) / float(slotsMax)) * 5)))

        # НЕ ТРОГАТЬ кавычки! Так сделано неспроста! Хрень в том, что система и перл убирают по \, потому и надо ставить их 2 подряд.
        o = Template(' -game $game +clientport $cPort+## +hostport $hostport+## +systemlinkport $sysLinkPort+## \
 +ip $hostIP -steamport $sPort+## +maxplayers $players +map \\\"$serverMap\\\" +exec server.cfg -pidfile $pid +log on \
 +sv_logsdir /home/$userName/logs/$server/run $vacEnable $fps $dbg')
        options = o.substitute(
            game=srcdsTypeToGame(type),
            cPort=clientPort,
            hostport=port,
            sysLinkPort=systemPort,
            hostIP=ip,
            sPort=steamPort,
            players=slots,
            serverMap=map.strip(),
            pid=pidFile,
            userName=user,
            server=serverDir,
            vacEnable=vac,
            fps=fpsmax,
            dbg=srvDebug)

        i = Template('/usr/bin/screen -U -m -d $scrDbg -S $pid ./nemrun -niceness $niceValue -nemlog ../../../logs/$server/startup/run-$pid-%Y%m%d.log \
    $nemCore -cleandownloads 10 -steamdir  /home/$userName/servers/$server -srvdir ./ $upd ')

        interface = i.substitute(
            scrDbg=screenDebug,
            pid=pidName,
            niceValue=nice,
            userName=user,
            nemCore=nemrunDebug,
            server=serverDir,
            serverID=id,
            serverGame=srcdsTypeToGame(type),
            userEmail=email,
            upd=update)

        runString = str(interface) + str(options)

    #
    # L4D1/2 Tickrate 100 ##################################
    elif type in ('l4d-t100', 'l4d2-t100'):

        nice = '0'  # Максимальный приоритет

        # НЕ ТРОГАТЬ кавычки! Так сделано неспроста! Хрень в том, что система и перл убирают по \, потому и надо ставить их 2 подряд.
        o = Template(' -game $game +clientport $cPort+## +hostport $hostport+## +systemlinkport $sysLinkPort+## \
 +ip $hostIP -steamport $sPort+## +maxplayers $players +map \\\"$serverMap\\\" +exec server.cfg -pidfile $pid +log on \
 +sv_logsdir /home/$userName/logs/$server/run $vacEnable $fps $dbg $tick')
        options = o.substitute(
            game=srcdsTypeToGame(type),
            cPort=clientPort,
            hostport=port,
            sysLinkPort=systemPort,
            hostIP=ip,
            sPort=steamPort,
            players=slots,
            serverMap=map.strip(),
            pid=pidFile,
            userName=user,
            server=serverDir,
            vacEnable=vac,
            fps=fpsmax,
            dbg=srvDebug,
            tick=tickrate)

        i = Template('/usr/bin/screen -U -m -d $scrDbg -S $pid ./nemrun -niceness $niceValue -nemlog ../../../logs/$server/startup/run-$pid-%Y%m%d.log \
    $nemCore -cleandownloads 10 -steamdir  /home/$userName/servers/$server -srvdir ./ $upd ')

        interface = i.substitute(
            scrDbg=screenDebug,
            pid=pidName,
            niceValue=nice,
            userName=user,
            nemCore=nemrunDebug,
            server=serverDir,
            serverID=id,
            serverGame=srcdsTypeToGame(type),
            userEmail=email,
            upd=update)

        runString = str(interface) + str(options)
    #
    # Team Fortress 2 & Day of Defeat: Source & Counter-Strike: Source ##################################
    elif type in ("tf", "dods", "css", "hl2mp", "zps"):

        if int(slots) <= 11:
            nice = '0'
        else:
            nice = str(4 - int(round((float(slots) / float(slotsMax)) * 4)))

        # НЕ ТРОГАТЬ кавычки! Так сделано неспроста! Хрень в том, что система и перл убирают по \, потому и надо ставить их 2 подряд.
        o = Template(' -game $game +clientport $cPort +hostport $hostport +port $hostport +systemlinkport $sysLinkPort \
 +ip $hostIP -steamport $sPort +maxplayers $players +map \\\"$serverMap\\\" +tv_port $tvport -pidfile $pid +log on \
 +sv_logsdir /home/$userName/logs/$server/run $vacEnable $dbg -strictportbind $tick')
        options = o.substitute(game=srcdsTypeToGame(type),
                               cPort=clientPort,
                               hostport=port,
                               sysLinkPort=systemPort,
                               hostIP=ip,
                               sPort=steamPort,
                               players=slots,
                               serverMap=map.strip(),
                               tvport=tvPort,
                               pid=pidFile,
                               userName=user,
                               server=serverDir,
                               vacEnable=vac,
                               dbg=srvDebug,
                               tick=tickrate)

        i = Template('/usr/bin/screen -U -m -d $scrDbg -S $pid ./nemrun -niceness $niceValue -nemlog ../../../logs/$server/startup/run-$pid-%Y%m%d.log \
    $nemCore -cleandownloads 10 -steamdir  /home/$userName/servers/$server -srvdir ./ $upd ')

        interface = i.substitute(
            scrDbg=screenDebug,
            pid=pidName,
            niceValue=nice,
            userName=user,
            nemCore=nemrunDebug,
            server=serverDir,
            serverID=id,
            serverGame=srcdsTypeToGame(type),
            userEmail=email,
            upd=update)

        runString = str(interface) + str(options)
    #
    #
    # Counter-Strike: Global Offensive Tick 66 ##################################
    elif type == 'csgo':

        if gameMode == None or gameMode == 'None':
            gameTypeMode = ['0', '0']
        else:
            gameTypeMode = gameMode.split("/")

        if mapGroup == None or mapGroup == 'None':
            mapGroup = ''
        else:
            mapGroup = '+mapgroup ' + mapGroup

        o = Template('$autoUpdate -game $game -console -usercon +game_type $game_type +game_mode $game_mode $mapgrp \
    +clientport $cPort +hostport $hostport +port $hostport +systemlinkport $sysLinkPort \
    +ip $hostIP -steamport $sPort -maxplayers_override $players +map \\\"$serverMap\\\" +tv_port $tvport -pidfile $pid +log on \
    +sv_logsdir /home/$userName/logs/$server/run $vacEnable $tick $dbg $host_map $host_collection $auth_key -strictportbind')

        options = o.substitute(autoUpdate=update,
                               game=srcdsTypeToGame(type),
                               game_type=gameTypeMode[0],
                               game_mode=gameTypeMode[1],
                               mapgrp=mapGroup,
                               cPort=clientPort,
                               hostport=port,
                               sysLinkPort=systemPort,
                               hostIP=ip,
                               sPort=steamPort,
                               players=slots,
                               serverMap=map.strip(),
                               tvport=tvPort,
                               pid=pidFile,
                               userName=user,
                               server=serverDir,
                               vacEnable=vac,
                               tick=tickrate,  # После запуска в продажу Tick128, убрать параметр отсюда!
                               dbg=srvDebug,
                               host_map=hostmap,
                               host_collection=hostcollection,
                               auth_key=authkey)

        interface = '/usr/bin/screen -U -m -d %s -S %s ./srcds_run ' % (screenDebug, pidName)

        runString = str(interface) + str(options)

    #
    #
    # Counter-Strike: Global Offensive Tick 128 ##################################
    elif type == 'csgo-t128':

        if gameMode == None or gameMode == 'None':
            gameTypeMode = ['0', '0']
        else:
            gameTypeMode = gameMode.split("/")

        if mapGroup == None or mapGroup == 'None':
            mapGroup = ''
        else:
            mapGroup = '+mapgroup ' + mapGroup

        o = Template('$autoUpdate -game $game -console -usercon +game_type $game_type +game_mode $game_mode $mapgrp \
    +clientport $cPort +hostport $hostport +port $hostport +systemlinkport $sysLinkPort \
    +ip $hostIP -steamport $sPort -maxplayers_override $players +map \\\"$serverMap\\\" +tv_port $tvport -pidfile $pid +log on \
    +sv_logsdir /home/$userName/logs/$server/run $vacEnable $tick $dbg $host_map $host_collection $auth_key -strictportbind')
        options = o.substitute(autoUpdate=update,
                               game=srcdsTypeToGame(type),
                               game_type=gameTypeMode[0],
                               game_mode=gameTypeMode[1],
                               mapgrp=mapGroup,
                               cPort=clientPort,
                               hostport=port,
                               sysLinkPort=systemPort,
                               hostIP=ip,
                               sPort=steamPort,
                               players=slots,
                               serverMap=map.strip(),
                               tvport=tvPort,
                               pid=pidFile,
                               userName=user,
                               server=serverDir,
                               vacEnable=vac,
                               tick=tickrate,
                               dbg=srvDebug,
                               host_map=hostmap,
                               host_collection=hostcollection,
                               auth_key=authkey)

        interface = '/usr/bin/screen -U -m -d %s -S %s ./srcds_run ' % (screenDebug, pidName)

        runString = str(interface) + str(options)

    #
    #
    # Counter-Strike: Source v34 ##################################
    elif type == "cssv34":

        # НЕ ТРОГАТЬ кавычки! Так сделано неспроста! Хрень в том, что система и перл убирают по \, потому и надо ставить их 2 подряд.
        o = Template(' -game $game +clientport $cPort +hostport $hostport \
 +ip $hostIP -steamport $sPort +maxplayers $players +map \\\"$serverMap\\\" +tv_port $tvport -pidfile $pid +log on \
 +sv_logsdir /home/$userName/logs/$server/run $tick $vacEnable $fps $dbg')
        options = o.substitute(game=srcdsTypeToGame(type),
                               cPort=clientPort,
                               hostport=port,
                               hostIP=ip,
                               sPort=steamPort,
                               players=slots,
                               serverMap=map.strip(),
                               tvport=tvPort,
                               pid=pidFile,
                               userName=user,
                               server=serverDir,
                               vacEnable=vac,
                               fps=fpsmax,
                               dbg=srvDebug,
                               tick=tickrate)

        interface = '/usr/bin/screen -U -m -d %s -S %s ./srcds_run ' % (screenDebug, pidName)

        runString = str(interface) + str(options)
    #
    # Counter-strike 1.6 ###########################################################
    elif type == "cs16" or type == "dmc" or type == 'hl1':

        if int(slots) <= 12:
            nice = '0'
        else:
            nice = str(4 - int(round((float(slots) / float(slotsMax)) * 4)))

        # НЕ ТРОГАТЬ кавычки! Так сделано неспроста! Хрень в том, что система и перл убирают по \, потому и надо ставить их 2 подряд.
        o = Template(' ./hlds_run +ip $hostIP -game $game +clientport $cPort +hostport $hostport +port $hostport \
 -maxplayers $players $fps $master $ticrate -pingboost 1 +map \\\"$serverMap\\\" -pidfile $pid +log on $vacEnable $dbg')
        options = o.substitute(hostIP=ip,
                               game=srcdsTypeToGame(type),
                               cPort=clientPort,
                               hostport=port,
                               players=slots,
                               serverMap=map.strip(),
                               pid=pidFile,
                               vacEnable=vac,
                               fps=fpsmax,
                               master=nomaster,
                               ticrate=systicrate,
                               dbg=srvDebug)

        if debug == '1':
            interface = ('/usr/bin/screen -U -m -d -L -S %s nice -n %s ' % (pidName, nice))
        else:
            interface = ('/usr/bin/screen -U -m -d -S %s nice -n %s ' % (pidName, nice))

        runString = str(interface) + str(options)

    return runString


def codStart(id, user, ip, port, slots, type, map, mod, punkbuster, rconPassword, debug):
    serverDir = type + "_" + str(id)
    pidName = str(type + "-" + str(id))
    # pidFile = "/home/pid/" + user + "/" + pidName + ".pid"

    if map == 'rotate':
        map = 'map_' + map.strip()
    else:
        map = 'map ' + map.strip()

    if rconPassword == 'None':
            rconPassword = ''
    else:
        rconPassword = '+set rcon_password "%s"' % rconPassword

    if debug == '1':
        srvDebug = '-debug'
    else:
        srvDebug = ''

    logDate = datetime.now().strftime("%d-%m-%Y-%H%M")

    #
    # COD2               ###########################################################

    if type == 'cod2':
        execFile = 'cod2_lnxded'

        if mod == 'None' or mod == 'main':
            mod = '+set fs_game main'
            cfg = 'server.cfg'    # Для стандартного мода - стандартный конфиг
        else:
            mod = '+set fs_game mods/' + mod
            cfg = 'modserver.cfg'  # Для стороннего мода - свой конфиг

    #
    # COD4 & Rotu        ###########################################################
    elif type == 'cod4' or type == 'cod4v1' or type == 'cod4fixed':
        execFile = 'cod4_lnxded'

        if mod == 'ModWarfare':
            cfg = 'server.cfg'   # Для стандартного мода - стандартный конфиг

        else:
            cfg = 'modserver.cfg'  # Для стороннего мода - свой конфиг

        if mod == 'None':
            # mod = '+set fs_game mods/ModWarfare'
            mod = ' '
            cfg = 'server.cfg'
        else:
            mod = '+set fs_game mods/' + mod

        if type == 'cod4fixed':
            slots = '64'

    o = Template('./$exe +set dedicated 2 +set net_ip $hostIP +set net_port $hostport \
+set ui_maxclients $clients +set sv_maxclients $clients $startMod +set g_logsync 2 +set g_logfile 1 +set g_log "$logDir/$logName" \
+exec $serverCfg +$serverMap +set sv_punkbuster $pb $rconPass +set loc_language 6 $dbg')

    options = o.substitute(exe=execFile,
                           hostIP=ip,
                           hostport=port,
                           clients=slots,
                           startMod=mod,
                           serverCfg=cfg,
                           serverMap=map.strip(),
                           pb=int(punkbuster),
                           rconPass=rconPassword,
                           logDir=serverDir,
                           logName='games_mp-' + logDate + '.log',
                           dbg=srvDebug)
    if debug == '1':
        interface = ('/usr/bin/screen -U -m -d -L -S %s ' % pidName)
    else:
        interface = ('/usr/bin/screen -U -m -d -S %s ' % pidName)

    runString = str(interface) + str(options)

    return runString


def hltvStart(id, user, ip, port, slots, type):
    clientPort = str(int(port) + 2015)    # Client Ports
    # MATCH='27029'                      # Match Ports
    # systemPort   = str(int(port) + 3015)    # System Ports
    # steamPort    = str(int(port) - 1015)
    # netPort      = str(int(port) + 17914)
    tvPort = str(int(port) + 1015)
    pidName = str(type + "-tv-" + str(id))
    pidFile = "/home/pid/" + user + "/" + pidName + ".pid"
    serverDir = type + "_" + str(id)

    #
    # Counter-strike 1.6 ###########################################################
    if type == "cs16" or type == "dmc":

        o = Template('./hltv -ip $hostIP -port $hostport +connect $hostIP:$connectTo \
 +maxclients  $clients -nodns -pidfile $pid -logfile 0')
        options = o.substitute(hostIP=ip,
                               cPort=clientPort,
                               hostport=tvPort,
                               clients=slots,
                               connectTo=port,
                               pid=pidFile)
        # Предварительно необходимо указать путь для библиотек, потом стартовать сервер
        interface = ('export LD_LIBRARY_PATH=.:$LD_LIBRARY_PATH && /usr/bin/screen -U -m -d -S %s ' % pidName)

        runString = str(interface) + str(options)

    return runString


def uedsStart(id, user, ip, port, slots, type, map, vac, setPassword, debug):

    slots = str(slots)
    # queryPort   = str(int(port) + 1)
    listenPort = str((int(port) - 7707) + 8075)
    gameSpyPort = str((int(port) - 7707) + 7917)
    vac = str(vac)
    map = str(map).strip() + '.rom'

    serverDir = type + "_" + str(id)
    pidName = str(type + "-" + str(id))

    logDate = datetime.now().strftime("%d-%m-%Y-%H%M")

    if vac == '1':
        vac = 'true'
    elif vac == '0':
        vac = 'false'

    if setPassword == '1':
        setPassword = '?AdminName=admin?AdminPassword=' + id
    else:
        setPassword = ''

    ini = 'KillingFloor-%s.ini' % id
    # print ini
    o = Template(' ./ucc-bin server \
$serverMap?game=KFmod.KFGameType?VACSecured=$vacEnable?MaxPlayers=$players?multihost=$hostIP?Port=$hostport?OldQueryPortNumber=$gsPort?ListenPort=$lPort$passwd \
-ini=$iniName -log=log/$server/$logName')
    options = o.substitute(hostIP=ip,
                           hostport=port,
                           gsPort=gameSpyPort,
                           lPort=listenPort,
                           players=slots,
                           serverMap=map,
                           userName=user,
                           server=serverDir,
                           vacEnable=vac,
                           passwd=setPassword,
                           iniName=ini,
                           logName='run-' + logDate + '.log'
                           )

    if debug == '1':
        interface = ('/usr/bin/screen -U -m -d -L -S %s ' % pidName)
    else:
        interface = ('/usr/bin/screen -U -m -d -S %s ' % pidName)

    runString = str(interface) + str(options)

    return runString


def voiceMumbleStart(id, user, type):
    # pidName = str(type + "_" + str(id))
    # pidFile = "/home/pid/" + user + "/" + pidName + ".pid"
    serverDir = type + "_" + str(id)

    return "/home/" + user + "/servers/" + serverDir + "/murmur.x86 -ini murmur.ini"
