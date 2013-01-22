<?php
date_default_timezone_set('Europe/Amsterdam');
error_reporting(E_ALL);

// Config
define('BASEDIR', dirname(__FILE__));

define('LOCKDIR', BASEDIR.'/lock');
define('LOGDIR', BASEDIR.'/log');

define('JOB_DIR', BASEDIR.'/Jobs');

require_once(BASEDIR .'/Base/CronJob.php');
require_once(JOB_DIR .'/TestJob.php');
