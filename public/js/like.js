$(function() {
    $(".likebtn").click(function() {
        var uName = $('#uid1').text();
        if (this.style.color != "blue") {
            $(this).css("color", "blue");
            var isLiked = "YES";
            var likeNo = $(this).siblings().text();
            var a = Number(likeNo);
            $(this).siblings().html(++a);
        }
        else {
            $(this).css("color", "black");
            var likeNo = $(this).siblings().text();
            var a = Number(likeNo);
            $(this).siblings().html(--a);
            var isLiked = "NO";
        }
        $.ajax({
            type: 'POST',
            url: '/likes',
            data: 
            {
                username: uName,
                postid: this.id,
                like: isLiked
            },
            dataType: "text",
            success: function(response) {
                console.log(response);
            }
        });
    });
});

function updateLike() {
    var pusher = new Pusher('5c90cb34f6af626fc27b', {
        cluster: 'ap2'
    });
    
    var channel = pusher.subscribe('demo_pusher');
    channel.bind('updateLike', function(data) {
        document.getElementById(data.post).previousElementSibling.innerHTML = data.like;
    });
}

updateLike();