(function($)
{
    // copied from plugin source
    $(document).on('click', '#wpdf_save_keys', function(e) {
        e.preventDefault(); 
        
        if( !$('#flickr_appid').val()) {return false;} 

        var flickrapikey = $('#flickr_appid').val();

        $('#wpdf_save_keys_form').html('<img src="' + wpdf_plugin_url + '/images/ajax-loader.gif" style="width: 16px; height: 16px;margin-bottom: -2px;" /></span>');          
        
        var data = {
            action: 'wpdf_save_keys',
            wpnonce: wpdf_security_nonce.security,
            flickrapi: flickrapikey
        };

        $.ajax ({
            type: 'POST',
            url: ajaxurl,
            data: data,
            dataType: 'json',
            success: function(response) {
                if(response.error != undefined && response.error != "") {
                    $('#wpdf_save_keys_form').html(response.error);
                } else {
                    $('#wpdf_save_keys_form').remove();
                    $('#wpdf_main').show();
                    $('#module-flickr').prop('disabled', false).next().replaceWith('Flickr')
                }
            }
        });         

        return false;           
    });

    if ( 'FLICKR_APP_ID_HTML' in window && FLICKR_APP_ID_HTML ) {
        $(document).ready(function()
        {
            $('#wpdf_main').append(FLICKR_APP_ID_HTML)
        })
    }

})( jQuery )