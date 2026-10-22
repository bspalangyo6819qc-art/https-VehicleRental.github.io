<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('escapePolicyText')) {
    function escapePolicyText($value): string
    {
        return e($value);
    }
}

if (!isset($escapePolicyText)) {
    $escapePolicyText = static function ($value): string {
        return escapePolicyText($value);
    };
}

if (!isset($escape)) {
    $escape = static function ($value): string {
        return e($value);
    };
}

if (!function_exists('escapeHtml')) {
    function escapeHtml($value): string
    {
        return e($value);
    }
}

if (!function_exists('isLocalDevelopmentRequest')) {
    function isLocalDevelopmentRequest(): bool
    {
        $host = strtolower((string) parse_url('http://' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST));
        $address = $_SERVER['REMOTE_ADDR'] ?? '';

        return in_array($host, ['localhost', '127.0.0.1', '::1'], true)
            && in_array($address, ['127.0.0.1', '::1'], true);
    }
}

if (!function_exists('app_redirect')) {
    function app_redirect(string $location): void
    {
        header('Location: ' . $location, true, 303);
        exit;
    }
}

if (!function_exists('app_find_vehicle')) {
    function app_find_vehicle($identifier): ?array
    {
        foreach ($_SESSION['vehicles'] as $vehicle) {
            if ((string) ($vehicle['slug'] ?? '') === (string) $identifier
                || (string) ($vehicle['id'] ?? '') === (string) $identifier) {
                return $vehicle;
            }
        }

        return null;
    }
}

if (!function_exists('app_set_pending_rental_vehicle')) {
    function app_set_pending_rental_vehicle($identifier): void
    {
        $vehicle = app_find_vehicle($identifier);
        if ($vehicle === null) {
            unset($_SESSION['pending_rental_vehicle']);
            return;
        }

        $_SESSION['pending_rental_vehicle'] = (string) $vehicle['slug'];
    }
}

if (!function_exists('app_redirect_after_customer_auth')) {
    function app_redirect_after_customer_auth(string $fallback = 'index.php'): void
    {
        $vehicleSlug = $_SESSION['pending_rental_vehicle'] ?? '';
        unset($_SESSION['pending_rental_vehicle']);

        if (is_string($vehicleSlug) && $vehicleSlug !== '') {
            app_redirect('rentflow.php?id=' . rawurlencode($vehicleSlug));
        }

        app_redirect($fallback);
    }
}

if (!function_exists('app_next_id')) {
    function app_next_id(string $collection): int
    {
        $next = (int) ($_SESSION['next_ids'][$collection] ?? 1);
        $_SESSION['next_ids'][$collection] = $next + 1;

        return $next;
    }
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['vehicles'])) {
    $_SESSION['vehicles'] = [
        ['id' => 1, 'slug' => 'toyota-vios', 'brand' => 'Toyota', 'name' => 'Toyota Vios', 'category' => 'Car', 'category_id' => 1, 'daily_rate' => 2600, 'seats' => 4, 'fuel_type' => 'Gas', 'transmission' => 'Automatic', 'status' => 'available', 'model_year' => 2024, 'mileage_km' => 18000, 'engine' => '1.3L DOHC', 'location' => 'Quezon City', 'insurance' => 'Comprehensive', 'description' => 'A dependable and efficient city car designed for comfort and fuel economy.'],
        ['id' => 2, 'slug' => 'honda-city', 'brand' => 'Honda', 'name' => 'Honda City', 'category' => 'Car', 'category_id' => 1, 'daily_rate' => 3300, 'seats' => 4, 'fuel_type' => 'Gas', 'transmission' => 'Automatic', 'status' => 'available', 'model_year' => 2023, 'mileage_km' => 22000, 'engine' => '1.5L i-VTEC', 'location' => 'Makati', 'insurance' => 'Comprehensive', 'description' => 'A comfortable sedan for city drives and weekend trips.'],
        ['id' => 3, 'slug' => 'bmw-x5', 'brand' => 'BMW', 'name' => 'BMW X5', 'category' => 'SUV', 'category_id' => 2, 'daily_rate' => 7200, 'seats' => 5, 'fuel_type' => 'Gas', 'transmission' => 'Automatic', 'status' => 'available', 'model_year' => 2022, 'mileage_km' => 26000, 'engine' => '3.0L TwinPower Turbo', 'location' => 'Taguig', 'insurance' => 'Comprehensive', 'description' => 'A spacious premium SUV with a refined ride and modern features.'],
        ['id' => 4, 'slug' => 'ford-escape', 'brand' => 'Ford', 'name' => 'Ford Escape', 'category' => 'SUV', 'category_id' => 2, 'daily_rate' => 6100, 'seats' => 5, 'fuel_type' => 'Gas', 'transmission' => 'Automatic', 'status' => 'available', 'model_year' => 2021, 'mileage_km' => 31000, 'engine' => '2.0L EcoBoost', 'location' => 'Pasig', 'insurance' => 'Comprehensive', 'description' => 'A versatile SUV suited to family travel and longer journeys.'],
        ['id' => 5, 'slug' => 'honda-wave-alpha', 'brand' => 'Honda', 'name' => 'Honda Wave Alpha', 'category' => 'Motorcycle', 'category_id' => 3, 'daily_rate' => 1200, 'seats' => 2, 'fuel_type' => 'Gas', 'transmission' => 'Manual', 'status' => 'available', 'model_year' => 2024, 'mileage_km' => 5000, 'engine' => '110cc', 'location' => 'Quezon City', 'insurance' => 'Basic', 'description' => 'An economical motorcycle for quick trips around the city.'],
        ['id' => 6, 'slug' => 'mitsubishi-montero', 'brand' => 'Mitsubishi', 'name' => 'Mitsubishi Montero', 'category' => 'SUV', 'category_id' => 2, 'daily_rate' => 8200, 'seats' => 7, 'fuel_type' => 'Diesel', 'transmission' => 'Automatic', 'status' => 'available', 'model_year' => 2023, 'mileage_km' => 19000, 'engine' => '2.4L MIVEC Diesel', 'location' => 'Makati', 'insurance' => 'Comprehensive', 'description' => 'A roomy seven-seat SUV for comfortable group travel.'],
    ];
}

$_SESSION['categories'] = $_SESSION['categories'] ?? [
    ['id' => 1, 'name' => 'Car', 'description' => 'Comfortable sedans for city and highway travel.', 'icon' => ''],
    ['id' => 2, 'name' => 'SUV', 'description' => 'Versatile vehicles with extra passenger and cargo space.', 'icon' => ''],
    ['id' => 3, 'name' => 'Motorcycle', 'description' => 'Agile two-wheel transport for local trips.', 'icon' => ''],
];
$_SESSION['users'] = $_SESSION['users'] ?? [];
$_SESSION['bookings'] = $_SESSION['bookings'] ?? [];
$_SESSION['reviews'] = $_SESSION['reviews'] ?? [];
$_SESSION['payments'] = $_SESSION['payments'] ?? [];
$_SESSION['maintenance'] = $_SESSION['maintenance'] ?? [];
$_SESSION['customer_user'] = $_SESSION['customer_user'] ?? [];
$_SESSION['admin_user'] = $_SESSION['admin_user'] ?? [];
if (isLocalDevelopmentRequest()) {
    $hasDemoCustomer = false;
    foreach ($_SESSION['users'] as $user) {
        if ((int) ($user['id'] ?? 0) === -1) {
            $hasDemoCustomer = true;
            break;
        }
    }
    if (!$hasDemoCustomer) {
        $_SESSION['users'][] = [
            'id' => -1,
            'name' => 'Jordan Reyes',
            'full_name' => 'Jordan Reyes',
            'email' => 'user123@autoride.test',
            'phone' => '+639171234567',
            'address' => 'Makati City, Metro Manila',
            'profile_picture' => 'sample-profile.svg',
            'password_hash' => '',
            'status' => 'active',
            'created_at' => '2024-02-15',
            'is_demo' => true,
        ];
    }
    if ((int) ($_SESSION['customer_user']['id'] ?? 0) === -1) {
        foreach ($_SESSION['users'] as $user) {
            if ((int) ($user['id'] ?? 0) === -1) {
                unset($user['password_hash']);
                $_SESSION['customer_user'] = array_merge($_SESSION['customer_user'], $user);
                break;
            }
        }
    }
}
$_SESSION['admin_password_hash'] = $_SESSION['admin_password_hash'] ?? '$2y$10$THCWx0yp88AZdwIBxakc5OoGJ8wzo0FuqMSoYSRo2dnBXYug1Vo9m';
$_SESSION['next_ids'] = $_SESSION['next_ids'] ?? [
    'vehicle' => 7,
    'category' => 4,
    'user' => 1,
    'booking' => 1,
    'review' => 1,
    'payment' => 1,
    'maintenance' => 1,
];

foreach ($_SESSION['vehicles'] as $index => &$vehicle) {
    if ((int) ($vehicle['id'] ?? 0) >= 1 && (int) ($vehicle['id'] ?? 0) <= 6
        && !isset($vehicle['is_demo'])) {
        $vehicle['is_demo'] = true;
    }
    $vehicle['plate_number'] = $vehicle['plate_number'] ?? ('DEMO-' . str_pad((string) ($vehicle['id'] ?? ($index + 1)), 4, '0', STR_PAD_LEFT));
    $vehicle['image'] = $vehicle['image'] ?? 'vehicle-placeholder.svg';
    $vehicle['model'] = $vehicle['model'] ?? ($vehicle['name'] ?? 'Sample vehicle');
}
unset($vehicle);

