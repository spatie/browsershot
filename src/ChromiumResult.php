<?php

namespace Spatie\Browsershot;

class ChromiumResult
{
    private string $result;
    private string|null $exception;

    /**
     * @var null|array{type: string, message: string, location: array, stackTrace: string}
     */
    private null|array $consoleMessages;

    /**
     * @var null|array{url: string}
     */
    private null|array $requestsList;

    /**
     * @var null|array{status: int, url: string}
     */
    private null|array $failedRequests;

    /**
     * @var null|array{name: string, message: string}
     */
    private null|array $pageErrors;

    /**
     * @var null|array{url: string, status: int, statusText: string, headers: array}
     */
    private null|array $redirectHistory;

    public function __construct(array|null $output)
    {
        $this->result = $output['result'] ?? '';
        $this->exception = $output['exception'] ?? null;
        $this->consoleMessages = $output['consoleMessages'] ?? null;
        $this->requestsList = $output['requestsList'] ?? null;
        $this->failedRequests = $output['failedRequests'] ?? null;
        $this->pageErrors = $output['pageErrors'] ?? null;
        $this->redirectHistory = $output['redirectHistory'] ?? null;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getException()
    {
        return $this->exception;
    }

    public function getConsoleMessages()
    {
        return $this->consoleMessages;
    }

    public function getRequestsList()
    {
        return $this->requestsList;
    }

    public function getFailedRequests()
    {
        return $this->failedRequests;
    }

    public function getPageErrors()
    {
        return $this->pageErrors;
    }

    public function getredirectHistory()
    {
        return $this->redirectHistory;
    }
}
