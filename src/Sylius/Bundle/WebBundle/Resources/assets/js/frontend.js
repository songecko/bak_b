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
    $.ajax({
        type: $(this).attr('method'),
        url: $(this).attr('action'),
        data: $(this).serialize(),
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
        	else if(data.indexOf('formStep3') >=0)
        	{
        		step = 3;
        	}
        	else
        	{
        		step = 4;
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

        $('.sylius-different-billing-address-trigger').click(function() {
            $('#sylius-billing-address-container').toggleClass('hidden');
        });
        
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
			//$("#mainImage").elevateZoom();
        });
        
        //$("#mainImage").elevateZoom();
        
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
        var sending = false;
        $(".formAddToCart").submit(function(e)
        {        	
        	e.preventDefault();
        	if(sending == false)
        	{
        		var opts = {
        				  lines: 11, // The number of lines to draw
        				  length: 5, // The length of each line
        				  width: 4, // The line thickness
        				  radius: 3, // The radius of the inner circle
        				  corners: 1, // Corner roundness (0..1)
        				  rotate: 42, // The rotation offset
        				  direction: 1, // 1: clockwise, -1: counterclockwise
        				  color: '#000', // #rgb or #rrggbb or array of colors
        				  speed: 1.8, // Rounds per second
        				  trail: 54, // Afterglow percentage
        				  shadow: false, // Whether to render a shadow
        				  hwaccel: false, // Whether to use hardware acceleration
        				  className: 'spinner', // The CSS class to assign to the spinner
        				  zIndex: 2e9, // The z-index (defaults to 2000000000)
        				  top: '50%', // Top position relative to parent
        				  left: '85%%' // Left position relative to parent
        				};
    				var target = $(this).find('p').get(0);
    				var spinner = new Spinner(opts).spin(target);
        		sending = true;
	        	$.ajax({
	        	  url: $(this).attr("action"),
	        	  type:"POST",
	        	  data: $(this).serialize(),
	        	  success: function(data, textStatus, xhr) {
	        		  
	        		  $('.shoppingCart .description .totalProd .totalCant a').html('('+ data.cart.quantity + ')');
	        		  $('.shoppingCart .description .totalProd .totalPrice a').html(data.cart.total);
	                  alert(data.status); alert(data.cart.quantity); alert(data.cart.total);
	                  sending = false;
	                  spinner.stop();
	              }
	        	});
        	} //cierra if
        });
    });

})( jQuery );
