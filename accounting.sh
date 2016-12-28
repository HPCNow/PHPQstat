#!/bin/bash
#set -xv
# Exporting Environment Variables
#########################################
source /var/www/PHPQstat/phpqstat.conf
#########################################

if ! [ -d $RRD_ROOT ]; then mkdir -p $RRD_ROOT; fi
QUEUES=$(qconf -sql | cut -d. -f1)

# Inici BBDD
#################
for q in $QUEUES; do
creabbdd=""
   if ! [ -f $RRD_ROOT/qacct_${q}.rrd ] ; then 
       creabbdd="${creabbdd}DS:${q}-used:GAUGE:1000000:0:999995000 "
       rrdtool create $RRD_ROOT/qacct_${q}.rrd -s 180 $creabbdd RRA:AVERAGE:0.5:1:576
   fi
done
# Queue Waiting
creabbdd="DS:slots-qw:GAUGE:1000000:0:999995000 "
if ! [ -f $RRD_ROOT/qacct_qw.rrd ] ; then
       rrdtool create $RRD_ROOT/qacct_qw.rrd -s 180 $creabbdd RRA:AVERAGE:0.5:1:576
fi

# Actualitzo la BBDD
######################
i=0 
for q in $QUEUES; do
# NOTE <---------------------------------------------------------------------
# If your Queues don't have the .q extension, you can comment the follow line
qname="${q}${QEXT}"
data="N"
    cpusused=$(qstat -u *, -q $qname | gawk '{if ($5 !~ /qw/){sum=sum+$9}}END{print sum}')
    cpuslimit=${CLIMIT[${i}]}
    if [ -z $cputime ] ; then cputime=0; fi
    if [ -z $cpusused ] ; then cpusused=0; fi
    data="$data:$cpusused"
    rrdupdate $RRD_ROOT/qacct_${q}.rrd $data
    echo "rrdupdate $RRD_ROOT/qacct_${q}.rrd $data"
    i=$((i+1))
done

# Queue Waiting
    data="N"
    cpusqw=$(qstat -u *, | gawk '{if ($5 ~ /qw/){sum=sum+$NF}}END{if (sum >0){ print sum}else{print 0}}')
    data="$data:$cpusqw"
    rrdupdate $RRD_ROOT/qacct_qw.rrd $data
    echo "rrdupdate $RRD_ROOT/qacct_qw.rrd $data"


# Creo la grafica
######################
DATE=$(date '+%a %b %-d %H\:%M\:%S %Z %Y')

unset datagrups
i=0 
for q in $QUEUES; do
 datagrups="$datagrups DEF:${q}-used=$RRD_ROOT/qacct_${q}.rrd:${q}-used:AVERAGE LINE2:${q}-used#${COLOR[${i}]}:${q} "
 datagrups="$datagrups GPRINT:${q}-used:MIN:%12.0lf%s"
 datagrups="$datagrups GPRINT:${q}-used:MAX:%12.0lf%s"
 datagrups="$datagrups GPRINT:${q}-used:AVERAGE:%12.0lf%s\\l"
 i=$((i+1))
done

# Queue Waiting
 datagrups="$datagrups DEF:slots-qw=$RRD_ROOT/qacct_qw.rrd:slots-qw:AVERAGE LINE2:slots-qw#${COLOR[${i}]}:slots-qw"
 datagrups="$datagrups GPRINT:slots-qw:MIN:%12.0lf%s"
 datagrups="$datagrups GPRINT:slots-qw:MAX:%12.0lf%s"
 datagrups="$datagrups GPRINT:slots-qw:AVERAGE:%12.0lf%s\\l"

HEIGHT=200
WIDTH=600
 
rrdtool graph $WEB_ROOT/img/hour.png -a PNG -s -1hour -t "HPC Accounting (hourly)" -h  $HEIGHT -w  $WIDTH -v "Used CPU's" COMMENT:"                   Min Used" COMMENT:"   Max Used"  COMMENT:"    Avg Used \\l" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/day.png -a PNG -s -1day -t "HPC Accounting (daily)" -h $HEIGHT -w $WIDTH -v "Used CPU's" COMMENT:"             Min Used" COMMENT:"   Max Used"  COMMENT:"    Avg Used \\l" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/week.png -a PNG -s -1week -t "HPC Accounting (Weekly)" -h $HEIGHT -w $WIDTH -v "Used CPU's" COMMENT:"             Min Used" COMMENT:"   Max Used"  COMMENT:"    Avg Used \\l" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/month.png -a PNG -s -1month -t "HPC Accounting (Monthly)" -h $HEIGHT -w $WIDTH -v "Used CPU's" COMMENT:"             Min Used" COMMENT:"   Max Used"  COMMENT:"    Avg Used \\l" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/year.png -a PNG -s -1year -t "HPC Accounting (Yearly)" -h $HEIGHT -w $WIDTH -v "Used CPU's" COMMENT:"       Min Used" COMMENT:"   Max Used"  COMMENT:"    Avg Used \\l" $datagrups  COMMENT:"Last update\: $DATE" > /dev/null
