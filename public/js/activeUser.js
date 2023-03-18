window.addEventListener('beforeunload', (event) => {
    var id = $("#uid1").text();
    $.ajax({
        type: 'POST',
        url: '/removeActiveUser',
        data: 
        {
            userid: id
        },
        dataType: "text",
        success: function(response) {
            console.log(response);
        }
    });
});

function addPost() {
    var pusher = new Pusher('5c90cb34f6af626fc27b', {
        cluster: 'ap2'
    });
    
    var channel = pusher.subscribe('demo_pusher');
    channel.bind('activeUser', function(data) {
        console.log(data);
        if (data.action == "add") {
            if (!document.querySelector('#user'+data.userid)) {
                const activeUsersList = document.querySelector('.inner-left');
                const divItem = document.createElement('div');
                divItem.className = 'user';
                divItem.innerHTML = `
                <i class="fa-regular fa-user"></i>
                <p id="user${data.userid}">${ data.userid }</p>
                <i class="active fa-solid fa-circle"></i>
                `;
                activeUsersList.prepend(divItem);
            }
        }
        else {
            $('#user'+data.userid).parent().remove();
        }
    });
}

addPost();


$(window).on("load", function(){
    var id = $("#uid1").text();
    $.ajax({
        type: 'POST',
        url: '/addActiveUser',
        data: 
        {
            userid: id
        },
        dataType: "text",
        success: function(response) {
            console.log(response);
        }
    });
});

/**
 * This is used to show the edit/delete post option to those users who add the post.
 */
$(window).on("load",function() {
    var id = $("#uid1").text();
    $.ajax({
        type: 'POST',
        url: '/addRights',
        data: 
        {
            userid: id
        },
        beforeSend: function () {
            $('.loader').css("display","flex");
        },
        dataType: "text",
        success: function(response) {
            $('.loader').css("display","none");
            // console.log(response);
        }
    });
});

function showMenuBtn() {
    var pusher = new Pusher('5c90cb34f6af626fc27b', {
        cluster: 'ap2'
    });

    var channel = pusher.subscribe('demo_pusher');
    channel.bind('giveRights', function(data) {
        data.forEach(element => {
            $('#menu-'+element).css("display","block");
        });
    });
}

showMenuBtn();