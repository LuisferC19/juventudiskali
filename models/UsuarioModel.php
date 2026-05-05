<?php
/**
 * models/UsuarioModel.php
 * Modelo CRUD completo para la tabla `usuarios`.
 *
 * Columnas reales de `usuarios` (según iskali_base_datos.sql):
 *   id_usuario, id_rol, nombre, apellido, email, contrasena_hash,
 *   activo, intentos_fallidos, fecha_bloqueo_temporal, ultimo_acceso, created_at
 *
 * NOTA: La tabla NO tiene columna `updated_at`. Se eliminó de todas las queries.
 */
class UsuarioModel
{
    private PDO $db;

    public function __construct(PDO $conexion)
    {
        $this->db = $conexion;
    }

    // =========================================================
    //  READ — Consultas GET
    // =========================================================

    /**
     * Obtener todos los usuarios con su rol (para la tabla principal).
     */
    public function consultar(): array
    {
        $sql = "
            SELECT
                u.id_usuario,
                u.nombre,
                u.apellido,
                u.email,
                u.activo,
                u.intentos_fallidos,
                u.ultimo_acceso,
                u.created_at,
                r.nombre AS rol,
                r.id_rol
            FROM usuarios u
            LEFT JOIN roles r ON u.id_rol = r.id_rol
            ORDER BY u.id_usuario ASC
        ";
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Obtener un usuario por su ID (para edición y verificaciones).
     */
    public function consultarPorId(int $id)
    {
        $sql = "
            SELECT
                u.id_usuario,
                u.id_rol,
                u.nombre,
                u.apellido,
                u.email,
                u.activo,
                u.intentos_fallidos,
                u.ultimo_acceso,
                u.created_at,
                r.nombre AS rol
            FROM usuarios u
            LEFT JOIN roles r ON u.id_rol = r.id_rol
            WHERE u.id_usuario = ?
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Total de usuarios registrados.
     */
    public function obtenerTotal(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM usuarios");
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }

    /**
     * Total de usuarios activos.
     */
    public function obtenerTotalActivos(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM usuarios WHERE activo = 1");
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }

    /**
     * Verificar si un email ya existe (para validar duplicados).
     * @param string   $email
     * @param int|null $excluirId  ID a excluir (útil al editar el propio usuario)
     */
    public function emailExiste(string $email, ?int $excluirId = null): bool
    {
        if ($excluirId !== null) {
            $sql  = "SELECT COUNT(*) AS cnt FROM usuarios WHERE email = ? AND id_usuario != ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([strtolower(trim($email)), $excluirId]);
        } else {
            $sql  = "SELECT COUNT(*) AS cnt FROM usuarios WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([strtolower(trim($email))]);
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['cnt'] ?? 0) > 0;
    }

    // =========================================================
    //  CREATE — POST: insertar nuevo usuario
    // =========================================================

    /**
     * Insertar un nuevo usuario en la base de datos.
     * @param string $password  Contraseña en texto plano (se hashea internamente)
     */
    public function insertar(
        string $nombre,
        string $apellido,
        string $email,
        string $password,
        int    $id_rol,
        bool   $activo
    ): bool {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // La tabla usuarios NO tiene updated_at — solo created_at
        $sql = "
            INSERT INTO usuarios
                (id_rol, nombre, apellido, email, contrasena_hash, activo, intentos_fallidos, created_at)
            VALUES
                (?, ?, ?, ?, ?, ?, 0, NOW())
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $id_rol,
            trim($nombre),
            trim($apellido),
            strtolower(trim($email)),
            $hash,
            $activo ? 1 : 0,
        ]);
    }

    // =========================================================
    //  UPDATE — POST: actualizar usuario existente
    // =========================================================

