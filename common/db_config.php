<?php

date_default_timezone_set('Etc/GMT-5');

//require_once('include/helper.php'); // include Alain's helper functions

require_once('MysqliDb.php');

//require_once('encrypt.php');

// print_r(__LINE__);
// die();
// Enforce E_ALL, but allow users to set levels not part of E_ALL.
//error_reporting(E_ALL | error_reporting());

// initialize variables
$data = array();

// database configuration starts here //

// db var config
$server_host = $_SERVER['HTTP_HOST'];
// $db_host = $confighost;
$dbpassdata="lkevacQaV6VckdEVKbAANqnxRfwspv6618DtG3D399dJST9ut/impGbyNP4mrqn4TB45yOmBdydBt1DR4FfsQd13T4LX5Wtprv4ADcPMZB/c7uDHY8WH2OMhGeH+hoyf|NinFqSYPFzRAARrSUMg5FwF5WjrjKNWMFVNrChgrWPM=";
$dbuserdata="KjQu4XDzpx6tbqhFGPUdfQaEUR/SjtQoiD9IHdx5H6qPa8O/jEUMjZL4s2bhtsa4qrbqb+UfIzUUPMOK2oFhP7JtN+6hwPGToyz1yuAoj83HbpwVfP+Z9SoUJqiJMA4J|ns24jfQxfvFyt2ac9jX0jCmWDkD8ik2dGYI6pboJ+kU=";
$dbkey="ccb5154d0fd67524f5aa6dc9dd388806022bd0c50831e10e9fef2e567b31ba76";

//$userd=mc_decrypt($dbuserdata, $dbkey);
//$passd=mc_decrypt($dbpassdata, $dbkey);

// $db_user = "supremeUser";
// $db_pass = "SupremeDb2018@Secure";
// $db_name = 'suprpaysez';

$db_host = 'localhost';
$db_user = "root";
$db_pass = "";
$db_name = 'suprpaysez';

//$db = MysqliDb::getInstance();
//var_dump($db);exit();

$db = new Mysqlidb ($db_host, $db_user, $db_pass, $db_name);

session_start();
ob_start();