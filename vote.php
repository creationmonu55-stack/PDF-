<?php
require __DIR__.'/config.php';
if($_SERVER['REQUEST_METHOD']!=='POST')json_out(['error'=>'POST required'],405);
$x=input_json();$id=clean_text((string)($x['campaign_id']??''),80);$side=(string)($x['side']??'');
if($id===''||!in_array($side,['a','b'],true))json_out(['error'=>'Invalid vote request.'],400);
try{
 $result=with_lock(function()use($id,$side){
   $d=load_data();if(!find_campaign($d,$id))return ['status'=>404,'data'=>['error'=>'Campaign not found.']];
   $v=token();
   foreach($d['votes'] as $row)if($row['campaign_id']===$id&&hash_equals($v,$row['voter_token']))return ['status'=>200,'data'=>['already_voted'=>true,'message'=>'You have already voted in this campaign.']];
   $d['votes'][]=['campaign_id'=>$id,'voter_token'=>$v,'side'=>$side,'created_at'=>gmdate('c')];save_data($d);
   return ['status'=>200,'data'=>['ok'=>true,'message'=>'Vote counted.']];
 });
 json_out($result['data'],$result['status']);
}catch(Throwable $e){json_out(['error'=>'Vote could not be recorded.'],500);}
