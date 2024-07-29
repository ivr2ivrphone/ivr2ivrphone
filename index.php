<?php
set_time_limit(1000);
$hangup=$_REQUEST['hangup'];
$did=$_REQUEST['did'];
$pass=$_REQUEST['pass'];
$path=$_REQUEST['path'];
$did1=$_REQUEST['did1'];
$pass1=$_REQUEST['pass1'];
$path1=$_REQUEST['path1'];
 if($did == null){print "read=t- הקישוא את המערכת הראשונה=did,yes,10,9,15,Digits,yes,";
exit();
}
elseif($pass == null){print "read=t-  הקישוא את הסיסמא הראשונה=pass,yes,,1,24,Digits,";
exit();
} 
 
elseif($path == null){print "read=t-  הקישוא את השלוחה ממנה יעתיק=path,,,1,24,Alpha,,,*/,";
exit();
} 
 if($did1 == null){print "read=t- הקישוא את המערכת אליה ברצונכם להעתיק=did1,yes,10,9,15,Digits,yes,";
exit();
}
elseif($pass1 == null){print "read=t-  הקישוא את הסיסמא =pass1,yes,,1,24,Digits,";
exit();
} 
 
elseif($path1 == null){print "read=t-  הקישוא את השלוחה אליה יעתיק=path1,,,1,24,Alpha,,,*/,";
exit();
} 
if($hangup == null && $did != null && $pass != null && $path != null && $did1 != null && $pass1 != null && $path1 != null){
$log=json_decode(file_get_contents("https://www.call2all.co.il/ym/api/Login?username={$did}&password={$pass}"), true);
$login=json_decode(file_get_contents("https://www.call2all.co.il/ym/api/Login?username={$did1}&password={$pass1}"), true);
$sta = $log['responseStatus'];
$stat = $login['responseStatus'];
if
($stat == "OK" && $sta == "OK"){
$tok=$log['token'];
$token=$login['token'];
$my_arr = array();
$OK = array();
$ERROR = array();
$url=json_decode(file_get_contents("https://www.call2all.co.il/ym/api/GetIVR2Dir?token={$tok}&path=ivr2:/$path"), true);
$files = $url['files'];
foreach ($files as $file){
$fileType=$file['fileType'];
$what=$file['what'];
if("txt"=="txt"){
$u=json_decode(file_get_contents("https://www.call2all.co.il/ym/api/GetTextFile?token={$tok}&what=$what&"), true);
$contents=$u['contents'];
$contents =  str_replace("\n", "%0A", $contents);
$contents =  str_replace(" ", "%20", $contents);
$obj = json_decode(file_get_contents("https://www.call2all.co.il/ym/api/GetIVR2DirStats?token={$token}&path=ivr2:/{$path1}&"),true);
$fi = $obj['maxFile'];
$filess = $fi['name'];
if($filess != null){
$fil = $filess + 1 ;
}
if($fil<9){
    $fil=sprintf("00%u",$fil);
}
elseif($fil<99){
$fil=sprintf("0%u",$fil);
}
$ur=json_decode(file_get_contents("https://www.call2all.co.il/ym/api/UploadTextFile?token={$token}&what=ivr2:{$path1}/{$fil}.txt&contents={$contents}&"), true);
$status = $ur['responseStatus'];
$my_arr[] = $status;
}
}
foreach ($my_arr as $my){
if($my=="OK"){
    $OK[] = $my;
}
if($my=="EXCEPTION" || $my=="ERROR" || $my=="FORBIDDEN"){
 $ERROR[] = $my;
}
}
$m= count($OK);
$y= count($ERROR);

print "id_list_message=t-הפעולה הסתיימה, סך הקבצים שהועתקו .n-{$m}.t-, סך הקבצים שלא הועתקו .n-{$y}";
}else{
    print "id_list_message=t-שגיאה&";
}
}
