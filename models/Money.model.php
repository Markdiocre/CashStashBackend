<?php

class Money
{
    protected $pdo, $gm, $auth;

    public function __construct(\PDO $pdo, GlobalMethods $gm, Auth $auth)
    {
        $this->pdo = $pdo;
        $this->gm = $gm;
        $this->auth = $auth;
    }

    public function getPocketMoney()
    {
        $token_check = $this->auth->verifyToken();
        if ($token_check["is_valid"]) {
            $sql = "CALL getPocketMoney(?)";
            try {
                $stmt = $this->pdo->prepare($sql);

                if ($stmt->execute([$token_check["id"]])) {
                    $res = $stmt->fetch();
                    return $this->gm->response_payload($res, "success", "Pocket money successfully fetched", 200);
                } else {
                    return $this->gm->response_payload(null, "failed", "Failed to execute query", 400);
                }
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        } else {
            return $this->gm->response_payload(null, "failed", "You are not Authorized. Please log in first", 403);
        }
    }

    public function getTransactions(){
        $token_check = $this->auth->verifyToken();
        if ($token_check["is_valid"]) {
            $sql = "CALL getTransactions(?)";
            try {
                $stmt = $this->pdo->prepare($sql);

                if ($stmt->execute([$token_check["id"]])) {
                    $res = $stmt->fetchAll();
                    if($stmt->rowCount() >= 1){
                        return $this->gm->response_payload($res, "success", "Transactions successfully fetched", 200);
                    }
                    return $this->gm->response_payload(null, "success", "You dont have any transactions", 204);
                } else {
                    return $this->gm->response_payload(null, "failed", "Failed to execute query", 400);
                }
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        } else {
            return $this->gm->response_payload(null, "failed", "You are not Authorized. Please log in first", 403);
        }
    }

    public function makeTransaction($data){
        $token_check = $this->auth->verifyToken();
        if ($token_check["is_valid"]) {
            $sql = "CALL makeTransaction(?,?,?,?)";
            try {
                $stmt = $this->pdo->prepare($sql);
                if ($stmt->execute([$token_check["id"],$data->value, $data->type, $data->description])) {
                    return $this->gm->response_payload(null, "success", "Successfully inserted data", 200);
                } else {
                    return $this->gm->response_payload(null, "failed", "Failed to execute query", 400);
                }
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        } else {
            return $this->gm->response_payload(null, "failed", "You are not Authorized. Please log in first", 403);
        }
    }

    public function updateTransaction($data){
        $token_check = $this->auth->verifyToken();
        if ($token_check["is_valid"]) {
            $sql = "CALL updateTransaction(?,?,?,?)";
            try {
                $stmt = $this->pdo->prepare($sql);
                if ($stmt->execute([$data->money_id,$data->value, $data->type, $data->description])) {
                    return $this->gm->response_payload(null, "success", "Successfully updated data", 200);
                } else {
                    return $this->gm->response_payload(null, "failed", "Failed to execute query", 400);
                }
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        } else {
            return $this->gm->response_payload(null, "failed", "You are not Authorized. Please log in first", 403);
        }
    }

    public function deleteTransaction($data){
        $token_check = $this->auth->verifyToken();
        if ($token_check["is_valid"]) {
            $sql = "CALL deleteTransaction(?)";
            try {
                $stmt = $this->pdo->prepare($sql);
                if ($stmt->execute([$data->money_id])) {
                    return $this->gm->response_payload(null, "success", "Successfully deleted data", 200);
                } else {
                    return $this->gm->response_payload(null, "failed", "Failed to execute query", 400);
                }
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        } else {
            return $this->gm->response_payload(null, "failed", "You are not Authorized. Please log in first", 403);
        }
    }
}
