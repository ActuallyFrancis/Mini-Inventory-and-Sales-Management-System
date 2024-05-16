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
    
    public function get($categoryId){
        return $this->db->where('id', $categoryId)->get('category')->row();
    }
    
    public function getCategories(){
        return $this->db->get('category')->result();
    }
}