#!/bin/bash
echo "Content-type: text/html; charset=UTF-8"
echo ""
echo ""
echo "Попытка рестарта сервера SHOUTcast ID%id"
echo -n "Начало в "
date
echo ""
#
# chkconfig: 2345 20 80
# description: Mumble Server Init Script

./.server_stop_%id.sh

./.server_start_%id.sh