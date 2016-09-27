(function($){
	$.fn.megamenu = function() {
		var predefines = {
			screenWidth: $(window).width() || document.body.clientWidth
		};
		return this.each(function(){
			var wide_size;
			$(this).find('.drop-down').each(function(){
				wide_size = 0;
				$(this).children('ul').each(function(i){
					wide_size += $(this).outerWidth(true);
				});
				$(this).width(wide_size);
				var dropdown = $(this);
				dropdown.addClass(dropdown.parent().offset().left > predefines.screenWidth - dropdown.outerWidth(true) ? 'right' : 'left');
				dropdown.parent().mouseenter(function(){
					dropdown.stop().fadeIn(300).parent().addClass('dropped');
				}).mouseleave(function(){
					dropdown.stop().fadeOut(200).parent().removeClass('dropped');
				});
			});
		});
	};
})(jQuery);

$(function(){
	$('#mega_menu').megamenu();
});