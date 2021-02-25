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
         '<h1>Your auto-generated password: <strong>'.$data['password'].'</strong></h1>'.
         '<p><strong>NB: Please note your auto generated password as this will not be shown again!</strong></p>'.
         '<p>If you forget your password, do not worry. You can always request a password reset from login page.</p>';
         '</div></div>';

$html .= '<h2>Please capture additional information below (<span class="star">*</span>required):</h2>';

$html .= '<div class="row"><div class="col-lg-12">'.
         '<span class="star">*</span>Name for invoicing:<br/>'.
         Form::textInput('name_invoice',$form['name_invoice'],$input_param).
         '</div></div>';

$html .= '<div class="row"><div class="col-lg-12">'.
         '<span class="star">*</span>Cellphone number:<br/>'.
         Form::textInput('cell',$form['cell'],$input_param).
         '</div></div>';

$html .= '<div class="row"><div class="col-lg-12">'.
         'Alternative email address:<br/>'.
         Form::textInput('email_alt',$form['email_alt'],$input_param).
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

      
echo $html;          

//print_r($form);
//print_r($data);
?>
