<?php
namespace Lyra;

class Profiler
{
	public $starttime;
	public $timekeep = array();
	public $count = 0;
	public function __construct()
	{
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$this->starttime = $mtime;
	}
	
	public function setTime($name)
	{
	   $mtime = microtime();
	   $mtime = explode(" ",$mtime);
	   $mtime = $mtime[1] + $mtime[0];
	   $endtime = $mtime;
	   $totaltime = ($endtime - $this->starttime);
	   $this->timekeep[$this->count++ . '. ' .$name] =$totaltime;
	}
	
	public function getTime()
	{
		return $this->timekeep;
	}
}
