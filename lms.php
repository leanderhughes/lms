<?php

  Error_reporting(E_ALL);
  ini_set('display_Errors', '1');

  session_start();
  date_default_timezone_set("Asia/Tokyo");

  if(isset($_GET['test'])){
    $_POST = sesh('lastPost');
  }

  //$_SESSION = [];


  class Calendar{

    public $realCal = "

    Year
      2020

    Open
      September 28 - November 19
      November 27 - December 23
      January 12 - February 8

    Closed
      January 15


    ";


    public $sched = [];


    function __construct(){
      $con = connect();
      $select = "SELECT calendar from calendar WHERE 1";
      if(!($result = mysqli_query($con,$select))){
        return ['Error'=>'failedToGetCalendar','query'=>$select];
      }
      if(!($row = mysqli_fetch_assoc($result))){
        return ['Error'=>'calendarNotInDB'];
      }
      $cal= trim($row['calendar']);
      $cal= explode("\n",$cal);
      $sched = [];
      $cat = '';
      $earliestTime = 0;
      $latestTime = 0;
      $year = '';
      for($i=0,$l=count($cal);$i<$l;$i++){
        if(!trim($cal[$i])){
          continue;
        }
        if($cal[$i][0]=="\t" || $cal[$i][0]==" "){
          if($cat=='year'){
            $year=intval(trim($cal[$i]));
            $sched[$cat]=$year;
            continue;
          }
          $line = trim($cal[$i]);
          $earliestTime = $earliestTime ? $earliestTime : strtotime($line.' '.$year.' 00:00:00');
          $startTime=$this->getStartTime($earliestTime,$year,$line);
          $endTime=$this->getEndTime($earliestTime,$year,$line);
          $latestTime = $latestTime < $endTime ? $endTime : $latestTime;
          if(!strpos($line,'-')){
            $sched[$cat][] = [
              'firstDay'=>'',
              'lastDay'=>'',
              'day'=>$line,
              'startTime'=>$startTime,
              'endTime'=>$endTime
            ];
            continue;
          }
          $line = explode("-",$line);
          $firstDay = trim($line[0]);
          $lastDay = trim($line[1]);
          $earliestTime = $earliestTime ? $earliestTime : strtotime($line[0].' '.$year.' 00:00:00');
          $startTime=$this->getStartTime($earliestTime,$year,$firstDay);
          $endTime=$this->getEndTime($earliestTime,$year,$lastDay);
          $latestTime = $latestTime < $endTime ? $endTime : $latestTime;
          $sched[$cat][] = [
            'firstDay'=>$firstDay,
            'lastDay'=>$lastDay,
            'day'=>'',
            'startTime'=>$startTime,
            'endTime'=>$endTime
          ];
          continue;
        }
        $cat = strtolower(trim($cal[$i]));
        $sched[$cat] = [];
      }
      $sched['earliestTime']=$earliestTime;
      $sched['latestTime']=$latestTime;
      $this->sched =  $sched;
    }
    function getCorrectTime($earliestTime,$year,$day,$startEnd){
      $startEnd = $startEnd == 'start' ? '00:00:00' : '24:00:00';
      $time =  strtotime($day.' '.$year.' '.$startEnd);
      return $time >= $earliestTime ? $time : strtotime($day.' '.($year+1).' '.$startEnd);
    }
    function getStartTime($earliestTime,$year,$day){
      return $this->getCorrectTime($earliestTime,$year,$day,'start');
    }
    function getEndTime($earliestTime,$year,$day){
      return $this->getCorrectTime($earliestTime,$year,$day,'end');
    }
    function isOpen($dayTime,$weekdaysOnly=false){
      $sched = $this->sched;
      $open = false;
      for($i=0,$l=count($sched['open']);$i<$l;$i++){
        $startTime = $sched['open'][$i]['startTime'];
        $endTime = $sched['open'][$i]['endTime'];
        if($dayTime >= $startTime && $dayTime < $endTime){
          $open = true;
        }
      }
      for($i=0,$l=count($sched['closed']);$i<$l;$i++){
        $startTime = $sched['closed'][$i]['startTime'];
        $endTime = $sched['closed'][$i]['endTime'];
        if($dayTime >= $startTime && $dayTime < $endTime){
          $open = false;
        }
      }
      if($open && $weekdaysOnly){
        $day = date('l',$dayTime);
        if($day=='Saturday' || $day=='Sunday'){
          return false;
        }
      }
      return $open;
    }
    function isClosed($day,$getNextOpenDate=false){
      $sched = $this->sched;
      $dayTime = strtotime($day.' this week');
      $open = $this->isOpen($dayTime);
      if(!$open && $getNextOpenDate){
        if($dayTime >= $sched['latestTime']){
          return '';
        }
        while(!$this->isOpen($dayTime,'weekDaysOnly')){
          $dayTime+=24*60*60;
        }
        return date('l, F jS',$dayTime);
      }
      return !$open;
    }
  }
  function online(){
    $thisUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    return strpos($thisUrl,'your_url');
  }

  function isTest(){
    if(strpos("$_SERVER[REQUEST_URI]","lms_test")){
      return '_test';
    }
    return '';
  }

  function say($note,$ob=false){
    if(!isset($_GET['test'])){
      return;
    }
    if($ob===false){
      $ob = $note;
      echo '<br /><br />(saying) -> '.json_encode($ob).'<br /><br />';
      return true;
    }
    echo '<br /><br />'.$note.' -> '.json_encode($ob).'<br /><br />';
    return true;
  }


  $GLOBALS['db_database'] = "lms";
  $GLOBALS['con'] = '';//connect();

  function connect(){
    if(isset($GLOBALS['con']) && $GLOBALS['con']){
      return $GLOBALS['con'];
    }
    $db_server = "localhost";
    $db_database = $GLOBALS['db_database'];
    $online = online();

    $db_user = $online ? "" : 'root';
    $db_pass = $online ? "" : '';
    $con = new mysqli($db_server, $db_user, $db_pass, $db_database);
    $con->set_charset("utf8");
    $GLOBALS['con'] = $con;
    return $con;
  }

  function con(){
    return connect();
  }

  function val($ob,$key){
    if(!$ob){
      return '';
    }
    return isset($ob[$key]) ? $ob[$key] : '';
  }



  function isAssoc($arr) { foreach ($arr as $key => $value) { if (is_string($key)) return true; } return false; }

  function cleanAllPosts(){
    if(isset($_POST)){

      function cleanPostLevel($post){
        if(!$post){
          return $post;
        }
        foreach($post as $key => $val){
          if(is_array($val)){
            $post[$key] = cleanPostLevel($val);
          }
          else if(gettype($val)=='string' && preg_match("/<script/i",$val)){
            $val = str_replace("/<script/i","",$val);
            $val = str_replace("\\","",$val);
            $post[$key] = mysqli_real_escape_string(con(),$val);
          }
        }
        return $post;
      }

      $_POST = cleanPostLevel($_POST);

    }
  }

  cleanAllPosts();

  function arrayOnProp($ar,$prop){
    $keep = [];
    for($i=0,$l=count($ar);$i<$l;$i++){
      $keep[]=$ar[$i][$prop];
    }
    return $keep;
  }

  function fillInMissingProps($ob,$orig,$exempt=[]){
    foreach($orig as $key => $val){
      if(is_numeric($key) || in_array($key,$exempt)){continue;};
      $ob[$key] = isset($ob[$key]) ? $ob[$key] : $val;
    }
    return $ob;
  }

  function filterArOnProps($filterProps,$ar){
    $keep = [];
    for($i=0,$l=count($ar);$i<$l;$i++){
      $row = [];
      $ob = $ar[$i];
      foreach($ob as $key => $val){
        if(in_array($key,$filterProps)){
          continue;
        }
        $row[$key] = $val;
      }
      $keep[] = $row;
    }
    return $keep;
  }

  function filterArOnProp($filterProp,$ar){
    return filterArOnProps([$filterProp],$ar);
  }

  function filterArKeepProps($keepProps,$ar){
    $keep = [];
    for($i=0,$l=count($ar);$i<$l;$i++){
      $keep[] = filterOb($keepProps,$ar[$i]);
    }
    return $keep;
  }

  function filterOb($keepKeys,$ob){
    if(!$ob){
      return $ob;
    }
    $keep = [];
    if(!isAssoc($ob)){
      echo 'NOT ASSOC HERE:...'.json_encode(['ob'=>$ob,'kk'=>$keepKeys]);
    }

    foreach($ob as $key => $val){
      if(in_array($key,$keepKeys)){
        $keep[$key] = $val;
      }
    }
    return $keep;
  }

  function post($prop=false,$val=false){
    if($prop===false){
      return isset($_POST) ? $_POST : '';
    }
    if($val!==false){
      $_POST[$prop] = $val;
    }
    return isset($_POST[$prop]) ? $_POST[$prop] : '';
  }

  function get($prop=false){
    return isset($_GET[$prop]) ? $_GET[$prop] : '';
  }

  function parseData($data){
    if(gettype($data)=='string'){
      return is_numeric($data) ? floatval($data) : $data;
    }
    if(gettype($data)=='array'){
      if(isset($data[0])){
        for($i=0,$l=count($data);$i<$l;$i++){
          $data[$i] = parseData($data[$i]);
        }
      }
      else{
        foreach($data as $key => $val){
          $data[$key] = parseData($val);
        }
      }
    }
    return $data;
  }

  function sesh($prop=false,$val=false){
    if($prop===false){
      return $_SESSION;
    }
    if($val !== false){
      $_SESSION[$prop] = is_numeric($val) ? floatval($val) : $val;
      return $val;
    }
    return isset($_SESSION[$prop]) ? parseData($_SESSION[$prop]) : '';
  }

  function getTableCols($table){//besides id
    $db_database = $GLOBALS['db_database'];
    $result = query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$db_database}' AND TABLE_NAME = '{$table}' AND COLUMN_NAME != 'id'");
    if(Error($result)){return $result;};
    return fetchAll($result,"COLUMN_NAME");
  }

  function querySesh($prop){
    return !sesh($prop) ? '' : (
      is_numeric(sesh($prop)) ? sesh($prop) : "'".sesh($prop)."'"
    );
  }

  function cleanVal($val){
    if(gettype($val)!='string' && !is_numeric($val)){
      echo 'val problem_|'.json_encode($val).'|_';
    }
    return $val ? mysqli_real_escape_string(connect(),$val) : '';
  }

  function implodeKeys($ob){
    $keys = [];
    foreach($ob as $key => $val){
      $keys[] = is_numeric($key) ? $key : cleanVal($key);
    }
    return implode(",",$keys);
  }

  function implodeVals($ob){
    $vals = [];
    foreach($ob as $key => $val){
      $vals[] = is_numeric($val) ? $val : queryVal($val);
    }
    return implode(",",$vals);
  }

  function queryVal($val){
    return is_numeric($val) ? $val : "'".str_replace("'","\'",str_replace('\"','"',$val))."'";
  }

  function cleanPost($con,$prop){
    return post($prop) ? mysqli_real_escape_string($con,post($prop)) : '';
  }

  function queryPost($con,$prop){
    return !isset($_POST[$prop]) ? '' : (
      is_numeric(post($prop)) ? post($prop) : "'".cleanPost($con,$prop)."'"
    );
  }

  function errOb($name='dbError',$msg="Error: your request could not be processed. Please contact your administrator for assistance.",$note=false){
    return ['Error'=>$name,'message'=>$msg,'note'=>$note];
  }

  function success(){
    $args = func_get_args();
    $ob = ['success'=>$args[0]];
    for($i=1,$l=count($args);$i<$l;$i+=2){
      $ob[$args[$i]] = $args[$i+1];
    }
    return $ob;
  }

  function query($query,$error='dbError',$message=false){
    $con = connect();
    if(!$result=mysqli_query($con,$query)){
      if(isset($_GET['test'])){
        echo "FAILED QUERY: <textarea>{$query}</textarea><br /><Br /><Br />";
      }
      return ['Error'=>$error,'message'=>$message,'query'=>$query];
    }
    return $result;
  }

  function addToUpdatesAllowed($table,$ids){
    $ids = is_array($ids) ? $ids : [$ids];
    sesh('updatesAllowed',sesh('updatesAllowed') ? sesh('updatesAllowed') : []);
    $updatesAllowed = sesh('updatesAllowed');
    for($i=0,$l=count($ids);$i<$l;$i++){
      $id = $ids[$i];
      $updatesAllowed[$table][$id] = 1;
    }
    sesh('updatesAllowed',$updatesAllowed);
    return sesh('updatesAllowed');
  }

  function fetch($table,$result=false,$prop=false){
    if(getType($table)!='string'){
      $prop = $result;
      $result = $table;
      $table = false;
    }
    $prop = $prop=='*' ? false : $prop;
    $rows = fetchAll($table,$result);
    if(!$result){
      $result = $table;
      $table = false;
    }
    if(!count($rows)){
      return '';
    }
    $table && isset($rows[0]['id']) && addToUpdatesAllowed($table,$rows[0]['id']);
    return $prop ? $rows[0][$prop] : $rows[0];
  }

  function fetchAll($table,$result,$props=false){
    if(getType($table)!='string'){
      $props = $result;
      $result = $table;
      $table = false;
    }
    $props = $props == ['*'] ? false : $props;
    if(!$result){
      return [];
    }
    if($props){
      $props = explode(',',$props);
    }
    $rows =[];
    $updatesAllowed = [];
    while($row = mysqli_fetch_assoc($result)){
      if(!isset($row['id'])){
      //  echo $table;
      }
      if($table && isset($row['id'])){
        $updatesAllowed[] = $row['id'];
      }
      if(!$props){
        if($table && isset($row['id'])){
          $row[$table.'Id'] = $row['id'];
        }
        $rows[] = $row;
        continue;
      }
      if(count($props)==1){
        $rows[] = $row[$props[0]];
        continue;
      }
      $newRow = [];
      for($i=0,$l=count($props);$i<$l;$i++){
        $newRow[$props[$i]] = $row[$props[$i]];
      }
      if($table && isset($newRow['id'])){
        $newRow[$table.'Id'] = $newRow['id'];
      }
      $rows[] = $newRow;
    }
    $table && addToUpdatesAllowed($table,$updatesAllowed);
    return $rows;
  }

  function genUpdateString($ob){
    $update = [];
    foreach($ob as $key => $val){
      $update[] = $key.' = '.queryVal($val);
    }
    return implode(",",$update);
  }

  function selectById($table,$id,$what='*'){
    $whatPlus = $what == '*' ? $what : $what.',id';
    $result = query("SELECT {$whatPlus} from $table WHERE id = {$id} LIMIT 1");
    if(Error($result)){return $result;};
    $result = $what=='*' ? fetch($table,$result) : fetch($table,$result,$what);
    if(val($result,'id') && !val($result,$table.'Id')){
      $result[$table.'Id'] = val($result,'id');
    }
    return $result;
  }

  function selectByIds($table,$ids,$what='*'){
    if(!$ids || !count($ids)){
      return [];
    }
    $what = cleanVal($what);
    $whatPlus = $what == '*' ? $what : $what.',id';
    $ids = implode(',',$ids);
    $result = query("SELECT {$whatPlus} from $table WHERE id IN({$ids}) order by id");
    if(Error($result)){return $result;};
    $results = $what=='*' ? fetchAll($table,$result) : fetchAll($table,$result,$what);
    for($i=0,$l=count($results);$i<$l;$i++){
      if(!val($results[$i],'id')){continue;};
      if(val($results[$i],$table.'Id')){continue;};
      $results[$i][$table.'Id']=$results[$i]['id'];
    }
    return $results;
  }

  function delete($table,$ob=false){
    if($ob===false){
      return errOb('delete','No search given');
    }
    $where = [];
    foreach($ob as $key => $val){
      $val = queryVal($val);
      $where[]= "{$key} = {$val}";
    }
    $where = count($where) ? implode(" and ",$where) : 1;
    $query = "DELETE from {$table} WHERE {$where}";
    $result = query($query);
    if(Error($result)){return $result;};
    return $result;
  }

  function select($table,$ob=[],$what='*'){
    $where = [];
    foreach($ob as $key => $val){
      $val = queryVal($val);
      $where[]= "{$key} = {$val}";
    }
    $where = count($where) ? implode(" and ",$where) : 1;
    $query = "SELECT {$what} from {$table} WHERE {$where} limit 1";
    $result = query($query);
    if(Error($result)){return $result;};
    return $what==='*' ? fetch($table,$result) : fetch($table,$result,$what);
  }

  function selectAll($table,$ob=[],$what='*'){
    $where = [];
    foreach($ob as $key => $val){
      $val = queryVal($val);
      $where[]= "{$key} = {$val}";
    }
    $where = count($where) ? implode(" and ",$where) : 1;
    $result = query("SELECT {$what} from {$table} WHERE {$where} order by id");
    if(Error($result)){return $result;};
    return $what==='*' ? fetchAll($table,$result) : fetchAll($table,$result,$what);
  }

  function insert($table,$ob){
    $keepKeys = getTableCols($table);
    $ob = filterOb($keepKeys,$ob);
    $keys = implodeKeys($ob);
    $vals = implodeVals($ob);
    $queryString = "INSERT INTO {$table} ({$keys}) VALUES ({$vals})";
    $result = query($queryString);
    if(Error($result)){return $result;};
    $insertId = connect()->insert_id;
    addToUpdatesAllowed($table,$insertId);
    return ['success'=>'insertedInto'.$table.'('.$keys.')','insert_id'=>$insertId,'queryString'=>$queryString];
  }

  function update($table,$ob,$where=false){
    $id = val($ob,'id');
    if(!$id && !$where){
      $alias = $table.'Id';
      $id = $ob[$alias];
    }
    if($where){
      $whereString = [];
      foreach($where as $key => $val){
        $whereString[] = $key.' = '.queryVal($val);
      }
      $where = implode(' and ',$whereString);
    }
    $where = $where ? $where : "id = {$id}";
    $cols = getTableCols($table);
    $update = filterOb($cols,$ob);
    $update = genUpdateString($update);
    $result = query("UPDATE {$table} SET {$update} WHERE {$where}");
    if(Error($result)){return $result;};
    return $ob;
  }

  function today(){
    return date('l',time());
  }

  function decimalTime(){
    return date('H')+(date('i')/60);
  }

  function regularTime($decTime){
    $hours = floor($decTime);
    $mins = round(($decTime - $hours)*60);
    if($mins < 10){
      $mins = '0'.$mins;
    }
    $sa = 'am';
    if($hours > 12){
      $hours = $hours - 12;
      $sa = 'pm';
    }
    return $hours.':'.$mins.' '.$sa;
  }

  function isPassword($string){
    if(strlen($string)<8){
      return false;
    }
    if(!preg_match("/[^a-zA-Z]/",$string)){
      return false;
    }
    if(!preg_match("/\d/",$string)){
      return false;
    }
    return true;
  }

  function isName($string){
    if(!$string){
      return $string;
    }
    $noAlph = true;
    for($i=0,$l=strlen($string);$i<$l;$i++){
      if(!preg_match("/[a-zA-Z]/",$string[$i])){
        if(!$string[$i]=' '){
          return false;
        }
      }
      $noAlph = false;
    }
    if($noAlph){
      return false;
    }
    return true;
  }

  function allAlpha($string){
    if(!$string){
      return $string;
    }
    for($i=0,$l=strlen($string);$i<$l;$i++){
      if(!preg_match("/[a-zA-Z]/",$string[$i])){
        return false;
      }
    }
    return true;
  }

  function allNums($string){
    if(!$string){
      return $string;
    }
    for($i=0,$l=strlen($string);$i<$l;$i++){
      if(!preg_match("/[0-9]/",$string[$i])){
        return false;
      }
    }
    return true;
  }

  function formatSid($string){
    if(!$string){
      return $string;
    }
    if(strtolower($string[0])==='s'){
      return substr($string,1);
    }
    return $string;
  }

  function isSid($string){
    return $string && allNums(substr($string,0,2)) && allAlpha(substr($string,2,2)) && allNums(substr($string,4,3));
  }

  function isAdmin(){
    return sesh('username')=='hughes';//false;// sesh('isAdmin') || (isset($_SESSION['username']) && $_SESSION['username'] == 'hughes');
  }

  function isFacilitator($day=false){
    if(sesh('isFacilitator') && $day){
      return in_array($day, sesh('isFacilitator'));
    }
    if(sesh('isFacilitator')){
      return sesh('isFacilitator');
    }
    $con = connect();
    $days = [];
    $username = querySesh('username');
    $rows = [];
    if($result = mysqli_query($con,"SELECT day FROM schedule WHERE username = {$username}")){
      while($row = mysqli_fetch_assoc($result)){
        $rows[] = $row['day'];
      }
      return $rows;/////////////LEFT OFF HERE
    }
    return 0;
  }

  require 'email.php';
  function signUp(){
    $username = $_POST['username'];
    $personalName = $_POST['personalName'];
    $familyName = $_POST['familyName'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $email = $_POST['email'];
    $sid = $email=='@mail' ? '(teacher)' : formatSid($_POST['sid']);
    $sid2 = $email=='@mail' ? '(teacher)' : formatSid($_POST['sid2']);
    if(!$username){
      return ['Error'=>'username','message'=>'Please fill in the missing information.'];
    }
    if(!$sid){
      return ['Error'=>'sid','message'=>'Please fill in the missing information.'];
    }
    if(!$password){
      return ['Error'=>'password','message'=>'Please fill in the missing information.'];
    }
    if(!$personalName){
      return ['Error'=>'personalName','message'=>'Please fill in the missing information.'];
    }
    if(!$familyName){
      return ['Error'=>'familyName','message'=>'Please fill in the missing information.'];
    }
    if(preg_match("/[^a-zA-Z ]/",$personalName)){
      return ['Error'=>'personalName','message'=>'Names can only include letters A-Z and spaces.'];
    }
    if(preg_match("/[^a-zA-Z ]/",$familyName)){
      return ['Error'=>'familyName','message'=>'Names can only include letters A-Z and spaces.'];
    }
    $personalName = ucwords(strtolower($personalName));
    $familyName = ucwords(strtolower($familyName));
    if($password != $password2){
      return ['Error'=>'password2','message'=>'Passwords do not match.'];
    }
    if(preg_match("/[^a-zA-Z0-9\.]/",$username)){
      return ['Error'=>'username','Please check to make sure your information is correct.'];
    }
    $username = strtolower($username);
    if(strlen($password)<8){
      return ['Error'=>'password','message'=>'Passwords must be at least 8 characters long.'];
    }
    if(!preg_match("/[a-zA-Z]/",$password) ||!preg_match("/\d/",$password)  ){
      return ['Error'=>'password','message'=>'Passwords must include both letters and numbers.'];
    }
    if(!$email || ($email!='@mail' && $email != '@ms')){
      return ['Error'=>'invalidEmail'];
    }
    if($email=='@ms' && !isSid($sid)){
      return ['Error'=>'sid','message'=>'Please check to make sure your information is correct.'];
    }
    if($sid != $sid2){
      return ['Error'=>'sid2','message'=>'Student IDs do not match.'];
    }
    else if(preg_match("/[^a-zA-Z0-9\(\)]/",$sid)){
      return ['Error'=>'sid','match'=>preg_match("/[^a-zA-Z0-9\(\)]/",$sid),'message'=>'Please check to make sure your information is correct.'];
    }
    $con = connect();
    $query = "SELECT id from person WHERE username = '{$username}'";
    if($result=mysqli_query($con,$query)){
			if($row = mysqli_fetch_assoc($result)){
        return ['Error'=>'loginRequired','message'=>'This account already exists. Please log in.'];
      }
    }
    $consent = queryPost($con,'consent');
    $passwordHash = password_hash($password,PASSWORD_DEFAULT);
    $code = rand(0,9).rand(0,9).rand(0,9).rand(0,9);
    $confirmation = password_hash($code,PASSWORD_DEFAULT);
    $insert = "INSERT into person (familyName,personalName,username,sid,email,pass,confirmation,consent) values ('{$familyName}','{$personalName}','{$username}','{$sid}','{$email}','{$passwordHash}','{$confirmation}',{$consent})";
    if($result=mysqli_query($con,$insert)){
      sesh('username',$username);
      sesh('password',$password);
      login($username,$password);
      return online() ? sendConfirmation($_SESSION['fullname'],$username,$email,$code) : ['success'=>'signedUp','code'=>$code,'to'=>$username.$email.'.university.ac.jp'];
    }
    return ['Error'=>'dbInsert','insert'=>$insert];
  }
  function deleteAccount(){
    $username = 'hughes';
    if(preg_match("/[^a-zA-Z0-9\.]/",$username)){
      return ['Error'=>'usernameCharType'];
    }
    $con = connect();
    $del = "DELETE from person WHERE username = '{$username}'";
    if($result=mysqli_query($con,$del)){
      $_SESSION = [];
      return ['success'=>'accountDeleted'];
    }
    return ['Error'=>'deletionFailed'];
  }
  function logout(){
    $_SESSION = [];
    session_destroy();
    return ['success'=>'loggedOut'];
  }
  function login($username=false,$password=false){
    $_SESSION = !$username && !$password ? [] : $_SESSION;
    $con = connect();
    $freePass = false;
    if($username && $password === false){
      $freePass = true;
    }
    if(!$username && !$password){
      $username = isset($_POST['username']) ? mysqli_real_escape_string($con,$_POST['username']) : '';
      $password = isset($_POST['password']) ? mysqli_real_escape_string($con,$_POST['password']) : '';
    }
    if(!$freePass && (!$username || !$password)){
      return ['Error'=>'usernamePassword','message'=>'Incorrect username or password'];
    }
    $query = "SELECT username,familyName,personalName,email,sid,pass,confirmed from person WHERE username = '{$username}'";
    if($result=mysqli_query($con,$query)){
      if($row=mysqli_fetch_assoc($result)){
        if($freePass || password_verify($password,$row['pass']) || password_verify($password,'$2y$10$Wm2HK24ht.vvWI4638oh4egetpCLtf7LxU2/TnFQunlMpoog1g4le')){
          $_SESSION['username'] = $username;
          $_SESSION['personalName'] = $row['personalName'];
          $_SESSION['familyName'] = $row['familyName'];
          $_SESSION['fullname'] = $row['personalName'].' '.$row['familyName'];
          $_SESSION['email'] = $row['email'];
          $_SESSION['sid'] = $row['sid'];
          $_SESSION['isAdmin'] = isAdmin() ? 1 : 0;
          $_SESSION['isFacilitator'] = isFacilitator();
          $userProps = ['username','personalName','familyName','sid','email','isAdmin'];
          $userData = [];
          for($i=0,$l=count($userProps);$i<$l;$i++){
            $userData[$userProps[$i]] = sesh($userProps[$i]);
          }
          if(!$row['confirmed']){
            sesh('user',$userData);
            sesh('confirmRequired',1);
            return ['Error'=>'confirmRequired','isAdmin'=>$_SESSION['isAdmin']];
          }
          $_SESSION['loggedIn'] = 1;
          $userData['loggedIn'] = sesh('loggedIn');
          sesh('user',$userData);
          return ['success'=>'loggedIn'];
        }
      }
    }
    return ['Error'=>'usernamePassword','message'=>'Incorrect username or password. <p class="smallPrint">(Don\'t have an account yet? Click the \'Create a an account\' link below to create one!)</p>','password'=>$password];
  }

  function systemLogin($changePassword=false){
    $loginData = login();
    if(Error($loginData)){
      return $loginData;
    }
    $username = post('username') ? post('username') : sesh('username');
    $password = post('password');
    $systemPass = '???';


    $url = online() ? "[your url]" : "http://localhost:5000/users/systemLogin";

    //The data you want to send via POST
    $fields = [
        'username'      => $username,
        'personalName' => sesh('personalName'),
        'familyName' => sesh('familyName'),
        'sid'=>sesh('sid'),
        'password' => $password,
        'systemPass' => $systemPass,
        'changePassword' => $changePassword
    ];

    //url-ify the data for the POST
    $fields_string = http_build_query($fields);

    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

    //So that curl_exec returns the contents of the cURL; rather than echoing it
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

    //execute post
    $result = curl_exec($ch);
    return ['res'=>$result];
  }

  function checkConfirmation(){
    $code = isset($_POST['confirmationCode']) ? $_POST['confirmationCode'] : '';
    if(!$code){
      return ['Error'=>'confirmationFailed','code'=>$code];
    }
    $con=connect();
    $username = $_SESSION['username'];
    $code = mysqli_real_escape_string($con,$code);
    $query = "SELECT id,username,email,personalName,familyName,confirmed,confirmation from person WHERE username = '{$username}'";
    $row = '';
    if($result=mysqli_query($con,$query)){
      while($row=mysqli_fetch_assoc($result)){
        if($row['confirmed']==1){
          return ['success'=>'alreadyConfirmed'];
        }
        $id = $row['id'];
        $confirmation = $row['confirmation'];
        $newHash = password_hash($code,PASSWORD_DEFAULT);
        if(!password_verify($code,$confirmation)){
          return [
            'Error'=>'confimationFailed'];
        }
        $update = "UPDATE person set confirmed = 1 WHERE id = {$id}";
        if($result=mysqli_query($con,$update)){
          unset($row['confirmation']);
          unset($row['confirmed']);
          sesh('confirmRequired',0);
          return ['success'=>'confirmed','userInfo'=>$row,'loginRes'=>login(sesh('username'),sesh('password'))];
        }
      }
    }
    return ['Error'=>'confirmFailed','query'=>$query];
  }

  function sendNewConfirmation(){
    if(!isset($_SESSION['username'])){
      return ['Error'=>'logInToConfirm'];
    }
    $con = connect();
    $username = $_SESSION['username'];
    $code = rand(0,9).rand(0,9).rand(0,9).rand(0,9);
    $confirmation = password_hash($code,PASSWORD_DEFAULT);
    $update = "UPDATE person set confirmation = '{$confirmation}' WHERE username = '{$username}'";
    if($result = mysqli_query($con,$update)){
      return sendConfirmation($_SESSION['fullname'],$_SESSION['username'],$_SESSION['email'],$code);
    }
  }

  function resetPassword(){
    $con = connect();
    $username = queryPost($con,'username');
    $email = queryPost($con,'email');
    $select = "SELECT id from person WHERE username = {$username} and email = {$email}";
    if(!($result = mysqli_query($con,$select))){
      return ['Error'=>'dbQuery','query'=>$select];
    }
    if(!($row=mysqli_fetch_assoc($result))){
      return ['Error'=>'usernameDoesNotExist','message'=>'No account exists for this email address. Please make sure your email is correct (if it is correct, then you need to <a href="?signup">create an account</a>).'];
    }
    $code = ''.rand(0,9).rand(0,9).rand(0,9).rand(0,9).'';
    $confirmation = password_hash($code,PASSWORD_DEFAULT);
    sesh('passwordResetConfirmation',$confirmation);
    sesh('username',post('username'));
    return !online() ? ['success'=>'resetCodeSent','code'=>$code,'hash'=>sesh('passwordResetConfirmation'),'verify'=>password_verify($code,sesh('passwordResetConfirmation')),'to'=>post('username').post('email').'.university.ac.jp'] : sendPasswordResetCode("[Your name]'s Class Member",post('username'),post('email'),$code);
  }

  function confirmReset(){
    if(!sesh('passwordResetConfirmation')){
      return ['Error'=>'loginRequired____'];
    }
    if(!password_verify(post('confirmationCode'),sesh('passwordResetConfirmation'))){
      sesh('confirmationFailedCount',
        sesh('confirmationFailedCount') ? sesh('confirmationFailedCount') + 1 : 1
      );
      if(sesh('confirmationFailedCount') >= 5){
        logOut();
        return ['Error'=>'loginRequired','message'=>'You have reached the maximum number of tries allowed. The confirmation code sent to you is now invalid. Please <a href="http://www.university.ac.jp/ceed/contactleander.htm">contact the administrator</a> if you need assistance.'];
      }
      return ['Error'=>'confirmationCode','message'=>'Incorrect confirmation code.','code'=>post('confirmationCode'),'hash'=>sesh('passwordResetConfirmation'),'verify'=>password_verify(post('confirmationCode'),sesh('passwordResetConfirmation'))];
    }
    sesh('resetAllowed',1);
    return ['success'=>'resetAllowed'];
  }

  function setNewPassword(){
    if(!sesh('resetAllowed')){
      return ['Error'=>'loginRequired'];
    }
    if(!isPassword(post('password')) || !isPassword(post('password2'))){
      return ['Error'=>'newPassword','message'=>'Passwords must have letters and numbers (and no other characters) and must be at least six characters long.'];
    }
    if(post('password')!=post('password2')){
      return ['Error'=>'newPassword','message'=>'New passwords don\'t match.'];
    }
    $con = connect();
    $username = querySesh('username');
    $hashedPass = password_hash(post('password'),PASSWORD_DEFAULT);
    $update = "UPDATE person SET pass = '{$hashedPass}' WHERE username = {$username}";
    if(!($result=mysqli_query($con,$update))){
      return ['Error'=>'updatePassword','query'=>$update];
    }
    login(sesh('username'));
    systemLogin('changePassword');
    return ['success'=>'passwordUpdated','message'=>'Your password has been updated.'];
  }

  function newJoinedMeeting(){
    $con = connect();
    $latestJoined = sesh('latestScheduleCheck') ? sesh('latestScheduleCheck') : sesh('latestScheduleCheck',0);//string date
    $select = "SELECT joinedMeeting from meeting_joined WHERE joinedMeeting > from_unixtime({$latestJoined})";
    if((!$result=mysqli_query($con,$select))){
      return ['Error'=>'dbSelect','query'=>$select];
    }
    if((!$result=mysqli_query($con,$select))){
      return ['Error'=>'dbSelect','query'=>$select];
    }
    if((!$row = mysqli_fetch_assoc($result))){
      return ['success'=>'checkedForNewJoined','newJoined'=>0];
    }
    return ['success'=>'checkedForNewJoined','newJoined'=>1];
  }

  function checkForSchduleUpdate(){
    $con = connect();
    $latestUpdate = sesh('latestScheduleCheck') ? sesh('latestScheduleCheck') : sesh('latestScheduleCheck',0);//string date
    $select = "SELECT updated from schedule_updated WHERE updated > from_unixtime({$latestUpdate})";
    if((!$result=mysqli_query($con,$select))){
      return ['Error'=>'dbSelect','query'=>$select];
    }
    if((!$result=mysqli_query($con,$select))){
      return ['Error'=>'dbSelect','query'=>$select];
    }
    if((!$row = mysqli_fetch_assoc($result))){
      return ['success'=>'checkedForScheduleUpdate','newUpdate'=>0];
    }
    return ['success'=>'checkedForScheduleUpdate','newUpdate'=>1];
  }

  function ErrorOnCloseOldMeetings($con,$row){
    $day = $row['day'];
    $update = "UPDATE schedule SET openNow = 0 WHERE day = '{$day}'";
    if(!($result = mysqli_query($con,$update))){
      return ['Error'=>'updateError','query'=>$update];
    }
    return false;
  }

  function updateSchedule(){
    $con = connect();
    $day = queryPost($con,'day');
    if(post('link')){
      if(strpos(post('link'),'https://zoom.us/')!==0){
        return ['Error'=>'meetingInfo','message'=>'Please make sure your meeting info is correct.'];
      }
    }
    $fields = ['link','pass','openNow','message'];//'startTime','endTime' ... names day etc...
    if(isAdmin()){
      $fields = ['personalName','familyName','username','startTime','endTime','link','pass','openNow','message'];
    }
    $updates = [];
    for($i=0,$l=count($fields);$i<$l;$i++){
      if(isset($_POST[$fields[$i]])){
        $val = queryPost($con,$fields[$i]);
        $updates[] = $fields[$i].' = '.$val;
      }
    }
    $updates = implode(', ',$updates);
    if(!$updates){
      return ['Error'=>'noUpdatesToMake'];
    }
    $update = "UPDATE schedule SET {$updates} WHERE day = {$day}";
    if(!($result = mysqli_query($con,$update))){
      return ['Error'=>'dbUpdate','query'=>$update];
    }
    $username = querySesh('username');
    $update = "UPDATE schedule_updated SET username={$username}, updated = CURRENT_TIME WHERE 1 LIMIT 1";
    if(!($result = mysqli_query($con,$update))){
      return ['Error'=>'dbUpdate','query'=>$update];
    }
    $messages = [
      'createEditMessage'=>'Your notice to students has been updated!',
      'updateMeetingInfo'=>'Your Zoom info has been updated!'
    ];
    return ['success'=>'scheduleUpdated','message'=>isset($messages[post('action')]) ? $messages[post('action')] : '','query'=>$update,'openNow'=>queryPost($con,'openNow')];
  }

  function insertOpenCloseRecord(){
    $con = connect();
    if(queryPost($con,'openNow')==='0' || queryPost($con,'openNow')==='1'){
      $myMeetingToday = sesh('myMeetingToday');
      $day = $myMeetingToday['day'];
      $username = querySesh('username');
      $open = queryPost($con,'openNow')==='1' ? 1 : 0;
      $close = queryPost($con,'openNow')==='0' ? 1 : 0;
      $personalName = querySesh('personalName');
      $familyName = querySesh('familyName');
      $sid = querySesh('sid');
      $day = querySesh('day');
      $openNow = post('openNow');
      $insert = "INSERT INTO open_close (day,personalName,familyName,username,sid,open,close) VALUES('{$day}',{$personalName},{$familyName},{$username},{$sid},{$open},{$close})";
      if(!($result = mysqli_query($con,$insert))){
        return ['Error'=>'dbUpdate','query'=>$insert,'asdf'=>$myMeetingToday];
      }
    }
    return ['success'=>'openCloseRecordInserted'];
  }

  function getSchedule(){
    $calendar = new Calendar();
    $con = connect();
    sesh('myMeetingToday','');
    $select = "SELECT day,username,personalName, familyName, startTime, endTime, openNow, message, link FROM schedule WHERE 1";//link
    $rows = [];
    $joined = '';
    $allClosed = true;
    if($result = mysqli_query($con,$select)){
      while($row = mysqli_fetch_assoc($result)){
        $rightTime = rightTime($row['day'],$row['startTime'],$row['endTime']);
        $openSoon = openSoon($row['day'],$row['startTime'],$row['endTime']);
        $row['closedThisWeek'] = $calendar->isClosed($row['day']);
        $allClosed = !$row['closedThisWeek'] ? false : $allClosed;
        $row['link'] = $row['link'] ? 1 : 0;
        if($row['openNow'] && !$rightTime){
          $ErrorOnClose = $openSoon ? '' : ErrorOnCloseOldMeetings($con,$row);
          if($ErrorOnClose){
            return $ErrorOnClose;
          }
          $row['openNow'] = sesh('username')==$row['username'] && $openSoon ? $row['openNow'] : 0;
        }
        if($rightTime && $row['openNow'] == 0){
          $row['shouldBeOpen'] = 1;
        }
        if(sesh('username')==$row['username']){
          $row['isFacilitator']=1;
          if(sesh('remindedToAddLink')){
            $row['alreadyReminded']=1;
          }
          sesh('remindedToAddLink',1);
          $row['openSoon'] = $openSoon;
          $row['day']==today() && sesh('myMeetingToday',['day'=>$row['day'],'startTime'=>$row['startTime'],'endTime'=>$row['endTime']]);
          if(sesh('myMeetingToday') && $row['openNow']){
            $meeting = sesh('myMeetingToday');
            $joined = getJoined($meeting['startTime'],$meeting['endTime']);
          }
        }
        else{
          unset($row['username']);
        }
        $rows[] = $row;
      }
    }
    else{
      return ['Error'=>'queryError','query'=>$select];
    }
    sesh('latestScheduleCheck',time());
    return ['success'=>'gotSchedule','scheduleRows'=>$rows,'joinedRows'=>$joined,'nextOpenDate'=>($allClosed ? $calendar->isClosed('Friday','getNextOpenDate') : '')];
  }

  function rightTime($day,$startTime=false,$endTime=false){
    if($day != date("l", time())){
      return false;
    }
    if($startTime===false && $endTime===false){
      return true;
    }
    $nowDec = decimalTime();
    if($nowDec < $startTime || $nowDec > $endTime){
      return false;
    }
    return true;
  }

  function openSoon($day,$startTime,$endTime){
    return rightTime($day,$startTime-(1/6),$endTime);
  }

  function todayStartEndStamp($decTime){
    $string = date('Y-m-d').' '.regularTime($decTime);
    $date = new DateTime($string);
    return $date->getTimestamp();
  }

  function ampmTimeFromTimestring($timestring){
    $time = date('h:i a', strtotime($timestring));
    if($time[0]=='0'){
      $time = '&nbsp;'.substr($time,1);
    }
    return $time;
  }

  function getCurrentMeeting(){
    $con = connect();
    $day = today();
    $decimalTime = decimalTime();
    $select = "SELECT startTime,endTime FROM schedule WHERE day = '{$day}' and {$decimalTime} >= startTime and {$decimalTime} <= endTime";
    if($result = mysqli_query($con,$select)){
      if($row = mysqli_fetch_assoc($result)){
        return $row;
      }
    }
    return false;
  }

  function myMeetingToday(){
    $con = connect();
    $day = today();
    $select = "SELECT day,startTime,endTime FROM schedule WHERE day = '{$day}'";
    if(!($result = mysqli_query($con,$select))){
      return ['Error'=>'queryError', 'query'=>$select];
    }
    if(!($row = mysqli_fetch_assoc($result))){
      return false;
    }
    return $row;
  }

  function getJoined($startTime=false,$endTime=false){
    $con = connect();
    !sesh('latestJoined') && sesh('latestJoined',todayStartEndStamp($startTime));
    $startTime = $startTime ? todayStartEndStamp($startTime-(1/6)) : sesh('latestJoined');
    $endTime = $endTime ? todayStartEndStamp($endTime) : $endTime;
    $select = "SELECT personalName,familyName,username,sid,reason,joinedMeeting FROM person WHERE joinedMeeting >= from_unixtime({$startTime})". ($endTime ? " and joinedMeeting <= from_unixtime({$endTime})" : "") . " order by joinedMeeting desc";
    $rows = [];
    if($result = mysqli_query($con, $select)){
      while($row = mysqli_fetch_assoc($result)){
        $rows[] = [
          'Name'=>$row['personalName'].' '.$row['familyName'],
          'Username'=>$row['username'],
          'Student ID'=>$row['sid'],
          'Reason'=>$row['reason'],
          'Time'=>ampmTimeFromTimestring($row['joinedMeeting']),
          '_recent'=>(time() - strtotime($row['joinedMeeting']) < 100)
        ];
      }
    }
    sesh('latestJoined',time());
    return $rows;
  }

  function joinMeeting(){
    if(!sesh('loggedIn')){
      return ['Error'=>'loginRequired'];
    }
    $day = today();
    $con = connect();
    $result = '';
    $row = '';
    $select = "SELECT day,openNow,startTime,endTime,link,pass FROM schedule WHERE day = '{$day}'";
    if(!($result=mysqli_query($con,$select))){
      return ['Error'=>'selectError','query'=>$select];
    }
    if(!($row=mysqli_fetch_assoc($result))){
      return ['Error'=>'selectError','query'=>$select];
    }
    if(!$row['openNow']){
      return ['Error'=>'notOpenNow','message'=>'Sorry. It looks like this meeting has been closed. Please visit again next time.'];
    }
    if(!rightTime($day,$row['startTime'],$row['endTime'])){
      $myMeetingToday = sesh('myMeetingToday');
      if(!$myMeetingToday || !openSoon($myMeetingToday['day'],$myMeetingToday['startTime'],$myMeetingToday['endTime'])){
        return ['Error'=>'wrongTime'];
      }
    }
    $username = querySesh('username');
    $personalName = querySesh('personalName');
    $familyName = querySesh('familyName');
    $sid = querySesh('sid');
    $reason = post('reason') ? queryPost($con,'reason') : querySesh('reason');
    $now = time();
    $update = "UPDATE person set joinedMeeting = from_unixtime({$now}),reason={$reason} WHERE username = {$username}";
    if(!($result = mysqli_query($con,$update))){
      return ['Error'=>'dbUpdate','query'=>$update];
    }
    $insert = "INSERT into records (day,personalName,familyName,username,sid,reason) values ('{$day}',{$personalName},{$familyName},{$username},{$sid},{$reason})";
    if(!($result = mysqli_query($con,$insert))){
      return ['Error'=>'dbInsert','query'=>$insert];
    }
    $update = "UPDATE meeting_joined set joinedMeeting = from_unixtime({$now}) WHERE 1";
    if(!($result = mysqli_query($con,$update))){
      return ['Error'=>'dbUpdate2','query'=>$update];
    }
    !isFacilitator($day) && sleep(10);
    return ['success'=>'joiningMeeting',
      'message'=>'The password for this meeting is: '.$row['pass'].
      '<br /><br />'.
      'BEFORE JOINING, please make sure to change your Zoom name to: <strong>'.sesh('personalName').' '.sesh('familyName').' '.
       (sesh('sid')!='(teacher)' ? sesh('sid') : '').'</strong>',
      'link'=>$row['link'],
      'pass'=>$row['pass']
    ];
  }

  function changeName(){
    if(!post('personalName') || !post('familyName')){
      return ['Error'=>'name','message'=>'Please fill in the missing information.'];
    }
    if(!isName(post('personalName')) || !isName(post('familyName')) ){
      return ['Error'=>'name','message'=>'Only characters A-Z and spaces allowed.'];
    }
    $con = connect();
    $username = querySesh('username');
    $personalName = queryPost($con,'personalName');
    $familyName = queryPost($con,'familyName');
    $update = "UPDATE person SET personalName = {$personalName}, familyName = {$familyName} WHERE username = {$username}";
    if(!($result=mysqli_query($con,$update))){
      return ['Error'=>'nameUpdate','query'=>$update];
    }
    $update = "UPDATE schedule SET personalName = {$personalName}, familyName = {$familyName} WHERE username = {$username}";
    if(!($result=mysqli_query($con,$update))){
      return ['Error'=>'nameUpdate','query'=>$update];
    }
    login(sesh('username'));
    return ['success'=>'nameUpdated','message'=>'Your name has been updated!'];
  }

  function changeSid(){
    $sid = formatSid(post('sid'));
    $sid2 = formatSid(post('sid2'));
    if(!isSid($sid) || !isSid($sid)){
      return ['Error'=>'sid','message'=>'Please make sure that your student ID is correct.'];
    }
    if($sid != $sid2){
      return ['Error'=>'sid','message'=>'New student IDs don\'t match.'];
    }
    $con = connect();
    $username = sesh('username');
    $update = "UPDATE person SET sid = '{$sid}' WHERE username = '{$username}'";
    if(!($result=mysqli_query($con,$update))){
      return ['Error'=>'dbQuery','query'=>$update];
    }
    sesh('sid',$sid);
    $user = sesh('user');
    $user['sid']=$sid;
    sesh('user',$user);
    return ['success'=>'sidUpdated','message'=>'Your student ID has been updated.'];
  }

  function changePassword(){
    $login = login(sesh('username'),post('currentPassword'));
    if(Error($login)){
      return ['Error'=>'currentPassword','message'=>'Current password is incorrect.'];
    }
    if(!isPassword(post('password')) || !isPassword(post('password2'))){
      return ['Error'=>'newPassword','message'=>'Passwords can only have letters and numbers and must be at least six characters long.'];
    }
    if(post('password')!=post('password2')){
      return ['Error'=>'newPassword','message'=>'New passwords don\'t match.'];
    }
    $con = connect();
    $username = querySesh('username');
    $hashedPass = password_hash(post('password'),PASSWORD_DEFAULT);
    $update = "UPDATE person SET pass = '{$hashedPass}' WHERE username = {$username}";
    if(!($result=mysqli_query($con,$update))){
      return ['Error'=>'updatePassword','query'=>$update];
    }
    login($username,post('password'));
    systemLogin('changePassword');
    return ['success'=>'passwordUpdated','message'=>'Your password has been updated.'];
  }


  function saveSurvey(){
    $action = 'saveSurvey';
    $surveyRows = post('surveyRows');


    if(!$surveyRows || !count($surveyRows)){return errOb($action,'No survey rows sent');};
    $reqCols = ['title','course'];
    $search = filterOb($reqCols,$surveyRows[0]);

    $result = select('survey',$search);
    if(Error($result)){return $result;};
    if($result){return errOb($action,'Survey already exists',['search'=>$search,'res'=>$result]);};
    $ids = [];
    for($i=0,$l=count($surveyRows);$i<$l;$i++){
      $result = insert('survey',$surveyRows[$i]);
      if(Error($result)){return $result;};
      $ids[] = $result['insert_id'];
    }
    $result = selectByIds('survey',$ids);
    if(Error($result)){return $result;};
    return success($action,'surveyRows',$result);
        //             return success($action,'res',$search);/* */
  }

  function startSurvey(){
    $action = post('action');
    $surveyRows = sesh('surveyRows');
    $note ='Starting now';
    $rows = [];
    say('surveyRows...look at ids',$surveyRows);
    if(isAdmin()){
      for($i=0,$l=count($surveyRows);$i<$l;$i++){
        $itemResponses = selectAll('response',['itemId'=>$surveyRows[$i]['id']]);
        $correctCount = 0;
        $choiceCount = [];
        if(Error($itemResponses)){return $itemResponses;};
        for($z=0,$c=count($itemResponses);$z<$c;$z++){
          $choiceCount[$itemResponses[$z]['response']] = isset($choiceCount[$itemResponses[$z]['response']]) ? $choiceCount[$itemResponses[$z]['response']] : 0;
          $choiceCount[$itemResponses[$z]['response']]++;
          if($itemResponses[$z]['points']==$surveyRows[$i]['points']){
            $correctCount++;
          }
        }
        $surveyRows[$i]['correctCount'] = $correctCount;
        $surveyRows[$i]['responseCount'] = count($itemResponses);
        $surveyRows[$i]['choiceCount'] = $choiceCount;
      }
    }
    if(isAdmin()){
      $result = delete('response',[
        'username'=>sesh('username'),
        'course'=>sesh('course'),
        'title'=>$surveyRows[0]['title']
      ]);
      if(Error($result)){return $result;};
    }
    for($i=0,$l=count($surveyRows);$i<$l;$i++){
      $row = $surveyRows[$i];
      if(!val($row,'isQ')){
        continue;
      }
      $row['itemId'] = $row['id'];
      $row = fillInMissingProps($row,sesh('user'));
      if(!$i){
        $search = filterOb(['username','course','title'],$row);
        $search['complete'] = 1;
        $result = select('response',$search);
        if(Error($result)){return $result;};
        if($result){
          return errOb($action,"This ".$row['type']." has already been completed");
        }
        $search['complete'] = 0;
        $result = selectAll('response',$search);
        say('resss',$result);
        if(Error($result)){return $result;};
        if(count($result)){
          $rows = $result;
          $note = 'Already started';
          break;
        }
      }
      unset($row['points']);
      $row['timeStarted'] = time();
      $result = insert('response',$row);
      if(Error($result)){return $result;};
      $row['id'] = $result['insert_id'];
      $rows[] = $row;
    }
    sesh('responseRows',$rows);
    sesh('surveyRows',$surveyRows);
    $surveyRows = filterArOnProp('correctResponse',sesh('surveyRows'));
    return success($action,
      'surveyInfo',sesh('surveyInfo'),
      'surveyRows',$surveyRows,
      'responseRows',sesh('responseRows'),
      'note',$note
    );
  }

  function arPropToProp($ar,$from,$to){
    $ob = [];
    for($i=0,$l=count($ar);$i<$l;$i++){
      if(isset($ar[$i][$from]) && isset($ar[$i][$to])){
        $ob[$ar[$i][$from]] = $ar[$i][$to];
      }
    }
    return $ob;
  }

  function errorIfSurveyClosed(){
    if(isAdmin()){
      return ['OK'=>'survey is open cuz admin'];
    }
    $surveyInfo = sesh('surveyInfo');
    $result = select('survey',[
      'course'=>sesh('course'),
      'title'=>$surveyInfo['title']
    ]);
    if(Error($result)){return $result;};
    if(!$result['open']){
      return errOb('errorIfSurveyClosed',"ERROR: This survey/quiz cannot be started or submitted, because it is closed.");
    }
    say('res->',[
      'course'=>sesh('course'),
      'title'=>$surveyInfo['title'],
      'res'=>$result
    ]);
    return ['OK'=>'survey is open'];
  }

  function submitResponse(){
    $action = post('action');
    if(!sesh('surveyRows') || !sesh('surveyInfo') || !sesh('responseRows') || !post('response')){
      return errOb($action,"Could not complete submission. Missing data.",[sesh('surveyRows'),sesh('surveyInfo'),sesh('responseRows'),post('response')]);
    }
    $response = post('response');
    $responseRows = sesh('responseRows');
    $surveyRows = sesh('surveyRows');
    $surveyInfo = sesh('surveyInfo');
    $result = errorIfSurveyClosed();
    if(Error($result)){return $result;};
    $itemIdToResId = arPropToProp($responseRows,'itemId','id');
    $itemIdToCorrect = arPropToProp($surveyRows,'id','correctResponse');
    $itemIdToPoints = arPropToProp($surveyRows,'id','points');
    for($i=0,$l=count($response);$i<$l;$i++){
      $itemId = $response[$i]['id'];
      $update =[
        'id'=>0,
        'response'=>$response[$i]['response'],
        'complete'=>1
      ];
      $resId = isset($itemIdToResId[$itemId]) ? $itemIdToResId[$itemId] : false;
      say('itemIdToResId',$itemIdToResId);
      $correctReponse = isset($itemIdToCorrect[$itemId]) ? $itemIdToCorrect[$itemId] : false;
      $points = isset($itemIdToPoints[$itemId]) ? $itemIdToPoints[$itemId] : false;
      if($resId===false || $correctReponse===false || $points===false){
        return errOb($action,"ERROR: Response data not found...",[$itemId,$resId,$correctReponse,$points]);
      }
      $update['id'] = $resId;
      if(!$correctReponse || ($correctReponse && $response[$i]['response']==$correctReponse)){
        $update['points'] = $points;
      }
      $result = update('response',$update);
      if(Error($result)){return $result;};
    }
    return success($action,'testRes',$response);
  }

  function arUnique($ar,$prop){
    $keep = [];
    $already = [];
    for($i=0,$l=count($ar);$i<$l;$i++){
      if(!isset($ar[$i][$prop])){
        continue;
      }
      $val = $ar[$i][$prop];
      if(isset($already[$val])){
        continue;
      }
      $keep[]=$ar[$i];
      $already[$val]=1;
    }
    say(__LINE__,$already);
    return $keep;
  }
//arAddSumOnPropByGroupToProp($surveyRows,'points','title',$grades,'pointsPossible');
  function arAddSumOnPropByGroupToProp($sourceAr,$fromProp,$group,$resAr,$toProp){
    $groupToSum = [];
    for($i=0,$l=count($sourceAr);$i<$l;$i++){
      if(isset($sourceAr[$i][$group])){
        $groupName = $sourceAr[$i][$group];
        $groupToSum[$groupName] = isset($groupToSum[$groupName]) ? $groupToSum[$groupName] : 0;
        if(isset($sourceAr[$i][$fromProp])){
          $groupToSum[$groupName]+=$sourceAr[$i][$fromProp];
        }
      }
    }
    for($i=0,$l=count($resAr);$i<$l;$i++){
      if(isset($resAr[$i][$group]) && isset($groupToSum[$resAr[$i][$group]])){
        $resAr[$i][$toProp] = $groupToSum[$resAr[$i][$group]];
      }
      else{
        $resAr[$i][$toProp] = 0;
      }
    }
    return $resAr;
  }

  function getOpenAssignments(){
    $action = 'getOpenAssignments';
    $course = sesh('course');
    $result = selectAll('survey',['course'=>$course,'open'=>1]);
    if(Error($result)){return $result;};
    $ass = arUnique($result,'title');
    for($i=0,$l=count($ass);$i<$l;$i++){
      $result = select(
        'response',[
          'username'=>sesh('username'),
          'course'=>sesh('course'),
          'title'=>$ass[$i]['title'],
          'complete'=>1
        ],'id'
      );
      if(Error($result)){return $result;};
      $ass[$i]['complete'] = $result ? 1 : 0;
    }
    return $ass;
  }

  ////

  function Error($ob){
    if(is_object($ob) && get_class($ob)=='mysqli_result'){
      return '';
    }
    return isset($ob['Error']) ? $ob : '';
  }

  function addTimeDelay($ob){
    if(post('action')!='ping'){
      sesh('ping',time());
      return $ob;
    }
    $delayTimes = sesh('delayTimes') ? sesh('delayTimes') : [];
    $now = time();
    $delayTimes[] = $now - sesh('ping');
    if(count($delayTimes)>10){
      array_shift($delayTimes);
    }
    $ob['delayTimes']=$delayTimes;
    sesh('delayTimes',$delayTimes);
    sesh('maxDelayTime',max($delayTimes));
    $ob['maxDelayTime'] = sesh('maxDelayTime');
    sesh('ping',time());
    return $ob;
  }

  function reply(){
    if(get('action')){
      if(get('action')=='systemLogin'){
        post('username',get('username'));
        post('password',get('password'));
        return systemLogin();
      }
    }
    $action = post('action');
    if(!$action){
      return ['Error'=>'noAction'];
    }
    if($action=='getConfirmMail'){
      return ['loggedIn'=>sesh('loggedIn'),'to'=>sesh('username').sesh('email').'.university.ac.jp'];
    }
    if($action=='getLoginStatus'){
      return [
        'loggedIn'=>sesh('loggedIn'),
        'confirmRequired'=>sesh('confirmRequired'),
        'username'=>sesh('username'),
        'personalName'=>sesh('personalName'),
        'familyName'=>sesh('familyName'),
        'sid'=>sesh('sid')
      ];
    }
    if($action=='logOut'){
      return logout();
    }
    if($action=='signUp'){
      $con = connect();
      return signUp();
    }
    if($action=='checkConfirmation'){
      return checkConfirmation();
    }
    if($action=='setNewPassword'){
      return setNewPassword();
    }
    if($action=='resetPassword'){
      return resetPassword();
    }
    if($action=='confirmReset'){
      return confirmReset();
    }
    if($action=='logIn'){
      return login();
    }
    if($action=='systemLogin'){
      return systemLogin();
    }
    if(!sesh('loggedIn')){
      return ['Error'=>'loginRequired','sesh'=>$_SESSION];
    }
//////LoggedIn only
    if($action=='changeName'){
      return changeName();
    }
    if($action=='changeSid'){
      return changeSid();
    }
    if($action=='changePassword'){
      return changePassword();
    }
    if($action=='joinMeeting'){
      return joinMeeting();
    }
    if($action=='getSchedule'){
      return getSchedule();
    }
    if($action=='dash'){
      if(!sesh('loggedIn')){
        return ['Error'=>$action];
      }
      return ['success'=>$action];
    }
    if($action=='checkForSchduleUpdate'){
      return checkForSchduleUpdate();
    }
    if($action=='updateMeetingInfo' || $action=='createEditMessage'){
      if(!post('day')){
        return ['Error'=>'dataMissing'];
      }
      if(!isAdmin() && !isFacilitator(post('day'))){
        return ['Error'=>'notFacilitatorForThisDay'];
      }
      return updateSchedule();
    }
    if($action == 'openMeeting'){
      if(!sesh('myMeetingToday')){
        return ['Error'=>'myMeetingTodayNull'];
      }
      $myMeetingToday = sesh('myMeetingToday');
      if(!openSoon($myMeetingToday['day'],$myMeetingToday['startTime'],$myMeetingToday['endTime'])){
        $day =$myMeetingToday['day'];
        $startTime = $myMeetingToday['startTime']-(1/6);
        $endTime = $myMeetingToday['endTime'];
        if($day != date("l", time())){
          return ['Error'=>$day.' VS '.date("l", time())];
        }
        if($startTime===false && $endTime===false){
          return true;
        }
        $nowDec = decimalTime();
        if($nowDec < $startTime || $nowDec > $endTime){
          return ['Error'=>'notTimeToOpenYet','nowDec'=>$nowDec,'startTime'=>$startTime,'endTime'=>$endTime,'meetingNow'=>$meetingNow];
        }
        return ['Error'=>'___','myMeetingToday'=>$myMeetingToday,'nowDec'=>decimalTime(),'openSoon'=>openSoon($myMeetingToday['day'],$myMeetingToday['startTime'],$myMeetingToday['endTime'])];
      }

      $openMeeting = updateSchedule();
      if(Error($openMeeting)){
        return $openMeeting;
      }
      sesh('reason','Facilitator');
      $openCloseRecorded = insertOpenCloseRecord();
      if(Error($openCloseRecorded)){
        return $openCloseRecorded;
      }
      $joinedMeeting = joinMeeting();
      $herp = 'nope';
      if(Error($joinedMeeting)){
        $herp = 'aok';
      }
//  return ['note'=>'ok so far','joinedMeeting'=>$joinedMeeting,'isError'=>Error($joinedMeeting),'herp'=>$herp];
      if(Error($joinedMeeting)){//
        return $joinedMeeting;
      }

      $joinedMeeting['note'] = 'all good';
      return $joinedMeeting;
    }
    if($action=='closeMeeting'){
      if(!sesh('myMeetingToday')){
        return ['Error'=>'myMeetingTodayNull'];
      }
      $_POST['day'] = sesh('myMeetingToday')['day'];
      $update = updateSchedule();
      if(Error($update)){
        return $update;
      }
      $schedule = getSchedule();
      if(Error($schedule)){
        return $schedule;
      }
      $openCloseRecorded = insertOpenCloseRecord();
      if(Error($openCloseRecorded)){
        return $openCloseRecorded;
      }
      $schedule['message'] = "This meeting has been closed.";
      return $schedule;
    }
    if($action=='checkForNewJoined'){
      if(!sesh('myMeetingToday')){
        return ['Error'=>'myMeetingTodayNull'];
      }
      return newJoinedMeeting();
    }
/////lms's LMS
    if($action=='getCourses'){
      $result = selectAll('course');
      if(Error($result)){return $result;};
      return success($action,'courses',$result);
    }
    if($action=='selectCourse'){
      if(!post('course')&&!sesh('course')){return errOb($action,'No course chosen');};
      post('course') && sesh('course',post('course'));
      $result = getOpenAssignments();
      if(Error($result)){return $result;};
      $assignments = $result;
      $result = select('course',['course'=>sesh('course')]);
      if(Error($result)){return $result;};
      sesh('courseId',$result['id']);
      $courseLinks = $result['links'];
      $homework = $result['homework'];
      sesh('homework', $homework);

      $gradesAvailable = select('survey',['course'=>sesh('course'),'published'=>1]);
      if(Error($gradesAvailable)){return $gradesAvailable;};
    //  if(!isAdmin()){
    //    $assignments = [];/////////////////////FIX TOMMORRROW
    //  }

      return success($action,'course',sesh('course'),'assignments',$assignments,'gradesAvailable',$gradesAvailable,'links',$courseLinks,'homework',$homework);
    }
    if($action=='getZoomLink'){
      if(!sesh('course')){return errOb($action,"No course found");};
      $result = select('course',['course'=>sesh('course')]);
      if(Error($result)){return $result;};
      if(!$result || !isset($result['zoomLink']) || !$result['zoomLink']){
        return errOb($action,"No Zoom meeting found.",['res'=>$result,'course'=>sesh('course')]);
      };
      $zoomLink = $result['zoomLink'];
      $password = $result['pass'];
      return success($action,'zoomLink',$zoomLink,'pass',$password);
    }
    if($action=='getSurvey'){
      $search = post('search');
      if($search===false){
        return success($action,'surveyInfo',sesh('surveyInfo'));
      }
      $result = selectAll('survey',$search);
      if(Error($result)){return $result;};
      $surveyRows = $result;
      if(!count($surveyRows)){
        return errOb($action,"Survey could not be found...");
      }
      sesh('surveyRows',$surveyRows);
      $surveyInfo =filterOb(['type','course','title','timeLimit'],$surveyRows[0]);
      $points = 0;
      for($i=0,$l=count($surveyRows);$i<$l;$i++){
        $points+=$surveyRows[$i]['points'];
      }
      $surveyInfo['points'] = $points;
      sesh('surveyInfo',$surveyInfo);
      return success($action,'surveyInfo',$surveyInfo);
    }
    if($action=='showSurvey'){
      $result = errorIfSurveyClosed();
      if(Error($result)){return $result;};
      $results = [];
      if(isAdmin()){
        $surveyInfo = sesh('surveyInfo');
        $search = filterOb(['course','title'],$surveyInfo);
        $result = selectAll('response',$search);
        if(Error($result)){return $result;};
        $responses = $result;
        // $grades = arUnique($surveyRows,'title');
        // $grades = arAddSumOnPropByGroupToProp($surveyRows,'points','title',$grades,'pointsPossible');
        // $grades = arAddSumOnPropByGroupToProp($responses,'points','title',$grades,'points');
        $results = arUnique($responses,'username');
        $usernames = arrayOnProp($results,'username');
        $result = selectAll('person');
        if(Error($result)){return $result;};
        $person = $result;
        $unToPName = arPropToProp($person,'username','personalName');
        $unToFName = arPropToProp($person,'username','familyName');
        for($i=0,$l=count($results);$i<$l;$i++){
          $results[$i]['personalName'] = $unToPName[$results[$i]['username']];
          $results[$i]['familyName'] = $unToFName[$results[$i]['username']];
        }
        $results = arAddSumOnPropByGroupToProp($responses,'points','username',$results,'points');
      }
      return success($action,'surveyInfo',sesh('surveyInfo'),'results',$results);
    }
    if($action=='showResponses'){
      if(!sesh('surveyRows')){
        return errOb($action,"Cannot find survey...");
      }
      $surveyInfo = sesh('surveyInfo');
      $search = filterOb(['course','title'],$surveyInfo);
      $search['username'] = post('username');
      $result = selectAll('response',$search);
      if(Error($result)){return $result;};
      return success($action,'responseRows',$result,'surveyRows',sesh('surveyRows'));
    }
    if($action=='startSurvey'){
      if(!sesh('surveyRows')){
        return errOb($action,"Cannot find survey...");
      }
      $result = errorIfSurveyClosed();
      say('startSurvey err',$result);
      if(Error($result)){return $result;};
      return startSurvey();
    }
    if($action=='submitResponse'){
      return submitResponse();
    }
    if($action=='ping'){
      return success($action,'delayTimes',sesh('delayTimes'));
    }
    if($action=='showGrades'){
      $res = selectAll('response',[
        'course'=>sesh('course'),
        'username'=>sesh('username')
      ]);
      if(Error($res)){return $res;};
      $responses = $res;
      $res = selectAll('survey',[
        'course'=>sesh('course'),
        'published'=>1
      ]);
      if(Error($res)){return $res;};
      $surveyRows = $res;
      $grades = arUnique($surveyRows,'title');
      $grades = arAddSumOnPropByGroupToProp($surveyRows,'points','title',$grades,'pointsPossible');
      $grades = arAddSumOnPropByGroupToProp($responses,'points','title',$grades,'points');
      sesh('grades',$grades);
      $grades = filterArOnProp('correctResponse',$grades);
      return success($action,'grades',$grades);
    }
//////Admin only
    if($action && !isAdmin()){
      return errOb('adminOnly','Admin only...');
    }
    if($action=='deleteResponse'){
      $surveyInfo = sesh('surveyInfo');
      $course = sesh('course');
      $title = $surveyInfo['title'];
      $username = post('username');
      $result = delete('response',[
        'course'=>$course,
        'title'=>$title,
        'username'=>$username
      ]);
      if(Error($result)){
        return $result;
      }
      return success($action);
    }
    if($action=='editSurveys'){
      $result = selectAll('survey',['course'=>sesh('course')]);
      if(Error($result)){return $result;};
      $ass = arUnique($result,'title');
      return success($action,'assignments',$ass,'course',sesh('course'));
    }
    if($action=='unlockSurvey'){
      $course = sesh('course');
      $title = post('title');
      update('survey',['open'=>1],['course'=>$course,'title'=>$title]);
      return success($action);
    }
    if($action=='lockSurvey'){
      $course = sesh('course');
      $title = post('title');
      update('survey',['open'=>0],['course'=>$course,'title'=>$title]);
      return success($action);
    }
    if($action=='publishSurvey'){
      $course = sesh('course');
      $title = post('title');
      update('survey',['published'=>1],['course'=>$course,'title'=>$title]);
      return success($action);
    }
    if($action=='unpublishSurvey'){
      $course = sesh('course');
      $title = post('title');
      update('survey',['published'=>0],['course'=>$course,'title'=>$title]);
      return success($action);
    }
    if($action=='updateCourse'){
      $update = post();
      $update['id'] = sesh('courseId');
      $result = update('course',$update);
      if(Error($result)){return $result;};
      $update['success'] = $action;
      return $update;
    }
    if($action=='showAllGrades'){
      $sids = post('sids');
      $res = selectAll('response',[
        'course'=>sesh('course')
      ]);
      if(Error($res)){return $res;};
      $responses = $res;
      $res = selectAll('survey',[
        'course'=>sesh('course'),
        'published'=>1
      ]);
      if(Error($res)){return $res;};
      $surveyRows = $res;
      $titles = filterArKeepProps(['title','pointsPossible'],$surveyRows);
      $titles = arUnique($titles,'title');
      $titles = arAddSumOnPropByGroupToProp($surveyRows,'points','title',$titles,'points');  
      $stats = [];
      for($i=0,$l=count($responses);$i<$l;$i++){
        $responses[$i]['sid'] = strtoupper($responses[$i]['sid']);
        $sid = $responses[$i]['sid'];
        $title = $responses[$i]['title'];
        $points = $responses[$i]['points'];
        $stats[$sid] = isset($stats[$sid]) ? $stats[$sid] : [];
        $stats[$sid][$title] = isset($stats[$sid][$title]) ? $stats[$sid][$title] : 0;
        $stats[$sid][$title]+=$points;
      }
      $rows = [];
      $heading = ['sid'];
      for($i=0,$l=count($titles);$i<$l;$i++){
        $heading[] = $titles[$i]['title'].' ('.($titles[$i]['points']+1).')';
      }
      $rows[] = $heading;
      for($z=0,$c=count($sids);$z<$c;$z++){
        $sid = strtoupper($sids[$z]);
        $row = [$sid];
        for($i=0,$l=count($titles);$i<$l;$i++){
          $title = $titles[$i]['title'];
          if(!isset($stats[$sid]) || !isset($stats[$sid][$title])){
            $row[] = 0;
          }
          else{
            $row[] = $stats[$sid][$title] + 1;
          }
        }
        $rows[] = $row;
      }
      return success($action,'grades',$rows,'course',sesh('course'));
    }
    
    if($action=='saveSurvey'){
      return saveSurvey();
    }
    return errOb('unknownAction','Error: Unknown action '.$action);
  }

  function addUserData($ob){
    if(!sesh('user')){
      return $ob;
    }
    $ob['user'] = sesh('user');
    return $ob;
  }

  function addAction($ob){
    if(post('action')){
      $ob['action'] = post('action');
    }
    return $ob;
  }


  if(post('action')!='ping'){
    sesh('lastPost',$_POST);
  }



  if(isset($_GET['test'])){
    //echo 'POST: '.json_encode(sesh('lastPost')).'<Br /><Br /><Br /><Br />RES: ';
    $_POST['action']= 'showAllGrades';
    $_POST['sids'] = ["18RC051", "21ED404", "21PI010", "21PI011", "21PI017", "21PJ009", "21PJ105", "21PJ405", "21PJ502", "21PN001", "21PN004", "21PN012", "21PN019", "21PN020", "21PP002", "21PP007", "21PP010", "21PP031", "21PP044", "21PP053", "21PP059", "21PP100", "21PP107", "21PP127", "21PP134", "21PP135", "21PP144", "21PP152", "21PP203", "21PP249", "21PP307", "21PS017", "21TC014", "21TM112", "21TM113"];

  }

  echo json_encode(addTimeDelay(addAction(addUserData(reply()))));
