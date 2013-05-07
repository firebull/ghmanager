#!/bin/sh
# description: SHOUTcast streaming MP3 radio station server
# chkconfig: 2345 99 00

echo ""
echo "Попытка остановки сервера ID%id<br/>"
echo -n "Начало в "
date
echo ""

SHOUTCAST='%path'
PID='/home/pid/%user'
TITLE="SHOUTcast 1.9.8 Server #%id"
NAME='shoutcast_%id'

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
	
exit 0
