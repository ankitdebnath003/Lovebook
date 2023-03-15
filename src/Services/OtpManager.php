<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Username;
use App\Entity\Userotp;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The Class is used to manage all otp related functions.
 */
class OtpManager 
{    
    /**
     *   @var string
     *     Stores the object of PHPMailer class.
     */
    private $mail;
    /**
     *   @var string
     *     Stores the password of sender's email id.
     */
    private $mailPass;
    /**
     *   @var object
     *     Stores the object of EntityManagerInterface Class.
     */
    private $em;
    
    /**
     * Constructor is used to set PHPMailer class object and the password of email id.
     *
     *   @param string $pass
     *     Stores the password of the email id.
     * 
     *   @return void
     */
    public function __construct(string $pass, object $em) {
        $this->mail = new PHPMailer(TRUE);
        $this->mailPass = $pass;
        $this->em = $em;
    }
    
    /**
     * The function is used to send the otp to user's email id.
     *
     *   @param string $email
     *     Stores the user's email id.
     *   @param int $otp
     *     Stores the otp.
     * 
     *   @return string
     *     Based on the mail sent to the user.
     */
    public function sendOtp(string $email, int $otp) {
        try {
            $this->mail->isSMTP();
            // Setting up the host as gmail.com								
            $this->mail->Host	 = 'smtp.gmail.com';					
            $this->mail->SMTPAuth = TRUE;							
            // The email id from where the mail will be sent.
            $this->mail->Username = 'rupamdas674@gmail.com';				
            $this->mail->Password = $this->mailPass;
            $this->mail->SMTPSecure = 'tls';				
            $this->mail->Port = 587;
            $this->mail->setFrom('rupamdas674@gmail.com');	
            $this->mail->isHTML(TRUE);				
            $this->mail->addAddress($email);	
            $this->mail->Subject = 'Verify Your Email Id';
            // Define the body of the mail.
            $this->mail->Body = "Your otp is : $otp<br>Use this to verify your email id";
            if ($this->mail->send()) {
                return "OTP has been sent to your email id";
            }
        }
        catch (Exception $e) {
            return "Can't send mail to your email id";
        }
    }
    public function sendMail(string $email, string $link) {
        try {
            $this->mail->isSMTP();
            // Setting up the host as gmail.com								
            $this->mail->Host	 = 'smtp.gmail.com';					
            $this->mail->SMTPAuth = TRUE;							
            // The email id from where the mail will be sent.
            $this->mail->Username = 'rupamdas674@gmail.com';				
            $this->mail->Password = $this->mailPass;
            $this->mail->SMTPSecure = 'tls';				
            $this->mail->Port = 587;
            $this->mail->setFrom('rupamdas674@gmail.com');	
            $this->mail->isHTML(TRUE);				
            $this->mail->addAddress($email);	
            $this->mail->Subject = 'Verify Your Email Id';
            // Define the body of the mail.
            $this->mail->Body = "Your otp is : $link<br>Use this to verify your email id";
            if ($this->mail->send()) {
                return "OTP has been sent to your email id";
            }
        }
        catch (Exception $e) {
            return "Can't send mail to your email id";
        }
    }
    
    /**
     * The function is used to check if the otp exists in the database or not.
     *
     *   @param int $otp
     *     Stores the otp.
     *   @param object $em
     *     Stores the object of EntityManagerInterface class.
     * 
     *   @return bool
     *     Based on the availability of the otp in the database.
     */
    public function checkOtp(int $otp) {
        $otpExist = $this->em->getRepository(Userotp::class)->findOneBy(['otp' => $otp]);
        if ($otpExist != NULL) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Used to check if email exist in the database to store its otp.
     * 
     *   @param string $email
     *     Stores the email id of the user.
     *   @param object $em
     *     Stores the object of EntityManagerInterface class. 
     * 
     *   @return bool
     *     based on availability of the email id.
     */
    public function checkEmail(string $email) {
        $emailExist = $this->em->getRepository(Userotp::class)->findOneBy(['email' => $email]);
        if ($emailExist != NULL) {
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * The function is used to set otp into the database.
     *
     *   @param string $email
     *     Stores the email.
     *   @param int $otpno
     *     Stores the otp.
     *   @param bool $flag
     *     Stores the boolean value if the email exists or not.
     *   @param object $em
     *     Stores the object of EntityManagerInterface class.
     * 
     *   @return void
     */
    public function setOtp(string $email, int $otpno, bool $flag) {
        if ($flag) {
            $user = $this->em->getRepository(Userotp::class)->findOneBy(['email' => $email]);
            $user->setOtp($otpno);
            $this->em->persist($user);
            $this->em->flush();
        }
        else {
            $user = new Userotp();
            $user->setUserOtp($email, $otpno);
            $this->em->persist($user);
            $this->em->flush();
        }
    }
}