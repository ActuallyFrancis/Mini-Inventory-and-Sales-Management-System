<?php
defined('BASEPATH') OR exit('');
?>

<div class="pwell hidden-print">   
    <div class="row">
        <div class="col-sm-12">
            <!-- sort and co row-->
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-2 form-inline form-group-sm" style="display:flex;flex-direction:row">
                        <button class="btn btn-primary btn-sm" id='createItem' style="margin-right: 5px">Add New Item</button>
                        <button class="btn btn-primary btn-sm" id='createCategory'>Add Category</button>
                    </div>

                    <div class="col-sm-3 form-inline form-group-sm">
                        <label for="itemsListPerPage">Show</label>
                        <select id="itemsListPerPage" class="form-control">
                            <option value="1">1</option>
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label>per page</label>
                    </div>

                    <div class="col-sm-4 form-group-sm form-inline">
                        <label for="itemsListSortBy">Sort by</label>
                        <select id="itemsListSortBy" class="form-control">
                            <option value="name-ASC">Item Name (A-Z)</option>
                            <option value="unitPrice-DESC">Unit Price (Highest first)</option>
                            <option value="quantity-DESC">Quantity (Highest first)</option>
                            <option value="name-DESC">Item Name (Z-A)</option>
                            <option value="unitPrice-ASC">Unit Price (lowest first)</option>
                            <option value="quantity-ASC">Quantity (lowest first)</option>
                        </select>

                            <label for="transListCategory">Category</label>
                            <select id="transListCategory" class="form-control">
                                <option value="-1">All</option>
                                <?php if(isset($categories) && !empty($categories)): ?>
                                    <?php foreach($categories as $get): ?>
                                        <option value="<?=$get->id?>"><?=$get->name?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                    </div>

                    <div class="col-sm-3 form-inline form-group-sm">
                        <label for='itemSearch'><i class="fa fa-search"></i></label>
                        <input type="search" id="itemSearch" class="form-control" placeholder="Search Items">
                    </div>
                </div>
            </div>
            <!-- end of sort and co div-->
        </div>
    </div>
    
    <hr>
    
    <!-- row of adding new item form and items list table-->
    <div class="row">
        <div class="col-sm-12">
            <!--Form to add/update an item-->
            <div class="col-sm-4 hidden" id='createNewItemDiv'>
                <div class="well">
                    <div style="display: flex;">
                        <h4>Add New Item</h4>
                        <button class="close cancelAddItem" style="text-align:end;flex:auto;">&times;</button><br>
                    </div>
                    <form name="addNewItemForm" id="addNewItemForm" role="form">
                        <div class="text-center errMsg" id='addCustErrMsg'></div>
                        
                        <br>
                        
                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="itemName">Item Name</label>
                                <input type="text" id="itemName" name="itemName" placeholder="Item Name" maxlength="80"
                                    class="form-control" onchange="checkField(this.value, 'itemNameErr')">
                                <span class="help-block errMsg" id="itemNameErr"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="itemCategory">Item Category</label>
                                <?php if (!empty($categories)): ?>
                                    <select id="itemCategory" name="itemCategory" class="form-control">
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category->id ?>"><?= $category->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <select id="itemCategory" name="itemCategory" class="form-control" disabled>
                                        <option>No categories available</option>
                                    </select>
                                <?php endif; ?>
                                <span class="help-block errMsg" id="itemCategoryErr"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="itemQuantity">Quantity</label>
                                <input type="number" id="itemQuantity" name="itemQuantity" placeholder="Available Quantity"
                                    class="form-control" min="0" onchange="checkField(this.value, 'itemQuantityErr')">
                                <span class="help-block errMsg" id="itemQuantityErr"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="unitPrice">(₱)Unit Price</label>
                                <input type="text" id="itemPrice" name="itemPrice" placeholder="(₱)Unit Price" class="form-control"
                                    onchange="checkField(this.value, 'itemPriceErr')">
                                <span class="help-block errMsg" id="itemPriceErr"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="itemDescription" class="">Description (Optional)</label>
                                <textarea class="form-control" id="itemDescription" name="itemDescription" rows='4'
                                    placeholder="Optional Item Description"></textarea>
                            </div>
                        </div>
                        <br>
                        <div class="row text-center">
                            <div class="col-sm-6 form-group-sm">
                                <button class="btn btn-primary btn-sm" id="addNewItem">Add Item</button>
                            </div>

                            <div class="col-sm-6 form-group-sm">
                                <button type="reset" id="cancelAddItem" class="btn btn-danger btn-sm cancelAddItem" form='addNewItemForm'>Cancel</button>
                            </div>
                        </div>
                    </form><!-- end of form-->
                </div>
            </div>

            <!--Form to add new category-->
            <div class="col-sm-4 hidden" id='createNewCategoryDiv'>
                <div class="well">
                    <div style="display: flex;">
                        <h4>Add Category</h4>
                        <button class="close cancelAddItem" style="text-align:end;flex:auto;">&times;</button><br>
                    </div>
                    <form name="addNewCategoryForm" id="addNewCategoryForm" role="form">
                        <div class="text-center errMsg" id='addCategoryErrMsg'></div>
                        <br>
                        <div class="row">
                            <div class="col-sm-12 form-group-sm">
                                <label for="categoryName">Category Name</label>
                                <input type="text" id="categoryName" name="categoryName" placeholder="Category Name" maxlength="80"
                                       class="form-control" onchange="checkField(this.value, 'categoryNameErr')">
                                <span class="help-block errMsg" id="categoryNameErr"></span>
                                
                                <label for="categoryDesc">Category Description (optional)</label>
                                <textarea class="form-control" id="categoryDesc" name="categoryDesc" rows='4'
                                    placeholder="Category Description"></textarea>
                                
                            </div>
                        </div>

                        <br>
                        <div class="row text-center">
                            <div class="col-sm-6 form-group-sm">
                                <button class="btn btn-primary btn-sm" id="addNewCategory">Add Category</button>
                            </div>

                            <div class="col-sm-6 form-group-sm">
                                <button type="reset" id="cancelAddCategory" class="btn btn-danger btn-sm cancelAddCategory" form='addNewCategoryForm'>Cancel</button>
                            </div>
                        </div>
                    </form><!-- end of form-->
                </div>
            </div>
            
            
            <!--- Item list div-->
            <div class="col-sm-12" id="itemsListDiv">
                <!-- Item list Table-->
                <div class="row">
                    <div class="col-sm-12" id="itemsListTable"></div>
                </div>
                <!--end of table-->
            </div>
            <!--- End of item list div-->

        </div>
    </div>
    <!-- End of row of adding new item form and items list table-->
