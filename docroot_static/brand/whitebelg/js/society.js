$(function(){

;(function ($) {

$.fn.mpSimpleSlider = function(option){
  var self = this;

  self.settings = {
    api : '',
    query : {
      'code' : '',
      'next_id':0
    },
    limit: 10,
    container: self,
    moreButton: null,
    prevButton: null,
    showModal: true,
    appendMode: false,
    cutStrLength: 30,
    loadedScrollTopPadding: 0,
    containerWrap: false
  };
  self.currentPage = 0;
  self.pageArr = [0];

  $.extend(self.settings,option);

  //get data
  self.getData = function () {
    self.settings.query.next_id = self.pageArr[ self.currentPage ];
    $.ajax({
      type: 'GET',
      url: self.settings.api,
      data: self.settings.query,
      dataType: 'JSONP',
      success: function(json){
        if( json.data.length > 0 ){
          self.showData( json );
          self.settings.containerWrap.css('display','block');
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        
      },
      timeout: 10000
    });
  };

  self.cutStr = function(text, cutLen, addStr){
      if (! text) return '';
      return (text.length > cutLen) ? text.substr(0, cutLen) + addStr: text ;
  };

  //show data
  self.showData = function(data){
      self.settings.container.stop().animate({
        'opacity': 0
      },0);

    //set More Status
    if( data.pagination.next_url ){
      self.settings.moreButton.addClass('whitebelgBtnActive');
      var _next_id = parseInt( data.pagination.next_id , 10);
      if( self.pageArr.indexOf( _next_id ) === -1 ){
        self.pageArr.push( _next_id );
      }
    }
    else {
      self.settings.moreButton.removeClass('whitebelgBtnActive');
    }
    if( self.currentPage > 0 ){
      self.settings.prevButton.stop().addClass('whitebelgBtnActive');
    }
    else {
      self.settings.prevButton.removeClass('whitebelgBtnActive');
    }
    //create DOM
    var elmAll = '';
    for( var i = 0; i < data.data.length; i++){
      var elm = '';
      //wrapper
      elm += '<div class="mpSimpleSliderEntry"><div class="mpSimpleSliderEntryInner">';

      //photo

      if( data.data[i].media[0] !== undefined ){
        elm += '<div class="mpSimpleSliderEntryPhoto" style="background:url(' + data.data[i].media[0].media_url + ') 50% 50% no-repeat; background-size: cover;">';
        elm += '<img class="mpSimpleSliderEntryPhotoImage" src="' + data.data[i].media[0].media_url + '"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMAAAACQCAYAAABeUmTwAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAAABx0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNui8sowAAAAWdEVYdENyZWF0aW9uIFRpbWUAMDMvMjIvMTYPhPJiAAABl0lEQVR4nO3TMQEAIAzAMMC/5yFjRxMFfXpn5kDV2w6ATQYgzQCkGYA0A5BmANIMQJoBSDMAaQYgzQCkGYA0A5BmANIMQJoBSDMAaQYgzQCkGYA0A5BmANIMQJoBSDMAaQYgzQCkGYA0A5BmANIMQJoBSDMAaQYgzQCkGYA0A5BmANIMQJoBSDMAaQYgzQCkGYA0A5BmANIMQJoBSDMAaQYgzQCkGYA0A5BmANIMQJoBSDMAaQYgzQCkGYA0A5BmANIMQJoBSDMAaQYgzQCkGYA0A5BmANIMQJoBSDMAaQYgzQCkGYA0A5BmANIMQJoBSDMAaQYgzQCkGYA0A5BmANIMQJoBSDMAaQYgzQCkGYA0A5BmANIMQJoBSDMAaQYgzQCkGYA0A5BmANIMQJoBSDMAaQYgzQCkGYA0A5BmANIMQJoBSDMAaQYgzQCkGYA0A5BmANIMQJoBSDMAaQYgzQCkGYA0A5BmANIMQJoBSDMAaQYgzQCkGYA0A5BmANIMQJoBSDMAaQYgzQCkGYA0A5BmANIMQNoH8/UEHdCuOrsAAAAASUVORK5CYII=">';
        elm += '</div>';
      }

      //comment
      elm += '<div class="mpSimpleSliderEntryComment">' + self.cutStr( data.data[i].message , self.settings.cutStrLength, '...') + '</div>';

      elm += '</div></div>';
      elmAll += elm;
    }
    self.settings.container.html( elmAll );
    self.settings.container.imagesLoaded(function(){
      self.settings.container.animate({
        'opacity': 1
      },300);
    });

  };

  //show modal
  self.showModal = function(obj){
    $('body').append('<div id="mpSimpleSliderOverlay"></div>');
    var modalDom = '<div id="mpSimpleSliderModal"><div id="mpSimpleSliderModalInner">';
    modalDom += '<a href="#" id="mpSimpleSliderModalCloseButton">close</a>';
    modalDom += obj.html();
    modalDom += '</div></div>';
    $('body').append( modalDom );

    var w = $(window);
    var scrollTop = w.scrollTop();
    if( w.height() > $('#mpSimpleSliderModal').height() + 50 ){
      $('#mpSimpleSliderModal').css('top', w.scrollTop() + ( w.height() - $('#mpSimpleSliderModal').height() ) /2 );
    }
    else {
      $('#mpSimpleSliderModal').css('top', w.scrollTop() + 50);
    }

    $('#mpSimpleSliderOverlay,#mpSimpleSliderModalCloseButton').on('click',function(){
      $('#mpSimpleSliderOverlay').fadeOut(100,function(){
        $('#mpSimpleSliderOverlay').remove();
        $('#mpSimpleSliderModal').remove();
      });
      return false;
    });

  };


  //init
  self.init = function(){
    //set query limit
    self.settings.query.limit = self.settings.limit;
    //direction button
    self.getData();
    //show modal
    if( self.settings.showModal ){
      self.settings.container.on('click','.mpSimpleSliderEntry',function(){
        modalObj = $(this);
        self.showModal(modalObj);
        return false;
      });
    }
  };

  //more
  self.settings.moreButton.on('click',function(){
    $('html,body').animate({'scrollTop':self.settings.container.offset().top - 10 - self.settings.loadedScrollTopPadding + 'px'},300);
    self.currentPage ++;
    self.settings.moreButton.removeClass('whitebelgBtnActive');
    self.settings.prevButton.removeClass('whitebelgBtnActive');
    self.getData();
    return false;
  });

  //more
  
  self.settings.prevButton.on('click',function(){
    $('html,body').animate({'scrollTop':self.settings.container.offset().top - 10 - self.settings.loadedScrollTopPadding + 'px'},300);
    self.currentPage --;
    self.settings.moreButton.removeClass('whitebelgBtnActive');
    self.settings.prevButton.removeClass('whitebelgBtnActive');
    self.settings.query.next_id = self.prev_id;
    self.getData();
    return false;
  });

  self.init();

}
})(jQuery);


$('#moniplaPage--photoOutputMonitor--entries').mpSimpleSlider({
    api : '//monipla.com/whitebelgtest/api/tweet_posts.json',
    query : {
      'code' : '7280d9735b6dc315bdf402801b455284',
      'action_ids': '32694%2C32695',
      'next_id':0
    },
    limit: 4,
    container: $('#moniplaPage--photoOutputMonitor--entries'),
    moreButton: $('#moniplaPage--photoOutputMonitor--next'),
    prevButton: $('#moniplaPage--photoOutputMonitor--prev'),
    showModal: true,
    appendMode: false,
    cutStrLength: 50,
    containerWrap: $('#js--whitebelgSocietyMonitor')
});

var photoOutputHowToEnjoyNum = ( $(window).width() > 641 ) ? 8 : 2;
var spLoadedScrollTopPadding = ( $(window).width() > 641 ) ? 0 : 31;


$('#moniplaPage--photoOutputHowToEnjoy--entries').mpSimpleSlider({
    api : '//monipla.com/whitebelgtest/api/tweet_posts.json',
    query : {
      'code' : '7280d9735b6dc315bdf402801b455284',
      'action_ids': '32687%2C32689',
      'next_id':0
    },
    limit: photoOutputHowToEnjoyNum,
    container: $('#moniplaPage--photoOutputHowToEnjoy--entries'),
    moreButton: $('#moniplaPage--photoOutputHowToEnjoy--next'),
    prevButton: $('#moniplaPage--photoOutputHowToEnjoy--prev'),
    showModal: true,
    appendMode: false,
    cutStrLength: 50,
    loadedScrollTopPadding: spLoadedScrollTopPadding,
    containerWrap: $('#js--whitebelgSocietyHowToEnjoy')
});


});