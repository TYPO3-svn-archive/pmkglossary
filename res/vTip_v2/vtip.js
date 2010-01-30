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
/**
Vertigo Tip by www.vertigo-project.com
Requires jQuery
*/

this.vtip = function() {
    this.xOffset = -10; // x distance from mouse
    this.yOffset = 10; // y distance from mouse

    $(".vtip").unbind().hover(
        function(e) {
            this.t = this.title;
            this.title = '';
            this.top = (e.pageY + yOffset); this.left = (e.pageX + xOffset);

            $('body').append( '<div id="vtip"><img id="vtipArrow" />' + this.t + '</div>' );

            //$('p#vtip #vtipArrow').attr("src", 'images/vtip_arrow.png');
            $('div#vtip #vtipArrow').attr("src", 'typo3conf/ext/pmkglossary/res/vTip_v2/images/vtip_arrow.png');
            $('div#vtip').css("top", this.top+"px").css("left", this.left+"px").fadeIn("slow");

        },
        function() {
            this.title = this.t;
            $("div#vtip").fadeOut("slow").remove();
        }
    ).mousemove(
        function(e) {
            this.top = (e.pageY + yOffset);
            this.left = (e.pageX + xOffset);

            $("div#vtip").css("top", this.top+"px").css("left", this.left+"px");
        }
    );

};

jQuery(document).ready(function($){vtip();})