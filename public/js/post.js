$(document).ready(function(){
    $('#post').keyup(function(){
        if ($(this).val().length != 0) {
            $("#post-btn").removeAttr("disabled");
            $("#post-btn").css("background-color","rgb(179, 120, 120)");
        }
        else {
            $("#post-btn").attr("disabled","disabled");
            $("#post-btn").css("background-color","rgb(216, 146, 146)");
        }
    });
});

function callAddPost() {
    var post = $("#post").val();
    var id = $("#uid1").text();
    var post = post.replace(/<[^>]+>/gim, '');
    $.ajax({
        type: 'POST',
        url: '/addPost',
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
            document.getElementById("post").value = "";
            $("#post-btn").attr("disabled",true);
            $("#post-btn").css("background-color","rgb(216, 146, 146)");
            $(".loader").css("display","none");
        },
        error: function(error) {
            $(".loader").css("display","none");
            console.log(error);
        }
    });
}

function addPost() {
    var pusher = new Pusher('913b70eb80f62f270cf5', {
        cluster: 'ap2'
    });
    
    var channel = pusher.subscribe('demo_pusher');
    channel.bind('addName', function(data) {
        console.log(data);
        const activeUsersList = document.querySelector('.post-area');
        const divItem = document.createElement('div');
        divItem.className = 'posts';
        divItem.idName = 'post'+data.postid;
        divItem.innerHTML = `
        <div class="inner-post">
            <div class="userdetails">
                <i class="fa-regular fa-user"></i>
                <p class="uname">${ data.userId }</p>
            </div>
            <div class="menu" id="menu-${data.postid}">
                <i id="menubar-${data.postid}" onclick="showMenu(this.id)" class="menubar fa-solid fa-bars"></i>
                <i id="cross-${data.postid}" onclick="showMenu(this.id)" class="cross fa-sharp fa-solid fa-circle-xmark"></i>
                <ul class="menu-list">
                    <li id="edit-${data.postid}" onclick="editPost(this.id)">Edit Post</li>
                    <li id="delete-${data.postid}" onclick="deletePost(this.id)">Delete Post</li>
                </ul>
            </div>
        </div>
        <p>${ data.post }</p>
        <div class="bottom-post">
            <div class="like">
                <p>0</p>
                <i id=${ data.postid } class="likebtn fa-solid fa-thumbs-up"></i>
            </div>
            <p class="cmmnt" id="showComment">0 Comments</p>
            </div>
        <button onclick="show(this.id)" class="cmmntt" id="comment1">Comments</button>
        <div class="comment-section">
            <div class="comment comment${data.postid}">
            </div>
            <div class="formcomment">
                <input class='commtexttt' type="text" required id="commtext" name="comment" placeholder="Enter your comment here.....">
                <input onclick="addcomm(this.id)" type="submit" id="comm-${ data.postid }" class="cmmnt-btn" name="cmmntbtn" value="Comment">
            </div>
        </div>
        `;
        activeUsersList.prepend(divItem);   
    });
}

function show(id) {
    var b = document.getElementById(id).nextElementSibling;
    if (b.style.display == "block") {
        b.style.display = "none";
    }
    else {
        b.style.display = "block";
    }
}

addPost();

function loadLike() {
    var uName = document.getElementById("uid1").innerHTML;
    $.ajax({
        type: 'POST',
        url: '/getLikes',
        data: 
        {
            username: uName,
        },
        beforeSend: function () {
            $('.loader').css("display","flex");
        },
        dataType: "text",
        success: function(response) {
            $(".loader").css("display","none");
            if (response) {
                response = JSON.parse(response);
                response.likes.forEach(element => {
                    $('#'+element).css("color","blue");
                });
            }
        }
    });
}

$(window).on("load", function(){
    loadLike();
});

function showMenu(id) {
    // $("#"+id).siblings(".menu-list").slideToggle();
    if ($("#"+id).siblings(".menu-list").css("display") == "block") {
        $("#"+id).siblings(".menu-list").css("display","none");
    }
    else {
        $("#"+id).siblings(".menu-list").css("display","block");
    }

    if ($("#"+id).css('display') == 'block') {
        $("#"+id).css("display","none");
        $("#"+id).siblings(".cross").css("display","block");
        $("#"+id).siblings(".menubar").css("display","block");
    }
}

function deletePost(id) {
    var comm = id.indexOf("-");
    var id = id.substring(++comm);
    $.ajax({
        type: 'POST',
        url: '/deletePost',
        data: 
        {
            id: id,
        },
        beforeSend: function () {
            $('.loader').css("display","flex");
        },
        dataType: "text",
        success: function(response) {
            console.log(response);
            $('#'+response).remove();
            $(".loader").css("display","none");
        }
    });
}

function delPost() {
    var pusher = new Pusher('913b70eb80f62f270cf5', {
        cluster: 'ap2'
    });
    
    var channel = pusher.subscribe('demo_pusher');
    channel.bind('deletepost', function(data) {
        console.log(data);
        $('#'+data).remove();
    });
}

delPost();