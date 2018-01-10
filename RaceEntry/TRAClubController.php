<?php
/*
TRAClubController.php

Actions:
-------------------------------------------
index  : Lists all the records.
add    : Builds a form to create a new record.
create : Inserts a new record into the database table.
edit   : Builds a form used to edit a record
update : Updates an existing record.
delete : Builds a form to delete an existing record.
destroy: Destroys an existing record.

*/
require_once '../lib/controller.php';
require_once "./TRAHelper.php";

$FileName = extract_file_name(__FILE__);              //DO NOT EDIT
$ControllerName = extract_controller_name($FileName); //DO NOT EDIT

class TRAClubController extends Controller {

  /**
   * index()
   *
   * Performs the SQL query and appropriate HTML
   * to SELECT all the club records.
   */
  function index() {
    global $db;

    $output = "";

    //TODO: SELECT query to retrun all the members of a specified clubId
    //
    $query = <<<_CLUB_SELECT_QUERY
      SELECT
        member_count = (
          // TODO: Sub-query to COUNT the members from each club
        ),
        *
      FROM
        // TODO: Specify the main table.
      ORDER BY
        name ASC
_CLUB_SELECT_QUERY;

    $result = $db->query($query);

    if($db->affected_row_count() >= 1) {
      $output .= "<table><thead><tr class='header-row'><th>Name</th><th>Member Count</th><th></th></tr></thead><tbody>";
      while($row = $db->fetch_row()) {
        $output .= <<<_ROW
          <tr>
            <td>{$row['name']}</td>
            <td>{$row['member_count']}</td>
            <td><a href="{$this->path("members")}?clubid={$row['clubId']}">List Members</a></td>
          </tr>
_ROW;
      }
      $output .= "</tbody></table>";
    }

    $output .= "<br><a href='{$this->pathToOther("MainController")}'>Back to main page</a>";

  	$this->view->render("Running Club Index",$output);

  }

  /**
   * members()
   *
   * Returns a collection of members that are all members of the same running club.
   */
  function members() {
    global $db;

    $clubId = $this->param('clubid');

    //TODO: SELECT query to retrieve all the members that belong to the specified club.
    //      Add the missing parts of the query.
    $query = <<<_CLUB_MEMBER_QUERY

      SELECT
        m.memberId,                 //TODO: Update field names
        m.name AS tra_member_name,  //      to match
        c.name AS club_name         //      your database.
      FROM
        // TODO: Specify the main table involved in the query
      JOIN [TRA2016].[clubs] AS c
      ON
        // TODO: How to join the two tables involved in the query
      WHERE c.clubId = {$clubId}

_CLUB_MEMBER_QUERY;
    //
    //END TODO

    //run the query
    $result = $db->query($query);

    $output = "<table><thead><tr class='header-row'><td>TRAnumber</td><td>Member Name</td><td>Club Name</td></tr></thead><tbody>";

    //output the results a record at a time
    //
    while (($row = $db->fetch_array(DB::FETCH_BOTH))) {
      //***************************************************
      // You might need to edit column names here
      // **************************************************
      $output .= "<tr><td>{$row['memberId']}</td><td>{$row['tra_member_name']}<td>{$row['club_name']}</td></tr>";
    }

	  $output .= "</tbody></table>";

    $output .= "<br><a href='{$this->path()}'>Back to club index</a>";
	  $output .= "<br><a href='{$this->pathToOther("MainController")}'>Back to main page</a>";

	  $this->view->render("Members by Club",$output);
  }

}

Controller::router($ControllerName);

?>
