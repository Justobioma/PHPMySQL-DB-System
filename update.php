<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$surname = $firstname = $midname = $title = $gender = $status = $email = $address = "";
$surname_err = $firstname_err = $phone_err = $email_err = $address_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate surname
    $input_surname = trim($_POST["surname"]);
    if(empty($input_surname)){
        $surname_err = "Please enter a surname.";
    } elseif(!filter_var($input_surname, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $surname_err = "Please enter a valid surname.";
    } else{
        $surname = $input_surname;
    }
    // Validate firstname
    $input_firstname = trim($_POST["firstname"]);
    if(empty($input_firstname)){
        $firstname_err = "Please enter a firstname.";
    } elseif(!filter_var($input_firstname, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $firstname_err = "Please enter a valid firstname.";
    } else{
        $surname = $input_firstname;
    }
    
    /* // Validate phone number
    $input_phone = trim($_POST["phone"]);
    if(len($input_phone) != 11){
        $phone_err = "Please enter a valid phone number.";
    } else{
        $phone = $input_phone;
    } */
    
    // Validate Email address
    $input_email = trim($_POST["email"]);
    if(empty($input_email)){
        $firstname_err = "Please enter an email address.";
    } elseif(!filter_var($input_email, FILTER_VALIDATE_EMAIL)){
        $email_err = "Please enter a valid email address.";
    } else{
        $email = $input_email;
    }
    // Validate Contact address
    $input_address = trim($_POST["address"]);
    if(empty($input_address)){
        $address_err = "Please enter an address.";
    } else{
        $address = $input_address;
    }
    
    /* // Validate salary
     $input_salary = trim($_POST["salary"]);
     if(empty($input_salary)){
     $salary_err = "Please enter the salary amount.";
     } elseif(!ctype_digit($input_salary)){
     $salary_err = "Please enter a positive integer value.";
     } else{
     $salary = $input_salary;
     } */
    
    // Check input errors before inserting in database
    if(empty($surname_err) && empty($firstname_err) && empty($email_err) && empty($address_err)){
        // Prepare an update statement
        $sql = "UPDATE members SET surname=?, firstname=?, midname=?, title=?, gender=?, status=?, email=?, address=?, WHERE sn=?";
        
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssssssssi", $param_surname, $param_firstname, $param_midname, $param_title, $param_gender,  $param_status, $param_email, $param_address, $param_sn);
            
            // Set parameters
            $param_surname = $surname;
            $param_firstname = $firstname;
            $param_midname = $midname;
            $param_title = $title;
            $param_gender = $gender;
            $param_status = $status;
            //$param_phone = $phone;
            $param_email = $email;
            $param_address = $address;
            $param_sn = $sn;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records updated successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        $stmt->close();
    }
    
    // Close connection
    $mysqli->close();
} else{
    // Check existence of sn parameter before processing further
    if(isset($_GET["sn"]) && !empty(trim($_GET["sn"]))){
        // Get URL parameter
        $id =  trim($_GET["sn"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM members WHERE sn = ?";
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("i", $param_sn);
            
            // Set parameters
            $param_sn = $sn;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                $result = $stmt->get_result();
                
                if($result->num_rows == 1){
                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $surname = $row["surname"];
                    $firstname = $row["firstname"];
                    $midname = $row["midname"];
                    $title = $row["title"];
                    $gender = $row["gender"];
                    $phone = $row["phone"];
                    //$email = $row["email"];
                    $address = $row["address"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        $stmt->close();
        
        // Close connection
        $mysqli->close();
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Update Record</h2>
                    </div>
                    <p>Please edit the input values and submit to update the record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group <?php echo (!empty($surname_err)) ? 'has-error' : ''; ?>">
                            <label>Surname</label>
                            <input type="text" name="surname" class="form-control" value="<?php echo $surname; ?>">
                            <span class="help-block"><?php echo $surname_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($firstname_err)) ? 'has-error' : ''; ?>">
                            <label>First Name</label>
                            <input type="text" name="firstname" class="form-control" value="<?php echo $firstname; ?>">
                            <span class="help-block"><?php echo $firstname_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($midname_err)) ? 'has-error' : ''; ?>">
                            <label>Middle Name</label>
                            <input type="text" name="midname" class="form-control" value="<?php echo $midname; ?>">
                            <span class="help-block"><?php echo $midname_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Title</label>
                            <input type="radio" name="title" value="Mrs" class="form-control" > Mrs
                            <input type="radio" name="title" value="Miss" class="form-control" > Miss
                            <input type="radio" name="title" value="Mr" class="form-control" > Mr
                            <input type="radio" name="title" value="Dr" class="form-control" > Dr
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <input type="radio" name="gender" value="Male" class="form-control" > Male
                            <input type="radio" name="gender" value="Female" class="form-control" > Female
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name = "status">
                            	<option value="Single" selected > Single </option>
                            	<option value="Married" selected > Married </option>
                            	<option value="Divorced" selected > Divorced </option>
                            	<option value="Widowed" selected > Widowed </option>
         					</select>
                            <span class="help-block"></span>
                        </div>
                        
                        <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                            <label>Email</label>
                            <input type="text" name="email" class="form-control"><?php echo $email; ?>">
                            <span class="help-block"><?php echo $email_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($address_err)) ? 'has-error' : ''; ?>">
                            <label>Address</label>
                            <textarea name="address" class="form-control"><?php echo $address; ?></textarea>
                            <span class="help-block"><?php echo $address_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
