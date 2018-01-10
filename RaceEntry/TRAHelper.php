<?php

class TRAHelper {

  //generic unordered list builder
  //create a href to a php file passing it the primary key as a parameter
  //assumes PK is first column  
  static function buildList($table,$php,$where_clause = null) {
    global $db;

    //set query to get all detail
    $query = "SELECT * FROM $table";

    if($where_clause != null) {
      $quert = $query." WHERE $where_clause";
    }

    //run the query
    $result = $db->query($query); 
    //initialise output string - an unordered list
    $list ="<ul>";

    //get the results a record at a time and build list
    while (($row=$db->fetch_array())){
        $count = 0;
        foreach($row as $value)
        {
           if ($count == 0){//first col so add href
               $list = $list."<li><a href=".$php."?param=".$value.">".$value."</a>";  
           }
           elseif ($count < count($row)-1) {//just add data
               $list = $list." ".$value;
           }
           else {//last col so also add end li
               $list = $list." ".$value."</li>";
           }
           $count++;
           unset($value);
        }//end foreach
    }//end while

    return $list."</ul>";
  }


  //generic select builder
  //if $optionOnly set true - returns option tags
  //else returns full select tag
  static function buildSelect($col1,$col2,$table,$optionOnly = false,$where_clause = null) {
    global $db;

    //set query to get the TRA detail
    $columns = $col2 ? $col1.",".$col2 : $col1;

    $query = "SELECT $columns FROM $table";

    if($where_clause != null) {
      $query = $query." WHERE $where_clause";
    }
    
    //run the query
    $result = $db->query($query);
   
    //local var for <option>s
    $opts ="";

    //get the results a record at a time and build option list for <select>
    while (($row = $db->fetch_row())){
      $opts = $opts."<option value=".$row[$col1].">".$row[$col2 ? $col2 : $col1]."</option> ";    
    }
    if (!$optionOnly)//add select tags
    {
      $opts= "<select name='$col1'>".$opts."</select>";
    }
    return $opts;
  }

}

?>
