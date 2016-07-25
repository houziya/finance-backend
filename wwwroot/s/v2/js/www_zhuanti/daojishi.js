function num(str){
        if(str<10){
          return '0'+str;
        }else{
          return str;
        }
      }

      window.onload=function(){
        var timer=null;
            timer=setInterval(function(){
        var nowday=new Date();
        var day=new Date(2014,12,10);//今年有一个润8月
            day.setHours(09);
            day.setMinutes(00);
             day.setSeconds(00);
        var aa=(day.getTime()-nowday.getTime())/1000;
        var day=aa/86400,
            hour=(aa%86400)/3600,
            minute=(aa%86400)%3600/60,
            second=(aa%86400)%3600%60;
       var  day2=parseInt(day),
            hour2=parseInt(hour),
            minute2=parseInt(minute);
            second2=parseInt(second);
            remainTime.innerHTML="<strong>" +"<span>"+num(day2)+ "</span>" + "天</strong>" +"<span>"+num(hour2)+ "</span>" + "时" + "<span>"+num(minute2)+ "</span>"+ "分" + "<span>"+num(second2)+ "</span>" + "秒";
            if(aa<=0){
              remainTime.innerHTML="<strong><span>00</span>天</strong><span>00</span>时<span>00</span>分<span>00</span>秒";
             clearInterval(timer);
            }
      },1000)
}