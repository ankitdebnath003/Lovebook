function cross() {
    $(".popup").css("display","none");
}

function newpost(id) {
    var post = $("#newPost").val();
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
            $("#newPost").val('');
            $(".popup").css("display","none");
            $(".loader").css("display","none");
            $('#cross-'+id).css("display","none");
            $('#menubar-'+id).css("display","block");
            $('#cross-'+id).siblings('.menu-list').css("display","none");
        }
    });
}

function editPost(id) {
    console.log(id);
    var comm = id.indexOf("-");
    var id = id.substring(++comm);
    $(".newposts").attr('id', id);
    $(".popup").css("display","flex");
}

function editPostLive() {
    var pusher = new Pusher('5c90cb34f6af626fc27b', {
        cluster: 'ap2'
    });
    
    var channel = pusher.subscribe('demo_pusher');
    channel.bind('editpost', function(data) {
        console.log(data);
        $('#'+data.id).children('p').text(data.text);
    });
}
editPostLive();