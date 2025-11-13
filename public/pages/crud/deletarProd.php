<?php
require_once __DIR__ . '/../../../includes/database.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/header.php';

if (!isAdmin()) {
    header('Location: /index.php');
    exit;
}

$pdo = Database::getInstance()->getConnection();

// Carrega todos os produtos
$stmt = $pdo->query("SELECT id, nome, preco, imagem FROM menu_items ORDER BY nome ASC");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$produtoSelecionado = null;
$idSelecionado = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['buscar'])) {
        // Prioriza explicitamente o select; se vazio usa o campo manual
        $idSelecionado = intval($_POST['id_select'] ?? 0);
        if ($idSelecionado <= 0) {
            $idSelecionado = intval($_POST['id_manual'] ?? 0);
        }

        if ($idSelecionado > 0) {
            $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
            $stmt->execute([$idSelecionado]);
            $produtoSelecionado = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    if (isset($_POST['confirmar_exclusao'])) {
        // Para remoção, suporte ao hidden (id_selected) criado quando o preview é exibido,
        // e como fallback os campos visíveis (manual/select).
        $idToDelete = intval($_POST['id_selected'] ?? 0); 
        if ($idToDelete <= 0) {
            $idToDelete = intval($_POST['id_manual'] ?? 0);
        }
        if ($idToDelete <= 0) {
            $idToDelete = intval($_POST['id_select'] ?? 0);
        }

        if ($idToDelete > 0) {
            $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
            if ($stmt->execute([$idToDelete])) {
                header('Location: /pages/crud/listarProd.php?success=Produto excluído com sucesso!');
            } else {
                header('Location: /pages/crud/listarProd.php?error=Erro ao excluir o produto.');
            }
            exit;
        } else {
            header('Location: /pages/crud/listarProd.php?error=ID inválido para exclusão.');
            exit;
        }
    }
}

elseif (isset($_GET['id']) && intval($_GET['id']) > 0) {
    $idSelecionado = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->execute([$idSelecionado]);
    $produtoSelecionado = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<link rel="stylesheet" href="/assets/css/crud.css">

<div class="dashboard-layout">
    <?php include __DIR__ . '/../../../includes/painel_admin.php'; ?>

    <div class="dashboard-content">
        <div class="titulo-crud">
            <h2>Remover Produto</h2>
        </div>

        <?php include __DIR__ . '/../../../includes/messages.php'; ?>

        <div class="criar-produto-container">
            <form method="post" class="form-criar-produto"><br>

                <label for="id_select">Selecione um produto:</label>
                <select name="id_select" id="id_select" class="input-criar-produto">
                    <option value="">Selecione...</option>
                    <?php foreach ($produtos as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($idSelecionado == $p['id']) ? 'selected' : '' ?>>
                            #<?= $p['id'] ?> — <?= htmlspecialchars($p['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="id_manual">Ou digite o ID:</label>
                <input type="number" name="id_manual" id="id_manual"
                       placeholder="Ex: 12"
                       value="<?= htmlspecialchars($idSelecionado ?: '') ?>"
                       class="input-criar-produto">

                <button type="submit" name="buscar" class="btn-criar-produto btn-buscar-produto">
                    🔍 Buscar Produto
                </button>

                <?php if ($produtoSelecionado): ?>
                    <div class="preview-bloco">
                        <div class="preview-container">
                            <p><strong>Produto selecionado:</strong></p>
                            <p>ID: #<?= $produtoSelecionado['id'] ?></p>
                            <p><strong><?= htmlspecialchars($produtoSelecionado['nome']) ?></strong></p>
                            <p>Valor: R$ <?= number_format($produtoSelecionado['preco'], 2, ',', '.') ?></p>
                            <?php if (!empty($produtoSelecionado['imagem'])): ?>
                                <img src="<?= htmlspecialchars($produtoSelecionado['imagem']) ?>"
                                     alt="prévia"
                                     class="preview-imagem"
                                     onerror="this.style.display='none'">
                            <?php endif; ?>
                        </div>

                        <input type="hidden" name="id_selected" value="<?= $produtoSelecionado['id'] ?>">

                        <button type="submit" name="confirmar_exclusao"
                                class="btn-criar-produto btn-remover-produto">
                              Remover Produto
                        </button>
                    </div>
                <?php endif; ?>
            </form>

            <script>
            document.addEventListener("DOMContentLoaded", () => {
                const idManual = document.getElementById("id_manual");
                const idSelect = document.getElementById("id_select");

                if (idManual && idSelect) {
                    idManual.addEventListener("input", () => {
                        if (idManual.value.trim() !== "") idSelect.value = "";
                    });
                    idSelect.addEventListener("change", () => {
                        if (idSelect.value.trim() !== "") idManual.value = "";
                    });
                }
            });
            </script>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
