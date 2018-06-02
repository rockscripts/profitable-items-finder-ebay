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
				<h4 class="page-header">Phone verification</h4>
				
                                    <?php
                                    $attributes = array('class' => 'form-horizontal', 'id' => 'myform', 'role'=>"form");
                                    echo form_open('orders/check_phone', $attributes);
                                    ?>
                                <span> Type your phone number registered on eBay. It looks like this format <b><?=trim(str_replace(range(0,9),'X',$phone))?></b> </span><br>
                                <span> We will send you a verification code through SMS service.</span><br>
                                    <span> <b>Note:</b> You must have your eBay phone updated</span>
					<div class="form-group">
                                            
						<label class="col-sm-2 control-label">Phone <span><img title="<?=$country->name?>" alt="<?=$country->name?>" src="<?=base_url()?>country-flags/<?=$country->iso?>.png"> + <?=$country->phonecode?></span></label>
						<div class="col-sm-4">
                                                    <input name="phone_typed" id="phone_typed" type="text" class="form-control" placeholder="<?=trim(str_replace(range(0,9),'X',$phone))?>" data-toggle="tooltip" data-placement="bottom" title="Tooltip for name">
						</div>
                                                 <input type="hidden" name="phone_code_removed" value="<?=$phone_code_removed?>">
                                                  <input type="hidden" name="id_encrypted" value="<?=$id_encrypted?>">
					</div>	
                                    <?php
                                    echo form_submit('mysubmit', 'Send');
                                    ?>
				</form>
			</div>
		</div>
	</div>
</div>
</div> 