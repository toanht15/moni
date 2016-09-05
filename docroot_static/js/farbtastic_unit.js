jQuery(function($){
  var colorWheel = $('.jsFarbtastic');
  for(i = 0; i<colorWheel.length; i++){
    var target = $(colorWheel[i]).prev('.jsColorInput');
    $(colorWheel[i]).farbtastic(target);
  }
  $('.jsColorInput').focusin(function(){
    $(this).next('.jsFarbtastic').fadeIn(200);
  });
  $('.jsColorInput').focusout(function(){
    $(this).next('.jsFarbtastic').fadeOut(200);
  });
});