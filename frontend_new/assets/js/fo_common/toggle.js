function windowSize() {
	// ------------
	var toggleItemH = $('.more_toggle .hidden-some-main ul li.item').height();
	var toggleItemH2 = toggleItemH*2;
	var toggleItemH6 = (toggleItemH + 10)*6;
	// var toggleItemL = $('.more_toggle .hidden-some-main ul li.item').length;
	// var toggleItemLH = toggleItemH*toggleItemL;

	console.log(toggleItemH);
	console.log(toggleItemH2);
	console.log(toggleItemH6);
	// console.log(toggleItemL);
	// console.log(toggleItemLH);
	
	if ($(window).width() < 768) {
		$('.more_toggle .hidden-some-main').css('height',toggleItemH6);
		$('.more_toggle .more-btn .show-more-btn').show();
		$('.more_toggle .more-btn .hide-more-btn').hide();
		// Mobile
		$(".more_toggle .more-btn .show-more-btn").click(function(){
			$('.more_toggle .hidden-some-main').css('height','auto');
			$('.more_toggle .more-btn .hide-more-btn').show();
			$('.more_toggle .more-btn .show-more-btn').hide();
		});
		$(".more_toggle .more-btn .hide-more-btn").click(function(){
			$('.more_toggle .hidden-some-main').css('height',toggleItemH6);
			$('.more_toggle .more-btn .show-more-btn').show();
			$('.more_toggle .more-btn .hide-more-btn').hide();
		});
	} else {
		$('.more_toggle .hidden-some-main').css('height',toggleItemH2);
		$('.more_toggle .more-btn .show-more-btn').show();
		$('.more_toggle .more-btn .hide-more-btn').hide();
		// Desktop
		$(".more_toggle .more-btn .show-more-btn").click(function(){
			$('.more_toggle .hidden-some-main').css('height','auto');
			$('.more_toggle .more-btn .hide-more-btn').show();
			$('.more_toggle .more-btn .show-more-btn').hide();
		});
		$(".more_toggle .more-btn .hide-more-btn").click(function(){
			$('.more_toggle .hidden-some-main').css('height',toggleItemH2);
			$('.more_toggle .more-btn .show-more-btn').show();
			$('.more_toggle .more-btn .hide-more-btn').hide();
		});
	}
};
$(window).resize(function() {
    windowSize();
});
windowSize();
