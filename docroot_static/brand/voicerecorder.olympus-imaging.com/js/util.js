$(document).ready(function(){
	$('#btnOlympusMenu').on('click',function(){
		$('#olympusGlobalNav').toggleClass('active');
	});

	$('#olympusGlobalNavClose').on('click',function(){
		$('#olympusGlobalNav').removeClass('active');
	});

});