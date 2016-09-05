$(document).ready(function(){

	$('#kenkenHeaderMenuBtn').on('click',function(){
		$('#kenkenHeader').toggleClass('kenkenActive');
		return false;
	});

	$('#kenkenHeader.kenkenActive #kenkenHeaderMenuBtn,#kenkenHeader ul a').on('click',function(){
		$('#kenkenHeader').removeClass('kenkenActive');
		if( $(this).attr('id') === 'kenkenHeaderMenuBtn' ){
			return false;
		}
	});

});