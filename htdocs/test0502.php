<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php echo strtotime(date('Y-m-d')); ?>
    <br>
    <?php echo time(); ?>
    <br>
    <?php if(strtotime(date('Y-m-d')) >= (time() - 60 * 60 * 24 *7)):echo "New!"; else: echo date('Y-m-d'); endif; ?>
</body>
</html>