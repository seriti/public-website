<?php
use Seriti\Tools\Form;
use Seriti\Tools\Html;

$input_param = ['class'=>'form-control'];


$html = '';

$html .= '<div class="row">'.
         '<div class="col-lg-6">';

$html .= '<h2>Congratulations you are registered! Please capture additional information below:</h2>';

$html .= '<div class="row"><div class="col-lg-12">'.
         'Your registered name: <strong>'.$form['name'].'</strong>'.
         '</div></div>';

$html .= '<div class="row"><div class="col-lg-12">'.
         'Your registered email: <strong>'.$form['email'].'</strong>'.
         '</div></div>';

$html .= '<div class="row"><div class="col-lg-12">'.
         'Your auto-generated password: <strong>'.$data['password'].'</strong>'.
         '</div></div>';

$html .= '<h2>Please capture additional information below:</h2>';

$html .= '<div class="row"><div class="col-lg-12">'.
         'Alternative email address:<br/>'.
         Form::textInput('email_alt',$form['email_alt'],$input_param).
         '</div></div>';

$html .= '<div class="row"><div class="col-lg-12">'.
         'Cellphone number:<br/>'.
         Form::textInput('cell',$form['cell'],$input_param).
         '</div></div>';

$html .= '<div class="row"><div class="col-lg-12">'.
         'Landline number:<br/>'.
         Form::textInput('tel',$form['tel'],$input_param).
         '</div></div>';

$html .= '<div class="row"><div class="col-lg-12">'.
         'Your physical address:<br/>'.
         Form::textAreaInput('address',$form['address'],20,5,$input_param).'<br/>'.
         '</div></div>';


$html .= '<div class="row"><div class="col-lg-12">'.
         '<input type="submit" class="btn btn-primary" id="register_button" value="Save my details" onclick="link_download("register_button");">'.
         '</div></div>'; 

$html .= '</div>'.
         '<div class="col-lg-6">';        
                        
$html .= '<p><strong>NB: Please note your auto generated password as this will not be shown again!</strong></p>'.
         '<p>If you forget your password, do not worry. You can at any point request a password reset or login token to be emailed to you from login screen.</p>';
        
$html .= '</div>'.
         '</div>';      
      
echo $html;          

//print_r($form);
//print_r($data);
?>
