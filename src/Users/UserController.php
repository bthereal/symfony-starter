<?php

namespace App\Users;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $users;

    public function __construct(UserRepo $users)
    {
        $this->users = $users;
    }

    public function indexAction()
    {
        $this->denyAccessUnlessGranted(UserPermissions::VIEW, $this->getUser());

        $users = $this->users->findAll();

        return $this->render('users/index.html.twig', [
                'users' => $users,
            ]
        );
    }

    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();

        $this->denyAccessUnlessGranted(UserPermissions::CREATE, $user);

        $roles = UserRoles::all();

        $form = $this->createForm(CreateUserFormType::class, $user, ['roles' => $roles]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setActive(true);

            try {
                $this->users->save($user);
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('warning', 'A user with this email already exists.');
            } catch (\Exception $e) {
                $this->addFlash('warning', 'An unknown error occurred, please try again or contact support if the problem persists.');
            }

            return $this->redirectToRoute('admin');
        }

        return $this->render('users/register.html.twig', [
                'form' => $form->createView(),
            ]
        );
    }

    public function updateAction(Request $request)
    {
        $userId = $request->attributes->get('userId');
        $user = $this->users->find($userId);

        if (!$user) {
            throw new NotFoundHttpException('User does not exist');
        }

        $this->denyAccessUnlessGranted(UserPermissions::EDIT, $user);

        $roles = UserRoles::all();

        $form = $this->createForm(EditUserFormType::class, $user, ['roles' => $roles]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('delete')->isClicked()) {
                return $this->redirectToRoute('user_delete', ['userId' => $userId]);
            }

            try {
                $this->users->save($user);
                $this->addFlash('success', 'User updated successfully.');
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('warning', 'A user with this email already exists.');
            } catch (\Exception $e) {
                $this->addFlash('warning', 'An unknown error occurred, please try again or contact support if the problem persists.');
            }

            return $this->redirectToRoute('admin');
        }

        return $this->render('users/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function deleteAction(Request $request)
    {
        $this->denyAccessUnlessGranted(UserPermissions::DELETE, $this->getUser());

        $userId = $request->attributes->get('userId');
        $user = $this->users->find($userId);

        if (!$user) {
            throw new NotFoundHttpException('User does not exist');
        }

        $form = $this->createForm(DeleteUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->users->delete($user);
                $this->addFlash('success', 'User deleted successfully.');
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
            }

            return $this->redirectToRoute('organisation_admin');
        }

        return $this->render('users/delete.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
