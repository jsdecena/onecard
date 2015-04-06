window.onecard = {

	bindEvent : function() {

		$('.onecard a').on('click', function(){
			$('#onecard').submit();
			return false;
		});
	}
}

window.onecard.bindEvent();