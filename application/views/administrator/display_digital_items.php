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
							else:
							?>
                            <p class="bg-success"><?=$message?></p>
                            <?php
                        endif;
                    endif;
                    ?>
			
			<div class="box-content">
				<h4 class="page-header">Digital Items</h4>
				<div>
				<div class="btn btn-primary add-digital-item">Add Digital Item</div>
				</div>
                 <table id="example" class="display" cellspacing="0" width="100%">
    <thead>
        <tr>
           <th>ID</th>
            <th>Title</th>
            <th>Actions</th>			
        </tr>
    </thead>
 
    <tfoot>
        <tr>
            <th>ID</th>
            <th>Title</th>  
			<th>Actions</th>			
        </tr>
    </tfoot>
 
    <tbody>
	<?php
	if(is_array($digital_items )):
	 foreach($digital_items as $digital_items):
	 ?>
	 <tr class="row-<?=$digital_items->id_digital_item?>">
		   <td ><?=$digital_items->id_digital_item?></td>
            <td><?=$digital_items->title?></td>  
            <td>
			<div class="btn btn-danger remove-digital-item" title="Remove this item" id_digital_item="<?=$digital_items->id_digital_item?>">Remove</div>
			<a href="<?=base_url()?>index.php/administrator/DigitalItems/cdkeys?di=<?=$digital_items->id_digital_item?>" class="btn btn-primary" title="Display CD-Keys for this item">CD-Keys</a>
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
	
	$(document).on("click",".add-digital-item",function()
		{
			$.post(ajax_url+"administrator/DigitalItems/get_digital_item_to_add_html",
    {
       
    },
    function(data, status){
		new Messi(data.digital_item_to_add_html, {title: 'New Digital Item', modal: true});
    }, "json");
		});
	$(document).on("click",".remove-digital-item",function()
		{
			var id_digital_item = $(this).attr("id_digital_item");
			Messi.ask('Are you sure you want remove this item?', function(val) { 
             if(val=="Y")
			 {
				 		$.post(ajax_url+"administrator/DigitalItems/remove",
						{   
						  id_digital_item:id_digital_item    
						},
						function(data, status)
						{
					     $(".row-"+id_digital_item).remove();
						new Messi('Digital Items has been removed successfully.', {title: 'Success', titleClass: 'success', buttons: [{id: 0, label: 'Close', val: 'X'}]});	
						}, "json");
			 }
			});
	
		});
</script>