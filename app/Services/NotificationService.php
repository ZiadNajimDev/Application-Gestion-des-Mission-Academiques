<?php
// app/Services/NotificationService.php


namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Models\Mission;

class NotificationService
{
    // Create notification for a specific user
    public function notifyUser($userId, $title, $message, $type = 'primary', $icon = 'bell', $link = null, $relatedId = null)
    {
        $notification = Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'icon' => $icon,
            'link' => $link,
            'related_id' => $relatedId
        ]);

        // Broadcast the notification
        broadcast(new \App\Events\NotificationCreated($notification))->toOthers();

        return $notification;
    }

    // Notify users by role
    public function notifyByRole($role, $title, $message, $type = 'primary', $icon = 'bell', $link = null, $relatedId = null)
    {
        $users = User::where('role', $role)->get();
        foreach ($users as $user) {
            $this->notifyUser($user->id, $title, $message, $type, $icon, $link, $relatedId);
        }
    }

    // Notify when mission is submitted
    public function notifyMissionSubmitted(Mission $mission)
    {
        // Get department head
        $departmentHeads = User::where('role', 'chef_departement')
            ->where('department', $mission->user->department)
            ->get();
        
        foreach ($departmentHeads as $head) {
            $this->notifyUser(
                $head->id,
                'Nouvelle mission soumise',
                'Une nouvelle mission a été soumise par ' . $mission->user->name,
                'primary',
                'paper-plane',
                route('chef.mission_validate'),
                $mission->id
            );
        }
    }

    // Notify when mission is validated by department head
    public function notifyMissionValidatedByHead(Mission $mission)
    {
        // Notify teacher
        $this->notifyUser(
            $mission->user_id,
            'Mission validée par chef de département',
            'Votre mission a été validée par le chef de département.',
            'success',
            'check-circle',
            route('teacher.missions.show', $mission->id),
            $mission->id
        );

        // Notify director
        $this->notifyByRole(
            'directeur',
            'Nouvelle mission à valider',
            'Une mission validée par le chef de département est en attente de votre validation.',
            'primary',
            'info-circle',
            route('director.pending_missions'),
            $mission->id
        );
    }

    // Notify when mission is validated by director
    public function notifyMissionValidatedByDirector(Mission $mission)
    {
        // Notify teacher
        $this->notifyUser(
            $mission->user_id,
            'Mission approuvée',
            'Votre mission a été approuvée par le directeur.',
            'success',
            'check-circle',
            route('teacher.missions.show', $mission->id),
            $mission->id
        );

        // Notify accountant
        $this->notifyByRole(
            'comptable',
            'Nouvelle mission approuvée',
            'Une nouvelle mission a été approuvée et nécessite des réservations.',
            'info',
            'plane',
            route('accountant.reservations'),
            $mission->id
        );

        // Notify department head
        $departmentHeads = User::where('role', 'chef_departement')
            ->where('department', $mission->user->department)
            ->get();
        
        foreach ($departmentHeads as $head) {
            $this->notifyUser(
                $head->id,
                'Mission approuvée par le directeur',
                'Une mission de votre département a été approuvée par le directeur.',
                'success',
                'check-circle',
                null,
                $mission->id
            );
        }
    }

    // Notify when mission is rejected
    public function notifyMissionRejected(Mission $mission, $rejectedBy)
    {
        $rejectorRole = $rejectedBy === 'chef' ? 'chef de département' : 'directeur';
        
        // Notify teacher
        $this->notifyUser(
            $mission->user_id,
            'Mission rejetée',
            'Votre mission a été rejetée par le ' . $rejectorRole . '.',
            'danger',
            'times-circle',
            route('teacher.missions.show', $mission->id),
            $mission->id
        );
    }

    // Notify when travel arrangements are made
    public function notifyTravelArranged(Mission $mission)
    {
        // Notify teacher
        $this->notifyUser(
            $mission->user_id,
            'Voyage réservé',
            'Vos arrangements de voyage ont été effectués. N\'oubliez pas de soumettre vos justificatifs après la mission.',
            'success',
            'ticket-alt',
            route('teacher.missions.show', $mission->id),
            $mission->id
        );
    }

    // Notify when proof documents are required
    public function notifyProofRequired(Mission $mission)
    {
        // Notify teacher
        $this->notifyUser(
            $mission->user_id,
            'Justificatifs requis',
            'N\'oubliez pas de soumettre vos justificatifs pour la mission.',
            'warning',
            'exclamation-circle',
            route('teacher.proof_documents'),
            $mission->id
        );
    }

    // Notify when proof documents are submitted
    public function notifyProofDocumentsSubmitted(Mission $mission)
    {
        // Notify accountant
        $this->notifyByRole(
            'comptable',
            'Nouveaux justificatifs soumis',
            'Des justificatifs ont été soumis pour la mission de ' . $mission->user->name,
            'info',
            'file-invoice',
            route('accountant.payments'),
            $mission->id
        );
    }

    // Notify when proof documents are validated
    public function notifyProofDocumentsValidated(Mission $mission)
    {
        // Notify teacher
        $this->notifyUser(
            $mission->user_id,
            'Justificatifs validés',
            'Vos justificatifs ont été validés par le service comptabilité.',
            'success',
            'check-circle',
            route('teacher.missions.show', $mission->id),
            $mission->id
        );
    }

    // Notify when payment is made
    public function notifyPaymentMade(Mission $mission, $amount)
    {
        // Notify teacher
        $this->notifyUser(
            $mission->user_id,
            'Paiement effectué',
            'Le paiement de ' . number_format($amount, 2) . ' DH a été effectué pour votre mission.',
            'success',
            'money-bill-wave',
            route('teacher.missions.show', $mission->id),
            $mission->id
        );
    }

    // Notify when payment is rejected
    public function notifyPaymentRejected(Mission $mission, $reason)
    {
        // Notify teacher
        $this->notifyUser(
            $mission->user_id,
            'Paiement rejeté',
            'Votre paiement a été rejeté. Raison: ' . $reason,
            'danger',
            'times-circle',
            route('teacher.missions.show', $mission->id),
            $mission->id
        );
    }

    // Notify when additional documents are requested
    public function notifyAdditionalDocumentsRequested(Mission $mission, $requestedDocuments)
    {
        // Notify teacher
        $this->notifyUser(
            $mission->user_id,
            'Documents supplémentaires requis',
            'Le service comptabilité demande des documents supplémentaires: ' . $requestedDocuments,
            'warning',
            'exclamation-circle',
            route('teacher.proof_documents'),
            $mission->id
        );
    }

    // Notify when budget is insufficient
    public function notifyInsufficientBudget(Mission $mission, $department, $requiredAmount, $availableAmount)
    {
        // Notify accountant
        $this->notifyByRole(
            'comptable',
            'Budget insuffisant',
            "Le budget du département {$department} est insuffisant pour la mission. Montant requis: " . 
            number_format($requiredAmount, 2) . " DH, Disponible: " . number_format($availableAmount, 2) . " DH",
            'danger',
            'exclamation-triangle',
            route('accountant.payments'),
            $mission->id
        );
    }

    // Notify when department budget is updated
    public function notifyDepartmentBudgetUpdated($department, $newBudget)
    {
        // Notify accountant
        $this->notifyByRole(
            'comptable',
            'Budget département mis à jour',
            "Le budget du département {$department} a été mis à jour à " . number_format($newBudget, 2) . " DH",
            'info',
            'chart-line',
            route('accountant.payments'),
            null
        );
    }

    // Notify when mission is completed and financial proof documents are required
    public function notifyFinancialProofRequired(Mission $mission)
    {
        // Notify teacher
        $this->notifyUser(
            $mission->user_id,
            'Justificatifs financiers requis',
            'Votre mission est terminée. Veuillez soumettre vos justificatifs financiers pour le remboursement.',
            'warning',
            'file-invoice-dollar',
            route('teacher.proof_documents'),
            $mission->id
        );

        // Notify accountant
        $this->notifyByRole(
            'comptable',
            'Mission terminée - Justificatifs en attente',
            'La mission de ' . $mission->user->name . ' est terminée. En attente des justificatifs financiers.',
            'info',
            'clock',
            route('accountant.payments'),
            $mission->id
        );
    }

    // Notify when mission is completed
    public function notifyMissionCompleted(Mission $mission)
    {
        // Notify teacher
        $this->notifyUser(
            $mission->user_id,
            'Mission terminée',
            'Votre mission est terminée. N\'oubliez pas de soumettre vos justificatifs financiers pour le remboursement.',
            'success',
            'check-circle',
            route('teacher.proof_documents'),
            $mission->id
        );

        // Notify accountant
        $this->notifyByRole(
            'comptable',
            'Mission terminée',
            'La mission de ' . $mission->user->name . ' est terminée. En attente des justificatifs financiers.',
            'info',
            'clipboard-check',
            route('accountant.payments'),
            $mission->id
        );
    }

    // Notify when financial proof documents are submitted
    public function notifyFinancialProofSubmitted(Mission $mission)
    {
        // Notify accountant
        $this->notifyByRole(
            'comptable',
            'Justificatifs financiers soumis',
            'Des justificatifs financiers ont été soumis pour la mission de ' . $mission->user->name,
            'info',
            'file-invoice-dollar',
            route('accountant.payments'),
            $mission->id
        );
    }

    // Notify when financial proof documents are validated
    public function notifyFinancialProofValidated(Mission $mission)
    {
        // Notify teacher
        $this->notifyUser(
            $mission->user_id,
            'Justificatifs financiers validés',
            'Vos justificatifs financiers ont été validés. Le paiement sera effectué prochainement.',
            'success',
            'check-circle',
            route('teacher.missions.show', $mission->id),
            $mission->id
        );
    }

    // Notify when financial proof documents are rejected
    public function notifyFinancialProofRejected(Mission $mission, $reason)
    {
        // Notify teacher
        $this->notifyUser(
            $mission->user_id,
            'Justificatifs financiers rejetés',
            'Vos justificatifs financiers ont été rejetés. Raison: ' . $reason,
            'danger',
            'times-circle',
            route('teacher.proof_documents'),
            $mission->id
        );
    }
}