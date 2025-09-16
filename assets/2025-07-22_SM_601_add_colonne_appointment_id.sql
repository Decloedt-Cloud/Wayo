ALTER TABLE sessions_meetings
ADD appointment_id INT NOT NULL,
ADD CONSTRAINT fk_appointment_id
FOREIGN KEY (appointment_id)
REFERENCES appointments(id)
ON DELETE CASCADE
ON UPDATE CASCADE;