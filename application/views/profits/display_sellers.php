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
			
				<h4 class="page-header">Sellers List</h4>
				<b>Import Listings By:  </b><span id="init_import" class='btn btn-primary button-top'>Seller</span> <span id="init_import" class='btn btn-info button-top'>Category</span>
			
					</div>
				<div>				
				</div>
                 <table id="example" class="display" cellspacing="0" width="100%">
    <thead>
        <tr>
           <th style="display:none">ID</th>
		   <th>			
				<!--<input type="checkbox" class="check-all"> -->
		    </th>
            <th>Seller</th>
            <th>Actions</th>			
        </tr>
    </thead>
 
    <tfoot>
        <tr>
            <th style="display:none">ID</th>
			<th>			
				<!--<input type="checkbox"  class="check-all" > -->
				
		    </th>
            <th>Seller</th>
            <th>Actions</th>			
        </tr>
    </tfoot> 
    <tbody>
	<?php
	if(is_array($profitable_sellers )):
	 foreach($profitable_sellers as $profitable_seller):
	 ?>
	 <tr class="row-<?=$profitable_seller->userID?>">
	 <td style="display:none">
		<?=$profitable_seller->userID?>
	</td>
	<td>
		<input type="checkbox"  class="check-item" seller-id="<?=$profitable_seller->userID?>"> 
	</td>		   
	<td  class="userID-<?=$profitable_seller->userID?>">
		<a href="https://www.ebay.com/usr/<?=$profitable_seller->userID?>" target="_blank"><?=$profitable_seller->userID?></a>
	</td>  
	<td>
		<a href="<?=base_url()?>profit?sellerID=<?=$profitable_seller->userID?>">
		 <img src="<?=base_url()?>template/devoops/img/analytics.png" class="icons-owner" title='Profitable Items'/>
		</a>
		<a href="<?=base_url()?>profit?sellerID=<?=$profitable_seller->userID?>">
		 <img src="<?=base_url()?>template/devoops/img/x-button.png" class="icons-owner" title='Remove User'/>
        </a>
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

<style>
.fees-table td
{
	padding:4px;
	border:1px solid:#ccc;
}
</style>
<script>


	var table = $('#example').DataTable({
   'aoColumnDefs': [{
        'bSortable': false,
        'aTargets': [0,1] /* 1st one, start by the right */
    }]
});	
$(document).on("click","#init_import",function(){
	var page = $("#pages").val();
			   if(page<=0)
			   page = 1;
			   
			   $.post(ajax_url+"profit/get_import_form",
				{page:page,display_items:"true"},
				function(data, status){
					new Messi(data.import_form, {title: 'Import items with profits', modal: true});
				}, "json");
});
</script>