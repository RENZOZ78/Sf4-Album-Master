<?php

namespace App\Security\Voter;

use App\Entity\Note;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Voter déterminant les droits de suppression d'une note d'album
 */
class NoteDeleteVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html

        // Le voter ne doit intervenir que s'il s'agit de l'attribut (similaire à un role) "NOTE_DELETE"
        // et si le sujet (ce sur quoi on vérifie le droit) est une instance de Note
        return $attribute === 'NOTE_DELETE'
            && $subject instanceof Note;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var Note $subject */

        $user = $token->getUser();
        // Utilisateur non connecté = pas le droit de supprimer
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Administrateur = autorisé à supprimer toutes les notes
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Utilisateur auteur de la note = autorisé à supprimer sa propre note
        if ($user === $subject->getAuthor()) {
            return true;
        }

        // Aucun des cas précédents = pas le droit de supprimer
        return false;
    }
}
