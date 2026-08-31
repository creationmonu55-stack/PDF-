<?php
declare(strict_types=1);
const DATA_FILE = __DIR__ . '/../data/votiva-data.json';

function default_data(): array {
    return ['campaigns'=>[
      ['id'=>'cricket-dhoni-virat','category'=>'Cricket','question'=>'WHO IS YOUR FAVOURITE?','title'=>'MS Dhoni vs Virat Kohli','subtitle'=>'Great players. Different styles. One choice.','a_name'=>'MS Dhoni','a_wiki'=>'MS_Dhoni','b_name'=>'Virat Kohli','b_wiki'=>'Virat_Kohli','ends_at'=>'2026-12-31T23:59:59+05:30','active'=>1],
      ['id'=>'football-ronaldo-messi','category'=>'Football','question'=>'WHO IS YOUR FAVOURITE?','title'=>'Cristiano Ronaldo vs Lionel Messi','subtitle'=>'Two legends. One choice.','a_name'=>'Cristiano Ronaldo','a_wiki'=>'Cristiano_Ronaldo','b_name'=>'Lionel Messi','b_wiki'=>'Lionel_Messi','ends_at'=>'2026-12-31T23:59:59+05:30','active'=>1],
      ['id'=>'movie-dhurandhar-pathan','category'=>'Movie','question'=>'WHICH ONE WINS?','title'=>'Dhurandhar vs Pathaan','subtitle'=>'Big screen. Bigger debate.','a_name'=>'Dhurandhar','a_wiki'=>'Dhurandhar_(film)','b_name'=>'Pathaan','b_wiki'=>'Pathaan_(film)','ends_at'=>'2026-12-31T23:59:59+05:30','active'=>1],
      ['id'=>'celebrity-srk-ranveer','category'=>'Celebrity','question'=>'WHO IS YOUR FAVOURITE?','title'=>'SRK vs Ranveer Singh','subtitle'=>'Two generations. One spotlight.','a_name'=>'Shah Rukh Khan','a_wiki'=>'Shah_Rukh_Khan','b_name'=>'Ranveer Singh','b_wiki'=>'Ranveer_Singh','ends_at'=>'2026-12-31T23:59:59+05:30','active'=>1],
      ['id'=>'other-khan-ojha','category'=>'Other','question'=>'WHO IS YOUR FAVOURITE?','title'=>'Khan Sir vs Ojha Sir','subtitle'=>'Teachers who inspire millions.','a_name'=>'Khan Sir','a_wiki'=>'Khan_Sir_(teacher)','b_name'=>'Avadh Ojha','b_wiki'=>'Avadh_Ojha','ends_at'=>'2026-12-31T23:59:59+05:30','active'=>1]
    ],'votes'=>[],'opinions'=>[]];
}
function load_data(): array {
    if(!file_exists(DATA_FILE)){
        $d=default_data(); save_data($d); return $d;
    }
    $raw=file_get_contents(DATA_FILE); $d=json_decode($raw ?: '',true);
    if(!is_array($d)||!isset($d['campaigns'],$d['votes'],$d['opinions'])){$d=default_data();save_data($d);}
    return $d;
}
function save_data(array $d): void {
    $dir=dirname(DATA_FILE); if(!is_dir($dir))mkdir($dir,0755,true);
    $tmp=DATA_FILE.'.tmp.'.bin2hex(random_bytes(4));
    file_put_contents($tmp,json_encode($d,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT),LOCK_EX);
    rename($tmp,DATA_FILE);
}
function with_lock(callable $fn) {
    $dir=dirname(DATA_FILE);if(!is_dir($dir))mkdir($dir,0755,true);
    $fp=fopen(DATA_FILE.'.lock','c');
    if(!$fp) throw new RuntimeException('Storage unavailable');
    flock($fp,LOCK_EX);
    try{return $fn();}finally{flock($fp,LOCK_UN);fclose($fp);}
}
function token(): string {
    if(empty($_COOKIE['votiva_voter'])){
        $t=bin2hex(random_bytes(32));
        setcookie('votiva_voter',$t,['expires'=>time()+31536000,'path'=>'/','secure'=>!empty($_SERVER['HTTPS']),'httponly'=>true,'samesite'=>'Lax']);
        $_COOKIE['votiva_voter']=$t;
    }
    return $_COOKIE['votiva_voter'];
}
function json_out(array $data,int $status=200): never {
    http_response_code($status);header('Content-Type: application/json; charset=utf-8');header('Cache-Control:no-store');
    echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);exit;
}
function input_json(): array {$d=json_decode(file_get_contents('php://input') ?: '{}',true);return is_array($d)?$d:[];}
function clean_text(string $s,int $max): string {$s=trim(preg_replace('/\s+/u',' ',$s));return function_exists('mb_substr')?mb_substr($s,0,$max):substr($s,0,$max);}
function find_campaign(array $d,string $id): ?array {foreach($d['campaigns'] as $c)if($c['id']===$id)return $c;return null;}
