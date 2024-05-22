<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;

class UrlToPdfConverterController extends AbstractController
{
    #[Route('/url/converter', name: 'app_url_converter')]
    public function UrlToPdfConverter
    (
        Request $request,
        HttpClientInterface $client
    ): Response
    {
        $url = $request->request->get('url');

        $response = $client->request('POST', 'http://localhost:3000/forms/chromium/convert/url', [
            'headers' => [
                'Content-Type' => 'multipart/form-data',
            ],
            'body' => [
                'url' => $url,
            ]
        ]);

        if ($response->getStatusCode() === 200) {
            $pdfContent = $response->getContent();

            return new Response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="file.pdf"',
            ]);
        } else {
            return $this->json([
                'error' => 'An error occurred while converting the URL to PDF. Please try again.',
            ], $response->getStatusCode());
        }
    }
}
