/**
 * Created by Administrator on 2014/10/9.
 */
    var num = 2;
    function next(id,id2,fast){
     var x =parseInt(id.css("width"));
    var n = parseInt(id.css("left")) - parseInt(id2.css("width"));
    if (n > -x) {
        id.filter(':not(:animated)').animate({
                "left": n + "px"
            }, fast
        );
    } else {
        id.filter(':not(:animated)').animate({
                "left": "0px"
            }, fast
        );
    }

}
function prev(id,id2,fast) {
    if(num == 2){
    var y =parseInt(id.css("width"))-parseInt(id2.css("width"));
    var n = parseInt( id.css("left"))+parseInt(id2.css("width"));
    if (n > 0) {
        id.animate({
                "left": -y+"px"
            }, fast
        );
    } else {
        id.animate({
                "left": n + "px"
            }, fast
        );
    }
            num = 1;
        setTimeout(function(){
            num = 2
        },fast)
    }
}
function bottomnext(id,id2,fast){
    if(num == 2){
        var x =parseInt(id.css("height"));
        var n = parseInt(id.css("top")) - parseInt(id2.css("height"));
        if (n > -x) {
            id.animate({
                    "top": n + "px"
                }, fast
            );
        } else {
            id.animate({
                    "top": "0px"
                }, fast
            );
        }
        num = 1;
        setTimeout(function(){
            num = 2
        },fast)
    }
}
function topprev(id,id2,fast) {
    if(num == 2){
        var y =parseInt(id.css("height"))-parseInt(id2.css("height"));
        var n = parseInt( id.css("top"))+parseInt(id2.css("height"));
        if (n > 0) {
            id.animate({
                    "top": -y+"px"
                }, fast
            );
        } else {
            id.animate({
                    "top": n + "px"
                }, fast
            );
        }
        num = 1;
        setTimeout(function(){
            num = 2
        },fast)
    }
}
