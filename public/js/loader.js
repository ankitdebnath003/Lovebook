function loadLoader() {
    $(".loader").css("display","flex");
}

$(window).on("load", function() {
    $(".loader").css("display","none");
});

loadLoader();