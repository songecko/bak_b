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

    });

})( jQuery );
