<?php
require __DIR__.'/config.php';
$d=load_data();$cat=isset($_GET['category'])?clean_text((string)$_GET['category'],30):'';$v=token();$out=[];
foreach($d['campaigns'] as $c){
 if(!$c['active']||($cat!==''&&strcasecmp($cat,$c['category'])!==0))continue;
 $a=0;$b=0;$opin=0;$my=null;
 foreach($d['votes'] as $x)if($x['campaign_id']===$c['id']){if($x['side']==='a')$a++;else $b++;if(hash_equals($v,$x['voter_token']))$my=$x['side'];}
 foreach($d['opinions'] as $x)if($x['campaign_id']===$c['id'])$opin++;
 $c['a_votes']=$a;$c['b_votes']=$b;$c['opinions']=$opin;$c['my_vote']=$my;$out[]=$c;
}
json_out(['campaigns'=>$out]);
