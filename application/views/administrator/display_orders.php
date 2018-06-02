<script src="<?=base_url()?>template/devoops/plugins/dialog/messi.min.js"></script>
<link rel="stylesheet" href="<?=base_url()?>template/devoops/plugins/dialog/messi.min.css" />
<div class="well">
    <div class="row">
	<div class="col-xs-12 col-sm-12">
		<div class="box">
                    <?php
                    if(isset($message)):
                        if($message_type=="error"):
                            ?>
                            <p class="bg-warning"><?=$message?></p>
                            <?php
                        endif;
                    endif;
                    ?>
			
			<div class="box-content">
				<h4 class="page-header">Orders</h4>
				<div class="buttons-area">
					<a href="<?=base_url()?>index.php/orders/import_orders?loc=admin"><div class="btn btn-primary import-orders">Import Orders</div></a>
					<div class="row form-group">
					<div class="col-sm-12">
					Filter by: 
						<select id="filter_by">
							<option>Select</option>
							<option value="3">Unverified</option>
							<option value="1">Verified + Pending</option>
							<option value="2">Verified + Shipped</option>
							<option value="4">Refunded</option>
						</select>
					</div>
				</div>
					</div>
                 <table id="example" class="display" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th style="display:none;">Order ID</th>
			<th>Country</th>
            <th>User ID</th>
            <th>Name</th>            
            <th>Paid Time</th>
			<th>App Status</th> 	
            <th>Shipping Status</th> 			
			<th>Actions</th>
        </tr>
    </thead>
 
    <tfoot>
        <tr>
            <th style="display:none;">Order ID</th>
			<th>Country</th>
            <th>User ID</th>
            <th>Name</th>            
            <th>Paid Time</th>
			<th>App Status</th> 
			<th>Shipping Status</th> 			
			<th>Actions</th>
        </tr>
    </tfoot>
 
    <tbody>
	<?php
	if(is_array($orders )):
	 foreach($orders as $order):
	 ?>
	 <tr>
		   <td style="display:none;"><?=$order->OrderID?></td>
            <td>
			    <div class="shipping-address-information s-a-i-<?=$order->OrderID?>" style="display:none;">
				   <b><?=utf8_encode (ucfirst(strtolower($order->ShippingAddressName)));?></b>
				   <br>
				   <?=utf8_encode (ucfirst(strtolower($order->ShippingAddressStreet)))?> 
				   <br>
				   <?=$order->ShippingAddressPostalCode?>
				   <br>
				   <?=utf8_encode (ucfirst(strtolower($order->ShippingAddressCityName)))?> 
				   <br>
				   <?=utf8_encode (ucfirst(strtolower($order->ShippingAddressStateOrProvince)))?> 
				   <br>
				   <?=utf8_encode (ucfirst(strtolower($order->ShippingAddressCountryName)))?> 
				</div>
			    <img src="<?=base_url()?>template/devoops/img/icon-map.png" class="view-address" style="cursor:pointer" order-id="<?=$order->OrderID?>" title="Display full shipping address"/> 
				<img src="<?=base_url()."country-flags/".$order->ShippingAddressCountry?>.png" title="<?=utf8_encode (ucfirst(strtolower($order->ShippingAddressCountryName)));?>"> 
				<?=$order->ShippingAddressPhone?>
		    </td>
            <td><?=$order->BuyerUserID?></td>
            <td><?=utf8_encode (ucfirst(strtolower($order->ShippingAddressName)));?></td>
            <td><?=$order->PaidTime?></td>
			<td>
			<?php
			$status = false;
			if($order->app_verified=="true"):
			?><div class="btn btn-success">Verified</div><?php
			else:
			
			  ?><div class="btn btn-default">Unverified</div><?php			
			endif;
			?>
			</td>
            <td>
			<?php
			$status = false;
			if($order->refunded=="true"):
			$status = "refunded";
			?><div class="btn btn-danger">Refunded</div><?php
			else:
			 if($order->shipped=="true"):
			 $status = "shipped";
			  ?><div class="btn btn-success" id="button-shipped-<?=$order->OrderID?>">Shipped</div><?php
			 else:
			 $status = "pending";
			  ?><div class="btn btn-default" id="button-pending-<?=$order->OrderID?>">Pending</div><?php
             endif;			 
			endif;
			?>
			
			
			
			</td>
			<td>
			  <div class="btn btn-primary display-transactions"  title="Transactions" order-id="<?=$order->OrderID?>" currency="<?=$order->AmountPaidCurrency?>" shipping-status="<?=$status?>">Transactions</div>
			   <?php	 if($order->refunded=="false"){?>
			  <div class="btn btn-primary delivery"  order-id="<?=$order->OrderID?>" title="It sends keys and invoices for this order transaction">Delivery</div>
			  <?php	 }?>
			  <?php	 if($order->refunded=="false"){?>
				 <div class="btn btn-warning refund" id="refund-<?=$order->OrderID?>" order-id="<?=$order->OrderID?>" title="Refund money on PayPal">Refund</div>	  
			 <?php	 }?>
			 	
             
			</td> 
        </tr>
	 <?php
	 endforeach;
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
	$(document).ready(function()
	{
		$(document).on("click",".view-address",function()
		{
			var order_id = $(this).attr("order-id");
			var shipping_address_information = $(".s-a-i-"+order_id).html();			
			new Messi(shipping_address_information, {title: 'Full Shipping Address', modal: true});
		});

	});
	
	/*$(document).on("click",".import-orders",function()
		{
			alert("in")
		   new Messi('Your request is processing...<br><div class="ajax-loader"></div>', {title: 'Import Orders'});
		   $.post(ajax_url+"orders/import_orders",
			{
				
			},
			function(data, status){
			 new Messi('Orders have been imported successfully.', {title: 'Import Orders'});
			 location.reload();
			}, "json");
				});*/
		$(document).on("click",".refund",function()
		{
			
			var order_id = $(this).attr("order-id");
			Messi.ask('¿Are you sure you want refund money?', function(val) { 
             if(val=="Y")
			 {
				
		        $.post(ajax_url+"orders/refund",
					{
						id_order: order_id
					},
					function(data, status)
					{
						if (typeof data.error != 'undefined') 
						  {							
							new Messi(data.message, {title: 'Refund', titleClass: 'error', buttons: [{id: 0, label: 'Close', val: 'X'}]});
						  }
						  else
						  {
							
							$("#refund-"+order_id).remove();
							$("#button-shipped-"+order_id).removeClass("btn-success");
							$("#button-shipped-"+order_id).addClass("btn-danger");
							$("#button-shipped-"+order_id).text("refunded");
							
							$("#button-pending-"+order_id).removeClass("btn-success");
							$("#button-pending-"+order_id).addClass("btn-danger");
							$("#button-pending-"+order_id).text("refunded");
							
							new Messi(data.message, {title: 'Refund', titleClass: 'success', buttons: [{id: 0, label: 'Close', val: 'X'}]});	  
						  }						
					}, "json"); 		
			 }
			});				
	    });	
	$(document).on("click",".display-transactions",function()
	{
		var order_id = $(this).attr("order-id");
		var currency = $(this).attr("currency");
		var shipping_status = $(this).attr("shipping-status");
		$.post(ajax_url+"administrator/orders/get_transactions_html",
		{
			id_order: order_id,
			currency: currency,
			shipping_status: shipping_status
		},
		function(data, status){
			new Messi(data.orders_transactions_html, {title: 'Transactions', modal: true});
		}, "json");		
    });
$("#filter_by").on("change",function(){
	    var val = $(this).val();
		window.location.replace(ajax_url+"administrator/orders/?filter="+val);
	
});


$(document).on("click",".delivery",function()
	{
		var order_id = $(this).attr("order-id");
    $.post(ajax_url+"administrator/orders/delivery",
    {
        id: order_id
    },
    function(data, status)
	{
		if (typeof data.error != 'undefined') 
		  {
			   new Messi(data.message, {title: 'Delivery', titleClass: 'error', buttons: [{id: 0, label: 'Close', val: 'X'}]});
          }
		  else
		  {
			if (typeof data.action != 'undefined') 
			{
				  $.post(ajax_url+"orders/"+data.action,
					{
						id_order: order_id
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
							}
							else
							{
							  $("#button-pending-"+order_id).text("shipped");
							  $("#button-pending-"+order_id).removeClass("btn-default");
							  $("#button-pending-"+order_id).addClass("btn-success");
							  new Messi(data.message, {title: 'Delivery', titleClass: 'success', buttons: [{id: 0, label: 'Close', val: 'X'}]});	  	
							}
							
						  }
						
					}, "json");
});
</script>