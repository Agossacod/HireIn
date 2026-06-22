<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use PDOException;
use RuntimeException;

final class UserRepository
{
    public function __construct(private PDO $db)
    {
    }

    /**
     * @param array<string, string> $data
     */
    public function createStudent(array $data): int
    {
        $this->db->beginTransaction();

        try {
            $userId = $this->createUser(
                role: 'etudiant',
                fullname: $data['fullname'],
                email: $data['email'],
                password: $data['password']
            );
            // Build insert dynamically based on actual columns present in the student_profiles table.
            $colStmt = $this->db->prepare("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'student_profiles'");
            $colStmt->execute();
            $existing = array_column($colStmt->fetchAll(PDO::FETCH_ASSOC), 'COLUMN_NAME');

            $cols = ['user_id'];
            $placeholders = [':user_id'];
            $bindings = [':user_id' => $userId];

            $optional = [
                'university' => $data['university'] ?? null,
                'level' => $data['level'] ?? null,
                'skills' => $data['skills'] ?? null,
                'city' => $data['city'] ?? null,
                'search_sector' => $data['search_sector'] ?? null,
                'phone' => $data['phone'] ?? null,
                'profile_photo' => $data['profile_photo'] ?? null,
                'cv' => $data['cv'] ?? null,
            ];

            foreach ($optional as $col => $val) {
                if (in_array($col, $existing, true)) {
                    $cols[] = $col;
                    $placeholders[] = ':' . $col;
                    $bindings[':' . $col] = $val !== '' ? $val : null;
                }
            }

            $sql = 'INSERT INTO student_profiles (' . implode(', ', $cols) . ') VALUES (' . implode(', ', $placeholders) . ')';
            $stmt = $this->db->prepare($sql);
            $stmt->execute($bindings);

            $this->db->commit();

            return $userId;
        } catch (PDOException $exception) {
            $this->db->rollBack();
            throw $this->mapDatabaseException($exception);
        }
    }

