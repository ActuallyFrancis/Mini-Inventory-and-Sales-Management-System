<?php
defined('BASEPATH') or exit('');

/**
 * Description of Misc
 * Do not check login status in the constructor of this class and some functions are to be accessed even without logging in
 *
 * @author Amir <amirsanni@gmail.com>
 * date 17th Feb. 2016
 */
class Misc extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
  }

  public function totalEarnedToday()
  {
    $this->genlib->checkLogin();

    $this->genlib->ajaxOnly();

    $this->load->model('transaction');

    $total_earned_today = $this->transaction->totalEarnedToday();

    $json['totalEarnedToday'] = $total_earned_today ? number_format($total_earned_today, 2) : "0.00";

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }

  /**
   * check if admin's session is still on
   */
  public function check_session_status()
  {
    if (isset($_SESSION['admin_id']) && ($_SESSION['admin_id'] !== false) && ($_SESSION['admin_id'] !== "")) {
      $json['status'] = 1;

      //update user's last seen time
      //update_last_seen_time($id, $table_name)
      $this->genmod->update_last_seen_time($_SESSION['admin_id'], 'admin');
    } else {
      $json['status'] = 0;
    }

    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }

  public function dbmanagement()
  {
    $this->genlib->checkLogin();

    $this->genlib->superOnly();

    $data['pageContent'] = $this->load->view('dbbackup', '', TRUE);
    $data['pageTitle'] = "Database";

    $this->load->view('main', $data);
  }

  public function dldb()
  {
    $this->genlib->checkLogin();

    $this->genlib->superOnly();
    
    // Export the items table to a CSV file, not the sqlite file
    $this->load->dbutil();
    $this->load->helper('file');
    
    $delimiter = ",";
    $newline = "\r\n";
    $enclosure = '"';
    
      // Join the items table with the item_category table
      $this->db->select('items.name as "Item Name", category.name as "Item Category", items.quantity as "Quantity", items.unitPrice as "Unit Price", items.description as "Description"');
      $this->db->from('items');
      $this->db->join('category', 'items.category = category.id');
      $query = $this->db->get();

      // Generate CSV data from the query result
      $data = $this->dbutil->csv_from_result($query, $delimiter, $newline, $enclosure);

      // Define the file path
      $file_path = BASEPATH . "sqlite/1410inventory.csv";

      // Write the data to the file
      write_file($file_path, $data);

      // Output the CSV file with appropriate headers
      $this->output->set_content_type('application/csv')->set_output(file_get_contents($file_path));
  }
  

  /**
   * 
   */
  public function importdb()
  {
    $this->genlib->checkLogin();
    $this->genlib->superOnly();

    //create a copy of the db file currently in the sqlite dir for keep in case something go wrong
    /*if (file_exists(BASEPATH . "sqlite/1410inventory.sqlite")) {
      copy(BASEPATH . "sqlite/1410inventory.sqlite", BASEPATH . "sqlite/backups/" . time() . ".sqlite");
    }*/

    $config['upload_path'] = BASEPATH . "sqlite/"; //db files are stored in the basepath
    $config['allowed_types'] = 'csv|txt';
    $config['file_ext_tolower'] = TRUE;
    $config['file_name'] = "import.csv";
    $config['max_size'] = 2000; //in kb
    $config['overwrite'] = TRUE; //overwrite the previous file

    $this->load->library('upload', $config); //load CI's 'upload' library
    $this->upload->initialize($config, TRUE);

    if ($this->upload->do_upload('dbfile') == FALSE) {
      $json['msg'] = $this->upload->display_errors();
      $json['status'] = 0;
    } else {
      $json['status'] = 1;
    }
    
    // Open the file for reading
    $file = fopen(BASEPATH . "sqlite/import.csv", 'r');
    
    $firstItem = true;
    $this->load->model('Category');
    
    while (($data = fgetcsv($file)) !== FALSE) {

        log_message("info", "Extracting data from CSV");

        // Split the line into an array of values
        $itemName = $data[0];
        $itemCategory = $data[1];
        $quantity = $data[2];
        $unitPrice = str_replace(",", "", $data[3]); // remove commas in price
        $description = $data[4];
        
        // Check if the format is being followed
        if (count($data) != 5) {
            $json['msg'] = "Invalid CSV file format, must follow the following format: Item Name, Item Category, Quantity, Unit Price, Description";
            $json['status'] = 0;
            $this->output->set_content_type('application/json')->set_output(json_encode($json));
            return;
        }
        
        if ($firstItem) {
            // Check if the first item is the header
            if ($itemName == "Item Name" && $itemCategory == "Item Category" && $quantity == "Quantity" && $unitPrice == "Unit Price" && $description == "Description") {
                $firstItem = false;
                continue;
            } else {
                $json['msg'] = "Invalid CSV file format, must follow the following format: Item Name, Item Category, Quantity, Unit Price, Description";
                $json['status'] = 0;
                $this->output->set_content_type('application/json')->set_output(json_encode($json));
                return;
            }
        }
        
        $category = $this->Category->get($itemCategory);

        if (!$category) {
            $this->Category->add($itemCategory, ''); // Assuming the category description is empty
            $category = $this->Category->get($itemCategory);
        }

        // Insert the data into the database
        $this->db->insert('items', [
            'name' => $itemName,
            'category' => $category->id, // Use the id of the category
            'quantity' => $quantity,
            'unitPrice' => $unitPrice,
            'description' => $description
        ]);
    }
    
    log_message('info', 'Imported data from CSV file');
    
    // Close the file
    fclose($file);

    //set final output
    $this->output->set_content_type('application/json')->set_output(json_encode($json));
  }
}
