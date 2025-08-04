-- SQL script to create the necessary tables for the Golf Session Manager plugin

CREATE TABLE wp_golf_session_credits (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    credit_balance INT DEFAULT 0,
    subscription_plan VARCHAR(50),
    cycle_start DATETIME,
    cycle_end DATETIME,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    booking_time INT DEFAULT 0
);