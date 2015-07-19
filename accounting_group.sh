#!/bin/bash
#set -xv
# Exporting Environment Variables
#########################################
source /var/www/PHPQstat/phpqstat.conf
#########################################
source $SGE_ROOT/default/common/settings.sh 

if [ -d $RRD_ROOT ]; then mkdir -p $RRD_ROOT; fi

tcputime=0
# Inici BBDD
#################
for ((i=1; i<=$ngroup; i++)); do
creabbdd=""
   if ! [ -f $RRD_ROOT/qacct_g${i}.rrd ] ; then 
       creabbdd="${creabbdd}DS:cpusused:GAUGE:1000000:0:999995000 "
       creabbdd="${creabbdd}DS:cpulimit:GAUGE:1000000:0:999995000 "
       creabbdd="${creabbdd}DS:cpushare:GAUGE:1000000:0:999995000 "
       creabbdd="${creabbdd}DS:cpusqw:GAUGE:1000000:0:99995000 "
       rrdtool create $RRD_ROOT/qacct_g${i}.rrd -s 180 $creabbdd RRA:AVERAGE:0.5:1:576 RRA:AVERAGE:0.5:6:672 RRA:AVERAGE:0.5:24:732 RRA:AVERAGE:0.5:144:1460
   fi
done

# Actualitzo la BBDD
######################
for ((i=1; i<=$ngroup; i++)); do
data="N"
    #cputime=$(qacct -b 201101010000 -g g$i| grep -v GROUP |grep -v = | gawk '{printf "%d\n", $5/3600}') 
    #cpusused=$(qstat -u "g$i*"| awk '{if ($5 !~ /qw/){sum=sum+$9}}END{print sum}')
    #cpushare=$(echo "${CSHARE[${i}]}/$TSHARE*$TCORES" | bc -l)
    #cpuslimit=$((cpushare*2))
    #jobs=$(qstat -u "g$i*" | awk '{print $5}' | grep -v qw | wc -l)
    #jobsqw=$(qstat -u "g$i*" | awk '{print $5}' | grep qw | wc -l)
    cpusused=$(qstat -u "g$i*"| awk '{if ($4 ~ /g'$i'[a-zA-Z]/ && $5 !~ /qw/){sum=sum+$9}}END{print sum}')
    cpushare=$((${CLIMIT[${i}]}/2))
    cpuslimit=${CLIMIT[${i}]}
    #cpusqw=$(qstat -u "g$i*"| awk '{if ($5 ~ /qw/){sum=sum+$NF}}END{if (sum >0){ print "-"sum}else{print 0}}')
    cpusqw=$(qstat -u "g$i*"| awk '{if ($4 ~ /g'$i'[a-zA-Z]/ && $5 ~ /qw/){sum=sum+$NF}}END{if (sum >0){ print sum}else{print 0}}')
    if [ -z $cputime ] ; then cputime=0; fi
    if [ -z $cpusused ] ; then cpusused=0; fi
    data="$data:$cpusused:$cpuslimit:$cpushare:$cpusqw"
    rrdupdate $RRD_ROOT/qacct_g${i}.rrd $data
    echo "rrdupdate $RRD_ROOT/qacct_g${i}.rrd $data"
done

# Creo la grafica
######################

DATE=$(date '+%a %b %-d %H\:%M\:%S %Z %Y')

for ((i=1; i<=$ngroup; i++)); do
 unset datagrups
 datagrups="$datagrups DEF:cpusused=$RRD_ROOT/qacct_g${i}.rrd:cpusused:AVERAGE LINE1:cpusused#0000FF:cpusused "
 datagrups="$datagrups GPRINT:cpusused:MIN:%lf%s"
 datagrups="$datagrups GPRINT:cpusused:MAX:%lf%s"
 datagrups="$datagrups GPRINT:cpusused:AVERAGE:%lf%s\\l"
 datagrups="$datagrups DEF:cpulimit=$RRD_ROOT/qacct_g${i}.rrd:cpulimit:AVERAGE LINE1:cpulimit#FF0000:cpulimit "
 datagrups="$datagrups GPRINT:cpulimit:MIN:%lf%s"
 datagrups="$datagrups GPRINT:cpulimit:MAX:%lf%s"
 datagrups="$datagrups GPRINT:cpulimit:AVERAGE:%lf%s\\l"
 datagrups="$datagrups DEF:cpushare=$RRD_ROOT/qacct_g${i}.rrd:cpushare:AVERAGE LINE1:cpushare#00FF00:cpushare "
 datagrups="$datagrups GPRINT:cpushare:MIN:%lf%s"
 datagrups="$datagrups GPRINT:cpushare:MAX:%lf%s"
 datagrups="$datagrups GPRINT:cpushare:AVERAGE:%lf%s\\l"
 datagrups="$datagrups DEF:cpusqw=$RRD_ROOT/qacct_g${i}.rrd:cpusqw:AVERAGE LINE1:cpusqw#00FFFF:cpusqw "
 datagrups="$datagrups GPRINT:cpusqw:MIN:%lf%s"
 datagrups="$datagrups GPRINT:cpusqw:MAX:%lf%s"
 datagrups="$datagrups GPRINT:cpusqw:AVERAGE:%lf%s\\l"

rrdtool graph $WEB_ROOT/img/hourly.g${i}.png -a PNG -s -1hour -t "HPC Accounting (hourly)" -h 200 -w 600 -v "Used CPU's" COMMENT:"       Minimum" COMMENT:"   Maximum"  COMMENT:"    Average\\l" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/daily.g${i}.png -a PNG -s -1day -t "HPC Accounting (daily)" -h 200 -w 600 -v "Used CPU's" COMMENT:"       Minimum" COMMENT:"   Maximum"  COMMENT:"    Average\\l" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/weekly.g${i}.png -a PNG -s -1week -t "HCP Accounting (Weekly)" -h 200 -w 600 -v "Used CPU's" COMMENT:"       Minimum" COMMENT:"   Maximum"  COMMENT:"    Average\\l" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/monthly.g${i}.png -a PNG -s -1month -t "HPC Accounting (Monthly)" -h 200 -w 600 -v "Used CPU's" $datagrups COMMENT:"Last update\: $DATE" > /dev/null

rrdtool graph $WEB_ROOT/img/yearly.g${i}.png -a PNG -s -1year -t "HPC Accounting (Yearly)" -h 200 -w 600 -v "Used CPU's" $datagrups  COMMENT:"Last update\: $DATE" > /dev/null

done