    public function countAdmins(): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) AS total FROM users WHERE role = :role');
        $stmt->execute([':role' => 'admin']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row === false ? 0 : (int) ($row['total'] ?? 0);
    }

    public function createAdmin(string $fullname, string $email, string $password): int
    {
        return $this->createUser('admin', $fullname, $email, $password);
    }

    /**
     * @param array<string, string> $data
     */
    public function createCompany(array $data): int
    {
        $this->db->beginTransaction();

        try {
            $userId = $this->createUser(
                role: 'entreprise',
                fullname: $data['recruiter_name'],
                email: $data['email'],
                password: $data['password']
            );

            $stmt = $this->db->prepare(
                'INSERT INTO company_profiles (user_id, company_name, sector, city, description, phone, logo)
                 VALUES (:user_id, :company_name, :sector, :city, :description, :phone, :logo)'
            );

            $stmt->execute([
                ':user_id' => $userId,
                ':company_name' => $data['company_name'],
                ':sector' => $data['sector'] !== '' ? $data['sector'] : null,
                ':city' => $data['city'] !== '' ? $data['city'] : null,
                ':description' => $data['description'] !== '' ? $data['description'] : null,
                ':phone' => $data['phone'] !== '' ? $data['phone'] : null,
                ':logo' => $data['logo'] !== '' ? $data['logo'] : null,
            ]);

            $this->db->commit();

            return $userId;
        } catch (PDOException $exception) {
            $this->db->rollBack();
            throw $this->mapDatabaseException($exception);
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, role, fullname, email, password_hash FROM users WHERE email = :email LIMIT 1'
        );

        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Récupère les profils étudiants enregistrés.
     *
     * @param string $search
     * @param int $limit
     * @param int $offset
     * @return array<int, array<string, mixed>>
     */
    public function getStudentProfiles(string $search = '', int $limit = 50, int $offset = 0): array
    {
        // Build select list dynamically based on actual columns present in student_profiles.
        $defaultCols = ['s.university', 's.level', 's.skills', 's.city'];
        $extraCols = [];

        try {
            $colStmt = $this->db->prepare("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'student_profiles'");
            $colStmt->execute();
            $existing = array_column($colStmt->fetchAll(PDO::FETCH_ASSOC), 'COLUMN_NAME');
        } catch (PDOException $e) {
            $existing = [];
        }

        if (in_array('profile_photo', $existing, true)) {
            $extraCols[] = 's.profile_photo';
        }
        if (in_array('cv', $existing, true)) {
            $extraCols[] = 's.cv';
        }
        if (in_array('phone', $existing, true)) {
            $extraCols[] = 's.phone';
        }

        $selectCols = array_merge(['u.id', 'u.fullname AS name'], $defaultCols, $extraCols);
        $sql = 'SELECT ' . implode(', ', $selectCols) . "\n                FROM users u\n                JOIN student_profiles s ON u.id = s.user_id\n                WHERE u.role = :role";

        $params = [':role' => 'etudiant'];

        if ($search !== '') {
            $sql .= ' AND (u.fullname LIKE :search OR s.skills LIKE :search OR s.city LIKE :search OR s.university LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY u.fullname ASC LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            if ($key === ':limit' || $key === ':offset') {
                continue;
            }
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère les entreprises.
     *
     * @param string $search
     * @return array<int, array<string, mixed>>
     */
    public function getCompanies(string $search = ''): array
    {
        $sql = 'SELECT u.id, u.fullname AS recruiter_name, c.company_name, c.sector, c.city, c.description, c.phone, c.logo
                FROM users u
                JOIN company_profiles c ON u.id = c.user_id
                WHERE u.role = :role';

        $params = [':role' => 'entreprise'];

        if ($search !== '') {
            $sql .= ' AND (c.company_name LIKE :search OR c.sector LIKE :search OR c.city LIKE :search OR u.fullname LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY c.company_name ASC';

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère le profil de l'entreprise par user_id.
     *
     * @param int $userId
     * @return array<string, mixed>|null
     */
    public function getCompanyProfileByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT u.id, u.fullname AS recruiter_name, u.email, c.company_name, c.sector, c.city, c.description, c.phone, c.logo
             FROM users u
             JOIN company_profiles c ON u.id = c.user_id
             WHERE u.id = :user_id AND u.role = :role LIMIT 1'
        );

        $stmt->execute([':user_id' => $userId, ':role' => 'entreprise']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function getStudentProfileByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT u.id, u.fullname, u.email, s.university, s.level, s.skills, s.city, s.search_sector, s.phone, s.profile_photo, s.cv
             FROM users u
             JOIN student_profiles s ON u.id = s.user_id
             WHERE u.id = :user_id AND u.role = :role
             LIMIT 1'
        );

        $stmt->execute([':user_id' => $userId, ':role' => 'etudiant']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * @param array<string, string|null> $data
     */
    public function updateStudentProfile(int $userId, array $data): bool
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                'UPDATE users SET fullname = :fullname WHERE id = :user_id AND role = :role'
            );
            $stmt->execute([
                ':fullname' => trim((string) ($data['fullname'] ?? '')),
                ':user_id' => $userId,
                ':role' => 'etudiant',
            ]);

            $stmt = $this->db->prepare(
                'UPDATE student_profiles SET university = :university, level = :level, skills = :skills,
                 city = :city, search_sector = :search_sector, phone = :phone,
                 profile_photo = :profile_photo, cv = :cv
                 WHERE user_id = :user_id'
            );

            $stmt->execute([
                ':university' => $data['university'] !== '' ? $data['university'] : null,
                ':level' => $data['level'] !== '' ? $data['level'] : null,
                ':skills' => $data['skills'] !== '' ? $data['skills'] : null,
                ':city' => $data['city'] !== '' ? $data['city'] : null,
                ':search_sector' => $data['search_sector'] !== '' ? $data['search_sector'] : null,
                ':phone' => $data['phone'] !== '' ? $data['phone'] : null,
                ':profile_photo' => $data['profile_photo'] !== '' ? $data['profile_photo'] : null,
                ':cv' => $data['cv'] !== '' ? $data['cv'] : null,
                ':user_id' => $userId,
            ]);

            $this->db->commit();
            return true;
        } catch (PDOException $exception) {
            $this->db->rollBack();
            return false;
        }
    }

    private function createUser(string $role, string $fullname, string $email, string $password): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (role, fullname, email, password_hash)
             VALUES (:role, :fullname, :email, :password_hash)'
        );

        $stmt->execute([
            ':role' => $role,
            ':fullname' => $fullname,
            ':email' => $email,
            ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Retourne la liste des utilisateurs (id, fullname, email, role).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllUsers(): array
    {
        $stmt = $this->db->prepare('SELECT id, fullname, email, role FROM users ORDER BY fullname ASC');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function updateUserRole(int $userId, string $role): bool
    {
        try {
            $stmt = $this->db->prepare('UPDATE users SET role = :role WHERE id = :user_id');
            return $stmt->execute([':role' => $role, ':user_id' => $userId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteUser(int $userId): bool
    {
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare('DELETE FROM student_profiles WHERE user_id = :user_id');
            $stmt->execute([':user_id' => $userId]);
            $stmt = $this->db->prepare('DELETE FROM company_profiles WHERE user_id = :user_id');
            $stmt->execute([':user_id' => $userId]);
            $stmt = $this->db->prepare('DELETE FROM users WHERE id = :user_id');
            $stmt->execute([':user_id' => $userId]);
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    private function mapDatabaseException(PDOException $exception): RuntimeException
    {
        $message = $exception->getMessage();
        if (str_contains($message, 'Duplicate entry') && str_contains($message, 'email')) {
            return new RuntimeException('Cet email existe deja.');
        }

        return new RuntimeException('Operation base de donnees impossible.');
    }
}
