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
                                    echo form_open('administrator/DigitalItems/add', $attributes);
                                    ?>
                                
					<div class="form-group" >
                                            
						<label class="col-sm-2 control-label">Title</label>
						<div class="col-sm-4">
                                                    <input name="digital_item_title" id="digital_item_title" type="text" class="form-control" placeholder="eg. Mario Bross..." data-toggle="tooltip" data-placement="bottom" title="Type Title for an item to add" style="min-width: 280px;">
						</div>
                                                 
					</div>	
                                    <?php
                                    echo form_submit('mysubmit', 'Submit');
                                    ?>
				</form>
			</div>
		</div>
	</div>
</div>
</div> 