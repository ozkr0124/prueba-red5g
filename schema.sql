CREATE SCHEMA prueba_red5g;

CREATE TABLE
    prueba_red5g.tbl_roles (
        id_roles INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        role VARCHAR(50) NOT NULL,
        state TINYINT DEFAULT 1 NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
    prueba_red5g.tbl_state_payments (
        id_state_payments INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        state_payment VARCHAR(20) NOT NULL,
        state TINYINT DEFAULT 1 NOT NULL
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
    prueba_red5g.tbl_users (
        id_users INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        document_id BIGINT NOT NULL,
        email VARCHAR(100) NOT NULL,
        user_name VARCHAR(50) NOT NULL,
        password VARCHAR(255) NOT NULL,
        state TINYINT DEFAULT 1 NOT NULL,
        role_id INT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT unq_tbl_users_document_id UNIQUE (document_id),
        CONSTRAINT unq_tbl_users_email UNIQUE (email),
        CONSTRAINT fk_tbl_users_tbl_roles FOREIGN KEY (role_id) REFERENCES prueba_red5g.tbl_roles(id_roles) ON DELETE NO ACTION ON UPDATE CASCADE
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE INDEX
    fk_tbl_users_tbl_roles ON prueba_red5g.tbl_users (role_id);

CREATE TABLE
    prueba_red5g.tbl_temp_upload_confirmation (
        id_temp_upload_confirmation INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        customer_document BIGINT NOT NULL,
        customer_name VARCHAR(100) NOT NULL,
        customer_email VARCHAR(100) NOT NULL,
        amount DECIMAL(10, 0) NOT NULL,
        payment_date DATE NOT NULL,
        payment_id VARCHAR(20) NOT NULL,
        ticket_upload VARCHAR(10) NOT NULL,
        user_id INT NOT NULL,
        CONSTRAINT fk_tbl_temp_upload_confirmation_users FOREIGN KEY (user_id) REFERENCES prueba_red5g.tbl_users(id_users) ON DELETE NO ACTION ON UPDATE NO ACTION
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE INDEX
    fk_tbl_temp_upload_confirmation_users ON prueba_red5g.tbl_temp_upload_confirmation (user_id);

CREATE TABLE
    prueba_red5g.tbl_temp_upload_pending (
        id_temp_upload_pending INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        customer_document BIGINT NOT NULL,
        customer_name VARCHAR(100) NOT NULL,
        customer_email VARCHAR(100) NOT NULL,
        amount DECIMAL(10, 0) NOT NULL,
        payment_date DATE NOT NULL,
        due_date DATE NOT NULL,
        ticket_upload VARCHAR(10) NOT NULL,
        user_id INT NOT NULL,
        CONSTRAINT fk_tbl_temp_upload_pending_users FOREIGN KEY (user_id) REFERENCES prueba_red5g.tbl_users(id_users) ON DELETE NO ACTION ON UPDATE CASCADE
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE INDEX
    fk_tbl_temp_upload_pending_users ON prueba_red5g.tbl_temp_upload_pending (user_id);

CREATE TABLE
    prueba_red5g.tbl_upload_payments (
        id_upload_payments INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        customer_document BIGINT NOT NULL,
        customer_name VARCHAR(100) NOT NULL,
        customer_email VARCHAR(100) NOT NULL,
        amount DECIMAL(10, 0) NOT NULL,
        payment_date DATE NOT NULL,
        due_date DATE NOT NULL,
        payment_id VARCHAR(20) NOT NULL,
        state_payment_id INT DEFAULT 1 NOT NULL,
        user_id INT NOT NULL,
        user_id_approved INT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
        CONSTRAINT unq_tbl_upload_payments_payment_id UNIQUE (payment_id),
        CONSTRAINT fk_tbl_upload_payments_state_payments FOREIGN KEY (state_payment_id) REFERENCES prueba_red5g.tbl_state_payments(id_state_payments) ON DELETE NO ACTION ON UPDATE CASCADE,
        CONSTRAINT fk_tbl_upload_payments_users FOREIGN KEY (user_id) REFERENCES prueba_red5g.tbl_users(id_users) ON DELETE NO ACTION ON UPDATE CASCADE,
        CONSTRAINT fk_tbl_upload_payments_users_approved FOREIGN KEY (user_id_approved) REFERENCES prueba_red5g.tbl_users(id_users) ON DELETE NO ACTION ON UPDATE NO ACTION
    ) ENGINE = InnoDB AUTO_INCREMENT = 1 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE INDEX
    fk_tbl_upload_payments_state_payments ON prueba_red5g.tbl_upload_payments (state_payment_id);

CREATE INDEX
    fk_tbl_upload_payments_users ON prueba_red5g.tbl_upload_payments (user_id);

CREATE INDEX
    fk_tbl_upload_payments_users_approved ON prueba_red5g.tbl_upload_payments (user_id_approved);

INSERT INTO prueba_red5g.tbl_roles( id_roles, role, state, created_at, updated_at ) VALUES ( 1, 'ADMIN', 1, '2024-01-05 08:38:02', '2024-01-05 08:38:02');
INSERT INTO prueba_red5g.tbl_roles( id_roles, role, state, created_at, updated_at ) VALUES ( 2, 'REGULAR', 1, '2024-01-05 08:38:02', '2024-01-05 08:38:02');
INSERT INTO prueba_red5g.tbl_state_payments( id_state_payments, state_payment, state ) VALUES ( 1, 'PENDIENTE', 1);
INSERT INTO prueba_red5g.tbl_state_payments( id_state_payments, state_payment, state ) VALUES ( 2, 'CONFIRMADO', 1);
INSERT INTO prueba_red5g.tbl_users( id_users, full_name, document_id, email, user_name, password, state, role_id, created_at, updated_at ) VALUES ( 1, 'ADMIN PRINCIPAL', 1122334455, 'correo@correo.com', 'adminppal', '$2y$12$xye0qHrEkeKIaYHV/lzbu.oySBZSDrIiBVaBkUEExjrllToPTqXl6', 1, 1, '2024-01-05 08:38:56', '2024-01-05 09:11:06');
INSERT INTO prueba_red5g.tbl_users( id_users, full_name, document_id, email, user_name, password, state, role_id, created_at, updated_at ) VALUES ( 2, 'USUARIO REGULAR', 1122334466, 'usuario@correo.com', 'usuarioregular', '$2y$12$Ajea.hhK8yJkByRo72uPjO60zxfxau7bqUqstEYFX2NbcNUWNDYWi', 1, 2, '2024-01-05 10:20:48', '2024-01-05 10:27:29');
INSERT INTO prueba_red5g.tbl_users( id_users, full_name, document_id, email, user_name, password, state, role_id, created_at, updated_at ) VALUES ( 3, 'Usuario 2', 9988776655, 'usuario2@correo.com', 'user2', '$2y$12$ht.V14F0uASCKUht62oGpet64i7M0V2D1YkfuV2IpyYl0OQgmnfxu', 1, 2, '2024-01-08 05:50:19', '2024-01-08 05:50:19');