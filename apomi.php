<?php
function balcheck($a,$b){
	
	$url="https://mobileairtimeng.com/httpapi/balance.php?userid=$a&pass=$b";
	$data=@file_get_contents($url);
	if ($data === false) {
		$data=0;
	}
	$bal=(float)$data;
	return $bal;
}

function getcarrier($a){
	if($a=='MTN'){
		$nt=15;
	}
	elseif($a=='GLO'){
		$nt=6;
	}
	elseif($a=='AIRTEL'){
		$nt=1;
	}
	elseif($a=='9MOBILE'){
		$nt=2;
	}
	return $nt;
}

function gettvdetail($a,$b,$tv){
	$url="https://mobileairtimeng.com/httpapi/apiinfos.php?userid=$a&pass=$b&tv=$tv";
	$data=@file_get_contents($url);
	if ($data === false) {
		$str='unable to load';
	}
	else{
		$data= filter_var($data, FILTER_SANITIZE_STRING);
	}
	return $data;
}
?>