<?php
defined('BASEPATH') OR exit('');
?>

<div class="pwell hidden-print"> 
    <div class="row">
        <div class="col-sm-6">
            <a href="<?=base_url()?>misc/dldb" download="1410inventory.sqlite"><button class="btn btn-primary">Download Data</button></a>
        </div>

        <br class="visible-xs">
        
        <div class="col-sm-6">
            <button class="btn btn-info" id="importdb">Import CSV Data</button>
            <input type="file" id="selecteddbfile" class="hidden" accept=".csv">
            <span class="help-block" id="dbFileMsg"></span>
        </div>

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
</div>