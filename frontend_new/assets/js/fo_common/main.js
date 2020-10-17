function windowSize() {
    if ($(window).width() > 768) {
        // Drop Down Menu
        $(".drop-down-menu").click(function() {
            $(this).toggleClass("active");
            $(".btn_a").attr("href", ""); 
        });
    }
    // Float Menu and Top Button - QR Code
    $(".open-qrcode").click(function() {
        $(this).toggleClass("active");
    });

    if ($(window).width() < 768) {
    $(".btn_a").attr("href", "https://www.app19.app/"); 
    };
};
$(window).resize(function() {
    windowSize();
});
windowSize();

// $(".drop-down-menu").click(function() {
// 	$(this).toggleClass("active");
// });