<?php defined('BASEPATH') OR exit('') ?>

<?= isset($range) && !empty($range) ? $range : ""; ?>
<div class="panel panel-primary">
    <!-- Default panel contents -->
    <div class="panel-heading">TRANSACTIONS</div>
    <?php if($allTransactions): ?>
    <div class="table table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Receipt No</th>
                    <th>Total Items</th>
                    <th>Items</th>
                    <th>Total Amount</th>
                    <th>Amount Tendered</th>
                    <th>Change Due</th>
                    <th>Staff</th>
                    <th>Customer</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($allTransactions as $get): ?>
                <tr>
                    <th><?= $sn ?>.</th>
                    <td><a class="pointer vtr" title="Click to view receipt"><?= $get->ref ?></a></td>
                    <td><?= $get->quantity ?></td>
                    <td>
                        <?php
                        $items = $this->transaction->getTransInfo($get->ref);
                        $itemNames = [];
                                                 
                        foreach($items as $item){
                            $itemNames[] = $item['itemName'];
                        }
                        
                        echo implode(", ", array_map(function($item, $count) {
                            return "(" . $count . "x) " . $item;
                        }, $itemNames, array_column($items, 'quantity')));
                        ?>
                    </td>
                    <td>₱<?= number_format($get->totalMoneySpent, 2) ?></td>
                    <td>₱<?= number_format($get->amountTendered, 2) ?></td>
                    <td>₱<?= number_format($get->changeDue, 2) ?></td>
                    <td><?=$get->staffName?></td>
                    <td><?=$get->cust_name?> - <?=$get->cust_email?></td>
                    <td><?= date('jS M, Y h:ia', strtotime($get->transDate)) ?></td>
                </tr>
                <?php $sn++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<!-- table div end-->
    <?php else: ?>
        <br/>
        <ul><li>No Transactions</li></ul>
    <?php endif; ?>
    
    <!--Pagination div-->
    <div class="col-sm-12 text-center">
        <ul class="pagination">
            <?= isset($links) ? $links : "" ?>
        </ul>
    </div>
</div>
<!-- panel end-->
