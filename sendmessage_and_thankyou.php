<?php
session_start();

function isValidEmail($email){ 
	return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}


if(isset($_POST['form-first-name']) && isset($_POST['form-email']) && isset($_POST['form-about-yourself']))
	{
	
		$admin_email = 'dmitry@somedomain.comx';
		$subject = "A new admin contact message from Dima BI Insights Admin Panel";

		$firstname = htmlspecialchars (substr($_POST['form-first-name'], 0, 50));
		$email = htmlspecialchars (substr($_POST['form-email'], 0, 50));
		$about = htmlspecialchars (substr($_POST['form-about-yourself'], 0, 500));
		
		$firstname=str_replace('"', "", $firstname);
		$firstname = str_replace("'", "", $firstname);
		$firstname = stripslashes($firstname);	
		
		$email=str_replace('"', "", $email);
		$email = str_replace("'", "", $email);
		$email = stripslashes($email);	

		$about=str_replace('"', "", $about);
		$about = str_replace("'", "", $about);
		$about = stripslashes($about);

		$headers = "From: " . strip_tags($admin_email) . "\r\n";
		$headers .= "Reply-To: ". strip_tags($email) . "\r\n";		
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";	

		if (isValidEmail($email))
		{

			$date = new DateTime("now", new DateTimeZone('Asia/Jerusalem') );
			$cur_dt =  $date->format('d-m-Y H:i:s');  
			$db_cur_dt = $date->format('Y-m-d H:i:s'); // same format as NOW()			
		  
			$message = '<html><body>Hello, BI Insights!' . '<br/><br/>' .
						 'Youve got a new message from the contact form!' . '<br/><br/>' .
						 'The message sent on: <b>' . $cur_dt . '</b><br/>' .
						 'Sender name: <b>' . $firstname . '</b><br/>' .
						 'Sender email: <b>' . $email . '</b><br/>' .						
						 'Message: <b>' . $about . '</b><br/><br/>' .
						 'Regards, ' . ' <br/>' .
						 'Admin.</body></html>';
			
		 	if (!mail($admin_email, "$subject", $message, $headers)) 
			{			
				// everything is not good... :) :) :)
			}
			else
			{
				// everything is good...
			}
		
		}		
	
	}

	header("Location: contactus_thankyou.php"); 
?>
