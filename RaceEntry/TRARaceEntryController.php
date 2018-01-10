<?php
/*
TRARaceEntryController.php

Actions:
-------------------------------------------
index  : Lists all the records.
add    : Builds a form to create a new record.
create : Inserts a new record into the database table.
edit   : Builds a form used to edit a record
update : Updates an existing record.
delete : NOT IMPLEMENTED AS IT IS NOT USED.
destroy: Destroys an existing record.

*/

// connect to dbase
require_once '../lib/controller.php';
require_once "./TRAHelper.php";

$FileName = extract_file_name(__FILE__);              //DO NOT EDIT
$ControllerName = extract_controller_name($FileName); //DO NOT EDIT

class TRARaceEntryController extends Controller {

  /**
   * index()
   *
   * Performs the SQL query and appropriate HTML
   * to SELECT all the race entry records.
   */
  function index() {
    global $db;

    //TODO: Query to get all race entry records here:
    //
    //      Note: This is Query 3 from the ICA documenation.
    //
    $query = <<< _QUERY
      SELECT
              // TODO:

              // State the table field that your need from the tables.

              // Notes:
              // 1) bib number should be FORMATed in a 4 digit zero padded
              //    number.  For example: 0123; 0028; 0001; etc.
              // 2) posistion:
              // Use a SQL CASE statement to display the race position.
              //      -1 indicates Did Not Finish (DNF).
              //       0 indications the race has not taken place yet.
              //       Any other value is the position that the runner came,
              //       which is to be FORMATed as a 0 zero-padded number
              //       for example: 0019.
              // 3) points:
              //    The points are not stored, they are computed.
              //    Use a SQL CASE statement to calculate determine the
              //    points based upon the runners position.
              //    See ICA documentation for how this is computed.

        FROM // TODO: Specify the main table

        JOIN // TODO: Join as many tables as you think you need.
             //       You will need more than 1 JOIN!

        GROUP BY // TODO: Specify how the data is to be grouped
        ORDER BY // TODO: Specify that the data is ordered by race id, then race position.
_QUERY;
    //
    //END TODO

    $result = $db->query($query);

    //Build the output here:
    //
    $output = "<table><thead><tr class='header-row'><td>Race Id</td><td>Venue</td><td>Date</td><td>Bib Number</td><td>Member Name</td><td>Age Category</td><td>Club</td><td>Position</td><td>Points</td><td></td><td></td></tr></thead><tbody>";

    while (($row = $db->fetch_array(DB::FETCH_BOTH))) {
      //TODO:
      //
      // Check and if required modify the field names below, for example 'raceId'
      // so that they correspond to your SELECT query above or your table field
      // names.  If you've used the AS clause in the SQL statement above, then
      // you will use that name instead.
      $output .= <<<_END
      <tr>
        <td>{$row['raceId']}</td>
        <td>{$row['venue_name']}</td>
        <td>{$row['date']}</td>
        <td>{$row['bibNo']}</td>
        <td>{$row['member_name']}</td>
        <td>{$row['description']}</td>
        <td>{$row['club_name']}</td>
        <td>{$row['position']}</td>
        <td>{$row['points']}</td>
        <td><a href="{$this->path("edit")}?raceentryid={$row['race_entry_id']}">Update</a></td>
        <td><a class="destroy" href="{$this->path("destroy")}?raceentryid={$row['race_entry_id']}">Delete</a></td>
      </tr>
_END;
      //
      //END TODO
    }

    $output .= "</tbody></table>";

    $output .= "<br><a href='{$this->pathToOther("MainController")}'>Back to main page</a>";

    $this->view->render("Index of TRA Race Entry",$output);

  }

  /**
   * add()
   *
   * Builds a form to create a race entry record.
   */
  function add() {
    global $db;

    $traNum = $this->param('tranum');

    //TODO: SQL SELECT query to get the name of the
    //      specified TRA member.
    $query = <<<_MEMBER_QUERY

      // Your query here.

_MEMBER_QUERY;
    //
    //END TODO

    $result = $db->query($query);

    $row = $db->fetch_row();

    //TODO: If you're name field in your member table is not called
    //      'name', change it to whatever it is called.
    //
    $name = $row['name'];
    //
    //END TODO

    $output = "<h2>Available races for {$name}</h2>";

    //TODO: Query to get all races that can be entered:
    //
    //      Note: This is Query 1 from the ICA documentation.
    //
    //      Hint: You can use a WHERE NOT EXISTS clause and a
    //            inner SELECT statement on the member's
    //            current race entries:
    //            WHERE NOT EXISTS
    //            (
    //               SELECT the races that the member has already entered.
    //            )
    $raceQuery = <<<_RACE_QUERY

      // Your SQL query here.

_RACE_QUERY;

    $result = $db->query($raceQuery);

    if($db->affected_row_count() > 0) {

    //Build a list from this query
    $options = "";

    while (($row = $db->fetch_row())){

        //TODO: Ensure that the field names match your table column names
        //      or the alias you gave it using the AS clause in the SQL
        //      query.
        //
        $options .= <<<_RACE_OPTION
          <option value="{$row['raceId']}">{$row['name']} ({$row['date']})</option>;
_RACE_OPTION;

        //
        //END TODO
      }


      $output .= <<<_END
        <form action="{$this->path("create")}" method="POST">
          <p>
            <input type="hidden" name="fmemberid" value="{$traNum}" />
          </p>
          <p>
            <label for="fraceid">TRA Race Date</label>
            <select name='fraceid'>{$options}</select>
          </p>
          <p>
            <label for="fbibno">Bib Number</label>
            <input type="number" name="fbibno" value="1" min="1"/>
          </p>
          <input type="submit" name="submit" value="Create"/>
        </form>
_END;

    }
    else {
      $output .= "There are no more races for this runner to run at this time.";
    }

    $output .= "<br><br><a href='{$this->pathToOther("TRAMemberController")}'>Back to Member listing page</a>";

    $this->view->render("TRA Race Entry Form",$output);
  }

