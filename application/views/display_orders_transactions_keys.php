<script src="<?=base_url()?>template/devoops/plugins/dialog/messi.min.js"></script>
<link rel="stylesheet" href="<?=base_url()?>template/devoops/plugins/dialog/messi.min.css" />
<div class="well">
    <div class="row">
	<div class="col-xs-12 col-sm-12">
		<div class="box">
                    <?php
                    if(isset($message)):
                            ?>
                            <p class="bg-success"><?=$message?></p>
                            <?php
                    endif;
                    ?>
					
					
			
			<div class="box-content">
				<h4 class="page-header">Order Transactions keys</h4>
				<div class="buttons-area">
					Send to your eMail:  <div class="btn btn-primary send-invoice-and-keys" order-id="<?=$id?>"><!--Invoice and -->Keys</div>
					</div>
				<p>
					 If you have keys with pending status, they will be updated between 12 hours and you can come back with link sent to your eBay message dashboard.
					</p>
                               <table border="1" class="custom-table" id="example" class="display" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th style="display:none;">Item ID</th>
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
		   <td style="display:none;"><?=$orders_transaction_keys[$index]["ItemID"]?></td>
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
			   <div class="btn btn-default">Pending</div>
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
			</div>
		</div>
	</div>
</div>
</div> 
                 
	<script>
    $('#example').DataTable();
	
	$(document).on("click",".send-invoice-and-keys",function()
	{
		var order_id = $(this).attr("order-id");
    $.post(ajax_url+"orders/send_invoice_keys_to_buyer",
    {
        id_order: order_id,
		is_json:"true"
    },
    function(data, status)
	{
		if (typeof data.error != 'undefined') 
		  {
			   new Messi(data.message, {title: 'Order Details to eMail', titleClass: 'error', buttons: [{id: 0, label: 'Close', val: 'X'}]});
          }
		  else
		  {
		    new Messi(data.message, {title: 'Order Details to eMail', titleClass: 'success', buttons: [{id: 0, label: 'Close', val: 'X'}]});	  
		  }
		
    }, "json");
});
</script>
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