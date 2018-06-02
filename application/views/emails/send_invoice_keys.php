<p>
 If you have keys with pending status, they will be updated between 12 hours and you can come back with <a href="<?=$shipping_link?>">link</a> sent to your eBay message dashboard.
</p>   
<p>
<b>Order Date:</b> <?=$date_placed?> 
</p>
   <table border="1" class="custom-table" id="example" class="display" cellspacing="0" width="100%">
    <thead>
        <tr>
         
			<th>Title</th>
            <th>key(s)</th>			
        </tr>
    </thead>
    <tbody>
	<?php
	
	if(is_array($orders_transaction_keys )):
	$index = 0;
	 for($i=0;$i<sizeof($orders_transaction_keys);$i++):
	 ?>
	 <tr>
		  
           <td><?=utf8_encode (ucfirst(strtolower(str_replace("?","-", $orders_transaction_keys[$index]["Title"]))))?></td>
          <td>
		  <?php
		   //*Display keys*/
		   $keys = $orders_transaction_keys[$index]["Keys"];
		   if(is_array($keys )):
		   if(sizeof($keys)>0):		   
			   foreach($keys as $key):
			   ?>
			   <div style="border:2px solid #eee;margin:2px;padding:2px;"><b><?php echo nl2br($key->key); ?></b></div>
			   <?php
			   endforeach;
			   else:
			   ?>
			   <div style="color: #525252;background-color: #b8b8b8;border-color: rgba(0, 0, 0, 0.3);padding:3px;text-align:center;border:1px solid #ccc;">Pending</div>
			   <?php
		   endif;
		   endif;
		  ?>
		   </td>
        </tr>
	 <?php
	 $index++;
	 endfor;
	 else:
	?>
	<tr>
	 <td colspan="3">No items to display.</td>
	</tr>
	<?php
	 endif;
	?>
    </tbody>
</table>
<style>
.btn-default {
    color: #525252;
    background-color: #b8b8b8;
    border-color: rgba(0, 0, 0, 0.3);
}
</style>