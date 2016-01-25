#!/bin/bash
#set -xv
# Exporting Environment Variables
#########################################
source /var/www/PHPQstat/phpqstat.conf
#########################################

# check to see if another qinfo.sh is already running
if [ -e /tmp/qinfo.run ]; then
	# qinfo already running, wait until it completes and exit.
	while (true); do
		sleep 1
		if [ ! -e /tmp/qinfo.run ]; then
			break
		fi
	done
	exit 0
fi

# no other qinfo running, touch runfile
touch /tmp/qinfo.run

# check load average
# get five minute load average and convert to decial
if [ "${REMOTE_MASTER}" != "" ]; then
	LOAD_FIVE=$(snmpwalk -v 1 -r 1 -c public -O e ${REMOTE_MASTER} .1.3.6.1.4.1.2021.10 | gawk '$1 == "UCD-SNMP-MIB::laLoad.2" { print $4 }')
	if [ "${LOAD_FIVE}" == "" ]; then
		LOAD_FIVE="Not Available"
		LOAD="0.00"
	fi
else
	LOAD_FIVE=$(uptime | gawk {' sub(/,$/,"",$11);print $11 '})
fi
LOAD=$(echo $LOAD_FIVE | awk -F '.' {' print $1$2 '})

# Convert LOAD_WAIT variable
LOAD_WAIT=$(echo $LOAD_WAIT | awk -F '.' {' print $1$2 '})

if [ -e /tmp/load.xml ]; then
	LAST_CHECK=$(gawk -F "date'>" {' sub(/<\/last>.*$/,"",$2);print $2 '} /tmp/load.xml)
else
	LAST_CHECK=$(date)
	echo "<?xml version='1.0'?><data><check name='wait'>no</check><load name='avg'>${LOAD_FIVE}</load><last name='date'>${LAST_CHECK}</last></data>" > /tmp/load.xml
fi

# check for load greater than or equal to specified amount
if [ ${LOAD} -ge ${LOAD_WAIT} ]; then
	# wait for load to reduce
	echo "<?xml version='1.0'?><data><check name='wait'>yes</check><load name='avg'>${LOAD_FIVE}</load><last name='date'>${LAST_CHECK}</last></data>" > /tmp/load.xml
	exit 0
else
	# update check time
	echo "<?xml version='1.0'?><data><check name='wait'>no</check><load name='avg'>${LOAD_FIVE}</load><last name='date'>$(date)</last></data>" > /tmp/load.xml
fi

# qhost data
./qhostout /tmp/qhost.xml

# qstat queue summary
./gexml -u all -R -o /tmp/qstat_queues.xml

# qstat all
./gexml -u all -o /tmp/qstat_all.xml

# complete, remove runfile
rm /tmp/qinfo.run
