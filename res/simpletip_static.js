$(window).load(function() {
	$(".vtip").each(function() {
		$(this).simpletip({
			fixed: false,
			offset: [12,12],
			content: $(this).attr('title'),
			onBeforeShow: function(){
				var el = this.getParent();
				// Save title attribute
				$.data(el, 'title', el.attr('title'));
				// Clear title attribute
				el.attr('title','');
			},
			onHide: function(){
				var el = this.getParent();
				// Restore title attribute
				el.attr('title',$.data(el, 'title'));
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
