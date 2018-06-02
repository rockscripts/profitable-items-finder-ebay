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
				
                                    <?php
                                    $attributes = array('class' => 'form-horizontal', 'id' => 'myform', 'role'=>"form");
                                    echo form_open_multipart('administrator/DigitalItems/add_key', $attributes);
                                    ?>
                    
						<div class="form-group upload-group" >
						<label class="col-sm-2 control-label">Upload</label>
						<div class="col-sm-4">
                              <input type="file" name="txt_file" size="20" />     <br>
                              						  
						</div>                                              
					</div>	
					<div class="form-group pack-area-group" style="display:none;">
						<div class="col-sm-4">
                              <textarea name="pack_keys"></textarea>     <br>
                              						  
						</div>                                              
					</div>	
					<div class="form-group" >
					<div class="col-sm-4">
						<div class="checkbox">
							<label>
								<input type="checkbox" is-checked="false"  name="is_upload_pack" id="is_upload_pack"> Upload Pack
								<i class="fa fa-square-o"></i>
							</label>
						</div>						
					</div>
					</div>		
					<div class="form-group" >
					<div class="col-sm-4" style="width: 100%;">
					<span style="font-size:10px;">Upload Cd-Keys in txt file.</span>	
					</div>
					</div>
					<input type="hidden" name="id_digital_item" value="<?=$id_digital_item?>" />   
						<?php
						 echo form_submit('mysubmit', 'Submit'); 
						?>
						
				</form>
			</div>
		</div>
	</div>
</div>
</div> 
<script>
$(document).on("click","#is_upload_pack",function()
		{
			
			var ischecked = $(this).attr("is-checked");
			if(ischecked=="false")
			{
				$(this).attr("is-checked","true");
			   $(".upload-group").hide();	
		       $(".pack-area-group").show();	
			}
			else
			{
			   $(this).attr("is-checked","false");
			   $(".upload-group").show();	
		       $(".pack-area-group").hide();
			}
			
		});
</script>