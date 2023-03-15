function cross() {
    $(".popup").css("display","none");
}

function editPost(id) {
    console.log(id);
    var comm = id.indexOf("-");
    var id = id.substring(++comm);
    $(".popup").css("display","flex");
    $("#newposts").click(function() {
        var post = $("#newPost").val();
        console.log(post+id);
        $.ajax({
            type: 'POST',
            url: '/editPost',
            data: 
            {
                id: id,
                post: post
            },
            beforeSend: function () {
                $('.loader').css("display","flex");
            },
            dataType: "text",
            success: function(response) {
                console.log(response);
                $(".popup").css("display","none");
                $(".loader").css("display","none");
                $('#cross-'+id).css("display","none");
                $('#menubar-'+id).css("display","block");
                $('#cross-'+id).siblings('.menu-list').css("display","none");
            }
        });
    });
}

function editPostLive() {
    var pusher = new Pusher('913b70eb80f62f270cf5', {
        cluster: 'ap2'
    });
    
    var channel = pusher.subscribe('demo_pusher');
    channel.bind('editpost', function(data) {
        console.log(data);
        // console.log($('#'+data.id).children('p').text());
        $('#'+data.id).children('p').text(data.text);
    });
}
setInterval(editPostLive, 1000);