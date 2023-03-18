// For actual html page.
$(function() {
    $(".cmmnt-btn").click(function() {
        var id = this.getAttribute("commentid");
        var comment = $(this).siblings().val();
        var uName = $('#uid1').text();
        $.ajax({
            type: 'POST',
            url: '/addComment',
            data: 
            {
                postid: id,
                comment: comment,
                uname: uName
            },
            beforeSend: function () {
                $('.loader').css("display","flex");
            },
            dataType: "text",
            success: function(response) {
                console.log(response);
                $('.commtexttt').val('');
                $('.loader').css("display","none");
            }
        });
    });
});

function addComment() {
    var pusher = new Pusher('5c90cb34f6af626fc27b', {
        cluster: 'ap2'
    });
    
    var channel = pusher.subscribe('demo_pusher');
    channel.bind('add', function(data) {
        $('#'+data.postid).parent().siblings('.cmmnt').text(data.commentno+' Comments');
        const activeUsersList = document.querySelector('.comment'+data.postid);
        const divItem = document.createElement('div');
        divItem.className = 'inner-comment';
        divItem.innerHTML = `
        <div class="like">
            <i class="fa-regular fa-user"></i>
            <p class="uname">${ data.username }</p>
        </div>
        <p>${ data.comment }</p>
        `;
        activeUsersList.prepend(divItem);   
    });
}
addComment();

$(document).ready(function() {
    $(".cmmntt").click(function(){
        $(this).siblings('.comment-section').slideToggle(function() {
            $(this).is(':visible') ? $(this).siblings('#comment1').val("Hide Comments"): $(this).siblings('#comment1').val("Show Comments");
        });
    });
});

// For dynamically created html.
function addcomm(id) {
    var comment = document.getElementById(id).previousElementSibling.value;
    var uName = $('#uid1').text();
    var comm = id.indexOf("-");
    var id = id.substring(++comm);
    $.ajax({
        type: 'POST',
        url: '/addComment',
        data:
        {
            postid: id,
            comment: comment,
            uname: uName
        },
        beforeSend: function () {
            $('.loader').css("display","flex");
        },
        dataType: "text",
        success: function(response) {
            console.log(response);
            $('.commtexttt').val('');
            $('.loader').css("display","none");
        }
    });
}