if (isLocalDevelopmentRequest()
    && empty($_SESSION['demo_admin_data_seeded'])
    && count($_SESSION['users']) <= 1
    && $_SESSION['bookings'] === []
    && $_SESSION['payments'] === []
    && $_SESSION['reviews'] === []
    && $_SESSION['maintenance'] === []) {
    $sampleCustomers = [
        ['id' => -2, 'name' => 'Alyssa Santos', 'full_name' => 'Alyssa Santos', 'email' => 'alyssa.santos@example.test', 'phone' => '+639171112201', 'address' => 'Makati City, Metro Manila', 'status' => 'active', 'created_at' => '2025-11-08', 'is_demo' => true],
        ['id' => -3, 'name' => 'Miguel Dela Cruz', 'full_name' => 'Miguel Dela Cruz', 'email' => 'miguel.delacruz@example.test', 'phone' => '+639171112202', 'address' => 'Quezon City, Metro Manila', 'status' => 'active', 'created_at' => '2025-12-14', 'is_demo' => true],
        ['id' => -4, 'name' => 'Sofia Garcia', 'full_name' => 'Sofia Garcia', 'email' => 'sofia.garcia@example.test', 'phone' => '+639171112203', 'address' => 'Pasig City, Metro Manila', 'status' => 'disabled', 'created_at' => '2026-01-19', 'is_demo' => true],
        ['id' => -5, 'name' => 'Noah Villanueva', 'full_name' => 'Noah Villanueva', 'email' => 'noah.villanueva@example.test', 'phone' => '+639171112204', 'address' => 'Taguig City, Metro Manila', 'status' => 'active', 'created_at' => '2026-03-03', 'is_demo' => true],
    ];
    $_SESSION['users'] = array_merge($_SESSION['users'], $sampleCustomers);
    foreach ($_SESSION['vehicles'] as &$sampleVehicle) {
        if ((int) ($sampleVehicle['id'] ?? 0) === 4) {
            $sampleVehicle['status'] = 'rented';
        }
    }
    unset($sampleVehicle);
    $_SESSION['bookings'] = [
        ['id' => 1, 'customer_id' => -2, 'full_name' => 'Alyssa Santos', 'email' => 'alyssa.santos@example.test', 'vehicle_id' => 1, 'vehicle_slug' => 'toyota-vios', 'vehicle_name' => 'Toyota Vios', 'pickup_date' => '2026-10-10', 'return_date' => '2026-10-12', 'pickup_time' => '10:00', 'pickup_location' => 'Makati', 'rental_type' => 'self_drive', 'days' => 2, 'rental_days' => 2, 'daily_rate' => 2600, 'driver_fee' => 0, 'insurance_fee' => 0, 'security_deposit' => 5000, 'additional_charges' => 0, 'balance' => 0, 'amount_paid' => 10200, 'payment_status' => 'paid', 'total_amount' => 10200, 'status' => 'completed', 'created_at' => '2026-10-02 09:15:00', 'is_demo' => true],
        ['id' => 2, 'customer_id' => -2, 'full_name' => 'Alyssa Santos', 'email' => 'alyssa.santos@example.test', 'vehicle_id' => 2, 'vehicle_slug' => 'honda-city', 'vehicle_name' => 'Honda City', 'pickup_date' => '2026-10-24', 'return_date' => '2026-10-27', 'pickup_time' => '08:00', 'pickup_location' => 'Quezon City', 'rental_type' => 'self_drive', 'days' => 3, 'rental_days' => 3, 'daily_rate' => 3300, 'driver_fee' => 0, 'insurance_fee' => 0, 'security_deposit' => 5000, 'additional_charges' => 0, 'balance' => 14900, 'amount_paid' => 0, 'payment_status' => 'unpaid', 'total_amount' => 14900, 'status' => 'requested', 'created_at' => '2026-10-20 11:30:00', 'is_demo' => true],
        ['id' => 3, 'customer_id' => -3, 'full_name' => 'Miguel Dela Cruz', 'email' => 'miguel.delacruz@example.test', 'vehicle_id' => 3, 'vehicle_slug' => 'bmw-x5', 'vehicle_name' => 'BMW X5', 'pickup_date' => '2026-10-26', 'return_date' => '2026-10-28', 'pickup_time' => '12:00', 'pickup_location' => 'Taguig', 'rental_type' => 'with_driver', 'days' => 2, 'rental_days' => 2, 'daily_rate' => 7200, 'driver_fee' => 1000, 'insurance_fee' => 0, 'security_deposit' => 5000, 'additional_charges' => 0, 'balance' => 20400, 'amount_paid' => 0, 'payment_status' => 'unpaid', 'total_amount' => 20400, 'status' => 'confirmed', 'created_at' => '2026-10-19 14:05:00', 'is_demo' => true],
        ['id' => 4, 'customer_id' => -4, 'full_name' => 'Sofia Garcia', 'email' => 'sofia.garcia@example.test', 'vehicle_id' => 4, 'vehicle_slug' => 'ford-escape', 'vehicle_name' => 'Ford Escape', 'pickup_date' => '2026-10-21', 'return_date' => '2026-10-23', 'pickup_time' => '14:00', 'pickup_location' => 'Pasig', 'rental_type' => 'self_drive', 'days' => 2, 'rental_days' => 2, 'daily_rate' => 6100, 'driver_fee' => 0, 'insurance_fee' => 0, 'security_deposit' => 5000, 'additional_charges' => 0, 'balance' => 0, 'amount_paid' => 17200, 'payment_status' => 'paid', 'total_amount' => 17200, 'status' => 'active', 'created_at' => '2026-10-18 16:20:00', 'released_at' => '2026-10-21 14:00:00', 'initial_mileage' => 31240, 'release_fuel_level' => '3/4', 'release_condition' => 'Good', 'is_demo' => true],
        ['id' => 5, 'customer_id' => -5, 'full_name' => 'Noah Villanueva', 'email' => 'noah.villanueva@example.test', 'vehicle_id' => 5, 'vehicle_slug' => 'honda-wave-alpha', 'vehicle_name' => 'Honda Wave Alpha', 'pickup_date' => '2026-10-29', 'return_date' => '2026-10-30', 'pickup_time' => '10:00', 'pickup_location' => 'Makati', 'rental_type' => 'self_drive', 'days' => 1, 'rental_days' => 1, 'daily_rate' => 1200, 'driver_fee' => 0, 'insurance_fee' => 0, 'security_deposit' => 5000, 'additional_charges' => 0, 'balance' => 6200, 'amount_paid' => 0, 'payment_status' => 'unpaid', 'total_amount' => 6200, 'status' => 'rejected', 'created_at' => '2026-10-20 08:45:00', 'is_demo' => true],
    ];
    $_SESSION['payments'] = [
        ['id' => 1, 'booking_id' => 1, 'customer_id' => -2, 'customer_name' => 'Alyssa Santos', 'vehicle_name' => 'Toyota Vios', 'amount' => 10200, 'payment_method' => 'card', 'status' => 'paid', 'paid_at' => '2026-10-02 09:20:00', 'reference_code' => 'SAMPLE-PAY-001', 'is_demo' => true],
        ['id' => 2, 'booking_id' => 4, 'customer_id' => -4, 'customer_name' => 'Sofia Garcia', 'vehicle_name' => 'Ford Escape', 'amount' => 17200, 'payment_method' => 'bank_transfer', 'status' => 'paid', 'paid_at' => '2026-10-18 16:25:00', 'reference_code' => 'SAMPLE-PAY-002', 'is_demo' => true],
    ];
    $_SESSION['reviews'] = [
        ['id' => 1, 'customer_id' => -2, 'customer_name' => 'Alyssa Santos', 'vehicle_id' => 1, 'vehicle_slug' => 'toyota-vios', 'vehicle_name' => 'Toyota Vios', 'rating' => 5, 'review' => 'Clean car, smooth pickup, and helpful staff.', 'review_text' => 'Clean car, smooth pickup, and helpful staff.', 'created_at' => '2026-10-13', 'status' => 'approved', 'is_demo' => true],
        ['id' => 2, 'customer_id' => -4, 'customer_name' => 'Sofia Garcia', 'vehicle_id' => 4, 'vehicle_slug' => 'ford-escape', 'vehicle_name' => 'Ford Escape', 'rating' => 4, 'review' => 'Comfortable for our family trip. The return was easy.', 'review_text' => 'Comfortable for our family trip. The return was easy.', 'created_at' => '2026-10-20', 'status' => 'pending', 'is_demo' => true],
    ];
    $_SESSION['maintenance'] = [
        ['id' => 1, 'vehicle_id' => 6, 'vehicle_name' => 'Mitsubishi Montero', 'maintenance_type' => 'Scheduled oil and filter service', 'description' => 'Routine 20,000 km service and inspection.', 'scheduled_date' => '2026-10-25', 'expected_completion_date' => '2026-10-25', 'completed_date' => '', 'cost' => 6800, 'status' => 'scheduled', 'is_demo' => true],
        ['id' => 2, 'vehicle_id' => 3, 'vehicle_name' => 'BMW X5', 'maintenance_type' => 'Brake inspection', 'description' => 'Inspect front pads and replace if needed.', 'scheduled_date' => '2026-10-18', 'expected_completion_date' => '2026-10-18', 'completed_date' => '2026-10-18', 'cost' => 4200, 'status' => 'completed', 'is_demo' => true],
    ];
    $_SESSION['next_ids']['booking'] = max((int) ($_SESSION['next_ids']['booking'] ?? 1), 6);
    $_SESSION['next_ids']['payment'] = max((int) ($_SESSION['next_ids']['payment'] ?? 1), 3);
    $_SESSION['next_ids']['review'] = max((int) ($_SESSION['next_ids']['review'] ?? 1), 3);
    $_SESSION['next_ids']['maintenance'] = max((int) ($_SESSION['next_ids']['maintenance'] ?? 1), 3);
    $_SESSION['demo_admin_data_seeded'] = true;
}

