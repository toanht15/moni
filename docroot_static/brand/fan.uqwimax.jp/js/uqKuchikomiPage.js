(function ($) {

/* ------------------------------------------------------------------------
tab
------------------------------------------------------------------------ */
var uqCurrentTab = window.location.hash.replace('#', "").split('&')[0];
if( uqCurrentTab === 'js--uqwimaxKuchikomiCont--mobile' ){
	uqSetCurrentTab(uqCurrentTab);
}
function uqSetCurrentTab(_target){
	$('.uqwimaxKuchikomiTab li').removeClass('uqwimaxKuchikomiTabOn');
	$('.uqwimaxKuchikomiCont').removeClass('uqwimaxKuchikomiContOn');
	if( _target === 'js--uqwimaxKuchikomiCont--mobile' ){
		$('.uqwimaxKuchikomiTab li#js--uqwimaxKuchikomiTab--mobile').addClass('uqwimaxKuchikomiTabOn');
		$('.uqwimaxKuchikomiCont#js--uqwimaxKuchikomiCont--mobile').addClass('uqwimaxKuchikomiContOn');
	}
	else if( _target === 'js--uqwimaxKuchikomiCont--wimax' ){
		$('.uqwimaxKuchikomiTab li#js--uqwimaxKuchikomiTab--wimax').addClass('uqwimaxKuchikomiTabOn');
		$('.uqwimaxKuchikomiCont#js--uqwimaxKuchikomiCont--wimax').addClass('uqwimaxKuchikomiContOn');
	}
}
$('.uqwimaxKuchikomiTab li').on('click',function(){
	$('.uqwimaxKuchikomiTab li').removeClass('uqwimaxKuchikomiTabOn');
	$('.uqwimaxKuchikomiCont').removeClass('uqwimaxKuchikomiContOn');
	if( $(this).attr('id') === 'js--uqwimaxKuchikomiTab--wimax'){
		$('.uqwimaxKuchikomiTab li#js--uqwimaxKuchikomiTab--wimax').addClass('uqwimaxKuchikomiTabOn');
		$('.uqwimaxKuchikomiCont#js--uqwimaxKuchikomiCont--wimax').addClass('uqwimaxKuchikomiContOn');
	}
	else {
		$('.uqwimaxKuchikomiTab li#js--uqwimaxKuchikomiTab--mobile').addClass('uqwimaxKuchikomiTabOn');
		$('.uqwimaxKuchikomiCont#js--uqwimaxKuchikomiCont--mobile').addClass('uqwimaxKuchikomiContOn');
	}
});

/* ------------------------------------------------------------------------
enquete
------------------------------------------------------------------------ */
// アンケート出力
$.fn.mpSimpleEnqueteSlideshow = function(option){
  var self = this;

  self.settings = {
    api : '',
    query : {
      'code' : ''
    },
    limit: 10
  };

  $.extend(self.settings,option);
  self.settings.query.limit = self.settings.limit;

  console.log( self.settings );

  //get data
  self.getData = function () {
    $.ajax({
      type: 'GET',
      url: self.settings.api,
      data: self.settings.query,
      dataType: 'JSONP',
      success: function(json){
      	console.log( json );
        if( json.result !== 'ng' ){
          self.showData( json );
        }
        else {
          self.showErr();
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        self.showErr();
      },
      timeout: 10000
    });
  };

  self.showErr = function(){
    self.html( '<div style="border: 1px solid #ccc; padding: 20px; text-align: center; margin-bottom: 60px;">投稿はまだありません</div>' );
  }

  self.cutStr = function(text, cutLen, addStr){
      if (! text) return '';
      return (text.length > cutLen) ? text.substr(0, cutLen) + addStr: text ;
  };

  //show data
  self.showData = function(json){
  	if( 'next_url' in json.pagination ){
  		self.settings.api = json.pagination.next_url;
  		self.settings.moreButton.attr('href', json.pagination.next_url);
    	self.settings.moreButton.fadeIn(300);
  	}
  	else {
  		self.settings.moreButton.css('display','none');
  	}
  	var _html = '';
  	for( var i = 0; i < json.data.answers.length; i++ ){
  		_html += '<div class="uqwimaxKuchikomiReviewSlide"><div class="uqwimaxKuchikomiReviewSlideInner">';
  		_html += '<p class="uqwimaxKuchikomiReviewSlideComment">' + json.data.answers[i].q_answers[0][0].answer_text + '</p>';
  		//_html += '<p class="uqwimaxKuchikomiReviewSlideUserName">' + json.data.answers[i].q_answers[1][0].answer_text + '</p>';
  		_html += '</div></div>';
  	}
	$item = $(_html);
	self.settings.container
		.append($item)
		.masonry( 'appended', $item);

	self.settings.container.imagesLoaded(function(){
		self.settings.container.masonry({
		  hiddenStyle: { opacity: 0 }
		});
	});

  };

  //init
  self.init = function(){
    //set query limit
    self.settings.query.limit = self.settings.limit;
    self.settings.moreButton.fadeOut(0);

    self.getData();
  };

　　self.settings.moreButton.on('click',function(){
    self.settings.moreButton.fadeOut(300);
  	self.settings.api = self.settings.moreButton.attr('href');
  	self.getData();
  	return false;
  });

	self.settings.container.masonry();
  self.init();

};

/*  */
var uqwimaxEntriesLimit = 3;
if( $(window).width() > 640 ){
  uqwimaxEntriesLimit = 9;
}
var uqwimaxKuchikomiReviewListWimax = new $('#js--uqwimaxKuchikomiReviewList--wimax').mpSimpleEnqueteSlideshow({
	'api': '//monipla.com/uq/api/questionnaire_answers.json',
    query : {
      'code' : '6eab315ea1955a8ee81cf3fba21eacaa'
    },
    limit: uqwimaxEntriesLimit,
    moreButton : $('#js--uqwimaxKuchikomiReviewList--wimax--more'),
    container : $('#js--uqwimaxKuchikomiReviewList--wimax')
});

var uqwimaxKuchikomiReviewListMobile = new $('#js--uqwimaxKuchikomiReviewList--mobile').mpSimpleEnqueteSlideshow({
	'api': '//monipla.com/uq/api/questionnaire_answers.json',
    query : {
      'code' : '8f48e9bf0fdff9c7bec5060e6ab5216f'
    },
    limit: uqwimaxEntriesLimit,
    moreButton : $('#js--uqwimaxKuchikomiReviewList--mobile--more'),
    container : $('#js--uqwimaxKuchikomiReviewList--mobile')
});

/* ------------------------------------------------------------------------
tweet
------------------------------------------------------------------------ */
$.fn.SocialinLiteSlide = function( options ){
/* 0. setting */
	var self = this,
	options = $.extend({
		api : '//api.social-in.com/mix/entries.json',
		query: {
			twitter_stream_id: '196',
			page: 1,
			count: 0
		},
		ajax: {
			cache: false,
			timeout: 30000,
			async: true,
			type: 'GET',
			dataType: 'jsonp'
		},
	}, options);
	options.query.count = uqwimaxEntriesLimit;

	//DOM
	self.cardContainer,
	self.pager,
	self.director;

	self.pageNum = parseInt( options.query.page, 10);

/* 1. getData */
	self.getData = function(){
		var ajax = options.ajax;
		options.query.page = self.pageNum;
		ajax.url = options.api;
		ajax.data = options.query;
		ajax.success = function(json){
			if( json.data.entries.length > 0 ) self.build( json.data );
		};
		ajax.error   = function(){
		};
		$.ajax(ajax);
	}

/* 2. init */
	self.init = function(){
		self.addClass('secSocialinGadget1');

		//card container
		self.cardContainer = ( options.cardContainer ) ? options.cardContainer : $('#js--uqwimaxMatomeSlider');
		self.cardContainer.masonry();

		self.getData();

		//director
		self.director = ( options.moreButton ) ? options.moreButton : $('.btnMoreSocialinGadget1', self);
		$('a', self.director).on('click',function(){
			self.pageNum = parseInt( $(this).attr('href'), 10 );
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
	self.build = function(data){
		self.cardArr = data.entries;
		self.pageMax = Math.ceil( parseInt(data.page.total_count, 10) / options.query.count ) - 1;
		//overlay
		self.overlayShow();

		var item = '';
		for( var i = 0; i <= options.query.count; i++ ){
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

		$(self.director).fadeIn();
		$('a', self.director).attr( 'href', ( self.pageNum + 1 ) );
		if( self.pageNum === self.pageMax || self.pageMax === 0 ) $(self.director).fadeOut();

		self.overlayClose();
	}


/* 4. setCard */
	self.getCardSource = function(_cardNum){

		var cardHtml ="";
		
		if( self.cardArr[_cardNum].stream_type === 'twitter' ){
			cardHtml += '<div class="uqwimaxMatomeSlide uqwimaxMatomeSlideTwitter">';
	    	cardHtml += '<div class="uqwimaxMatomeSlideCont"><a href="' + self.cardArr[_cardNum].link + '" target="_blank">';
	    	if( self.cardArr[_cardNum].extra_data.entities.media ){
	    		if( self.cardArr[_cardNum].extra_data.entities.media.length > 0 ){
	    			cardHtml += '<div class="uqwimaxMatomeSlideImg"><img src="' + self.cardArr[_cardNum].extra_data.entities.media[0].media_url_https + '"></div>';
	    		}
	    		else {
	    			cardHtml += '<div class="uqwimaxMatomeSlideImg"><img src="' + self.cardArr[_cardNum].extra_data.entities.media.media_url_https + '"></div>';
	    		}
	    		
	    	}
	    	cardHtml += '<p class="uqwimaxMatomeSlideDate">' + self.cardArr[_cardNum].created_at + '</p>';
	    	cardHtml += '<p class="uqwimaxMatomeSlideText">' + self.cardArr[_cardNum].text + '</p>';
	    	cardHtml += '</a></div><ul class="uqwimaxMatomeTwitterButton">';
	    	cardHtml += '<li class="uqwimaxMatomeTwitterButtonFollow"><a href="https://twitter.com/intent/user?screen_name=' + self.cardArr[_cardNum].extra_data.user.screen_name + '"><img src="../../img/base/iconTWFollowWhite.gif" width="30"></a></li>';
	    	cardHtml += '<li class="uqwimaxMatomeTwitterButtonReply"><a href="https://twitter.com/intent/tweet?in_reply_to=' + self.cardArr[_cardNum].object_id + '"><img src="../../img/base/iconTWReplyWhite.gif" width="30"></a></li>';
	    	cardHtml += '<li class="uqwimaxMatomeTwitterButtonRetweet"><a href="https://twitter.com/intent/retweet?tweet_id=' + self.cardArr[_cardNum].object_id + '"><img src="../../img/base/iconTWRetweetWhite.gif" width="30"></a></li>';
	    	cardHtml += '<li class="uqwimaxMatomeTwitterButtonFav"><a href="https://twitter.com/intent/favorite?tweet_id=' + self.cardArr[_cardNum].object_id + '"><img src="../../img/base/iconTWFavWhite.gif" width="30"></a></li>';
	    	cardHtml += '</ul>';
	    	cardHtml += '<p class="uqwimaxMatomeAuthor"><a href="https://twitter.com/' + self.cardArr[_cardNum].extra_data.user.screen_name + '" target="_blank" style="background: url(' + self.cardArr[_cardNum].extra_data.user.profile_image_url_https + ') 0 50% no-repeat; background-size:27px 27px;">' + self.cardArr[_cardNum].extra_data.user.name + '  @' + self.cardArr[_cardNum].extra_data.user.screen_name + ' </a></p>';
	    	cardHtml += '</div></div>';
		}
		if( self.cardArr[_cardNum].stream_type === 'facebook' ){

			cardHtml += '<div class="uqwimaxMatomeSlide uqwimaxMatomeSlideFacebook">';
			cardHtml += '<div class="uqwimaxMatomeSlideCont">';
			cardHtml += '<a href="' + self.cardArr[_cardNum].link + '" target="_blank">';
			cardHtml += '<div class="uqwimaxMatomeSlideImg"><img src="' + self.cardArr[_cardNum].extra_data.picture + '"></div>';
			cardHtml += '<p class="uqwimaxMatomeSlideDate">' + self.cardArr[_cardNum].created_at + '</p>';
			cardHtml += '<p class="uqwimaxMatomeSlideText">' + self.cardArr[_cardNum].text +  '</p>';
			cardHtml += '</a>';
			cardHtml += '</div>';
			cardHtml += '<p class="uqwimaxMatomeAuthor"><a href="https://www.facebook.com/' + self.cardArr[_cardNum].extra_data.from.id + '">' + self.cardArr[_cardNum].extra_data.from.name + '</a></p>';
			cardHtml += '</div>';
		}

		return cardHtml;
	}
/* 4-1. setCardStyle */
	self.setCardStyle = function(){
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

var uqwimaxMatomeSliderWimax = new $('#uqwimaxMatomeWimax').SocialinLiteSlide({
	query: {
		page: 1,
		twitter_stream_id: '208,209'
	},
	cardContainer : $('#js--uqwimaxMatomeSlider--wimax'),
	moreButton : $('#js--uqwimaxMoreButton1--wimax')
});

var uqwimaxMatomeSliderMobile = new $('#uqwimaxMatomeMobile').SocialinLiteSlide({
	query: {
		page: 1,
		twitter_stream_id: '210,211,212'
	},
	cardContainer : $('#js--uqwimaxMatomeSlider--mobile'),
	moreButton : $('#js--uqwimaxMoreButton1--mobile')
});

})($);