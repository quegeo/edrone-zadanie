<?php
require_once 'ResHelper.class.php';
if(isset($argv[1])){
    if(ResHelper::sendFromCSV($argv[1], array(
        "action"=>'127.0.0.1/recive.php'
    ))){
        echo "Done \n";
        print_r(ResHelper::getLastTimes());
    }else{
        echo "Exception : ".(ResHelper::getLastException()->getMessage())."\n";
    }
}else{
    echo "argument of csv path required \n";
}
?>