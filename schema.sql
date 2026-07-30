CREATE DATABASE IF NOT EXISTS medibook CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE medibook;

CREATE TABLE IF NOT EXISTS doctor (
  doctor_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  avg_rating DECIMAL(3,2) DEFAULT 0,
  review_count INT DEFAULT 0,
  bio TEXT,
  room_location VARCHAR(100),
  languages VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS patient (
  patient_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  initials VARCHAR(10) NOT NULL,
  contact_info VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS appointment (
  appointment_id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT NOT NULL,
  doctor_id INT NOT NULL,
  appt_date DATE NOT NULL,
  appt_time TIME NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'Pending',
  priority VARCHAR(20) NOT NULL DEFAULT 'Normal',
  visit_type VARCHAR(50) NOT NULL DEFAULT 'General',
  queue_number INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES patient(patient_id) ON DELETE CASCADE,
  FOREIGN KEY (doctor_id) REFERENCES doctor(doctor_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS review (
  review_id INT AUTO_INCREMENT PRIMARY KEY,
  doctor_id INT NOT NULL,
  patient_id INT NOT NULL,
  comment TEXT NOT NULL,
  review_date DATE NOT NULL,
  rating TINYINT NOT NULL DEFAULT 5,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (doctor_id) REFERENCES doctor(doctor_id) ON DELETE CASCADE,
  FOREIGN KEY (patient_id) REFERENCES patient(patient_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS availability_slot (
  slot_id INT AUTO_INCREMENT PRIMARY KEY,
  doctor_id INT NOT NULL,
  slot_date DATE NOT NULL,
  slot_time TIME NOT NULL,
  is_booked TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (doctor_id) REFERENCES doctor(doctor_id) ON DELETE CASCADE
);

-- Seed sample data
INSERT INTO doctor (name, avg_rating, review_count, bio, room_location, languages)
VALUES ('Dr. Fotso', 4.8, 28, 'Board-certified family physician with 10 years experience.', 'Room 12B', 'English, French');

INSERT INTO patient (name, initials, contact_info)
VALUES
  ('Emma Johnson', 'EJ', 'emma.johnson@example.com'),
  ('Liam Carter', 'LC', 'liam.carter@example.com'),
  ('Ava Williams', 'AW', 'ava.williams@example.com'),
  ('Noah Brown', 'NB', 'noah.brown@example.com');

INSERT INTO appointment (patient_id, doctor_id, appt_date, appt_time, status, priority, visit_type, queue_number)
VALUES
  (1, 1, CURDATE(), '09:00:00', 'Confirmed', 'Normal', 'Follow-up', 1),
  (2, 1, CURDATE(), '09:30:00', 'In room', 'Urgent', 'Consultation', 2),
  (3, 1, CURDATE(), '10:00:00', 'Confirmed', 'Normal', 'New patient', 3),
  (4, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '11:00:00', 'Pending', 'Normal', 'Consultation', NULL),
  (2, 1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '14:30:00', 'Pending', 'Urgent', 'Follow-up', NULL);

INSERT INTO review (doctor_id, patient_id, comment, review_date, rating)
VALUES
  (1, 1, 'Dr. Fotso was very attentive and explained everything clearly.', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 5),
  (1, 2, 'Great experience and timely appointment.', DATE_SUB(CURDATE(), INTERVAL 7 DAY), 5),
  (1, 3, 'Friendly staff and excellent care.', DATE_SUB(CURDATE(), INTERVAL 12 DAY), 4);

INSERT INTO availability_slot (doctor_id, slot_date, slot_time, is_booked)
VALUES
  (1, CURDATE(), '13:00:00', 0),
  (1, CURDATE(), '13:30:00', 1),
  (1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', 0);
