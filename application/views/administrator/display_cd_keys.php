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
				<h4 class="page-header">CD-Keys for <?=$item_digital_title?></h4>
				<div>
				<div class="btn btn-primary add-digital-item">Add CD-Key(s)</div>
				</div>
                 <table id="example" class="display" cellspacing="0" width="100%">
    <thead>
        <tr>
           <th>ID</th>
            <th>CD-KEY</th>
			<th>Status</th>
            <th>Actions</th>			
        </tr>
    </thead>
 
    <tfoot>
        <tr>
            <th>ID</th>
            <th>CD-KEY</th>  
			<th>Status</th>
			<th>Actions</th>			
        </tr>
    </tfoot>
 
    <tbody>
	<?php
	if(is_array($cd_keys )):
	 foreach($cd_keys as $cd_key):
	 ?>
	 <tr class="row-<?=$cd_key->id_cd_key?>">
		   <td ><?=$cd_key->id_cd_key?></td>
            <td><?=nl2br($cd_key->key)?></td>  
			  <td>
			<?php
			if($cd_key->sold=="false"):
			?>
			<div class="btn btn-default" title="Remove this item" id_cd_key="<?=$cd_key->id_cd_key?>">Unsold</div>	
			<?php
			else:
			?>
			<div class="btn btn-success" title="Remove this item" id_cd_key="<?=$cd_key->id_cd_key?>">Sold</div>	
			<?php
			endif;
			?> 
					
			</td>
            <td>
			<div class="btn btn-danger remove-cd-key-single" title="Remove this item" id_cd_key="<?=$cd_key->id_cd_key?>">Remove</div>			
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
			var di = "<?php echo $di;?>";
			var is_upload_pack = $("#is_upload_pack").val();
			$.post(ajax_url+"administrator/DigitalItems/get_cd_key_to_add_html",
    {
       di:di,
	   is_upload_pack:is_upload_pack
    },
    function(data, status){
		new Messi(data.cd_key_to_add_html, {title: 'New CD-Key(s)', modal: true});
    }, "json");
		});
	$(document).on("click",".remove-cd-key-single",function()
		{
			var id_cd_key = $(this).attr("id_cd_key");
			Messi.ask('Are you sure you want remove this item?', function(val) { 
             if(val=="Y")
			 {
				 		$.post(ajax_url+"administrator/DigitalItems/remove_cd_key_single",
						{   
						  id_cd_key:id_cd_key    
						},
						function(data, status)
						{
					     $(".row-"+id_cd_key).remove();
						new Messi('CD-KEY has been removed successfully.', {title: 'Success', titleClass: 'success', buttons: [{id: 0, label: 'Close', val: 'X'}]});	
						}, "json");
			 }
			});
	
		});
</script>