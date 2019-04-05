<?php


//////////////////////////////////////////////
//日本の市場休日判定
//日付の方はyyyy-mm-ddを想定
//$date:休日判定する日付
//true:平日、false:休日
//////////////////////////////////////////////
class checkHoliday{
    public static function check_holiday($date){
        if(self::check_sunday_saturday($date)==false or self::check_national_holiday($date)==false or self::check_hurikae_holiday($date)==false){
            return false;
        }else{
            return true;
        }
    }

    private static function check_sunday_saturday($date){
        $datetime = new DateTime($date);
        $w = (int)$datetime->format('w');
        if($w>=1 and $w<=5){
            return true;
        }else{
            return false;
        }
    }

    private static function check_national_holiday($date){
        $datetime = new DateTime($date);

        $func="checkHoliday::check_national_holiday_".$datetime->format('m');
        return $func($datetime);

    }

    private static function check_national_holiday_01($datetime){
        //正月三が日
        if($datetime->format('d')=='01' or $datetime->format('d')=='02' or $datetime->format('d')=='03'){
            return false;
            //成人の日
        }elseif($datetime->format('d')>='08' and $datetime->format('d')<='14'){
            $w = (int)$datetime->format('w');
            if($w==1){
                return false;
            }
        }
    }

    private static function check_national_holiday_02($datetime){
        //建国記念日
        if($datetime->format('d')=='11'){
            return false;
        }
        return true;
    }

    private static function check_national_holiday_03($datetime){
        //春分の日
        if($datetime->format('d')==floor(20.8431+0.242194*($datetime->format('Y')-1980))-floor(($datetime->format('Y')-1980)/4)){
            return false;
        }
        return true;
    }

    private static function check_national_holiday_04($datetime){
        //昭和の日
        if($datetime->format('d')=='29'){
            return false;
        }
        return true;
    }

    private static function check_national_holiday_05($datetime){
        //憲法記念日、みどりの日、こどもの日
        if($datetime->format('d')=='03' or $datetime->format('d')=='04' or $datetime->format('d')=='05'){
            return false;
        }
        //新天皇即位
        if($datetime->format('Y')=='2019' and $datetime->format('d')=='01'){
            return false;
        }
        return true;
    }

    private static function check_national_holiday_06($datetime){
        return true;
    }

    private static function check_national_holiday_07($datetime){
        //海の日
        if($datetime->format('Y')>=1996 and $datetime->format('Y')<=2002){
            if($datetime->format('d')=='20'){
                return false;
            }
        }else{
            if($datetime->format('d')>=15 and $datetime->format('d')<=21){
                $w = (int)$datetime->format('w');
                if($w==1){
                    return false;
                }
            }
        }
        return true;
    }

    private static function check_national_holiday_08($datetime){
        //山の日
        if($datetime->format('d')=='11'){
            //2016年以降なので
            if($datetime->format('Y')>=2016){
                return false;
            }
        }
        return true;
    }

    private static function check_national_holiday_09($datetime){
        //敬老の日
        if($datetime->format('Y')>=2003){
            if($datetime->format('d')>=15 and $datetime->format('d')<=21){
                $w = (int)$datetime->format('w');
                if($w==1){
                    return false;
                }
            }
        }else{
            if($datetime->format('d')=='15'){
                return false;
            }
        }
        //秋分の日
        if($datetime->format('d')==floor(23.2488+0.242194*($datetime->format('Y')-1980))-floor(($datetime->format('Y')-1980)/4)){
            return false;
        }
        return true;
    }

    private static function check_national_holiday_10($datetime){
        //体育の日
        if($datetime->format('Y')>=2000){
            if($datetime->format('d')>='08' and $datetime->format('d')<='14'){
                $w = (int)$datetime->format('w');
                if($w==1){
                    return false;
                }
            }
        }else{
            if($datetime->format('d')=='10'){
                return false;
            }
        }
        return true;
    }

    private static function check_national_holiday_11($datetime){
        //文化の日
        if($datetime->format('d')=='03'){
            return false;
            //勤労感謝の日
        }elseif($datetime->format('d')=='23'){
            return false;
        }
        return true;
    }

    private static function check_national_holiday_12($datetime){
        //天皇誕生日
        if($datetime->format('d')=='23'){
            if($datetime->format('Y')>=1989){
                return false;
            }
            //大晦日
        }elseif($datetime->format('d')=='31'){
            return false;
        }
        return true;
    }

    private static function check_hurikae_holiday($date){
        $yesterday=strftime('%Y-%m-%d', strtotime('-1 day', strtotime($date)));
        $datetime = new DateTime($yesterday);
        $w = (int)$datetime->format('w');

        list($year,$month,$day)=explode('-', $date);

        if($w==0){
            if(!($month=='01' and $day=='04')){
                if(check_national_holiday($yesterday)==false){
                    return false;
                }
            }
        }


        if($month=='05'){
            if($day=='06'){
                $datetime = new DateTime($date);
                $w = (int)$datetime->format('w');
                if($year>=2007){
                    if($w==2 or $w==3){
                        return false;
                    }
                }
            }
        }

        //祝日と祝日で挟まれたときに振替休日になる
        $tommorow=strftime('%Y-%m-%d', strtotime('+1 day', strtotime($date)));
        if(self::check_national_holiday($yesterday)==false and self::check_national_holiday($tommorow)==false){
            return false;
        }
        return true;
    }

}

//////////////////////////////////////////////
//前営業日を算出
//$date:前営業日を知りたい営業日
/////////////////////////////////////////////
function calc_yesterday($date){
	do{
		$date=strftime('%Y-%m-%d', strtotime('-1 day', strtotime($date)));
	}while(!checkHoliday::check_holiday($date));
	return $date;
}

//////////////////////////////////////////////
//翌営業日を算出
//$date:前営業日を知りたい営業日
/////////////////////////////////////////////
function calc_nextday($date){
	do{
		$date=strftime('%Y-%m-%d', strtotime('+1 day', strtotime($date)));
	}while(!checkHoliday::check_holiday($date));
	return $date;
}

function calc_preday($date){
    do{
        $date=strftime('%Y-%m-%d', strtotime('-1 day', strtotime($date)));
    }while(!checkHoliday::check_holiday($date));
    return $date;
}

?>
