<?php
require __DIR__.'/config.php';
$d=load_data();
if($_SERVER['REQUEST_METHOD']==='GET'){
 $id=clean_text((string)($_GET['campaign_id']??''),80);$out=[];
 foreach(array_reverse($d['opinions']) as $o)if($o['campaign_id']===$id){$out[]=$o;if(count($out)>=100)break;}
 json_out(['opinions'=>$out]);
}
if($_SERVER['REQUEST_METHOD']==='POST'){
 $x=input_json();$id=clean_text((string)($x['campaign_id']??''),80);$text=clean_text((string)($x['text']??''),500);
 if(!$id||strlen($text)<2)json_out(['error'=>'Please write a valid opinion.'],400);
 if(!find_campaign($d,$id))json_out(['error'=>'Campaign not found.'],404);
 $d['opinions'][]=['id'=>bin2hex(random_bytes(8)),'campaign_id'=>$id,'voter_token'=>token(),'text'=>$text,'created_at'=>gmdate('c')];save_data($d);
 json_out(['ok'=>true],201);
}
json_out(['error'=>'Method not allowed'],405);
