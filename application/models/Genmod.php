<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of Genmod
 *
 * @author Amir <amirsanni@gmail.com>
 */
class Genmod extends CI_Model{
    
    public function __construct() {
        parent::__construct();
    }

    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

    /**
     * Update any single column in any table using a single column in the where clause
     * @param string $tableName the name of the table to update
     * @param string $colName name of column to update
     * @param mixed $colVal value to insert into $colName
     * @param string $whereCol column to use in the where clause
     * @param mixed $whereColVal value of column $whereCol
     * @return boolean
     */
    public function updateTableCol($tableName, $colName, $colVal, $whereCol, $whereColVal){
        $q = "UPDATE $tableName SET $colName = ? WHERE $whereCol = ?";
        
        $this->db->query($q, [$colVal, $whereColVal]);
        
        if($this->db->affected_rows() > 0){
            return TRUE;
        }
        
        else{
            return FALSE;
        }
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * get a single column from any table using a single column in the where clause
     * @param string $tableName
     * @param string $selColName
     * @param string $whereColName
     * @param mixed $colValue
     * @return boolean
     */
    public function getTableCol($tableName, $selColName, $whereColName, $colValue){
        $q = "SELECT $selColName FROM $tableName WHERE $whereColName = ?";
        
        $run_q = $this->db->query($q, [$colValue]);
        
        if($run_q->num_rows() > 0){
            foreach($run_q->result() as $get){
                return $get->$selColName;
            }
        }
        
        else{
            return FALSE;
        }
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    
    /**
     * 
     * @param type $event
     * @param type $eventRowIdOrRef
     * @param type $eventDesc
     * @param type $eventTable
     * @param type $staffId
     * @return boolean
     */
    public function addevent($event, $eventRowIdOrRef, $eventDesc, $eventTable, $staffId){
        $data = ['event'=>$event, 'eventRowIdOrRef'=>$eventRowIdOrRef, 'eventDesc'=>$eventDesc, 'eventTable'=>$eventTable, 'staffInCharge'=>$staffId];
        
        $this->db->insert('eventlog', $data);
        
        if($this->db->affected_rows() > 0){
            return TRUE;
        }
        
        else{
            return FALSE;
        }
    }
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * 
     * @param type $admin_id
     * @return boolean
     */
    public function get_admin_name($admin_id){
       $q = "SELECT CONCAT_WS(' ', first_name, last_name) as 'name' FROM admin WHERE id = ?";
       
       $run_q = $this->db->query($q, [$admin_id]);
       
       if($run_q->num_rows() > 0){
           foreach($run_q->result_array() as $get){
               return $get['name'];
           }
       }
       
       else{
           return FALSE;
       }
   }
   
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
   
   /**
    * 
    * @param type $id
    * @param type $table_name
    * @return boolean
    */
   public function update_last_seen_time($id, $table_name){
        //set the datetime based on the db driver in use
        $this->db->platform() == "sqlite3" 
                ? 
        $this->db->set('last_seen', "datetime('now')", FALSE) 
                : 
        $this->db->set('last_seen', "NOW()", FALSE);
        
        $this->db->where('id', $id);

        $this->db->update($table_name);

        if($this->db->affected_rows()){
            return TRUE;
        }

        else{
            return FALSE;
        }
    }
    
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    /**
     * 
     * @param type $year
     * @return boolean
     */
    public function getYearEarnings($year=""){
        $year_to_fetch = $year ? $year : date('Y');
		
        if($this->db->platform() == "sqlite3"){
			$q = "SELECT transDate, totalPrice FROM transactions WHERE strftime('%Y', transDate) = '{$year_to_fetch}'";
			
			$run_q = $this->db->query($q);
		}
		
		else{
			$this->db->select('transDate, totalPrice');
			$this->db->where(['YEAR(transDate)'=>$year_to_fetch]);
			$run_q = $this->db->get('transactions');
		}
        
        if($run_q->num_rows()){
            return $run_q->result();
        }
        
        else{
            return FALSE;
        }
    }
    
    
    
    /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */
    
    public function getCategories() {
        $q = "SELECT * FROM categories";
        
        $run_q = $this->db->query($q);
        
        if($run_q->num_rows() > 0){
            return $run_q->result();
        }
        
        else{
            return FALSE;
        }
    }

    public function createDatabase()
    {
        $this->load->dbforge();
        $dbName = '1410inventory';

        // Check if all tables exist
        $this->db->db_select($dbName);
        $tables = ['admin', 'eventlog', 'items', 'lk_sess', 'transactions', 'categories'];
        foreach ($tables as $table) {
            if (!$this->db->table_exists($table)) {
                switch ($table) {
                    case 'admin':
                        $this->createAdminDatabase();
                        break;
                    case 'eventlog':
                        $this->createEventLogDatabase();
                        break;
                    case 'items':
                        $this->createItemsDatabase();
                        break;
                    case 'lk_sess':
                        $this->createLkSessDatabase();
                        break;
                    case 'transactions':
                        $this->createTransactionsDatabase();
                        break;
                    case 'categories':
                        $this->createCategoriesDatabase();
                        break;
                }
            }
        }
    }
    
    public function createAdminDatabase() {
        $this->load->dbforge();

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 3,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'first_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
                'null' => FALSE
            ),
            'last_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
                'null' => FALSE
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => FALSE
            ),
            'mobile1' => array(
                'type' => 'VARCHAR',
                'constraint' => '15',
                'null' => FALSE
            ),
            'mobile2' => array(
                'type' => 'VARCHAR',
                'constraint' => '15',
                'null' => FALSE
            ),
            'password' => array(
                'type' => 'CHAR',
                'constraint' => '60',
                'null' => FALSE
            ),
            'role' => array(
                'type' => 'CHAR',
                'constraint' => '5',
                'null' => FALSE
            ),
            'created_on' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            ),
            'last_login' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            ),
            'last_seen' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            ),
            'last_edited' => array(
                'type' => 'TIMESTAMP',
                'null' => FALSE
            ),
            'account_status' => array(
                'type' => 'CHAR',
                'constraint' => '1',
                'null' => FALSE,
                'default' => '1'
            ),
            'deleted' => array(
                'type' => 'CHAR',
                'constraint' => '1',
                'null' => FALSE,
                'default' => '0'
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('email');
        $this->dbforge->create_table('admin', TRUE);

        // start add first user
        $data = array(
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@gmail.com',
            'mobile1' => '12345678901',
            'mobile2' => '12345678901',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'role' => 'Super',
            'created_on' => date('Y-m-d H:i:s'),
            'last_login' => date('Y-m-d H:i:s'),
            'last_seen' => date('Y-m-d H:i:s'),
            'last_edited' => date('Y-m-d H:i:s')
        );

        $this->db->insert('admin', $data);
        // end add first user
    }
    public function createEventLogDatabase() {
        $this->load->dbforge();

        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'event' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => FALSE
            ),
            'eventRowIdOrRef' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE
            ),
            'eventDesc' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'eventTable' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE
            ),
            'staffInCharge' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'null' => FALSE
            ),
            'eventTime' => array(
                'type' => 'TIMESTAMP',
                'null' => FALSE,
                'default' => date('Y-m-d H:i:s')
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('eventlog', TRUE);

    }
    public function createItemsDatabase() {
        $this->load->dbforge();

        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => FALSE
            ),
            'code' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => FALSE
            ),
            'description' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'unitPrice' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => FALSE
            ),
            'quantity' => array(
                'type' => 'INT',
                'constraint' => 6,
                'null' => FALSE
            ),
            'dateAdded' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            ),
            'lastUpdated' => array(
                'type' => 'TIMESTAMP',
                'null' => FALSE,
                'default' => date('Y-m-d H:i:s'),
                'on update' => date('Y-m-d H:i:s')
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('name');
        $this->dbforge->add_key('code');
        $this->dbforge->create_table('items', TRUE);
    }
    public function createLkSessDatabase() {
        $this->load->dbforge();

        $fields = array(
            'id' => array(
                'type' => 'VARCHAR',
                'constraint' => '40',
                'null' => FALSE
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => FALSE
            ),
            'timestamp' => array(
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
                'default' => 0
            ),
            'data' => array(
                'type' => 'BLOB',
                'null' => FALSE
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('lk_sess', TRUE);
    }
    public function createTransactionsDatabase()
    {
        $this->load->dbforge();

        $fields = array(
            'transId' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'ref' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => FALSE
            ),
            'itemName' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => FALSE
            ),
            'itemCode' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => FALSE
            ),
            'description' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'quantity' => array(
                'type' => 'INT',
                'constraint' => 6,
                'null' => FALSE
            ),
            'unitPrice' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => FALSE
            ),
            'totalPrice' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => FALSE
            ),
            'totalMoneySpent' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => FALSE
            ),
            'amountTendered' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => FALSE
            ),
            'cust_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE
            ),
            'cust_email' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE
            ),
            'transType' => array(
                'type' => 'CHAR',
                'constraint' => '1',
                'null' => FALSE
            ),
            'staffId' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'null' => FALSE
            ),
            'transDate' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            ),
            'lastUpdated' => array(
                'type' => 'TIMESTAMP',
                'null' => FALSE,
                'default' => date('Y-m-d H:i:s'),
                'on update' => date('Y-m-d H:i:s')
            ),
            'cancelled' => array(
                'type' => 'CHAR',
                'constraint' => '1',
                'null' => FALSE,
                'default' => '0'
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('transId', TRUE);
        $this->dbforge->add_key('ref');
        $this->dbforge->create_table('transactions', TRUE);
    }
    public function createCategoriesDatabase() {
        $this->load->dbforge();

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 3,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => FALSE
            ),
            'description' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'dateAdded' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            ),
            'lastUpdated' => array(
                'type' => 'TIMESTAMP',
                'null' => FALSE,
                'default' => date('Y-m-d H:i:s'),
                'on update' => date('Y-m-d H:i:s')
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('name');
        $this->dbforge->create_table('categories', TRUE);
    }
}
