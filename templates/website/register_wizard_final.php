<?php
use Seriti\Tools\Form;
use Seriti\Tools\Html;

$input_param = ['class'=>'form-control'];


$html = '';

$html .= '<div class="row">'.
         '<div class="col-lg-6">';

$html .= '<h2>Thankyou for your time, we now have the following info on record:</h2>';

$html .= '<div class="row"><div class="col-lg-12">'.
         'Your registered name: <strong>'.$form['name'].'</strong>'.
         '</div></div>';

$html .= '<div class="row"><div class="col-lg-12">'.
         'Your registered email: <strong>'.$form['email'].'</strong>'.
         '</div></div>';

$html .= '<div class="row"><div class="col-lg-12">'.
         'Your Cellphone number: <strong>'.$form['cell'].'</strong>'.
         '</div></div>';

$html .= '<div class="row"><div class="col-lg-12">'.
         'Your Invoice name: <strong>'.$form['name_invoice'].'</strong>'.
         '</div></div>';

$html .= '<div class="row"><div class="col-lg-12">'.
         'Your Alternative email: <strong>'.$form['email_alt'].'</strong>'.
         '</div></div>';

$html .= '<div class="row"><div class="col-lg-12">'.
         'Your Landline number: <strong>'.$form['tel'].'</strong>'.
         '</div></div>';

$html .= '<div class="row"><div class="col-lg-12">'.
         'Your physical address: <strong>'.$form['address'].'</strong>'.
         '</div></div>';

$html .= '</div>'.
         '<div class="col-lg-6">';        
                        
$html .= '<p><strong>You are registered and logged in!</strong></p>'.
         '<p><strong>Please use menu above to continue navigating site.</strong></p>'.
         '<p>Your current login should last for 30 days from this device unless you logout or delete site cookies.</p>';
         '<p>If you forget your password, do not worry. You can at any point request a password reset or login token to be emailed to you from login screen.</p>';
        
$html .= '</div>'.
         '</div>';      
      
echo $html;          

//print_r($form);
//print_r($data);
?>
