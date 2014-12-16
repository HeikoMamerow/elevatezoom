jQuery(document).ready(function($) {

	// find all the images that have a class of elevateZoom
	// and set up zoom
	var ezclasses = '.' + ezSettings.ezClass.replace(' ',', .');
	console.log(ezclasses);
	
	$(ezclasses).each(function(){
	
		var zoom_image = $(this).attr('data-zoom-image');
	
		// if image doesn't have a zoom-image check parent	
		if (!zoom_image) {
			zoom_image = $(this).parent().attr('href');
			$(this).parent().attr({ href: '#' });
		}
		
		// got a zoom image...
		if (zoom_image) {
			$(this).attr({ 'data-zoom-image' : zoom_image });
			$(this).elevateZoom( getSettings( this.attributes, {} ));
		}
	
	});

	$(".zoomDisplay").each(function(){

		var extraSettings = {
			'gallery' : $(this).attr('data-gallery'),
			'galleryActiveClass' : 'zoomactive', 
		};

		$(this).elevateZoom( getSettings( this.attributes, extraSettings ) );
	}); 

	// pass images through to fancybox
	$(".zoomDisplay[data-fancybox='yes']").click(function(e) {  
		e.preventDefault();
  		var ez = $(this).data('elevateZoom');
  		//ez.closeAll(); 	
		$.fancybox(ez.getGalleryList());
  		return false;
	}); 

	function getSettings( p_attrs, p_extra ) {

		var p_settings = ezSettings;

		// add/set defaults
		for ( setting in p_extra) {
    			p_settings[setting] = p_extra[setting];
		}

		// add/set attributes (data-*)
		$.each(p_attrs, function() {
    			// this.attributes is not a plain object, but an array
    			// of attribute nodes, which contain both the name and value
    			if(this.specified) {
    				
				if ( this.name.substring(0,5) == 'data-' && this.name != 'data-zoom-image' ) {

					var name_bits = this.name.split('-');
					var setting_name = name_bits[1];

					for (var i=2; i < name_bits.length; i++) { 
						setting_name = setting_name + name_bits[i].substr(0,1).toUpperCase() + name_bits[i].substr(1);
					}

    					var setting_value = this.value;
    					
    					p_settings[setting_name] = setting_value;
    					console.log(setting_name, setting_value);
    				}
    			}
  		}); 

		return p_settings;

	}
	
});