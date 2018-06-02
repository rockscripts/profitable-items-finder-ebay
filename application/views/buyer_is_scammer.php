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
				  
				  <br><br><br>
				  This verification is to prevent payments with an eBay, PayPal and credit card stolen. If you are the owner of them, make sure the follow:<br>
				  <b>1.</b> You have your shipping address information and phone number updated for purchases with eBay.<br>
				  Kindly, Go to<i>  My eBay -> account -> addresses -> shipping information <b>for purchases</b> -> update.</i><br>
				  <b>2.</b> Your eBay and PayPal accounts were registered in the same country.<br>
				  <b>3.</b> You must stay in the same location as your eBay and PayPal accounts were registered.<br>
				  <b>4.</b> You have a PayPal account verified. It means you need to add a credit card and enter code sent by PayPal to complete verification.<br> Kindly, follow this <a target="_blank" href='https://www.paypal.com/cgi-bin/webscr?cmd=p/acc/seal-CA-unconfirmed-outside' title='verify account' >link</a> to learn and verify your account.<br>
				  
				  <p>
				  <br>
				  <b>As our payment and shipping conditions were described in item description.  
				  <?php
				  if($was_refunded=="false"):
				   ?>
	 			  We are refunding your payment right now.
                  <?php else:?>
				  Payment refunded on <i><?=$refunded_date?>.</i>
				  <?php endif;?>
   			      </b> You are welcome anytime if you meet requirements to shop in our store.
				  <p>				  
				  Thank you for understand.	<br>		  
				  <i>Sincerely, Mercado Directo.</i>
				  </div>
			</div>
		</div>
	</div>
</div>
</div> 