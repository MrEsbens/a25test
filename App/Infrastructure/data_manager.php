<?php
namespace DataManagers;


require_once 'Entities/data_mode_enum.php'; use DataSources\DataSource;
use Exception;

require_once 'sdbh.php'; use sdbh\sdbh;

/*
    Adapter class for operating data from different data sources
*/
class DataManager
{
    private string $datasource;
    private $dbh; 

    /**
    * @param datasource - type of data source
    **/
    function __construct($datasource = DataSource::DataBase)
    {
        $this->datasource = $datasource;

        switch($this->datasource)
        {
            case DataSource::DataBase:
                $this->dbh = new sdbh();
                break;
            default:
                throw new Exception("Unacceptable datasource: " . $this->datasource);
        }
    }

    /**
     * function selects all data from table
     * @param table - table name
    **/
    public function select_all(string $table)
    {
        switch($this->datasource)
        {
            case DataSource::DataBase:
                $selection = $this->dbh->make_query('SELECT * FROM '.$table);
                return $selection;
        }
    }

    /**
     * function selects product data from table
     * @param table - table name
     * @param id - product id
    **/
    public function select_product(
        $table,
        $id
    )
    {
        switch ($this->datasource) 
        {
            case DataSource::DataBase:
                $selection = $this->dbh->make_query('SELECT * FROM '.$table.' WHERE ID = '.$id);
                return $selection;
        }
    }

    /**
     * By given parameters returnes selection of values in array
     * @param $tbl_name - table name
     * @param $select_array - field - value condition array
     * @param $from , $amount - limits
     * @param $order_by - field to sort anser by
     * @param $order - "DESC" or Null
     * @param $deadlock_up - throw deadlock_exception out or restart query internaly
     * @param $lock_mode - LISH - LOCK IN SHARE MODE, FU - FOR UPDATE or Null - nothing
     * @return query result as array of rows, each an associative array
     */
    public function select_values(
        $tbl_name,
        $select_array,
        $from, $amount,
        $order_by,
        $order = Null,
        $deadlock_up = False,
        $lock_mode = Null
    )
    {
        switch ($this->datasource)
        {
            case DataSource::DataBase:
                $selection = $this->dbh->mselect_rows($tbl_name, $select_array, $from, $amount, $order_by, $order , $deadlock_up, $lock_mode);
                return unserialize($selection[0]['set_value']);
        }
    }
}
