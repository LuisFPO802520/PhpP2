    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    function getCart() {
        return $_SESSION['cart'] ?? [];
    }

    function addToCart($id, $nome, $preco, $imagem) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantidade']++;
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $id,
                'nome' => $nome,
                'preco' => $preco,
                'imagem' => $imagem,
                'quantidade' => 1
            ];
        }
    }

    function removeFromCart($id) {
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
    }

    function increaseQuantity($id) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantidade']++;
        }
    }

    function decreaseQuantity($id) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantidade']--;
            if ($_SESSION['cart'][$id]['quantidade'] <= 0) {
                unset($_SESSION['cart'][$id]);
            }
        }
    }

    function clearCart() {
        $_SESSION['cart'] = [];
    }

    function getCartTotal() {
        $total = 0;
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['preco'] * $item['quantidade'];
            }
        }
        return $total;
    }
    ?>
