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
                                    echo form_open('orders/check_verification_code', $attributes);
                                    ?>
                                <span> Enter the code sent to you phone number <?=$phone?>.</span><br>
                                    <span> <b>Note:</b> You have three times. If you exceed them, you must contact us as soon as possible or we will refund your money. Thank you in advance</span>
					<div class="form-group">
                                            
						<label class="col-sm-2 control-label">Code:</label>
						<div class="col-sm-4">
                                                    <input name="code" id="code" type="text" class="form-control" placeholder="XXXXXX" data-toggle="tooltip" data-placement="bottom" title="Verification Code">
						    
                                                </div>
                                                <div>
                                                    <a href="<?=base_url()?>index.php/orders/delivery/?id=<?=$id_encrypted?>">Send code again</a>
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