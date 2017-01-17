ABOUT PHPQstat
==============================================
**PHPQstat** is a web interface to qstat and other useful commands of the Grid Engine (GE) batch queue system.
With this interface, you and your users can monitor your jobs and your queue status conveniently via a web browser.

**AUTHORS**  
UGE support, HTML5 interface, qstat reduce and remote master options added by Lydia Sevelt (LydiaSevelt@gmail.com)  
Originally written by Jordi Blasco Pallarès (jordi.blasco@hpcnow.com).

**REPORTING BUGS**  
Report bugs to GitHUB issue Tracker https://github.com/LydiaSevelt/PHPQstat/issues

**ADDITIONAL LIBRARIES**  
The HTML5 interface utilizes the excellent datatables (https://datatables.net) and jquery (https://jquery.com) javascript libraries.  

**TESTED WITH**  
Son of Grid Engine 8.1.9  
Univa Grid Engine 8.3.1p6  

**LICENSE**  
This is free software: you are free to change and redistribute it. GNU General Public License version 3.0 (GPLv3).

**Version**  
0.3.0 (December 2016)

https://github.com/LydiaSevelt/PHPQstat

SCREENSHOTS
==============================================
Screenshots were taken from a test instance of Son of Grid Engine 8.1.9


Queue Status page with two test queues.
![Alt text](https://cloud.githubusercontent.com/assets/4594964/21457190/37e6a6fc-c8fb-11e6-8b6c-f1b04b920e5c.jpg "Queue Status")


All Jobs on the cluster, both running and in queue, tables are sortable by field, in the screenshot I am using the search feature to filter the running jobs table.
![Alt text](https://cloud.githubusercontent.com/assets/4594964/21457203/5405e6b8-c8fb-11e6-9039-4af29a50761a.jpg "Job Status")


All Jobs on the cluster with multiple parallel environment jobs with the new display scheme that shows all queues grouped with a single job.  
![Alt text](https://cloud.githubusercontent.com/assets/4594964/22034925/fa44aec8-dcbb-11e6-9054-4e3c53f83569.jpg "Job Status with parallel environment jobs")


Job information page displaying some details about a running job.
![Alt text](https://cloud.githubusercontent.com/assets/4594964/21457210/5d1d7108-c8fb-11e6-8609-79425139d3f2.jpg "Job Info")


Hosts Status with only the one desktop as the single host, so not very impressive looking.
![Alt text](https://cloud.githubusercontent.com/assets/4594964/21457439/53acf240-c8fd-11e6-9c04-31d40a264593.jpg "Hosts Status")


DEPENDENCIES
==============================================
Basic setup (on sge_master host):  
apache, php5, rrdtool and awk.

Remote master setup:  
webserver host: apache, php5, rrdtool, awk, snmp-utils  
sge_master host: snmpd, awk  

INSTALL
==============================================
1. Copy all files in your web accesible filesystem or download the project using GIT:  
    git clone https://github.com/LydiaSevelt/PHPQstat
2. Setup the following paths on phpqstat.conf :  
    SGE_ROOT=/opt/sge  
    SGE_CELL=default  
    RRD_ROOT=/var/www/PHPQstat/rrd  
    WEB_ROOT=/var/www/PHPQstat  
3. Edit the line: "source /var/www/PHPQstat/phpqstat.conf" to point to the location of your phpqstat.conf  
   in your web root in the files :  
    accounting.sh  
    qinfo.sh  
4. Setup the following config variables in config.inc.php :  
    $qstat_reduce="yes";  
    $cache_time="3";  
5. If using Univa Grid Engine set the variable in config.inc.php :  
    $UGE="yes";  
6. If using Son of Grid Engine and you do *not* already have a /$SGE_ROOT/$SGE_CELL/common/settings.sh file  
   then copy the sog.8.1.9.settings.sh file to /$SGE_ROOT/$SGE_CELL/common/settings.sh and modify  
   the variables to match your config :  
     export SGE_ROOT="/opt/sge"  
     export SGE_CELL="default"  
     export SGE_CLUSTER_NAME="p6444"  
     export DRMAA_LIBRARY_PATH="/opt/sge/lib//libdrmaa.so"  
7. If using qstat_reduce set LOAD_WAIT variable in phpqstat.conf for high load average protection :  
    LOAD_WAIT=10.00  
8. Add the following line to the proper users crontab, making sure you replace /var/www/PHPQstat with the proper path :  
    */3 * * * * /var/www/PHPQstat/accounting.sh > /dev/null 2>&1

  SETTING UP A REMOTE MASTER CONFIG
  ----------------------------------------------
9. Set REMOTE_MASTER in phpqstat.conf to the hostname of sge_master server :  
    REMOTE_MASTER=sgemaster.company.com  
10. Configure snmpd on sge_master host to provide uptime and load information via community public :  
    ```
    com2sec notConfigUser  default       public  
    group   notConfigGroup v1           notConfigUser  
    view    systemview    included   .1.3.6.1.4.1.2021.10  
    access  notConfigGroup ""      any       noauth    exact  systemview none none  
    ```
    
  OPTIONAL
  ----------------------------------------------
11. Replace PHPQstat/img/logo.png with the logo of your company/school to brand the page  

TODO LIST
==============================================
* Add install script to take care of some of the tedium automatically
* Add job accounting page and qstat_reduce functionality to allow users to view stats on completed jobs
* Admin page - config variables, display options, project/department, etc
* Add additional job information to job page
* Completely replace rrdtool graphs with something pretty (grafana?)

CHANGELOG
==============================================
* 0.1.0 Project started
* 0.1.1 Install instructions and job details support
* 0.1.2 Solved problem on cputime request on pending job
* 0.1.3 Solved problems with Start time and Submission Time
* 0.2.0 Real-time accounting feature
* 0.2.1 Migration to HPCNow GitHUB repo
* 0.2.2 Added qstat_reduce to cache xml files and only refesh based a time interval with high load average protection
* 0.2.3 Added ability to run phpqstat on a webserver that is a submit host, eliminating the need to run on the sge_master node
* 0.3.0 Added new HTML5 interface to fix look and feel as well as add functionality for users, This utilizes the excellent datatables and jquery-ui javascript libraries. Added UGE support option.
* 0.3.1 Parallel environment jobs are now displayed in a single line with all active queues associated. Many other small bug fixes and improvments as well. Install instructions also updated.
