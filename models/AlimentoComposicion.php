<?php
// models/AlimentoComposicion.php
// Modelo para el catálogo de composición química y nutricional por 100g de alimento.

require_once __DIR__ . '/../config/Conexion.php';

class AlimentoComposicion {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    /**
     * Listar todos los alimentos ordenados alfabéticamente
     */
    public function listarTodos() {
        $stmt = $this->pdo->query("SELECT * FROM alimentos_composicion ORDER BY nombre ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar alimentos por coincidencia de nombre o categoría (API reactiva)
     */
    public function buscar($query, $limite = 20) {
        $query = trim($query);
        if (empty($query)) {
            $stmt = $this->pdo->prepare("SELECT * FROM alimentos_composicion ORDER BY nombre ASC LIMIT :limite");
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $stmt = $this->pdo->prepare("SELECT * FROM alimentos_composicion 
                                      WHERE nombre LIKE :term1 OR categoria LIKE :term2 
                                      ORDER BY nombre ASC 
                                      LIMIT :limite");
        $param = '%' . $query . '%';
        $stmt->bindValue(':term1', $param, PDO::PARAM_STR);
        $stmt->bindValue(':term2', $param, PDO::PARAM_STR);
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener un alimento por su ID
     */
    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM alimentos_composicion WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => (int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Listar categorías únicas
     */
    public function listarCategorias() {
        $stmt = $this->pdo->query("SELECT DISTINCT categoria FROM alimentos_composicion ORDER BY categoria ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
