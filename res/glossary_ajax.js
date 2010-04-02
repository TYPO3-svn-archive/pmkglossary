$(window).load(function() {

	// Get the baseUrl in case the url is relative.
	var baseUrl = window.location.protocol+'//'+window.location.host+'/';

	$('.tx-pmkglossary-content').css('min-height',$('.tx-pmkglossary-menu').css('height'));

	$(".tx-pmkglossary-menu li a").each(function(i) {
		if (i==0) {
			// Load and set first item active
			var url = $(this).attr('href');
			url = (!url.match(/http[s]?:\/\//)) ? url=baseUrl+url : url;
			$('#tx-pmkglossary-content').load(url,function() {
				if (typeof Shadowbox == 'object') {
					Shadowbox.setup($('#tx-pmkglossary-content').find('img').get());
				}
			});
			$(this).addClass('act');
		}
		$(this).click(function(event) {
			event.preventDefault();

			// Add loading gfx
			$('#tx-pmkglossary-content').html('<div class="tx-pmkglossary-loading"></div>')

			// clear active class from all menu item.
			$(".tx-pmkglossary-menu li a").each(function() {
				$(this).removeClass('act');
			});
			// Set selected menu item active.
			$(this).addClass('act');

			var url = $(this).attr('href');
			url = (!url.match(/http[s]?:\/\//)) ? url=baseUrl+url : url;
			// Load Glossary for selected letter.
			$('#tx-pmkglossary-content').load(url,function() {
				if (typeof Shadowbox == 'object') {
					Shadowbox.setup($('#tx-pmkglossary-content').find('img').get());
				}
			});
			return false;
		})
	})
});
