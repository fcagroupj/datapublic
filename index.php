<!DOCTYPE html>
<html>
<head>
<link rel="icon" type="image/png" href="img/icon_appstorego.png" sizes="32x32" />
</head>
<body>

<?php
// Initialize the session
session_start();
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // last request was more than 5 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    $account_info = "Login now"; 
} else {
    $account_info = "";
    if(isset($_SESSION['username'])) $account_info = htmlspecialchars($_SESSION["username"]);
}
?>

<?php    
echo '<table border="0" width="300" align="center">';
echo "<td>".'<img src="img/icon_appstorego.png" alt="icon" />'."</td>";
echo "<td>"."DataPublic.org"."<br>"."Data For Public Healthy"."<br>"."<a href=login/>$account_info</a>"."</td>";
echo "</table>";

echo "<br>";
echo "<br>";
echo '<h1>OUR MISSION</h1>';
echo '<table border="0" width="800" align="center">';
echo "<td>".'<img src="img/icon_mission.png" alt="icon" />'."</td>";
echo "<td>"."Welcome to "."<a href=./login/welcome.php>join us. </a>"."We focus on making the maximum positive effort for our community."."</td>";
echo "</tr>";
echo "<td>".'<img src="img/icon_item.png" alt="icon" />'."</td>";
echo "<td>"."1) Learn mathematics used in computer programming"."</td>";
echo "</tr>";
echo "<td>".'<img src="img/icon_item.png" alt="icon" />'."</td>";
echo "<td>"."2) Get familiar with computer software and programming."."</td>";
echo "</tr>";
echo "<td>".'<img src="img/icon_item.png" alt="icon" />'."</td>";
echo "<td>"."3) Volunteer in our community."."</td>";
echo "</tr>";
echo "<td>".'<img src="img/icon_item.png" alt="icon" />'."</td>";
echo "<td>"."4) Create a data-driven computer application to domostrate your programming skills and leadership."."</td>";
echo "</tr>";
echo "</table>";

echo "<br>";
echo "<br>";
echo '<h1>OUR ACTIVITIES</h1>';
echo '<table border="0" width="800" align="center">';
echo "<td>".'<img src="img/icon_mission.png" alt="icon" />'."</td>";
echo "<td>"."Welcome to "."<a href=./login/controlpanel/activities_register.php>register our activities. </a>"."Some activities are not listed here."."</td>";
echo "</tr>";
echo "<td>".'<img src="img/icon_item.png" alt="icon" />'."</td>";
echo "<td>"."1) Computer coding in the open source project of COVID19VIZ on every weekend."."</td>";
echo "</tr>";
echo "<td>".'<img src="img/icon_item.png" alt="icon" />'."</td>";
echo "<td>"."2) Learning the math used in programming."."</td>";
echo "</tr>";
echo "<td>".'<img src="img/icon_item.png" alt="icon" />'."</td>";
echo "<td>"."3) Volunteering events such as cleaning up our parks."."</td>";
echo "</tr>";
echo "<td>".'<img src="img/icon_item.png" alt="icon" />'."</td>";
echo "<td>"."4) Visit a public health organization and donate every half year."."</td>";
echo "</tr>";
echo "</table>";

echo "<br>";
echo "<br>";
echo '<h1>HISTORY</h1>';
echo '<table border="0" width="800" align="center">';
echo "<td>".'<img src="img/icon_mission.png" alt="icon" />'."</td>";
echo "<td>"."This Nonprofit Organization, data public organization was founded in April, 2020 by:"."</td>";
echo "</tr>";
echo '<table border="0" width="800" align="center">';
echo "<td>".'<img src="img/icon_item.png" alt="icon" />'."</td>";
echo "<td>"."Dennis Yang, student in Grade 10, Troy High School, MI USA."."</td>";
echo "</tr>";
echo "<td>".'<img src="img/icon_item.png" alt="icon" />'."</td>";
echo "<td>"."Yihan Wang (Luna), student in Grade 9, Troy High School, MI USA."."</td>";
echo "</tr>";

echo "</table>";

echo "<br>";
echo "<h1>Project #1: Open Source Project COVID19VIZ</h1>";
echo '<table border="0" width="600" align="center">';
echo "<td>".'<img src="img/icon_world120.png" alt="icon" />'."</td>";
echo "<td>"."<a href=https://google.org/crisisresponse/covid19-map>Coronavirus map</a>"."<br>"."In the world"."</td>";
echo "<td>".'<img src="./covid19/img/Seal_of_United_States.svg.png" alt="icon" />'."</td>";
echo "<td>"."<a href=./covid19>COVID-19 in USA</a>"."<br>"."Visualization and Prediction"."</td>";
echo "</tr>";
echo "</table>";

