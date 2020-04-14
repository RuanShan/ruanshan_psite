// JavaScript Document

    $(document).on('mouseover', '.col-btn', function(e) {
            $('.col-btn').addClass('active');
			$('.row-btn').removeClass('active');
	});

    $(document).on('mouseover', '.row-btn', function(e) {
	        $('.row-btn').addClass('active');
			$('.col-btn').removeClass('active');
    });

    $(document).on('mouseleave', '.row-btn', function(e) {
			if(getType()=='col'){
			    $('.col-btn').addClass('active');
			    $('.row-btn').removeClass('active');
			}
    });

    $(document).on('mouseleave', '.col-btn', function(e) {
			if(getType()=='row'){
			    $('.row-btn').addClass('active');
			    $('.col-btn').removeClass('active');
			}
	});

    $(document).on('click', '.demo', function(e) {
            //$('.col-btn').addClass('active');
			//$('.row-btn').removeClass('active');

			$('.single').fadeOut();
			$('.all').fadeIn();


			var type=$(this).data('type');
			$('.all-demo').removeClass('zoomIn');
			$('.all-demo').addClass('zoomOut');
			setTimeout(function(){$('.all-demo').addClass('gone');$('.'+type).removeClass('zoomOut gone');$('.'+type).addClass('zoomIn');},500);


    });


    $(document).on('mouseover', '.menu li', function(e) {
			$('.menu li').removeClass('selected');
			$(this).addClass('selected');
	});

    $(document).on('mouseleave', '.menu li', function(e) {
			$('.menu li').removeClass('selected');
			$('.this').addClass('selected');
	});

    $(document).on('click', '.all-demo', function(e) {
			var name=$(this).data('name');
			var links=$(this).find('.links').html();
			var wedo=$(this).data('wedo');
			var src=$(this).data('src');
			var qrcodesrc=$(this).data('qrcodesrc');
			var info=$(this).find('.hidden-info').html();
			var title=$(this).find('h1').html();
			putInfo(name,wedo,links,info,src,title,qrcodesrc);
			$('.all').fadeOut();
			$('.single').fadeIn();
            $(window).scrollTop(0);
    });


	function isIpad(){
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

   function putInfo(name,wedo,links,info,src,title,qrcodesrc){
	   $('#title').html(title);
	   $('#info').html(info);
	   $('#name').html(name);
	   $('#wedo').html(wedo);
	   //$('#link').html(links);
	   $('#link').attr('href',links);
	   $('#demoimg').attr('src',src);
	   $('#qrcode').attr('src','');
	   $('#qrcode').hide();
	   if(typeof qrcodesrc!=undefined){
	      $('#qrcode').attr('src',qrcodesrc);
		  $('#qrcode').show();
	   }

   }


	function change(type){
		var othertype;
		if(type=='col'){othertype='row'}else{othertype='col'}
            $('.'+type+'-btn').addClass('active');
			$('.'+othertype+'-btn').removeClass('active');
			$('.all-demo').removeClass('style-'+othertype);
			$('.all-demo').addClass('style-'+type);
	}

   function getType(){
        var type;
		if($('.all-demo').hasClass('style-col')){return 'col'}
		else if($('.all-demo').hasClass('style-row')){return 'row'}
   }


  function init(){
		width=$(window).width();
		var leftpx=Math.floor((width-1070)/2)-108+200-76;
		if(leftpx<60){
			leftpx=76;
			$('.logo').css({'position':'absolute'});
		}else{
			$('.logo').css({'position':'fixed'});
		}
		$('.logo').css({'left':leftpx});
		color.changecolor(color.selectcolor());
   }


   window.onscroll=function(){
		width=$(window).width();
		var leftpx=Math.floor((width-1070)/2);
		if(leftpx<0){return}
        if($(window).scrollTop()>=200){
			$('.logo').css({'top':0});
		}else{
		    $('.logo').css({'top':50});
		}

   }



   window.onload=function(){init();}