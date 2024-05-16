<?php
defined('BASEPATH') OR exit('');

class Category extends CI_Model{
    public function __construct(){
        parent::__construct();
    }

    public function add($categoryName, $categoryDescription){
        $data = ['name'=>$categoryName, 'description'=>$categoryDescription, 'dateAdded' => date('Y-m-d H:i:s')];
        $this->db->insert('category', $data);

        return TRUE;
    }


    public function edit($categoryId, $categoryName, $categoryDescription){
        $data = ['name'=>$categoryName, 'description'=>$categoryDescription];
        $this->db->where('id', $categoryId)->update('category', $data);
        
        return TRUE;
    }
    
    public function get($categoryName){
        // Convert the input category name to lowercase
        $categoryName = strtolower($categoryName);

        // Find the category name in the database, case-insensitive
        $this->db->where('LOWER(name)', $categoryName);
        $query = $this->db->get('category');

        // If a category is found, return it. Otherwise, return null.
        return $query->num_rows() > 0 ? $query->row() : null;
    }
    
    public function getCategories(){
        return $this->db->get('category')->result();
    }
}