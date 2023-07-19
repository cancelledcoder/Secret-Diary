<?php

    session_start();

    $error="";

    if(array_key_exists("logout",$_GET)){
        session_unset();
        setcookie("id","",time() - 60 *60);
        $_COOKIE["id"] = "";
    }
    else if(array_key_exists("id",$_COOKIE) OR array_key_exists("id",$_SESSION)){

        //go to loggedinpage if you are still logged in
        header("Location: loggedinpage.php");
    }//end test for logout query string
    
    if(array_key_exists("submit",$_POST)){
       
        //connect db
        include('connection.php');

        //no email error
        if(!$_POST['email']){
            $error .=  "Email address is required.<br>";
        }
        
        //no password error
        if(!$_POST['password']){
            $error .=  "Password is required.<br>";
        }
        
        //Indicates error(s)
        if($error!=""){
            $error = "<p>There were error(s) in the form!</p>".$error;
        }
        else{
    
            $emailaddress = mysqli_real_escape_string($link,$_POST['email']);
            $password = mysqli_real_escape_string($link, $_POST['password']);
            $password = password_hash($password, PASSWORD_DEFAULT);

            if($_POST['signup']=='1'){

                $query = "SELECT id FROM users WHERE email = '" . $emailaddress . "' LIMIT 1";
            
                $result = mysqli_query($link,$query);
                if(mysqli_num_rows($result)>0){
                    $error = "That email address is taken.";
                }
                else{
                    
    
                    $query = "INSERT INTO users (email, password) VALUES ('" . $emailaddress . "','" . $password . "')";
                   
                    if(!mysqli_query($link, $query)){
                        $error = "<p>Could not sign you up - Please try again later.</p>";
                        $error .= "<p>". mysqli_error($link) . "</p>";
                    }
                    else{
                        //echo "Signup is Successful!<br>";
    
                        //create cookies
                        $id = mysqli_insert_id($link);
                        $_SESSION['id'] = $id;
                        if(isset($_POST['stayloggedin'])){
                            setcookie("id",$id, time() + 60 * 60 * 24 * 365);
                        }
    
                        header("Location: loggedinpage.php");
                    }
                    //end if successful/failed signup
                }
                //end of mysqli_num_rows test for existing email

            }

            else{
                
                $query = "SELECT * FROM users WHERE email = '" . $emailaddress . "'";
                $result = mysqli_query($link, $query);
                $row = mysqli_fetch_array($result);

                $password = mysqli_real_escape_string($link, $_POST['password']);

                if(isset($row) AND array_key_exists("password",$row)){
                    $passwordmatch = password_verify($password, $row['password']);
                    
                    if($passwordmatch){
                        $_SESSION['id'] = $row['id'];

                        if(isset($_POST['stayloggedin'])){
                            setcookie("id",$row['id'], time() + 60 * 60 * 24 * 365);
                        }

                        header("Location: loggedinpage.php");
                    }
                    else{
                        //password doesnt match
                        $error = "That email/password combination can not be found.";
                    }//password matches or not
                }
                else{
                    //email can't be found
                    $error = "That email/password combination can not be found.";
                }//email found or not

            }//end of if-else for login/signup

        }
        //end of error check
    }
    //end if submit exists

?>

<?php include('header.php'); ?>

<div class="container" id="homepagecontainer">
            <h1>Secret Diary</h1>

            <p>Store your thoughts permanently and securely!</p>

            <div id="error">
                <?php
                    if($error != ""){
                        echo '<div class="alert alert-danger" role="alert">'.
                        $error . '</div>';
                    }
                    
                ?>
            </div>

            <!-- Signup form -->
            <form method="post" id="signupform">
                <p>Interested? Signup Now!</p>
                <fieldset class="form-group"> 
                    <input type="email" name="email" class="form-control" placeholder="Your email">
                </fieldset>

                <fieldset class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Your password">
                </fieldset>

                <fieldset class="checkbox">
                    Stay Logged in:
                    <input type="checkbox" name="stayloggedin" value="1">
                </fieldset>

                <fieldset class="form-group">
                    <input type="hidden" name="signup" value="1">
                    <input type="submit" name="submit" class="btn btn-success" value="Sign Up!">
                </fieldset>

                <p><a class="toggleForms">Log In</a></p>

            </form>

            <!-- Login form -->
            <form method="post" id="loginform">
                <p>Login usig your email and password</p>
                <fieldset class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Your email">
                </fieldset>

                <fieldset class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Your password">
                </fieldset>

                <fieldset class="checkbox">
                    Stay Logged in:
                    <input type="checkbox" name="stayloggedin" value="1">
                </fieldset>

                <fieldset class="form-group">
                    <input type="hidden" name="signup" value="0">
                    <input type="submit" name="submit" class="btn btn-success" value="Log In!">
                </fieldset>
                <p><a class="toggleForms">Signup</a></p>

            </form>

        </div>

<?php include('footer.php'); ?>

          



