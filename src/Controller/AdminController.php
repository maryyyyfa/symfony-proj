<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\User;
use App\Form\CourseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        $courses = $entityManager->getRepository(Course::class)->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'users' => $users,
            'courses' => $courses,
        ]);
    }

    #[Route('/course/new', name: 'admin_course_new')]
    #[Route('/course/{id}/edit', name: 'admin_course_edit')]
    public function editCourse(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, Course $course = null): Response
    {
        $course = $course ?? new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pdfFile */
            $pdfFile = $form->get('pdfFile')->getData();
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pdfFile->guessExtension();

                $pdfFile->move(
                    $this->getParameter('courses_directory'),
                    $newFilename
                );

                $course->setPdfFile($newFilename);
            }

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('courses_images_directory'),
                    $newFilename
                );

                $course->setImage($newFilename);
            }

            $entityManager->persist($course);
            $entityManager->flush();

            $this->addFlash('success', 'Cours enregistré avec succès.');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/course_form.html.twig', [
            'form' => $form->createView(),
            'course' => $course,
        ]);
    }
} 