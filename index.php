

<?php
//Inclusion des classes

include 'Class/Request_class.php';
include 'Class/Country_class.php';
include 'Class/Laboratory_class.php';
include 'Class/Author_class.php';

//requetes de recupération des données

$Request = new Request;
$Request = $Request->Request_alldoc_querypagebypage('"environmental impact"AND"rare earth element"');

$Sortbycountry = new Country;
$Country=$Sortbycountry->get_name($Request);
$Sortbycountry->Sort_by_country($Country);

$Sortbylaboratory = new Laboratory;
//$Sortbylaboratory->Sort_by_laboratory($Request);

$Sortbyauthor = new Author;
//$Sortbyauthor->Sort_by_author($Request);


?>



