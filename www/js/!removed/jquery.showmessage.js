(function($) {
	$.showMessage = function(options) {
		// To avoid scope issues, use 'base' instead of 'this'
		// to reference this class from internal events and functions.
		var base = this;

		base.init = function() {

			base.options = $.extend({}, $.showMessage.defaultOptions, options);
			
			// Push notification to queue
			if (base.options.layout != 'topLeft' && base.options.layout != 'topRight') {
				if (base.options.force) {
					$.showMessage.queue.unshift({options: base.options});
				} else {
					$.showMessage.queue.push({options: base.options});
				}
				
				base.render();
				$.showMessage.available = false;
				
			} else {
				$.showMessage.available = true;
				base.render({options: base.options});
			}
			
		};
		
		// Render the queue
		base.render = function(showMessage) {
		 
			if ($.showMessage.available) {
				
				// Get showMessage from queue
				var notification = (jQuery.type(showMessage) === 'object') ? showMessage : $.showMessage.queue.shift();
				
				if (jQuery.type(notification) === 'object') {
					
					// Layout spesific container settings
					if (notification.options.layout == "topLeft" || notification.options.layout == "topRight") {
						if ($("ul.showMessage_container."+notification.options.layout).length > 0) {
							base.$showMessage_container = $("ul.showMessage_container."+notification.options.layout);
						} else {
							base.$showMessage_container = $('<ul/>').addClass('showMessage_container').addClass(notification.options.layout);
							$("body").prepend(base.$showMessage_container);
						}
						base.$showMessageContainer = $('<li/>');
						base.$showMessage_container.prepend(base.$showMessageContainer);
						
					} else {
						base.$showMessageContainer = $("body");
					}
					
					base.$bar 		= $('<div/>').addClass('showMessage_bar');
					base.$message = $('<div/>').addClass('showMessage_message');
					base.$text 		= $('<div/>').addClass('showMessage_text');
					base.$close 	= $('<div/>').addClass('showMessage_close');
					
					base.$message.append(base.$text).append(base.$close);
					base.$bar.append(base.$message);
					
					var $showMessage = base.$bar;
					$showMessage.data('showMessage_options', notification.options);
					
					// Basic layout settings
					$showMessage.addClass(notification.options.layout).addClass(notification.options.type).addClass(notification.options.theme);
					
					// Message and style settings
					$showMessage.find('.showMessage_text').html(notification.options.text).css({textAlign: notification.options.textAlign});
					
					// Closable option 
					(notification.options.closable) ? $showMessage.find('.showMessage_close').show() : $showMessage.find('.showMessage_close').remove();
					
					// Bind close event to button 
					$showMessage.find('.showMessage_close').bind('click', function() { $showMessage.trigger('showMessage.close'); });
					
					// Close on self click
					if (notification.options.closeOnSelfClick) {
						$showMessage.find('.showMessage_message').bind('click', function() { $showMessage.trigger('showMessage.close'); }).css('cursor', 'pointer');
					}
					
					// is Modal? 
					if (notification.options.modal) {
						$('<div />').addClass('showMessage_modal').prependTo($('body')).css(notification.options.modalCss).fadeIn('fast');
					}
					
					// Prepend showMessage to container
					base.$showMessageContainer.prepend($showMessage);
					
					// Bind close event
					$showMessage.one('showMessage.close', function(event, callback) {
						var options = $showMessage.data('showMessage_options');
						
						// Modal Cleaning
						if (options.modal) {
							$('.showMessage_modal').fadeOut('fast', function() { $(this).remove(); });
						}
						
						$showMessage.stop().animate(
								$showMessage.data('showMessage_options').animateClose,
								$showMessage.data('showMessage_options').speed,
								$showMessage.data('showMessage_options').easing,
								$showMessage.data('showMessage_options').onClose)
						.promise().done(function() {
							
							// Layout spesific cleaning
							if (options.layout == 'topLeft' || options.layout == 'topRight') {
								$showMessage.parent().remove();
							} else {
								$showMessage.remove();
							}
							
							// Are we have a callback function?
							if ($.isFunction(callback)) {
								callback.apply();
							}
							
							// queue render
							if (options.layout != 'topLeft' && options.layout != 'topRight') {
								$.showMessage.available = true;
								base.render();
							}
						});
					});
					
					// Set buttons if available
					if (notification.options.buttons) {
						$buttons = $('<div/>').addClass('showMessage_buttons');
						$showMessage.find('.showMessage_text').append($buttons);
						
						$.each(notification.options.buttons, function(i, button) {
							bclass = (button.type) ? button.type : 'gray';
							$('<button/>').addClass(bclass).html(button.text).appendTo($showMessage.find('.showMessage_buttons')).one("click", function() { $showMessage.trigger('showMessage.close', button.click); });
						});
					}
					
					// Start the show
					$showMessage.animate(
							notification.options.animateOpen,
							notification.options.speed,
							notification.options.easing,
							notification.options.onShow);
					
					// If showMessage is have a timeout option
					if (notification.options.timeout) {
						$showMessage.delay(notification.options.timeout).promise().done(function() { $showMessage.trigger('showMessage.close'); });
					}
				
				}
		 
			}
		};
		
		// Run initializer
		base.init();
	};
	
	$.showMessage.queue = [];
	
	$.showMessage.clearQueue = function () {
		$.showMessage.queue = [];
	};
	
	$.showMessage.close = function () {
		$('.showMessage_bar:first').trigger('showMessage.close');
	};

	$.showMessage.closeAll = function () {
		$.showMessage.clearQueue();
		$('.showMessage_bar').trigger('showMessage.close');
	};

	$.showMessage.available = true;
	$.showMessage.defaultOptions = {
		layout : "stop",
		theme : "default",
		animateOpen : {height: 'toggle'},
		animateClose : {height: 'toggle'},
		easing : 'swing',
		text : "null",
		textAlign : "center",
		type : "warning",
		speed : 500,
		timeout : 2500,
		closable : true,
		closeOnSelfClick : true,
		force : false,
		onShow : false,
		onClose : false,
		buttons : false,
		modal : false,
		modalCss : {'opacity': 0.6}
	};

	$.fn.showMessage = function(options) {
		return new $.showMessage(options);
	};

})(jQuery);

// Helper
function showMessage(options) {
	jQuery.fn.showMessage(options);
}