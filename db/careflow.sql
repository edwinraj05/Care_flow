-- ================================
-- CAREFLOW HOSPITAL MANAGEMENT DB
-- ================================

CREATE DATABASE IF NOT EXISTS careflow;
USE careflow;

-- ================================
-- ADMIN TABLE
-- ================================

CREATE TABLE admin (
    admin_id VARCHAR(10) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================
-- STAFF TABLE
-- ================================

CREATE TABLE staff (
    staff_id VARCHAR(10) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role ENUM('doctor','reception','pharmacist') NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(100),
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================
-- PATIENT TABLE
-- ================================

CREATE TABLE patients (
    patient_id VARCHAR(10) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(15),
    password VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================
-- TOKEN QUEUE TABLE
-- ================================

CREATE TABLE tokens (
    token_id VARCHAR(10) PRIMARY KEY,
    patient_id VARCHAR(10) NOT NULL,
    status ENUM('Waiting','NowServing','Completed') DEFAULT 'Waiting',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_token_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(patient_id)
    ON DELETE CASCADE
);

-- ================================
-- PRESCRIPTIONS TABLE
-- ================================

CREATE TABLE prescriptions (
    prescription_id INT AUTO_INCREMENT PRIMARY KEY,
    token_id VARCHAR(10),
    patient_id VARCHAR(10),
    doctor_id VARCHAR(10),
    diagnosis TEXT,
    medicines TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_pres_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(patient_id),

    CONSTRAINT fk_pres_token
    FOREIGN KEY (token_id)
    REFERENCES tokens(token_id)
);

-- ================================
-- MEDICAL HISTORY TABLE
-- ================================

CREATE TABLE medical_history (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id VARCHAR(10),
    doctor_id VARCHAR(10),
    diagnosis TEXT,
    medicines TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_history_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(patient_id)
);

-- ================================
-- PHARMACY ORDERS TABLE
-- ================================

CREATE TABLE pharmacy_orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    token_id VARCHAR(10),
    patient_id VARCHAR(10),
    medicines TEXT,
    status ENUM('Pending','Ready','Collected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_pharmacy_patient
    FOREIGN KEY (patient_id)
    REFERENCES patients(patient_id)
);

-- ================================
-- LOGIN LOGS TABLE
-- ================================

CREATE TABLE login_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(20),
    role VARCHAR(20),
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================
-- INSERT DEFAULT ADMIN ACCOUNT
-- ================================

INSERT INTO admin 
(admin_id, name, email, password, is_active)
VALUES 
('AID-0001', 'System Admin', 'admin@careflow.com', '1234', 1);

-- ================================
-- INSERT DEMO STAFF ACCOUNTS
-- ================================

INSERT INTO staff 
(staff_id, name, role, email, password, department, is_active)
VALUES

('RID-0001', 'Reception User', 'reception', 'reception@careflow.com', '1234', 'Front Desk', 1),

('DID-0001', 'Doctor User', 'doctor', 'doctor@careflow.com', '1234', 'General Medicine', 1),

('PHID-0001', 'Pharmacy User', 'pharmacist', 'pharmacy@careflow.com', '1234', 'Main Pharmacy', 1);

-- ================================
-- PERFORMANCE INDEXES
-- ================================

CREATE INDEX idx_patient_email ON patients(email);
CREATE INDEX idx_token_status ON tokens(status);
CREATE INDEX idx_prescription_patient ON prescriptions(patient_id);
CREATE INDEX idx_pharmacy_status ON pharmacy_orders(status);

-- ================================
-- CAREFLOW DATABASE READY
-- ================================
