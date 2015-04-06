<li class="col-md-4" style="list-style:none">
	<div class="onecard-wrap">
		<div class="container">
			<p class="payment_module onecard">				
				<a href="#">
					<span class="img-wrap">
						<img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/onecard/logo.png" alt="onecard" width="153" height="50" class="img-responsive" />							
					</span>
					<span>Pay via One Card</span>
					<span class="clearfix"></span>
				</a>
			</p>
		</div>
	</div>
	<form name="onecard" action="{$link}" method="post" id="onecard" />
	<input type="hidden" id="OneCard_MerchID" name="OneCard_MerchID" value="{$merchant_id}" /> 
	<input type="hidden" id="OneCard_TransID" name="OneCard_TransID" value="{$trans_id}" /> 
	<input type="hidden" id="OneCard_Amount" name="OneCard_Amount" value="{$amount}" /> 
	<input type="hidden" id="OneCard_Currency" name="OneCard_Currency" value="{$currency}" />
	<input type="hidden" id="OneCard_Timein" name="OneCard_Timein" value="{$time}" /> 
	<input type="hidden" id="OneCard_MProd" name="OneCard_MProd" value="Sendah Purchase" /> 
	<input type="hidden" id="OneCard_ReturnURL" name="OneCard_ReturnURL" value = "{$return_url}" /> 
	<input type="hidden" id="OneCard_HashKey" name="OneCard_HashKey" value="{$hash}" /> 
	</form>
</li>