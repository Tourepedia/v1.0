/**
 * Created by Sudhir on 20-Jun-15.
 */

function MoveMyLabel(inputGroup){
    var label = _id(inputGroup[1]);
    label.classList.add('move-label');
}

function ResetLabel(inputGroup){
    var input = _id(inputGroup[0]);
    if(input.value == ""){
        var label = _id(inputGroup[1]);
        label.classList.remove('move-label');
    }
}

function LaunchDialog(idName){
    document.body.style.overflow = "hidden";
    var dialog = _id(idName);
    dialog.style.display = "flex";
    dialog.style.display = "-webkit-flex";
    dialog.style.display = "-ms-flexbox";

}

function CloseDialog(idName){
    document.body.style.overflow = "auto";
    var dialog = _id(idName);
    dialog.style.display = "none";
}

function mainFunction(){
    var wScroll = window.pageYOffset;
    var moveTopButton = _id('move-top-button');
    window.addEventListener('scroll', function(){
        if(moveTopButton == null){
            moveTopButton = _id('move-top-button');
        }
        wScroll  = window.pageYOffset;
        if(wScroll > screen.height ){
            if(!moveTopButton.classList.contains("show"))
                moveTopButton.classList.add("show");
        }else{
            if(moveTopButton.classList.contains("show"))
                moveTopButton.classList.remove("show");
        }
    });

}

function ScrollToTop(){
    window.scrollTo(0,0);
}

window.addEventListener('load', mainFunction);

/* bookkeeping stuff */
function _id(idName){
    return document.getElementById(idName);
}


