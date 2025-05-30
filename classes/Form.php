<?php
class Form {
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            // Required field
            if (strpos($rule, 'required') !== false && empty($value)) {
                $errors[$field] = ucfirst($field) . ' is required';
                continue;
            }
            
            if (empty($value)) {
                continue; // Skip other validations if field is empty and not required
            }
            
            // Email validation
            if (strpos($rule, 'email') !== false && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = 'Invalid email format';
            }
            
            // Min length
            if (preg_match('/min:(\d+)/', $rule, $matches)) {
                $min = $matches[1];
                if (strlen($value) < $min) {
                    $errors[$field] = ucfirst($field) . " must be at least {$min} characters";
                }
            }
            
            // Max length
            if (preg_match('/max:(\d+)/', $rule, $matches)) {
                $max = $matches[1];
                if (strlen($value) > $max) {
                    $errors[$field] = ucfirst($field) . " must not exceed {$max} characters";
                }
            }
            
            // Numeric
            if (strpos($rule, 'numeric') !== false && !is_numeric($value)) {
                $errors[$field] = ucfirst($field) . ' must be a number';
            }
            
            // Match field
            if (preg_match('/match:(\w+)/', $rule, $matches)) {
                $matchField = $matches[1];
                if ($value !== ($data[$matchField] ?? null)) {
                    $errors[$field] = ucfirst($field) . " must match {$matchField}";
                }
            }
            
            // Unique in database
            if (preg_match('/unique:(\w+),(\w+)(?:,(\d+))?/', $rule, $matches)) {
                $table = $matches[1];
                $column = $matches[2];
                $exceptId = $matches[3] ?? null;
                
                $db = Database::getInstance();
                $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?";
                $params = [$value];
                $types = 's';
                
                if ($exceptId) {
                    $sql .= " AND id != ?";
                    $params[] = $exceptId;
                    $types .= 'i';
                }
                
                $stmt = $db->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                
                if ($row['count'] > 0) {
                    $errors[$field] = ucfirst($field) . ' is already taken';
                }
            }
        }
        
        return $errors;
    }

    public static function sanitize($data) {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }

    public static function old($key, $default = '') {
        return $_SESSION['old'][$key] ?? $default;
    }

    public static function setOld($data) {
        $_SESSION['old'] = $data;
    }

    public static function clearOld() {
        unset($_SESSION['old']);
    }
} 