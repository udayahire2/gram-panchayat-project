CREATE TABLE approved_marriage_certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    certificate_number INT(11) NOT NULL,
    husband_name VARCHAR(255) NOT NULL,
    husband_photo VARCHAR(255) NOT NULL,
    husband_address text NOT NULL,
    wife_name VARCHAR(255) NOT NULL,
    wife_photo VARCHAR(255) NOT NULL,3
    wife_address text NOT NULL,
    marriage_date DATE NOT NULL,
    registration_date DATE NOT NULL,
    registration_number VARCHAR(3) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);