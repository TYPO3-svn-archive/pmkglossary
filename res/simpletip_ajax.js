$(window).load(function() {

	// Get the baseUrl in case the url is relative.
	var baseUrl = window.location.protocol+'//'+window.location.host+'/';

	$(".vtip").each(function() {
		$(this).simpletip({
			fixed: false,
			offset: [12,12],
			content: '<div class="tiploading"></div>',
			onBeforeShow: function(){
				var el = this.getParent();
				if (el.attr('title')) {
					// Load AJAX based tooltip.
					this.load(baseUrl+el.attr('title'));
					// Clear title attribute
					el.attr('title','');
				}
			}
		});
	});
});

/* Fix for IE Cleartype/Filter problems */
jQuery.fn.fadeIn = function(speed, callback) {
     return this.animate({opacity: 'show'}, speed, function() {
         if (jQuery.browser.msie)
             this.style.removeAttribute('filter');
         if (jQuery.isFunction(callback))
             callback();
     });
 };
 jQuery.fn.fadeOut = function(speed, callback) {
     return this.animate({opacity: 'hide'}, speed, function() {
         if (jQuery.browser.msie)
             this.style.removeAttribute('filter');
         if (jQuery.isFunction(callback))
             callback();
     });
 };
 jQuery.fn.fadeTo = function(speed,to,callback) {
    return this.animate({opacity: to}, speed, function() {
        if (to == 1 && jQuery.browser.msie)
            this.style.removeAttribute('filter');
        if (jQuery.isFunction(callback))
            callback();
    });
};

