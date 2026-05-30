<?php

namespace App\Controller;

use App\Entity\Household;
use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\HouseholdRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/{_locale}/messages', requirements: ['_locale' => 'fr|en'])]
#[IsGranted('ROLE_USER')]
class MessageController extends AbstractController
{
    #[Route('', name: 'app_messages', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        MessageRepository $messages,
        HouseholdRepository $households,
        EntityManagerInterface $em
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $household = $this->resolveHousehold($user, $households);

        if ($household === null) {
            return $this->render('message/index.html.twig', [
                'household' => null,
                'meta_title' => 'Messagerie',
            ]);
        }

        $message = new Message();
        $message->setHousehold($household);
        $message->setAuthor($user);

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($message);
            $em->flush();
            $this->addFlash('success', 'flash.message_sent');
            return $this->redirectToRoute('app_messages');
        }

        return $this->render('message/index.html.twig', [
            'household' => $household,
            'form' => $form->createView(),
            'messages' => $messages->findByHousehold($household),
            'meta_title' => 'Messagerie',
        ]);
    }

    private function resolveHousehold(User $user, HouseholdRepository $households): ?Household
    {
        if ($user->getHousehold() !== null) {
            return $user->getHousehold();
        }
        if (in_array('ROLE_OWNER', $user->getRoles(), true)) {
            return $households->findOneBy(['owner' => $user]);
        }
        return null;
    }
}
