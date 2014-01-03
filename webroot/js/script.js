$(document).ready(function() {
	$('.logfilecontent-more-toggle').each(function() {
		$(this).click(function(e) {
			console.log('CLICKED');
			e.preventDefault();
			
			var $this = $(this),
				targetId = $this.data('more-toggle-target'),
				$target = $('#' + targetId);
			console.log(targetId);
			if ($target.length) {
				if ($target.is(':hidden')) {
					$target.slideDown();
				} else {
					$target.slideUp();
				}
			}
		});
	});
});