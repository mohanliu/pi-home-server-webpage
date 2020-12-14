#! /bin/bash

curr=`free -t |grep Total:`
total=`echo $curr | awk -F' ' '{print $2}'`
used=`echo $curr | awk -F' ' '{print $3}'`

echo $used/$total*100 |bc -l | xargs printf "%.2f\n"
