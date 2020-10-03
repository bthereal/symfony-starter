<?php

namespace App\Users;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    private $users;
    private $logger;
    private $passwordEncoder;

    public function __construct(UserRepo $users, LoggerInterface $logger, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->users = $users;
        $this->logger = $logger;
        $this->passwordEncoder = $passwordEncoder;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('user:create')
            ->setDescription('Create a user.')
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputArgument('roles', InputArgument::REQUIRED, 'The user roles'),
            ])
            ->setHelp(<<<'EOT'
                The <info>user:create</info> command creates an admin user:
                This interactive shell will ask you for an email, a password and am organisation name.
                You can alternatively specify the arguments as the first and second and third arguments:
                  <info>bin/console %command.full_name% john.doe@example.com mypassword ROLE_ADMIN</info>
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $user = new User();

        $user->setUsername($input->getArgument('email'));
        $user->setPassword($this->passwordEncoder->encodePassword($user, $input->getArgument('password')));
        $user->setActive(true);

        $roles = $input->getArgument('roles');

        if (false === strpos($roles, ',')) {
            if (in_array($roles, UserRoles::all())) {
                $user->setRoles([$roles]);
            }
        } else {
            $roles = explode(',', $roles);
            foreach ($roles as $role) {
                if (!in_array($role, UserRoles::all())) {
                    throw new \Exception('User role '.$role.' is invalid');
                }
            }
            $user->setRoles([$roles]);
        }

        try {
            $this->users->save($user);
        } catch (UniqueConstraintViolationException $e) {
            throw new \Exception('A user with this email already exists.');
        } catch (\Exception $e) {
            dump($e->getMessage());exit;
            throw new \Exception('An unknown error occurred, please try again or contact support if the problem persists.');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = [];

        if (!$input->getArgument('email')) {
            $question = new Question('Please choose an email:');
            $question->setValidator(function ($email) {
                if (empty($email)) {
                    throw new \Exception('Email can not be empty');
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception('Email address is invalid');
                }

                return $email;
            });
            $questions['email'] = $question;
        }

        if (!$input->getArgument('password')) {
            $question = new Question('Please choose a password:');
            $question->setValidator(function ($password) {
                if (empty($password)) {
                    throw new \Exception('Password can not be empty');
                }

                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }

        if (!$input->getArgument('roles')) {
            $question = new Question('Please enter an a comma separated list of valid system roles:');
            $question->setValidator(function ($roles) {
                if (empty($roles)) {
                    throw new \Exception('Roles can not be empty');
                }

                return $roles;
            });
            $questions['roles'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}
