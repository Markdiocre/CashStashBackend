<?php

class Auth
{
    protected $pdo, $gm;

    public function __construct(\PDO $pdo, GlobalMethods $gm)
    {
        $this->pdo = $pdo;
        $this->gm = $gm;
    }

    public function checkPassword($pword, $db_pword)
    {
        return $db_pword === crypt($pword, $db_pword);
    }

    public function generateSalt($length)
    {
        $str_hash = md5(uniqid(mt_rand(), true));
        $b64string = base64_encode($str_hash);
        $mb64string = str_replace("+", '.', $b64string);
        return substr($mb64string, 0, $length);
    }

    public function encrypt_password($pword)
    {
        $hashFormat = "$2y$10$";
        $saltLength = 22;
        $salt = $this->generateSalt($saltLength);
        return crypt($pword, $hashFormat . $salt);
    }

    public function login($data)
    {
        $username = $data->username;
        $password = $data->password;
        $sql = "CALL authLogin(?)";

        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([$username]);
            if ($stmt->rowCount() > 0) {
                $res = $stmt->fetchAll()[0];
                if ($this->checkPassword($password, $res['fld_password'])){
                    return array("msg"=>"Nyekis");
                }else{
                    return $this->gm->response_payload(null, "failed", "Username and password does not match", 400);

                }
            }else{
                return $this->gm->response_payload(null, "failed", "Account doesn't exist", 404);
            }
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function register($data)
    {
        $sql = "CALL registerUser(?,?,?,?,?)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $data->password = $this->encrypt_password($data->password);
            if ($stmt->execute([$data->fname, $data->lname, $data->email, $data->username, $data->password])) {
                $status = $stmt->fetch();
                if($status['@is_success'] == 1){
                    return $this->gm->response_payload(null, "success", "Successfully registered!", 200);
                }else if($status['@is_success'] == 0){
                    return $this->gm->response_payload($status['is_success'], "failed", "Username or Email is already registered", 400);
                }
            }
            return $this->gm->response_payload(null, "failed", "Cannot register user", 400);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function tokenGen($id)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode(['fld_personal_id' => $id]);
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, SECRET_KEY, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
        return array("token" => $jwt);
    }

    public function verifyToken($jwt)
    {
        $decoded = explode(".", $jwt);
        $payload = json_decode(str_replace(['+', '/', '='], ['-', '_', ''], base64_decode($decoded[1])));
        $signature = hash_hmac('sha256', $decoded[0] . "." . $decoded[1], SECRET_KEY, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        if ($base64UrlSignature === $decoded[2]) {
            return array("payload" => $payload, "is_valid" => True);
        }
    }
}
