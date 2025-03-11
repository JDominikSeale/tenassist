<?php

include_once 'server.php';

 function checkAPI($apiKey){
    $serverInstance = new serverCon();
    $conn = $serverInstance->conn;
    $sql = "SELECT
                id
            FROM 
                company
            LEFT JOIN
                api ON company.id = api.company_id
            WHERE
                api.key = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $apiKey);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows > 0){
        return $result->fetch_assoc()['id'];
    } else {
        return false;
    }
}

function clData($company){
    $companyID = checkAPI(findCompany($company));
    if ($companyID == false){
        return http_response_code(401);
    }
    $serverInstance = new serverCon();
    $conn = $serverInstance->conn;
    $sql ="SELECT
            customer.id, 
            customer.first_name, 
            customer.last_name, 
            customer.email, 
            customer.phone
        FROM
            customer
        LEFT JOIN robin ON customer.id = robin.case_id
        WHERE
            (
                robin.sent_date = '' OR robin.sent_date IS NULL
            ) AND robin.company_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $companyID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows > 0){
        return $result->fetch_assoc();
    } else {
        return false;
    }
};

function findCompany($company){
    $serverInstance = new serverCon();
    $conn = $serverInstance->conn;
    $sql = "SELECT
                id
            FROM 
                client
            WHERE
                company_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $company);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows > 0){
        return $result->fetch_assoc()['id'];
    } else {
        return false;
    }
}

function checkAPIKeyExist($companyID){
    $serverInstance = new serverCon();
    $conn = $serverInstance->conn;
    $sql = "SELECT
                id
            FROM 
                api
            WHERE
                company_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $companyID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows > 0){
        return true;
    } else {
        return false;
    }
}

function insertAPIKey($companyID, $apiKey){
    $serverInstance = new serverCon();
    $conn = $serverInstance->conn;
    $sql = "INSERT INTO
                api (company_id, key)
            VALUES
                (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $companyID, $apiKey);
    $stmt->execute();
    $stmt->close();
}

function updateAPIKey($companyID, $apiKey){
    $serverInstance = new serverCon();
    $conn = $serverInstance->conn;
    $sql = "UPDATE
                api
            SET
                key = ?
            WHERE
                company_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $apiKey, $companyID);
    $stmt->execute();
    $stmt->close();
}

function apiLogic($company){
    $exist = checkAPIKeyExist($company);
    $apiKey = bin2hex(random_bytes(16));
    if ($exist == false){
        insertAPIKey($company, $apiKey);
    } elseif ($exist == true){
        updateAPIKey($company, $apiKey);
    }
}

function genAPIKey($company){
    switch ($company){
        case 'Legal Eagle':
            $companyID = findCompany($company);
            apiLogic($companyID);
            break;
        default:
            return http_response_code(204);
    }
}