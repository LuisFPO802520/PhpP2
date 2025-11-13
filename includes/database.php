<?php
require_once __DIR__ . '/../config/config.php';

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dataDir = dirname(DB_PATH);
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0755, true);
        }

        $this->pdo = new PDO(DB_DSN, null, null, DB_OPTIONS);
        $this->init();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    private function init() {
        $sqlUsers = "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            senha TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'user' -- 'user' ou 'admin'
        );";
        $this->pdo->exec($sqlUsers);

        $sqlMenu = "CREATE TABLE IF NOT EXISTS menu_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT NOT NULL,
            descricao TEXT,
            preco REAL NOT NULL,
            quantidade INTEGER DEFAULT 0,
            disponibilidade INTEGER NOT NULL DEFAULT 1,
            imagem TEXT, 
            criado_em TEXT DEFAULT CURRENT_TIMESTAMP
        );";
        $this->pdo->exec($sqlMenu);

        $this->checkAndAddImagemColumn();

        $this->createDefaultAdmin();
    }

    private function checkAndAddImagemColumn() {
        $stmt = $this->pdo->query("PRAGMA table_info(menu_items)");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $colNames = array_column($columns, 'name');

        if (!in_array('imagem', $colNames)) {
            $this->pdo->exec("ALTER TABLE menu_items ADD COLUMN imagem TEXT");
        }

        if (!in_array('quantidade', $colNames)) {
            $this->pdo->exec("ALTER TABLE menu_items ADD COLUMN quantidade INTEGER DEFAULT 0");
        }
    }

    private function createDefaultAdmin() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
        $stmt->execute();
        $exists = $stmt->fetchColumn();

        if ($exists == 0) {
            $hash = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO users (nome, email, senha, role) VALUES (?, ?, ?, ?)");
            $stmt->execute(['Administrador', 'admin@bigbars.com', $hash, 'admin']);
        }
    }
}
