<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\User;
use App\Entity\Offre;
use App\Form\CandidatureType;
use App\Repository\CandidatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Smalot\PdfParser\Parser;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/offre/{id_offre}/candidature')]
final class CandidatureController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route(name: 'app_candidature_index', methods: ['GET'])]
    public function index(CandidatureRepository $candidatureRepository): Response
    {
        return $this->render('candidature/index.html.twig', [
            'candidatures' => $candidatureRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_candidature_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Offre $offre, CandidatureRepository $candidatureRepository): Response
    {
        $candidature = new Candidature();
        $candidature->setUser($entityManager->getReference(User::class, 1));
        $candidature->setOffre($offre);
        $candidature->setStatut('En attente');
        $candidature->setDateCandidature(new \DateTime());
        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cvFile = $form->get('cv_upload')->getData();

            if ($cvFile) {
                // Store the file content in the database as BLOB
                $cvContent = file_get_contents($cvFile->getPathname());
                $candidature->setCvFile($cvContent);

                try {
                    // Log the CV file details
                    $this->logger->info('CV file received', ['path' => $cvFile->getPathname(), 'size' => strlen($cvContent)]);

                    // Extract text from the CV using Smalot\PdfParser
                    $parser = new Parser();
                    $pdf = $parser->parseContent($cvContent);
                    $cvText = $pdf->getText();
                    $this->logger->info('CV text extracted', ['length' => strlen($cvText), 'text' => substr($cvText, 0, 100)]);

                    // Handle empty CV text (e.g., scanned PDF)
                    if (empty($cvText)) {
                        $this->logger->warning('CV text is empty, possibly a scanned PDF');
                        $cvText = 'No text extracted from CV';
                    }

                    // Get motivation letter
                    $motivationLetter = $candidature->getLettreMotivation();
                    $this->logger->info('Motivation letter retrieved', ['length' => strlen($motivationLetter)]);

                    // Prepare offer details
                    $offerDetails = [
                        'title' => $offre->getTitre(),
                        'description' => $offre->getDescription() ?? 'N/A',
                        'typeContrat' => $offre->getTypeContrat() ?? 'N/A',
                        'ville' => $offre->getVille() ?? 'N/A',
                        'gouvernorat' => $offre->getGouvernorat() ?? 'N/A',
                        'salaire' => $offre->getSalaire() ? $offre->getSalaire() . ' TND' : 'Not specified',
                    ];
                    $this->logger->info('Offer details prepared', $offerDetails);

                    // Truncate inputs to avoid token limit (2048 tokens)
                    $cvText = substr($cvText, 0, 1000);
                    $motivationLetter = substr($motivationLetter, 0, 500);
                    $this->logger->info('Inputs truncated', ['cv_length' => strlen($cvText), 'motivation_length' => strlen($motivationLetter)]);

                    // Send data to LLaMA via Ollama API
                    $client = HttpClient::create();
                    $this->logger->info('Sending request to Ollama');
                    $response = $client->request('POST', 'http://localhost:11434/api/chat', [
                        'json' => [
                            'model' => 'llama3:latest',
                            'messages' => [
                                [
                                    'role' => 'user',
                                    'content' => "Evaluate the candidate's suitability for the job offer based on the following data and return a score out of 100 as a single number (e.g., 85). Ensure the output is strictly a number with no text, explanation, or units.

                                    **Job Offer Details:**
                                    Title: {$offerDetails['title']}
                                    Description: {$offerDetails['description']}
                                    Contract Type: {$offerDetails['typeContrat']}
                                    Location: {$offerDetails['ville']}, {$offerDetails['gouvernorat']}
                                    Salary: {$offerDetails['salaire']}

                                    **Candidate Data:**
                                    CV Text: $cvText
                                    Motivation Letter: $motivationLetter"
                                ],
                            ],
                            'stream' => false,
                        ],
                    ]);

                    // Log the raw response
                    $rawResponse = $response->getContent();
                    $this->logger->info('Ollama response received', ['response' => $rawResponse]);

                    // Parse the score from LLaMA's response
                    $result = $response->toArray();
                    $score = (float) $result['message']['content'];
                    $this->logger->info('Score parsed', ['score' => $score]);

                    // Ensure the score is between 0 and 100
                    $score = max(0, min(100, $score));
                    $this->logger->info('Score validated', ['score' => $score]);

                    // Set the score in the Candidature entity
                    $candidature->setScore($score);
                    $this->logger->info('Score set in Candidature entity');
                } catch (\Exception $e) {
                    $this->logger->error('Error calculating score', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                    $this->addFlash('warning', 'Unable to calculate score: ' . $e->getMessage());
                    $candidature->setScore(null);
                }
            }

            $entityManager->persist($candidature);
            $entityManager->flush();
            $this->addFlash('success', 'Votre candidature a été soumise avec succès');
            return $this->redirectToRoute('app_offre_show', [
                'id_offre' => $offre->getIdOffre()
            ]);
        }

        return $this->render('candidature/new.html.twig', [
            'candidature' => $candidature,
            'offre' => $offre,
            'form' => $form,
        ]);
    }

    #[Route('/{id_candidature}', name: 'app_candidature_show', methods: ['GET'])]
    public function show(
        Candidature $candidature,
        Offre $offre,
        CandidatureRepository $candidatureRepository
    ): Response {
        $allCandidatures = $candidatureRepository->findBy(
            ['offre' => $offre],
            ['date_candidature' => 'ASC']
        );

        // Find current position in the list
        $loopPosition = array_search($candidature, $allCandidatures) + 1;

        // Get previous and next candidatures
        $previousCandidature = null;
        $nextCandidature = null;
        $currentIndex = array_search($candidature, $allCandidatures);

        if ($currentIndex > 0) {
            $previousCandidature = $allCandidatures[$currentIndex - 1];
        }

        if ($currentIndex < count($allCandidatures) - 1) {
            $nextCandidature = $allCandidatures[$currentIndex + 1];
        }

        // Get other candidatures for the same offre (excluding current one)
        $otherCandidatures = array_filter($allCandidatures, function ($c) use ($candidature) {
            return $c->getIdCandidature() !== $candidature->getIdCandidature();
        });

        return $this->render('candidature/show.html.twig', [
            'candidature' => $candidature,
            'loopPosition' => $loopPosition,
            'previousCandidature' => $previousCandidature,
            'nextCandidature' => $nextCandidature,
            'offre' => $offre,
            'otherCandidatures' => $otherCandidatures,
        ]);
    }

    #[Route('/{id_candidature}/edit', name: 'app_candidature_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidature $candidature, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_candidature_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('candidature/edit.html.twig', [
            'candidature' => $candidature,
            'form' => $form,
        ]);
    }

    #[Route('/{id_candidature}', name: 'app_candidature_delete', methods: ['POST'])]
    public function delete(Request $request, Candidature $candidature, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $candidature->getId_candidature(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($candidature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_candidature_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id_candidature}/view-cv', name: 'app_candidature_view_cv')]
    public function viewCv(Candidature $candidature): Response
    {
        $cvContent = $candidature->getCvFile();

        if (is_resource($cvContent)) {
            $cvContent = stream_get_contents($cvContent);
        }

        return new Response($cvContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="cv_' . $candidature->getId_candidature() . '.pdf"'
        ]);
    }

    #[Route('/{id_candidature}/download-cv', name: 'app_candidature_download_cv')]
    public function downloadCv(Candidature $candidature): Response
    {
        $cvContent = $candidature->getCvFile();

        if (is_resource($cvContent)) {
            $cvContent = stream_get_contents($cvContent);
        }

        return new Response($cvContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="cv_' . $candidature->getIdCandidature() . '.pdf"'
        ]);
    }

    #[Route('/{id_candidature}/accept', name: 'app_candidature_accept', methods: ['POST'])]
    public function accept(
        Candidature $candidature,
        EntityManagerInterface $entityManager,
        Request $request,
        MailerInterface $mailer
    ): Response {
        $candidature->setStatut('Acceptée');
        $entityManager->flush();

        try {
            $email = (new Email())
                ->from($this->getParameter('MAILER_FROM_ADDRESS'))
                ->to($candidature->getUser()->getEmail())
                ->subject('Votre candidature a été acceptée')
                ->text(sprintf(
                    "Bonjour %s,\n\nNous sommes heureux de vous informer que votre candidature pour l'offre '%s' a été acceptée.\n\nCordialement,\nL'équipe de recrutement",
                    $candidature->getUser()->getPrenom(),
                    $candidature->getOffre()->getTitre()
                ));

            $mailer->send($email);
            $this->logger->info('Acceptance email sent', ['candidature_id' => $candidature->getIdCandidature()]);

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true, 'message' => 'Email d\'acceptation envoyé']);
            }
            $this->addFlash('success', 'Email d\'acceptation envoyé');
        } catch (\Exception $e) {
            $this->logger->error('Failed to send acceptance email', [
                'error' => $e->getMessage(),
                'candidature_id' => $candidature->getIdCandidature()
            ]);

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'message' => 'Erreur d\'envoi d\'email']);
            }
            $this->addFlash('error', 'Erreur d\'envoi d\'email');
        }

        return $this->redirectToRoute('app_candidature_show', [
            'id_offre' => $candidature->getOffre()->getIdOffre(),
            'id_candidature' => $candidature->getIdCandidature(),
        ]);
    }

    #[Route('/{id_candidature}/refuse', name: 'app_candidature_refuse', methods: ['POST'])]
    public function refuse(
        Candidature $candidature,
        EntityManagerInterface $entityManager,
        Request $request,
        MailerInterface $mailer
    ): Response {
        $candidature->setStatut('Refusée');
        $entityManager->flush();

        try {
            // Create email using Symfony's Email class
            $email = (new Email())
                ->from('smtp@mailtrap.io') // Matches MAILER_FROM_ADDRESS in .env
                ->to($candidature->getUser()->getEmail())
                ->subject('Votre candidature a été examinée')
                ->text(sprintf(
                    "Bonjour %s,\n\nAprès examen, nous regrettons de vous informer que votre candidature pour l'offre '%s' n'a pas été retenue.\n\nCordialement,\nL'équipe de recrutement",
                    $candidature->getUser()->getPrenom(),
                    $candidature->getOffre()->getTitre()
                ));

            // Send email via Symfony Mailer (using Mailtrap API via MAILER_DSN)
            $mailer->send($email);

            $this->logger->info('Email sent successfully via Mailtrap API');

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true, 'message' => 'Email de refus envoyé']);
            }
            $this->addFlash('success', 'Email de refus envoyé');
        } catch (\Exception $e) {
            $this->logger->error('Email sending failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Erreur d\'envoi d\'email',
                    'error' => $e->getMessage()
                ]);
            }
            $this->addFlash('error', 'Erreur d\'envoi d\'email: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_candidature_show', [
            'id_offre' => $candidature->getOffre()->getIdOffre(),
            'id_candidature' => $candidature->getIdCandidature(),
        ]);
    }


    #[Route('/send-test-email', name: 'app_send_test_email')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        try {
            $email = (new Email())
                ->from('hello@demomailtrap.co')
                ->to('hammoudayass@gmail.com')
                ->subject('Hello from Symfony and MailTrap')
                ->text('This is a test email')
                ->html('<p>This is a <b>test</b> email!</p>');

            $mailer->send($email);

            $this->logger->info('Test email sent successfully');
            return new Response('Email sent successfully!');
        } catch (\Exception $e) {
            $this->logger->error('Failed to send test email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return new Response('Failed to send email: ' . $e->getMessage(), 500);
        }
    }
}
