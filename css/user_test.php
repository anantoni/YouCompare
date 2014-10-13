<?php
include_once 'user.php';
include_once 'DB_DEFINES.php';

$failed_tests=0;
$successful_tests=0;

$user1= new user("than", NOT_LOGGED_IN);
echo "<h2>STARTING TEST FOR CLASS USER</h2>";
if($user1->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>FAILED TO CREATE A USER OBJECT</b><br>";
}

echo "Login attempt with wrong username: ";
$user1->login("than1989");
if($user1->get_errno()==DB_OK)
{
    $failed_tests++;
    echo "<b>Sucessful login, failure<b><br>";
}
else
{
    $successful_tests++;
    echo "Failed to login, success<br>";
}

unset($user1);

$user1= new user("thanos", NOT_LOGGED_IN);
if($user1->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>FAILED TO CREATE A USER OBJECT</b><br>";
}

echo "Login attempt with wrong password: ";
$user1->login("than89");
if($user1->get_errno()==DB_OK)
{
    $failed_tests++;
    echo "<b>Sucessful login, failure<b><br>";
}
else
{
    $successful_tests++;
    echo "Failed to login, success<br>";
}

echo "Checking login status: ";
if($user1->is_logged_in()==LOGGED_IN)
{
   $failed_tests++;
   echo "<b>The user is logged in, failure</b><br>";
}
else
{
    $successful_tests++;
    echo "The user isn't logged in, success<br>";
}

echo "Login attempt with correct credentials: ";
$user1->login("than1989");
if($user1->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>Failed to login, failure<b><br>";
}
else
{
    $successful_tests++;
    echo "Sucessful login, success<br>";
}

echo "Checking login status: ";
if($user1->is_logged_in()!=LOGGED_IN)
{
   $failed_tests++;
   echo "<b>The user isn't logged in, failure</b><br>";
}
else
{
    $successful_tests++;
    echo "The user is logged in, success<br>";
}

echo "<br>Retrieving username: ".$user1->get_username()."<br>";
if($user1->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>Failed to retrieve info</b><br>";
}
else
{
    $successful_tests++;
}
echo "Retrieving name of the user: ".$user1->get_name()."<br>";
if($user1->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>Failed to retrieve info</b><br>";
}
else
{
    $successful_tests++;
}
echo "Retrieving surname of the user: ".$user1->get_surname()."<br>";
if($user1->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>Failed to retrieve info</b><br>";
}
else
{
    $successful_tests++;
}
echo "Retrieving full name of the user: ".$user1->get_full_name()."<br>";
if($user1->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>Failed to retrieve info</b><br>";
}
else
{
    $successful_tests++;
}
echo "Retrieving the email of the user: ".$user1->get_email()."<br>";
if($user1->get_errno()!=DB_OK)
{
    
    echo "<b>Failed to retrieve info</b><br>";
}
else
{
    $successful_tests++;
}


echo "<br>";
$prof=new profile_info;
$prof->username="thanoukos";
$prof->email="th_galan@hotmail.com";
$prof->name=NULL;
$prof->surname=NULL;
$user2= new user("thanoukos", NOT_LOGGED_IN);
$user2->register($prof, "1111");
echo "Registering a new user: ";
if($user2->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>Failed to register the user</b><br>";
}
else 
{
    $successful_tests++;
    echo "Success<br>";
}
echo "Trying to register the same user again, expecting failure: ";
$user2->register($prof, "1111");
if($user2->get_errno()==DB_OK)
{
    $failed_tests++;
    echo "<b>Registration complete, expected failure</b><br>";
}
else
{
    $successful_tests++;
    echo "Registration failed, success<br>";
}
echo "Login attempt with unverified user: ";
$user2->login("1111");
if($user2->get_errno()==DB_OK)
{
    $failed_tests++;
    echo "<b>Sucessful login, failure</b><br>";
}
else
{
    $successful_tests++;
    echo "Failed to login, success<br>";
}

