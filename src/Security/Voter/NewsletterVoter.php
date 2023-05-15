<?php

namespace App\Security\Voter;

use App\Entity\Newsletter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class NewsletterVoter extends Voter
{
    protected function supports($attribute, $newsletter):bool
    {
        return in_array($attribute, ['INSCRIT'])
            && $newsletter instanceof Newsletter;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {

        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if($subject->getEmail != ""){
            return false ;
        }
        
        return true ;
    }
}
