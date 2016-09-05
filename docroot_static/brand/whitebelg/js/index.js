$(function(){
$('#js--whitebelgTopPageSlider').bxSlider({
    auto: true
});

$.fn.mpSimpleThumbSlideshow = function(option){
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
    appendMode: false
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
      jsonpCallback: 'callback',
      dataType: 'JSONP',
      success: function(json){
        if( json.data.length > 0 ){
          self.showData( json );
          $('.whitebelgPostedPhotoDirectionBtn').css('display','table');
        }
        else {
          self.showErr();
        }
        
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
      },
      timeout: 10000
    });
  };

  //err
  self.showErr = function(){
    $('#whitebelgTopPageTheme').css('margin-bottom','40px');
  }

  //show data
  self.showData = function(data){
      $('#moniplaPage--photoOutput--entries').stop().animate({
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
      elm += '<div class="mpSimpleThumbSlideshowEntry"><div class="mpSimpleThumbSlideshowEntryInner">';

      //photo
      elm += '<div class="mpSimpleThumbSlideshowEntryPhoto" style="background:url(' + data.data[i].photos.default.url + ') 50% 50% no-repeat; background-size: cover;">';
      elm += '<a href="' + data.data[i].page_url + '" target="_blank"><img class="mpSimpleThumbSlideshowEntryPhotoImage" src="' + data.data[i].photos.default.url + '"><img src="data:image/png;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEUAAAALAAAAAABAAEAAAICRAEAOw=="></a>';
      elm += '</div>';

      //title
      elm += '<div class="mpSimpleThumbSlideshowEntryTitle">' + data.data[i].title + '</div>';

      //comment
      elm += '<div class="mpSimpleThumbSlideshowEntryComment">' + data.data[i].comment + '</div>';

      elm += '</div></div>';
      elmAll += elm;
    }
    self.settings.container.html( elmAll );
    $('#moniplaPage--photoOutput--entries').imagesLoaded(function(){
      $('#moniplaPage--photoOutput--entries').animate({
        'opacity': 1
      },300);
    });

  };

  //show modal
  self.showModal = function(obj){
    $('body').append('<div id="mpSimpleThumbSlideshowOverlay"></div>');
    var modalDom = '<div id="mpSimpleThumbSlideshowModal"><div id="mpSimpleThumbSlideshowModalInner">';
    modalDom += '<a href="#" id="mpSimpleThumbSlideshowModalCloseButton">close</a>';
    modalDom += obj.html();
    modalDom += '</div></div>';
    $('body').append( modalDom );

    var w = $(window);
    var scrollTop = w.scrollTop();
    if( w.height() > $('#mpSimpleThumbSlideshowModal').height() + 50 ){
      $('#mpSimpleThumbSlideshowModal').css('top', w.scrollTop() + ( w.height() - $('#mpSimpleThumbSlideshowModal').height() ) /2 );
    }
    else {
      $('#mpSimpleThumbSlideshowModal').css('top', w.scrollTop() + 50);
    }

    $('#mpSimpleThumbSlideshowOverlay,#mpSimpleThumbSlideshowModalCloseButton').on('click',function(){
      $('#mpSimpleThumbSlideshowOverlay').fadeOut(100,function(){
        $('#mpSimpleThumbSlideshowOverlay').remove();
        $('#mpSimpleThumbSlideshowModal').remove();
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
      self.settings.container.on('click','.mpSimpleThumbSlideshowEntryPhoto',function(){
        modalObj = $(this).parents('.mpSimpleThumbSlideshowEntry');
        self.showModal(modalObj);
        return false;
      });
    }
  };

  //more
  self.settings.moreButton.on('click',function(){
    self.currentPage ++;
    self.settings.moreButton.removeClass('whitebelgBtnActive');
    self.settings.prevButton.removeClass('whitebelgBtnActive');
    self.getData();
    return false;
  });

  //more
  
  self.settings.prevButton.on('click',function(){
    self.currentPage --;
    self.settings.moreButton.removeClass('whitebelgBtnActive');
    self.settings.prevButton.removeClass('whitebelgBtnActive');
    self.settings.query.next_id = self.prev_id;
    self.getData();
    return false;
  });

  self.init();

};

$('#moniplaPage--photoOutput--entries').mpSimpleThumbSlideshow({
    api : '//monipla.com/output_demo1/api/photos.json',
    //api : 'https://whitebelg.com/api/photos.json',
    query : {
      'code' : 'e580d357114fe9615c68ff1322ca0560',
      //'code' : '4a326cccc25d2e34e2e56786817d96a7',
      'next_id':0
    },
    limit: 6,
    container: $('#moniplaPage--photoOutput--entries'),
    moreButton: $('#moniplaPage--photoOutput--next'),
    prevButton: $('#moniplaPage--photoOutput--prev'),
    showModal: true,
    appendMode: false
});



});