#!/bin/bash
#提取关键信息的主程序
#利用shell实现多线程
if [ "$#" != 2 ];then
    echo "format: bash operate.sh begin_product end_product" 
    echo "eg: ./operate.sh 1 5   will get the important message of product 1 to product 5"
    exit
fi
begin_product=$1
end_product=$2
for id in $(seq $begin_product $end_product)
do
    php -f operate.php $id &
done
wait 
echo "------------------ok-----------------"
