#!/bin/bash
#set -xv
# Exporting Environment Variables
#########################################
source /var/www/PHPQstat/phpqstat.conf
#########################################

if [ -d $RRD_ROOT ]; then mkdir -p $RRD_ROOT; fi
QUEUES=$(qconf -sql | cut -d. -f1)

# Inici BBDD
#################
for q in $QUEUES; do
creabbdd=""
   if ! [ -f $RRD_ROOT/qacct_${q}.rrd ] ; then 
       creabbdd="${creabbdd}DS:${q}-used:GAUGE:1000000:0:999995000 "
       creabbdd="${creabbdd}DS:${q}-qw:GAUGE:1000000:0:99995000 "
       rrdtool create $RRD_ROOT/qacct_${q}.rrd -s 180 $creabbdd RRA:AVERAGE:0.5:1:576 RRA:AVERAGE:0.5:144:1460
   fi
done

# Actualitzo la BBDD
######################
i=0 
for q in $QUEUES; do
# NOTE <---------------------------------------------------------------------
# If your Queues don't have the .q extension, you can comment the follow line
qname="${q}.q"
data="N"
    cpusused=$(qstat -u *, -q $qname | awk '{if ($5 !~ /qw/){sum=sum+$9}}END{print sum}')
    cpuslimit=${CLIMIT[${i}]}
    cpusqw=$(qstat -u *, -q $qname| awk '{if ($5 ~ /qw/){sum=sum+$NF}}END{if (sum >0){ print sum}else{print 0}}')
    if [ -z $cputime ] ; then cputime=0; fi
    if [ -z $cpusused ] ; then cpusused=0; fi
    data="$data:$cpusused:$cpusqw"
    rrdupdate $RRD_ROOT/qacct_${q}.rrd $data
    echo "rrdupdate $RRD_ROOT/qacct_${q}.rrd $data"
    i=$((i+1))
done



# Creo la grafica
######################
DATE=$(date '+%a %b %-d %H\:%M\:%S %Z %Y')

unset datagrups
i=0 
for q in $QUEUES; do
 datagrups="$datagrups DEF:${q}-used=$RRD_ROOT/qacct_${q}.rrd:${q}-used:AVERAGE LINE1:${q}-used#${COLOR[${i}]}:${q} "
 datagrups="$datagrups GPRINT:${q}-used:MIN:%6.0lf%s"
 datagrups="$datagrups GPRINT:${q}-used:MAX:%6.0lf%s"
 datagrups="$datagrups GPRINT:${q}-used:AVERAGE:%6.0lf%s "
 datagrups="$datagrups DEF:${q}-qw=$RRD_ROOT/qacct_${q}.rrd:${q}-qw:AVERAGE LINE1:${q}-qw#${COLOR[${i}]} "
 datagrups="$datagrups GPRINT:${q}-qw:MIN:%6.0lf%s"
 datagrups="$datagrups GPRINT:${q}-qw:MAX:%6.0lf%s"
 datagrups="$datagrups GPRINT:${q}-qw:AVERAGE:%6.0lf%s\\l"
 i=$((i+1))
done

rrdtool graph $WEB_ROOT/img/hour.png -a PNG -s -1hour -t "HPC Accounting (hourly)" -h 200 -w 600 -v "Used CPU's" COMMENT:"             Min Used" COMMENT:"   Max Used"  COMMENT:"    Avg Used" COMMENT:" Min QW" COMMENT:"   Max QW"  COMMENT:"    Avg QW \\l" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/day.png -a PNG -s -1day -t "HPC Accounting (daily)" -h 200 -w 600 -v "Used CPU's" COMMENT:"       Min Used" COMMENT:"   Max Used"  COMMENT:"    Avg Used" COMMENT:" Min QW" COMMENT:"   Max QW"  COMMENT:"    Avg QW \\l" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/week.png -a PNG -s -1week -t "HCP Accounting (Weekly)" -h 200 -w 600 -v "Used CPU's" COMMENT:"       Min Used" COMMENT:"   Max Used"  COMMENT:"    Avg Used" COMMENT:" Min QW" COMMENT:"   Max QW"  COMMENT:"    Avg QW \\l" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/month.png -a PNG -s -1month -t "HPC Accounting (Monthly)" -h 200 -w 600 -v "Used CPU's" COMMENT:"       Min Used" COMMENT:"   Max Used"  COMMENT:"    Avg Used" COMMENT:" Min QW" COMMENT:"   Max QW"  COMMENT:"    Avg QW \\l" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/year.png -a PNG -s -1year -t "HPC Accounting (Yearly)" -h 200 -w 600 -v "Used CPU's" COMMENT:"       Min Used" COMMENT:"   Max Used"  COMMENT:"    Avg Used" COMMENT:" Min QW" COMMENT:"   Max QW"  COMMENT:"    Avg QW \\l" $datagrups  COMMENT:"Last update\: $DATE" > /dev/null
