/**
 * Jquery file execute only after the dom is fully loaded for that we use ready function
 * 
 *   @var pass 
 *     stores the password of the user.
 * 
 *   @var cpass 
 *     stores the confirm password of the user.
 */
$(document).ready(function() {
    var pass = document.querySelector('#password');
    var cpass = document.querySelector('#confirmpassword');
    var correct = document.querySelector('#correct');
    var wrong = document.querySelector('#wrong');
    
    pass.addEventListener('keyup', (element)=> {
        var val = element.target.value;
        if (val == cpass.value) {
            correct.style.display = "block";
            wrong.style.display = "none";
        }
        else {
            correct.style.display = "none";
            wrong.style.display = "block";
        }
    })
    cpass.addEventListener('keyup', (element)=> {
        var val = element.target.value;
        if (val === pass.value) {
            correct.style.display = "block";
            wrong.style.display = "none";
        }
        else {
            correct.style.display = "none";
            wrong.style.display = "block";
        }
    })
});

/**
 * Used to remove all the errors.
 */
function clearErrors() {
    errors = document.getElementsByClassName('formerror');
    for(let item of errors) {
        item.innerHTML = "";
    }
}

/**
 * Used to show the errors to the user.
 * 
 *   @param id 
 *     stores id of the form.
 * 
 *   @param error
 *     stores the error message to show.
 */
function seterror(id,error) {
    element = document.getElementById(id);
    element.getElementsByClassName('formerror')[0].innerHTML = error;
}

/**
 * Used to validate the form.
 * 
 *   @return bool
 *     based on the validation of the form.
 */
function validationForm() {
    var returnval = true;
    clearErrors();

    // Stores the first name of the employee.
    var fName = document.forms['myForm']["firstname"].value;
    // Check if first name has a valid length and doesn't contain any number.
    if (fName.length < 4) {
        seterror("firstname", "*Length of first name is too short");
        returnval = false;
    }
    else if (/\d/.test(fName)) {
        seterror("firstname", "*Name can't contain any number");
        returnval = false;
    }

    // Stores the last name of the employee.
    var lName = document.forms['myForm']["lastname"].value;
    // Check if last name has a valid length and doesn't contain any number.
    if (lName.length < 4) {
        seterror("lastname", "*Length of last name is too short");
        returnval = false;
    }
    else if (/\d/.test(lName)) {
        seterror("lastname", "*Name can't contain any number");
        returnval = false;
    }
    
    var pass = document.forms['myForm']["password"].value;
    var cpass = document.forms['myForm']["confirmpassword"].value;
    if (pass != cpass) {
        seterror("confpass", "*Passwords are not same");
        returnval = false;
    }
    return returnval;
}

/**
 * Used to check if the email end with .com then enable the GetOTP button.
 */
$(document).ready(function(){
    $('#emailid').keyup(function(){
        var email = $(this).val();
        if (email.endsWith(".com")) {
            $("#getotp").removeAttr("disabled");
        }
    });
});

/**
 * Used to send OTP when clicked on GetOtp button.
 */
function getOtp() {
    var resendotp = document.getElementById("resendotp");
    resendotp.style.display = "block";
    var otp = document.getElementById("otp");
    var email = jQuery('#emailid').val();
    otp.style.display = "block";
    $.ajax({
        type: 'POST',
        url: '/sendOtp',
        data: {emailid:email},
        dataType: "text",
        success: function(response) {
            console.log(response);
        }
    });
}

/**
 * Used to set timer to Resend Otp button.
 */
$(function() {
    $("#getotp").click(function() {
        $("#resendotp").attr("disabled","disabled");
        setTimeout(function() {
            $("#resendotp").removeAttr("disabled");
        },10000);
    });
});

/**
 * Used to check the availability of the username in the database.
 * 
 *   @var username
 *     stores the username and checks for its availability.
 */
$(document).ready(function(){
    $('#username').blur(function(){
        var userName = $(this).val();
        if (userName.length == 0) {
            $('span').css("display","none");
        }
        else {
            $('span').css("display","block");
        }
        $('#availability1').css("display","none");
        $('#availability2').css("display","none");
        $.ajax({
            type: 'POST',
            url: '/availability',
            data: {username:userName},
            dataType: "text",
            success: function(data) {
                if (data) {
                    $('#availability1').css("display","block");
                }   
                else {
                    $('#availability2').css("display","block");
                }             
            }
        });
    });
});

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
            beforeSend: function () {
                $('.loader').css("display","flex");
            },
            dataType: "text",
            success: function(response) {
                $('.loader').css("display","none");
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