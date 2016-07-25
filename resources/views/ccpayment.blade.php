<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</titl>
    </head>
    <body>
        <form method="post" name="redirect" action="https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction"> 
        <input type="hidden" name="encRequest" value="<?php echo $encrypt?>">
        <input type="hidden" name="access_code" value="<?php echo $access?>">
</form>
</center>
        <script language='javascript'>document.redirect.submit();</script>
    </body>
</html>
