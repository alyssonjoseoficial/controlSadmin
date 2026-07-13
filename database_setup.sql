CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS saas_systems (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    url VARCHAR(255) NOT NULL,
    api_key VARCHAR(255) NOT NULL,
    secret_key VARCHAR(255) NOT NULL,
    logo_url VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sso_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    system_id INT NOT NULL,
    token_jti VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (system_id) REFERENCES saas_systems(id)
);

-- Inserindo dados iniciais (Mock para teste)
-- Senha do admin: "password"
INSERT IGNORE INTO users (name, email, password_hash) VALUES 
('Super Admin', 'admin@controlsadmin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT IGNORE INTO saas_systems (name, slug, url, api_key, secret_key, logo_url) VALUES 
('Vepix', 'vepix', 'https://vepix.com/admin/sso', 'vepix_key_123', 'vepix_secret_super_safe_123!@#', 'https://via.placeholder.com/150/09f/fff.png?text=Vepix'),
('CacheZum', 'cachezum', 'https://cachezum.com/admin/sso', 'cachezum_key_123', 'cachezum_secret_super_safe_123!@#', 'https://via.placeholder.com/150/f90/fff.png?text=CacheZum'),
('Sistema SaaS 3', 'sys3', 'https://sys3.com/admin/sso', 'sys3_key_123', 'sys3_secret_super_safe_123!@#', 'https://via.placeholder.com/150/0c0/fff.png?text=SaaS+3'),
('Sistema SaaS 4', 'sys4', 'https://sys4.com/admin/sso', 'sys4_key_123', 'sys4_secret_super_safe_123!@#', 'https://via.placeholder.com/150/e0e/fff.png?text=SaaS+4'),
('Sistema SaaS 5', 'sys5', 'https://sys5.com/admin/sso', 'sys5_key_123', 'sys5_secret_super_safe_123!@#', 'https://via.placeholder.com/150/f00/fff.png?text=SaaS+5'),
('Sistema SaaS 6', 'sys6', 'https://sys6.com/admin/sso', 'sys6_key_123', 'sys6_secret_super_safe_123!@#', 'https://via.placeholder.com/150/00f/fff.png?text=SaaS+6'),
('Sistema SaaS 7', 'sys7', 'https://sys7.com/admin/sso', 'sys7_key_123', 'sys7_secret_super_safe_123!@#', 'https://via.placeholder.com/150/333/fff.png?text=SaaS+7');
