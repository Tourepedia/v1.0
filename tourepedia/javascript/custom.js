/**
 * Created by Sudhir on 17-Jun-15.
 */


window.onload = setUpFunction;
var notification;
function setUpFunction() {
    notification = document.getElementById("notification");
}
var notificationId;
function showNotification(notificationString, backgroundColor, duration) {
    notification.innerHTML = notificationString;
    notification.style.background = backgroundColor;
    notification.style.webkitAnimationDuration = duration.toString()+"s";
    notification.style.mozAnimationDuration = duration.toString()+"s";
    notification.style.msAnimationDuration = duration.toString()+"s";
    notification.style.animationDuration = duration.toString()+"s";
    notification.classList.add('play-animation');
    notificationId = setInterval(hideNotification, duration*1000);
}

function hideNotification(){
    notification.classList.remove('play-animation');
    clearInterval(notificationId);
}





