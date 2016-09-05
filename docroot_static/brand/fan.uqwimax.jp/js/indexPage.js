$(function(){

var myDate = function(mydate){
	return mydate.replace(/-/g, '/').substring(0,mydate.indexOf(" "));
};

//top slider
//get entries
$.ajax({
  type: 'GET',
  url: '//dm-test.aa-dev.com/api/categories.json',
  data: {
  	ids: 355
  },
  dataType: 'JSONP',
  success: function(json){
  	var _html = '';
    if( json.result !== 'ng' ){
	  	if( json.data.length > 0 ){
	  		for( var i = 0; i < json.data.length; i++ ){
	  			if( json.data[i].pages.length > 0 ){
	  				for( var j = 0; j < json.data[i].pages.length; j++ ){
    					_html += '<div class="uqwimaxTopPagePickupSlide"><a href="' + json.data[i].pages[j].url + '">';
						  _html += '<div class="uqwimaxTopPagePickupSlideImg" style="background: url(' + json.data[i].pages[j].og_image_url + ') 50% 50% no-repeat; background-size: contain"></div>';
    					_html += '<div class="uqwimaxTopPagePickupSlideCont">';
    					_html += '<h3 class="uqwimaxTopPagePickupSlideTtl">' + json.data[i].pages[j].title + '</h3>';
    					_html += '<div class="uqwimaxTopPagePickupSlideInfo">';
    					_html += '<p class="uqwimaxTopPagePickupSlideDate">' + myDate( json.data[i].pages[j].created_at ) + '</p>';
    					_html += '<p class="uqwimaxTopPagePickupSlideCommentsNum">' + json.data[i].pages[j].comment_count + 'コメント</p>';
    					_html += '</div></div></a></div>';
	  				}
	  			}
	  		}
	  		$('#js--uqwimaxTopPagePickupSlider').html(_html);
			$('#js--uqwimaxTopPagePickupSlider').slick({
			  autoplay: true,
			  infinite: true,
			  speed: 300,
			  slidesToShow: 1,
			  centerMode: true,
			  variableWidth: true
			});
	  	}
	  	else {
	  		$('#uqwimaxTopPagePickup').css('display','none');
	  	}
    }
    else {
      $('#uqwimaxTopPagePickup').css('display','none');
    }
  },
  error: function(XMLHttpRequest, textStatus, errorThrown) {
    $('#uqwimaxTopPagePickup').css('display','none');
  },
  timeout: 10000
});

//get TsushinEntries
$.ajax({
  type: 'GET',
  url: '//dm-test.aa-dev.com/api/categories.json',
  data: {
    ids: 134
  },
  dataType: 'JSONP',
  success: function(json){
    var _html = '';
    if( json.result !== 'ng' ){
      if( json.data.length > 0 ){
        if( json.data[0].pages.length > 0 ){
          for( var i = 0; i < json.data[0].pages.length; i++ ){

            _html += '<div class="uqwimaxTopPageTsushinSlide"><a href="' + json.data[0].pages[i].url + '">';
            _html += '<div class="uqwimaxTopPageTsushinSlideImg" style="background: url(' + json.data[0].pages[i].og_image_url + ') 50% 50% no-repeat; background-size: contain"></div>';
            _html += '<div class="uqwimaxTopPageTsushinSlideCont">';
            _html += '<h3 class="uqwimaxTopPageTsushinSlideTtl">' + json.data[0].pages[i].title + '</h3>';
            _html += '<div class="uqwimaxTopPageTsushinSlideInfo">';
            _html += '<p class="uqwimaxTopPageTsushinSlideDate">' + myDate( json.data[0].pages[i].created_at ) + '</p>';
            _html += '<p class="uqwimaxTopPageTsushinSlideCommentsNum">' + json.data[0].pages[i].comment_count + 'コメント</p>';
            _html += '</div></div></a></div>';

          }
        }
        $('#uqwimaxTopPageTsushin .uqwimaxTopPageTsushinSlider').html(_html);
      }
      else {
        $('#uqwimaxTopPageTsushin').css('display','none');
      }
    }
    else {
      $('#uqwimaxTopPageTsushin').css('display','none');
    }
  },
  error: function(XMLHttpRequest, textStatus, errorThrown) {
    $('#uqwimaxTopPageTsushin').css('display','none');
  },
  timeout: 10000
});




});