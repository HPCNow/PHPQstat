ABOUT PHPQstat
==============================================
**PHPQstat** is a web interface to qstat and other useful commands of the Grid Engine (GE) batch queue system.
With this interface, you and your users can monitor your jobs and your queue status conveniently via a web browser.

**AUTHORS**  
UGE support, HTML5 interface, qstat reduce and remote master options added by Lydia Sevelt (LydiaSevelt@gmail.com)  
Originally written by Jordi Blasco Pallarès (jordi.blasco@hpcnow.com).

**REPORTING BUGS**  
Report bugs to GitHUB issue Tracker https://github.com/LydiaSevelt/PHPQstat/issues

**LICENSE**  
This is free software: you are free to change and redistribute it. GNU General Public License version 3.0 (GPLv3).

**Version**  
0.3.0 (December 2016)

https://github.com/LydiaSevelt/PHPQstat

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
  RRD_ROOT=/var/www/PHPQstat/rrd  
  WEB_ROOT=/var/www/PHPQstat  
3. Setup the following config variables in config.inc.php :  
    $qstat_reduce="yes";  
    $cache_time="3";  
4. If using Univa Grid Engine set the variable in config.inc.php :  
    $UGE="yes";  
5. If using qstat_reduce set LOAD_WAIT variable in phpqstat.conf for high load average protection :  
    LOAD_WAIT=10.00  
6. Add the following line to the proper users crontab :  
    */3 * * * * /var/www/PHPQstat/accounting.sh > /dev/null 2>&1

  SETTING UP A REMOTE MASTER CONFIG
  ----------------------------------------------
7. Set REMOTE_MASTER in phpqstat.conf to the hostname of sge_master server :  
    REMOTE_MASTER=sgemaster.company.com  
8. Configure snmpd on sge_master host to provide uptime and load information via community public :  
    ```
    com2sec notConfigUser  default       public  
    group   notConfigGroup v1           notConfigUser  
    view    systemview    included   .1.3.6.1.4.1.2021.10  
    access  notConfigGroup ""      any       noauth    exact  systemview none none  
    ```
    
  OPTIONAL
  ----------------------------------------------
9. Replace PHPQstat/img/logo.png with the logo of your company/school to brand the page  

TODO LIST
==============================================
* Add additional job information to job page
* Add job accounting page and qstat_reduce functionality to allow users to view stats on completed jobs
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
