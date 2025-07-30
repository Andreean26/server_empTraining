<?php

// Test script untuk menambahkan training via API
echo "=== TESTING POST /api/trainings ===\n\n";

// Configuration
$baseUrl = 'http://localhost:8000/api';
$email = 'admin@training.com';
$password = 'password123';

// Function to make HTTP request
function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data) {
        if ($method === 'POST' || $method === 'PUT') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
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

$result = makeRequest($baseUrl . '/login', 'POST', $loginData, $headers);

echo "Status Code: {$result['http_code']}\n";
if ($result['error']) {
    echo "Error: {$result['error']}\n";
    exit;
}

$loginResponse = json_decode($result['response'], true);
echo "Response: " . json_encode($loginResponse, JSON_PRETTY_PRINT) . "\n\n";

if (!isset($loginResponse['data']['access_token'])) {
    echo "❌ Login failed! Cannot proceed.\n";
    exit;
}

$token = $loginResponse['data']['access_token'];
echo "✅ Token obtained: " . substr($token, 0, 30) . "...\n\n";

// Step 2: Create Training
echo "📚 STEP 2: CREATE TRAINING\n";
$trainingData = [
    'title' => 'Advanced Laravel API Development - ' . date('Y-m-d H:i:s'),
    'description' => 'Comprehensive training on Laravel API development including authentication, validation, and best practices. This course covers advanced topics like API versioning, rate limiting, and testing strategies.',
    'trainer_name' => 'John Smith',
    'trainer_email' => 'john.smith@company.com',
    'training_date' => '2025-08-15',
    'start_time' => '09:00',
    'end_time' => '17:00',
    'location' => 'Training Room A - Building 1',
    'max_participants' => 25,
    'additional_info' => [
        'duration_hours' => 8,
        'category' => 'Technical',
        'objectives' => [
            'Master Laravel API development',
            'Implement authentication with Sanctum',
            'Build robust validation systems',
            'Create comprehensive API tests',
            'Understand API versioning strategies'
        ],
        'requirements' => [
            'Basic PHP knowledge',
            'Laravel fundamentals',
            'Understanding of REST principles',
            'Laptop with development environment'
        ],
        'materials' => [
            'Laravel documentation',
            'API testing tools (Postman/Insomnia)',
            'Code samples and exercises',
            'Project templates'
        ],
        'schedule' => [
            'Day 1: API Foundations & Authentication',
            'Day 2: Advanced Features & Validation', 
            'Day 3: Testing & Best Practices'
        ]
    ]
];

$headers = [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Bearer ' . $token
];

$result = makeRequest($baseUrl . '/trainings', 'POST', $trainingData, $headers);

echo "Status Code: {$result['http_code']}\n";
if ($result['error']) {
    echo "Error: {$result['error']}\n";
}

$createResponse = json_decode($result['response'], true);
echo "Response: " . json_encode($createResponse, JSON_PRETTY_PRINT) . "\n\n";

if ($result['http_code'] === 201) {
    echo "✅ Training created successfully!\n";
    $trainingId = $createResponse['data']['id'];
    echo "📋 Training ID: {$trainingId}\n\n";
    
    // Step 3: Get created training
    echo "🔍 STEP 3: GET CREATED TRAINING\n";
    $result = makeRequest($baseUrl . '/trainings/' . $trainingId, 'GET', null, [
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ]);
    
    echo "Status Code: {$result['http_code']}\n";
    $getResponse = json_decode($result['response'], true);
    echo "Training Details: " . json_encode($getResponse, JSON_PRETTY_PRINT) . "\n\n";
    
} else {
    echo "❌ Training creation failed!\n";
}

// Step 4: Get all trainings to verify
echo "📋 STEP 4: GET ALL TRAININGS\n";
$result = makeRequest($baseUrl . '/trainings', 'GET', null, [
    'Accept: application/json',
    'Authorization: Bearer ' . $token
]);

echo "Status Code: {$result['http_code']}\n";
$listResponse = json_decode($result['response'], true);

if (isset($listResponse['data']['data'])) {
    echo "Total Trainings: " . count($listResponse['data']['data']) . "\n";
    echo "Recent Trainings:\n";
    foreach (array_slice($listResponse['data']['data'], 0, 5) as $training) {
        echo "  🎯 ID: {$training['id']} - {$training['title']}\n";
        echo "     📅 " . ($training['training_date'] ?? 'No date') . " ({$training['start_time']} - {$training['end_time']})\n";
        echo "     👨‍🏫 Trainer: {$training['trainer_name']}\n";
        echo "     📍 Location: " . ($training['location'] ?? 'No location') . "\n\n";
    }
} else {
    echo "No training data found\n";
}

echo "=== TESTING COMPLETED ===\n";

// Optional: Test with curl commands
echo "\n💡 EQUIVALENT CURL COMMANDS:\n\n";

echo "# 1. Login:\n";
echo "curl -X POST {$baseUrl}/login \\\n";
echo "  -H \"Content-Type: application/json\" \\\n";
echo "  -d '{\"email\":\"{$email}\",\"password\":\"{$password}\"}'\n\n";

echo "# 2. Create Training (replace YOUR_TOKEN with actual token):\n";
echo "curl -X POST {$baseUrl}/trainings \\\n";
echo "  -H \"Content-Type: application/json\" \\\n";
echo "  -H \"Authorization: Bearer YOUR_TOKEN\" \\\n";
echo "  -d '" . json_encode($trainingData) . "'\n\n";

echo "# 3. Get All Trainings:\n";
echo "curl -X GET {$baseUrl}/trainings \\\n";
echo "  -H \"Authorization: Bearer YOUR_TOKEN\"\n\n";
