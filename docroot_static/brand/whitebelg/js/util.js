$(document).ready(function(){

	$('#whitebelgBtnToggleNav').on('click',function(){
		$('#whitebelgHeader').toggleClass('active');
		return false;
	});

	$('#whitebelgGlobalNavClose,#whitebelgHeader ul a').on('click',function(){
		$('#whitebelgHeader').removeClass('active');
		if( $(this).parent().hasClass('whitebelgNavClose') || $(this).attr('id') === 'whitebelgGlobalNavClose' ){
			return false;
		}
		
	});

	var whitebelgStudentsNum = $('#whitebelgStudentsNum').text().toString().replace(/(\d)(?=(\d{3})+$)/g , '$1c');
	var _numHtml = '';
	for( var i = whitebelgStudentsNum.length - 1; i > -1; i--){
		var _num = whitebelgStudentsNum.charAt(i);

		_numHtml = '<span class="whitebelgStudentsNum' + _num + '"></span>' + _numHtml;
	}
	$('#whitebelgStudentsNum').html( _numHtml );

});