echo "<br>";
echo "<h2>Project #2: math for computer, math4computer</h2>";
echo '<table border="0" width="600" align="center">';
echo "<td>".'<img src="img/icon_service.png" alt="icon" />'."</td>";
echo "<td>"."<a href=https://github.com/lunawyh/covid19viz/wiki/math4computer-1.-data-and-matrix>Array</a>"."<br>"."In the coding"."</td>";
echo "<td>".'<img src="./img/icon_service1.png" alt="icon" />'."</td>";
echo "<td>"."<a href=https://github.com/lunawyh/covid19viz/wiki/math4computer-2.-coordination-and-rotation>Coordination</a>"."<br>"."In the coding"."</td>";
echo "</tr>";
echo "<td>".'<img src="img/icon_service2.png" alt="icon" />'."</td>";
echo "<td>"."<a href=https://github.com/lunawyh/covid19viz/wiki/math4computer-3.-Permutation-and-sorting>Permutation and sorting</a>"."<br>"."In the coding"."</td>";
echo "<td>".'<img src="./img/icon_service3.png" alt="icon" />'."</td>";
echo "<td>"."<a href=https://github.com/lunawyh/covid19viz/wiki/math4computer-4.-Arrangement-and-matching>Arrangement and fitting</a>"."<br>"."In the coding"."</td>";
echo "</tr>";
echo "</table>";

echo "<br>";
echo "<h2>Project #3: python for Machine Learning</h2>";
echo '<table border="0" width="600" align="center">';
echo "<td>".'<img src="img/icon_service4.png" alt="icon" />'."</td>";
echo "<td>"."<a href=https://www.youtube.com/watch?v=r4mwkS2T9aI&list=PLQVvvaa0QuDfKTOs3Keq_kaG2P55YRn5v&index=4>Machine Learning with Python Advanced</a>"."<br>"."supervised, unsupervised, and deep learning algorithms"."</td>";
echo "<td>".'<img src="img/icon_service5.png" alt="icon" />'."</td>";
echo "<td>"."<a href=https://www.youtube.com/watch?v=rdfbcdP75KI&list=PLeo1K3hjS3uvCeTYTeyfe0-rN5r8zn9rw&index=19>Machine Learning Tutorial Python (Study now!) </a>"."<br>"."What is machine learning, deep learning? machine learning applications in real life"."</td>";
echo "</tr>";

echo "</table>";

echo "<br>";
echo "<h2>Project #4: summer project</h2>";
echo '<table border="0" width="600" align="center">';
echo "<td>".'<img src="img/icon_service7.png" alt="icon" />'."</td>";
echo "<td>"."<a href=./project/>Solve questions of subjects in high school with AI such as math, English</a>"."<br>"."for summer in 2021 (enroll now!)"."</td>";
echo "<td>".'<img src="./img/icon_service6.png" alt="icon" />'."</td>";
echo "<td>"."<a href=https://github.com/PacktPublishing/Bioinformatics-with-Python-Cookbook-Second-Edition>Open source project (coming soon)</a>"."<br>"."For Bioinformatics with Python Cookbook, Second Edition."."</td>";
echo "</tr>";
echo "</table>";

echo "<br>";
echo "<h3>Friendly links</h3>";
echo '<table border="0" width="600" align="center">';
echo "<td>".'<img src="img/icon_service5.png" alt="icon" />'."</td>";
echo "<td>"."<a href=./luna/>Luna Skills</a>"."<br>"."Art, Music and Coding"."</td>";
echo "<td>".'<img src="./img/icon_house.png" alt="icon" />'."</td>";
echo "<td>"."<a href=http://www.datapublic.org/house/>OX rent Cloud </a>"."<br>"."Rent management, Electronic receipts."."</td>";
echo "</tr>";
echo "</table>";

echo '<table border="0" width="600" align="center">';
echo "<td>".'<img src="img/ox_rent_qr100.png" alt="icon" />'."</td>";
echo "<td>".'<a href="https://twitter.com/Jeff81994794">Twitter</a>'.' '.
    '<a href="https://www.facebook.com/OX-Rent-Toolkits-100113822071176">Facebook</a>'.' '.
    '<a href="https://www.youtube.com/channel/UC4ap0lCTYDgDibVa332mqbg">Youtube</a>'.' '.
    '<a href="https://vm.tiktok.com/ZMeL1ckb8/">Tiktok</a>'.' '.
    '<a href="https://detroit.craigslist.org/okl/bfs/d/detroit-0x-rent-cloud/7294571067.html">Craigslist</a>'.'<br>'.
    "Email: datapublic.org@gmail.com".'<br>'."Ph: 001-248-7066-8499".'<br>'."Copyright @2020 Data Public Organization"."</td>";
echo "</tr>";
echo "</table>";

?>

</body>
</html>