#!/bin/bash
#爬虫程序入口，利用shell命令实现多线程
if [ "$#" != "2" ];then
    echo "format: ./spider.sh begin_page end_page"
    echo "eg:  bash spider.sh 1 10"
    echo "eg: ./spider.sh 1 10"
    exit
fi
begin=$1
end=$2
for i in $(seq $begin $end)
do
    if [ ! -d "$i" ];then
        mkdir "$i"
    fi
    echo "Thread $i is running on the background..."
    echo "You can close the terminal to close it..."
    php -f spider.php $i &
done
wait
echo "-----------------------ok-------------------------"
