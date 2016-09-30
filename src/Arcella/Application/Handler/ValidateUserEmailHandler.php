<?php

/*
 * This file is part of the Arcella package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arcella\Application\Handler;

use Arcella\Domain\Command\ValidateUserEmail;
use Arcella\Domain\Event\UserUpdatedEmailEvent;
use Arcella\Domain\Repository\UserRepositoryInterface;
use Arcella\UserBundle\Utils\TokenValidator;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

/**
 * This class is responsible for handling the ValidateUserEmail command, which is
 * used to validate a changed email address of a given User entity.
 */
class ValidateUserEmailHandler
{
    /**
     * @var $userRepository EntityRepository
     */
    private $userRepository;

    /**
     * @var TokenValidator TokenValidator
     */
    private $tokenValidator;

    /**
     * ValidateUserEmailHandler constructor.
     *
     * @param UserRepositoryInterface $userRepository
     * @param TokenValidator          $tokenValidator
     */
    public function __construct(UserRepositoryInterface $userRepository, TokenValidator $tokenValidator)
    {
        $this->userRepository = $userRepository;
        $this->tokenValidator = $tokenValidator;
    }

    /**
     * Handles the ValidateUserEmail command and changes the email address of a
     * User entity.
     *
     * @param ValidateUserEmail $command
     *
     * @throws EntityNotFoundException
     */
    public function handle(ValidateUserEmail $command)
    {
        // Fetch User entity
        $user = $this->userRepository->findOneBy(['username' => $command->username()]);

        if (!$user) {
            throw new EntityNotFoundException(
                'No entity found for username '.$command->username()
            );
        }

        if (!$this->tokenValidator->validateToken($command->token())) {
            throw new ValidatorException('Could not validate token: '.$command->token());
        }

        $user->setEmailIsVerified(true);
        $this->tokenValidator->removeToken($command->token());

        // Add the User entity to the UserRepository
        $this->userRepository->save($user);
    }
}
