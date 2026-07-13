<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    $max = $_GET['max'] ?? 42;
    $arr = [];
    for($x = 1; $x<=$max; $x++){
        $arr[] =  strval($x);
    }
    shuffle($arr);
    shuffle($arr);
    shuffle($arr);
    shuffle($arr);
    shuffle($arr);
    shuffle($arr);

    $newarr = [];
    for($x = 1; $x<=4; $x++){
        $newarr[] = $arr[$x];
    }
    sort($newarr);
    echo implode(", ", $newarr);
    ?>
</body>
</html>