<?php
class ResHelper{ 
    private static $lastException   = null;
    private static $lastTimes      =  null;
    private function __construct(){}

    private static function loadCSV($filename){
        $row = 0;
        $all_data=[];
        try {
            if (($handle = fopen($filename, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if( $data[0]=="Imie" ||
                        $data[1]=="Nazwisko"||
                        $data[2]=="Email"||
                        $data[3]=="Kategorie")
                        continue;
                    $all_data[$row]['name']=$data[0];
                    $all_data[$row]['surname']=$data[1];
                    $all_data[$row]['email']=$data[2];
                    $all_data[$row]['kategorie']=$data[3];
                    $row++;
                }
                fclose($handle);
            } else {
                throw new Exception('Cannot read the configuration file: '.$filename);
            }
        } catch(Exception $e){
            self::$lastException=$e;
            return [];
        }
        return $all_data;
    }

    private static function sendData($data,$settings){
        if(!empty($data)){
            try {
                foreach($data as $row){
                    $data_string = json_encode($row);
                    $ch = curl_init($settings['action']);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                    curl_setopt($ch, CURLINFO_CONNECT_TIME, true);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, true);
                    curl_setopt($ch, CURLINFO_TOTAL_TIME, true);
                    curl_setopt($ch, CURLINFO_REDIRECT_TIME, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_FAILONERROR, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($data_string))
                    );
                    curl_exec($ch);
                    $req_header=curl_getinfo($ch);
                    curl_close($ch);
                    if($req_header['http_code']!="200") throw new Exception("Error on execute cUrl !");
                    else self::$lastTimes[]=$req_header['total_time']."s";
                }
                return true;
            } catch(Exception $e){
                self::$lastException=$e;
                return false;
            }
        } else {
            return false;
        }
    }

    public static function sendFromCSV($filename,$settings){
        return self::sendData(self::loadCSV($filename), $settings);
    }
    public static function getLastException(){
        return self::$lastException;
    }
    public static function getLastTimes(){
        return self::$lastTimes;
    }
}