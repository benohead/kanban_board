<?php
class User {
	public $user_active = 0;
	private $clean_email;
	public $status = false;
	private $clean_password;
	private $username;
	private $displayname;
	public $sql_failure = false;
	public $mail_failure = false;
	public $email_taken = false;
	public $username_taken = false;
	public $displayname_taken = false;
	public $activation_token = 0;
	public $success = NULL;
	public $added_by_admin = 0;

	function __construct($user, $display, $pass, $email, $added_by_admin) {
		//Used for display only
		$this->displayname = $display;

		//Sanitize
		$this->clean_email = sanitize($email);
		$this->clean_password = trim($pass);
		$this->username = sanitize($user);

		$this->addedByAdmin = $added_by_admin;
		
		if (usernameExists($this->username)) {
			$this->username_taken = true;
		} else
			if (displayname_exists($this->displayname)) {
				$this->displayname_taken = true;
			} else
				if (email_exists($this->clean_email)) {
					$this->email_taken = true;
				} else {
					//No problems have been found.
					$this->status = true;
				}
	}

	public function userAddUser() {
		global $mysqli, $email_activation, $website_url, $db_table_prefix;

		//Prevent this function being called if there were construction errors
		if ($this->status) {
			//Construct a secure hash for the plain text password
			$secure_pass = generate_hash($this->clean_password);

			//Construct a unique activation token
			$this->activation_token = generate_activation_token();

			//Do we need to send out an activation email?
			if ($email_activation == "true") {
				//User must activate their account first
				$this->user_active = 0;

				$mail = new userMail();

				//Build the activation message
				$activation_message = translate('You will need to activate your account before you can login. Please follow the link below to activate your account. \n\n%1$sactivate-account.php?token=%2$s', $website_url, $this->activation_token);

				//Define more if you want to build larger structures
				$hooks = array (
					"searchStrs" => array (
						"#ACTIVATION-MESSAGE",
						"#ACTIVATION-KEY",
						"#USERNAME#"
					),
					"subjectStrs" => array (
						$activation_message,
						$this->activation_token,
						$this->displayname
					)
				);

				/* Build the template - Optional, you can just use the sendMail function
				 Instead to pass a message. */
				$this->mail_failure = !$mail->newTemplateMsg("new-registration.txt", $hooks);
				//Send the mail. Specify users email here and subject.
				//SendMail can have a third parementer for message if you do not wish to build a template.
				$this->mail_failure = $this->mail_failure && (!$mail->sendMail($this->clean_email, "New User"));
				if (!$this->mail_failure) {
					if ($this->addedByAdmin) {
						$this->success = translate('User successfully registered and activation email sent.');
					}
					else {
						$this->success = translate('You have successfully registered. You will soon receive an activation email.<br>You must activate your account before logging in.');
					}
				}
			} else {
				//Instant account activation
				$this->user_active = 1;
				if ($this->addedByAdmin) {
					$this->success = translate('User successfully registered and activated.');
				}
				else {
					$this->success = translate('You have successfully registered. You can now login <a href="login.php">here</a>.');
				}
			}

			if (!$this->mail_failure) {
				//Insert the user into the database providing no errors have been found.
				$stmt = $mysqli->prepare("INSERT INTO " . $db_table_prefix . "users (user_name, display_name, password, email, activation_token, last_activation_request, lost_password_request, active, title, sign_up_stamp, last_sign_in_stamp)
														VALUES (?, ?, ?, ?, ?, '" . time() . "', '0', ?, 'New Member', '" . time() . "', '0' )");

				$stmt->bind_param("sssssi", $this->username, $this->displayname, $secure_pass, $this->clean_email, $this->activation_token, $this->user_active);
				$stmt->execute();
				$inserted_id = $mysqli->insert_id;
				$stmt->close();

				//Insert default role into matches table
				$stmt = $mysqli->prepare("INSERT INTO " . $db_table_prefix . "user_roles (user_id, role_id) VALUES (?, '1')");
				$stmt->bind_param("s", $inserted_id);
				$stmt->execute();
				$stmt->close();
			}
		}
	}
}
?>