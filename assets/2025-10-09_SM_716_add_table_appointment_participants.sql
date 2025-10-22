CREATE TABLE appointment_participants (
    id INT(11) NOT NULL AUTO_INCREMENT,
    appointment_id INT(11) NOT NULL,
    guest INT(10) UNSIGNED NOT NULL,
    type ENUM('class', 'individual') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
);