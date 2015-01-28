/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

var step = 1;

var checkoutSubmitHandler = function (ev) 
{
	$('#collapse'+step+' .preloader').css('display', 'inline');
	
    $.ajax({
        type: $(this).attr('method'),
        url: $(this).attr('action'),
        data: $(this).serialize(),
        complete: function()
        {
        	$('#collapse'+step+' .preloader').hide();
        },
        success: function (data) 
        {	
        	if(data.indexOf('has-error') <= 0)
        	{
        		$('#collapse'+step).collapse('hide');        		
        	}
        	
        	if(data.indexOf('formStep1') >= 0)
        	{
        		step = 1;        		
        	}
        	else if(data.indexOf('formStep2') >=0)
        	{
        		step = 2;
        	}
        	else
        	{
        		step = 3;
        	}
        	
        	$('#collapse'+step+' .panel-body').html(data);
        	
        	if(data.indexOf('has-error') <= 0)
        	{
        		$('#collapse'+step).collapse('show');
        	}
        	
        	$('#formStep'+step).submit(checkoutSubmitHandler);
        }
    });

    ev.preventDefault();
};

(function ( $ ) {
    'use strict';

    $(document).ready(function() 
    {	
    	$('#formStep1').submit(checkoutSubmitHandler);
        
        jQuery.getScript('http://www.geoplugin.net/javascript.gp', function()
        {
        	var continent = geoplugin_continentCode();
        
	        if(continent == 'EU')
	        {
		        $.cookieCuttr({
		        	cookieDiscreetLink: true,
		        	cookieDiscreetPosition: "topright",
		        	cookiePolicyLink: "/?showFullMessage=true",
		        });
	        }
	        
	        if ((jQuery.cookie('cc_cookie_accept') == "cc_cookie_accept") || (jQuery.cookie('cc_cookie_decline') == "cc_cookie_decline")) {
	        	$('.cc-cookies').hide();
	        }
    	});
        
        //Sticky header
        $("header").sticky({
        	topSpacing:0,
        	wrapperClassName: 'headerStickyWrapper'
        });
        
        //Back to top
        var offset = 220;
        var duration = 500;
        jQuery(window).scroll(function() {
            if (jQuery(this).scrollTop() > offset) {
                jQuery('.back-to-top').fadeIn(duration);
            } else {
                jQuery('.back-to-top').fadeOut(duration);
            }
        });
        
        jQuery('.back-to-top').click(function(event) {
            event.preventDefault();
            jQuery('html, body').animate({scrollTop: 0}, duration);
            return false;
        });
        
        // Image swap on hover
        $("#gallery img").click(function(){
            $('#mainImage').attr('src',$(this).data('mediumImg'));
            $('#mainImage').data('zoomImage', $(this).data('bigImg'));
			$("#mainImage").elevateZoom();
        });
        
        $("#mainImage").elevateZoom();
        
        $('.chentesDescription .chenteButton').click(function(e)
        {
        	e.preventDefault();
        	
        	$('.chentesDescription .description').toggle();
        });
        
        // Products change
        $("#productsList img").click(function(e)
        {
        	e.preventDefault();
        	var productId = $(this).data('productId');
        	
            $('.productBox').hide();
            $('.productBox_'+productId).show();
        });
        
        //Ajax product add to cart
        var sending = false;
        $(".formAddToCart").submit(function(e)
        {        	
        	e.preventDefault();
        	if(sending == false)
        	{
        		//Loading image (using spin.js)
        		var opts = {
        			lines: 11, length: 5, width: 4, radius: 3, corners: 1, rotate: 42, direction: 1, color: '#000', speed: 1.8,
        			trail: 54, shadow: false, hwaccel: false, className: 'spinner', zIndex: 209, top: '50%', left: '85%' 
        		};
        		var target = $(this).find('p').get(0);
    			var spinner = new Spinner(opts).spin(target);
    			
        		sending = true;
        		
        		//Send the ajax request
	        	$.ajax({
	        	  url: $(this).attr("action"),
	        	  type:"POST",
	        	  data: $(this).serialize(),
	        	  success: function(data, textStatus, xhr) 
	        	  {  
	        		  $('.shoppingCart .description .totalProd .totalCant a').html('('+ data.cart.quantity + ')');
	        		  $('.shoppingCart .description .totalProd .totalPrice a').html(data.cart.total);
	        		  
	        		  spinner.stop();
	                  sending = false;
	              }
	        	});
        	} //cierra if
        });
        $(".formAddToCartShow").submit(function(e)
        {        	
        	e.preventDefault();
        	if(sending == false)
        	{
        		//Loading image (using spin.js)
        		var opts = {
        			lines: 11, length: 5, width: 4, radius: 3, corners: 1, rotate: 42, direction: 1, color: '#000', speed: 1.8,
        			trail: 54, shadow: false, hwaccel: false, className: 'spinner', zIndex: 209, top: '94%', left: '42%' 
        		};
        		var target = $(this).find('p').get(0);
    			var spinner = new Spinner(opts).spin(target);
    			
        		sending = true;
        		
        		//Send the ajax request
	        	$.ajax({
	        	  url: $(this).attr("action"),
	        	  type:"POST",
	        	  data: $(this).serialize(),
	        	  success: function(data, textStatus, xhr) 
	        	  {  
	        		  $('.shoppingCart .description .totalProd .totalCant a').html('('+ data.cart.quantity + ')');
	        		  $('.shoppingCart .description .totalProd .totalPrice a').html(data.cart.total);
	        		  
	        		  spinner.stop();
	                  sending = false;
	              }
	        	});
        	} //cierra if
        });        
        //popup con el formulario de contacto
    	$('.costumerFormLink').magnificPopup({
		  callbacks: {
		    open: function() {
		    	$('.contentCostumerForm form').show();
		    	$('.contentCostumerForm .sended').hide();
		    	$('.contentCostumerForm form')[0].reset();
		    },
		  },
  		  type:'inline',
  		  items: {
  			  src: '#costumerFormPopup',
  			  type: 'inline'
  		  }
    	});
    	var sendingCostumerForm = false;
    	$("#costumerFormPopup form").validate(
    	{
			onkeyup: false,
			onclick: false,
			onfocusout: false,
			errorPlacement: function(error, element) 
			{
			},
			highlight: function(element, errorClass, validClass) 
			{
			    $(element).addClass(errorClass).removeClass(validClass);
			},
			unhighlight: function(element, errorClass, validClass) 
			{
			    $(element).removeClass(errorClass).addClass(validClass);
			},
			invalidHandler: function(event, validator)
			{
				//alert("Debes completar todos los campos correctamente para continuar.");
			},
			submitHandler: function(form)
			{	
				if(sendingCostumerForm == false)
	        	{
					sendingCostumerForm = true;
		        	$.ajax({
			        	  url: $(form).attr("action"),
			        	  type: "POST",
			        	  data: $(form).serialize(),
			        	  success: function(data, textStatus, xhr) 
			        	  {  
			        		  $('.contentCostumerForm form').hide();
			        		  $('.contentCostumerForm .sended').show();
			              },
			              complete: function(jqXHR,textStatus)
			              {
			            	  sendingCostumerForm = false;
			              }
			        });
	        	}
			}
    			
    	});
    	
    	//Subscription popup
    	
    	$('.subscribe > a').magnificPopup({
    		type: 'ajax',
    		alignTop: true,
    		overflowY: 'scroll', // as we know that popup content is tall we set scroll overflow by default to avoid jump
    		callbacks: {
    			ajaxContentAdded: function()
    			{
    				$('.subscription-popup .changeContent').click(function(e)
			    	{
			    		e.preventDefault();
			    		
			    		var nextContent = $(this).data('content');
			    		$('.subscription-popup .content').addClass('hide');
			    		$('.subscription-popup .'+nextContent).removeClass('hide');
			    	});
    				
    				$('select[name="sylius_cart_item[variant][cintura]"]').closest('.form-group').hide();
    				
    				$('select[name="sylius_cart_item[variant][sexo]"]').change(function(e)
    				{
    					if($(this).val() == 1) //if is Mujer
    					{
    						$('select[name="sylius_cart_item[variant][cintura]"]').closest('.form-group').show();
    					}else {
    						$('select[name="sylius_cart_item[variant][cintura]"]').closest('.form-group').hide();	
    					}
    				});
    			}
    		}
    	});
    	
    	//Services popup
    	$('.services > a').magnificPopup({
    		type: 'ajax',
    		alignTop: true,
    		overflowY: 'scroll', // as we know that popup content is tall we set scroll overflow by default to avoid jump
    		callbacks: {
    			ajaxContentAdded: function()
    			{
    				var sendingServicesForm = false;
    				$('.services-popup form').submit(function(e)
    				{
    					e.preventDefault();
    					
    					if(!sendingServicesForm)
    					{
	    					sendingServicesForm = true;
	    					
		    				$.ajax({
					        	  url: $(this).attr("action"),
					        	  type: "POST",
					        	  data: $(this).serialize(),
					        	  success: function(data, textStatus, xhr) 
					        	  {  
					        		  $('.services-popup form').hide();
					        		  $('.services-popup .thanks').removeClass('hide');
					              },
					              complete: function(jqXHR,textStatus)
					              {
					            	  sendingServicesForm = false;
					              }
					        });
    					}
    				});
    			}
    		}
    	});
    	
    	//Show new products pages
    	$(window).on("scroll", function() {
    		var footerHeight = $('footer').height() + 500;
    		var scrollHeight = $(document).height();
    		var scrollPosition = $(window).height() + $(window).scrollTop() + footerHeight;
    		if ((scrollHeight - scrollPosition) / scrollHeight <= 0) {
    			var $change = false;
    			$('.page').each(function(index){
    				if($(this).css('display') == 'none' && $change == false){
    					$(this).show();
    					$change = true;
    				}
    			});
    		}
    	});
    	// menu dropdown hover
    	$('.navBar .navMenu .dropdown').hover(function(){
    		$(this).addClass('open');
    		}, function(){
    		$(this).removeClass('open');
    	});
    });

})( jQuery );
