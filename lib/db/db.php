<?php
// -------------------------------------------
// db.php
//
// Steven Mead 2016
// School of Computing
// Teesside University
//
// DB is a simple class for opening an connection
// and sending queries to an MS-SQL server.
// -------------------------------------------
class DB {
  const FETCH_NUM   = MSSQL_NUM;
  const FETCH_ASSOC = MSSQL_ASSOC;
  const FETCH_BOTH  = MSSQL_BOTH;

  private $db_server;
  private $db_name;
  private $svr_name;
  private $last_query_result;
  
  function __construct($db_name, $svr_name) {
    $this->db_name = $db_name;
    $this->svr_name = $svr_name;
  }

  function __destruct() {
    if($this->db_server) {
      mssql_close($this->db_server);
      $this->db_server = null;
      $this->clean();
    }
  }

  function connect($usr_name, $pw) {
    //ignore repeated attempts to connect.
    if($this->db_server) return;

    //connect to uni sqlserver
    $this->db_server =  mssql_connect(
                          $this->svr_name,
                          $usr_name,
                          $pw);
    //fail safe
    if (!$this->db_server) {
      //output error message
      die("unable to connect to server: ".$this->svr_name);
    }
    else {
      //select the database to be used (your user id)
      mssql_select_db($this->db_name);
    }
  }

  function query($query) {
    if($this->db_server) {
      $this->last_query_result = mssql_query($query);
      return $this->last_query_result != null;
    }
  }

  function fetch_row() {
    if($this->db_server) {
      return mssql_fetch_array($this->last_query_result);
    }
  }

  function fetch_array($fetch_as = DB::FETCH_NUM) {
    if($this->db_server) {
      return mssql_fetch_array($this->last_query_result, $fetch_as);
    }
  }

  function affected_row_count() {
    if($this->db_server) {
      return mssql_rows_affected($this->db_server);
    }
  }

  function clean() {
    if($this->db_server && $this->last_query_result) {
      //finish the query and reclaim the memory used
      mssql_free_result($this->last_query_result);
    }
  }

}

?>
