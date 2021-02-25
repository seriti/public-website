<?php
use Seriti\Tools\Form;
use Seriti\Tools\Html;

$input_param = ['class'=>'form-control'];


$import_options = ['BANK_SBSA'=>'Standard Bank Current Account CSV dump',
                   'BANK_SBSA_CC'=>'Standard Bank Credit Card CSV dump'];


$html = '';

$html .= '<div class="row">'.
         '<div class="col-lg-6">';

if($data['user_created']) {

   $html .= '<h2>You are already registered!</h2>'.
            '<input type="submit" class="btn btn-primary" value="Continue">';

   $html .= '<div class="row"><div class="col-lg-12">'.
            'Your registered name: <strong>'.$form['name'].'</strong>'.
            '</div></div>';

   $html .= '<div class="row"><div class="col-lg-12">'.
            'Your registered email: <strong>'.$form['email'].'</strong>'.
            '</div></div>';
} else {
   $html .= '<div class="row"><div class="col-lg-12">'.
            'What name do you wish to use:<br/>'.
            Form::textInput('name',$form['name'],$input_param).
            '</div></div>';

   $html .= '<div class="row"><div class="col-lg-12">'.
            'What is your email address:<br/>'.
            Form::textInput('email',$form['email'],$input_param).'<br/>'.
            'Please verify email address:<br/>'.
            Form::textInput('email_repeat',$form['email_repeat'],$input_param).'<br/>'.
            '</div></div>';

   $html .= '<div class="row"><div class="col-lg-12">'.
            '<input type="submit" class="btn btn-primary" id="register_button" value="Register me" onclick="link_download("register_button");">'.
            '</div></div>'; 
}

$html .= '</div>'.
         '<div class="col-lg-6">';        
                        
$html .= '<div class="row"><div class="col-lg-12">'.
         '<p><b>NB1:</b> A secure password will be auto-generated and emailed to you(please check your spam folder if you do not receive it). You can reset password at your next login if you would like to change it.</p>'.
         '<p><b>NB2:</b> Every user must have a unique email address. You cannot register twice with the same email address.</p>'.
         '</div></div>';       
        
$html .= '</div>'.
         '</div>';      
      
echo $html;          

//print_r($form);
//print_r($data);
?>
