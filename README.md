# SerSql

Hello Friends!


This is very simple Sql Class with html query form!

Work with MySql!

if you want to get data from the database, create a file * .php in the directory where the file 'Ser_Sql_query.Class.php'.

 *.php text:

    <?php
    
      require_once('Ser_Sql_query.Class.php');
    
        echo('<!DOCTYPE html><html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8"></head><body>');
        
            $query = new Ser_Sql_query('root','12345','mysql','localhost','','');
            $query->sql_query_from_http_form();
            
        echo('</body></html>');
      
      unset($query2);
      
    ?>


