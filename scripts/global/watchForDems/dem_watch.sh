#!/bin/bash

echo ""
echo "Попытка запуска скрипта поиска демок <br/>"
echo -n "Начало в "
date
echo ""

WATCHER='/images/scripts/global/watchForDems'
PID='/var/run'
NAME='dems_watcher'
TITLE="Watcher for TV demos "
IP='%ip'
PORT='%port'
DAEMON='watch.py'
USER='root'
DIRSFILE='dirs.lst'
OPTS="-d $DIRSFILE"

# Screen command
INTERFACE="/usr/bin/screen -U -m -d -S $NAME"

# Check if the pid files currently exist
start () {
    if [ ! -f $PID/$NAME.pid ]; then
        if [ -x $WATCHER/$DAEMON ]; then
            touch $PID/$NAME.pid
            echo "Starting $TITLE"
            cd $WATCHER
            # Запуск
            $INTERFACE $WATCHER/$DAEMON $OPTS
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
        	echo "Не найден скрипт"
        fi
    else
        echo -e "Cannot start $TITLE.  Server is already running or updating."
        #exit 1
    fi	
}

stop () {
	if [ -f $PID/$NAME.pid ]; then
	   rm -f $PID/$NAME
	   echo "Stopping $TITLE."
	# Get the process ID from the pid file we created earlier
		for id in `cat $PID/$NAME.pid`
		    do kill -9 $id
		    echo "Killing process ID $id"
		    echo "Removing $TITLE pid file"
		    # Remove server pid file
		    rm -rf $PID/$NAME.pid
		    break
		done
		echo "$TITLE stopped."
	    else
	        echo -e "Cannot stop $TITLE.  Server is not running."
			#exit 1
	    fi	
}

case "$1" in
    'start')
        start
        ;;
    'stop')
        stop
        ;;
    'restart')
        stop
        sleep 1
        start
        ;;
    *)
        echo "Usage $0 start|stop|restart"
esac

exit 0