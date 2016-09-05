$(document).ready(function(){

	$('#uqwimaxHeaderMenuBtn').on('click',function(){
		$('#uqwimaxHeader ul').toggleClass('uqwimaxMenuActive');
		return false;
	});

	$('#uqwimaxHeader ul a').on('click',function(){
		$('#uqwimaxHeader ul').removeClass('uqwimaxMenuActive');
		if( $(this).attr('id') === 'uqwimaxHeaderMenuCloseBtn' ){
			return false;
		}
		else {
			var _target = $(this).attr('href');
			var _scrollTop = $( _target ).offset().top - 60;
			$('body,html').animate({scrollTop:_scrollTop}, 200, 'swing');
			return false;
		}
	});

	var _headerHeight = ( /iPhone|Windows Phone|Opera Mobi|Fennec|Android.*Mobile/i.test(navigator.userAgent) ) ? 142 : 172;
	$('body').css('padding-top', _headerHeight + 'px');
	$(window).on('scroll', function(){
		//uqwimaxHeader
		var _top = $(this).scrollTop();
		if( _top > _headerHeight && !$('#uqwimaxHeader').hasClass('uqwimaxHeaderActive') ){
			$('#uqwimaxHeader').addClass('uqwimaxHeaderActive');
		}
		else if( _top <= $('body > header').height() ) {
			$('#uqwimaxHeader').removeClass('uqwimaxHeaderActive');
		}
	});
});