<?php ?>

<script type="text/javascript">
	jQuery(document).ready(function() {
		// SHOW EXTRA OPTIONS
		jQuery('#button_type').on('change', function(i, v) {
			jQuery('tr.do_not_show').toggleClass('do_show');
		});

		// GET IMAGE
	    if (jQuery('.set_custom_images').length > 0) {
	        if ( typeof wp !== 'undefined' && wp.media && wp.media.editor) {
	            jQuery(document).on('click', '.set_custom_images', function(e) {
	                e.preventDefault();
	                var button = jQuery(this);
	                var id = button.prev();
	                wp.media.editor.send.attachment = function(props, attachment) {
	                    id.val(attachment.url);

	                    jQuery('#wpts_thumbnail').attr('src',attachment.url).removeClass('not_visible');
	                };
	                wp.media.editor.open(button);
	                return false;
	            });
	        }
	    }

	    // VALIDATE FORM
	    jQuery('button[data-action="validate"]').on('click', function() {
	    	var to_submit = jQuery(this).attr('data-to-submit');
	    	var err = 0;
	    	var button_type = jQuery('#button_type').val();
	    	jQuery('.validate').removeClass('invalid');
	    	jQuery('.validate').each(function() {
	    		if(jQuery(this).val() == '' || (jQuery(this).attr('type') == 'number' && !jQuery.isNumeric(jQuery(this).val()))	) {
	    			
	    			// Add error
	    			err++;
		    		jQuery(this).addClass('invalid');

		    		// Exception for product name
	    			if(button_type == 'oneoff' && jQuery(this).attr('name') == 'button_product_name') {
	    				err--;
	    			}

	    			console.log(jQuery(this).attr('name'));
	    			
	    		}
	    	});

	    	if(err < 1) {
    			jQuery('#'+to_submit).submit();
    		}
	    });
	    
	});
</script>

<?php ?>