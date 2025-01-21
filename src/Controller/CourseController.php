<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\CourseProgress;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\UserInterest;
use Symfony\Component\HttpFoundation\Request;

#[Route('/courses')]
class CourseController extends AbstractController
{
    #[Route('/', name: 'app_course_index', methods: ['GET'])]
    public function index(CourseRepository $courseRepository): Response
    {
        return $this->render('course/index.html.twig', [
            'courses' => $courseRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_course_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Course $course, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $isInterested = false;
        $progress = null;
        
        if ($user) {
            // Vérifier l'intérêt
            $interest = $entityManager->getRepository(UserInterest::class)->findOneBy([
                'user' => $user,
                'course' => $course
            ]);
            $isInterested = $interest !== null;

            // Récupérer la progression
            $progress = $entityManager->getRepository(CourseProgress::class)->findOneBy([
                'user' => $user,
                'course' => $course
            ]);

            // Créer une nouvelle progression si elle n'existe pas
            if (!$progress) {
                $progress = new CourseProgress();
                $progress->setUser($user);
                $progress->setCourse($course);
                $progress->setLastAccessedAt(new \DateTimeImmutable());
                $entityManager->persist($progress);
                $entityManager->flush();
            }
        }

        return $this->render('course/show.html.twig', [
            'course' => $course,
            'isInterested' => $isInterested,
            'progress' => $progress,
        ]);
    }

    #[Route('/course/{id}/interest', name: 'app_course_interest', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function interest(Course $course, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('interest'.$course->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $user = $this->getUser();
        
        // Vérifier si l'utilisateur n'est pas déjà intéressé
        $existingInterest = $entityManager->getRepository(UserInterest::class)->findOneBy([
            'user' => $user,
            'course' => $course
        ]);

        if (!$existingInterest) {
            $interest = new UserInterest();
            $interest->setUser($user);
            $interest->setCourse($course);
            
            $entityManager->persist($interest);
            $entityManager->flush();

            $this->addFlash('success', 'Vous êtes maintenant intéressé par ce cours.');
        }

        return $this->redirectToRoute('app_course_show', ['id' => $course->getId()]);
    }

    #[Route('/course/{id}/uninterest', name: 'app_course_uninterest', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function uninterest(Course $course, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('uninterest'.$course->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $user = $this->getUser();
        
        $interest = $entityManager->getRepository(UserInterest::class)->findOneBy([
            'user' => $user,
            'course' => $course
        ]);

        if ($interest) {
            $entityManager->remove($interest);
            $entityManager->flush();

            $this->addFlash('success', 'Vous n\'êtes plus intéressé par ce cours.');
        }

        return $this->redirectToRoute('app_course_show', ['id' => $course->getId()]);
    }
} 