echo "Verifying new user: ";
$user2->verify_user(md5($prof->username));
if($user2->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>Failed to verify the user</b><br>";
}
else
{
    $successful_tests++;
    echo "Success<br>";
}
echo "Verifying user(again), failure expected: ";
$user2->verify_user(md5($prof->username));
if($user2->get_errno()==DB_OK)
{
    $failed_tests++;
    echo "<b>User verified, expected failuer</b><br>";
}
else
{
    $successful_tests++;
    echo "User allready verified, Success<br>";
}
echo "Login attempt after verification: ";
$user2->login("1111");
if($user2->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>Failed to login, failure<b><br>";
}
else
{
    $successful_tests++;
    echo "Sucessful login, success<br>";
}

echo "Rating category: ";
$user2->rate_category(8, 2);
if($user2->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>Failed to rate </b>".$user1->get_errno()."<br>";
}
else
{
    $successful_tests++;
    echo "Succesfull rating<br>";
}
echo "Rating category again, must fail: ";
$user2->rate_category(8, 2);
if($user2->get_errno()==DB_OK)
{
    $failed_tests++;
    echo "<b>Succesfull rating</b><br>";
}
else
{
    $successful_tests++;
    echo "Rating failed<br>";
}

echo "Rating entity: ";
$user2->rate_entity(16, 2);
if($user2->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>Failed to rate </b>".$user1->get_errno()."<br>";
}
else
{
    $successful_tests++;
    echo "Succesfull rating<br>";
}
echo "Rating entity again, must fail: ";
$user2->rate_entity(16, 2);
if($user2->get_errno()==DB_OK)
{
    $failed_tests++;
    echo "<b>Succesfull rating</b><br>";
}
else
{
    $successful_tests++;
    echo "Rating failed<br>";
}
$user2->set_username("Chuck_Norris");
echo "Changing username to Chuck_Norris: ";
if($user2->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>Failed to update username". $user2->get_errno()." </b><br>";
}
else
{
    $successful_tests++;
    echo "Username updated<br>";
}
echo "Retrieving username: ".$user2->get_username()."<br>";
if($user2->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>Failed to retrieve info</b><br>";
}
else
{
    $successful_tests++;
}
$user2->set_email("th.galan@gmail.com");
echo "Changing mail to th.galan@gmail.com: ";
if($user2->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>Failed to update email</b><br>";
}
else
{
    $successful_tests++;
    echo "Email updated<br>";
}
echo "Retrieving mail: ".$user2->get_email()."<br>";
if($user2->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>Failed to retrieve info</b><br>";
}
else
{
    $successful_tests++;
}

$user2->set_name("Thanasis");
echo "Changing name to Thanasis: ";
if($user2->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>Failed to update name</b><br>";
}
else
{
    $successful_tests++;
    echo "Name updated<br>";
}
echo "Retrieving name: ".$user2->get_name()."<br>";
if($user2->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>Failed to retrieve info</b><br>";
}
else
{
    $successful_tests++;
}
$user2->set_surname("Galanos");
echo "Changing surname to Galanos: ";
if($user2->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>Failed to update surname</b><br>";
}
else
{
    $successful_tests++;
    echo "Surname updated<br>";
}
echo "Retrieving surname: ".$user2->get_surname()."<br>";
if($user2->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>Failed to retrieve info</b><br>";
}
else
{
    $successful_tests++;
}

$user2->forgot_pasword($user2->get_username());
if($user2->get_errno()==DB_OK)
{
    $failed_tests++;
    echo "<b>Password changed while logged in, failure</b><br>";
}
else
{
    echo "Failed to change password, success<br>";
    $successful_tests++;
}

$user2->delete_account();
echo "Deleting account: ";
if($user2->get_errno()!=DB_OK)
{
    $failed_tests++;
    echo "<b>Failed to delete the account</b><br>";
}
else
{
    $successful_tests++;
    echo "Account deleted<br>";
}

echo "<h2>END OF TEST</h2>";

echo "<h3>Statistics</h3>";
echo "Successful test: $successful_tests<br>";
echo "Failed tests: $failed_tests<br>";
$rate= $successful_tests/($successful_tests+$failed_tests)*100;
echo "Success rate: $rate %";
?>