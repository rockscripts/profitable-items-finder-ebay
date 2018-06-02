<div class="well">
    <div class="row">
	<div class="col-xs-12 col-sm-12">
		<div class="box">
               			
			<div class="box-content">
				<h4 class="page-header">Verification Result</h4>
                 <p>

</p>				 
				<?php
                  echo $message;                  
				  ?>
				  <div>
				              <?php
                                    $attributes = array('class' => 'form-horizontal', 'id' => 'myform', 'role'=>"form");
                                    echo form_open('orders/delivery', $attributes);
                                    ?>
                                
					<div class="form-group" >
                                            
						<label class="col-sm-2 control-label">Title</label>
						<div class="col-sm-4">
                                                    <input name="phone_number" id="phone_number" type="text" class="form-control" placeholder="eg. Mario Bross..." data-toggle="tooltip" data-placement="bottom" title="Type Title for an item to add" style="min-width: 280px;">
						</div>
                           <input type="hidden" value="<?=$id?>" name="id" id="id" />                      
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
</div> 