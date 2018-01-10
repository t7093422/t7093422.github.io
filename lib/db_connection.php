<?php
// -------------------------------------------
// db_connection.php
//
// Steven Mead 2016
// School of Computing
// Teesside University
//
// Creates a global DB instance for
// use throughout the CodeHarness.
// -------------------------------------------

require_once 'exception_thrower.php';
require_once 'db/db.php';

//Replace the values in angled brackets with
//your SQL-SERVER details.
//
//Note: Remove the angled brackets!
$db = new DB('<Your database name>','scm-database.tees.ac.uk');

if($db) {
  $db->connect('<Your user ID>','<Your MS-SQL password>');
}

?>
