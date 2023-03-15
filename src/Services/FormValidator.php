<?php
namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Username;
use App\Entity\Userotp;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormValidator 
{
    /**
     *   @var string
     *     stores the firstname of the user.
     */
    private $fName;    
    /**
     *   @var string
     *     stores the lastname of the user.
     */
    private $lName;     
    /**
     *   @var string
     *     stores the username of the user.
     */
    private $uName;     
    /**
     *   @var string
     *     stores the email of the user.
     */
    private $email;     
    /**
     *   @var int
     *     stores the otp.
     */
    private $otp;    
    /**
     *   @var string
     *     stores the password.
     */
    private $pass;    
    /**
     *   @var string
     *     stores the confirm password.
     */
    private $cPass;
    /**
     *   @var object
     *     stores the EntityManagerInterface Object.
     */
    private $em;
        
    /**
     * Constructor is used to store the user's data in class variables.
     *
     *   @param string $fName
     *     Stores the firstname.
     *   @param string $lName
     *     Stores the lastname.
     *   @param string $uName
     *     Stores the username.
     *   @param string $email
     *     Stores the email.
     *   @param int $otp
     *     Stores the otp.
     *   @param string $pass
     *     Stores the password.
     *   @param string $cPass
     *     Stores the confirm password.
     *   @param object $em
     *     Stores the object of EntityManagerInterface class.
     * 
     *   @return void
     */ 
    public function __construct(string $fName, string $lName, string $uName, string $email, int $otp, string $pass, string $cPass, object $em) {
        $this->fName = $fName;
        $this->lName = $lName;
        $this->uName = $uName;
        $this->email = $email;
        $this->otp = $otp;
        $this->pass = $pass;
        $this->cPass = $cPass;
        $this->em = $em;
    }
    
    /**
     * The function is used to validate the form's data.
     *
     *   @return mixed
     *     Based on the validity of the form.
     */
    public function validateForm() {

        // Check the length of the name.
        if (strlen($this->fName) < 4 or strlen($this->lName) < 4) {
            return "Name field is too short";
        }

        // Check if name has a number in it.
        elseif (preg_match('~[0-9]+~', $this->fName) or (preg_match('~[0-9]+~', $this->lName))) {
			return "Name field can't contain any number";
		} 

        // Check if name has special characters in it.
		elseif (preg_match("/[\'^£$%&*()}{@#~?><>,|=_+¬-]/", $this->fName) or preg_match("/[\'^£$%&*()}{@#~?><>,|=_+¬-]/", $this->lName)) {
			return "Name field can't contain any special character";
		}

        // Check if username exist in the database or not.
        elseif ($this->checkUserName()) {
            return "Username exist. Provide another username";
        }

        // Check if email id exist in the database or not.
        elseif ($this->checkEmail()) {
            return "Email exist. Provide another email";
        }

        // Check if the otp is valid.
        elseif ($this->validOtp()) {
            return "OTP is invalid. Check your email for correct otp.";
        }

        // Check the pattern of the email.
        elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
			return "Email Id is not valid";
		}

        // Check if the both passwords are same or not.
        elseif ($this->pass != $this->cPass) {
            return "Passwords do not match. Please try again.";
        }

        // Check if password is strong or not.
        elseif ($this->passCheck()) {
            return "Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.";
        }
        return 1;
    }

    /**
     * The function is used to check if the password is strong or not.
     *
     *   @return boolean
     *     Based on strong password.
     */
    public function passCheck() {
        // Variables to check if password is strong.
        $upperCase = preg_match('@[A-Z]@', $this->pass);
        $lowerCase = preg_match('@[a-z]@', $this->pass);
        $number = preg_match('@[0-9]@', $this->pass);
        $specialChars = preg_match('@[^\w]@', $this->pass);
        
        if (strlen($this->pass) < 8 or !$upperCase or !$lowerCase or !$number or !$specialChars) {
            return TRUE;
        }
    }

    /**
     * The function is used to check availability of the data in the database.
     *
     *   @return bool
     *     Based on the availability of the data.
     */
    public function checkUserName() {    
        $user = $this->em->getRepository(Username::class)->findOneBy(['username' => $this->uName]);
        if ($user != NULL) {
            return TRUE;
        }
    }
    
    /**
     * The function is used to check availability of the data in the database.
     *
     *   @return bool
     *     Based on the availability of the data.
     */
    public function checkEmail() {    
        $data = $this->em->getRepository(Username::class)->findOneBy(['email' => $this->email]);
        if ($data != NULL) {
            return TRUE;
        }
    }

    /**
     * The function is used to validate the otp.
     *
     *   @return bool
     *     Based on valid otp.
     */
    public function validOtp() {
        $data = $this->em->getRepository(Userotp::class)->findOneBy(['email' => $this->email]);
        $checkOtp = $data->getOtp();
        if ($checkOtp != $this->otp) {
            return TRUE;
        } 
    }
}
?>