</div>

<!--modal to update stock-->
<div id="updateStockModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">&times;</button>
                <h4 class="text-center">Update Stock</h4>
                <div id="stockUpdateFMsg" class="text-center"></div>
            </div>
            <div class="modal-body">
                <form name="updateStockForm" id="updateStockForm" role="form">
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label>Item Name</label>
                            <input type="text" readonly id="stockUpdateItemName" class="form-control">
                        </div>
                        
                        <div class="col-sm-4 form-group-sm">
                            <label>Quantity in Stock</label>
                            <input type="text" readonly id="stockUpdateItemQInStock" class="form-control">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-6 form-group-sm">
                            <label for="stockUpdateType">Update Type</label>
                            <select id="stockUpdateType" class="form-control checkField">
                                <option value="">---</option>
                                <option value="newStock">Add Stock</option>
                                <option value="deficit">Reduce Stock</option>
                            </select>
                            <span class="help-block errMsg" id="stockUpdateTypeErr"></span>
                        </div>
                        
                        <div class="col-sm-6 form-group-sm">
                            <label for="stockUpdateQuantity">Quantity</label>
                            <input type="number" id="stockUpdateQuantity" placeholder="Update Quantity"
                                class="form-control checkField" min="0">
                            <span class="help-block errMsg" id="stockUpdateQuantityErr"></span>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-12 form-group-sm">
                            <label for="stockUpdateDescription" class="">Description</label>
                            <textarea class="form-control checkField" id="stockUpdateDescription" placeholder="Update Description"></textarea>
                            <span class="help-block errMsg" id="stockUpdateDescriptionErr"></span>
                        </div>
                    </div>
                    
                    <input type="hidden" id="stockUpdateItemId">
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="stockUpdateSubmit">Update</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!--end of modal-->

<!--modal to edit item-->
<div id="editItemModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">&times;</button>
                <h4 class="text-center">Edit Item</h4>
                <div id="editItemFMsg" class="text-center"></div>
            </div>
            <div class="modal-body">
                <form role="form">
                    <div class="row">
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemNameEdit">Item Name</label>
                            <input type="text" id="itemNameEdit" placeholder="Item Name" autofocus class="form-control checkField">
                            <span class="help-block errMsg" id="itemNameEditErr"></span>
                        </div>
                        
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemQuantityEdit">Quantity</label>
                            <input type="number" id="itemQuantityEdit" placeholder="Available Quantity" class="form-control checkField" min="0">
                            <span class="help-block errMsg" id="itemQuantityEditErr"></span>
                        </div>
                        
                        <div class="col-sm-4 form-group-sm">
                            <label for="unitPrice">Unit Price</label>
                            <input type="text" id="itemPriceEdit" name="itemPrice" placeholder="Unit Price" class="form-control checkField">
                            <span class="help-block errMsg" id="itemPriceEditErr"></span>
                        </div>
                        
                        <!-- Category dropdown -->
                        <div class="col-sm-4 form-group-sm">
                            <label for="itemCategoryEdit">Item Category</label>
                            <?php if (!empty($categories)): ?>
                                <select id="itemCategoryEdit" class="form-control checkField">
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category->id ?>"><?= $category->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <select id="itemCategoryEdit" class="form-control checkField" disabled>
                                    <option>No categories available</option>
                                </select>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-12 form-group-sm">
                            <label for="itemDescriptionEdit" class="">Description (Optional)</label>
                            <textarea class="form-control" id="itemDescriptionEdit" placeholder="Optional Item Description"></textarea>
                        </div>
                    </div>
                    <input type="hidden" id="itemIdEdit">
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="editItemSubmit">Save</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!--end of modal-->
<script src="<?=base_url()?>public/js/items.js"></script>