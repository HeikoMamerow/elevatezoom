jQuery(document).ready(function($) {

	// find all the images that have a class of elevateZoom
	// and set up zoom
	var ezclasses = '.' + ezSettings.ez_class.replace(' ',', .');
	console.log(ezclasses);
	
	$(ezclasses).each(function(){
	
		var zoom_image = $(this).attr('ez-zoom-image');
		
		if (!zoom_image) zoom_image = $(this).parent().attr('href');
		
		if (zoom_image) {
		
			var localSettings = ezSettings;
		
			$.each(this.attributes, function() {
    			// this.attributes is not a plain object, but an array
    			// of attribute nodes, which contain both the name and value
    			if(this.specified) {
    				if ( this.name.substring(0,3) == 'ez-' ) {
    					var setting_name_bits = this.name.substring(3).split('-');
    					var setting_name = setting_name_bits[0];
    					
    					for (i=1; i<setting_name_bits.length; i++) {
    						setting_name = setting_name + setting_name_bits[i].substring(0,1).toUpperCase() + setting_name_bits[i].substring(1);
    					}
    					
    					var setting_value = this.value;
    					
    					localSettings[setting_name] = setting_value;
    					console.log(setting_name, setting_value);
    				}
    			}
  			});

			$(this).parent().attr({ href: '#' });
			$(this).attr({ 'data-zoom-image' : zoom_image });
			$(this).elevateZoom( localSettings );
		
		}
	
	});
	
});