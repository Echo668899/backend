#!/bin/sh
#######公共进程########################
stillRunning=$(/bin/ps -ef |/bin/grep "/yc152_php/bin/shell.php crontab queue" |/bin/grep -v "grep")
if [ ! "$stillRunning" ] ; then
dateStr=$(date +"%Y-%m-%d %H:%M:%S")
echo "$dateStr crontab/queue  not started"
/usr/bin/php  /var/www/html/yc152_php/bin/shell.php crontab queue>/dev/null&
echo "crontab/queue  has started!"
fi
#######AI订单########################
stillRunning=$(/bin/ps -ef |/bin/grep "/yc152_php/bin/shell.php crontab ai" |/bin/grep -v "grep")
if [ ! "$stillRunning" ] ; then
dateStr=$(date +"%Y-%m-%d %H:%M:%S")
echo "$dateStr crontab ai  not started"
/usr/bin/php  /var/www/html/yc152_php/bin/shell.php crontab ai>/dev/null&
echo "crontab ai  has started!"
fi
#######Job默认队列########################
stillRunning=$(/bin/ps -ef |/bin/grep "/yc152_php/bin/shell.php job queue default mongodb" |/bin/grep -v "grep")
if [ ! "$stillRunning" ] ; then
dateStr=$(date +"%Y-%m-%d %H:%M:%S")
echo "$dateStr job queue default mongodb  not started"
/usr/bin/php  /var/www/html/yc152_php/bin/shell.php job queue default mongodb>/dev/null&
echo "job queue default mongodb  has started!"
fi
#######数据中心########################
stillRunning=$(/bin/ps -ef |/bin/grep "/yc152_php/bin/shell.php center data" |/bin/grep -v "grep")
if [ ! "$stillRunning" ] ; then
dateStr=$(date +"%Y-%m-%d %H:%M:%S")
echo "$dateStr center data  not started"
/usr/bin/php  /var/www/html/yc152_php/bin/shell.php center data>/dev/null&
echo "center data  has started!"
fi
#######上传查询########################
stillRunning=$(/bin/ps -ef |/bin/grep "/yc152_php/bin/shell.php crontab uploadFind" |/bin/grep -v "grep")
if [ ! "$stillRunning" ] ; then
dateStr=$(date +"%Y-%m-%d %H:%M:%S")
echo "$dateStr crontab uploadFind  not started"
/usr/bin/php  /var/www/html/yc152_php/bin/shell.php crontab uploadFind>/dev/null&
echo "crontab uploadFind  has started!"
fi

