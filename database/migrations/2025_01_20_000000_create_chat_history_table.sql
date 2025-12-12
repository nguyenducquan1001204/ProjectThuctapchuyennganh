-- Migration: Tạo bảng lưu lịch sử chat của từng tài khoản
-- Date: 2025-01-20
-- Database: MySQL/MariaDB

CREATE TABLE IF NOT EXISTS chat_history (
    historyid INT AUTO_INCREMENT PRIMARY KEY,
    userid INT NOT NULL,
    conversationid VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    createdat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_chat_history_userid FOREIGN KEY (userid) REFERENCES systemuser(userid) ON DELETE CASCADE,
    CONSTRAINT chk_chat_history_role CHECK (role IN ('user', 'assistant'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo index để tăng tốc độ truy vấn
CREATE INDEX idx_chat_history_userid ON chat_history(userid);
CREATE INDEX idx_chat_history_conversationid ON chat_history(conversationid);
CREATE INDEX idx_chat_history_createdat ON chat_history(createdat);

-- Tạo bảng lưu thông tin cuộc trò chuyện
CREATE TABLE IF NOT EXISTS chat_conversation (
    conversationid VARCHAR(255) PRIMARY KEY,
    userid INT NOT NULL,
    title VARCHAR(255) DEFAULT NULL,
    createdat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updatedat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_chat_conversation_userid FOREIGN KEY (userid) REFERENCES systemuser(userid) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo index cho bảng conversation
CREATE INDEX idx_chat_conversation_userid ON chat_conversation(userid);
CREATE INDEX idx_chat_conversation_updatedat ON chat_conversation(updatedat);

