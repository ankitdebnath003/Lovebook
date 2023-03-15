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
    var pusher = new Pusher('913b70eb80f62f270cf5', {
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