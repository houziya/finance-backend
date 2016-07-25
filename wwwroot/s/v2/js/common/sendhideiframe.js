;(function(w){
    function calcPageHeight(doc) {
        var cHeight = Math.max(doc.body.clientHeight, doc.documentElement.clientHeight)
        var sHeight = Math.max(doc.body.scrollHeight, doc.documentElement.scrollHeight)
        var height  = Math.max(cHeight, sHeight)
        return height
    }
    window.onload = function() {
        var doc = document
        var height = calcPageHeight(doc)
        var myifr = doc.getElementById('myifr')
        if($('#ljsearch').length != 0) $('#ljsearch').css('marginTop','0'); // 搜索内容的顶部边距取消
        if (myifr) {
            myifr.src = 'http://www.renrentou.com/static/sendhideiframe.html?height=' + (parseInt($('#ljsearch').css('height'))+parseInt($('#ljsearch').next().css('height'))+100);    
        }
        setTi(); // 给搜索按钮增加重新获取高度方法以应对ajax取来的数据
        function setTi(){
            _t = setTimeout(function(){
                height = calcPageHeight(doc);
                if(myifr){
                    myifr.src = 'http://www.renrentou.com/static/sendhideiframe.html?height=' + (parseInt($('#ljsearch').css('height'))+parseInt($('#ljsearch').next().css('height'))+100) 
                }
            },5500)
        }
    };
})(this)

