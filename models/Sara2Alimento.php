<?php
// models/Sara2Alimento.php
// Modelo para la base de datos nutricional oficial argentina SARA 2 (Compilación ENNyS 2)

require_once __DIR__ . '/../config/Conexion.php';

class Sara2Alimento {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    /**
     * Listar todos los 26 grupos oficiales SARA 2
     *
     * @return array
     */
    public function listarGrupos() {
        $stmt = $this->pdo->query("SELECT DISTINCT grupo_id, grupo_nombre FROM sara2_alimentos ORDER BY grupo_id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar alimentos por coincidencia de texto y/o filtro de grupo SARA 2
     *
     * @param string $query
     * @param int|null $grupoId
     * @param int $limite
     * @return array
     */
    public function buscar($query = '', $grupoId = null, $limite = 40) {
        $query = trim($query);
        $grupoId = !empty($grupoId) ? (int)$grupoId : null;

        $conditions = [];
        $params = [];

        if (!empty($query)) {
            $conditions[] = "(nombre LIKE :term1 OR grupo_nombre LIKE :term2)";
            $params[':term1'] = '%' . $query . '%';
            $params[':term2'] = '%' . $query . '%';
        }

        if (!empty($grupoId) && $grupoId > 0) {
            $conditions[] = "grupo_id = :grupo_id";
            $params[':grupo_id'] = $grupoId;
        }

        $sql = "SELECT * FROM sara2_alimentos";
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " ORDER BY grupo_id ASC, nombre ASC LIMIT :limite";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener un alimento por su ID con todos sus 39 componentes nutricionales
     *
     * @param int $id
     * @return array|null
     */
    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM sara2_alimentos WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => (int)$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    /**
     * Listar todos los alimentos ordenados por grupo y nombre
     *
     * @param int|null $grupoId
     * @return array
     */
    public function listarTodos($grupoId = null) {
        if (!empty($grupoId) && (int)$grupoId > 0) {
            $stmt = $this->pdo->prepare("SELECT * FROM sara2_alimentos WHERE grupo_id = :grupo_id ORDER BY nombre ASC");
            $stmt->execute([':grupo_id' => (int)$grupoId]);
        } else {
            $stmt = $this->pdo->query("SELECT * FROM sara2_alimentos ORDER BY grupo_id ASC, nombre ASC");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
