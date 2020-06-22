<?php
/*
** Get Rate Function
** version(1.0)
** Function To Get Rate Item From Database
*/
function getRate($itemid)
{
    global $connection;
    $stmt2 = $connection ->prepare("SELECT AVG(rating) AS rating FROM item_rating WHERE item_id = ? ");
    $stmt2 ->execute(array($itemid));
    $rating = $stmt2 ->fetch();
    return ceil($rating['rating']);
}

/*
** Get All Function
** version(3.0)
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
** Check IF User Is Not Activated Function
** version(3.0)
*/
function checkStatus($field,$table, $where,$value)
{
    global $connection;
    $stmt = $connection ->prepare("SELECT
                                        $field
                                    FROM
                                        $table
                                    Where
                                        $where =?
                                ");
    $stmt ->execute(array($value));
    $status = $stmt ->fetch();
    return $status[$field];
}

/*
** Function fOR Count And Check If Found Data Or Not
** Insert 2 pramater for return counter
** Insert 3 prameters for check if Value Already Exist Or Not
** version(2.0)
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


//  Function to change page title 
//version(1.0)
function getTitle()
{
    global $pageTitle ;
    if(isset($pageTitle)){echo $pageTitle;}
    else{echo "Default";}
}

