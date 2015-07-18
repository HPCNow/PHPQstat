![HPCNow!](https://github.com/HPCNow/PHPQstat/img/botton_logo.png)

ABOUT PHPQstat
==============================================
PHPQstat is a web interface that allows to connect to the useful commands of the Sun Grid Engine (SGE) batch queue system. With this interface, you can monitor your job status and your queues health at real time. In the Slurm branch you will find similar interface for this workload manager.
This project is developed and maintained by HPCNow! consulting : http://www.hpcnow.com

AUTHOR : Written by Jordi Blasco Pallarès (jordi.blasco@hpcnow.com).

REPORTING BUGS : Report bugs to GitHUB issue Tracker https://github.com/HPCNow/PHPQstat/issues

LICENSE : This is free software: you are free to change and redistribute it. GNU General Public License version 3.0 (GPLv3).
Version : 0.3.0 (July 2015)

https://github.com/HPCNow/PHPQstat

DEPENDENCIES
==============================================
You will need Apache server, php5, rrdtool and awk.

INSTALL
==============================================
(1) Copy all files in your web accesible filesystem or download the project using GIT:
```git clone git://github.com/HPCKP/PHPQstat.git```
(2) Setup the following paths on phpqstat.conf :
```
SGE_ROOT=/sge
RRD_ROOT=/var/www/PHPQstat/rrd
WEB_ROOT=/var/www/PHPQstat
```
(3) Add the following line on the crontab :
```*/3 * * * * /var/www/PHPQstat/accounting.sh > /dev/null 2>&1```

SCREENSHOTS
==============================================
![Real Time Queue Status](https://github.com/HPCNow/PHPQstat/img/realtime_queue_status.png)
![Running Jobs Per User](https://github.com/HPCNow/PHPQstat/img/irunning_jobs_per_user.png)
![Job Details](https://github.com/HPCNow/PHPQstat/img/job_details.png)

ROADMAP
==============================================
0.1 Functional
0.2 Real-time accounting
0.3 Slurm support
0.4 Security & Stable
0.5 Look & aspect improvement

TODO LIST
==============================================
* Group joblist
* all users joblist
* Job info (submission time, wait time, walltime, cputime, efficiency=(cputime/(walltime*slots))
* User auth via PAM
* Basic Job Submission

CHANGELOG
==============================================
0.3.0 Slurm integration
0.2.1 Migration to HPCNow GitHUB repo
0.2.0 Real-time accounting feature
0.1.3 Solved problems with Start time and Submission Time
0.1.2 Solved problem on cputime request on pending job
0.1.1 Install instructions and job details support
0.1.0 Project started

