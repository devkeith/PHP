<!DOCTYPE html>
<html lang="en">
<head>
    <title>PHP is Fun</title>
</head>
<body>
    <h2>This is getting fun</h2>
    <?php
        $name="Rhoda";
        $age="25";

        echo "<p> Hello, $name!</p>";
        echo "<p> You are $age years old </p>";

        if ($age >= 18) {
            echo "<p> Status : Adult </p>";
        } else {
            echo "<p> Status : Minor </p>";
        }

        echo "<p> NUmbers from 1 to 5 </p>";
        for ($i=1; $i<=5; $i++) {
            echo $i."";
        }
    ?>
</body>
</html>