<?php

  date_default_timezone_set("Asia/Tokyo");

  class Calendar{

    public $months = [''];

    //$cal must be in chronological order

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
      $cal = explode("\n",trim("
Year
  2020

Open
  September 28 - November 19
  November 27 - December 23
  January 12 - February 8

Closed
  September 2 - September 4
  January 15

      "));
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
      echo json_encode($sched);
      echo json_encode($this->sched);
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
          return ['nextOpenDate'=>''];
        }
        while(!$this->isOpen($dayTime,'weekDaysOnly')){
          $dayTime+=24*60*60;
        }
        return ['nextOpenDate'=>date('l, F jS',$dayTime)];
      }
      return !$open;
    }
  }

  $calendar = new Calendar();
  echo json_encode($calendar->isClosed('Wednesday','$getNextOpenDate'));
