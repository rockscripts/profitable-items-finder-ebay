
                 <table border="1" class="custom-table" id="example1" class="display" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th style="display:none;">Order ID</th>
			<th>Title</th>
            <th>Qty</th>
            <th>Price</th> 
            <th>Key</th>			
        </tr>
    </thead>
    <tbody>
	<?php
	if(is_array($order_transactions )):
	 foreach($order_transactions as $transaction):
	 ?>
	 <tr>
		   <td style="display:none;"><?=$transaction->OrderID?></td>
           <td><?=utf8_encode (ucfirst(strtolower($transaction->Title)))?></td>
           <td><?=$transaction->QuantityPurchased?></td>
		   <td><?=$transaction->TransactionPrice?> <span class="currency"><?=$currency?></span></td>
		   <td>
		   <?php
			if($shipping_status=="refunded"):
			?><div class="btn btn-danger">Refunded</div><?php
			else:
			 if($shipping_status=="shipped"):
			 //echo "<pre>";
			// print_r($orders_transaction_keys);
			  if(is_array($orders_transaction_keys )):
			   $index = 0;
			    for($i=0;$i<sizeof($orders_transaction_keys);$i++):
				 $keys = $orders_transaction_keys[$i]["Keys"];
				 if(is_array($keys)):
		          if(sizeof($keys)>0):		
				   foreach($keys as $key):
                    ?>
                         <div style="border:2px solid #eee;margin:2px;padding:2px;"><b><?php echo $key->key; ?></b></div>
                   <?php
                    endforeach;				   
				  endif;
				 endif;		   
				endfor;
			  endif;
			 else:
			  ?><div class="btn btn-default">Pending</div><?php
             endif;			 
			endif;
			?>
		   </td>
        </tr>
	 <?php
	 endforeach;
	 endif;
	?>
    </tbody>
</table>
	
<style>
    .custom-table th,.custom-table td 
	{
	 border:1px solid #eee;
	 padding:5px;
	}
	.currency
	{
		font-size:11px;
	}
</style>