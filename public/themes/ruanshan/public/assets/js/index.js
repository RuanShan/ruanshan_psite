// JavaScript Document
var isPlay;
var scrolltop;
var touchendY;
var touchstratY;
var flag = false;
var thisIndex;
var page = {

  init: function() {

    width = $(window).width();
    height = $(window).height();
    var leftpx = Math.floor((width - 1070) / 2);
    if (leftpx < 0) {
      leftpx = 76;
      $('.logo').css({
        'position': 'absolute'
      });
    } else {
      $('.logo').css({
        'position': 'fixed'
      });
    }
    $('.logo').css({
      'left': leftpx
    });
    document.documentElement.scrollTop = 0;
    document.body.scrollTop = 0;
    //$(window).scrollTop(0);
    isPlay = new Array(false, false, false, false, false, false, false);
    if (isMobile() == true) {
      $('.page-header').css({
        'height': height
      });
      $('.page-header .title').css({
        'margin-top': Math.floor(height / 3)
      });
      $('.part').css({
        'height': height
      });
      page.scrollpage();
    }
    $('.loading').hide();
  },
  play: function(n) {
    var aniClass = document.getElementsByClassName('animation' + n);
    var aniClass2 = document.getElementsByClassName('animation' + n + '2');
    var aniClass5 = document.getElementsByClassName('animation' + n + '5');
    var aniClass6 = document.getElementsByClassName('animation' + n + '6');
    var remove = document.getElementsByClassName('remove');
    for (var i = 0; i < aniClass.length; i++) {
      aniClass[i].beginElement();
    }
    //if(isPlay[0]){for(var i=0;i<remove.length;i++){remove[i].setAttribute('fill','remove');}}//
    if (n == 1) {
      setTimeout(function() {
        for (var i = 0; i < aniClass2.length; i++) {
          aniClass2[i].beginElement();
        }
      }, 2000)
    }
    if (n == 4) {
      setTimeout(function() {
        for (var i = 0; i < aniClass5.length; i++) {
          aniClass5[i].beginElement();
        }
      }, 1500);
      setTimeout(function() {
        for (var i = 0; i < aniClass6.length; i++) {
          aniClass6[i].beginElement();
        }
      }, 1600)
    }
  },
  checkscroll: function() {
    $(window).bind("scroll", function(event) {
      $('.example').each(function(index) {
        var fold = $(window).height() + $(window).scrollTop();
        var scrollTop = $(window).scrollTop();
        set = $(this).height() * 0.2;
        if ((fold >= $(this).offset().top + set) && ($(this).offset().top + $(this).height() - set >= scrollTop)) {
          if (isPlay[index]) {
            return
          }
          isPlay[index] = true;
          page.play(index + 1);
        }
      });
    });


  },
  scrollpage: function() {

    $('.page-header').bind('touchend', function(e) {
      page.touchend(e, -1);
    });

    $('.page-header').bind('touchstart', function(e) {
      page.touchstart(e, -1);
    });


    $('.page-footer').bind('touchend', function(e) {

      if ($(window).scrollTop() < height * 8) {
        $('html,body').animate({
          scrollTop: height * 7
        }, 800);
      }

    });




    $('.part').each(function(index) {

      $(this).bind('touchstart', function(e) {
        page.touchstart(e, index);
      });

      $(this).bind('touchend', function(e) {
        page.touchend(e, index);

      });
    })


  },
  touchstart: function(e, index) {

    //e.preventDefault();
    var touchstrat = e.originalEvent.targetTouches[0];
    touchstratY = touchstrat.pageY;
    //thisIndex=index;

  },
  touchend: function(e, index) {
    //e.preventDefault();
    if (flag) {
      return
    }
    flag = true;
    var touchend = e.originalEvent.changedTouches[0];
    touchendY = touchend.pageY;
    if (touchendY - touchstratY < 0) {
      $('html,body').animate({
        scrollTop: height * (index + 2)
      }, 800, function() {
        flag = false
      });
    } else if (touchendY - touchstratY > 0) {
      if (index == -1) {
        index = 0;
      }
      $('html,body').animate({
        scrollTop: height * (index)
      }, 800, function() {
        flag = false
      });
    } //$(window).scrollTop(height*(index));
  }

}






$(document).on('mouseover', '.menu li', function(e) {
  $('.menu li').removeClass('selected');
  $(this).addClass('selected');
});

$(document).on('mouseleave', '.menu li', function(e) {
  $('.menu li').removeClass('selected');
  $('.this').addClass('selected');
});



window.onload = function() {
  page.init();
  page.checkscroll();
  color.changecolor(color.selectcolor());
}

/*鼠标滑过碎片闪动*/
$(document).on('mouseover', 'path', function(e) {
  var $el = $(this);
  $el.css({
    webkitAnimation: "flash 0.3s ease-in-out 0s infinite alternate"
  });
});

$(document).on('mouseout', 'path', function(e) {
  var $el = $(this);
  setTimeout(function() {
    $el.css({
      webkitAnimation: "none"
    });
  }, 300);
});


$(document).on('mouseover', 'polygon', function(e) {
  var $el = $(this);
  $el.css({
    webkitAnimation: "flash 0.3s ease-in-out 0s infinite alternate"
  });
});

$(document).on('mouseout', 'polygon', function(e) {
  var $el = $(this);
  setTimeout(function() {
    $el.css({
      webkitAnimation: "none"
    });
  }, 300);
});



if (isMobile() == true) {

  $(document).on('touchstart', 'svg', function(e) {
    var svg = $(this);
    var num = svg.data('type');
    page.play(num);
  });

} else {

  $(document).on('click', 'svg', function(e) {
    var svg = $(this);
    var num = svg.data('type');
    page.play(num);
  });

}

function isMobile() {
  var userAgentInfo = navigator.userAgent;
  var Agents = ["Android", "iPhone", "SymbianOS", "Windows Phone", "iPod", "iPad"];
  var flag = false;
  for (var v = 0; v < Agents.length; v++) {
    if (userAgentInfo.indexOf(Agents[v]) > 0) {
      flag = true;
      break;
    }
  }
  return flag;
}

function isIpad() {
  var userAgentInfo = navigator.userAgent;
  var Agents = ["iPad"];
  var flag = false;
  for (var v = 0; v < Agents.length; v++) {
    if (userAgentInfo.indexOf(Agents[v]) > 0) {
      flag = true;
      break;
    }
  }
  return flag;

}

window.addEventListener('resize', function() {
  width = $(window).width();
  var leftpx = Math.floor((width - 1070) / 2) - 108 + 200 - 76;
  if (leftpx < 60) {
    leftpx = 76;
    $('.logo').css({
      'position': 'absolute'
    });
  } else {
    $('.logo').css({
      'position': 'fixed'
    });
  }
  $('.logo').css({
    'left': leftpx
  });

})
