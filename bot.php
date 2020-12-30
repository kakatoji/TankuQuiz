<?php

error_reporting(0);
class Tanku{
  public $server,
         $head,
         $api,
         $cfg;
         
  public function __construct(){
    $this->server="https://quiz.vabetu.com";
    $this->head  =[
           "Content-Type"=> "application/x-www-form-urlencoded; charset=UTF-8",
           "User-Agent"=> "Dalvik/2.1.0 (Linux; U; Android 7.0; Redmi Note 4 MIUI/V11.0.2.0.NCFMIXM)",
           "Host"=> "quiz.vabetu.com"];
    $this->cfg   ="config.json";
    $this->api   =[
       "getdata"    =>  "/api/players/getplayerdata",
       "login"      =>  "/api/players/login",
       "wd"         =>  "/api/withdrawals/request/new"
      ];
  }
  
  private function head(){
    foreach($this->head as $head=>$body){
      $uas[]=$head.": ".$body;
    }
    return $uas;
  }
  private function save($data_mob){
    $file=$this->cfg;
    if(!file_get_contents($file)){
      file_put_contents($file,"[]");}
    $as=json_decode(file_get_contents($file),1);
    $ar=array_merge($as,$data_mob);
    file_put_contents($file,json_encode($ar,JSON_PRETTY_PRINT));
  }
  public function tmp(){
    echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
  }
  
  private function curl($url, $post = 0, $httpheader = 0, $proxy = 0){ // url, postdata, http headers, proxy, uagent
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        if($post){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        if($httpheader){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        }
        if($proxy){
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
            // curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        }
        curl_setopt($ch, CURLOPT_HEADER, true);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch);
        if(!$httpcode) return "Curl Error : ".curl_error($ch); else{
            $header = substr($response, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
            $body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
            curl_close($ch);
            return array($header, $body);
        }
    }
  public function timer($tmr){ 
           $timr=time()+$tmr; 
           while(true): 
           echo "\r                       \r"; 
           $res=$timr-time(); 
           if($res < 1){break;} 
           echo date('H:i:s',$res); 
           sleep(1); 
           endwhile;
         }
  public function login($email,$pass){
    $url=$this->server.$this->api["login"];
    $data="email=$email&password=$pass&";
    return $this->curl($url,$data,$this->head());
  }
  public function exse(){
    $akun=json_decode(file_get_contents($this->cfg),1);
    $cr=shell_exec("clear");
    back:
    echo $cr;
    $this->tmp();
    echo "[>] Total akun anda (".count($akun).")\n";
    echo "\t~[1]Input akun~\n";
    echo "\t~[2]cek profile~\n";
    $this->tmp();
    $menu=readline("[>]Input: ");
    $this->tmp();
    switch($menu){
      case 1:
        echo $this->nologin();
        goto back;
        $data=json_decode(file_get_contents($this->cfg),1);
        
        break;
      case 2:
        $data=json_decode(file_get_contents($this->cfg),1);
        while(true){
        foreach($data as $head=>$body){
          $akun=$this->data($body["email"]);
          $pro=json_decode($akun[1]);
          foreach($pro as $akun=>$req){
            echo "~ID    ".$req->id."\n";
            echo "~NAMA  ".$req->name."\n";
            echo "~EMAIL ".$req->email."\n";
            echo "~SCORE ".$req->score."\n";
            $this->tmp();
            $x=25*15;
            $update=$this->update($req->id,$x);
            if($req->score > 50000){
              echo "=========================[Witdhraw]=====================\n";
              $no=json_decode(file_get_contents($this->cfg),1);
              $wd=$this->witdh($req->score,$no["ovo"],$req->id);
              $wdh=json_decode($wd[1],1);
              if($wdh["message"] == "success"){
                echo "[>]withdrawals to => ".$no."\n";
              }
            }
           }
          }
          $this->timer(3);
        }
        break;
    }
    
  }
  private function nologin(){
    $user=readline("[>]Email: ");
    $pass=readline("[>]Password: ");
    $wd=readline("[>]Input ovo number: ");
    $data=["user-".rand(0,9)=>["email"=>$user,"password"=>$pass,"ovo"=>$wd]];
    $this->save($data);
  }
  public function update($id,$poin){
    $url=$this->server."/api/players/$id/update";
    $data="points=$poin";
    return $this->curl($url,$data,$this->head());
  }
  public function data($email){
    $url=$this->server.$this->api['getdata'];
    $data="email=$email&";
    return $this->curl($url,$data,$this->head());
  }
  public function witdh($amo,$no,$id){
    $url=$this->server.$this->api["wd"];
    $data="amount=$amo.00&account=$no&method=OVO&player_id=$id&points=$amo&";
    return $this->curl($url,$data,$this->head());
  }
  
}
$as=new Tanku();
$as->exse();
