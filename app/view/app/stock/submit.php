<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    </head>
    <body>
        <form action="<?php echo $path;?>" method="post" style="display: none" id="submit">
            <textarea name="req"><?php echo $req;?></textarea>
            <input type="hidden" name="sign" value="<?php echo $sign;?>">
        </form>
        <script>window.onload = function(){document.getElementById("submit").submit();}</script>
    </body>
</html>