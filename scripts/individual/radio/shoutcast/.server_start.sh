#!/bin/bash
# description: SHOUTcast streaming MP3 radio station server
# chkconfig: 2345 99 00

echo ""
echo "Попытка запуска сервера ID%id<br/>"
echo -n "Начало в "
date
echo ""

SHOUTCAST='%path'
PID='/home/pid/%user'
NAME='shoutcast_%id'
TITLE="SHOUTcast 1.9.8 Server #%id"
IP='%ip'
PORT='%port'
DAEMON='sc_serv'
USER='%user'
INIFILE='sc_serv.conf'
OPTS="$INIFILE"

# Check if the pid files currently exist
    if [ ! -f $PID/$NAME.pid ]; then
        if [ -x $SHOUTCAST/$DAEMON ]; then
            touch $PID/$NAME
            echo "Starting $TITLE"
            echo "Server IP: $IP"
            echo "Server ports: $PORT"
            cd $SHOUTCAST
            # Запуск
            $SHOUTCAST/$DAEMON $OPTS > /dev/null 2>&1 & 
            # Если запустился - определим его PID
            ps -ef | grep "$NAME" | grep -v grep | awk '{print $2}'
            PIDNUM=$?
            # Если найден PID, сохранить его в файл .pid
            if [ $PIDNUM > 0 ]; then
            	ps -ef | grep "$NAME" | grep -v grep | awk '{print $2}' > $PID/$NAME.pid 
            fi
            # Prevent race condition on SMP kernels
             sleep 1
            # echo "$TITLE server process ID written to $PID/$NAME.pid"
            echo "$TITLE started."
        else
        	echo "Нет сервера. Не инициализирован?"
        fi
    else
        echo -e "Cannot start $TITLE.  Server is already running or updating."
        #exit 1
    fi	
exit 0