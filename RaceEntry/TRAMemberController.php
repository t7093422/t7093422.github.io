<?php
/*
TRAMemberController.php

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

class TRAMemberController extends Controller {

  /**
   * index()
   *
   * Performs the SQL query and appropriate HTML
   * to SELECT all the member records.
   */
  function index() {
    global $db;

    //TODO: Query 3 to get all member records here:
    //
    $query = <<<_MEMBER_QUERY


      SELECT

        // TODO:
        // State the table field that your need from the tables.

        // Notes:
        // 1) Include a computed field will be the total number of completed races
        // 2) Include a computed field that is the calculated number of points
        //    based on the position of each race (an aggregate function).
        //

        FROM // TODO: State the main table.

        LEFT JOIN // TODO: Which table to join
          ON // TODO: Which columns to join on
		GROUP BY // TODO: How to group the aggregated data (points)
        ORDER BY // TODO: Order the data by points and member name.
_MEMBER_QUERY;
    //
    //END TODO

    //Execute the query
    //
    $db->query($query);

	  $output = "";

    //TODO: Build the output here:
    //
    if($db->affected_row_count() >= 1) {
      $output .= "<table><thead><tr class='header-row'><th>Name</th><th>Completed Races</th><th>Total Race Points</th><th></th><th></th></tr></thead><tbody>";
      while($row = $db->fetch_row()) {
        $output .= <<<_ROW
          <tr>
            <td>{$row['name']}</td>
            <td>{$row['completed_races']}</td>
            <td>{$row['points']}</td>
            <td><a class="destroy" href="{$this->path("destroy")}?tranum={$row["memberId"]}">Delete</a></td>
            <td><a href="{$this->pathToOther("TRARaceEntryController","add")}?tranum={$row['memberId']}">Register for a race</a></td>
          </tr>
_ROW;
      }
      $output .= "</tbody></table>";
    }

    $output .= "<br><a href='{$this->path("add")}'>Add a new member</a>";
    $output .= "<br><a href='{$this->pathToOther("MainController")}'>Back to main page</a>";

    $this->view->render("Index of TRA Members",$output);
    //
    //END TODO
  }

  /**
   * add()
   *
   * Builds a form to create a member record.
   */
  function add() {
    global $db;

    $selectHTML1 = TRAHelper::buildSelect('clubId','name','[TRA2016].[clubs]',true);
    $selectHTML2 = TRAHelper::buildSelect('ageCategoryCode','description','[TRA2016].[agecategories]',true);

  //output form
    $output = <<<_END
      <p>Please fill in all details and press insert<p/>
      <form action="{$this->path('create')}" method="post">
      <p>
        <label for="fname">Name</label>
        <input type="text" name="fname" />
      </p>

      <p>
        <label for="fclub">Club</label>
        <select name='fclub'>{$selectHTML1}</select>
      <p/>

      <p>
        <label for="fage">Age Class</label>
        <select name='fage'>{$selectHTML2}</select>
      </p>

      <input type="hidden" name="fyearpaid" value="2014" />

      <input type="submit" value="insert" />

      </form>
_END;

    $output .= "<br><a href='{$this->pathToOther("MainController")}'>Back to main page</a>";

	  $this->view->render("New Member Form",$output);
  }

  /**
   * create()
   *
   * Performs the SQL query and appropriate HTML
   * to INSERT a new member record.
   */
  function create() {
    global $db;

    // Extract the POST parameters set by the form.
    //
    $name = $this->param('fname');
    $clubId = $this->param('fclub');
    $ageCategoryCode = $this->param('fage');
    $yearpaid = $this->param('fyearpaid');

    //TODO: Query to INSERT a new member record here:
    //
    $query = <<< _INSERT_QUERY

      // Your query here.

_INSERT_QUERY;

    //
    //END TODO

    //run the query
    $result = $db->query($query);

    //check result and output message
    if ($result){
      $output = "Inserted";
    }
    else {
      $output = "Insert failed";
    }

    $output .= "<br><a href='{$this->path()}'>Back to member listing</a>";
    $output .= "<br><a href='{$this->pathToOther("MainController")}'>Back to main page</a>";

    $this->view->render("Create Result",$output);
  }

  /**
   * delete()
   *
   * Builds a form to delete an existing member record.
   */
  function delete() {

    $output = <<<_END
      <form action="{$this->path("destroy")}" method="post">
        <input type="submit" name="submit" value="Delete"/>
      </form>
_END;

    $this->view->render("Delete Form",$output);
  }

  /**
   * destroy()
   *
   * Performs the SQL query and appropriate HTML
   * to DELETE an existing member record.
   */
  function destroy() {
    global $db;

    $traNum = $this->param('tranum');

    //TODO: Query to DELETE a member record here:
    //
    $query = <<< _DELETE_QUERY

      // Your query here

_DELETE_QUERY;
    //
    //END TODO

    $result = $db->query($query);

    if ($result){
      $output = "Deleted";
    }
    else {
      $output = "Delete failed";
    }

    $output .= "<br><a href='{$this->path()}'>Back to member list</a>";

    $this->view->render("Delete Result",$output);
  }

}

Controller::router($ControllerName);

?>
