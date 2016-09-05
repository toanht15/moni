(function ($) {
$.fn.uqMatomeGadget = function( options ){
/* 0. setting */
	var self = this,
	options = $.extend({
		api : '//monipla.com/uq/api/sns_panels.json',
		query: {
			code: 'ed2f67ec3b1c1e8ca07d816c675bb645',
			limit: '9',
			p: 1
		},
		ajax: {
			cache: false,
			timeout: 30000,
			async: true,
			type: 'GET',
			dataType: 'jsonp'
		},
	}, options);

	if( $(window).width() <= 640 ){
		options.query.limit = '5';
	}


	//DOM
	self.cardContainer,
	self.pager,
	self.director;

	self.pageNum = parseInt( options.query.p, 10);

/* 1. getData */
	self.getData = function(){
		var ajax = options.ajax;
		options.query.page = self.pageNum;
		ajax.url = options.api;
		ajax.data = options.query;
		ajax.success = function(json){
			console.log(json);
			if( json.data.length > 0 ) self.build( json );
		};
		ajax.error   = function(){
		};
		$.ajax(ajax);
	}

/* 2. init */
	self.init = function(){
		self.addClass('secSocialinGadget1');

		//card container
		self.cardContainer = $('#js--uqwimaxMatomeSlider');
		self.cardContainer.masonry();

		self.getData();

		//director
		self.director = $('#js--uqMatomeMoreBtn');
		$('a', self.director).on('click',function(){
			//self.api = $(this).attr('href');
			self.getData();
			return false;
		});

		$(self).on('click','li.cardSocialinGadget1 a.contentsSocialinGadget1',function(){
			var modalObj = $(this).parents('li.cardSocialinGadget1').html();
			self.showCardModal( modalObj );
			return false;
		});
		//self.cardContainer.masonry();
	}

/* 3. build */
	self.build = function(json){
		self.cardArr = json.data;

		//overlay
		self.overlayShow();

		var item = '';
		for( var i = 0; i <= options.query.limit; i++ ){
			if( self.cardArr[i] ){
				item += self.getCardSource(i);
			}
		}
		$item = $(item);
		self.cardContainer
			.append($item)
			.masonry( 'appended', $item);

		self.cardContainer.imagesLoaded(function(){
			self.cardContainer.masonry({
			  hiddenStyle: { opacity: 0 }
			});
		});

		$(self.director).fadeOut(0);
		
		if( json.pagination.next_url ){
			self.pageNum += 1;
			options.query.p = self.pageNum;
			//$('a', self.director).attr( 'href', json.pagination.next_url );
			$(self.director).fadeIn(500);
		}

		self.overlayClose();
	}


/* 4. setCard */
	self.getCardSource = function(_cardNum){

		var cardHtml ="";
		
		if( self.cardArr[_cardNum].stream_name === 'TwitterEntry' ){
			cardHtml += '<div class="uqwimaxMatomeSlide uqwimaxMatomeSlideTwitter">';
	    	cardHtml += '<div class="uqwimaxMatomeSlideCont"><a href="' + self.cardArr[_cardNum].entry.page_link + '">';
	    	cardHtml += '<div class="uqwimaxMatomeSlideImg"><img src="' + self.cardArr[_cardNum].entry.image_url + '"></div>';
	    	cardHtml += '<p class="uqwimaxMatomeSlideDate">' + self.cardArr[_cardNum].entry.pub_date + '</p>';
	    	cardHtml += '<p class="uqwimaxMatomeSlideText">' + self.cardArr[_cardNum].entry.panel_text + '</p>';
	    	cardHtml += '</a></div><ul class="uqwimaxMatomeTwitterButton">';
	    	cardHtml += '<li class="uqwimaxMatomeTwitterButtonFollow"><a href="https://twitter.com/intent/user?screen_name=' + self.cardArr[_cardNum].screen_name + '"><img src="../../img/base/iconTWFollow.gif" width="30"></a></li>';
	    	cardHtml += '<li class="uqwimaxMatomeTwitterButtonReply"><a href="https://twitter.com/intent/tweet?in_reply_to=' + self.cardArr[_cardNum].entry.object_id + '"><img src="../../img/base/iconTWReply.gif" width="30"></a></li>';
	    	cardHtml += '<li class="uqwimaxMatomeTwitterButtonRetweet"><a href="https://twitter.com/intent/retweet?tweet_id=' + self.cardArr[_cardNum].entry.object_id + '"><img src="../../img/base/iconTWRetweet.gif" width="30"></a></li>';
	    	cardHtml += '<li class="uqwimaxMatomeTwitterButtonFav"><a href="https://twitter.com/intent/favorite?tweet_id=' + self.cardArr[_cardNum].entry.object_id + '"><img src="../../img/base/iconTWFav.gif" width="30"></a></li>';
	    	cardHtml += '</ul>';
	    	cardHtml += '<p class="uqwimaxMatomeAuthor"><a href="https://twitter.com/' + self.cardArr[_cardNum].screen_name + '" target="_blank" style="background: url(' + self.cardArr[_cardNum].image_url + ') 0 50% no-repeat; background-size:27px 27px;">' + self.cardArr[_cardNum].page_name + ' </a></p>';
	    	cardHtml += '</div></div>';
		}

		else if( self.cardArr[_cardNum].stream_name === 'FacebookEntry' ){
			cardHtml += '<div class="uqwimaxMatomeSlide uqwimaxMatomeSlideFacebook">';
			cardHtml += '<div class="uqwimaxMatomeSlideCont">';
			cardHtml += '<a href="' + self.cardArr[_cardNum].entry.page_link + '">';
			cardHtml += '<div class="uqwimaxMatomeSlideImg"><img src="' + self.cardArr[_cardNum].entry.image_url + '"></div>';
			cardHtml += '<p class="uqwimaxMatomeSlideDate">' + self.cardArr[_cardNum].entry.pub_date + '</p>';
			cardHtml += '<p class="uqwimaxMatomeSlideText">' + self.cardArr[_cardNum].entry.panel_text +  '</p>';
			cardHtml += '</a>';
			cardHtml += '</div>';
			cardHtml += '<p class="uqwimaxMatomeAuthor"><a href="https://www.facebook.com/uqwimax/" target="_blank">' + self.cardArr[_cardNum].page_name + '</a></p>';
			cardHtml += '</div>';
		}

		else if( self.cardArr[_cardNum].stream_name === 'RssEntry' ){
			cardHtml += '<div class="uqwimaxMatomeSlide uqwimaxMatomeSlideBlog">';
			cardHtml += '<div class="uqwimaxMatomeSlideCont">';
			cardHtml += '<a href="' + self.cardArr[_cardNum].entry.link + '" target="_blank">';
			cardHtml += '<div class="uqwimaxMatomeSlideImg"><img src="' + self.cardArr[_cardNum].entry.image_url + '"></div>';
			cardHtml += '<p class="uqwimaxMatomeSlideDate">' + self.cardArr[_cardNum].entry.pub_date + '</p>';
			cardHtml += '<p class="uqwimaxMatomeSlideText">' + self.cardArr[_cardNum].entry.panel_text + '</p>';
			cardHtml += '</a>';
			cardHtml += '</div>';
			cardHtml += '<p class="uqwimaxMatomeAuthor"><a href="http://www.uqwimax.jp/wimax_blog/" target="_blank">UQまとめブログ</a></p>';
			cardHtml += '</div>';
		}

		return cardHtml;
	}

	self.nl2br = function (str) {
		return str.replace(/[\n\r]/g, "<br>");
	};

	self.myDate = function(mydate){
		return mydate.replace(/-/g, '/').substring(0,mydate.indexOf(" "));
	};

/* 5. showDetail */
	self.showCardModal = function(_modalObj){
		//overlay
		self.overlayShow();

		var modalDom = '<div id="modalSocialinGadget1"><div id="modalInnerSocialinGadget1">';
		modalDom += '<p class="close"><a href="#" id="btnCloseModalSocialinGadget1"></a></p>';
		modalDom += _modalObj;
		modalDom += '</div></div>';

		$('body').append( modalDom );
		if ( options.skin === 2 ) $('#modalSocialinGadget1').addClass('modalSocialinGadget1Dark');
		$('#modalSocialinGadget1 .cardSocialinGadget1Inner').css('cssText', 'margin: 0px !important;' + 'padding: ' + options.modalPadding + 'px !important');

		$('#modalSocialinGadget1').imagesLoaded(function(){
			var w = $(window);
			var scrollTop = w.scrollTop();
			if( w.height() > $('#modalSocialinGadget1').height() + 70 ){
				$('#modalSocialinGadget1').css('cssText','top: ' + ( w.scrollTop() + ( w.height() - $('#modalSocialinGadget1').height() ) /2 ) + 'px !important;');
			}
			else {
				$('#modalSocialinGadget1').css('cssText', 'top: ' + ( w.scrollTop() + 70 ) + 'px !important;' );
			}
		});


		$(document).on('click','#loaderOverlaySocialinGadget1',function(){
			self.modalClose();
			return false;
		});

		$(document).on('click','a#btnCloseModalSocialinGadget1',function(){
			self.modalClose();
			return false;
		});
	}

/* 5-1. overlayShow */
	self.overlayShow = function(){
		$('body').append('<div id="loaderOverlaySocialinGadget1"><div id="loaderBarSocialinGadget1"></div></div>');
		if ( options.skin === 2 ) $('#loaderOverlaySocialinGadget1').addClass('loaderOverlaySocialinGadget1Dark');
	}
/* 5-2. overlayClose */
	self.overlayClose = function(){
		$('#loaderBarSocialinGadget1').addClass('loaderBarLoadedSocialinGadget1');
		$('#loaderBarSocialinGadget1').fadeOut(0,function(){
			$('#loaderBarSocialinGadget1').remove();
			$('#loaderOverlaySocialinGadget1').remove();
		});
	}

/* 5-3. modalClose */
	self.modalClose = function(){
		$('#btnCloseModalSocialinGadget1').stop().fadeOut(0,function(){
			$(this).remove();
		})
		$('#modalSocialinGadget1').stop().fadeOut(0,function(){
			$(this).remove();
		})
		$('#loaderOverlaySocialinGadget1').fadeOut(0,function(){
			$(this).remove();
		})
	}

	self.init();
}

$('#js--uqwimaxMatomeSlider').uqMatomeGadget({});

})($);