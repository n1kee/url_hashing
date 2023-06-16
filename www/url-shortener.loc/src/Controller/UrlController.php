<?php

namespace App\Controller;

use App\Entity\Url;
use App\Repository\UrlRepository;
use App\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use DateTimeImmutable;
use App\Validator\UrlValidator;
use App\Validator\HashValidator;

class UrlController extends Controller
{
    /**
     */
    public function getUrl(string $url): ?Url
    {
        /** @var UrlRepository $urlRepository */
        $urlRepository = $this->getDoctrine()->getRepository(Url::class);
        return $urlRepository->findOneByUrl($url);
    }

    /**
     */
    public function getUrlByHash(string $hash, HashValidator $hashValidator): ?Url
    {
        $this->validate([ "hash" => $hash ], $hashValidator);

        /** @var UrlRepository $urlRepository */
        $urlRepository = $this->getDoctrine()->getRepository(Url::class);
        $url = $urlRepository->findOneByHash($hash);
        if ($url) {
            $nowTmstmp = (new DateTimeImmutable)->getTimestamp();
            $hasCreatedTmstmp = $url->getCreatedDate()->getTimestamp();
            $hashExists = abs($nowTmstmp - $hasCreatedTmstmp) / 60 / 60;

            if ($hashExists > $this->getParameter('url_decoding_time')) {
                $this->sendErrorResponse("Expired hash.");
            }
        } else {
            $this->sendErrorResponse("Non-existent hash.");
        }
        return $url;
    }

    /**
     * @Route("/encode-url", name="encode_url")
     */
    public function encodeUrl(Request $request, UrlValidator $urlValidator): JsonResponse
    {
        $urlString = $request->get('url');

        $this->validate([ "url" => $urlString ], $urlValidator);

        $url = $this->getUrl($urlString);

        if (!$url) {
            $url = new Url();
            $url->setUrl($urlString);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($url);
            $entityManager->flush();
        }

        return $this->json([
            'hash' => $url->getHash()
        ]);
    }

    /**
     * @Route("/decode-url", name="decode_url")
     */
    public function decodeUrl(Request $request, HashValidator $hashValidator): JsonResponse
    {
        $url = $this->getUrlByHash($request->get('hash'), $hashValidator);

        return $this->json([
            'url' => $url->getUrl()
        ]);
    }


    /**
     * @Route("/go-url", name="go_url")
     */
    public function goUrl(Request $request, HashValidator $hashValidator)
    {
        $url = $this->getUrlByHash($request->get('hash'), $hashValidator);

        return $this->redirect($url->getUrl());
    }
}
