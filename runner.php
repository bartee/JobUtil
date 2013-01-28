<?php
date_default_timezone_set('Europe/Amsterdam');
error_reporting(E_ALL);

// Config
define('BASEDIR', dirname(__FILE__));

define('LOCKDIR', BASEDIR.'/lock');
define('LOGDIR', BASEDIR.'/log');

define('JOB_DIR', BASEDIR.'/Jobs');

require_once(BASEDIR .'/Base/CronJob.php');


$jobname = $_SERVER['argv'][1];

if (!$jobname || count($_SERVER['argv']) != 2){
	die('illegal request!');
	exit;
}
$path = JOB_DIR.'/'.$jobname.'.php';

if (preg_match('/[a-z0-9A-Z]*/', $jobname) != 1 || !file_exists($path)){
	die('illegal jobname!');
	exit;
}

require_once(JOB_DIR.'/'.$jobname.'.php');
if (!class_exists($jobname)){
	die('Illegal jobname!');
	exit;
}
echo '

Running '.$jobname.'...';
$job = new $jobname();
if (!$job->canBeRun()){
	// Mismatch in scheduling job 
	echo '
Job can not be run!';
	exit;
}
// Executing job 
$job->execute();