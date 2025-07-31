<?php

echo "=== TESTING PDF UPLOAD FOR TRAINING ===\n\n";

// Configuration
$baseUrl = 'http://localhost:8000/api';
$email = 'admin@training.com';
$password = 'password123';

// Function to make HTTP request with file upload
function makeMultipartRequest($url, $data, $filePath, $headers = []) {
    $ch = curl_init();
    
    // Prepare multipart data
    $postData = [];
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            // For arrays, we need to send them as individual fields
            foreach ($value as $subKey => $subValue) {
                $postData[$key . '[' . $subKey . ']'] = $subValue;
            }
        } else {
            $postData[$key] = $value;
        }
    }
    
    // Add file if exists
    if ($filePath && file_exists($filePath)) {
        $postData['pdf_material'] = new CURLFile($filePath, 'application/pdf', basename($filePath));
    }
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    if ($headers) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'response' => $response,
        'error' => $error
    ];
}

// Function for JSON requests
function makeJsonRequest($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data && ($method === 'POST' || $method === 'PUT')) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    if ($headers) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'response' => $response,
        'error' => $error
    ];
}

// Step 1: Login to get token
echo "🔐 STEP 1: LOGIN\n";
$loginData = [
    'email' => $email,
    'password' => $password
];

$headers = [
    'Content-Type: application/json',
    'Accept: application/json'
];

$result = makeJsonRequest($baseUrl . '/login', 'POST', $loginData, $headers);

echo "Status Code: {$result['http_code']}\n";
if ($result['error']) {
    echo "Error: {$result['error']}\n";
    exit;
}

$loginResponse = json_decode($result['response'], true);

if (!isset($loginResponse['data']['access_token'])) {
    echo "❌ Login failed! Response: {$result['response']}\n";
    exit;
}

$token = $loginResponse['data']['access_token'];
echo "✅ Token obtained: " . substr($token, 0, 30) . "...\n\n";

// Step 2: Create Training with PDF file
echo "📚 STEP 2: CREATE TRAINING WITH PDF\n";

$trainingData = [
    'title' => 'PDF Upload Test Training - ' . date('Y-m-d H:i:s'),
    'description' => 'Testing PDF upload functionality for training materials.',
    'trainer_name' => 'Test Trainer',
    'trainer_email' => 'trainer@test.com',
    'training_date' => '2025-08-20',
    'start_time' => '09:00',
    'end_time' => '17:00',
    'location' => 'Test Room',
    'max_participants' => 15
];

$headers = [
    'Accept: application/json',
    'Authorization: Bearer ' . $token
];

$pdfPath = 'sample_training.pdf';
echo "PDF file exists: " . (file_exists($pdfPath) ? 'YES' : 'NO') . "\n";
echo "PDF file size: " . (file_exists($pdfPath) ? filesize($pdfPath) . ' bytes' : 'N/A') . "\n";

$result = makeMultipartRequest($baseUrl . '/trainings', $trainingData, $pdfPath, $headers);

echo "Status Code: {$result['http_code']}\n";
if ($result['error']) {
    echo "cURL Error: {$result['error']}\n";
}

$createResponse = json_decode($result['response'], true);
echo "Response: " . json_encode($createResponse, JSON_PRETTY_PRINT) . "\n\n";

if ($result['http_code'] === 201 && isset($createResponse['data']['id'])) {
    $trainingId = $createResponse['data']['id'];
    echo "✅ Training created successfully! ID: {$trainingId}\n";
    
    // Check if PDF path is saved
    $pdfPath = $createResponse['data']['pdf_material'] ?? null;
    echo "PDF Material Path: " . ($pdfPath ? $pdfPath : 'NULL') . "\n\n";
    
    // Step 3: Verify training details
    echo "🔍 STEP 3: VERIFY TRAINING DETAILS\n";
    $result = makeJsonRequest($baseUrl . '/trainings/' . $trainingId, 'GET', null, [
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ]);
    
    $trainingDetails = json_decode($result['response'], true);
    if (isset($trainingDetails['data']['pdf_material'])) {
        echo "Verified PDF Material: " . ($trainingDetails['data']['pdf_material'] ?? 'NULL') . "\n";
        
        if ($trainingDetails['data']['pdf_material']) {
            $fullPath = storage_path('app/public/' . $trainingDetails['data']['pdf_material']);
            echo "Full file path: {$fullPath}\n";
            echo "File exists on disk: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
            if (file_exists($fullPath)) {
                echo "File size on disk: " . filesize($fullPath) . " bytes\n";
            }
        }
    }
    
} else {
    echo "❌ Training creation failed!\n";
    if (isset($createResponse['errors'])) {
        echo "Validation errors:\n";
        foreach ($createResponse['errors'] as $field => $errors) {
            echo "  {$field}: " . implode(', ', $errors) . "\n";
        }
    }
}

echo "\n=== DEBUG INFORMATION ===\n";
echo "PHP upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "PHP post_max_size: " . ini_get('post_max_size') . "\n";
echo "PHP file_uploads: " . (ini_get('file_uploads') ? 'enabled' : 'disabled') . "\n";
echo "Current directory: " . getcwd() . "\n";

echo "\n=== TESTING COMPLETED ===\n";
