/**
 * Created by Administrator on 2014/10/20.
 */
var MessageBox = {
    isMoving: true,
    titleMaxLength: 10,
    target: null,
    bgTar:null,
    currentX: 0,
    currentY: 0,
    Show: function(w, h, aTitle, aMsg, isModal, btnNub, aYesFunction, aNoFunction) {
        MessageBox.Close();

        var browserWidth = $(window).width();
        var browserHeight = $(window).height();

        if(isModal==true){
            $bg = $("<div></div>");
            $bg.css({"width": browserWidth+"px","height": browserWidth+"px","position": "fixed","top":"0px","left":"0px","z-index": 9998,"background-color": "#333","opacity":"0.8"});
            $("body").append($bg);
            MessageBox.bgTar=$bg;
            $bg.click(function(){
            });
        }

        $box = $("<div></div>");
        MessageBox.target = $box;
        $box.css({"width": "0px","height": "0px","position": "fixed","border": "1px solid #ccc","z-index": 9999,"background-color": "#fff", "left": browserWidth / 2 + "px","top": browserHeight / 2  + "px"});
        $title = $("<div><h3></h3></div>");
        $x= $("<a>X</a>");
        $title.append($x);
        $x.css({
            "font": "13px \"Î¢ÈíÑÅºÚ\", Arial, Helvetica, sans-serif",
            "-webkit-border-radius": "3px",
            "-moz-border-radius": "3px",
            "border-radius": "3px",
            "float": "right",
            "padding": "3px 10px 3px 10px",
            "margin": "5px 10px 5px 0",
            "cursor": "pointer",
            "background-color": "#fff",
            "border": "1px solid #ccc"
        });
        $x.hover(function() {
            $x.css("background-color", "#efefef");
        }, function() {
            $x.css("background-color", "#fff");
        });
        $x.click(function() {
            MessageBox.Close();
        });

        $title.css({"width": "100%","border-bottom": "1px solid #e8e8e8", "background": "#3ba9e6","overflow":"hidden"});
        $title.find("h3").css({"line-height": "30px","padding": "8px 0 8px 10px","margin": "0","color": "#fff","font": "15px \"Î¢ÈíÑÅºÚ\", Arial, Helvetica, sans-serif","float":"left"});

        $title.find("h3").html(aTitle);
        $title.hover(function() {
            $title.css({
                "cursor": "move"
            });
        });
        var drog = false;
        $title.mousedown(function(e) {
            drog = true;
            MessageBox.currentX = e.pageX;
            MessageBox.currentY = e.pageY;
            $("body").mousemove(function(e) {
                if (drog == true) MessageBox.Drog(e);
            });
        });
        $("body").mouseup(function() {
            drog = false;
        });
        $box.append($title);
        if (MessageBox.isMoving == true) {
            $box.animate({
                width: w + "px",
                height:h + "px",
                "left": browserWidth / 2 - w / 2 + "px",
                "top": browserHeight / 2 - h / 2 + "px"
            }, 300);
        } else {
            $box.css("width", w + "px").css("height", h + "px");
        }
        $box.hover(function() {$box.css({"-webkit-box-shadow": "0px 0px 8px #ccc","-moz-box-shadow": "0px 0px 8px #ccc","box-shadow": "0px 0px 8px #ccc"});
        }, function() {
            $box.css({"-webkit-box-shadow": "0px 0px 5px #ccc","-moz-box-shadow": "0px 0px 5px #ccc","box-shadow": "0px 0px 5px #ccc"});
        });
        $center = $("<div></div>");
        $center.html(aMsg);
        $center.css({"height":h - 80 + "px","width": "95%","margin": "0 auto","margin-top": "15px","color": "#808080","font": "13px \"Î¢ÈíÑÅºÚ\", Arial, Helvetica, sans-serif","overflow":"hidden"});
        $box.append($center);
        $center.attr("class","alertbox");

        $foot = $("<div></div>");
        $foot.css({"height": "35px","width": "100%","margin-top": "-15px"});
        if (btnNub == 2) {
            $btnNo = $("<a>È¡Ïû</a>");
            $btnNo.css({
                "font": "13px \"Î¢ÈíÑÅºÚ\", Arial, Helvetica, sans-serif",
                "-webkit-border-radius": "3px",
                "-moz-border-radius": "3px",
                "border-radius": "3px",
                "float": "right",
                "padding": "3px 10px 3px 10px",
                "margin": "5px 10px 5px 0",
                "cursor": "pointer",
                "background-color": "#fff",
                "border": "1px solid #ccc"
            });
            $btnNo.hover(function() {
                $btnNo.css("background-color", "#efefef");
            }, function() {
                $btnNo.css("background-color", "#fff");
            });
            $foot.append($btnNo);
            $btnNo.click(function() {
                if (aNoFunction != null) aNoFunction();
                MessageBox.Close();
            });
        }
        $btnYes = $("<a>È·¶¨</a>");
        $btnYes.attr("class", "button");
        $btnYes.css({"font": "13px \"Î¢ÈíÑÅºÚ\", Arial, Helvetica, sans-serif","-webkit-border-radius": "3px","-moz-border-radius": "3px","border-radius": "3px","float": "right","padding": "3px 10px 3px 10px","margin": "5px 10px 0 0",
                "cursor": "pointer",
                "background-color": "#fff",
            "border": "1px solid #ccc"
        });
        $btnYes.hover(function() {$btnYes.css("background-color", "#efefef");}, function() {$btnYes.css("background-color", "#fff");});
        $foot.append($btnYes);
        $btnYes.click(function() {
            if (aYesFunction != null) aYesFunction();
            MessageBox.Close();
        });
        $box.append($foot);
        $("body").append($box);
    },
    //?³é?
    Close: function() {
        $(this.target).remove();
        if(this.bgTar!=null)$(this.bgTar).remove();
    },
    //???
    Drog: function(e) {
        var x = e.pageX;
        var y = e.pageY;
        var cy = $box.offset().top - $(document.body).scrollTop() + (y - $(document.body).scrollTop() - (MessageBox.currentY - $(document.body).scrollTop()));
        $("#headMenuItema").html(cy);
        $box.css("left", $box.offset().left + (x - MessageBox.currentX) + "px").css("top", cy + "px");
        MessageBox.currentX = x;
        MessageBox.currentY = y;
    }
}
