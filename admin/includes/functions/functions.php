<?php
//  Function to change page title 
//version(1.0)
function getTitle()
{
    global $pageTitle ;
    if(isset($pageTitle)){echo $pageTitle;}
    else{echo "Default";}
}

//redirect Function
//version(2.0)
function redirectHome($theMsg, $url = null ,$seconds =3 )
{
    if($url == null)
    {
        $url ='index.php';
        $link = 'Home Page ';
    }
    else{
        if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !=='')
        {
            $url = $_SERVER['HTTP_REFERER'];
            $link = 'Previous Page';
        }
        else
        {
            $url ='index.php';
            $link = 'Home Page ';
        }
    }
    echo $theMsg;
    echo  "<div class='alert alert-info text-center'>You Will Be Redirected To  $link  After $seconds Seconds</div>";
    header("refresh:$seconds;url=$url");//take A time And URL To Redirect Another Page
    exit();
}


/*
** Get All Function
** version(2.0)
** Function To Get All Records From Database
*/
function getAllFrom($field , $table, $join,  $on, $where = NULL, $and = NULL, $orderField, $ordering = "DESC" , $limit = NULL)
{

    global $connection;
    $getAll = $connection ->prepare("SELECT $field FROM $table $join  $on  $where  $and  ORDER BY $orderField $ordering $limit ");
    $getAll->execute();
    $all = $getAll->fetchAll();
    return $all;
}

/*
** Function fOR Count And Check If Found Data Or Not
** Insert 2 pramater for return counter
** Insert 3 prameters for check if Item Already Exist Or Not
** version(1.0)
*/
function countFunc($columnName,$tableName , $value = null)
{
    global $connection;
    if($value === null)
    {
        $stmt = $connection ->prepare("SELECT $columnName From $tableName ");
        $stmt ->execute();
    }
    else
    {
        $stmt =$connection ->prepare("SELECT $columnName From $tableName  WHERE $columnName = ? ");
        $stmt ->execute(array($value));
    }
    $count = $stmt ->rowCount();
    return $count;
}


/*
** Get Latest Records Function
** version(1.0)
*/
function getLatest( $columnName ,$tableName ,$orderColumn ,$limit = 5 )
{
    global $connection;
    $stmt = $connection ->prepare("SELECT $columnName FROM $tableName ORDER BY $orderColumn DESC LIMIT $limit ");
    $stmt->execute();
    $rows = $stmt->fetchAll();
    return $rows;
}







//Check Item Function 
//version(1.0)
// function checkItem($columnName,$tableName,$value)
// {
//     global $connection;
//     //Check if username Or Email Is Exist
//     $stmt =$connection ->prepare("SELECT `$columnName` From `$tableName` WHERE `$columnName`= ? ");
//     $stmt ->execute(array($value));
//     $count = $stmt ->rowCount();
//     return $count;
// }


// //Count  Of Items Function 
// //version(1.0)
// function countItems($item,$tableName)
// {
//     global $connection;
//     $stmt = $connection ->prepare("SELECT COUNT($item) FROM `$tableName`");
//     $stmt ->execute();

//     return $stmt ->fetchColumn();
// }



















