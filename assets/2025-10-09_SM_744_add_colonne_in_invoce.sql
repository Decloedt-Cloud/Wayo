CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  school_id INT NULL,
  class_id INT NULL,
  invoice_id INT NULL,
  amount DECIMAL(10,2) NOT NULL,
  currency VARCHAR(10) DEFAULT 'EUR',
  payment_type ENUM('school_join', 'class_enrol', 'subscription', 'other') NOT NULL,
  payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
  payment_method VARCHAR(50), -- Stripe, PayPal, etc.
  transaction_ref VARCHAR(100) NULL, -- ID Stripe/PayPal, etc.
  paid_at DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  
);

ALTER TABLE invoices 
ADD COLUMN payment_type ENUM('class_enrol', 'community_join') DEFAULT 'class_enrol',
ADD COLUMN payment_id INT NULL;