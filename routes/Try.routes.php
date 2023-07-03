<?php

    if($req[0] == 'try'){ echo json_encode($auth->tokenGen(2)); return;}
    if($req[0]=='jwt'){ echo json_encode($auth->verifyToken("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmbGRfcGVyc29uYWxfaWQiOjJ9.MIio7hBHNKZ0lwFOwHZ33xVVj7L4xoPWm5Z6xP3aPew"));  return;}
    // if($req[0] == 'tokenvalid'){ echo "bobo"; return;}
    // if($req[0]=='try'): echo "Tanga"; endif; return;