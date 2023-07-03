<?php

class GlobalMethods
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function callRoutine($proc, $dt = null)
    {
        $params = null;
        $data = null;
        $msg = "Unable to process request";
        $remarks = "failed";
        $values = array();
        $sql = "";

        if ($dt != null) {
            $params = $dt->payload;
            foreach ($params as $key => $value) {
                array_push($values, $value);
            }
            $sql = "CALL $proc(" . str_repeat("?,", count($values) - 1) . "?);";
        } else {
            $sql = "CALL $proc()";
        }
        $stmt = $this->pdo->prepare($sql);

        try {
            if (count($values) > 0) {
                $stmt->execute($values);
                if ($stmt->rowCount() > 0) {
                    if ($res = $stmt->fetchAll()) : $data = $res;
                    endif;
                    $stmt->closeCursor();
                    $remarks = "success";
                    $msg = "Successfully performed the request operation";
                    $code = 200;
                }
            } else {
                $data = array();
                if ($result = $this->pdo->query($sql)->fetchAll()) {
                    foreach ($result as $record) {
                        array_push($data, $record);
                    }
                    $stmt->closeCursor();
                    $remarks = "success";
                    $msg = "Successfully performed the request operation";
                    $code = 200;
                } else {
                    $msg = 'No Data Found';
                    $code = 404;
                }
            }

            return $this->response_payload($data, $remarks, $msg, $code);
        } catch (\PDOException $e) {
            $msg = $e->getMessage();
            $code = 403;
            echo $e->getMessage();
            return $this->response_payload(null, $remarks, $msg, $code);
        }
    }

    public function response_payload($payload, $remarks, $message, $code)
    {
        $status = array("remarks" => $remarks, "message" => $message);
        http_response_code($code);
        return array("status" => $status, "payload" => $payload, "timestamp" => date_create(), "prepared_by" => "Mark Thaddeus Manuel");
    }
}
