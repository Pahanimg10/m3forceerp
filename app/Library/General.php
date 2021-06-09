<?php

namespace App\Library;

use Illuminate\Support\Facades\DB;

class General
{

    function sendMail($to, $subject, $message, $from, $cc=null){
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $headers .= "From: $from\r\n" .
                    "Reply-To: $from\r\n" .
                    "X-Mailer: PHP/" . phpversion();
        if($cc != null){
           $headers .= "Cc: $cc\r\n";
        }
        
        return mail($to, $subject, $message, $headers);
    } 
    
}