$action = $_POST['action'] ?? '';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && is_string($action)) {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        if ($action === 'check_availability') {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['available' => false, 'error' => 'Your session expired. Reload the page and try again.']);
            exit;
        }
        exit('Your session expired. Reload the page and try again.');
    }

    if ($action === 'check_availability') {
        header('Content-Type: application/json; charset=UTF-8');

        $vehicle = app_find_vehicle($_GET['id'] ?? $_POST['vehicle_id'] ?? '');
        $pickup = $_POST['pickup'] ?? '';
        $returnDate = $_POST['return'] ?? '';
        $pickupDate = is_string($pickup) ? DateTimeImmutable::createFromFormat('!Y-m-d', $pickup) : false;
        $endDate = is_string($returnDate) ? DateTimeImmutable::createFromFormat('!Y-m-d', $returnDate) : false;
        $validDates = $pickupDate && $endDate
            && $pickupDate->format('Y-m-d') === $pickup
            && $endDate->format('Y-m-d') === $returnDate
            && $pickupDate >= new DateTimeImmutable('today')
            && $endDate > $pickupDate;

        if (!$vehicle || ($vehicle['status'] ?? '') !== 'available') {
            http_response_code(404);
            echo json_encode(['available' => false, 'error' => 'This vehicle is not available for rental.']);
            exit;
        }
        if (!$validDates) {
            http_response_code(422);
            echo json_encode(['available' => false, 'error' => 'Choose valid future dates with the return date after pick-up.']);
            exit;
        }

        foreach ($_SESSION['bookings'] as $existingBooking) {
            if ((int) ($existingBooking['vehicle_id'] ?? 0) === (int) $vehicle['id']
                && in_array($existingBooking['status'] ?? '', ['requested', 'confirmed', 'active'], true)
                && $pickup < ($existingBooking['return_date'] ?? '')
                && $returnDate > ($existingBooking['pickup_date'] ?? '')) {
                http_response_code(200);
                echo json_encode(['available' => false, 'error' => 'This vehicle already has a rental request for part of those dates. Choose different dates.']);
                exit;
            }
        }

        echo json_encode(['available' => true]);
        exit;
    }

    $customer = $_SESSION['customer_user'];
    $admin = $_SESSION['admin_user'];

    if (in_array($action, ['login', 'admin_login', 'register'], true)) {
        $identifier = strtolower(trim((string) ($action === 'admin_login' ? ($_POST['username'] ?? '') : ($_POST['email'] ?? ''))));
        $password = $_POST['password'] ?? '';
        if (!is_string($password)) {
            $password = '';
        }

        if ($action === 'register') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $phone = trim((string) ($_POST['phone'] ?? ''));
            $email = filter_var($identifier, FILTER_VALIDATE_EMAIL);
            $confirmed = $_POST['confirm_password'] ?? '';
            if ($name === '' || $phone === '' || !$email || strlen($password) < 8
                || !hash_equals($password, is_string($confirmed) ? $confirmed : '')
                || empty($_POST['terms'])) {
                $_SESSION['signup_error'] = 'Enter valid details, use a password of at least 8 characters, confirm it, and accept the terms.';
            } else {
                $exists = false;
                foreach ($_SESSION['users'] as $user) {
                    if (strtolower($user['email']) === strtolower($email)) {
                        $exists = true;
                        break;
                    }
                }
                if ($exists) {
                    $_SESSION['signup_error'] = 'An account with that email already exists.';
                } else {
                    $newUser = [
                        'id' => app_next_id('user'),
                        'name' => $name,
                        'full_name' => $name,
                        'email' => strtolower($email),
                        'phone' => $phone,
                        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                        'status' => 'active',
                        'created_at' => date('Y-m-d'),
                        'is_demo' => false,
                    ];
                    $_SESSION['users'][] = $newUser;
                    unset($newUser['password_hash']);
                    $_SESSION['customer_user'] = $newUser;
                    $_SESSION['admin_user'] = [];
                    unset($_SESSION['signup_error']);
                    session_regenerate_id(true);
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    app_redirect_after_customer_auth('user_profile.php');
                }
            }
            $_SESSION['login_error'] = '';
        } else {
            $matchedUser = null;
            foreach ($_SESSION['users'] as $user) {
                $matchesIdentifier = $action === 'admin_login'
                    ? strtolower((string) $user['email']) === $identifier
                    : strtolower((string) $user['email']) === $identifier;
                if ($matchesIdentifier && empty($user['is_demo']) && ($user['status'] ?? 'active') === 'active'
                    && password_verify($password, $user['password_hash'])) {
                    $matchedUser = $user;
                    break;
                }
            }

            if ($matchedUser !== null && $action === 'login') {
                unset($matchedUser['password_hash']);
                $matchedUser['name'] = $matchedUser['full_name'];
                $_SESSION['customer_user'] = $matchedUser;
                $_SESSION['admin_user'] = [];
                $_SESSION['login_error'] = '';
                session_regenerate_id(true);
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                app_redirect_after_customer_auth();
            }

            $demoCustomerActive = false;
            foreach ($_SESSION['users'] as $user) {
                if ((int) ($user['id'] ?? 0) === -1) {
                    $demoCustomerActive = ($user['status'] ?? 'active') === 'active';
                    break;
                }
            }
            $demoCustomerMatch = $action === 'login'
                && $identifier === 'user123'
                && password_verify($password, $_SESSION['admin_password_hash'])
                && isLocalDevelopmentRequest()
                && $demoCustomerActive;
            if ($demoCustomerMatch) {
                $_SESSION['customer_user'] = [
                    'id' => -1,
                    'name' => 'Jordan Reyes',
                    'full_name' => 'Jordan Reyes',
                    'email' => 'user123@autoride.test',
                    'phone' => '+639171234567',
                    'address' => 'Makati City, Metro Manila',
                    'profile_picture' => 'sample-profile.svg',
                    'status' => 'active',
                    'created_at' => '2024-02-15',
                    'is_demo' => true,
                ];
                $_SESSION['admin_user'] = [];
                $_SESSION['login_error'] = '';
                session_regenerate_id(true);
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                app_redirect_after_customer_auth();
            }

            $demoMatch = $action === 'admin_login'
                && $identifier === 'user123'
                && password_verify($password, $_SESSION['admin_password_hash'])
                && isLocalDevelopmentRequest();
            if ($demoMatch) {
                $_SESSION['customer_user'] = [];
                $_SESSION['admin_user'] = [
                    'id' => 1,
                    'username' => 'user123',
                    'full_name' => 'User 123',
                    'email' => 'user123@autoride.test',
                    'phone' => 'Not provided',
                    'created_at' => date('Y-m-d'),
                ];
                $_SESSION['login_error'] = '';
                session_regenerate_id(true);
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                app_redirect('admin_panel.php');
            }

            if ($action === 'admin_login' && !isLocalDevelopmentRequest()) {
                $_SESSION['login_error'] = 'The demo administrator account is available only from this computer.';
            } else {
                $_SESSION['login_error'] = 'Invalid email or password.';
            }
            $_SESSION['login_username'] = $identifier;
        }
    } elseif (in_array($action, ['logout', 'admin_logout'], true)) {
        $_SESSION['customer_user'] = [];
        $_SESSION['admin_user'] = [];
        session_regenerate_id(true);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        app_redirect($action === 'admin_logout' ? 'admin_login.php' : 'log_in.php');
    } elseif ($action === 'update_profile' && !empty($customer['id'])) {
        $name = trim((string) ($_POST['full_name'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $address = trim((string) ($_POST['address'] ?? ''));
        if ($name === '' || $phone === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($address) > 255) {
            $_SESSION['profile_error'] = 'Enter a valid name, email, phone number, and address no longer than 255 characters.';
        } else {
            $emailInUse = false;
            foreach ($_SESSION['users'] as $user) {
                if ((int) $user['id'] !== (int) $customer['id'] && strtolower((string) $user['email']) === $email) {
                    $emailInUse = true;
                    break;
                }
            }
            if ($emailInUse) {
                $_SESSION['profile_error'] = 'That email address is already in use.';
                app_redirect('user_profile.php#overview');
            }
            foreach ($_SESSION['users'] as &$user) {
                if ((int) $user['id'] === (int) $customer['id']) {
                    $user['name'] = $user['full_name'] = $name;
                    $user['email'] = $email;
                    $user['phone'] = $phone;
                    $user['address'] = $address;
                    break;
                }
            }
            unset($user);
            $_SESSION['customer_user']['name'] = $_SESSION['customer_user']['full_name'] = $name;
            $_SESSION['customer_user']['email'] = $email;
            $_SESSION['customer_user']['phone'] = $phone;
            $_SESSION['customer_user']['address'] = $address;
            $_SESSION['profile_message'] = 'Your profile has been updated.';
            $_SESSION['profile_error'] = '';
        }
        app_redirect('user_profile.php#overview');
    } elseif ($action === 'change_password' && !empty($admin['id'])) {
        $old = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (!is_string($old) || !password_verify($old, $_SESSION['admin_password_hash'])
            || !is_string($new) || !is_string($confirm)
            || strlen($new) < 8 || !hash_equals($new, $confirm)) {
            $_SESSION['password_error'] = 'Enter your current password and a matching new password of at least 8 characters.';
        } else {
            $_SESSION['admin_password_hash'] = password_hash($new, PASSWORD_DEFAULT);
            $_SESSION['password_message'] = 'Your administrator password has been changed for this session.';
            $_SESSION['password_error'] = '';
        }
        app_redirect('admin_profile.php#password');
    } elseif (in_array($action, ['submit_review', 'submit_browse_review'], true) && !empty($customer['id'])) {
        $slug = (string) ($_POST['vehicle'] ?? $_GET['id'] ?? '');
        $vehicle = app_find_vehicle($slug);
        $rating = filter_var($_POST['rating'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 5]]);
        $text = trim((string) ($_POST['review'] ?? ''));
        if (!$vehicle || !$rating || strlen($text) < 3 || strlen($text) > 2000) {
            $_SESSION['review_error'] = 'Select a vehicle, choose a rating from 1 to 5, and enter a review of 3 to 2,000 characters.';
            $_SESSION['feedback_error'] = $_SESSION['review_error'];
        } else {
            $_SESSION['reviews'][] = [
                'id' => app_next_id('review'),
                'customer_id' => (int) $customer['id'],
                'customer_name' => (string) ($customer['name'] ?? $customer['full_name'] ?? 'Customer'),
                'vehicle_id' => (int) $vehicle['id'],
                'vehicle_slug' => $vehicle['slug'],
                'vehicle_name' => $vehicle['name'],
                'rating' => $rating,
                'review' => $text,
                'review_text' => $text,
                'created_at' => date('Y-m-d'),
                'status' => 'pending',
                'is_demo' => !empty($customer['is_demo']),
            ];
            $_SESSION['review_message'] = 'Thank you. Your review has been saved for this session.';
            $_SESSION['feedback_message'] = $_SESSION['review_message'];
            $_SESSION['feedback_error'] = '';
        }
        $_SESSION['review_rating'] = (string) ($rating ?: 5);
        $_SESSION['review_text'] = $text;
        $_SESSION['feedback_rating'] = (string) ($rating ?: 5);
        $_SESSION['feedback_text'] = $text;
        $_SESSION['feedback_vehicle'] = $slug;
        app_redirect($action === 'submit_review'
            ? 'vehicle_details.php?id=' . rawurlencode($slug) . '#reviews'
            : 'brows_vehicle.php#feedback');
    } elseif ($action === 'request_booking') {
        $vehicle = app_find_vehicle($_POST['vehicle_id'] ?? $_GET['id'] ?? '');
        if (empty($customer['id'])) {
            app_set_pending_rental_vehicle($_POST['vehicle_id'] ?? $_GET['id'] ?? '');
            app_redirect('log_in.php');
        } else {
            $pickup = (string) ($_POST['pickup'] ?? '');
            $returnDate = (string) ($_POST['return'] ?? '');
            $pickupDate = DateTimeImmutable::createFromFormat('!Y-m-d', $pickup);
            $endDate = DateTimeImmutable::createFromFormat('!Y-m-d', $returnDate);
            $validDates = $pickupDate && $endDate
                && $pickupDate->format('Y-m-d') === $pickup
                && $endDate->format('Y-m-d') === $returnDate
                && $pickupDate >= new DateTimeImmutable('today')
                && $endDate > $pickupDate;
            $allowedTimes = ['08:00', '10:00', '12:00', '14:00'];
            $allowedLocations = ['Makati', 'Quezon City', 'Pasig', 'Taguig'];
            if (!$vehicle || ($vehicle['status'] ?? '') !== 'available' || !$validDates
                || empty($_POST['terms'])
                || !in_array($_POST['pickup_time'] ?? '', $allowedTimes, true)
                || !in_array($_POST['pickup_location'] ?? '', $allowedLocations, true)) {
                $_SESSION['booking_error'] = 'Choose an available vehicle, valid future dates, a pickup location, and accept the rental terms.';
            } else {
                $days = (int) $pickupDate->diff($endDate)->days;
                $withDriver = ($_POST['rental_type'] ?? 'self_drive') === 'with_driver';
                $collision = false;
                foreach ($_SESSION['bookings'] as $existingBooking) {
                    if ((int) $existingBooking['vehicle_id'] === (int) $vehicle['id']
                        && in_array($existingBooking['status'], ['requested', 'confirmed', 'active'], true)
                        && $pickup < $existingBooking['return_date']
                        && $returnDate > $existingBooking['pickup_date']) {
                        $collision = true;
                        break;
                    }
                }
                if ($collision) {
                    $_SESSION['booking_error'] = 'This vehicle already has a rental request for part of those dates. Choose different dates.';
                    app_redirect('rentflow.php?id=' . rawurlencode((string) $vehicle['slug']));
                }
                $driverFee = $withDriver ? $days * 500 : 0;
                $total = $days * (float) $vehicle['daily_rate'] + $driverFee + 5000;
                $_SESSION['bookings'][] = [
                    'id' => app_next_id('booking'),
                    'customer_id' => (int) $customer['id'],
                    'full_name' => (string) ($customer['name'] ?? $customer['full_name']),
                    'email' => (string) $customer['email'],
                    'vehicle_id' => (int) $vehicle['id'],
                    'vehicle_slug' => $vehicle['slug'],
                    'vehicle_name' => $vehicle['name'],
                    'pickup_date' => $pickup,
                    'return_date' => $returnDate,
                    'pickup_time' => (string) ($_POST['pickup_time'] ?? ''),
                    'pickup_location' => (string) $_POST['pickup_location'],
                    'rental_type' => $withDriver ? 'with_driver' : 'self_drive',
                    'days' => $days,
                    'rental_days' => $days,
                    'daily_rate' => (float) $vehicle['daily_rate'],
                    'driver_fee' => $driverFee,
                    'insurance_fee' => 0,
                    'security_deposit' => 5000,
                    'balance' => $total,
                    'amount_paid' => 0,
                    'payment_status' => 'unpaid',
                    'total_amount' => $total,
                    'status' => 'requested',
                    'created_at' => date('Y-m-d H:i:s'),
                    'is_demo' => !empty($customer['is_demo']),
                ];
                $_SESSION['booking_success'] = 'Your rental request has been submitted. An administrator can review it during this session.';
                $_SESSION['booking_error'] = '';
            }
        }
        $id = (string) ($_POST['vehicle_id'] ?? $_GET['id'] ?? '');
        app_redirect('rentflow.php?id=' . rawurlencode($id));
    } elseif (!empty($admin['id']) && in_array($action, [
        'booking_decision', 'booking_lifecycle', 'vehicle_add', 'vehicle_update', 'vehicle_delete',
        'booking_payment', 'review_moderation', 'category_add', 'category_update', 'category_delete',
        'rate_update', 'customer_save', 'customer_status', 'maintenance_add', 'maintenance_update',
        'maintenance_delete', 'review_delete',
    ], true)) {
        $adminAction = true;
        $adminReturnSection = [
            'booking_decision' => 'requests-approve',
            'booking_lifecycle' => 'release',
            'booking_payment' => 'payments',
            'review_moderation' => 'reviews',
            'vehicle_add' => 'vehicle-add',
            'vehicle_update' => 'vehicle-edit',
            'vehicle_delete' => 'vehicle-delete',
            'category_add' => 'category-add',
            'category_update' => 'category-edit',
            'category_delete' => 'category-edit',
            'rate_update' => 'rates-update',
            'customer_save' => 'customer-edit',
            'customer_status' => 'customer-disable',
            'maintenance_add' => 'maintenance-add',
            'maintenance_update' => 'maintenance-add',
            'maintenance_delete' => 'maintenance-view',
            'review_delete' => 'reviews',
        ][$action];
        $id = (int) ($_POST['vehicle_id'] ?? 0);
        $bookingId = (int) ($_POST['booking_id'] ?? 0);
        if ($action === 'booking_decision') {
            $decision = $_POST['decision'] ?? '';
            $updated = false;
            $available = true;
            if ($decision === 'confirmed') {
                $targetBooking = null;
                foreach ($_SESSION['bookings'] as $candidate) {
                    if ((int) ($candidate['id'] ?? 0) === $bookingId) {
                        $targetBooking = $candidate;
                        break;
                    }
                }
                if ($targetBooking !== null) {
                    foreach ($_SESSION['bookings'] as $existingBooking) {
                        if ((int) ($existingBooking['id'] ?? 0) !== $bookingId
                            && (int) ($existingBooking['vehicle_id'] ?? 0) === (int) ($targetBooking['vehicle_id'] ?? 0)
                            && in_array($existingBooking['status'] ?? '', ['confirmed', 'active'], true)
                            && ($targetBooking['pickup_date'] ?? '') < ($existingBooking['return_date'] ?? '')
                            && ($targetBooking['return_date'] ?? '') > ($existingBooking['pickup_date'] ?? '')) {
                            $available = false;
                            break;
                        }
                    }
                }
            }
            foreach ($_SESSION['bookings'] as &$booking) {
                if ((int) $booking['id'] === $bookingId && $booking['status'] === 'requested') {
                    if (in_array($decision, ['confirmed', 'rejected'], true) && $available) {
                        $booking['status'] = $decision;
                        if ($decision === 'rejected') {
                            $booking['balance'] = 0;
                            $booking['payment_status'] = 'void';
                        }
                        $updated = true;
                    }
                    break;
                }
            }
            unset($booking);
            $_SESSION['admin_flash_message'] = $updated
                ? ($decision === 'confirmed' ? 'Rental request approved.' : 'Rental request rejected.')
                : (!$available ? 'This vehicle has another approved rental during those dates.' : 'Rental request was not found or has already been processed.');
        } elseif ($action === 'booking_payment') {
            $amount = filter_var($_POST['amount'] ?? null, FILTER_VALIDATE_FLOAT);
            $method = $_POST['payment_method'] ?? '';
            $allowedMethods = ['cash', 'bank_transfer', 'card'];
            $updated = false;
            foreach ($_SESSION['bookings'] as &$booking) {
                $balance = (float) ($booking['balance'] ?? $booking['total_amount'] ?? 0);
                if ((int) ($booking['id'] ?? 0) === $bookingId
                    && in_array($booking['status'] ?? '', ['confirmed', 'active', 'completed'], true)
                    && $balance > 0
                    && $amount !== false
                    && $amount > 0
                    && $amount <= $balance
                    && in_array($method, $allowedMethods, true)) {
                    $booking['amount_paid'] = (float) ($booking['amount_paid'] ?? 0) + $amount;
                    $booking['balance'] = max(0, $balance - $amount);
                    $booking['payment_status'] = $booking['balance'] === 0.0 ? 'paid' : 'partial';
                    $paymentId = app_next_id('payment');
                    $_SESSION['payments'][] = [
                        'id' => $paymentId,
                        'booking_id' => $bookingId,
                        'customer_id' => (int) $booking['customer_id'],
                        'customer_name' => (string) ($booking['full_name'] ?? 'Customer'),
                        'vehicle_name' => (string) $booking['vehicle_name'],
                        'amount' => $amount,
                        'payment_method' => $method,
                        'status' => 'paid',
                        'paid_at' => date('Y-m-d H:i:s'),
                        'reference_code' => 'AR-' . date('ymd') . '-' . str_pad((string) $paymentId, 4, '0', STR_PAD_LEFT),
                        'is_demo' => !empty($booking['is_demo']),
                    ];
                    $updated = true;
                    break;
                }
            }
            unset($booking);
            $_SESSION['admin_flash_message'] = $updated
                ? 'Payment recorded and the balance has been updated.'
                : 'Enter a valid payment amount, method, and outstanding booking.';
        } elseif ($action === 'review_moderation') {
            $reviewId = (int) ($_POST['review_id'] ?? 0);
            $status = $_POST['status'] ?? '';
            $updated = false;
            foreach ($_SESSION['reviews'] as &$review) {
                if ((int) ($review['id'] ?? 0) === $reviewId
                    && in_array($status, ['approved', 'hidden', 'rejected'], true)) {
                    $review['status'] = $status;
                    $updated = true;
                    break;
                }
            }
            unset($review);
            $_SESSION['admin_flash_message'] = $updated
                ? 'Review moderation status updated.'
                : 'Review could not be found or the selected status is invalid.';
        } elseif ($action === 'review_delete') {
            $reviewId = (int) ($_POST['review_id'] ?? 0);
            $exists = false;
            foreach ($_SESSION['reviews'] as $review) {
                if ((int) ($review['id'] ?? 0) === $reviewId) {
                    $exists = true;
                    break;
                }
            }
            if ($exists) {
                $_SESSION['reviews'] = array_values(array_filter($_SESSION['reviews'], static function ($review) use ($reviewId): bool {
                    return (int) ($review['id'] ?? 0) !== $reviewId;
                }));
            }
            $_SESSION['admin_flash_message'] = $exists ? 'Review deleted.' : 'Review could not be found.';
        } elseif ($action === 'booking_lifecycle') {
            $adminReturnSection = ($_POST['to_status'] ?? '') === 'completed' ? 'return' : 'release';
            $updated = false;
            $additionalCharges = filter_var($_POST['additional_charges'] ?? 0, FILTER_VALIDATE_FLOAT);
            $additionalCharges = $additionalCharges === false || $additionalCharges < 0 ? -1 : (float) $additionalCharges;
            $toStatus = $_POST['to_status'] ?? '';
            $mileage = filter_var($_POST['mileage'] ?? null, FILTER_VALIDATE_INT);
            $fuelLevel = $_POST['fuel_level'] ?? '';
            $condition = $_POST['condition'] ?? '';
            $validFuelLevels = ['Full', '3/4', '1/2', '1/4', 'Empty'];
            $validConditions = $toStatus === 'active'
                ? ['Excellent', 'Good', 'Fair']
                : ['Excellent', 'Good', 'Fair', 'Damaged'];
            $lifecycleValid = $mileage !== false && $mileage >= 0
                && in_array($fuelLevel, $validFuelLevels, true)
                && in_array($condition, $validConditions, true)
                && ($toStatus !== 'completed' || $additionalCharges >= 0);
            foreach ($_SESSION['bookings'] as &$booking) {
                if ((int) $booking['id'] === (int) ($_POST['booking_id'] ?? 0)
                    && $booking['status'] === ($_POST['from_status'] ?? '')
                    && in_array($_POST['to_status'] ?? '', ['active', 'completed'], true)
                    && $lifecycleValid
                    && ($toStatus !== 'completed' || $mileage >= (int) ($booking['initial_mileage'] ?? 0))
                    && ($booking['status'] !== 'active' || $additionalCharges >= 0)) {
                    $booking['status'] = $_POST['to_status'];
                    if ($_POST['to_status'] === 'active') {
                        $booking['released_at'] = date('Y-m-d H:i:s');
                        $booking['initial_mileage'] = $mileage;
                        $booking['release_fuel_level'] = $fuelLevel;
                        $booking['release_condition'] = $condition;
                        $booking['release_notes'] = trim((string) ($_POST['notes'] ?? ''));
                        foreach ($_SESSION['vehicles'] as &$vehicle) {
                            if ((int) ($vehicle['id'] ?? 0) === (int) ($booking['vehicle_id'] ?? 0)) {
                                $vehicle['status'] = 'rented';
                                break;
                            }
                        }
                        unset($vehicle);
                    } else {
                        $booking['returned_at'] = date('Y-m-d H:i:s');
                        $booking['return_mileage'] = $mileage;
                        $booking['return_fuel_level'] = $fuelLevel;
                        $booking['return_condition'] = $condition;
                        $booking['damage_notes'] = trim((string) ($_POST['damage_notes'] ?? ''));
                        $booking['additional_charges'] = $additionalCharges;
                        $booking['total_amount'] = (float) ($booking['total_amount'] ?? 0) + $additionalCharges;
                        $booking['balance'] = (float) ($booking['balance'] ?? 0) + $additionalCharges;
                        if ($additionalCharges > 0) {
                            $booking['payment_status'] = (float) ($booking['amount_paid'] ?? 0) > 0 ? 'partial' : 'unpaid';
                        }
                        foreach ($_SESSION['vehicles'] as &$vehicle) {
                            if ((int) ($vehicle['id'] ?? 0) === (int) ($booking['vehicle_id'] ?? 0)) {
                                $vehicle['status'] = 'available';
                                break;
                            }
                        }
                        unset($vehicle);
                    }
                    $updated = true;
                    break;
                }
            }
            unset($booking);
            $_SESSION['admin_flash_message'] = $updated
                ? (($_POST['to_status'] ?? '') === 'active' ? 'Vehicle release recorded.' : 'Vehicle return recorded and charges updated.')
                : (!$lifecycleValid ? 'Enter valid mileage, fuel level, vehicle condition, and additional charges.' : 'Rental record could not be updated because its status or return mileage is invalid.');
        } elseif ($action === 'vehicle_add' || $action === 'vehicle_update') {
            $category = null;
            foreach ($_SESSION['categories'] as $item) {
                if ((int) $item['id'] === (int) ($_POST['category_id'] ?? 0)) {
                    $category = $item;
                    break;
                }
            }
            $slug = trim((string) ($_POST['slug'] ?? ''));
            $name = trim((string) ($_POST['name'] ?? ''));
            $rate = filter_var($_POST['daily_rate'] ?? null, FILTER_VALIDATE_FLOAT);
            $image = trim((string) ($_POST['image'] ?? 'vehicle-placeholder.svg'));
            if (isset($_FILES['vehicle_image']) && ($_FILES['vehicle_image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $upload = $_FILES['vehicle_image'];
                $allowedImageTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                $fileInfo = is_array($upload) && isset($upload['tmp_name']) && is_uploaded_file($upload['tmp_name'])
                    ? finfo_open(FILEINFO_MIME_TYPE)
                    : false;
                $mimeType = $fileInfo ? finfo_file($fileInfo, $upload['tmp_name']) : '';
                if ($fileInfo) {
                    finfo_close($fileInfo);
                }
                if (!is_array($upload) || ($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
                    || ($upload['size'] ?? 0) > 5242880 || !isset($allowedImageTypes[$mimeType])) {
                    $_SESSION['admin_flash_message'] = 'Choose a JPG, PNG, or WebP image smaller than 5 MB.';
                    $image = '';
                } else {
                    $uploadDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'vehicles';
                    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
                        $_SESSION['admin_flash_message'] = 'Vehicle image upload directory could not be created.';
                        $image = '';
                    } else {
                        $filename = bin2hex(random_bytes(16)) . '.' . $allowedImageTypes[$mimeType];
                        if (move_uploaded_file($upload['tmp_name'], $uploadDirectory . DIRECTORY_SEPARATOR . $filename)) {
                            $image = 'uploads/vehicles/' . $filename;
                        } else {
                            $_SESSION['admin_flash_message'] = 'Vehicle image could not be saved.';
                            $image = '';
                        }
                    }
                }
            }
            if (!$category || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)
                || $name === '' || trim((string) ($_POST['brand'] ?? '')) === ''
                || trim((string) ($_POST['plate_number'] ?? '')) === ''
                || !preg_match('/^(?:[A-Za-z0-9][A-Za-z0-9._-]*\.svg|uploads\/vehicles\/[a-f0-9]{32}\.(?:jpg|png|webp))$/', $image)
                || trim((string) ($_POST['fuel_type'] ?? '')) === ''
                || trim((string) ($_POST['transmission'] ?? '')) === ''
                || $rate === false || $rate < 0
                || (int) ($_POST['seats'] ?? 0) < 1 || (int) ($_POST['seats'] ?? 0) > 255
                || (int) ($_POST['model_year'] ?? 0) < 1901 || (int) ($_POST['model_year'] ?? 0) > 2155
                || (int) ($_POST['mileage_km'] ?? -1) < 0) {
                $_SESSION['admin_flash_message'] = 'Vehicle details are incomplete or invalid.';
            } else {
                $existingIndex = null;
                $slugIndex = null;
                foreach ($_SESSION['vehicles'] as $index => $item) {
                    if ($id > 0 && (int) $item['id'] === $id) {
                        $existingIndex = $index;
                    }
                    if ($item['slug'] === $slug) {
                        $slugIndex = $index;
                    }
                }
                $record = [
                    'id' => $action === 'vehicle_update' ? $id : 0,
                    'slug' => $slug, 'brand' => trim((string) ($_POST['brand'] ?? '')),
                    'name' => $name, 'category' => $category['name'], 'category_id' => (int) $category['id'],
                    'daily_rate' => (float) $rate, 'fuel_type' => trim((string) ($_POST['fuel_type'] ?? 'Gas')),
                    'transmission' => trim((string) ($_POST['transmission'] ?? 'Automatic')),
                    'seats' => max(1, (int) ($_POST['seats'] ?? 4)),
                    'model_year' => (int) ($_POST['model_year'] ?? date('Y')),
                    'mileage_km' => max(0, (int) ($_POST['mileage_km'] ?? 0)),
                    'engine' => trim((string) ($_POST['engine'] ?? '')),
                    'location' => trim((string) ($_POST['location'] ?? '')),
                    'insurance' => trim((string) ($_POST['insurance'] ?? 'Comprehensive')),
                    'status' => in_array($_POST['status'] ?? '', ['available', 'rented', 'maintenance', 'inactive'], true) ? $_POST['status'] : 'available',
                    'description' => trim((string) ($_POST['description'] ?? '')),
                    'plate_number' => trim((string) ($_POST['plate_number'] ?? '')),
                    'image' => $image,
                    'model' => trim((string) ($_POST['model'] ?? '')) ?: $name,
                    'is_demo' => false,
                ];
                if ($action === 'vehicle_add' && $id !== 0) {
                    $_SESSION['admin_flash_message'] = 'A new vehicle cannot include an existing vehicle ID.';
                } elseif ($action === 'vehicle_add' && $slugIndex !== null) {
                    $_SESSION['admin_flash_message'] = 'That vehicle slug is already in use.';
                } elseif ($action === 'vehicle_add') {
                    $record['id'] = app_next_id('vehicle');
                    $_SESSION['vehicles'][] = $record;
                    $_SESSION['admin_flash_message'] = 'Vehicle added.';
                } elseif ($id <= 0 || $existingIndex === null) {
                    $_SESSION['admin_flash_message'] = 'The vehicle to update could not be found.';
                } elseif ($slugIndex !== null && $slugIndex !== $existingIndex) {
                    $_SESSION['admin_flash_message'] = 'That vehicle slug is already in use.';
                } else {
                    $_SESSION['vehicles'][$existingIndex] = $record;
                    $_SESSION['admin_flash_message'] = 'Vehicle updated.';
                }
            }
        } elseif ($action === 'vehicle_delete') {
            $hasRental = false;
            $vehicleExists = false;
            foreach ($_SESSION['vehicles'] as $vehicle) {
                if ((int) $vehicle['id'] === $id) {
                    $vehicleExists = true;
                    break;
                }
            }
            foreach ($_SESSION['bookings'] as $booking) {
                if ((int) $booking['vehicle_id'] === $id) {
                    $hasRental = true;
                    break;
                }
            }
            if (!$hasRental && $vehicleExists) {
                $_SESSION['vehicles'] = array_values(array_filter($_SESSION['vehicles'], static function ($item) use ($id): bool {
                    return (int) $item['id'] !== $id;
                }));
            }
            $_SESSION['admin_flash_message'] = $hasRental
                ? 'Vehicles with rental history cannot be deleted.'
                : ($vehicleExists ? 'Vehicle deleted.' : 'Vehicle could not be found.');
        } elseif ($action === 'category_add' || $action === 'category_update') {
            $categoryId = (int) ($_POST['category_id'] ?? 0);
            $name = trim((string) ($_POST['name'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $icon = trim((string) ($_POST['icon'] ?? ''));
            if ($name === '') {
                $_SESSION['admin_flash_message'] = 'Enter a category name.';
            } elseif (strlen($description) > 500 || strlen($icon) > 120) {
                $_SESSION['admin_flash_message'] = 'Category description or icon path is too long.';
            } elseif ($action === 'category_add' && $categoryId !== 0) {
                $_SESSION['admin_flash_message'] = 'A new category cannot include an existing category ID.';
            } else {
                $found = false;
                foreach ($_SESSION['categories'] as &$category) {
                    if ($action === 'category_update'
                        && (int) $category['id'] === $categoryId
                        && $categoryId > 0) {
                        $category['name'] = $name;
                        $category['description'] = $description;
                        $category['icon'] = $icon;
                        foreach ($_SESSION['vehicles'] as &$vehicle) {
                            if ((int) $vehicle['category_id'] === $categoryId) {
                                $vehicle['category'] = $name;
                            }
                        }
                        unset($vehicle);
                        $found = true;
                        break;
                    }
                }
                unset($category);
                if ($action === 'category_add') {
                    $_SESSION['categories'][] = [
                        'id' => app_next_id('category'),
                        'name' => $name,
                        'description' => $description,
                        'icon' => $icon,
                    ];
                    $_SESSION['admin_flash_message'] = 'Category added.';
                } elseif ($found) {
                    $_SESSION['admin_flash_message'] = 'Category updated.';
                } else {
                    $_SESSION['admin_flash_message'] = 'The category to update could not be found.';
                }
            }
        } elseif ($action === 'category_delete') {
            $categoryId = (int) ($_POST['category_id'] ?? 0);
            $assigned = false;
            $categoryExists = false;
            foreach ($_SESSION['vehicles'] as $vehicle) {
                if ((int) $vehicle['category_id'] === $categoryId) {
                    $assigned = true;
                }
            }
            foreach ($_SESSION['categories'] as $category) {
                if ((int) $category['id'] === $categoryId) {
                    $categoryExists = true;
                    break;
                }
            }
            if (!$assigned && $categoryExists) {
                $_SESSION['categories'] = array_values(array_filter($_SESSION['categories'], static function ($item) use ($categoryId): bool {
                    return (int) $item['id'] !== $categoryId;
                }));
            }
            $_SESSION['admin_flash_message'] = $assigned
                ? 'Categories assigned to vehicles cannot be deleted.'
                : ($categoryExists ? 'Category deleted.' : 'Category could not be found.');
        } elseif ($action === 'rate_update') {
            $updated = false;
            $rate = filter_var($_POST['daily_rate'] ?? null, FILTER_VALIDATE_FLOAT);
            $minimumDays = filter_var($_POST['minimum_days'] ?? 1, FILTER_VALIDATE_INT);
            $additionalFee = filter_var($_POST['additional_fee'] ?? 0, FILTER_VALIDATE_FLOAT);
            foreach ($_SESSION['vehicles'] as &$vehicle) {
                if ((int) $vehicle['id'] === $id
                    && $rate !== false && $rate >= 0
                    && $minimumDays !== false && $minimumDays >= 1 && $minimumDays <= 365
                    && $additionalFee !== false && $additionalFee >= 0) {
                    $vehicle['daily_rate'] = (float) $rate;
                    $vehicle['minimum_rental_days'] = (int) $minimumDays;
                    $vehicle['additional_fee'] = (float) $additionalFee;
                    $updated = true;
                    break;
                }
            }
            unset($vehicle);
            $_SESSION['admin_flash_message'] = $updated ? 'Vehicle rate updated.' : 'Enter a valid rate for an existing vehicle.';
        } elseif ($action === 'customer_save' || $action === 'customer_status') {
            $customerId = (int) ($_POST['customer_id'] ?? 0);
            $saved = false;
            foreach ($_SESSION['users'] as &$user) {
                if ((int) $user['id'] === $customerId) {
                    if ($action === 'customer_save') {
                        $fullName = trim((string) ($_POST['full_name'] ?? ''));
                        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
                        $phone = trim((string) ($_POST['phone'] ?? ''));
                        $address = trim((string) ($_POST['address'] ?? ''));
                        $emailTaken = false;
                        foreach ($_SESSION['users'] as $otherUser) {
                            if ((int) ($otherUser['id'] ?? 0) !== $customerId
                                && strtolower((string) ($otherUser['email'] ?? '')) === $email) {
                                $emailTaken = true;
                                break;
                            }
                        }
                        if ($fullName === '' || strlen($fullName) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL)
                            || strlen($phone) > 40 || strlen($address) > 255 || $emailTaken) {
                            break;
                        }
                        $user['full_name'] = $user['name'] = $fullName;
                        $user['email'] = $email;
                        $user['phone'] = $phone;
                        $user['address'] = $address;
                    } else {
                        if (!in_array($_POST['status'] ?? '', ['active', 'disabled'], true)) {
                            break;
                        }
                        $user['status'] = $_POST['status'];
                    }
                    $saved = true;
                    break;
                }
            }
            unset($user);
            $_SESSION['admin_flash_message'] = $saved ? 'Customer record updated.' : 'Customer details are invalid or the account could not be found.';
        } elseif ($action === 'maintenance_add' || $action === 'maintenance_update') {
            $maintenanceId = (int) ($_POST['maintenance_id'] ?? 0);
            $vehicleId = (int) ($_POST['vehicle_id'] ?? 0);
            $vehicle = app_find_vehicle($vehicleId);
            $scheduledDate = (string) ($_POST['scheduled_date'] ?? '');
            $parsedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $scheduledDate);
            $expectedCompletion = (string) ($_POST['expected_completion_date'] ?? '');
            $actualCompletion = (string) ($_POST['completed_date'] ?? '');
            $expectedParsed = $expectedCompletion === '' ? null : DateTimeImmutable::createFromFormat('!Y-m-d', $expectedCompletion);
            $actualParsed = $actualCompletion === '' ? null : DateTimeImmutable::createFromFormat('!Y-m-d', $actualCompletion);
            $maintenanceType = trim((string) ($_POST['maintenance_type'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $cost = filter_var($_POST['cost'] ?? 0, FILTER_VALIDATE_FLOAT);
            if (!$vehicle || $maintenanceType === '' || strlen($maintenanceType) > 160
                || strlen($description) > 2000 || $cost === false || $cost < 0
                || !$parsedDate || $parsedDate->format('Y-m-d') !== $scheduledDate
                || ($expectedCompletion !== '' && (!$expectedParsed || $expectedParsed->format('Y-m-d') !== $expectedCompletion))
                || ($actualCompletion !== '' && (!$actualParsed || $actualParsed->format('Y-m-d') !== $actualCompletion))) {
                $_SESSION['admin_flash_message'] = 'Choose a vehicle and enter a maintenance type, valid dates, and a non-negative cost.';
                app_redirect('admin_panel.php?section=maintenance-add');
            }
            if (($action === 'maintenance_add' && $maintenanceId !== 0)
                || ($action === 'maintenance_update' && $maintenanceId <= 0)) {
                $_SESSION['admin_flash_message'] = 'The maintenance action does not match the selected record.';
                app_redirect('admin_panel.php?section=maintenance-add');
            }
            $record = [
                'id' => $maintenanceId > 0 ? $maintenanceId : app_next_id('maintenance'),
                'vehicle_id' => $vehicleId,
                'vehicle_name' => $vehicle['name'] ?? 'Unknown vehicle',
                'maintenance_type' => $maintenanceType,
                'description' => $description,
                'scheduled_date' => $scheduledDate,
                'expected_completion_date' => $expectedCompletion,
                'completed_date' => $actualCompletion,
                'cost' => (float) $cost,
                'status' => in_array($_POST['maintenance_status'] ?? '', ['scheduled', 'in_progress', 'completed', 'cancelled'], true) ? $_POST['maintenance_status'] : 'scheduled',
                'is_demo' => false,
            ];
            $found = false;
            foreach ($_SESSION['maintenance'] as &$item) {
                if ((int) $item['id'] === $maintenanceId && $maintenanceId > 0) {
                    $item = $record;
                    $found = true;
                    break;
                }
            }
            unset($item);
            if ($record['status'] === 'in_progress') {
                foreach ($_SESSION['vehicles'] as &$maintenanceVehicle) {
                    if ((int) ($maintenanceVehicle['id'] ?? 0) === $vehicleId) {
                        $maintenanceVehicle['status'] = 'maintenance';
                        break;
                    }
                }
                unset($maintenanceVehicle);
            } elseif ($record['status'] === 'completed') {
                $hasActiveRental = false;
                foreach ($_SESSION['bookings'] as $booking) {
                    if ((int) ($booking['vehicle_id'] ?? 0) === $vehicleId
                        && ($booking['status'] ?? '') === 'active') {
                        $hasActiveRental = true;
                        break;
                    }
                }
                if (!$hasActiveRental) {
                    foreach ($_SESSION['vehicles'] as &$maintenanceVehicle) {
                        if ((int) ($maintenanceVehicle['id'] ?? 0) === $vehicleId
                            && ($maintenanceVehicle['status'] ?? '') === 'maintenance') {
                            $maintenanceVehicle['status'] = 'available';
                            break;
                        }
                    }
                    unset($maintenanceVehicle);
                }
            }
            if ($action === 'maintenance_add') {
                $_SESSION['maintenance'][] = $record;
                $_SESSION['admin_flash_message'] = 'Maintenance record added.';
            } elseif ($found) {
                $_SESSION['admin_flash_message'] = 'Maintenance record updated.';
            } else {
                $_SESSION['admin_flash_message'] = 'The maintenance record to update could not be found.';
            }
        } elseif ($action === 'maintenance_delete') {
            $maintenanceId = (int) ($_POST['maintenance_id'] ?? 0);
            $maintenanceExists = false;
            foreach ($_SESSION['maintenance'] as $item) {
                if ((int) $item['id'] === $maintenanceId) {
                    $maintenanceExists = true;
                    break;
                }
            }
            if ($maintenanceExists) {
                $_SESSION['maintenance'] = array_values(array_filter($_SESSION['maintenance'], static function ($item) use ($maintenanceId): bool {
                    return (int) $item['id'] !== $maintenanceId;
                }));
            }
            $_SESSION['admin_flash_message'] = $maintenanceExists
                ? 'Maintenance record deleted.'
                : 'Maintenance record could not be found.';
        }
        app_redirect('admin_panel.php?section=' . rawurlencode($adminReturnSection));
    } elseif (in_array($action, ['update_profile', 'change_password', 'submit_review', 'submit_browse_review', 'request_booking'], true)) {
        http_response_code(403);
        exit('Please sign in with the appropriate account before continuing.');
    }
}

$loginError = $_SESSION['login_error'] ?? '';
$signupError = $_SESSION['signup_error'] ?? '';
$username = $_SESSION['login_username'] ?? '';
$passwordError = $_SESSION['password_error'] ?? '';
$passwordMessage = $_SESSION['password_message'] ?? '';
$profile = $_SESSION['admin_user'];
if (!is_array($profile)) {
    $profile = [];
}
$profile = array_merge([
    'full_name' => 'Administrator',
    'email' => 'admin@autoride.test',
    'phone' => 'Not provided',
    'created_at' => 'N/A',
], $profile);

$pageTitle = $pageTitle ?? 'Autoride';
$pageDescription = $pageDescription ?? 'Your trusted partner for vehicle rentals.';
$policyPages = $policyPages ?? [
    'privacy_policy.php' => 'Privacy Policy',
    'terms_of_policy.php' => 'Terms of Service',
    'faqs.php' => 'FAQs',
    'terms_and_conditions.php' => 'Rental Terms & Conditions',
    'return_policy.php' => 'Return Policy',
];
$policySections = $policySections ?? [];
$currentPolicy = $currentPolicy ?? '';

$requestPage = basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''));
if ($requestPage === 'index.php') {
    $vehicles = array_values(array_filter($_SESSION['vehicles'], static function ($vehicle): bool {
        return ($vehicle['status'] ?? '') === 'available';
    }));
}

if ($requestPage === 'brows_vehicle.php') {
    $searchType = strtolower(trim((string) ($_GET['type'] ?? '')));
    $searchPassengers = filter_var($_GET['passengers'] ?? null, FILTER_VALIDATE_INT);
    $searchPickup = (string) ($_GET['pickup'] ?? '');
    $searchReturn = (string) ($_GET['return'] ?? '');
    $pickupDate = DateTimeImmutable::createFromFormat('!Y-m-d', $searchPickup);
    $returnDate = DateTimeImmutable::createFromFormat('!Y-m-d', $searchReturn);
    $hasSearchDates = $pickupDate && $returnDate
        && $pickupDate->format('Y-m-d') === $searchPickup
        && $returnDate->format('Y-m-d') === $searchReturn
        && $returnDate > $pickupDate;
    $vehicles = array_values(array_filter($_SESSION['vehicles'], static function ($vehicle) use ($searchType, $searchPassengers, $searchPickup, $searchReturn, $hasSearchDates): bool {
        if (($vehicle['status'] ?? '') !== 'available'
            || ($searchType !== '' && strtolower($vehicle['category']) !== $searchType)
            || (is_int($searchPassengers) && (int) $vehicle['seats'] < $searchPassengers)) {
            return false;
        }
        if ($hasSearchDates) {
            foreach ($_SESSION['bookings'] as $booking) {
                if ((int) $booking['vehicle_id'] === (int) $vehicle['id']
                    && in_array($booking['status'], ['requested', 'confirmed', 'active'], true)
                    && $searchPickup < $booking['return_date']
                    && $searchReturn > $booking['pickup_date']) {
                    return false;
                }
            }
        }
        return true;
    }));
    $feedbackMessage = $_SESSION['feedback_message'] ?? '';
    $feedbackError = $_SESSION['feedback_error'] ?? '';
    $feedbackVehicle = $_SESSION['feedback_vehicle'] ?? '';
    $feedbackRating = $_SESSION['feedback_rating'] ?? '5';
    $feedbackText = $_SESSION['feedback_text'] ?? '';
}

if ($requestPage === 'vehicle_details.php') {
    $vehicle = app_find_vehicle($_GET['id'] ?? 'toyota-vios');
    $vehicleId = (string) ($vehicle['slug'] ?? ($_GET['id'] ?? ''));
    if ($vehicle !== null) {
        $vehicle = array_merge($vehicle, [
            'type' => $vehicle['category'], 'summary' => $vehicle['description'],
            'features' => ['Air Conditioning', $vehicle['transmission'] . ' Transmission', 'USB Charging'],
            'tags' => [$vehicle['category'], $vehicle['fuel_type'], $vehicle['status']],
            'deposit' => 5000, 'fuel' => $vehicle['fuel_type'],
            'price' => $vehicle['daily_rate'], 'year' => (string) $vehicle['model_year'],
            'mileage' => number_format((int) $vehicle['mileage_km']) . ' km',
            'plate' => 'On request',
        ]);
    }
    $reviewMessage = $_SESSION['review_message'] ?? '';
    $reviewError = $_SESSION['review_error'] ?? '';
    $reviewRating = $_SESSION['review_rating'] ?? '5';
    $reviewText = $_SESSION['review_text'] ?? '';
    $vehicleReviews = array_values(array_filter($_SESSION['reviews'], static function ($review) use ($vehicle): bool {
        return $vehicle !== null && (int) $review['vehicle_id'] === (int) $vehicle['id']
            && ($review['status'] ?? 'approved') === 'approved';
    }));
    $pickup = (string) ($_GET['pickup'] ?? '');
    $returnDate = (string) ($_GET['return'] ?? '');
}

if ($requestPage === 'rentflow.php') {
    $selectedVehicle = app_find_vehicle($_GET['id'] ?? '');
    if (empty($_SESSION['customer_user']['id'])) {
        app_set_pending_rental_vehicle($selectedVehicle['slug'] ?? '');
        app_redirect('log_in.php');
    }
    $vehicle = $selectedVehicle === null
        ? null
        : [$selectedVehicle['slug'], $selectedVehicle['name'], $selectedVehicle['category'], $selectedVehicle['daily_rate']];
    $pickup = (string) ($_POST['pickup'] ?? $_GET['pickup'] ?? '');
    $returnDate = (string) ($_POST['return'] ?? $_GET['return'] ?? '');
    $rentalType = (string) ($_POST['rental_type'] ?? 'self_drive');
    $pickupTime = (string) ($_POST['pickup_time'] ?? '');
    $pickupLocation = (string) ($_POST['pickup_location'] ?? '');
    $bookingError = $_SESSION['booking_error'] ?? '';
    $bookingSuccess = $_SESSION['booking_success'] ?? '';
    unset($_SESSION['booking_error'], $_SESSION['booking_success']);
}

if ($requestPage === 'admin_panel.php' && !empty($_SESSION['admin_user']['id'])) {
    $vehicles = $_SESSION['vehicles'];
    $categories = array_map(static function ($category) use ($vehicles): array {
        $category['vehicle_count'] = count(array_filter($vehicles, static function ($vehicle) use ($category): bool {
            return (int) $vehicle['category_id'] === (int) $category['id'];
        }));
        return $category;
    }, $_SESSION['categories']);
    $requests = array_values(array_filter($_SESSION['bookings'], static function ($booking): bool {
        return $booking['status'] === 'requested';
    }));
    $allReviews = $_SESSION['reviews'];
    $allPayments = $_SESSION['payments'];
    $allBookings = $_SESSION['bookings'];
    $customers = array_map(static function ($user) use ($allBookings): array {
        unset($user['password_hash']);
        $user['rental_count'] = count(array_filter($allBookings, static function ($booking) use ($user): bool {
            return (int) $booking['customer_id'] === (int) $user['id'];
        }));
        return $user;
    }, $_SESSION['users']);
    $maintenanceRecords = $_SESSION['maintenance'];
    $releaseRecords = array_values(array_filter($_SESSION['bookings'], static function ($booking): bool {
        return in_array($booking['status'], ['confirmed', 'active', 'completed'], true);
    }));
    $rentalReport = [];
    foreach ($_SESSION['bookings'] as $booking) {
        $status = $booking['status'];
        if (!isset($rentalReport[$status])) {
            $rentalReport[$status] = ['status' => $status, 'total' => 0];
        }
        $rentalReport[$status]['total']++;
    }
    $rentalReport = array_values($rentalReport);
    $vehicleReport = [];
    foreach ($vehicles as $vehicle) {
        $category = $vehicle['category'];
        if (!isset($vehicleReport[$category])) {
            $vehicleReport[$category] = ['category' => $category, 'available' => 0, 'total' => 0];
        }
        $vehicleReport[$category]['total']++;
        if ($vehicle['status'] === 'available') {
            $vehicleReport[$category]['available']++;
        }
    }
    $vehicleReport = array_values($vehicleReport);
    $maintenanceReport = [];
    foreach ($maintenanceRecords as $record) {
        $status = $record['status'];
        if (!isset($maintenanceReport[$status])) {
            $maintenanceReport[$status] = ['status' => $status, 'total' => 0];
        }
        $maintenanceReport[$status]['total']++;
    }
    $maintenanceReport = array_values($maintenanceReport);
    $revenueReport = 0.0;
    foreach ($allPayments as $payment) {
        if (($payment['status'] ?? '') === 'paid' && empty($payment['is_demo'])) {
            $revenueReport += (float) ($payment['amount'] ?? 0);
        }
    }
    $sampleRevenue = 0.0;
    foreach ($allPayments as $payment) {
        if (($payment['status'] ?? '') === 'paid' && !empty($payment['is_demo'])) {
            $sampleRevenue += (float) ($payment['amount'] ?? 0);
        }
    }
    $paidAmount = array_sum(array_map(static function ($payment): float {
        return ($payment['status'] ?? '') === 'paid' && empty($payment['is_demo'])
            ? (float) ($payment['amount'] ?? 0)
            : 0.0;
    }, $allPayments));
    $outstandingAmount = array_sum(array_map(static function ($booking): float {
        return empty($booking['is_demo'])
            && in_array($booking['status'] ?? '', ['confirmed', 'active', 'completed'], true)
            ? (float) ($booking['balance'] ?? 0)
            : 0.0;
    }, $allBookings));
    $dashboardStats = [
        'vehicles' => count($vehicles),
        'active_rentals' => count(array_filter($_SESSION['bookings'], static function ($booking): bool { return $booking['status'] === 'active'; })),
        'pending_requests' => count($requests),
        'customers' => count($customers),
        'approved_rentals' => count(array_filter($allBookings, static function ($booking): bool { return $booking['status'] === 'confirmed'; })),
        'completed_rentals' => count(array_filter($allBookings, static function ($booking): bool { return $booking['status'] === 'completed'; })),
        'revenue' => $revenueReport,
        'paid_amount' => $paidAmount,
        'outstanding_amount' => $outstandingAmount,
        'sample_revenue' => $sampleRevenue,
    ];
    $popularVehicles = [];
    foreach ($allBookings as $booking) {
        $vehicleId = (int) ($booking['vehicle_id'] ?? 0);
        if (!isset($popularVehicles[$vehicleId])) {
            $popularVehicles[$vehicleId] = ['name' => (string) ($booking['vehicle_name'] ?? 'Vehicle'), 'rentals' => 0];
        }
        $popularVehicles[$vehicleId]['rentals']++;
    }
    usort($popularVehicles, static function ($left, $right): int {
        return $right['rentals'] <=> $left['rentals'];
    });
    $monthlyRevenue = [];
    foreach ($allPayments as $payment) {
        if (($payment['status'] ?? '') !== 'paid' || empty($payment['paid_at']) || !empty($payment['is_demo'])) {
            continue;
        }
        $month = date('Y-m', strtotime($payment['paid_at']));
        $monthlyRevenue[$month] = ($monthlyRevenue[$month] ?? 0) + (float) $payment['amount'];
    }
    krsort($monthlyRevenue);
    $editVehicle = null;
    foreach ($vehicles as $item) {
        if ((int) $item['id'] === (int) ($_GET['edit_vehicle'] ?? 0)) {
            $editVehicle = $item;
        }
    }
    $editCategory = null;
    foreach ($categories as $item) {
        if ((int) $item['id'] === (int) ($_GET['edit_category'] ?? 0)) {
            $editCategory = $item;
        }
    }
    $editMaintenance = null;
    foreach ($maintenanceRecords as $item) {
        if ((int) $item['id'] === (int) ($_GET['edit_maintenance'] ?? 0)) {
            $editMaintenance = $item;
        }
    }
}

if ($requestPage === 'user_profile.php') {
    $customer = $_SESSION['customer_user'];
    $profile = [
        'full_name' => $customer['name'] ?? 'Guest User',
        'email' => $customer['email'] ?? 'Not signed in',
        'phone' => $customer['phone'] ?? 'Not provided',
        'address' => $customer['address'] ?? '',
        'created_at' => $customer['created_at'] ?? '',
    ];
    $bookings = array_values(array_filter($_SESSION['bookings'], static function ($booking) use ($customer): bool {
        return !empty($customer['id']) && (int) $booking['customer_id'] === (int) $customer['id'];
    }));
    $reviews = array_values(array_filter($_SESSION['reviews'], static function ($review) use ($customer): bool {
        return !empty($customer['id']) && (int) $review['customer_id'] === (int) $customer['id'];
    }));
    $rentalCharges = array_values(array_filter($bookings, static function ($booking): bool {
        return in_array($booking['status'] ?? '', ['confirmed', 'active', 'completed'], true);
    }));
    $payments = [];
    foreach ($_SESSION['payments'] as $payment) {
        if ((int) ($payment['customer_id'] ?? 0) === (int) $customer['id']) {
            $payments[] = $payment;
        }
    }
    $outstandingBalances = array_values(array_filter($bookings, static function ($booking): bool {
        return (float) ($booking['balance'] ?? 0) > 0
            && in_array($booking['status'] ?? '', ['confirmed', 'active', 'completed'], true);
    }));
    $rentalCounts = ['active' => 0, 'upcoming' => 0, 'completed' => 0, 'cancelled' => 0];
    foreach ($bookings as $booking) {
        if (in_array($booking['status'], ['requested', 'confirmed'], true)) {
            $rentalCounts['upcoming']++;
        } elseif (in_array($booking['status'], ['active', 'completed'], true)) {
            $rentalCounts[$booking['status']]++;
        } elseif (in_array($booking['status'], ['cancelled', 'rejected'], true)) {
            $rentalCounts['cancelled']++;
        }
    }
    $profileMessage = $_SESSION['profile_message'] ?? '';
    $profileError = $_SESSION['profile_error'] ?? '';
    $reviewMessage = $_SESSION['review_message'] ?? '';
    $totalCharges = array_sum(array_map(static function ($booking): float { return (float) $booking['total_amount']; }, $rentalCharges));
    $paidTotal = array_sum(array_map(static function ($payment): float {
        return (float) ($payment['amount'] ?? 0);
    }, $payments));
    $outstandingTotal = array_sum(array_map(static function ($booking): float {
        return (float) $booking['balance'];
    }, $outstandingBalances));
    $refundedTotal = 0.0;
}
