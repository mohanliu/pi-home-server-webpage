#! /bin/bash

freq=`vcgencmd measure_clock arm |sed -e "s/^.*=//"`

echo $freq/1000000000 |bc -l |xargs printf "%.2f\n"
