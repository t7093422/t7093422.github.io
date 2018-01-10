<?php
/*
TRARaceEntryController.php

Actions:
-------------------------------------------
index - Simply provides a list of options.

*/
require_once '../lib/controller.php';
require_once "./TRAHelper.php";

$FileName = extract_file_name(__FILE__);              //DO NOT EDIT
$ControllerName = extract_controller_name($FileName); //DO NOT EDIT

class MainController extends Controller {

  function index() {
    global $db;

    $output = <<<_END
Select an option:
<ol>
<li><a href="{$this->pathToOther("TRAMemberController")}">View member list</a></li>
<li><a href="{$this->pathToOther("TRAMemberController","add")}">Add a member</a></li>
<li><a href="{$this->pathToOther("TRARaceEntryController","")}">Race Entry List</a></li>
<li><a href="{$this->pathToOther("TRAClubController","")}">Running Club List</a></li>
... unfinished
</ol>
_END;

	 $this->view->render("TRA Membership Mini-site",$output);

  }

}

Controller::router($ControllerName);

?>
