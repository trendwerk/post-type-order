(function($){
	var TPPTOrder = function(el) {
		var self = this;
		var order = [];
		
		this.init = function() {
			self.setOrder;
		}
		
		this.events = function() {
			$(el).find('ul').sortable({
				axis: 'y',
				stop: function(e,ui) {
					if($(el).data('taxonomy')) {
						var data = {
							action: 'tp_pt_order',
							order: $(this).closest('ul').sortable('toArray'),
							taxonomy: $(el).data('taxonomy'),
							term: $(this).closest('.tp-pt-order-taxonomy').data('term')
						};
					} else {
						var data = {
							action: 'tp_pt_order',
							order: $(this).closest('ul').sortable('toArray')
						};
					}
					
					jQuery.post(ajaxurl,data,function(response) {
						
					});
				}
			});
		}
		
		$(document).ready(function() {
			self.init();
			self.events();
		});
	}
	
	$.fn.TPPTOrder = function() {
		return this.each(function() {
			if($(this).data('TPPTOrder')) return;

			var tp_pt_order = new TPPTOrder(this);
			$(this).data('TPPTOrder',tp_pt_order);
		});
	}
	
	jQuery(document).ready(function($) {
		$('.tp-pt-order').TPPTOrder();
	});
})(jQuery);
