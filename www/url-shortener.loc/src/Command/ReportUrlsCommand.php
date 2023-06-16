<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\Url;
use App\Repository\UrlRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Exception;

class ReportUrlsCommand extends Command
{
    protected static $defaultName = 'ReportUrlsCommand';
    protected static $defaultDescription = "Reports all new URL's to the designated service.";

    public function __construct(
        UrlRepository $urlRepository,
        HttpClientInterface $httpClient,
        ParameterBagInterface $params
    ) {
        $this->urlRepository = $urlRepository;
        $this->httpClient = $httpClient;
        $this->sendReportsUrl = $params->get('send_reports_url');
        $this->sendReportsToken = $params->get('send_reports_token');

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $unreportedUrls = $this->urlRepository->getAllUnreported();
        $unreportedUrlsNum = count($unreportedUrls); 

        if ($unreportedUrlsNum) {
            $urlsToReport = array_map(function($url) {
                $mappedUrl = [];
                $mappedUrl["url"] = $url["url"];
                $mappedUrl["createdDate"] = $url["createdDate"];
                return $mappedUrl;
            }, $unreportedUrls);

            try {
                $response = $this->httpClient->request(
                    'POST',
                    $this->sendReportsUrl,
                    [
                        "body" => [
                            "urls" => $urlsToReport,
                            "token" => $this->sendReportsToken,
                        ]
                    ]
                );

                $responseContent = $response->toArray();
            } catch(Exception $ex) {
                echo $ex->getMessage() . PHP_EOL;
                return Command::FAILURE;
            }

            

            if (!empty($responseContent['success'])) {
                $unreportedUrlsIds = array_map(function($url) {
                    return $url["id"];
                }, $unreportedUrls);
                $this->urlRepository->markAsReported($unreportedUrlsIds);
            }
        }

        $io->success("{$unreportedUrlsNum} new URL's has been reported to " . $this->sendReportsUrl);

        return Command::SUCCESS;
    }
}
