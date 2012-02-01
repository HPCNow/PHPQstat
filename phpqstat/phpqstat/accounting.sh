#!/bin/bash
#set -xv
# Exporting Environment Variables
#########################################
source ./phpqstat.conf
#########################################

if [ -d $RRD_ROOT ]; then mkdir -p $RRD_ROOT; fi
QUEUES=$(qconf -sql)

# Inici BBDD
#################
creabbdd=""
for q in $QUEUES; do
   if ! [ -f $RRD_ROOT/qacct_${q}.rrd ] ; then 
       creabbdd="${creabbdd}DS:cpusused:GAUGE:1000000:0:999995000 "
       creabbdd="${creabbdd}DS:cpulimit:GAUGE:1000000:0:999995000 "
       creabbdd="${creabbdd}DS:cpusqw:GAUGE:1000000:0:99995000 "
       rrdtool create $RRD_ROOT/qacct_${q}.rrd -s 180 $creabbdd RRA:AVERAGE:0.5:1:576 RRA:AVERAGE:0.5:24:732 RRA:AVERAGE:0.5:144:1460
   fi
done

# Actualitzo la BBDD
######################
for q in $QUEUES; do
data="N"
    cpusused=$(qstat -u -q $q| awk '{if ($5 !~ /qw/){sum=sum+$9}}END{print sum}')
    #cpushare=$((${CLIMIT[${i}]}/2))
    cpuslimit=${CLIMIT[${i}]}
    cpusqw=$(qstat -u -q $q| awk '{if ($5 ~ /qw/){sum=sum+$NF}}END{if (sum >0){ print sum}else{print 0}}')
    if [ -z $cputime ] ; then cputime=0; fi
    if [ -z $cpusused ] ; then cpusused=0; fi
    #data="$data:$cpusused:$cpuslimit:$cpushare:$cpusqw"
    data="$data:$cpusused:$cpuslimit:$cpusqw"
    rrdupdate $RRD_ROOT/qacct_${q}.rrd $data
    echo "rrdupdate $RRD_ROOT/qacct_${q}.rrd $data"
done


# Creo la grafica
######################
DATE=$(date '+%a %b %-d %H\:%M\:%S %Z %Y')

unset datagrups
for q in $QUEUES; do
 datagrups="$datagrups DEF:cpusused=$RRD_ROOT/qacct_${q}.rrd:cpusused:AVERAGE LINE1:cpusused#${COLOR[${i}]}:cpusused "
 datagrups="$datagrups GPRINT:cpusused:MIN:%lf%s"
 datagrups="$datagrups GPRINT:cpusused:MAX:%lf%s"
 datagrups="$datagrups GPRINT:cpusused:AVERAGE:%lf%s\\l"
 datagrups="$datagrups DEF:cpusqw=$RRD_ROOT/qacct_${q}.rrd:cpusqw:AVERAGE LINE1:cpusqw#${COLOR[${i}]}:cpusqw "
 datagrups="$datagrups GPRINT:cpusqw:MIN:%lf%s"
 datagrups="$datagrups GPRINT:cpusqw:MAX:%lf%s"
 datagrups="$datagrups GPRINT:cpusqw:AVERAGE:%lf%s\\l"
done

rrdtool graph $WEB_ROOT/img/hourly.png -a PNG -s -1hour -t "HPC Accounting (hourly)" -h 200 -w 600 -v "Used CPU's" COMMENT:"       Minimum" COMMENT:"   Maximum"  COMMENT:"    Average\\l" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/daily.png -a PNG -s -1day -t "HPC Accounting (daily)" -h 200 -w 600 -v "Used CPU's" COMMENT:"       Minimum" COMMENT:"   Maximum"  COMMENT:"    Average\\l" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/weekly.png -a PNG -s -1week -t "HCP Accounting (Weekly)" -h 200 -w 600 -v "Used CPU's" COMMENT:"       Minimum" COMMENT:"   Maximum"  COMMENT:"    Average\\l" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/monthly.png -a PNG -s -1month -t "HPC Accounting (Monthly)" -h 200 -w 600 -v "Used CPU's" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/yearly.png -a PNG -s -1year -t "HPC Accounting (Yearly)" -h 200 -w 600 -v "Used CPU's" $datagrups  COMMENT:"Last update\: $DATE" > /dev/null