  /**
   * create()
   *
   * Performs the SQL query and appropriate HTML
   * to INSERT a new race entry record.
   */
  function create() {
    global $db;

    $traNum = $this->param('fmemberid');
    $raceId = $this->param('fraceid');
    $bibNo = $this->param('fbibno');

    $output = "";

    //TODO: SQL statement to get the member's current age category and clubId
    //
    $memberQuery = <<<_QUERY

      // Your SQL query here.

_QUERY;

    //
    //END TODO

    $memberResult = $db->query($memberQuery);

    if($memberResult) {
      $row = $db->fetch_row();

      $ageCategoryCode = $row['ageCategoryCode'];

      $clubId = $row['clubId'];

      //TODO: Query to INSERT a new race entry record here:
      //
      $query = <<<_QUERY

        // Your SQL query here.

_QUERY;

      $result = $db->query($query);

      //
      //END TODO

      if ($result){
        $output = "Inserted";
      }
      else {
        $output = "Insert failed";
      }
    }

    $output .= "<br><a href='{$this->pathToOther("TRAMemberController")}'>Back to Member listing page</a>";

    $this->view->render("TRA Race Entry Create Result",$output);

  }

  /**
   * edit()
   *
   * Builds a form to edit an existing race entry record.
   */
  function edit() {

    global $db;

    $raceentryid = $this->param('raceentryid');

    //TODO: Query to SELECT a specific race that
    //      a member has entered so that it can
    //      be updated with points and position
    //      information.
    $raceEntryQuery = <<<_RACE_ENTRY_QUERY

      // Your SQL query here.

_RACE_ENTRY_QUERY;
    //
    //END TODO

    $result = $db->query($raceEntryQuery);

    $row = $db->fetch_row();

    //TODO:  Ensure that your field names match those below, for example
    //       'venue_name' and 'member_name'.  The will be either your table's
    //       field names or the alias your specified using the AS clause in
    //       in the SQL query above.
    //
    $output = "Update {$row['venue_name']} on date {$row['date']} entry for entrant {$row['member_name']}";

    $output .= <<<_END
      <form action="{$this->path("update")}" method="POST">
        <input type="hidden" name="fraceentryid" value="{$raceentryid}"/>
        <p>
          <label for="fposition">Position</label>
          <input type="number" name="fposition" min="-1" value="{$row['current_position']}"/>
        </p>
        <p>
          <label for="fbibnum">Bib Number</label>
          <input type="number" name="fbibnum" value="{$row['current_bib_no']}" min="1"/>
        </p>
        <input type="submit" name="submit" value="Update"/>
      </form>
_END;
    //
    //END TODO

    $output .= "<br><a href=\"{$this->path()}\">Back to race listing</a>";

    $this->view->render("Update TRA Race Entry",$output);
  }

  /**
   * update()
   *
   * Performs the SQL query and appropriate HTML
   * to UPDATE an existing race entry record.
   */
  function update() {
    global $db;

    $raceEntryId = $this->param('fraceentryid');
    $position = $this->param('fposition');
    $bibNum = $this->param('fbibnum');

    //TODO: Query to UPDATE a race entry record here:
    //
    $query = <<<_UPDATE_RACE_ENTRY_QUERY

      // Your SQL query here.

_UPDATE_RACE_ENTRY_QUERY;

    $output = "";

    //
    //END TODO

    $result = $db->query($query);

    if($result) {
      $output .= "Update Success";
    }
    else {
      $output .= "Update Failed";
    }

    $output .= "<br><a href=\"{$this->path()}\">Back to race listing</a>";

    $this->view->render("Update TRA Race Entry",$output);
  }

  /**
   * destroy()
   *
   * Performs the SQL query and appropriate HTML
   * to DELETE an existing race entry record.
   */
  function destroy() {
    global $db;

    $raceentryid = $this->param('raceentryid');

    //TODO: Query to DELETE a race entry record here:
    //
    $query = <<<_DESTROY_RACE_ENTRY_QUERY

      // Your SQL query here.

_DESTROY_RACE_ENTRY_QUERY;
    //
    //END TODO

    // Run the SQL query:
    //
    $result = $db->query($query);

    $output = "";

    if($result) {
      $output .= "Delete Success";
    }
    else {
      $output .= "Delete Failed";
    }

    $output .= "<br><a href=\"{$this->path()}\">Back to race listing</a>";

    $this->view->render("Delete TRA Entry",$output);
  }
}

Controller::router($ControllerName);

?>
