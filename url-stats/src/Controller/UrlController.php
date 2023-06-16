<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UrlRepository;
use App\Entity\Url;

class UrlController extends AbstractController
{
    public function __construct(UrlRepository $urlRepository)
    {
        $this->urlRepository = $urlRepository;
    }

    /**
     * @Route("/urls/add", name="url_add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        $urlStr = preg_replace("|^https?://(www.)?|", "", $request->input("url"));

        $url = new Url;
        $url->url = $urlStr;
        $url->setCreatedDate($request->input("created_date"));

        $this->urlRepository->add($url, true);

        return $this->json([
            "success" => true,
        ]);
    }

    /**
     * @Route("/urls/period", name="url_period_stats")
     */
    public function period(Request $request): Response
    {
        return $this->json([
            "count" => $this->urlRepository->countUniqueByPeriod(
                $request->get("from"),
                $request->get("to")
            )
        ]);
    }

    /**
     * @Route("/urls/domain", name="url_domain_stats")
     */
    public function domain(Request $request): Response
    {
        return $this->json([
            "count" => $this->urlRepository->countUniqueByDomain(
                $request->get("domain")
            )
        ]);
    }
}
