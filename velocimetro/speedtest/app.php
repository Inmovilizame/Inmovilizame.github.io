<?php
include_once('SpeedTest.php');
include_once('FirebaseLib.php');

$fbSpeeds = 'https://velocimetro.firebaseio.com/';
$fbRef = new FirebaseLib($fbSpeeds);

$speedtest = new SpeedTest();
$data = $speedtest->runTest();

$fbRef->push('/speeds/', $data);
