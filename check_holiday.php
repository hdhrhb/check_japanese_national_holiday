<?php
//////////////////////////////////////////////
//前営業日を算出
//$date:前営業日を知りたい営業日
/////////////////////////////////////////////
function calc_yesterday($date){
	do{
		$date=strftime('%Y-%m-%d', strtotime('-1 day', strtotime($date)));
	}while(!check_holiday($date));
	return $date;
}

//////////////////////////////////////////////
//翌営業日を算出
//$date:前営業日を知りたい営業日
/////////////////////////////////////////////
function calc_nextday($date){
	do{
		$date=strftime('%Y-%m-%d', strtotime('+1 day', strtotime($date)));
	}while(!check_holiday($date));
	return $date;
}

function calc_preday($date){
    do{
        $date=strftime('%Y-%m-%d', strtotime('-1 day', strtotime($date)));
    }while(!check_holiday($date));
    return $date;
}


//////////////////////////////////////////////
//日本の市場休日判定
//日付の方はyyyy-mm-ddを想定
//$date:休日判定する日付
//true:平日、false:休日
//////////////////////////////////////////////
function check_holiday($date){
	if(check_sunday_saturday($date)==false or check_national_holiday($date)==false or check_hurikae_holiday($date)==false){
		return false;
	}else{
		return true;
	}
}

function check_sunday_saturday($date){
	$datetime = new DateTime($date);
	$w = (int)$datetime->format('w');
	if($w>=1 and $w<=5){
		return true;
	}else{
		return false;
	}
}

function check_national_holiday($date){
	list($year,$month,$day)=explode('-', $date);
	$datetime = new DateTime($date);

	if($month=='01'){
		//正月三が日
		if($day=='01' or $day=='02' or $day=='03'){
			return false;
			//成人の日
		}elseif($day>='08' and $day<='14'){
			$w = (int)$datetime->format('w');
			if($w==1){
				return false;
			}
		}
	}elseif($month=='02'){
		//建国記念日
		if($day=='11'){
			return false;
		}
	}elseif($month=='03'){
		//春分の日
		if($day==floor(20.8431+0.242194*($year-1980))-floor(($year-1980)/4)){
			return false;
		}

	}elseif($month=='04'){
		//昭和の日
		if($day=='29'){
			return false;
		}
	}elseif($month=='05'){
		//憲法記念日、みどりの日、こどもの日
		if($day=='03' or $day=='04' or $day=='05'){
			return false;
		}
		//新天皇即位
		if($year=='2019' and $day=='01'){
		    return false;
		}
	}elseif($month=='07'){
		//海の日
		if($year>='1996' and $year<='2002'){
			if($day=='20'){
				return false;
			}
		}else{
			if($day>='15' and $day<='21'){
				$w = (int)$datetime->format('w');
				if($w==1){
					return false;
				}
			}
		}
	}elseif($month=='08'){
		//山の日
		if($day=='11'){
			//2016年以降なので
			if($year>='2016'){
				return false;
			}
		}
	}elseif($month=='09'){
		//敬老の日
		if($year>='2003'){
			if($day>='15' and $day<='21'){
				$w = (int)$datetime->format('w');
				if($w==1){
					return false;
				}
			}
		}else{
			if($day=='15'){
				return false;
			}
		}
		//秋分の日
		if($day==floor(23.2488+0.242194*($year-1980))-floor(($year-1980)/4)){
			return false;
		}
	}elseif($month=='10'){
		//体育の日
		if($year>='2000'){
			if($day>='08' and $day<='14'){
				$w = (int)$datetime->format('w');
				if($w==1){
					return false;
				}
			}
		}else{
			if($day=='10'){
				return false;
			}
		}

	}elseif($month=='11'){
		//文化の日
		if($day=='03'){
			return false;
			//勤労感謝の日
		}elseif($day=='23'){
			return false;
		}
	}elseif($month=='12'){
		//天皇誕生日
		if($day=='23'){
			if($year>='1989'){
				return false;
			}
			//大晦日
		}elseif($day=='31'){
			return false;
		}
	}
	return true;
}

function check_hurikae_holiday($date){
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
	if($month=='09'){
		$tommorow=strftime('%Y-%m-%d', strtotime('+1 day', strtotime($date)));
		if(check_national_holiday($yesterday)==false and check_national_holiday($tommorow)==false){
			return false;
		}
	}
	return true;
}
?>