$(window).load(function() {
	var url = window.location.href;
	url += url.indexOf('?')==-1 ? '?' : '&';
	$(".vtip").each(function() {
		$(this).simpletip({
			fixed: false,
			offset: [10,10],
			content: '<div class="tiploading"></div>',
			//content: $(this).attr('title'),
			onBeforeShow: function(){
				var el = this.getParent();
				// Save title attribute
				$.data(el, 'title', el.attr('title'));
				// Clear title attribute
				el.attr('title','');

				if (el.attr('lang')) {
						// Load AJAX based tooltip.
					//this.load('index.php?eID=pmkglossary&uid='+parseInt(el.attr('lang')));
					//this.load('index.php?type=52&tx_pmkglossary_pi1[glossary]='+parseInt(el.attr('lang'));
					this.load(url+'type=52&tx_pmkglossary_pi1[glossary]='+parseInt(el.attr('lang')));
				}
			},
			onHide: function(){
				var el = this.getParent();
				if (el.attr('lang')) {
					// Clear lang attribute
					el.attr('lang','');
				}
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
 jQuery.fn.fadeTo = function(speed,to,callback) {
    return this.animate({opacity: to}, speed, function() {
        if (to == 1 && jQuery.browser.msie)
            this.style.removeAttribute('filter');
        if (jQuery.isFunction(callback))
            callback();
    });
};

