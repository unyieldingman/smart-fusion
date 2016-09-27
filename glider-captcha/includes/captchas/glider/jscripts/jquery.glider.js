/**
	jQuery Powered Plug-in
	[jquery.glider.js]
	
	@version		1.0.0
	@entrypoint		glider
	@pubdate		03/18/2014 9:34PM
	@developer		FDTD Designer (FILON)
	
	Licensed under the MIT Licence
	http://www.opensource.org/licenses/mit-license.php
*/


var Glider = {
	Init: function(obj, hash, options){
		var defaults = {
				theme: 'silverglide',
				width: 296,
				locale: [
					'Slide right to continue',
					'Keep movement',
					'You are not a bot!'
				]
			},
			system = {
				dragKeeping: false,
				keepState: 0,
				reverseState: 0,
				actTerm: {
					state: 1,
					value: 0
				},
				cookieName: 'gl_token'
			};
		$.cookie(system.cookieName, null);
		var settings = $.extend(defaults, options);
		$(obj).each(function(){
			// Simple checking
			if (settings.width < 100) { settings.width = 296; }
			
			var	self = $(this),
				slidebar = $('<div>', {'class': 'glide-surface', 'width': settings.width}),
				glider = $('<div>', {'class': 'glide-craft'}),
				terminal = $('<div>', {'class': 'glide-terminal'}),
				arrow = $('<span>');
			
			// Build DOM-structure
			self.addClass('glider');
			slidebar.addClass(settings.theme).appendTo(self);
			glider.appendTo(slidebar);
			terminal.appendTo(slidebar);
			arrow.appendTo(glider);
			
			// Show terminal's start message
			terminal.text(settings.locale[0]);
			
			// Initialize UI methods
			glider.draggable({
				axis: 'x',
				containment: slidebar,
				revert: function(){
					system.reverseState = !(parseInt(glider.css('left')) >= (slidebar.width() - glider.outerWidth(true)));
					return system.actTerm.state & system.reverseState;
				},
				drag: function(event, ui){
					if (ui.position.left > 0 && !system.dragKeeping) {
						system.dragKeeping = true; system.keepState = 1;
						terminal.fadeOut(150, function(){
							$(this).text(settings.locale[1]).fadeIn(200);
						});
					} else if (ui.position.left == 0 && system.keepState == 1) {
						system.dragKeeping = false; system.keepState = 0;
						terminal.fadeOut(150, function(){
							$(this).text(settings.locale[0]).fadeIn(200);
						});
					} else if (system.keepState == 1 && ui.position.left == (slidebar.width() - glider.outerWidth(true))) {
						system.keepState = 0; system.actTerm.state = 0; system.actTerm.value = ui.position.left;
						glider.draggable('disable').css('cursor', 'default');
						terminal.fadeOut(150, function(){
							$(this).text(settings.locale[2]).addClass('finish').fadeIn(200);
						});
					}
				},
				stop: function(event, ui){
					if (system.reverseState) {
						terminal.fadeOut(150, function(){
							$(this).text(settings.locale[0]).fadeIn(200);
							system.reverseState = 0;
						});
					}
					if (system.actTerm.state == 0) {
						glider.css({left: system.actTerm.value});
						$.cookie(system.cookieName, hash);
					}
				}
			});
		});
	}
};