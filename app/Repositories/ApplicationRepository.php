<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class ApplicationRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Crée une nouvelle candidature.
     *
     * @param int $studentUserId
     * @param int $offerId
     * @param string $coverLetter
     * @param string|null $cvPath
     * @return bool
     */
    public function create(int $studentUserId, int $offerId, string $coverLetter, ?string $cvPath = null): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO applications (student_user_id, offer_id, cover_letter, cv_path, status, created_at, updated_at)
             VALUES (:student_id, :offer_id, :cover_letter, :cv_path, :status, NOW(), NOW())'
        );

        return $stmt->execute([
            ':student_id' => $studentUserId,
            ':offer_id' => $offerId,
            ':cover_letter' => $coverLetter,
            ':cv_path' => $cvPath,
            ':status' => 'sent',
        ]);
    }

    /**
     * Récupère les candidatures d'un étudiant.
     *
     * @param int $studentUserId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getByStudent(int $studentUserId, int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, o.title, o.city, o.contract_type, o.deadline, 
                    c.company_name, c.sector
             FROM applications a
             JOIN offers o ON a.offer_id = o.id
             JOIN company_profiles c ON o.company_user_id = c.user_id
             WHERE a.student_user_id = :student_id
             ORDER BY a.created_at DESC
             LIMIT :limit OFFSET :offset'
        );

        $stmt->bindValue(':student_id', $studentUserId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère les candidatures reçues pour une offre.
     *
     * @param int $offerId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getByOffer(int $offerId, int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, u.fullname, s.university, s.skills
             FROM applications a
             JOIN users u ON a.student_user_id = u.id
             LEFT JOIN student_profiles s ON u.id = s.user_id
             WHERE a.offer_id = :offer_id
             ORDER BY a.created_at DESC
             LIMIT :limit OFFSET :offset'
        );

        $stmt->bindValue(':offer_id', $offerId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère toutes les candidatures reçues par une entreprise.
     *
     * @param int $companyUserId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getByCompany(int $companyUserId, int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, o.title, o.city, u.fullname, s.university, s.skills
             FROM applications a
             JOIN offers o ON a.offer_id = o.id
             JOIN users u ON a.student_user_id = u.id
             LEFT JOIN student_profiles s ON u.id = s.user_id
             WHERE o.company_user_id = :company_id
             ORDER BY a.created_at DESC
             LIMIT :limit OFFSET :offset'
        );

        $stmt->bindValue(':company_id', $companyUserId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Récupère une candidature par son ID.
     *
     * @param int $applicationId
     * @return array|null
     */
    public function getById(int $applicationId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, o.title, o.city, o.company_user_id, u.fullname, c.company_name
             FROM applications a
             JOIN offers o ON a.offer_id = o.id
             JOIN users u ON a.student_user_id = u.id
             JOIN company_profiles c ON o.company_user_id = c.user_id
             WHERE a.id = :id'
        );

        $stmt->bindValue(':id', $applicationId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($result) ? $result : null;
    }

    /**
     * Met à jour le statut d'une candidature.
     *
     * @param int $applicationId
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $applicationId, string $status): bool
    {
        if (!in_array($status, ['sent', 'reviewed', 'accepted', 'rejected'], true)) {
            return false;
        }

        $stmt = $this->db->prepare(
            'UPDATE applications SET status = :status, updated_at = NOW() WHERE id = :id'
        );

        return $stmt->execute([
            ':status' => $status,
            ':id' => $applicationId,
        ]);
    }

    /**
     * Vérifie si un étudiant a déjà candidaté à une offre.
     *
     * @param int $studentUserId
     * @param int $offerId
     * @return bool
     */
    public function hasApplied(int $studentUserId, int $offerId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM applications WHERE student_user_id = :student_id AND offer_id = :offer_id'
        );

        $stmt->bindValue(':student_id', $studentUserId, PDO::PARAM_INT);
        $stmt->bindValue(':offer_id', $offerId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
