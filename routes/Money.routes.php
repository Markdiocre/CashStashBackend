<?php

if($req[0]=='pocketmoney'){echo json_encode($money->getPocketMoney()); return;}
if($req[0]=='getTransactions'){echo json_encode($money->getTransactions()); return;}
if($req[0]=='makeTransaction'){echo json_encode($money->makeTransaction($data_input)); return;}
if($req[0]=='updateTransaction'){echo json_encode($money->updateTransaction($data_input)); return;}
if($req[0]=='deleteTransaction'){echo json_encode($money->deleteTransaction($data_input)); return;}
