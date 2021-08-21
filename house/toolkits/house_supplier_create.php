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
    header("location: ../login.php");
    exit;
}

if(isset($_SESSION["groups_in"]) && strpos($_SESSION["groups_in"], "house") !== false){
    
} else {
    header("location: ../welcome.php");
    exit;
}
?>

<?php
// Include config file
require_once "../config.php";
$h_id = $h_function = 0;
$h_name = ""; 
// Define variables and initialize with empty values
// h_company, h_contact, h_telephone, h_address, h_email, h_website, h_note
$h_param01 = $h_param02 = $h_param03 = $h_param04 = $h_param05 = "";
$h_param06 = $h_param07 = $h_param11 = "";
$h_param01_err = $h_param02_err = $h_param03_err = $h_param04_err = $h_param05_err = "";
$h_param06_err = $h_param07_err = $id_list_err = $mysql_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate h_param01
    if(empty(trim($_POST["h_param01"]))){
        $h_param01_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param01"])) < 2){
        $h_param01_err = "The param is too short.";
    } else{
        $h_param01 = trim($_POST["h_param01"]);
    }
    // Validate h_param02
    if(empty(trim($_POST["h_param02"]))){
        $h_param02_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param01"])) < 2){
        $h_param02_err = "The param is too short.";
    } else{
        $h_param02 = trim($_POST["h_param02"]);
    }
    // Validate h_param03
    if(empty(trim($_POST["h_param03"]))){
        $h_param03_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param03"])) < 2){
        $h_param03_err = "The param is too short.";
    } else{
        $h_param03 = trim($_POST["h_param03"]);
    }
    // Validate h_param04
    if(empty(trim($_POST["h_param04"]))){
        $h_param04_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param04"])) < 2){
        $h_param04_err = "The param is too short.";
    } else{
        $h_param04 = trim($_POST["h_param04"]);
    }
    // Validate h_param05
    if(empty(trim($_POST["h_param05"]))){
        $h_param05_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param05"])) < 2){
        $h_param05_err = "The param is too short.";
    } else{
        $h_param05 = trim($_POST["h_param05"]);
    }
    // Validate h_param06
    if(empty(trim($_POST["h_param06"]))){
        $h_param06_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param06"])) < 2){
        $h_param06_err = "The param is too short.";
    } else{
        $h_param06 = trim($_POST["h_param06"]);
    }
    // Validate h_param07
    if(empty(trim($_POST["h_param07"]))){
        $h_param07_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param07"])) < 2){
        $h_param07_err = "The param is too short.";
    } else{
        $h_param07 = trim($_POST["h_param07"]);
    }
    // Validate h_param11
    if(empty(trim($_POST["h_param11"]))){
        $h_param11_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param11"])) < 1){
        $h_param11_err = "The param is too short.";
    } else{
        $h_param11 = trim($_POST["h_param11"]);
    }
    
    // Check input errors before inserting in database
    if( empty($h_param01_err) && empty($h_param02_err) && empty($h_param03_err) && 
        empty($h_param04_err) && empty($h_param05_err) && empty($h_param06_err) && 
        empty($h_param07_err) && empty($h_param11_err) ){
            $mysql_err = "";             
        
            // Prepare an insert statement
            $sql = "INSERT INTO tb_houses_suppliers (un_id, h_category, h_company, h_contact, h_telephone, h_address, h_email, h_website, h_note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ddsssssss", $_SESSION["id"], $param_h_param11, $param_h_param01, 
                    $param_h_param02, $param_h_param03, $param_h_param04, $param_h_param05, $param_h_param06, 
                    $param_h_param07 );
                
                // Set parameters
                $param_h_param01 = $h_param01;
                $param_h_param02 = $h_param02;
                $param_h_param03 = $h_param03;
                $param_h_param04 = $h_param04;
                $param_h_param05 = $h_param05;
                $param_h_param06 = $h_param06;
                $param_h_param07 = $h_param07;
                $param_h_param11 = $h_param11;
                
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // jump to manage one
                    header("location: ./house_supplier_manage.php?query_sp_id=".'0');
                    exit;
                } else{
                    $mysql_err = "Something went wrong. Please create a supplier again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
        
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create a house supplier</title>
    <link rel="icon" type="image/png" href="../../img/icon_appstorego.png" sizes="32x32" />
    <link rel="stylesheet" href="../../css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 640px; padding: 20px; }
    </style>
</head>
<body>
    <?php
        $account_info = "";
        if(isset($_SESSION['username'])) $account_info = htmlspecialchars($_SESSION["username"]);
        echo '<table border="0" width="300" align="center">';
        echo "<td>".'<img src="../../img/icon_appstorego.png" alt="icon" />'."</td>";
        echo "<td>"."<a href=../>DataPublic.org</a>"."<br>"."OX Rent Cloud"."<br>"."<a href=../welcome.php>$account_info</a>"."</td>";
        echo "</table>";
    ?>
    <div class="wrapper">
        <p><?php
                echo '<a href="'."./house_supplier_create.php?query_h_id=".$h_id."&h_name=".$h_name."&h_function=".$h_function.'">'        
                ?>Create</a> -> Review -> <?php 
                echo '<a href="'."./house_supplier_manage.php?query_h_id=".$h_id."&h_name=".$h_name."&h_function=".$h_function.'">'        
                ?>Manage</a> -> Close -> Reopen.</p>
        <h2>Create a house supplier</h2>
        <p>To add a supplier for your house, please fill all boxes and click the button Create now.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            
            <div class="form-group <?php echo (!empty($id_list_err)) ? 'has-error' : ''; ?>">
                <label for='formId[]'>Category</label><br>                
                <select id="h_param11" name="h_param11">
                    <?php
                        // pay methods x5
                        echo '<option value=0 '; 
                        if($h_param11 == 0) echo "selected"; 
                        echo ' >please select</option>';
                        for ($x = 1; $x <= 4; $x++) {
                            echo '<option value='.$x.' '; 
                            if($h_param11 == $x) echo "selected";
                            if($x == 1) echo ' >'.'Service.'.'</option>';
                            if($x == 2) echo ' >'.'Supplier.'.'</option>';
                            if($x == 3) echo ' >'.'Landlord,tenants.'.'</option>';
                            if($x == 4) echo ' >'.'Others.'.'</option>';
                        }
                    ?>
                </select>               
                <span class="help-block"><?php echo $id_list_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param01_err)) ? 'has-error' : ''; ?>">
                <label>Name of company</label>
                <input type="text" name="h_param01" class="form-control" value="<?php echo $h_param01; ?>">
                <span class="help-block"><?php echo $h_param01_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($h_param02_err)) ? 'has-error' : ''; ?>">
                <label>Contact person</label>
                <input type="text" name="h_param02" class="form-control" value="<?php echo $h_param02; ?>">
                <span class="help-block"><?php echo $h_param02_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param03_err)) ? 'has-error' : ''; ?>">
                <label>Telephone</label>
                <input type="text" name="h_param03" class="form-control" value="<?php echo $h_param03; ?>">
                <span class="help-block"><?php echo $h_param03_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param04_err)) ? 'has-error' : ''; ?>">
                <label>Address</label>
                <input type="text" name="h_param04" class="form-control" value="<?php echo $h_param04; ?>">
                <span class="help-block"><?php echo $h_param04_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param05_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="text" name="h_param05" class="form-control" value="<?php echo $h_param05; ?>">
                <span class="help-block"><?php echo $h_param05_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param06_err)) ? 'has-error' : ''; ?>">
                <label>Website</label>
                <input type="text" name="h_param06" class="form-control" value="<?php echo $h_param06; ?>">
                <span class="help-block"><?php echo $h_param06_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param07_err)) ? 'has-error' : ''; ?>">
                <label>Note</label>
                <input type="text" name="h_param07" class="form-control" value="<?php echo $h_param07; ?>">
                <span class="help-block"><?php echo $h_param07_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Create now">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p><?php echo '<span style="color:#F00;text-align:center;">'.$mysql_err.'</span>'; ?></p>
            <p><a href="./house_supplier_all.php">Go to list all suppliers</a>.</p>
            <p><?php
                echo '<a href="'."./house_supplier_create.php?query_h_id=".$h_id."&h_name=".$h_name."&h_function=".$h_function.'">'        
                ?>Create</a> -> Review -> <?php 
                echo '<a href="'."./house_supplier_manage.php?query_h_id=".$h_id."&h_name=".$h_name."&h_function=".$h_function.'">'        
                ?>Manage</a> -> Close -> Reopen.</p>
            <p>Copyright @2020 <a href="../">Data Public Organization</a>.</p>
        </form>
    </div>    
</body>
</html>