    /**
     * Actualizar datos de un usuario.
     * Si $password es null o vacío, NO se cambia la contraseña.
     *
     * @param string|null $password  null = no cambiar contraseña
     */
    public function actualizar(
        int     $id,
        string  $nombre,
        string  $apellido,
        string  $email,
        int     $id_rol,
        bool    $activo,
        ?string $password = null
    ): bool {
        if (!empty($password)) {
            // Actualizar incluyendo nueva contraseña
            $sql = "
                UPDATE usuarios
                SET id_rol          = ?,
                    nombre          = ?,
                    apellido        = ?,
                    email           = ?,
                    contrasena_hash = ?,
                    activo          = ?
                WHERE id_usuario = ?
            ";
            $params = [
                $id_rol,
                trim($nombre),
                trim($apellido),
                strtolower(trim($email)),
                password_hash($password, PASSWORD_BCRYPT),
                $activo ? 1 : 0,
                $id,
            ];
        } else {
            // Actualizar sin cambiar contraseña
            $sql = "
                UPDATE usuarios
                SET id_rol   = ?,
                    nombre   = ?,
                    apellido = ?,
                    email    = ?,
                    activo   = ?
                WHERE id_usuario = ?
            ";
            $params = [
                $id_rol,
                trim($nombre),
                trim($apellido),
                strtolower(trim($email)),
                $activo ? 1 : 0,
                $id,
            ];
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Desbloquear un usuario (resetear intentos fallidos).
     */
    public function desbloquear(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE usuarios SET intentos_fallidos = 0, fecha_bloqueo_temporal = NULL WHERE id_usuario = ?"
        );
        return $stmt->execute([$id]);
    }

    // =========================================================
    //  DELETE — Eliminar usuario (con limpieza de dependencias)
    // =========================================================

    /**
     * Eliminar un usuario por ID eliminando primero sus registros dependientes.
     *
     * Tablas con FK NOT NULL que apuntan a usuarios (no se pueden nullificar):
     *  - historial_accesos          → DELETE seguro (log de accesos)
     *  - recuperacion_contrasena    → DELETE seguro (tokens expirados)
     *  - notificaciones             → DELETE seguro (mensajes del usuario)
     *  - voluntarios                → DELETE en cascada (con asignaciones)
     *  - asignaciones_voluntario    → DELETE antes de voluntarios
     *
     * Tablas con FK NOT NULL que contienen datos de negocio críticos:
     *  - campanas, donaciones, beneficiarios, entregas, etc.
     *  Si el usuario tiene registros en esas tablas, se hace baja lógica
     *  (activo = 0) en lugar de eliminación física para preservar la integridad.
     *
     * @param  int  $id
     * @return bool  true = eliminado físicamente | false = baja lógica aplicada
     */
    public function eliminar(int $id): bool
    {
        // ── Verificar si tiene registros de negocio críticos ──────────────
        $tieneDatos = $this->tieneDatosCriticos($id);

        if ($tieneDatos) {
            // No se puede borrar físicamente sin perder datos de negocio.
            // Se hace baja lógica: desactivar la cuenta.
            $stmt = $this->db->prepare(
                "UPDATE usuarios SET activo = 0 WHERE id_usuario = ?"
            );
            $stmt->execute([$id]);
            return false; // false = se aplicó baja lógica, no eliminación física
        }

        // ── Eliminar registros de soporte (seguros de borrar) ─────────────
        $this->db->prepare(
            "DELETE FROM asignaciones_voluntario WHERE id_voluntario IN
             (SELECT id_voluntario FROM voluntarios WHERE id_usuario = ?)"
        )->execute([$id]);

        $this->db->prepare(
            "DELETE FROM voluntarios WHERE id_usuario = ?"
        )->execute([$id]);

        $this->db->prepare(
            "DELETE FROM notificaciones WHERE id_usuario = ?"
        )->execute([$id]);

        $this->db->prepare(
            "DELETE FROM historial_accesos WHERE id_usuario = ?"
        )->execute([$id]);

        $this->db->prepare(
            "DELETE FROM recuperacion_contrasena WHERE id_usuario = ?"
        )->execute([$id]);

        // ── Eliminar el usuario ───────────────────────────────────────────
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
        $stmt->execute([$id]);
        return true; // true = eliminado físicamente
    }

    /**
     * Verifica si el usuario tiene registros críticos de negocio que impiden
     * su eliminación física (campanas, donaciones, beneficiarios, entregas, etc.)
     */
    private function tieneDatosCriticos(int $id): bool
    {
        $queries = [
            "SELECT COUNT(*) FROM campanas               WHERE id_usuario_creador     = ?",
            "SELECT COUNT(*) FROM donaciones             WHERE id_usuario_registrador = ?",
            "SELECT COUNT(*) FROM beneficiarios          WHERE id_usuario_registrador = ?",
            "SELECT COUNT(*) FROM entregas               WHERE id_usuario_responsable = ?",
            "SELECT COUNT(*) FROM avances_campana        WHERE id_usuario             = ?",
            "SELECT COUNT(*) FROM movimientos_inventario WHERE id_usuario             = ?",
            "SELECT COUNT(*) FROM reconocimientos        WHERE id_usuario_emisor      = ?",
        ];

        foreach ($queries as $sql) {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            if ((int)$stmt->fetchColumn() > 0) {
                return true;
            }
        }

        return false;
    }

    // =========================================================
    //  CATÁLOGOS — Datos auxiliares
    // =========================================================

    /**
     * Obtener todos los roles disponibles (para el <select>).
     */
    public function obtenerRoles(): array
    {
        $stmt = $this->db->query("SELECT id_rol, nombre FROM roles ORDER BY nombre ASC");
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
}