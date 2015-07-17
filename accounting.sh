#!/bin/bash
#set -xv
# Exporting Environment Variables
#########################################
source /var/www/PHPQstat/phpqstat.conf
#########################################

function cpusused() { 
    if [ "$BQS" = 'SGE' ]; then
        cpusused=$(qstat -u *, -q $qname | gawk '{if ($5 !~ /qw/){sum=sum+$9}}END{print sum}')
    fi
    if [ "$BQS" = 'Slurm' ]; then
        cpusused=$(squeue -p $1 -t R -o "%.4C" | gawk '{if ($1 !~ /CPUS/){sum=sum+$1}}END{print sum}')
    fi
    echo $cpusused
}

function cpusqw() { 
    if [ "$BQS" = 'SGE' ]; then
        cpusqw=$(qstat -u *, | gawk '{if ($5 ~ /qw/){sum=sum+$NF}}END{if (sum >0){ print sum}else{print 0}}')
    fi
    if [ "$BQS" = 'Slurm' ]; then
        cpusqw=$(squeue -p $1 -t PD -o "%.4C" | gawk '{if ($1 !~ /CPUS/){sum=sum+$1}}END{print sum}')
    fi
    echo $cpusqw
}

if ! [ -d $RRD_ROOT ]; then mkdir -p $RRD_ROOT; fi
# DB Creation
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

# DB update
######################
i=0 
for q in $QUEUES; do
qname="${q}${QEXT}"
data="N"
    cpusused $qname
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
    cpusqw
    data="$data:$cpusqw"
    rrdupdate $RRD_ROOT/qacct_qw.rrd $data
    echo "rrdupdate $RRD_ROOT/qacct_qw.rrd $data"


# Print chart
######################
DATE=$(date '+%a %b %-d %H\:%M\:%S %Z %Y')

unset datagrups
i=0 
for q in $QUEUES; do
 datagrups="$datagrups DEF:${q}-used=$RRD_ROOT/qacct_${q}.rrd:${q}-used:AVERAGE LINE1:${q}-used#${COLOR[${i}]}:${q} "
 datagrups="$datagrups GPRINT:${q}-used:MIN:%12.0lf%s"
 datagrups="$datagrups GPRINT:${q}-used:MAX:%12.0lf%s"
 datagrups="$datagrups GPRINT:${q}-used:AVERAGE:%12.0lf%s\\l"
 i=$((i+1))
done

# Queue Waiting
 datagrups="$datagrups DEF:slots-qw=$RRD_ROOT/qacct_qw.rrd:slots-qw:AVERAGE LINE1:slots-qw#${COLOR[${i}]}:slots-qw"
 datagrups="$datagrups GPRINT:slots-qw:MIN:%12.0lf%s"
 datagrups="$datagrups GPRINT:slots-qw:MAX:%12.0lf%s"
 datagrups="$datagrups GPRINT:slots-qw:AVERAGE:%12.0lf%s\\l"

rrdtool graph $WEB_ROOT/img/hour.png -a PNG -s -1hour -t "HPC Accounting (hourly)" -h 200 -w 600 -v "Used CPU's" COMMENT:"                   Min Used" COMMENT:"   Max Used"  COMMENT:"    Avg Used \\l" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/day.png -a PNG -s -1day -t "HPC Accounting (daily)" -h 200 -w 600 -v "Used CPU's" COMMENT:"             Min Used" COMMENT:"   Max Used"  COMMENT:"    Avg Used \\l" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/week.png -a PNG -s -1week -t "HCP Accounting (Weekly)" -h 200 -w 600 -v "Used CPU's" COMMENT:"             Min Used" COMMENT:"   Max Used"  COMMENT:"    Avg Used \\l" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/month.png -a PNG -s -1month -t "HPC Accounting (Monthly)" -h 200 -w 600 -v "Used CPU's" COMMENT:"             Min Used" COMMENT:"   Max Used"  COMMENT:"    Avg Used \\l" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/year.png -a PNG -s -1year -t "HPC Accounting (Yearly)" -h 200 -w 600 -v "Used CPU's" COMMENT:"       Min Used" COMMENT:"   Max Used"  COMMENT:"    Avg Used \\l" $datagrups  COMMENT:"Last update\: $DATE" > /dev/null
